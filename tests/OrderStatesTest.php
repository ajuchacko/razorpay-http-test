<?php

namespace Ajuchacko\RazorpayHttp\Tests;

use Ajuchacko\RazorpayHttp\Payment;

class OrderStatesTest extends Testcase
{
	function setUp(): void
	{
		parent::setUp();
    	
    	$this->api = app('razorpay');
	}

	/** @test */
	function on_payment_attempt_order_status_changes_to_attempted()
	{
		$order = $this->api->order->create([
			'amount' => 100, 
			'currency' => 'INR',
			'receipt' => '123', 
			'notes' => ['key' => 'custom order note'],
			'partial_payment' => false
		]);
		$this->assertEquals('created', $order->status);

		$order->paidUsing([
			'email' => 'jon@mail.com',
			'contact' => '9898989898',
			'card_number' => '4242424242424242',
			'name' => 'jon doe',
			'expiry' => now()->addYear(),
			'cvv' => 123,
		], 'authorized');

		$this->assertEquals('attempted', $order->status);
		$this->assertEquals(1, $order->attempts);
		$this->assertInstanceOf(Payment::class, $payment = $order->payments()['items']->first());
		$this->assertEquals('authorized', $payment->status);
		$this->assertEquals($order->id, $payment->order_id);
		$this->assertEquals('card', $payment->method);
		$this->assertFalse($payment->captured);
	}

	/** @test */
	function on_payment_capture_order_status_changes_to_paid()
	{
		$order = $this->api->order->create([
			'amount' => 100, 
			'currency' => 'INR',
			'receipt' => '123', 
			'notes' => ['key' => 'custom order note'],
			'partial_payment' => false
		])->paidUsing([
			'email' => 'jon@mail.com',
			'contact' => '9898989898',
			'card_number' => '4242424242424242',
			'name' => 'jon doe',
			'expiry' => now()->addYear(),
			'cvv' => 123,
		], 'captured');

		$this->assertEquals('paid', $order->status);
		$this->assertTrue($order->payments()['items']->first()->captured);
	}

	/** test */
	function order_status_stays_paid_even_if_payment_is_refunded()
	{
		$order = $this->api->order->create([
			'amount' => 100, 
			'currency' => 'INR',
			'receipt' => '123', 
			'notes' => ['key' => 'custom order note'],
			'partial_payment' => false
		])->paidUsing([
			'email' => 'jon@mail.com',
			'contact' => '9898989898',
			'card_number' => '4242424242424242',
			'name' => 'jon doe',
			'expiry' => now()->addYear(),
			'cvv' => 123,
		], 'captured');

		$payment = $order->payments()->first();
		$refund = $this->api->refund->create(['payment_id' => $payment->id]);

		$this->assertEquals('paid', $order->status);
	}
}