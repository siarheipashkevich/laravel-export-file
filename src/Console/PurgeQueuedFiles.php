<?php

namespace Esupl\ExportFile\Console;

use Illuminate\Console\Command;
use Esupl\ExportFile\Contracts\QueuedFile;

/**
 * Class PurgeQueuedFiles
 *
 * @package Esupl\ExportFile\Console
 */
class PurgeQueuedFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export-file:purge-queued-files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Purge queued files older than 2 weeks';

    /**
     * Executes the console command.
     *
     * @return void
     */
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
