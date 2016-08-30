<?php

use \Fuel\Core\DB;

/**
 * 共有URL API
 */
class Controller_V1_Share extends Controller_V1_Abstract
{

	/**
	 * 利用者情報を取得
	 */
	public function get_customer()
	{
		$customer = Model_Customer_Share::get_customer_by_hash(Input::get('hash'));
		if ($customer === null)
		{
			$this->response(['result' => 'ng'], 200);
			return;
		}

		$customer_result						 = $customer->to_array();
		$customer_result['latest_hearing_at']	 = Model_Hearing::latest_hearing_at($customer->id);
		Arr::delete($customer_result, ['id', 'created_at', 'updated_at', 'deleted_at', 'created_user_id', 'updated_user_id', 'twilio_number', 'twilio_client', 'note', 'gcm_push_token', 'is_connected']);
		$this->response(['result' => 'ok', 'customer' => Date::date_string_to_iso8601($customer_result)], 200);
	}

	/**
	 * 聞き取り情報を取得
	 */
	public function get_hearings()
	{
		$customer = Model_Customer_Share::get_customer_by_hash(Input::get('hash'));
		if ($customer === null)
		{
			$this->response(['result' => 'ng'], 200);
			return;
		}

		$query		 = Model_Hearing::get_search_query(['customer_id' => $customer->id]);
		$query->order_by('hearing_at', 'DESC');
		$hearings	 = $query->get();

		// 一般公開画面用のため、パラメータ制限と追加
		$hearings_result = [];
		foreach ($hearings as $row)
		{
			$hearing				 = $row->to_array();
			$hearing_content		 = json_decode($row->hearing_content, true);
			$hearing['sentiment']	 = Arr::get($hearing_content, 'impression_1') * 2;
			Arr::delete($hearing, ['id', 'created_at', 'updated_at', 'deleted_at', 'created_user_id', 'updated_user_id', 'customer_id', 'hearing_content']);
			$hearings_result[]		 = $hearing;
		}

		$this->response(['result' => 'ok', 'hearings' => $hearings_result], 200);
	}

	/**
	 * 1件の聞き取り情報を取得
	 */
	public function get_hearing()
	{
		$customer = Model_Customer_Share::get_customer_by_hash(Input::get('hash'));
		if ($customer === null)
		{
			$this->response(['result' => 'ng'], 200);
			return;
		}

		$query	 = Model_Hearing::get_search_query(['customer_id' => $customer->id, 'hearing_at' => Input::get('date')]);
		$hearing = $query->get_one();
		if ($hearing === null or $hearing->hearing_content === null)
		{
			$this->response(['result' => 'ng'], 200);
			return;
		}

		// 一般公開画面用のため、パラメータ制限と追加
		$hearing->hearing_content	 = json_decode($hearing->hearing_content);
		$hearing_result				 = Date::date_string_to_iso8601($hearing->to_array());
		Arr::delete($hearing, ['id', 'created_at', 'updated_at', 'deleted_at', 'created_user_id', 'updated_user_id', 'customer_id']);

		$this->response(['result' => 'ok', 'hearing' => $hearing_result], 200);
	}

	/**
	 * Twilio履歴を取得
	 */
	public function get_twilio_histories()
	{
		$customer = Model_Customer_Share::get_customer_by_hash(Input::get('hash'));
		if ($customer === null)
		{
			$this->response(['result' => 'ng'], 200);
			return;
		}

		$histories = Model_Twilio_History::find_by_date($customer->id, Input::get('date'));

		// 一般公開画面用のため、パラメータ制限と追加
		$histories_result = [];
		foreach ($histories as $row)
		{
			$history			 = $row->to_array();
			Arr::delete($history, ['id', 'created_at', 'updated_at', 'deleted_at', 'created_user_id', 'updated_user_id', 'staff_id', 'customer_id', 'to', 'from', 'sid', 'parent_call_sid', 'account_sid', 'price']);
			$histories_result[]	 = $history;
		}

		$this->response(['result' => 'ok', 'twilio_histories' => $histories_result], 200);
	}

	/**
	 * 共有URLの作成
	 */
	public function post_create()
	{
		$customer = Model_Customer::find((int) Input::post_json('customer_id'));
		if ($customer === null)
		{
			$this->response(['result' => 'ng', 'error' => '該当する情報が見つかりませんでした。'], 200);
			return;
		}

		$validate = Model_Customer_Share::validate();
		if ($validate->run(Input::post_json()) === false)
		{
			$this->response(['result' => 'ng', 'error' => $validate->get_messages()], 200);
			return;
		}

		try
		{
			DB::start_transaction();

			// 共有URLを複数持たないように
			if ($customer->share)
			{
				$share = Model_Customer_Share::find($customer->share->id);
				$share->delete();
			}

			// 保存
			$hash	 = Str::random('alnum', 30);
			$share	 = Model_Customer_Share::forge([
						'customer_id'	 => $customer->id,
						'hash'			 => $hash,
						'password'		 => \Auth\Auth::instance()->hash_password(Input::post_json('password')),
			]);
			$share->save();

			DB::commit_transaction();
			$this->response(['result' => 'ok', 'share_url' => Uri::create_share_url($hash, 'hearing')], 200);
		}
		catch (Exception $e)
		{
			DB::rollback_transaction();
			$this->response(['result' => 'ng', 'error' => $e->getMessage()], 200);
		}
	}

	/**
	 * 共有URLの受け入れ
	 */
	public function post_accept()
	{
		if (Model_Customer_Share::accept(Input::post_json('hash'), Input::post_json('password')))
		{
			$this->response(['result' => 'ok'], 200);
		}
		else
		{
			$this->response(['result' => 'ng', 'error' => 'URLまたはパスワードが間違っています。'], 200);
		}
	}

	/**
	 * 共有URLの受け入れ確認
	 */
	public function post_is_accept()
	{
		if (Model_Customer_Share::is_accept(Input::post_json('hash')))
		{
			$this->response(['result' => 'ok',], 200);
		}
		else
		{
			$this->response(['result' => 'ng', 'error' => 'URLまたはパスワードが間違っています。'], 200);
		}
	}

	/**
	 * 共有URLの作成
	 */
	public function post_change_password()
	{
		$customer = Model_Customer::find((int) Input::post_json('customer_id'));
		if ($customer === null || $customer->share === null)
		{
			$this->response(['result' => 'ng', 'error' => '該当する情報が見つかりませんでした。'], 200);
			return;
		}

		$validate = Model_Customer_Share::validate();
		if ($validate->run(Input::post_json()) === false)
		{
			$this->response(['result' => 'ng', 'error' => $validate->get_messages()], 200);
			return;
		}

		try
		{
			DB::start_transaction();

			$share			 = Model_Customer_Share::find($customer->share->id);
			$share->password = \Auth\Auth::instance()->hash_password(Input::post_json('password'));
			$share->save();

			DB::commit_transaction();
			$this->response(['result' => 'ok'], 200);
		}
		catch (Exception $e)
		{
			DB::rollback_transaction();
			$this->response(['result' => 'ng', 'error' => $e->getMessage()], 200);
		}
	}

	/**
	 * 共有URLの作成
	 */
	public function post_delete()
	{
		$customer = Model_Customer::find((int) Input::post_json('customer_id'));
		if ($customer === null || $customer->share === null)
		{
			$this->response(['result' => 'ng', 'error' => '該当する情報が見つかりませんでした。'], 200);
			return;
		}

		try
		{
			DB::start_transaction();

			$share = Model_Customer_Share::find($customer->share->id);
			$share->delete();

			DB::commit_transaction();
			$this->response(['result' => 'ok'], 200);
		}
		catch (Exception $e)
		{
			DB::rollback_transaction();
			$this->response(['result' => 'ng', 'error' => $e->getMessage()], 200);
		}
	}

}
