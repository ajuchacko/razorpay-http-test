<?php

namespace Ajuchacko\RazorpayHttp\Tests;

class PaymentTest extends Testcase {

	function setUp(): void
	{
		parent::setUp();
    	
    	$this->api = app('razorpay');
	}

	/** @test */
	function it_can_fetch_a_payment_using_id()
	{
		$payment_id = $this->api->order->create([
			'amount' => 100, 
			'currency' => 'INR',
			'receipt' => '123', 
			'notes' => ['key' => 'custom order note'],
			'partial_payment' => false
		])
		->paidUsing([
			'email' => 'jane@mail.com',
			'contact' => '9898989898',
			'card_number' => '4242424242424242',
			'name' => 'jon doe',
			'expiry' => now()->addYear(),
			'cvv' => 123,
		], 'failed')
		->paidUsing([
			'email' => 'jon@mail.com',
			'contact' => '9898989898',
			'card_number' => '4242424242424242',
			'name' => 'jon doe',
			'expiry' => now()->addYear(),
			'cvv' => 456,
		], 'captured')
		->payments()['items']->last()->id;

		$payment  = $this->api->payment->fetch($payment_id);

		$this->assertNotNull($payment->id);
		$this->assertEquals('captured', $payment->status);
		$this->assertEquals('jon@mail.com', $payment->email);
	}

	/** @test */
	function it_can_capture_a_payment_after_fetching_it_using_id()
	{
		$payment_id = $this->api->order->create([
			'amount' => $amount = 100, 
			'currency' => 'INR',
			'receipt' => '123', 
			'notes' => ['key' => 'custom order note'],
			'partial_payment' => false
		])
		->paidUsing([
			'email' => 'jane@mail.com',
			'contact' => '9898989898',
			'card_number' => '4242424242424242',
			'name' => 'jon doe',
			'expiry' => now()->addYear(),
			'cvv' => 123,
		], 'authorized')
		->payments()['items']->last()->id;

		$payment = $this->api->payment->fetch($payment_id)->capture(['amount' => 50.0]); 

		$this->assertTrue($payment->captured());
		// $this->assertNotNull($payment->customer_id);
		// check order -> amount_due 
		// check order -> amount_paid
		// card_id  is filled on attempt
		// card is filled on attempt
	}

	/** @test */
	function trying_to_capture_unauthorized_payment_throws_error()
	{
		$payment_id = $this->api->order->create([
			'amount' => $amount = 100, 
			'currency' => 'INR',
			'receipt' => '123', 
			'notes' => ['key' => 'custom order note'],
			'partial_payment' => false
		])
		->paidUsing([
			'email' => 'jane@mail.com',
			'contact' => '9898989898',
			'card_number' => '4242424242424242',
			'name' => 'jon doe',
			'expiry' => now()->addYear(),
			'cvv' => 123,
		], 'failed')
		->payments()['items']->last()->id;

		$payment = $this->api->payment->fetch($payment_id);
		try {
			$payment->capture(['amount' => 50.0]);
		} catch (\Exception $e) {
			$this->assertFalse($payment->captured());
		}
	}

	/** @test */
	function trying_to_capture_more_amount_than_authorized_throws_error()
	{
		$payment_id = $this->api->order->create([
			'amount' => $amount = 100, 
			'currency' => 'INR',
			'receipt' => '123', 
			'notes' => ['key' => 'custom order note'],
			'partial_payment' => false
		])
		->paidUsing([
			'email' => 'jane@mail.com',
			'contact' => '9898989898',
			'card_number' => '4242424242424242',
			'name' => 'jon doe',
			'expiry' => now()->addYear(),
			'cvv' => 123,
		], 'authorized')
		->payments()['items']->last()->id;

		$payment = $this->api->payment->fetch($payment_id);
		try {
			$payment->capture(['amount' => 500.0]);
		} catch (\Exception $e) {
			$this->assertFalse($payment->captured());
		}
	}
}