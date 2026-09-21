<?php

namespace Pashkevich\ExportFile;

use Illuminate\Http\Request;
use Pashkevich\ExportFile\Contracts\QueuedFile;
use Pashkevich\ExportFile\Contracts\ExportFile as ExportFileContract;
use Pashkevich\ExportFile\Traits\{HandlesValidation, HandlesAuthorization};

abstract class ExportFile implements ExportFileContract
{
    use HandlesValidation;
    use HandlesAuthorization;

    private string $type;

    protected Request $request;

    protected mixed $user;

    /**
     * Base initialization of the export file.
     */
    final public function init(Request $request, string $type): self
    {
        $this->request = $request;
        $this->type = $type;

        $this->user = $this->retrieveUser();

        return $this;
    }

    /**
     * Validates incoming request.
     */
    public function validate(): void
    {
        if (!$this->passesAuthorization()) {
            $this->failedAuthorization();
        }

        $rules = $this->rules();

        if (!empty($rules)) {
            $this->request->validate($rules, $this->messages(), $this->attributes());
        }

        $this->passedValidation();
    }

    /**
     * Initializes the export file.
     */
    public function initialize(): void
    {
    }

    /**
     * Gets the url for downloading export file.
     */
    public function getDownloadUrl(): string
    {
        return '';
    }

    /**
     * Downloads the export file.
     */
    public function download()
    {
        return null;
    }

    /**
     * Gets the displayable filename of the export file.
     */
    public function filename(): string
    {
        return '';
    }

    /**
     * Checks if the export file should be queued.
     */
    public function shouldQueue(): bool
    {
        return false;
    }

    /**
     * Gets the jobs available for export file through queue.
     */
    public function jobs(QueuedFile $queuedFile): array
    {
        return [];
    }

    /**
     * Gets the type of the export file.
     */
    protected function getType(): string
    {
        return $this->type;
    }

    /**
     * Retrieves the authenticated user from request.
     */
    protected function retrieveUser()
    {
        return $this->request->user();
    }
}
