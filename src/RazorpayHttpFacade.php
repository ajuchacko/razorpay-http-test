<?php

namespace Ajuchacko\RazorpayHttp;

use Illuminate\Support\Facades\Facade;

class RazorpayHttpFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'razorpay-http';
    }
}
