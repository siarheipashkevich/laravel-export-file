<?php

namespace Pashkevich\ExportFile;

class ExportFileHelper
{
    public const string TYPE_FIELD = 'exf_type';
    public const string FORMAT_FIELD = 'exf_format';
    public const string MODE_FIELD = 'exf_mode';

    public const string AUTO_MODE = 'auto';
    public const string DOWNLOAD_MODE = 'download';
    public const string QUEUE_MODE = 'queue';

    public const string READY_STATUS = 'ready';
    public const string QUEUED_STATUS = 'queued';
    public const string FAILED_STATUS = 'failed';
}
