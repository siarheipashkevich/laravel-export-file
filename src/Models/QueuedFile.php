<?php

namespace Pashkevich\ExportFile\Models;

use Illuminate\Support\{Carbon, Str};
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Filesystem\Filesystem;
use Pashkevich\ExportFile\Contracts\QueuedFile as QueuedFileContract;

/**
 * @property int $id
 * @property string $uuid
 * @property string $disk
 * @property string $directory
 * @property string $filename
 * @property string $status
 * @property array $options
 * @property Carbon $created_at
 */
class QueuedFile extends Model implements QueuedFileContract
{
    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'options' => 'array',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function (self $queuedFile) {
            $queuedFile->uuid = Str::uuid()->toString();
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
     */
    public function getDiskPath(): string
    {
        return ltrim(rtrim($this->directory, '/') . '/' . ltrim($this->filename, '/'), '/');
    }

    /**
     * Checks if the file exists on disk.
     */
    public function fileExists(): bool
    {
        return $this->storage()->exists($this->getDiskPath());
    }

    /**
     * Gets the download url for the queued file.
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
     */
    public function isCompleted(): bool
    {
        return $this->status === self::COMPLETED_STATUS;
    }

    /**
     * Checks that the queued file is in a 'Failed' status.
     */
    public function isFailed(): bool
    {
        return $this->status === self::FAILED_STATUS;
    }

    /**
     * Marks the queued file as completed.
     */
    public function markAsCompleted(): void
    {
        $this->update(['status' => self::COMPLETED_STATUS]);
    }

    /**
     * Marks the queued file as failed.
     */
    public function markAsFailed(): void
    {
        $this->update(['status' => self::FAILED_STATUS]);
    }

    /**
     * Gets the filesystem object for this file.
     */
    protected function storage(): Filesystem
    {
        return Storage::disk($this->disk);
    }
}
