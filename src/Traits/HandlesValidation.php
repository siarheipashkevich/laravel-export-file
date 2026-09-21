<?php

namespace Pashkevich\ExportFile\Traits;

trait HandlesValidation
{
    /**
     * Gets the validation rules that apply to the export file.
     */
    protected function rules(): array
    {
        return [];
    }

    /**
     * Gets custom messages for validator errors.
     */
    protected function messages(): array
    {
        return [];
    }

    /**
     * Gets custom attributes for validator errors.
     */
    protected function attributes(): array
    {
        return [];
    }

    /**
     * Handles a passed validation attempt.
     */
    protected function passedValidation(): void
    {
    }
}
