<?php

namespace Pashkevich\ExportFile\Contracts;

interface ExportFile
{
    /**
     * Validates the incoming request.
     */
    public function validate(): void;

    /**
     * Initializes the export file.
     */
    public function initialize(): void;

    /**
     * Gets the url for downloading export file.
     */
    public function getDownloadUrl(): string;

    /**
     * Downloads the export file.
     */
    public function download();

    /**
     * Gets the displayable filename of the export file.
     */
    public function filename(): string;

    /**
     * Checks if the export file should be queued.
     */
    public function shouldQueue(): bool;

    /**
     * Retrieves the queued file which will be used on the queued export file.
     */
    public function retrieveQueuedFile(): QueuedFile;

    /**
     * Gets the jobs available for export file through queue.
     */
    public function jobs(QueuedFile $queuedFile): array;
}
