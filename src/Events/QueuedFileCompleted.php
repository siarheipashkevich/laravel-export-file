<?php

namespace Esupl\ExportFile\Events;

use Illuminate\Queue\SerializesModels;
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
    use SerializesModels;

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
