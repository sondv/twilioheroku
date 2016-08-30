<?php

/**
 * モデル基底クラス
 *
 * @extends  ORM Model
 */
class Model_Abstract extends \Orm\Model_Soft
{
	/**
	 * イベント登録
	 *
	 * @var array
	 */
	protected static $_observers = [
		'Orm\Observer_CreatedAt' => [
			'events'			 => ['before_insert', 'before_save'],
			'mysql_timestamp'	 => true,
		],
		'Orm\Observer_UpdatedAt' => [
			'events'			 => ['before_insert', 'before_update', 'before_save'],
			'mysql_timestamp'	 => true,
		],
		'Orm\Observer_Self'		 => [
//			'events' => ['before_insert', 'before_save']
		],
	];

	/**
	 * 論理削除
	 *
	 * @var type
	 */
	protected static $_soft_delete = [
		'deleted_field'		 => 'deleted_at',
		'mysql_timestamp'	 => true
	];

	/**
	 * 作成スタッフID
	 */
	public function _event_before_insert()
	{
		$staff_id = \Session::get(Model_Staff::SESSION_KEY_LOGIN_ID);
		if ($staff_id)
		{
			$this->created_user_id = $staff_id;
		}
		else
		{
			$this->created_user_id = 0;
		}
	}

	/**
	 * 更新スタッフID
	 */
	public function _event_before_save()
	{
		$staff_id = \Session::get(Model_Staff::SESSION_KEY_LOGIN_ID);
		if ($staff_id)
		{
			$this->updated_user_id = $staff_id;
		}
		else
		{
			$this->updated_user_id = 0;
		}
	}

	/**
	 * ORMのQueryオブジェクトから、ページネーションを作成する
	 *
	 * @param   \Orm\Query $query
	 * @param   string $name
	 * @return  \Pagination
	 */
	public static function set_pagination(&$query, $name = 'pagination')
	{
		$pagination = \Pagination::forge($name, ['total_items' => $query->count()]);
		$query->limit($pagination->per_page);
		$query->offset($pagination->offset);
		return $pagination;
	}

	/**
	 * テーブル内のデータを全て削除
	 */
	public static function delete_all()
	{
		\DB::query('DELETE FROM `'.self::table().'`')->execute();
		\DB::query('ALTER TABLE `'.self::table().'` AUTO_INCREMENT=1')->execute();
	}
}
