<?php

namespace Ajuchacko\RazorpayHttp\Tests;

use Ajuchacko\RazorpayHttp\FakeRazorpay;
use Ajuchacko\RazorpayHttp\Order;

class OrderTest extends TestCase
{
	function setUp(): void
	{
		parent::setUp();
    	
    	$this->razorpay = app('razorpay');
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

    /** @test */
    function it_can_create_as_many_orders_as_needed()
    {
        $orderA  = $this->razorpay->order->create(
            [
                'amount' => 100, 
                'currency' => 'INR',
                'receipt' => '123', 
                'notes' => ['key' => 'custom order note'],
                'partial_payment' => false
            ]
        );
        $orderB  = $this->razorpay->order->create(
            [
                'amount' => 200, 
                'currency' => 'USD',
                'receipt' => 53454, 
                'notes' => ['user_id' => 'special uuid for user'],
                'partial_payment' => false
            ]
        );

        $this->assertCount(2, $this->razorpay->orders());
    }

// TO POST CONTROLLER
     // $result = $this->razorpay->newOrder()->paidUsing($card, 'fail')
    // $response = $this->json('POST', "concerts/{$concert->id}/orders", [
    //     'email'            => 'john@example.com',
    //     'ticket_quantity'  => 3,
    //      'razorpay_payment_id' => $result['razorpay_payment_id'];
    //      'razorpay_order_id' => $result['razorpay_order_id'];
    //      'razorpay_signature' => $result['razorpay_signaure'];
    // ]);
// ON CONTROLLER
    // $this->validate(request(), [
    //     'email'             => 'required|email',
    //     'ticket_quantity'   => 'required|min:1|integer',
    //     'payment_token'     => 'required'
    //     'razorpay_payment_id' => 'required',
    //     'razorpay_order_id' => 'required',
    //     'razorpay_signature' => 'required',
    // ]);

    //     try {
    //         $reservation = $concert->reserveTickets(request('ticket_quantity'), request('email'));

    //         $order = $reservation->complete($this->paymentGateway, request('payment_token'), $concert->user->stripe_account_id);
}
