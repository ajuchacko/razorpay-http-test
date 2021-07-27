<?php

namespace Ajuchacko\RazorpayHttp\Tests;

use Ajuchacko\RazorpayHttp\FakeRazorpay;
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
    	$this->assertInstanceOf(FakeRazorpay::class, $this->razorpay);
    }

    /** @test */
    function it_can_create_a_test_order()
    {
    	$order  = $this->razorpay->order->create(
    		[
    			'amount' => 100, 
    			'currency' => 'INR',
    			'receipt' => '123', 
    			'notes' => ['key' => 'custom order note'],
    			'partial_payment' => false
    		]
		);

    	$this->assertInstanceOf(Order::class, $order);
    	$this->assertFalse($order->partial_payment);
    	$this->assertEquals(100, $order->amount);
    	$this->assertEquals(100, $order->amount_due);
    	$this->assertEquals(0, $order->amount_paid);
    	$this->assertEquals(0, $order->attempts);
    	$this->assertEquals('created', $order->status);
    	$this->assertEquals('123', $order->receipt);
    	$this->assertEquals(['key' => 'custom order note'], $order->notes);
    }
}
