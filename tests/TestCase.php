<?php

namespace Miracuthbert\Royalty\Tests;

use Eloquent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Kalnoy\Nestedset\NestedSetServiceProvider;
use Miracuthbert\Royalty\RoyaltyServiceProvider;
use Miracuthbert\Royalty\Tests\Models\User;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    use RefreshDatabase;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        Eloquent::unguard();

        $this->loadLaravelMigrations(['--database' => 'testbench']);

        // call migrations specific to our tests, e.g. to seed the db
        // the path option should be an absolute path.
        $this->loadMigrationsFrom([
            '--database' => 'testbench',
            '--path' => realpath(__DIR__ . '/database/migrations'),
        ]);

        $this->seed('PointTableSeeder');

        // factories
        $this->withFactories(__DIR__ . '/database/factories');
    }

    /**
     * Clean up the testing environment before the next test.
     *
     * @return void
     */
    protected function tearDown(): void
    {
//        $this->artisan('migrate:rollback');
    }


    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            NestedSetServiceProvider::class,
            RoyaltyServiceProvider::class
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // Setup laravel roles user model
        $app['config']->set('royalty.user.model', User::class);

        $app['config']->set('database.default', 'testbench');

        // Setup default database to use sqlite :memory:
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }
}
