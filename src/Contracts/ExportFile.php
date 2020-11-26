<?php

namespace Esupl\ExportFile\Contracts;

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
     * Gets the displayable filename of the export file.
     *
     * @return string
     */
    public function filename(): string;

    /**
     * Checks if the export file should be queued.
     *
     * @return bool
     */
    public function shouldQueue(): bool;

    /**
     * Retrieves the queued file which will be used on the queued export file.
     *
     * @return QueuedFile
     */
    public function retrieveQueuedFile(): QueuedFile;

    /**
     * Gets the jobs available for export file through queue.
     *
     * @param QueuedFile $queuedFile
     * @return array
     */
    public function jobs(QueuedFile $queuedFile): array;
}
