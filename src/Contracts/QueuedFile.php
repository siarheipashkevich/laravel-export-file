<?php

namespace Esupl\ExportFile\Contracts;

/**
 * Interface QueuedFile
 *
 * @package Esupl\ExportFile\Contracts
 */
interface QueuedFile
{
    /**
     * Marks the queued file as completed.
     *
     * @return void
     */
    public function markAsCompleted(): void;

    /**
     * Marks the queued file as failed.
     *
     * @return void
     */
    public function markAsFailed(): void;
}
