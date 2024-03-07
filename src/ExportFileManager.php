<?php

namespace Pashkevich\ExportFile;

use Illuminate\Http\Request;
use InvalidArgumentException;
use Pashkevich\ExportFile\Exceptions\NonexistentExportFileException;
use Pashkevich\ExportFile\Contracts\ExportFile as ExportFileContract;

final class ExportFileManager
{
    private static array $registry = [];

    /**
     * Registers the export file.
     *
     * @param string $type
     * @param string $exportFileClass
     * @return void
     */
    public static function register(string $type, string $exportFileClass): void
    {
        if (empty($type) || array_key_exists($type, self::$registry)) {
            return;
        }

        if (!class_exists($exportFileClass)) {
            throw new InvalidArgumentException(
                sprintf('The class you are trying to register (%s) as export file not found.', $exportFileClass)
            );
        }

        if (!class_implements($exportFileClass, ExportFileContract::class)) {
            throw new InvalidArgumentException(
                sprintf(
                    'The class you are trying to register (%s) as property type, ' .
                    'must implement the %s interface.',
                    $exportFileClass,
                    ExportFileContract::class
                )
            );
        }

        self::$registry[$type] = $exportFileClass;
    }

    /**
     * Makes the export file by the given type.
     *
     * @param Request $request
     * @return ExportFileContract
     */
    public static function make(Request $request): ExportFileContract
    {
        $type = $request->get('exf_type');

        $exportFileClass = self::$registry[$type] ?? null;

        if (is_null($exportFileClass)) {
            throw new NonexistentExportFileException("No export file is registered with the type `$type`.");
        }

        return resolve($exportFileClass)->init($request, $type);
    }

    /**
     * Gets all registered types of the export files.
     *
     * @return array
     */
    public static function types(): array
    {
        return array_keys(self::$registry);
    }
}
