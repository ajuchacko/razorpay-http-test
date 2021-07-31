<?php

namespace Ajuchacko\RazorpayHttp\Tests;

use Ajuchacko\RazorpayHttp\FakeApi;
use Ajuchacko\RazorpayHttp\Order;

class FakeApiTest extends TestCase
{
	function setUp(): void
	{
		parent::setUp();
    	
    	$this->api = app('razorpay');
	}

    /** @test */
    function can_get_fake_payment_gateway_instance()
    {
    	$this->assertInstanceOf(FakeApi::class, $this->api);
    }

    /** @test */
    function it_can_create_new_order_to_pay()
    {
        $details = [
            'amount' => 500,
            'currency' => 'USD',
            'receipt' => 534759374, 
            'notes' => [],
            'partial_payment' => false
        ];

        $order = $this->api->newOrder($details);

        $this->assertInstanceOf(Order::class, $order);
        $this->assertNotNull($order->id);
    }
}
