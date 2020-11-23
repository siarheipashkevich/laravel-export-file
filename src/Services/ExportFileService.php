<?php

namespace Esupl\ExportFile\Services;

use Throwable;
use Illuminate\Http\Request;
use Esupl\ExportFile\ExportFileManager;
use Esupl\ExportFile\Jobs\FinalizeQueuedFile;
use Illuminate\Foundation\Bus\PendingDispatch;
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

        return rescue(function () use ($exportFile, $request) {
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
        }, function (Throwable $e) {
            return [
                'status' => 'failed',
                'params' => [
                    'message' => 'Oops. Something went wrong.',
                ],
            ];
        });
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
        $queuedFile = resolve(QueuedFile::class);

        $queuedFile->fill([
            'disk' => config('filesystems.default'),
            'filename' => $exportFile->getFilename(),
            'status' => 'queued',
            'options' => ['request' => $request->input()],
        ]);

        if (method_exists($exportFile, 'beforeQueue')) {
            $exportFile->beforeQueue($queuedFile);
        }

        $queuedFile->save();

        $pendingDispatch = $exportFile->moveToQueue($queuedFile);

        if ($pendingDispatch instanceof PendingDispatch) {
            $pendingDispatch->chain([
                new FinalizeQueuedFile($queuedFile),
            ]);
        }

        return $queuedFile;
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
