<?php

namespace Esupl\ExportFile\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Esupl\ExportFile\Contracts\QueuedFile;
use Illuminate\Contracts\Queue\ShouldQueue;
use Esupl\ExportFile\Events\QueuedFileCompleted;

/**
 * Class FinalizeQueuedFile
 *
 * @package Esupl\ExportFile\Jobs
 */
class FinalizeQueuedFile implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    /**
     * @var QueuedFile
     */
    private QueuedFile $queuedFile;

    /**
     * FinalizeQueuedFile constructor.
     *
     * @param QueuedFile $queuedFile
     */
    public function __construct(QueuedFile $queuedFile)
    {
        $this->queuedFile = $queuedFile;
    }

    /**
     * Executes the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->queuedFile->markAsCompleted();

        QueuedFileCompleted::dispatch($this->queuedFile);
    }
}
