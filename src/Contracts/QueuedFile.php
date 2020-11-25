<?php

namespace Esupl\ExportFile\Contracts;

use Illuminate\Database\Eloquent\Model;

/**
 * Interface QueuedFile
 *
 * @mixin Model
 *
 * @package Esupl\ExportFile\Contracts
 */
interface QueuedFile
{
    /**
     * The queued status.
     */
    public const QUEUED_STATUS = 'queued';

    /**
     * The completed status.
     */
    public const COMPLETED_STATUS = 'completed';

    /**
     * The failed status.
     */
    public const FAILED_STATUS = 'failed';

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
