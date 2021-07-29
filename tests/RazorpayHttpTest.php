<?php

namespace Ajuchacko\RazorpayHttp\Tests;

use Ajuchacko\RazorpayHttp\FakeApi;
use Ajuchacko\RazorpayHttp\Order;

class RazorpayHttpTest extends TestCase
{
	function setUp(): void
	{
		parent::setUp();
    	
    	$this->razorpay = app('razorpay');
	}

    /** @test */
    function can_get_fake_payment_gateway_instance()
    {
    	$this->assertInstanceOf(FakeApi::class, $this->razorpay);
    }
}
