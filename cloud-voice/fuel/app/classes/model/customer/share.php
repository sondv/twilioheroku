<?php

/**
 * 利用者.共有テーブル
 *
 * @package  app
 * @extends  ORM Model
 */
class Model_Customer_Share extends Model_Abstract
{

	protected static $_table_name	 = 'customer_shares';
	protected static $_properties	 = [
		'id',
		'created_at',
		'updated_at',
		'deleted_at',
		'created_user_id',
		'updated_user_id',
		'customer_id',
		'hash',
		'password',
		'logined_at',
	];

	/**
	 * Belongs To
	 */
	protected static $_belongs_to = [
		'customer' => [
			'model_to'	 => 'Model_Customer',
			'key_from'	 => 'customer_id',
			'key_to'	 => 'id',
		],
	];

	/**
	 * セッションキー：受け入れURL
	 */
	const SESSION_KEY_ACCEPT_URLS = 'share_accept_urls';

	/**
	 * Validationを返す
	 *
	 * @return Validation
	 */
	public static function validate()
	{
		$val = Validation::forge();
		$val->add_field('password', 'パスワード', 'required|min_length[6]');
		return $val;
	}

	/**
	 * 共有URLハッシュから検索
	 *
	 * @param string $hash
	 * @return array
	 */
	public static function find_by_hash($hash)
	{
		if (empty($hash))
		{
			return null;
		}

		return self::query()
						->where('hash', $hash)
						->limit(1)
						->get_one();
	}

	/**
	 * 共有URLハッシュより利用者を返す
	 *
	 * @param string $hash
	 * @return Model_Customer
	 */
	public static function get_customer_by_hash($hash)
	{
		$customer_hash = self::find_by_hash($hash);
		if ($customer_hash === null or $customer_hash->customer === null)
		{
			return null;
		}
		return $customer_hash->customer;
	}

	/**
	 * パスワードによる受け入れ
	 *
	 * @param string $hash
	 * @param string $password
	 * @return array
	 */
	public static function accept($hash, $password)
	{
		if (empty($hash) or empty($password))
		{
			return false;
		}

		$customer_hash = self::find_by_hash($hash);
		if ($customer_hash and $customer_hash->password === \Auth\Auth::instance()->hash_password($password) and $customer_hash->customer)
		{
			$customer_hash->logined_at	 = date('Y-m-d H:i:s');
			$customer_hash->save();

			$session = \Session::get(self::SESSION_KEY_ACCEPT_URLS);
			if (empty($session) or ! is_array($session))
			{
				$session = [];
			}
			$session[$hash] = true;
			\Session::set(self::SESSION_KEY_ACCEPT_URLS, $session);
			\Session::instance()->rotate();

			return true;
		}
		return false;
	}

	/**
	 * ハッシュが受け入れ状態かどうか確認
	 *
	 * @return bool
	 */
	public static function is_accept($hash)
	{
		$session = \Session::get(self::SESSION_KEY_ACCEPT_URLS, []);
		if (Arr::get($session, $hash))
		{
			return true;
		}

		return false;
	}
}
