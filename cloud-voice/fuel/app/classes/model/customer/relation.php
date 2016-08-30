<?php

/**
 * 関係者テーブル
 *
 * @package  app
 * @extends  ORM Model
 */
class Model_Customer_Relation extends Model_Abstract
{
	protected static $_table_name	 = 'customer_relations';
	protected static $_properties	 = [
		'id',
		'created_at',
		'updated_at',
		'deleted_at',
		'created_user_id',
		'updated_user_id',
		'family_name',
		'given_name',
		'family_name_kana',
		'given_name_kana',
		'relation',
		'post_code',
		'address',
		'birthday',
		'phone_number_1',
		'phone_number_2',
		'email',
		'is_emergency',
		'emergency_order',
	];

	/**
	 * 緊急発進対象フラグ：非対象
	 */
	const IS_EMERGENCY_FALSE = 0;

	/**
	 * 緊急発進対象フラグ：対象
	 */
	const IS_EMERGENCY_TRUE = 1;

	/**
	 * バリデーション作成
	 *
	 * @return Validation
	 */
	public static function validation()
	{
		$val = Validation::forge();
		$val->add('family_name', 'お名前（姓）')->add_rule('required');
		$val->add('given_name', 'お名前（名）')->add_rule('required');
		$val->add('family_name_kana', 'カナ（姓）');
		$val->add('given_name_kana', 'カナ（名）');
		$val->add('relation', '続柄')->add_rule('required');
		$val->add('post_code', '郵便番号');
		$val->add('address', '住所')->add_rule('max_length', 128);
		$val->add('birthday', '生年月日')->add_rule('max_length', 10);
		$val->add('phone_number_1', '電話番号1')->add_rule('max_length', 50)->add_rule('required');
		$val->add('phone_number_2', '電話番号2')->add_rule('max_length', 50);
		$val->add('email', 'メールアドレス')->add_rule('max_length', 256);
		$val->add('is_emergency', '緊急発信対象フラグ')->add_rule('required');
		$val->add('emergency_order', '緊急発信優先順位')->add_rule('valid_string', ['numeric', 'utf8'])->add_rule('required');
		return $val;
	}

	/**
	 * 緊急発進対象の関係者を取得
	 *
	 * @param  int $customer_id
	 * @return array
	 */
	public static function get_emergency_relations($customer_id)
	{
		return self::query()
				->where('customer_id', $customer_id)
				->where('is_emergency', self::IS_EMERGENCY_TRUE)
				->order_by('emergency_order', 'ASC')
				->get();
	}

	/**
	 * 保存
	 *
	 * @param type $cascade
	 * @param type $use_transaction
	 * @return type
	 */
	public function save($cascade = null, $use_transaction = false)
	{
		$this->phone_number = Ex\Core\Str::reset_international_calling_code($this->phone_number, 'jp');
		return parent::save($cascade, $use_transaction);
	}
}
