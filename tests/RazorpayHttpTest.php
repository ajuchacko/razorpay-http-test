<?php

namespace Ajuchacko\RazorpayHttp\Tests;

use Ajuchacko\RazorpayHttp\Razorpay;
use Ajuchacko\RazorpayHttp\RazorpayHttp;

class RazorpayHttpTest extends TestCase
{
    /** @test */
    public function can_get_fake_payment_gateway_instance()
    {
    	$razorpay = app('razorpay');

    	$this->assertInstanceOf(Razorpay::class, $razorpay);
    }
}
