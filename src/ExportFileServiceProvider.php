<?php

namespace Pashkevich\ExportFile;

use Illuminate\Support\ServiceProvider;
use Pashkevich\ExportFile\Contracts\QueuedFile;

class ExportFileServiceProvider extends ServiceProvider
{
    /**
     * Bootstraps the application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/export_file.php' => config_path('export_file.php')
            ], 'config');

            $this->commands([
                Console\PurgeQueuedFiles::class,
            ]);
        }
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
