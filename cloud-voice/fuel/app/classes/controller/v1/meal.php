<?php

/**
 * 料理データAPI
 */
class Controller_V1_Meal extends Controller_V1_Abstract
{

	/**
	 * 料理データの検索
	 */
	public function get_search()
	{
		$this->format = 'json';

		$meals = Model_Meal::get_list_by_name(Input::get('keyword'));
		$this->response(['result' => 'ok', 'meals' => $meals], 200);
	}

	/**
	 * 料理データを仮登録
	 */
	public function post_add()
	{
		$this->format = 'json';

		$values = ['name' => Input::post('name')];

		try
		{
			DB::start_transaction();
			$id		 = Model_Meal::create_temporary($values);
			DB::commit_transaction();

			$meal	 = Model_Meal::find($id);
			$this->response(['result' => 'ok', 'meal'	 => [
					'id'	 => (int) $meal->id,
					'name'	 => trim($meal->name)
			]], 200);
		}
		catch (Exception $e)
		{
			DB::rollback_transaction();
			$this->response(['result' => 'ng', 'error' => $e->getMessage()], 200);
		}
	}

}
