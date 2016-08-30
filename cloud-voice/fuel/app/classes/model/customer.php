<?php

/**
 * 利用者
 *
 * @package  app
 * @extends  ORM Model
 */
class Model_Customer extends Model_Abstract
{

	protected static $_table_name	 = 'customers';
	protected static $_properties	 = [
		'id',
		'created_at',
		'updated_at',
		'deleted_at',
		'created_user_id',
		'updated_user_id',
		'family_name',
		'given_name',
		'family_name_kana',
		'given_name_kana',
		'post_code',
		'address',
		'blood_type',
		'birthday',
		'height',
		'weight',
		'temperature',
		'blood_pressure_systolic',
		'blood_pressure_diastolic',
		'phone_number_1',
		'phone_number_2',
		'twilio_number',
		'twilio_client',
		'email',
		'share_email_1',
		'share_email_2',
		'share_email_3',
		'share_email_4',
		'share_email_5',
		'note',
		'photo',
		'gcm_push_token',
		'is_connected',
	];

	/**
	 * Has One
	 */
	protected static $_has_one = [
		'share' => [
			'model_to'	 => 'Model_Customer_Share',
			'key_from'	 => 'id',
			'key_to'	 => 'customer_id',
		],
	];

	/**
	 * 端末接続状態：接続済み
	 */
	const IS_CONNECTED_YES = 1;

	/**
	 * 端末接続状態：未接続
	 */
	const IS_CONNECTED_NO = 0;

	/**
	 * Validationを返す
	 *
	 * @param string $factory
	 * @return Validation
	 */
	public static function validate($factory)
	{
		$val = Validation::forge($factory.Str::random('sha'));
		if ($factory === 'push_token')
		{
			$val->add('number', 'twilio番号')->add_rule('max_length', 50)->add_rule('required');
			$val->add('token', 'プッシュ通知用トークン')->add_rule('max_length', 256)->add_rule('required');
		}
		else if ($factory === 'save')
		{
			// 登録・編集
			$val->add_field('family_name', 'お名前（姓）', 'required|max_length[20]');
			$val->add_field('given_name', 'お名前（名）', 'required|max_length[20]');
			$val->add_field('family_name_kana', 'カナ（名）', 'max_length[40]');
			$val->add_field('given_name_kana', 'カナ（名）', 'max_length[40]');
			$val->add_field('post_code', '郵便番号', 'max_length[50]');
			$val->add_field('address', '住所', 'max_length[255]');
			$val->add_field('blood_type', '血液型', 'max_length[2]');
			$val->add_field('birthday', '生年月日', 'date');
			$val->add_field('height', '身長', 'valid_string[numeric,dots]');
			$val->add_field('weight', '体重', 'valid_string[numeric,dots]');
			$val->add_field('temperature', '体温', 'valid_string[numeric,dots]');
			$val->add_field('blood_pressure_systolic', '最高血圧', 'valid_string[numeric,dots]');
			$val->add_field('blood_pressure_diastolic', '最低血圧', 'valid_string[numeric,dots]');
			$val->add_field('phone_number_1', '電話番号1', 'max_length[50]');
			$val->add_field('phone_number_2', '電話番号2', 'max_length[50]');
			$val->add_field('twilio_number', 'twilio番号', 'max_length[50]');
			$val->add_field('twilio_client', 'twilioクライアント名', 'max_length[50]');
			$val->add_field('email', 'メールアドレス', 'max_length[255]|valid_email');
			$val->add_field('share_email_1', '共有メールアドレス', 'max_length[255]|valid_email');
			$val->add_field('share_email_2', '共有メールアドレス', 'max_length[255]|valid_email');
			$val->add_field('share_email_3', '共有メールアドレス', 'max_length[255]|valid_email');
			$val->add_field('share_email_4', '共有メールアドレス', 'max_length[255]|valid_email');
			$val->add_field('share_email_5', '共有メールアドレス', 'max_length[255]|valid_email');
			$val->add_field('note', 'メモ', 'max_length[1000]');
			$val->add_field('gcm_push_token', 'GCMプッシュトークン', 'max_length[255]');
		}
		return $val;
	}

	/**
	 * 条件に一致する利用者を返す
	 *
	 * @return Model_Customer
	 */
	public static function search()
	{
		$query = self::query();
		$query->where('deleted_at', 'IS', null);
		$query->order_by('id', 'ASC');
		return $query->get();
	}

	/**
	 * 検索クエリを返す
	 *
	 * @param array $param
	 * @return Query
	 */
	public static function get_search_query($params)
	{
		$query = self::query();
		$query->where('deleted_at', 'IS', null);
		$query->order_by('id', 'ASC');

		// キーワード
		$keywords = Str::explode_with_cleaning(Arr::get($params, 'keyword'));
		if ( ! empty($keywords))
		{
			$query->where_open();
			foreach ($keywords as $index => $keyword)
			{
				$keyword = "%{$keyword}%";
				if ($index === 0)
				{
					$query->where('family_name', 'LIKE', $keyword);
					$query->or_where('given_name', 'LIKE', $keyword);
					$query->or_where('family_name_kana', 'LIKE', $keyword);
					$query->or_where('given_name_kana', 'LIKE', $keyword);
					$query->or_where('address', 'LIKE', $keyword);
					$query->or_where('phone_number_1', 'LIKE', $keyword);
					$query->or_where('phone_number_2', 'LIKE', $keyword);
					$query->or_where('twilio_number', 'LIKE', $keyword);
					$query->or_where('email', 'LIKE', $keyword);
					$query->or_where('share_email_1', 'LIKE', $keyword);
					$query->or_where('share_email_2', 'LIKE', $keyword);
					$query->or_where('share_email_3', 'LIKE', $keyword);
					$query->or_where('share_email_4', 'LIKE', $keyword);
					$query->or_where('share_email_5', 'LIKE', $keyword);
					$query->or_where('note', 'LIKE', $keyword);
				}
				else
				{
					$query->or_where('family_name', 'LIKE', $keyword);
					$query->or_where('given_name', 'LIKE', $keyword);
					$query->or_where('family_name_kana', 'LIKE', $keyword);
					$query->or_where('given_name_kana', 'LIKE', $keyword);
					$query->or_where('address', 'LIKE', $keyword);
					$query->or_where('phone_number_1', 'LIKE', $keyword);
					$query->or_where('phone_number_2', 'LIKE', $keyword);
					$query->or_where('twilio_number', 'LIKE', $keyword);
					$query->or_where('email', 'LIKE', $keyword);
					$query->or_where('share_email_1', 'LIKE', $keyword);
					$query->or_where('share_email_2', 'LIKE', $keyword);
					$query->or_where('share_email_3', 'LIKE', $keyword);
					$query->or_where('share_email_4', 'LIKE', $keyword);
					$query->or_where('share_email_5', 'LIKE', $keyword);
					$query->or_where('note', 'LIKE', $keyword);
				}
			}
			$query->where_close();
		}

		return $query;
	}

	/**
	 * JSONデータを保存
	 *
	 * @param array $json
	 * @return type
	 */
	public static function save_json($json)
	{
		if (empty($json) and isset($json['customer']) === false)
		{
			return;
		}

		$customer = $json['customer'];
		if (isset($customer['id']))
		{
			$model = self::find((int) $customer['id']);
			$model->set($customer);
			$model->update();
		}
		else
		{
			$model = self::forge($customer);
			$model->create();
		}
	}

	/**
	 * twilio番号よりGCMプッシュトークンを更新する
	 *
	 * @param string $twilio_number
	 * @param string $push_token
	 * @return int
	 */
	public static function update_push_token($twilio_number, $push_token)
	{
		if (empty($twilio_number) or empty($push_token))
		{
			return null;
		}

		$customer = self::query()
				->where('twilio_number', $twilio_number)
				->order_by('created_at', 'DESC')
				->limit(1)
				->get_one();

		if ($customer)
		{
			$customer->gcm_push_token = $push_token;
			return $customer->save();
		}
		return null;
	}

	/**
	 * twilio番号より利用者を返す
	 *
	 * @param string $twilio_number
	 * @return array
	 */
	public static function find_by_twilio_number($twilio_number)
	{
		if (empty($twilio_number))
		{
			return null;
		}

		return self::query()
						->where('twilio_number', $twilio_number)
						->order_by('created_at', 'DESC')
						->limit(1)
						->get_one();
	}

	/**
	 * 電話番号より利用者を返す
	 *
	 * @param string $phone_number
	 * @return Model
	 */
	public static function find_by_phone_number($phone_number)
	{
		if (empty($phone_number))
		{
			return null;
		}

		return self::query()
						->where_open()
						->or_where('phone_number_1', $phone_number)
						->or_where('phone_number_2', $phone_number)
						->where_close()
						->order_by('created_at', 'DESC')
						->limit(1)
						->get_one();
	}

}
