<?php

namespace Ajuchacko\RazorpayHttp;

class Order {
	
	public $response = [];
	
	public function create(array $parameters = [])
    {
    	$this->newResponse($parameters);

    	return $this;
    }

    private function schema()
    {
	    return array_keys([
			"id" => "order_DaZlswtdcn9UNV",
			"amount" => 50000,
			"entity" => "order",
			"partial_payment" => false,
			"amount_paid" => 0,
			"amount_due" => 50000,
			"currency" => "INR",
			"receipt" => "Receipt #20",
			"status" => "created",
			"attempts" => 0,
			"notes" => [],
			"created_at" => 1572502745
		]);
	}

	public function newResponse(array $parameters)
	{
		$this->response = [
			"id" => "order_" . substr(md5(mt_rand()), 0, 14),
			"amount" => $parameters['amount'],
			"entity" => "order",
			"partial_payment" => $parameters['partial_payment'] ?? false,
			"amount_paid" => 0,
			"amount_due" => $parameters['amount'],
			"currency" => $parameters['currency'],
			"receipt" => $parameters['receipt'] ?? '',
			"status" => "created",
			"attempts" => 0,
			"notes" => $parameters['notes'] ?? [],
			"created_at" => time()
		];
	}

    public function __get($property)
    {
	    if (array_key_exists($property, $this->response)) {
    		return $this->response[$property];
        }

        throw new \BadMethodCallException("Undefined Property [{$property}] called.");
    }
}
