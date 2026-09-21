<?php

namespace Pashkevich\ExportFile\Console;

use Illuminate\Console\Command;
use Pashkevich\ExportFile\Contracts\QueuedFile;

class PurgeQueuedFiles extends Command
{
    protected $signature = 'export-file:purge-queued-files';

    protected $description = 'Purge queued files older than 2 weeks';

    public function handle(): void
    {
        $retainFor = config('export_file.keep_all_queued_files_for_days');

        $queuedFile = resolve(QueuedFile::class);

        $queuedFile->query()
            ->where('created_at', '<', now()->subDays($retainFor))
            ->get()
            ->each(function (QueuedFile $queuedFile) {
                $queuedFile->delete();
            });
    }
}
