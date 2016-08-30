<?php

/**
 * \Fuel\Core\Uriの拡張
 */
class Uri extends \Fuel\Core\Uri
{

	/**
	 * 共有URLを作成
	 *
	 * @param string $hash
	 * @param string $action
	 * @param string $date
	 * @return string
	 */
	public static function create_share_url($hash, $action = 'info', $date = null)
	{
		if (empty($hash))
		{
			return '';
		}

		$url = Config::get('const.system.url.share').'#/customer/'.$action.'/'.$hash.'/';
		if ($date)
		{
			$url .= Date::format_datetime($date, 'Y-m-d');
		}

		return $url;
	}

}
