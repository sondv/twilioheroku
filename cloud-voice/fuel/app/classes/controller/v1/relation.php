<?php

use \Fuel\Core\DB;

/**
 * 関係者API
 */
class Controller_V1_Relation extends Controller_V1_Abstract
{

	/**
	 * 検索
	 */
	public function get_find_one()
	{
		$relation = Model_Customer::find(Input::get('id'));
		if (empty($relation))
		{
			$this->response(['result' => 'ng'], 200);
			return;
		}

		$this->response(['result' => 'ok', 'relation' => Date::date_string_to_iso8601($relation->to_array())], 200);
	}

	/**
	 * 利用関係者の緊急連絡先を検索
	 */
	public function get_find_emergency_relations()
	{
		$relations = Model_Customer_Relation::get_emergency_relations(Input::get('customer_id'));
		$this->response(['result' => 'ok', 'relations' => $relations], 200);
	}
//	/**
//	 * 保存
//	 */
//	public function post_save()
//	{
//		$json		 = Input::post_json();
//		$validate	 = Model_Customer::validate('save', $json);
//		if ($validate->run($json) === false)
//		{
//			$this->response(['result' => 'ng', 'error' => $validate->get_messages()], 200);
//			return;
//		}
//
//		try
//		{
//			DB::start_transaction();
//			Model_Customer::save_json($json);
//			DB::commit_transaction();
//			$this->response(['result' => 'ok'], 200);
//		}
//		catch (Exception $e)
//		{
//			DB::rollback_transaction();
//			$this->response(['result' => 'ng', 'error' => $e->getMessage()], 200);
//		}
//	}
//
//	/**
//	 * 削除
//	 */
//	public function post_delete()
//	{
//		$customer = Model_Customer::find((int) Input::post_json('id'));
//		if ($customer === null)
//		{
//			$this->response(['result' => 'ng', 'error' => '該当する情報が見つかりませんでした。'], 200);
//			return;
//		}
//
//		try
//		{
//			DB::start_transaction();
//			$customer->delete();
//			DB::commit_transaction();
//			$this->response(['result' => 'ok'], 200);
//		}
//		catch (Exception $e)
//		{
//			DB::rollback_transaction();
//			$this->response(['result' => 'ng', 'error' => $e->getMessage()], 200);
//		}
//	}
}
