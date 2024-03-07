<?php

namespace Pashkevich\ExportFile\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Pashkevich\ExportFile\Contracts\QueuedFile;
use Pashkevich\ExportFile\Events\QueuedFileCompleted;

class FinalizeQueuedFile implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(private readonly QueuedFile $queuedFile) {}

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
