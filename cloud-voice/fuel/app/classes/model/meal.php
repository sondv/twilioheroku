<?php

/**
 * 料理データ
 */
class Model_Meal extends Model_Abstract
{
	protected static $_table_name	 = 'meals';
	protected static $_properties	 = [
		'id',
		'created_at',
		'updated_at',
		'deleted_at',
		'name',
		'kana',
		'category_menu',
		'category_cooking',
		'category_cuisine',
		'category_chief_material',
		'adequate_intake',
		'amount',
		'energy',
		'moisture',
		'protein',
		'lipid',
		'carbohydrate',
		'calcium',
		'iron',
		'vitamin_A',
		'vitamin_D',
		'vitamin_B1',
		'vitamin_B2',
		'vitamin_C',
		'saturated_fatty_acid',
		'monovalent_saturated_fatty_acid',
		'polyvalent_saturated_fatty_acid',
		'dietary_fiber',
		'salt',
		'menu_staple_food',
		'menu_side_dish',
		'menu_main_dishes',
		'menu_milk',
		'menu_fruit',
		'menu_confectionery',
		'cereal',
		'also_attached_much',
		'sugar_sweeteners_such',
		'nuts_and_seeds',
		'green_and_yellow_vegetables',
		'other_vegetables',
		'fruits',
		'mushrooms',
		'seaweed',
		'beans',
		'seafood',
		'meat',
		'eggs',
		'milks',
		'oils_and_fats',
		'confectionery',
		'taste_beverages',
		'seasonings_and_spices',
		'other',
		'is_temporary',
	];

	/**
	 * 仮登録フラグ：非仮登録
	 */
	const IS_TEMPORARY_FALSE = 0;

	/**
	 * 仮登録フラグ：仮登録
	 */
	const IS_TEMPORARY_TRUE = 1;

	/**
	 * 名前から料理データのリストを取得
	 *
	 * @return array
	 */
	public static function get_list_by_name($keyword)
	{
		$list = [];

		$exploded	 = Str::explode_with_cleaning($keyword);
		$query		 = self::query();
		foreach ($exploded as $keyword)
		{
			$query->where_open();
			$query->where('name', 'LIKE', "%{$keyword}%");
			$query->or_where('kana', 'LIKE', "%{$keyword}%");
//			$query->or_where('category_menu', 'LIKE', "%{$keyword}%");
//			$query->or_where('category_cooking', 'LIKE', "%{$keyword}%");
//			$query->or_where('category_cuisine', 'LIKE', "%{$keyword}%");
//			$query->or_where('category_chief_material', 'LIKE', "%{$keyword}%");
			$query->where_close();
		}
		$query->order_by('name', 'ASC');

		$meals = $query->get();
		foreach ($meals as $meal)
		{
			$list[] = [
				'id'	 => (int) $meal->id,
				'name'	 => trim($meal->name)
			];
		}

		return $list;
	}

//	/**
//	 * 指定キーのリストを取得
//	 *
//	 * @return array
//	 */
//	public static function get_groupby($key)
//	{
//		if (empty($key))
//		{
//			return [];
//		}
//
//		$list	 = [];
//		$meals	 = self::query()
//			->group_by($key)
//			->order_by($key, 'ASC')
//			->get();
//		foreach ($meals as $meal)
//		{
//			$value = trim($meal->$key);
//			if (empty($value))
//			{
//				continue;
//			}
//			$list[] = $value;
//		}
//		return $list;
//	}

	/**
	 * 料理データの仮登録
	 *
	 * @param  array $values
	 * @return int ID
	 */
	public static function create_temporary($values)
	{
		$values['is_temporary'] = self::IS_TEMPORARY_TRUE;

		$library = self::forge();
		$library->set($values);
		if ($library->save())
		{
			return $library->id;
		}
		else
		{
			return false;
		}
	}
}
