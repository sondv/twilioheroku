<?php

/**
 * \Fuel\Core\Strの拡張
 */
class Str extends \Fuel\Core\Str
{

	/**
	 * 文字列を半角スペース＆カンマで区切り返す
	 *
	 * @param string $str
	 * @return array
	 */
	public static function explode_with_cleaning($str)
	{
		$saKV	 = mb_convert_kana($str, 'saKV');
		$cleand	 = str_replace([',', '、', '  '], ' ', $saKV);
		return explode(' ', $cleand);
	}

	/**
	 * 郵便番号を整形
	 *
	 * @param string $post_code
	 * @return string
	 */
	public static function format_post_code($post_code)
	{
		if (empty($post_code))
		{
			return '';
		}

		if (mb_strlen($post_code) === 7 and preg_match("/^[0-9]+$/", $post_code))
		{
			return '〒'.substr($post_code, 0, 3).'-'.substr($post_code, 3);
		}
		else
		{
			return '〒'.$post_code;
		}
	}

	/**
	 * 秒数を分秒へ変換する
	 *
	 * @param int $sec
	 * @return string
	 */
	public static function what_minutes($sec)
	{
		if ($sec > 60)
		{
			$minutes = (int) ($sec / 60);
			$second	 = $sec - ($minutes * 60);
			return $minutes.'分'.$second.'秒';
		}
		else
		{
			return $sec.'秒';
		}
	}

	/**
	 * 国際電話番号を付与する
	 *
	 * @param string $number   電話番号
	 * @param string $countory 国コード 日本:jp
	 * @return string
	 */
	public static function reset_international_calling_code($number, $countory = 'jp')
	{
		// 国際番号が設定済みであれば剥がす
		$code = \Fuel\Core\Config::get('const.system.international.calling_code.'.$countory);
		if (substr($number, 0, strlen($code)) === $code)
		{
			$number = substr($number, strlen($code));
		}

		// 市外局番の先頭に0があれば除去
		if (substr($number, 0, 1) === '0')
		{
			$number = substr($number, 1);
		}

		// 改めて国際番号を付与する
		return $code.$number;
	}

	/**
	 * 国際電話番号を除去する
	 *
	 * @param string $number  電話番号
	 * @return string
	 */
	public static function remove_international_calling_code($number)
	{
		if (empty($number))
		{
			return '';
		}

		$codes = \Fuel\Core\Config::get('const.system.international.calling_code');
		foreach ($codes as $code)
		{
			if (substr($number, '0', strlen($code)) === $code)
			{
				$number = substr($number, strlen($code));
			}
		}
		return $number;
	}

	/**
	 * 国内電話番号へ変換する
	 *
	 * @param string $number  電話番号
	 * @return string
	 */
	public static function to_local_phone_number($number)
	{
		$local_number = self::remove_international_calling_code($number);
		if (substr($local_number, 0, 1) !== '0')
		{
			$local_number = '0'.$local_number;
		}
		return $local_number;
	}

	/**
	 * Twilioのクライアント名から、Twilio電話番号へ変換
	 *
	 * @param string $number   Twilioクライアント名
	 * @return string
	 */
	public static function client_name_to_local_phone_number($number)
	{
		$number = str_replace('client:cloudvoice', '+', $number);
		return self::to_local_phone_number($number);
	}

	/**
	 * 全ての番号から、サーバー用の電話番号（国際番号なし、ハイフンなし）へ変換
	 *
	 * @param string $number 番号（Twilioクライアント名、その他番号）
	 * @return string
	 */
	public static function to_server_phone_number($number)
	{
		$number	 = self::client_name_to_local_phone_number($number);
		$number	 = str_replace('-', '', $number);
		return $number;
	}

}
