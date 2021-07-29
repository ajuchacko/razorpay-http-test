<?php

namespace Ajuchacko\RazorpayHttp;

use Razorpay\Api\Api;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
// use VendorName\Skeleton\Commands\SkeletonCommand;

class RazorpayHttpServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('razorpay-http')
            ->hasConfigFile();
            // ->hasViews()
            // ->hasMigration('create_skeleton_table')
            // ->hasCommand(SkeletonCommand::class);
    }

    public function packageRegistered()
    {
        $this->app->singleton(Api::class, function ($app) {
           	
         	[$api_key, $api_secret] = array_values(config('razorpay-http'));

        	if ($app->environment() == 'testing') {
	            return new FakeApi($api_key='', $api_secret='');
        	} else {
                return new Api($api_key, $api_secret);
        	}
        });

        $this->app->instance('razorpay', $this->app->make(Api::class));
    }
}
