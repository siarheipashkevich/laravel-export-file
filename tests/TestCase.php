<?php

namespace Pashkevich\ExportFile\Tests;

use Pashkevich\ExportFile\ExportFileServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app): array
    {
        return [ExportFileServiceProvider::class];
    }
}
