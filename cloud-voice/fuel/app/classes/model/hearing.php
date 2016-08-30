<?php

/**
 * 聞き取り
 *
 * @package  app
 * @extends  ORM Model
 */
class Model_Hearing extends Model_Abstract
{
	protected static $_table_name	 = 'hearings';
	protected static $_properties	 = [
		'id',
		'created_at',
		'updated_at',
		'deleted_at',
		'created_user_id',
		'updated_user_id',
		'customer_id',
		'hearing_at',
		'hearing_content',
	];

	/**
	 * Validationを返す
	 *
	 * @param string $factory
	 * @return Validation
	 */
	public static function validate($factory, $json = null)
	{
		$val = Validation::forge($factory.Str::random('sha'));
		if ($factory === 'save' and ( empty($json) or isset($json['hearing']) === false))
		{
			// 登録・編集
			$val->add_field('customer_id', '利用者ID', 'required|max_length[19]|valid_string[numeric]');
			$val->add_field('hearing_at', '聞き取り日', 'required|date');
		}
		else if ($factory === 'search')
		{
			// 検索
		}
		return $val;
	}

	/**
	 * 聞き取り情報を返す
	 *
	 * @return Model_Hearing
	 */
	public static function find_one($customer_id, $hearing_at)
	{
		$query = self::query();
		$query->where('deleted_at', 'IS', null);
		$query->where('customer_id', '=', $customer_id);
		$query->where('hearing_at', '=', $hearing_at);
		return $query->get_one();
	}

	/**
	 * JSONデータを保存
	 *
	 * @param array $json
	 * @return type
	 */
	public static function save_json($json)
	{
		if (empty($json) and isset($json['hearing']) === false)
		{
			return;
		}

		$hearing = $json['hearing'];
		$model	 = self::find_one(Arr::get($hearing, 'customer_id'), Arr::get($hearing, 'hearing_at'));
		if ($model)
		{
			$model->set($hearing);
			$model->update();
		}
		else
		{
			$model = self::forge($hearing);
			$model->create();
		}
	}

	/**
	 * 検索クエリを取得
	 *
	 * @return Orm\Query
	 */
	public static function get_search_query($params)
	{
		$query = self::query();

		$query->where('deleted_at', 'IS', null);
		if (array_key_exists('customer_id', $params))
		{
			$query->where('customer_id', '=', Arr::get($params, 'customer_id'));
		}
		if (array_key_exists('hearing_at', $params))
		{
			$query->where('hearing_at', '>=', date('Y-m-d 00:00:00', strtotime(Arr::get($params, 'hearing_at'))));
			$query->where('hearing_at', '<=', date('Y-m-d 23:59:59', strtotime(Arr::get($params, 'hearing_at'))));
		}

		return $query;
	}

	/**
	 * 指定期間の聞き取り情報を返す
	 *
	 * @param int $customer_id
	 * @param string $hearing_at_begin YYYY-MM-DD
	 * @param string $hearing_at_end   YYYY-MM-DD
	 * @return Model_Hearing
	 */
	public static function find_by_period($customer_id, $hearing_at_begin, $hearing_at_end)
	{
		$query = self::query();
		$query->where('deleted_at', 'IS', null);
		$query->where('customer_id', '=', $customer_id);
		$query->where('hearing_at', '>=', $hearing_at_begin);
		$query->where('hearing_at', '<=', $hearing_at_end);
		$query->order_by('hearing_at', 'ASC');
		return $query->get();
	}

	/**
	 * 最近の聞き取り状況を返す
	 *
	 * @param int $customer_id
	 * @param int $limit
	 * @return Model_Hearing
	 */
	public static function recently($customer_id, $limit = 10)
	{
		$query = self::query();
		$query->where('deleted_at', 'IS', null);
		$query->where('customer_id', '=', $customer_id);
		$query->order_by('hearing_at', 'DESC');
		$query->limit($limit);
		return $query->get();
	}

	/**
	 * 最近の聞き取り状況と平均値を返す
	 *
	 * @param int $customer_id
	 * @param int $limit
	 * @return array
	 */
	public static function recently_with_average($customer_id, $limit = 10)
	{
		$recently = [];

		// 最近の聞き取り点数を加算していき、最後に平均値を求める
		$hearings = self::recently($customer_id, $limit);
		foreach ($hearings as $hearing)
		{
			$content = json_decode($hearing->hearing_content, true);
			foreach (array_keys(Config::get('const.hearing.target')) as $key)
			{
				if (isset($recently[$key]) === false)
				{
					$recently[$key] = 0;
				}
				$recently[$key] += \Mark::calculation($content, $key);
			}
		}

		// 点数の平均値を求める
		foreach (array_keys(Config::get('const.hearing.target')) as $key)
		{
			if (isset($recently[$key]) === false)
			{
				$recently[$key] = 0;
			}
			$recently[$key] = count($hearings) > 0 ? (float) number_format($recently[$key] / count($hearings), 1) : 0;
		}

		return $recently;
	}

	/**
	 * 最新の聞き取り日を返す
	 *
	 * @param  int $customer_id
	 * @return null | string
	 */
	public static function latest_hearing_at($customer_id)
	{
		$query	 = self::query();
		$query->where('deleted_at', 'IS', null);
		$query->where('customer_id', '=', $customer_id);
		$query->where('hearing_at', '<=', date('Y-m-d H:i:s'));
		$query->order_by('hearing_at', 'DESC');
		$hearing = $query->get_one();
		return ($hearing) ? $hearing->hearing_at : null;
	}

	/**
	 * 最新の聞き取り日を返す
	 *
	 * @param  int $customer_id
	 * @param  string $date   YYYY-MM-DD
	 * @return bool
	 */
	public static function is_already_hearing($customer_id, $date)
	{
		$query	 = self::query();
		$query->where('deleted_at', 'IS', null);
		$query->where('customer_id', '=', $customer_id);
		$query->where('hearing_at', 'BETWEEN', [Date::format_datetime($date, 'Y-m-d 00:00:00'), Date::format_datetime($date, 'Y-m-d 23:59:59')]);
		$hearing = $query->get_one();
		return ($hearing) ? true : false;
	}
}
