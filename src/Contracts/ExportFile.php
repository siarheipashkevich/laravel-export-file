<?php

namespace Esupl\ExportFile\Contracts;

use Esupl\ExportFile\Contracts\QueuedFile;
use Illuminate\Foundation\Bus\PendingDispatch;

/**
 * Interface ExportFile
 *
 * @package Esupl\ExportFile\Contracts
 */
interface ExportFile
{
    /**
     * Validates the incoming request.
     *
     * @return void
     */
    public function validate(): void;

    /**
     * Initializes the export file.
     *
     * @return void
     */
    public function initialize(): void;

    /**
     * Gets the url for downloading export file.
     *
     * @return string
     */
    public function getDownloadUrl(): string;

    /**
     * Downloads the export file.
     *
     * @return mixed
     */
    public function download();

    /**
     * Gets the filename of the export file.
     *
     * @return string
     */
    public function getFilename(): string;

    /**
     * Checks if the export file should be queued.
     *
     * @return bool
     */
    public function shouldQueue(): bool;

    /**
     * Moves the export file to the queue.
     *
     * @param QueuedFile $queuedFile
     * @return PendingDispatch|null
     */
    public function moveToQueue(QueuedFile $queuedFile): ?PendingDispatch;
}
