<?php

namespace Ajuchacko\RazorpayHttp;

class Payment {

	private $data;
	
	private $status;

	public function __construct(string $status)
	{
		$this->status = $status;
	}

	public function createPaymentFor(Order $order, $card)
	{
		$this->data = [
			"id" => "pay_" . substr(md5(mt_rand()), 0, 14),
			"entity" => "payment",
			"amount" => $order->amount,
			"currency" => $order->currency,
			"status" => $this->status,
			"order_id" => $order->id,
			"invoice_id" => null,
			"international" => false,
			"method" => "card",
			"amount_refunded" => 0,
			"refund_status" => null,
			"captured" => false,
			"description" => null,
			"card_id" => "card_" . substr(md5(mt_rand()), 0, 14),
			"bank" => null,
			"wallet" => null,
			"vpa" => null,
			"email" => $card['email'] ?? '',
			"contact" => $card['contact'] ?? '',
			"notes" => [],
			"fee" => 1,
			"tax" => 0,
			"error_code" => null,
			"error_description" => null,
			"error_source" => null,
			"error_step" => null,
			"error_reason" => null,
			"acquirer_data" => [
				// "rrn" => "032540100810"
			],
			"created_at" => 1605871409
		];
	}

	public function __get($property)
    {
	    if (array_key_exists($property, $this->data)) {
    		return $this->data[$property];
        }

        return;
    }
}	