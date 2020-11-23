<?php

namespace Esupl\ExportFile\Tests;

use Orchestra\Testbench\TestCase;
use Esupl\ExportFile\ExportFileServiceProvider;

class ExampleTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [ExportFileServiceProvider::class];
    }

    /** @test */
    public function true_is_true()
    {
        $this->assertTrue(true);
    }
}
