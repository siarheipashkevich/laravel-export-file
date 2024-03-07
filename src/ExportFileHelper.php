<?php

namespace Pashkevich\ExportFile;

class ExportFileHelper
{
    public const TYPE_FIELD = 'exf_type';
    public const FORMAT_FIELD = 'exf_format';
    public const MODE_FIELD = 'exf_mode';

    public const AUTO_MODE = 'auto';
    public const DOWNLOAD_MODE = 'download';
    public const QUEUE_MODE = 'queue';

    public const READY_STATUS = 'ready';
    public const QUEUED_STATUS = 'queued';
    public const FAILED_STATUS = 'failed';
}
