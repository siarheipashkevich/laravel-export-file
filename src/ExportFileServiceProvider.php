<?php

namespace Esupl\ExportFile;

use Illuminate\Support\ServiceProvider;
use Esupl\ExportFile\Contracts\QueuedFile;

/**
 * Class ExportFileServiceProvider
 *
 * @package Esupl\ExportFile
 */
class ExportFileServiceProvider extends ServiceProvider
{
    /**
     * Bootstraps the application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/export_file.php' => config_path('export_file.php')
        ], 'export-file-config');

        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\PurgeQueuedFiles::class,
            ]);
        }

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    /**
     * Registers any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/export_file.php', 'export_file');

        $this->app->bind(QueuedFile::class, config('export_file.queued_file_model'));
    }
}
