<?php

return [
    /*
     * The force mode for handling export files.
     */
    'force_mode' => env('EXPORT_FILE_FORCE_MODE', 'download'),

    /*
     * The number of days for which queued files must be kept.
     */
    'keep_all_queued_files_for_days' => 7,

    /*
     * The fully qualified class name of the queued file model.
     */
    'queued_file_model' => \Esupl\ExportFile\Models\QueuedFile::class,

    /*
     * The directory where will be stored completed queued files.
     */
    'queued_files_directories' => 'queued-files',
];
