<?php

namespace Pashkevich\ExportFile\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Pashkevich\ExportFile\Contracts\QueuedFile;

class QueuedFileCompleted
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public QueuedFile $queuedFile) {}
}
