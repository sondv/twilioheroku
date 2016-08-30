<?php

/**
 * 認証API
 */
class Controller_V1_Auth extends Controller_V1_Abstract
{

	/**
	 * ログイン
	 */
	public function post_login()
	{
		if (Model_Staff::login(Input::post_json('login_id'), Input::post_json('password')))
		{
			$staff = Model_Staff::logged_staff();
			$this->response([
				'result' => 'ok',
				'staff'	 => [
					'id'				 => $staff['id'],
					'family_name'		 => $staff['family_name'],
					'given_name'		 => $staff['given_name'],
					'family_name_kana'	 => $staff['family_name_kana'],
					'given_name_kana'	 => $staff['given_name_kana'],
					'email'				 => $staff['email'],
					'phone_number'		 => $staff['phone_number'],
				],
			], 200);
		}
		else
		{
			$this->response(['result' => 'ng', 'error' => 'ログイン出来ませんでしたAAAAAAAABBBB。'], 200);
		}
	}

	/**
	 * ログインの確認
	 */
	public function post_is_logged()
	{
		if (Model_Staff::is_logged())
		{
			$this->response(['result' => 'ok',], 200);
		}
		else
		{
			$this->response(['result' => 'ng', 'error' => 'ログインしていません。'], 200);
		}
	}

	/**
	 * ログアウト
	 */
	public function post_logout()
	{
		\Session::delete(Model_Staff::SESSION_KEY_LOGIN_ID);
		\Session::delete(Model_Staff::SESSION_KEY_LOGIN_HASH);
		$this->response(['result' => 'ok'], 200);
	}
}
