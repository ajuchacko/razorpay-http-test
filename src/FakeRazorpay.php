<?php

namespace Ajuchacko\RazorpayHttp;

use Illuminate\Support\Str;
use ReflectionClass;

class FakeRazorpay
{
	private $orders;

	public function __construct()
	{
		# code...
	}

	public function __call($property, $args)
	{
		if (property_exists($this, $property)) {
            return collect($this->{$property});
        }

        throw new \BadMethodCallException("Undefined Method [{$name}] called.");
	}

	public function __get($name)
	{
		$class = "\\Ajuchacko\\RazorpayHttp\\".ucwords($name);

        if (class_exists($class) && ! (new ReflectionClass($class))->isAbstract()) {
        	$property = Str::plural($name);
            return $this->{$property}[] = new $class;
        }

        throw new \BadMethodCallException("Undefined Property [{$name}] called.");
	}
}
