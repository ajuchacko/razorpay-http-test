<?php

namespace Ajuchacko\RazorpayHttp;

use Illuminate\Support\Str;
use Razorpay\Api\Api;
use ReflectionClass;

class FakeApi extends Api
{
	private $orders;

	public function __construct($key, $secret)
	{
		parent::__construct($key, $secret);
	}

	public function newOrder(array $parameters = [])
    {
        return $this->order->create($parameters);
    }

	public function __call($property, $args)
	{
		if (property_exists($this, $property)) {
            return collect($this->{$property});
        }

        throw new \BadMethodCallException("Undefined Method [{$property}] called.");
	}

	public function __get($name)
	{
		$class = "\\Ajuchacko\\RazorpayHttp\\".ucwords($name);

        if (class_exists($class) && ! (new ReflectionClass($class))->isAbstract()) {
        	$property = Str::plural($name);
        	if ($property === 'payments') {
        		return new Payment;
        	}
            return $this->{$property}[] = new $class;
        }

        throw new \BadMethodCallException("Undefined Property [{$name}] called.");
	}
}
