<?php

namespace Ajuchacko\RazorpayHttp;

class Order {

	private static $states = ['created', 'attempted', 'paid'];
	
	public $response = [];

	public $payments = [];
	
	public function create(array $parameters = [])
    {
    	$this->newResponse($parameters);

    	return $this;
    }

    public function fetch(string $id)
    {
    	return $this->manager()->orders()->first(function ($value, $key) use ($id) {
    		return $value->id === $id;
    	});
    }

    public function all(array $options = [])
    {
    	return $this->manager()->orders()->filter(function ($order) {
    		return $order->id !== null;
    	});
    }

    public function paidUsing(array $card, string $payment_status)
    {
    	$this->updateStatus(true);
    	++$this->attempts;

    	$payment = new Payment($payment_status);
    	$payment->createPaymentFor($this, $card);
    	$this->payments[] = $payment;

    	return $this;
    }

	public function updateStatus(bool $update)
	{
		$current_status = $this->response['status'];

	    $key = array_search($current_status, self::$states) + 1;
		
		$this->response['status'] = $update ? self::$states[$key] : $current_status;
	}

    public function payments()
    {
    	$payments = collect($this->payments);

    	return [
    		'entity' => 'collection',
    		'count'  => $payments->count(),
    		'items'  => $payments
    	];
    }

    protected function manager()
    {
    	return app('razorpay');
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

        // throw new \BadMethodCallException("Undefined Property [{$property}] called.");
        return;
    }

   //  public function toArray()
   //  {
   //  	return [
   //  		"id" => $this->id,
			// "amount" => $this->amount,
			// "entity" => $this->entity,
			// "partial_payment" => $this->partial_payment,
			// "amount_paid" => $this->amount_paid,
			// "amount_due" => $this->amount_due,
			// "currency" => $this->currency,
			// "receipt" => $this->receipt,
			// "status" => $this->status,
			// "attempts" => $this->attempts,
			// "notes" => $this->notes,
			// "created_at" => $this->created_at,
   //  	];
   //  }
}
