<?php

namespace Ajuchacko\RazorpayHttp;

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
}
