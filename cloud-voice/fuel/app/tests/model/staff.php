<?php

//use Fuel\Core\Str;

/**
 * モデルスタッフ
 *
 * @group Controller
 * @group Acl
 */
class Test_Model_Staff extends Fuel\Core\TestCase
{

	public function setup()
	{
		try
		{
			DB::start_transaction();
			$this->truncate();
			DB::commit_transaction();
		}
		catch (Exception $e)
		{
			DB::rollback_transaction();
		}
	}

	/**
	 * ----------------------------------------------------------
	 * save_json
	 * ----------------------------------------------------------
	 */
	public function test_JSONから保存_新規作成()
	{
		$values['staff'] = [
			'login_id'			 => 'hogehoge',
			'password'			 => 'hogehoge',
			'family_name'		 => '田中',
			'given_name'		 => '太郎',
			'family_name_kana'	 => 'タナカ',
			'given_name_kana'	 => 'タロウ',
			'email'				 => 'test@example.com',
			'phone_number'		 => '09012345678'
		];
		$id				 = Model_Staff::save_json($values);
		$this->assertEquals($id, 1);
	}

	public function test_JSONから保存_パラメータが空()
	{
		$values	 = [];
		$null	 = Model_Staff::save_json($values);
		$this->assertNull($null);
	}

	/**
	 * ----------------------------------------------------------
	 * get_search_query
	 * ----------------------------------------------------------
	 */
	public function test_検索()
	{
		$query	 = Model_Staff::get_search_query();
		$staffs	 = $query->get();
		$this->assertEquals($staffs, 1);
	}

	/**
	 * テーブルを空に
	 */
	private function truncate()
	{
		\Fuel\Core\DB::query('TRUNCATE TABLE staffs')->execute();
	}

}
