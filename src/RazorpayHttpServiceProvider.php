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
    }

    public function packageRegistered()
    {
        $this->app->singleton(Api::class, function ($app) {

            $api_key = config('razorpay-http.api_key');
            $api_secret = config('razorpay-http.api_secret');

        	if ($app->environment() == 'testing') {
	            return new FakeApi($api_key, $api_secret);
        	} else {
                return new Api($api_key, $api_secret);
        	}
        });

        $this->app->instance('razorpay', $this->app->make(Api::class));
    }
}
