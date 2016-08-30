<?php

/**
 * Twilio通話履歴テーブル
 *
 * @package  app
 * @extends  ORM Model
 */
class Model_Twilio_History extends Model_Abstract
{

	protected static $_table_name	 = 'twilio_histories';
	protected static $_properties	 = [
		'id',
		'created_at',
		'created_user_id',
		'updated_at',
		'updated_user_id',
		'deleted_at',
		'staff_id',
		'customer_id',
		'to',
		'from',
		'record_file_path',
		'sid',
		'parent_call_sid',
		'account_sid',
		'status', // ステータス参照；https://jp.twilio.com/docs/api/rest/call#call-status-values
		'start_time',
		'end_time',
		'duration',
		'price',
	];

	/**
	 * 緊急発進対象の関係者を取得
	 *
	 * @param  int $customer_id
	 * @return array
	 */
	public static function get_emergency_relations($customer_id)
	{
		return self::query()
						->where('deleted_at', 'IS', null)
						->where('customer_id', $customer_id)
						->order_by('start_time', 'ASC')
						->get();
	}

	/**
	 * SIDから取得
	 *
	 * @param  string $sid
	 * @return array
	 */
	public static function get_by_sid($sid)
	{
		if (empty($sid))
		{
			return null;
		}

		return self::query()
						->where('deleted_at', 'IS', null)
						->where('sid', $sid)
						->get_one();
	}

	/**
	 * parent_call_sidから取得
	 *
	 * @param  string $sid
	 * @return array
	 */
	public static function get_by_parent_call_sid($sid)
	{
		if (empty($sid))
		{
			return null;
		}

		return self::query()
						->where('deleted_at', 'IS', null)
						->where('parent_call_sid', $sid)
						->get_one();
	}

	/**
	 * 指定期間の通話履歴を返す
	 *
	 * @param int $customer_id
	 * @param string $from YYYY-MM-DD HH:II:SS
	 * @param string $to   YYYY-MM-DD HH:II:SS
	 * @return \Model_Twilio_History
	 */
	public static function find_by_period($customer_id, $from, $to)
	{
		return self::query()
						->where('deleted_at', 'IS', null)
						->where('customer_id', '=', $customer_id)
						->and_where_open()
						->or_where_open()
						->where('start_time', '>=', $from)
						->where('start_time', '<=', $to)
						->or_where_close()
						->or_where_open()
						->where('end_time', '>=', $from)
						->where('end_time', '<=', $to)
						->or_where_close()
						->and_where_close()
						->order_by('start_time', 'ASC')
						->get();
	}

	/**
	 * 指定日の通話履歴を返す
	 *
	 * @param int $customer_id
	 * @param string $date  YYYY-MM-DD HH:II:SS
	 * @return \Model_Twilio_History
	 */
	public static function find_by_date($customer_id, $date)
	{
		$time	 = strtotime($date);
		$from	 = date('Y-m-d 00:00:00', $time);
		$to		 = date('Y-m-d 23:59:59', $time);
		return self::find_by_period($customer_id, $from, $to);
	}

	/**
	 * 指定期間の通話履歴を返す
	 *
	 * @param string $to
	 * @param string $from
	 * @return \Model_Twilio_History
	 */
	public static function find_epmty_twilio_log($to, $from)
	{
		return self::query()
						->where('deleted_at', 'IS', null)
						->where('sid', 'IS', null)
						->where('to', '=', $to)
						->where('from', '=', $from)
						->order_by('created_at', 'DESC')
						->get_one();
	}

}
