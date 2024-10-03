<?php

namespace Cat\Helpers\General;

use Cat\Contracts\HelperContract;
use Cat\Helpers\HelperTrait;

/**
 * dogTitle
 * sadfasdfsadfh我是
 */
class Dog implements HelperContract
{
	use HelperTrait;

	public function bite()
	{
		echo 'dog bite';
	}


	public function title(): string
	{
		return 'dogTitle';
	}


	public function description(): string
	{
		return 'sadfasdfsadfh我是';
	}


	public function __invoke($argument)
	{
		return $this->bite($argument);
	}
}
