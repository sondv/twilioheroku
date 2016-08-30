<?php

use Fuel\Core\Log;
use Fuel\Core\DB;
use Fuel\Core\Config;
use Fuel\Core\Validation;

/**
 * TwilioAPI
 */
class Controller_V1_Gcm extends Controller_V1_Abstract
{

	/**
	 * プッシュで端末を起こす
	 *
	 * @return type
	 */
	public function get_wake_on_push()
	{
		Log::debug('gcm::get_wake_on_push');
		$api_key = Config::get('twilio.api_key');
		if (empty($api_key))
		{
			Log::warning(print_r(['APIキーが指定されていません', $api_key], true));
			$this->response(['result' => 'ng', 'message' => 'APIキーが指定されていません'], 400);
			return;
		}

		$customer = Model_Customer::find(Input::get('customer_id'));
		if (empty($customer) or empty($customer->gcm_push_token))
		{
			Log::warning(print_r(['利用者情報もしくはトークンが存在しません', Input::post('customer_id')], true));
			$this->response(['result' => 'ng', 'message' => '利用者情報もしくはトークンが存在しません'], 400);
			return;
		}

		try
		{
			DB::start_transaction();
			$result = $this->gcm_send($api_key, $customer->gcm_push_token);
			if (isset($result['success']) and $result['success'] > 0)
			{
				$customer->is_connected = Model_Customer::IS_CONNECTED_NO;
				$customer->save();
				DB::commit_transaction();

				Log::info(print_r(['success', Input::get('customer_id')], true));
				$this->response(['result' => 'ok'], 200);
			}
			else
			{
				DB::rollback_transaction();
				Log::warning(print_r(['error', $result['results'][0]['error']], true));
				$this->response(['result' => 'ng', 'message' => $result['results'][0]['error']], 400);
			}
		}
		catch (Exception $ex)
		{
			DB::rollback_transaction();
			Log::warning(print_r(['error', $ex->getMessage()], true));
			$this->response(['result' => 'ng', 'message' => $ex->getMessage()], 400);
		}
	}

	/**
	 * GCMにメッセージを送ってもらう
	 *
	 * @param string $api_key
	 * @param string $push_token
	 * @return array
	 */
	private function gcm_send($api_key, $push_token, $message = 'Push')
	{
		Log::debug('gcm::gcm_send');
		$curl		 = curl_init();
		curl_setopt($curl, CURLOPT_URL, 'https://android.googleapis.com/gcm/send');
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HEADER, true);
		curl_setopt($curl, CURLOPT_TIMEOUT, 5);
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode([
			'registration_ids'	 => [$push_token],
			'data'				 => ['message' => $message]
		]));
		curl_setopt($curl, CURLOPT_HTTPHEADER, [
			"Content-Type: application/json",
			"Authorization: key={$api_key}"
		]);
		$response	 = curl_exec($curl);
		$body		 = substr($response, curl_getinfo($curl, CURLINFO_HEADER_SIZE));
		$result		 = json_decode($body, true);
		curl_close($curl);
		return $result;
	}

	/**
	 * twilio番号よりGCMプッシュトークンを更新
	 *
	 * @return type
	 * @throws Exception
	 */
	public function post_token()
	{
		Log::debug('gcm::post_token');
		$validation = Model_Customer::validate('push_token');
		if ($validation->run() === false)
		{
			Log::warning(print_r([$validation->error(), Input::post('number'), Input::post('token')], true));
			$this->response(['result' => 'ng', 'message' => $validation->error()], 400);
			return;
		}

		try
		{
			DB::start_transaction();
			$number	 = Str::to_local_phone_number(Input::post('number'));
			$token	 = Input::post('token');
			if (Model_Customer::update_push_token($number, $token) === null)
			{
				Log::warning(print_r(['利用者またはトークンが指定されていません', Input::post('number'), Input::post('token')], true));
				throw new Exception('利用者またはトークンが指定されていません');
			}
			DB::commit_transaction();

			Log::info(print_r(['success', Input::post('number'), Input::post('token')], true));
			$this->response(['result' => 'ok'], 200);
		}
		catch (Exception $ex)
		{
			DB::rollback_transaction();
			Log::warning(print_r(['error', $ex->getMessage()], true));
			$this->response(['result' => 'ng', 'message' => $ex->getMessage()], 400);
		}
	}

	/**
	 * 端末が起きたことをサーバー側に通知する
	 *
	 * @return type
	 */
	public function post_woke_up_to_server()
	{
		Log::debug('gcm::post_woke_up_to_server');
		$customer = Model_Customer::find_by_twilio_number(Input::post('number'));
		if (empty($customer) or Validation::_empty($customer->id))
		{
			Log::warning(print_r(['利用者情報が存在しません', Input::post('number')], true));
			$this->response(['result' => 'ng', 'message' => '利用者情報が存在しません'], 400);
			return;
		}

		Log::info(print_r(['success', Input::post('number')], true));
		try
		{
			DB::start_transaction();
			$customer->is_connected = Model_Customer::IS_CONNECTED_YES;
			$customer->save();
			DB::commit_transaction();
			$this->response(['result' => 'ok'], 200);
		}
		catch (Exception $ex)
		{
			DB::rollback_transaction();
			Log::warning(print_r(['error', $ex->getMessage()], true));
			$this->response(['result' => 'ng', 'message' => $ex->getMessage()], 400);
		}
	}

	/**
	 * 端末が起動中か
	 */
	public function get_is_wake_up()
	{
		Log::debug('gcm::get_is_wake_up');
		// @todo アクセス制御
//		if (!Auth::check())
//		{
//			return $this->response(['error' => '401 Unauthorized'], 401);
//		}

		$customer = Model_Customer::find(Input::get('customer_id'));
		if (empty($customer) or $customer->is_connected !== Model_Customer::IS_CONNECTED_YES)
		{
			$this->response(['result' => 'ng'], 400);
		}
		$this->response(['result' => 'ok'], 200);
	}
}
