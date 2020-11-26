<?php

namespace Esupl\ExportFile\Models;

use Illuminate\Support\{Carbon, Str};
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Filesystem\Filesystem;
use Esupl\ExportFile\Contracts\QueuedFile as QueuedFileContract;

/**
 * Class QueuedFile
 *
 * @property int $id
 * @property string $uuid
 * @property string $disk
 * @property string $directory
 * @property string $filename
 * @property string $status
 * @property array $options
 * @property Carbon $created_at
 *
 * @package Esupl\ExportFile\Models
 */
class QueuedFile extends Model implements QueuedFileContract
{
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'options' => 'array',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted(): void
    {
        static::creating(function (self $queuedFile) {
            $queuedFile->uuid = (string)Str::uuid();
        });

        static::deleted(function (QueuedFile $queuedFile) {
            $storage = Storage::disk($queuedFile->disk);

            if ($storage->exists($queuedFile->getDiskPath())) {
                $storage->delete($queuedFile->getDiskPath());
            }
        });
    }

    /**
     * Get the path to the file relative to the root of the disk.
     *
     * @return string
     */
    public function getDiskPath(): string
    {
        return ltrim(rtrim((string)$this->directory, '/') . '/' . ltrim((string)$this->filename, '/'), '/');
    }

    /**
     * Checks if the file exists on disk.
     *
     * @return bool
     */
    public function fileExists(): bool
    {
        return $this->storage()->exists($this->getDiskPath());
    }

    /**
     * Gets the download url for the queued file.
     *
     * @return string
     */
    public function getDownloadUrl(): string
    {
        if (!$this->isCompleted()) {
            return '';
        }

        if (in_array($this->disk, ['public', 'local'])) {
            $url = $this->storage()->url($this->getDiskPath());
        } else {
            $url = $this->storage()->temporaryUrl($this->getDiskPath(), now()->addDay());
        }

        return $url;
    }

    /**
     * Checks that the queued file is in a 'Completed' status.
     *
     * @return bool
     */
    public function isCompleted(): bool
    {
        return $this->status === self::COMPLETED_STATUS;
    }

    /**
     * Checks that the queued file is in a 'Failed' status.
     *
     * @return bool
     */
    public function isFailed(): bool
    {
        return $this->status === self::FAILED_STATUS;
    }

    /**
     * Marks the queued file as completed.
     *
     * @return void
     */
    public function markAsCompleted(): void
    {
        $this->update(['status' => self::COMPLETED_STATUS]);
    }

    /**
     * Marks the queued file as failed.
     *
     * @return void
     */
    public function markAsFailed(): void
    {
        $this->update(['status' => self::FAILED_STATUS]);
    }

    /**
     * Gets the filesystem object for this file.
     *
     * @return Filesystem
     */
    protected function storage(): Filesystem
    {
        return Storage::disk($this->disk);
    }
}
