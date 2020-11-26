<?php

namespace Esupl\ExportFile;

use Illuminate\Http\Request;
use Esupl\ExportFile\Contracts\QueuedFile;
use Esupl\ExportFile\Contracts\ExportFile as ExportFileContract;
use Esupl\ExportFile\Traits\{HandlesValidation, HandlesAuthorization};

/**
 * Class ExportFile
 *
 * @package Esupl\ExportFile
 */
abstract class ExportFile implements ExportFileContract
{
    use HandlesValidation;
    use HandlesAuthorization;

    /**
     * @var string
     */
    private string $type;

    /**
     * @var Request
     */
    protected Request $request;

    /**
     * @var mixed
     */
    protected $user;

    /**
     * Base initialization of the export file.
     *
     * @param Request $request
     * @param string $type
     * @return self
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
     *
     * @return void
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
     *
     * @return void
     */
    public function initialize(): void
    {
    }

    /**
     * Gets the url for downloading export file.
     *
     * @return string
     */
    public function getDownloadUrl(): string
    {
        return '';
    }

    /**
     * Downloads the export file.
     *
     * @return mixed
     */
    public function download()
    {
        return null;
    }

    /**
     * Gets the displayable filename of the export file.
     *
     * @return string
     */
    public function filename(): string
    {
        return '';
    }

    /**
     * Checks if the export file should be queued.
     *
     * @return bool
     */
    public function shouldQueue(): bool
    {
        return false;
    }

    /**
     * Gets the jobs available for export file through queue.
     *
     * @param QueuedFile $queuedFile
     * @return array
     */
    public function jobs(QueuedFile $queuedFile): array
    {
        return [];
    }

    /**
     * Gets the type of the export file.
     *
     * @return string
     */
    protected function getType(): string
    {
        return $this->type;
    }

    /**
     * Retrieves the authenticated user from request.
     *
     * @return mixed
     */
    protected function retrieveUser()
    {
        return $this->request->user();
    }
}
