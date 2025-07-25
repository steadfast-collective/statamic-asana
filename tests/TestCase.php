<?php

namespace SteadfastCollective\StatamicAsana\Tests;

use Spatie\LaravelRay\RayServiceProvider;
use Statamic\Testing\AddonTestCase;
use Statamic\Testing\Concerns\PreventsSavingStacheItemsToDisk;
use SteadfastCollective\StatamicAsana\ServiceProvider;

abstract class TestCase extends AddonTestCase
{
    use PreventsSavingStacheItemsToDisk;

    protected string $addonServiceProvider = ServiceProvider::class;

    protected string $fakeStacheDirectory = __DIR__.'/__fixtures__/stache';

    protected function setUp(): void
    {
        parent::setUp();

        $this->preventSavingStacheItemsToDisk();
    }

    protected function getPackageProviders($app): array
    {
        return array_merge(
            [
                RayServiceProvider::class,
            ],
            parent::getPackageProviders($app)
        );
    }
}
