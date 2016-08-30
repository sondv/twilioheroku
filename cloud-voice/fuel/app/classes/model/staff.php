<?php

use Fuel\Core\Input;

/**
 * スタッフ
 *
 * @package  app
 * @extends  ORM Model
 */
class Model_Staff extends Model_Abstract
{

	/**
	 * テーブル名の設定
	 *
	 * @var string
	 */
	protected static $_table_name = 'staffs';

	/**
	 * フィールド名の設定
	 *
	 * @var array
	 */
	protected static $_properties = [
		'id',
		'created_at',
		'updated_at',
		'deleted_at',
		'created_user_id',
		'updated_user_id',
		'login_id',
		'password',
		'family_name',
		'given_name',
		'family_name_kana',
		'given_name_kana',
		'email',
		'phone_number',
		'login_hash',
		'logined_at',
	];

	/**
	 * セッションキー：ログインID
	 */
	const SESSION_KEY_LOGIN_ID = 'staff_login_id';

	/**
	 * セッションキー：ログインハッシュ
	 */
	const SESSION_KEY_LOGIN_HASH = 'staff_login_hash';

	/**
	 * バリデーション作成
	 *
	 * @param string $factory
	 * @param array  $json
	 * @return  \Validation
	 */
	public static function validate($factory, $json = null)
	{
		$val = Validation::forge($factory.Str::random('sha'));
		if ($factory === 'save')
		{
			// 登録・編集
			$val->add_field('login_id', 'ログインID', 'required|valid_string[alpha,numeric,dashes]|max_length[50]');
			$val->add_field('family_name', '氏名（姓）', 'required|max_length[20]');
			$val->add_field('given_name', '氏名（名）', 'required|max_length[20]');
			$val->add_field('family_name_kana', 'カナ（姓）', 'required|max_length[40]');
			$val->add_field('given_name_kana', 'カナ（名）', 'required|max_length[40]');
			$val->add_field('email', 'メールアドレス', 'valid_email|max_length[255]');
			$val->add_field('phone_number', '電話番号', 'max_length[50]');
			if ( ! Arr::get($json, 'staff.id') or Arr::get($json, 'staff.password'))
			{
				$val->add_field('password', 'パスワード', 'required|min_length[6]');
			}
		}
		return $val;
	}

	/**
	 * 不要なパラメーターの削除
	 *
	 * @param array  $staff
	 */
	public static function clean($staff)
	{
		Arr::delete($staff, ['deleted_at', 'password']);
		return $staff;
	}

	/**
	 * ログインIDからスタッフ情報を取得
	 *
	 * @param   string $login_id
	 * @return  null|array
	 */
	public static function get_by_login_id($login_id)
	{
		$query = self::query();
		$query->where('login_id', '=', $login_id);
		return $query->get_one();
	}

	/**
	 * 既に登録されているログインIDか
	 *
	 * @param   string $login_id
	 * @return  bool
	 */
	public static function exists_login_id($login_id, $exclude_id = null)
	{
		$staff = self::get_by_login_id($login_id);

		if ($staff)
		{
			if ($exclude_id and $staff->id == $exclude_id)
			{
				return false;
			}
			return true;
		}
		return false;
	}

	/**
	 * JSONから保存
	 *
	 * @param  array $json
	 * @return null|int 作成時はスタッフID
	 */
	public static function save_json($json)
	{
		if (empty($json) and isset($json['staff']) === false)
		{
			return null;
		}

		$staff = $json['staff'];
		if (empty($staff['password']))
		{
			unset($staff['password']);
		}
		else
		{
			$staff['password'] = \Auth\Auth::instance()->hash_password($staff['password']);
		}

		if (isset($staff['id']))
		{
			$model = self::find((int) $staff['id']);
			$model->set($staff);
			$model->update();
			return (int) $staff['id'];
		}
		else
		{
			$model = self::forge($staff);
			$model->create();
			return (int) $model->id;
		}
	}

	/**
	 * 検索クエリを取得
	 *
	 * @return Orm\Query
	 */
	public static function get_search_query()
	{
		$query = self::query();

		// キーワード
		if (Input::get('search_keyword'))
		{
			$input = '%'.Input::get('search_keyword').'%';
			$query->or_where('family_name', 'like', self::trim_line_tel_number($input));
			$query->or_where('given_name', 'like', self::trim_line_tel_number($input));
			$query->or_where('family_name_kana', 'like', self::trim_line_tel_number($input));
			$query->or_where('given_name_kana', 'like', self::trim_line_tel_number($input));
			$query->or_where('email', 'like', $input);
			$query->or_where('phone_number', 'like', $input);
		}

		return $query;
	}

	/**
	 * ログイン
	 *
	 * @param string $login_id
	 * @param string $password
	 * @return bool
	 */
	public static function login($login_id, $password)
	{
		if (empty($login_id) or empty($password))
		{
			return false;
		}

		$staff = self::get_by_login_id($login_id);
		if ($staff and $staff->password === \Auth\Auth::instance()->hash_password($password))
		{
			$login_hash = sha1(\Config::get('auth.salt').$staff->login_id.time());

			$staff->login_hash	 = $login_hash;
			$staff->logined_at	 = date('Y-m-d H:i:s');
			$staff->save();

			\Session::set(self::SESSION_KEY_LOGIN_ID, $staff->id);
			\Session::set(self::SESSION_KEY_LOGIN_HASH, $login_hash);
			\Session::instance()->rotate();

			return true;
		}
		return false;
	}

	/**
	 * ログインスタッフの取得
	 *
	 * @return null|array
	 */
	public static function logged_staff()
	{
		return self::find(\Session::get(self::SESSION_KEY_LOGIN_ID));
	}

	/**
	 * ログインスタッフの確認
	 *
	 * @return bool
	 */
	public static function is_logged()
	{
		$staff = self::logged_staff();
		if ($staff and $staff->login_hash === \Session::get(self::SESSION_KEY_LOGIN_HASH))
		{
			$staff->logined_at = date('Y-m-d H:i:s');
			$staff->save();
			return true;
		}

		\Session::delete(self::SESSION_KEY_LOGIN_ID);
		\Session::delete(self::SESSION_KEY_LOGIN_HASH);

		return false;
	}

}
