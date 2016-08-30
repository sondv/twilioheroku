<?php

/**
 * \Inputの拡張
 */
class Input extends \Fuel\Core\Input
{

	/**
	 * JSON送信を取得する
	 *
	 * @param string $key
	 * @return array
	 */
	public static function post_json($key = null)
	{
		$json_string = file_get_contents('php://input');
		if (empty($json_string))
		{
			return null;
		}

		$json = Date::iso8601_to_date_string(json_decode($json_string, true));
		if ($json and $key)
		{
			return Arr::get($json, $key);
		}

		return $json;
	}
}
