<?php

namespace Esupl\ExportFile\Traits;

/**
 * Trait HandlesValidation
 *
 * @package Esupl\ExportFile\Traits
 */
trait HandlesValidation
{
    /**
     * Gets the validation rules that apply to the request.
     *
     * @return array
     */
    protected function rules(): array
    {
        return [];
    }

    /**
     * Gets custom messages for validator errors.
     *
     * @return array
     */
    protected function messages(): array
    {
        return [];
    }

    /**
     * Gets custom attributes for validator errors.
     *
     * @return array
     */
    protected function attributes(): array
    {
        return [];
    }

    /**
     * Handles a passed validation attempt.
     *
     * @return void
     */
    protected function passedValidation(): void
    {
    }
}
