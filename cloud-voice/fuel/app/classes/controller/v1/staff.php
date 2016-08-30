<?php

use Fuel\Core\DB;

/**
 * スタッフAPI
 */
class Controller_V1_Staff extends Controller_V1_Abstract
{

	/**
	 * 検索
	 */
	public function get_find()
	{
		$query		 = Model_Staff::get_search_query();
		$pagination	 = Model_Staff::set_pagination($query);
		$staffs		 = [];
		foreach ($query->get() as $row)
		{
			$staffs[] = Model_Staff::clean($row->to_array());
		}

		$this->response([
			'staffs'		 => $staffs,
			'total_pages'	 => (int) $pagination->total_pages,
			'current_page'	 => (int) $pagination->calculated_page,
		], 200);
	}

	/**
	 * 検索（1件のみ）
	 */
	public function get_find_one()
	{
		$staff = Model_Staff::find(Input::get('id'));
		if ($staff === null)
		{
			$this->response(['result' => 'ng', 'error' => '該当する情報が見つかりませんでした。'], 200);
			return;
		}

		$this->response(['result' => 'ok', 'staff' => Model_Staff::clean($staff->to_array())], 200);
	}

	/**
	 * 保存
	 */
	public function post_save()
	{
		$json		 = Input::post_json();
		$validate	 = Model_Staff::validate('save', $json);
		if ($validate->run(Arr::get($json, 'staff', [])) === false)
		{
			$this->response(['result' => 'ng', 'error' => $validate->get_messages()], 200);
			return;
		}

		if (Model_Staff::exists_login_id(Arr::get($json, 'staff.login_id'), Arr::get($json, 'staff.id')))
		{
			$this->response(['result' => 'ng', 'error' => ['login_id' => '既に使用されているログインIDです']], 200);
			return;
		}

		try
		{
			DB::start_transaction();
			Model_Staff::save_json($json);
			DB::commit_transaction();
			$this->response(['result' => 'ok'], 200);
		}
		catch (Database_Exception $e)
		{
			DB::rollback_transaction();
			$this->response(['result' => 'ng', 'error' => $e->getMessage()], 200);
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
		$staff = Model_Staff::find((int) Input::post_json('id'));
		if ($staff === null)
		{
			$this->response(['result' => 'ng', 'error' => '該当する情報が見つかりませんでした。'], 200);
			return;
		}

		try
		{
			DB::start_transaction();
			$staff->delete();
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
