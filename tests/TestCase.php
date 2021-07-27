<?php

namespace Ajuchacko\RazorpayHttp\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;
use Ajuchacko\RazorpayHttp\RazorpayHttpServiceProvider;

class TestCase extends Orchestra
{
    public function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Ajuchacko\RazorpayHttp\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            RazorpayHttpServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        
        // include_once __DIR__.'/../database/migrations/create_skeleton_table.php.stub';
        // (new \CreatePackageTable())->up();
        
    }
}
