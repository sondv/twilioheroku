<?php

use Fuel\Core\Controller_Rest;

/**
 * API v1 継承用コントローラー
 */
abstract class Controller_V1_Abstract extends Controller_Rest
{

	public function before()
	{
		parent::before();
	}

	public function after($response)
	{
		return parent::after($response);
	}
}
