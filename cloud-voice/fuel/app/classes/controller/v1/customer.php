<?php

use \Fuel\Core\DB;
use \Fuel\Core\Upload;

/**
 * 利用者API
 */
class Controller_V1_Customer extends Controller_V1_Abstract
{

	/**
	 * 検索
	 */
	public function get_find()
	{
		$query		 = Model_Customer::get_search_query(['keyword' => Input::get('keyword')]);
		$pagination	 = Model_Customer::set_pagination($query);
		$customers	 = [];
		foreach ($query->get() as $row)
		{
			$customer								 = $row->to_array();
			$customer['recently']					 = Model_Hearing::recently_with_average($row->id);
			$customer['latest_hearing_at']			 = Model_Hearing::latest_hearing_at($row->id);
			$customer['is_already_hearing_today']	 = Model_Hearing::is_already_hearing($row->id, date('Y-m-d'));
			Arr::delete($customer, ['deleted_at', 'gcm_push_token']);
			$customers[]							 = $customer;
		}

		$this->response([
			'result'		 => 'ok',
			'customers'		 => $customers,
			'total_pages'	 => (int) $pagination->total_pages,
			'current_page'	 => (int) $pagination->calculated_page,
		], 200);
	}

	/**
	 * 検索
	 */
	public function get_find_one()
	{
		$customer = Model_Customer::find(Input::get('id'));
		if ($customer === null)
		{
			$this->response(['result' => 'ng'], 200);
			return;
		}

		$customer_result						 = $customer->to_array();
		$customer_result['latest_hearing_at']	 = Model_Hearing::latest_hearing_at($customer->id);
		$customer_result['share_url']			 = $customer->share ? Uri::create_share_url($customer->share->hash, 'hearing') : null;
		Arr::delete($customer_result, ['deleted_at', 'gcm_push_token']);
		$this->response(['result' => 'ok', 'customer' => Date::date_string_to_iso8601($customer_result)], 200);
	}

	/**
	 * twilio番号より利用者データを取得
	 */
	public function get_find_one_by_twilio_number()
	{
		$customer = Model_Customer::find_by_twilio_number(Input::get('twilio_number'));
		if (empty($customer))
		{
			$this->response(['result' => 'ok', 'is_registered' => false], 200);
			return;
		}

		$this->response(['result' => 'ok', 'is_registered' => true, 'customer' => Date::date_string_to_iso8601($customer->to_array())], 200);
	}

	/**
	 * 利用者の電話番号情報を取得
	 */
	public function get_phones()
	{
		$customer = Model_Customer::find(Input::get('id'));
		if ($customer === null)
		{
			$this->response(['result' => 'ng'], 200);
			return;
		}

		$phones = [];
		if ($customer->twilio_number)
		{
			$phones[] = [
				'type'	 => 'Twilio',
				'number' => $customer->twilio_number,
			];
		}
		if ($customer->phone_number_1)
		{
			$phones[] = [
				'type'	 => '電話.1',
				'number' => $customer->phone_number_1,
			];
		}
		if ($customer->phone_number_2)
		{
			$phones[] = [
				'type'	 => '電話.2',
				'number' => $customer->phone_number_2,
			];
		}

		$count_relations = 1;
		$relations = Model_Customer_Relation::get_emergency_relations(Input::get('customer_id'));
		foreach ($relations as $row)
		{
			if ($row->phone_number_1)
			{
				$phones[] = [
					'type'	 => '緊急連絡先.'.$count_relations,
					'number' => $row->phone_number_1,
				];
				$count_relations++;
			}
			if ($row->phone_number_2)
			{
				$phones[] = [
					'type'	 => '緊急連絡先.'.$count_relations,
					'number' => $row->phone_number_2,
				];
				$count_relations++;
			}
		}

		$this->response(['result' => 'ok', 'phones' => $phones], 200);
	}

	/**
	 * 保存
	 */
	public function post_save()
	{
		$json		 = Input::post_json();
		$validate	 = Model_Customer::validate('save');
		if ($validate->run(Arr::get($json, 'customer', [])) === false)
		{
			$this->response(['result' => 'ng', 'error' => $validate->get_messages()], 200);
			return;
		}

		try
		{
			DB::start_transaction();
			Model_Customer::save_json($json);
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
	 * 削除
	 */
	public function post_delete()
	{
		$customer = Model_Customer::find((int) Input::post_json('id'));
		if ($customer === null)
		{
			$this->response(['result' => 'ng', 'error' => '該当する情報が見つかりませんでした。'], 200);
			return;
		}

		try
		{
			DB::start_transaction();
			$customer->delete();
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
	 * アップロード
	 */
	public function post_upload_photo()
	{
		Upload::process([
			'field'			 => 'file',
			'ext_whitelist'	 => ['jpeg', 'jpg', 'png', 'gif'],
			'path'			 => Config::get('const.customer.photo.path.absolute'),
			'auto_rename'	 => false,
			'new_name'		 => Str::random('sha1', 40),
		]);
		if (Upload::is_valid())
		{
			Upload::save();
			$upload = Upload::get_files(0);
			if (empty($upload))
			{
				$this->response(['result' => 'ng', 'error' => 'upload failed'], 200);
				return;
			}

			$this->response(['result' => 'ok', 'photo' => Arr::get($upload, 'saved_as')], 200);
		}
		else
		{
			foreach (Upload::get_errors() as $file)
			{
				$this->response(['result' => 'ng', 'error' => $file['error']], 200);
				return;
			}
		}
	}

	/**
	 * 定数
	 */
	public function get_const()
	{
		$this->response(['result' => 'ok', 'const' => Config::get('const.customer')], 200);
	}

}
