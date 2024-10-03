<?php

namespace Cat\Helpers\General;

use Cat\Contracts\HelperContract;
use Cat\Helpers\HelperTrait;

class EchoDemo implements HelperContract
{
	use HelperTrait;

	public function aaa($argument)
	{
		dump(__METHOD__.'aaa');
	}


	public function bbb($argument)
	{
		dump(__METHOD__.'bbb');
	}


	public function title(): string
	{
		return 'aaaaaaaaa';
	}


	public function description(): string
	{
		return 'b';
	}


	public function __invoke($argument)
	{
		return $this->aaa($argument);
	}
}
