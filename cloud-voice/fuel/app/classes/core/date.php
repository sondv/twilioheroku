<?php

/**
 * \Dateの拡張
 */
class Date extends \Fuel\Core\Date
{

	/**
	 * 配列内に、ISO8601形式の文字列があればY-m-d H:i:s形式に変換する
	 *
	 * @param $array
	 * @return mixed
	 */
	public static function iso8601_to_date_string($array)
	{
		foreach ($array as $key => &$val)
		{
			if (empty($val))
			{
				continue;
			}

			if (is_array($val))
			{
				$array[$key] = self::iso8601_to_date_string($val);
			}
			else if (gettype($val) === 'string' and preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}.\d{3}Z$/', $val))
			{
				$array[$key] = date('Y-m-d H:i:s', strtotime($val));
			}
		}
		return $array;
	}

	/**
	 * 配列内に、Y-m-d H:i:s形式の文字列があればISO8601形式に変換する
	 *
	 * @param $array
	 * @return mixed
	 */
	public static function date_string_to_iso8601($array)
	{
		foreach ($array as $key => &$val)
		{
			if (is_array($val))
			{
				$array[$key] = self::iso8601_to_date_string($val);
			}
			else if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $val) or preg_match('/^\d{4}-\d{2}-\d{2}$/', $val))
			{
				$datetime	 = new DateTime($val);
				$datetime->setTimezone(new DateTimeZone('GMT'));
				$array[$key] = $datetime->format('Y-m-d').'T'.$datetime->format('H:i:s').'.000Z';
			}
		}
		return $array;
	}

	/**
	 * 日付より年齢に変換する
	 *
	 * @param $date
	 * @return string
	 */
	public static function to_age($date)
	{
		if (strtotime($date) === -1)
		{
			return null;
		}

		return floor((date('Ymd') - date('Ymd', strtotime($date))) / 10000);
	}

	/**
	 * 空の日付データか？
	 *
	 * @param string $date
	 * @return bool
	 */
	public static function is_empty_date($date)
	{
		// 空かどうか検証
		if (empty($date) or
				$date === '0000-00-00' or
				$date === '0000/00/00' or
				$date === '0000-00-00 00:00:00' or
				$date === '0000/00/00 00:00:00' or
				strtotime($date) === false)
		{
			return true;
		}

		return false;
	}

	/**
	 * 空の時間データか？
	 *
	 * @param string $time
	 * @return bool
	 */
	public static function is_empty_time($time)
	{
		// 空かどうか検証
		if (empty($time) or $time === '00:00:00')
		{
			return true;
		}

		return false;
	}

	/**
	 * 日時を整形して返す
	 *
	 * @param string $datetime
	 * @param string $format
	 * @return string
	 */
	public static function format_datetime($datetime, $format = 'Y/m/d H:i')
	{
		if (self::is_empty_date($datetime))
		{
			return null;
		}

		\Config::load('const', true);
		$w_index = strpos($format, 'w');
		if ($w_index !== false)
		{
			$w		 = date('w', strtotime($datetime));
			$wj		 = \Config::get('const.system.week_short.'.$w);
			$format	 = str_replace('w', $wj, $format);
		}

		return date($format, strtotime($datetime));
	}

	/**
	 * 営業時間内か？
	 *
	 * @param string $datetime
	 * @param string $format
	 * @return string
	 */
	public static function is_business_hours($time = null)
	{
		if ($time === null)
		{
			$time = time();
		}

		$current_date = date('Y-m-d', $time);

		$from	 = strtotime($current_date.' '.Config::get('const.system.business_hours.from').':00');
		$to		 = strtotime($current_date.' '.Config::get('const.system.business_hours.to').':00');
		$current = strtotime(date('Y-m-d G:i', $time));
		if ($from <= $current and $current <= $to)
		{
			return true;
		}
		return false;
	}

}
