<?php

namespace Ajuchacko\RazorpayHttp\Tests;

use Ajuchacko\RazorpayHttp\Order;
use Illuminate\Support\Collection;

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

    /** @test */
    function it_can_fetch_an_order_by_id()
    {
        $order_id = $this->createTestOrders()->last()->id;

        $order = $this->razorpay->order->fetch($order_id);

        $this->assertEquals(20, $order->amount);
        $this->assertEquals('USD', $order->currency);
        $this->assertEquals(10, $order->receipt);
    }

    /** @test */
    function it_can_get_all_orders()
    {
        // $options = [
        //     // 'authorized' => false,
        //     'receipt' => ,
        //     'from' => ,
        //     'to' => ,
        //     'count' => ,
        //     'skip' => ,
        //     // 'expand' => []
        // ];
        $this->createTestOrders();

        $orders = $this->razorpay->order->all();

        $this->assertCount(10, $orders);
    }

    /** @test */
    function it_can_fetch_all_payments_of_an_order()
    {
        $this->makePaymentsFor(
            $random_order = $this->createTestOrders()->random(),
            ['failed', 'authorized']
        );

        $payments = $this->razorpay->order->fetch($random_order->id)->payments(); 

        $this->assertArrayHasKey('count', $payments);
        $this->assertArrayHasKey('entity', $payments);
        $this->assertCount(2, $payments['items']);
    }

    private function createTestOrders()
    {
        return Collection::times(10, function ($number) {
            return $this->razorpay->order->create([
                'amount' => $number + 10,
                'currency' => 'USD',
                'receipt' => $number, 
                'notes' => ['user_id' => 'special uuid for user'],
                'partial_payment' => false
            ]);
        });
    }

    private function makePaymentsFor(Order $order, array $payment_states)
    {
        foreach ($payment_states as $payment_state) {
            $order->paidUsing([
                'email' => 'jon@mail.com',
                'contact' => '9898989898',
                'card_number' => '4242424242424242',
                'name' => 'jon doe',
                'expiry' => now()->addYear(),
                'cvv' => random_int(123, 999),
            ], $payment_state);
        }
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
