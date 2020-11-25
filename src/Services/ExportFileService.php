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
            $queuedFile = DB::transaction(fn() => $this->createQueuedFileAndDispatchToQueue($exportFile));

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
     * Creates a queued file for handling export file on the queue.
     *
     * @param ExportFile $exportFile
     * @return QueuedFile
     */
    protected function createQueuedFileAndDispatchToQueue(ExportFile $exportFile): QueuedFile
    {
        $queuedFile = $this->retrieveQueuedFile($exportFile);

        $jobs = $exportFile->jobs($queuedFile);

        array_push($jobs, new FinalizeQueuedFile($queuedFile));

        if (!empty($jobs)) {
            Bus::chain($jobs)
                ->catch(function () use ($queuedFile) {
                    $queuedFile->markAsFailed();

                    QueuedFileFailed::dispatch($queuedFile);
                })
                ->onQueue('exports')
                ->dispatch();
        }

        return $queuedFile;
    }

    /**
     * Retrieves the queued file for export file on the queue.
     *
     * @param ExportFile $exportFile
     * @return QueuedFile
     */
    protected function retrieveQueuedFile(ExportFile $exportFile): QueuedFile
    {
        $queuedFile = $exportFile->retrieveQueuedFile();

        if (!$queuedFile->exists) {
            $queuedFile->save();
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
