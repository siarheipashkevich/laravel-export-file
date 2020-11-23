<?php

namespace Esupl\ExportFile\Events;

use Esupl\ExportFile\Contracts\QueuedFile;
use Illuminate\Foundation\Events\Dispatchable;

/**
 * Class QueuedFileCompleted
 *
 * @package Esupl\ExportFile\Events
 */
class QueuedFileCompleted
{
    use Dispatchable;

    /**
     * @var QueuedFile
     */
    public QueuedFile $queuedFile;

    /**
     * QueuedFileCompleted constructor.
     *
     * @param QueuedFile $queuedFile
     */
    public function __construct(QueuedFile $queuedFile)
    {
        $this->queuedFile = $queuedFile;
    }
}
