<?php

namespace Pashkevich\ExportFile\Contracts;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Model
 */
interface QueuedFile
{
    /**
     * The queued status.
     */
    public const string QUEUED_STATUS = 'queued';

    /**
     * The completed status.
     */
    public const string COMPLETED_STATUS = 'completed';

    /**
     * The failed status.
     */
    public const string FAILED_STATUS = 'failed';

    /**
     * Marks the queued file as completed.
     */
    public function markAsCompleted(): void;

    /**
     * Marks the queued file as failed.
     */
    public function markAsFailed(): void;

    /**
     * Gets the file path on the storage disk.
     */
    public function getDiskPath(): string;
}
