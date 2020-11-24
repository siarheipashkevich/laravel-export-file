<?php

namespace Esupl\ExportFile\Traits;

use Illuminate\Auth\Access\AuthorizationException;

/**
 * Trait HandlesAuthorization
 *
 * @package Esupl\ExportFile\Traits
 */
trait HandlesAuthorization
{
    /**
     * Determines if the request passes the authorization check.
     *
     * @return bool
     */
    protected function passesAuthorization(): bool
    {
        if (method_exists($this, 'authorize')) {
            return $this->authorize();
        }

        return true;
    }

    /**
     * Handles a failed authorization attempt.
     *
     * @return void
     * @throws AuthorizationException
     */
    protected function failedAuthorization(): void
    {
        throw new AuthorizationException();
    }
}
