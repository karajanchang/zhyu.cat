<?php

namespace Cat\Helpers\General;

use Cat\Contracts\HelperContract;
use Cat\Helpers\HelperTrait;

/**
 * echo測試
 * asdfasfasf
 */
class EchoDemo2 implements HelperContract
{
	use HelperTrait;

	public function good()
	{
		dump('Hi', __METHOD__);

		return ;
	}


	public function title(): string
	{
		return 'echo測試';
	}


	public function description(): string
	{
		return 'asdfasfasf';
	}


	public function __invoke($argument)
	{
		return $this->good($argument);
	}
}
