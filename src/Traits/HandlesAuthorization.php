<?php

namespace Esupl\ExportFile\Traits;

use Illuminate\Http\Request;
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
     * @param Request $request
     * @param $user
     * @return bool
     */
    protected function passesAuthorization(Request $request, $user): bool
    {
        if (method_exists($this, 'authorize')) {
            return $this->authorize($request, $user);
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
