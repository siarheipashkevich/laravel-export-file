<?php

namespace Esupl\ExportFile\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Bus};
use Esupl\ExportFile\Events\QueuedFileFailed;
use Esupl\ExportFile\Jobs\FinalizeQueuedFile;
use Esupl\ExportFile\Contracts\{QueuedFile, ExportFile};
use Esupl\ExportFile\{ExportFileHelper, ExportFileManager};

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
                'status' => ExportFileHelper::QUEUED_STATUS,
                'params' => [
                    'queued_file_id' => $queuedFile->id,
                    'filename' => $queuedFile->filename,
                ],
            ];
        }

        return [
            'status' => ExportFileHelper::READY_STATUS,
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

        if (!empty($jobs)) {
            Bus::chain([...$jobs, new FinalizeQueuedFile($queuedFile)])
                ->catch(function () use ($queuedFile) {
                    $queuedFile->markAsFailed();

                    QueuedFileFailed::dispatch($queuedFile);
                })
                ->onQueue('exports')
                ->dispatch();
        } elseif (!$queuedFile->fileExists()) {
            $queuedFile->markAsFailed();
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
        if (config('export_file.force_mode') === ExportFileHelper::DOWNLOAD_MODE) {
            return false;
        }

        $mode = $request->get(ExportFileHelper::MODE_FIELD, ExportFileHelper::AUTO_MODE);

        if ($mode === ExportFileHelper::DOWNLOAD_MODE) {
            return false;
        }

        return $mode === ExportFileHelper::QUEUE_MODE || $exportFile->shouldQueue();
    }
}
