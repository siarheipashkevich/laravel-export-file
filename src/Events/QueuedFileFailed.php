<?php

namespace Esupl\ExportFile\Events;

use Illuminate\Queue\SerializesModels;
use Esupl\ExportFile\Contracts\QueuedFile;
use Illuminate\Foundation\Events\Dispatchable;

/**
 * Class QueuedFileFailed
 *
 * @package Esupl\ExportFile\Events
 */
class QueuedFileFailed
{
    use Dispatchable;
    use SerializesModels;

    /**
     * @var QueuedFile
     */
    public QueuedFile $queuedFile;

    /**
     * QueuedFileFailed constructor.
     *
     * @param QueuedFile $queuedFile
     */
    public function __construct(QueuedFile $queuedFile)
    {
        $this->queuedFile = $queuedFile;
    }
}
