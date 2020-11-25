<?php

namespace Esupl\ExportFile\Services;

use Illuminate\Http\Request;
use Esupl\ExportFile\ExportFileManager;
use Illuminate\Support\Facades\{DB, Bus};
use Esupl\ExportFile\Events\QueuedFileFailed;
use Esupl\ExportFile\Jobs\FinalizeQueuedFile;
use Esupl\ExportFile\Contracts\{QueuedFile, ExportFile};

/**
 * Class ExportFileService
 *
 * @package Esupl\ExportFile\Services
 */
class ExportFileService
{
    /**
     * Exports the file.
     *
     * @param Request $request
     * @return array
     */
    public function export(Request $request): array
    {
        $exportFile = ExportFileManager::make($request);

        $exportFile->validate();

        $exportFile->initialize();

        if ($this->shouldQueue($request, $exportFile)) {
            $queuedFile = $this->moveToQueue($request, $exportFile);

            return [
                'status' => 'queued',
                'params' => [
                    'queued_file_id' => $queuedFile->id,
                    'filename' => $queuedFile->filename,
                ],
            ];
        }

        return [
            'status' => 'ready',
            'params' => [
                'url' => $exportFile->getDownloadUrl(),
            ],
        ];
    }

    /**
     * Downloads file.
     *
     * @param Request $request
     * @return mixed
     */
    public function download(Request $request)
    {
        $exportFile = ExportFileManager::make($request);

        $exportFile->initialize();

        return $exportFile->download();
    }

    /**
     * Moves the export file to the queue.
     *
     * @param Request $request
     * @param ExportFile $exportFile
     * @return QueuedFile
     */
    protected function moveToQueue(Request $request, ExportFile $exportFile): QueuedFile
    {
        if (method_exists($exportFile, 'prepareQueuedFile')) {
            $queuedFile = $exportFile->prepareQueuedFile();
        } else {
            $queuedFile = resolve(QueuedFile::class);

            $queuedFile->fill([
                'disk' => config('filesystems.default'),
                'filename' => $exportFile->getFilename(),
                'status' => QueuedFile::QUEUED_STATUS,
                'options' => ['request' => $request->input()],
            ]);
        }

        return DB::transaction(function () use ($exportFile, $queuedFile) {
            $queuedFile->save();

            $jobs = $exportFile->moveToQueue($queuedFile);

            if (!empty($jobs)) {
                Bus::chain([
                    ...$jobs,
                    new FinalizeQueuedFile($queuedFile),
                ])->catch(function () use ($queuedFile) {
                    $queuedFile->markAsFailed();

                    QueuedFileFailed::dispatch($queuedFile);
                })->onQueue('exports')->dispatch();
            }

            return $queuedFile;
        });
    }

    /**
     * Checks that the export file should be queued.
     *
     * @param Request $request
     * @param ExportFile $exportFile
     * @return bool
     */
    protected function shouldQueue(Request $request, ExportFile $exportFile): bool
    {
        if (config('export_file.force_mode') === 'download') {
            return false;
        }

        $mode = $request->get('exf_mode', 'auto');

        if ($mode === 'download') {
            return false;
        }

        return $mode === 'queue' || $exportFile->shouldQueue();
    }
}
