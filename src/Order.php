<?php

namespace Ajuchacko\RazorpayHttp;

use ArrayAccess;

class Order implements ArrayAccess
{
	private static $states = ['created', 'attempted', 'paid'];
    
    protected $payment_response = [];
	
	public $response = [];

	public $payments = [];
	
	public function create(array $parameters = [])
    {
    	$this->newResponse($parameters);

    	return $this;
    }

    public function fetch(string $order_id)
    {
    	return $this->manager()->orders()->first(function (Order $order) use ($order_id) {
    		return $order->id === $order_id;
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
    	$this->updateStatus($this->status() === self::$states[0]);
    	++$this->attempts;

    	$payment = new Payment($payment_status);
    	$payment->createPaymentFor($this, $card);
    	$this->payments[] = $payment;

        $signature = hash_hmac(
            'sha256', "{$this->id}|{$payment->id}", FakeApi::getSecret()
        );
        $this->payment_response = [
            'razorpay_payment_id' => $payment->id,
            'razorpay_signature'  => $signature,
            'razorpay_order_id'   => $this->id
        ];

    	return $this;
    }

	public function updateStatus(bool $update)
	{
		$current_status = $this->status();

	    $key = array_search($current_status, self::$states) + 1;

		$this->response['status'] = $update ? self::$states[$key] : $current_status;
	}

    public function payments()
    {
    	$payments = collect($this->payments);

    	return collect([
    		'entity' => 'collection',
    		'count'  => $payments->count(),
    		'items'  => $payments
    	]);
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

    public function status()
    {
    	return $this->response['status'];
    }

    public function get($key, $default = null)
    {
        if (array_key_exists($key, $this->payment_response)) {
            return $this->payment_response[$key];
        }

        return value($default);
    }

    public function offsetExists($offset)
    {
        return isset($this->payment_response[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }
    
    public function offsetSet($offset, $value)
    {
        # code...
    }

    public function offsetUnset($offset)
    {
        # code...
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
