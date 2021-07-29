<?php

namespace Ajuchacko\RazorpayHttp\Tests;

class PaymentTest extends Testcase {

	function setUp(): void
	{
		parent::setUp();
    	
    	$this->razorpay = app('razorpay');
	}

	/** @test */
	function it_can_fetch_a_payment_using_id()
	{
		$payment_id = $this->razorpay->order->create([
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

		$payment  = $this->razorpay->payment->fetch($payment_id);

		$this->assertNotNull($payment->id);
		$this->assertEquals('captured', $payment->status);
		$this->assertEquals('jon@mail.com', $payment->email);
	}
}