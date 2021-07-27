<?php

namespace Ajuchacko\RazorpayHttp;

use ReflectionClass;

class FakeRazorpay
{
	public function __construct()
	{
		# code...
	}

	public function __get($name)
	{
		$class = "\\Ajuchacko\\RazorpayHttp\\".ucwords($name);

        if (class_exists($class) && ! (new ReflectionClass($class))->isAbstract()) {
            return new $class;
        }

        throw new \BadMethodCallException("Undefined Property [{$name}] called.");
	}
}
