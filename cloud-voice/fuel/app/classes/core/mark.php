<?php

/**
 * 聞き取り採点クラス
 */
class Mark
{
	/**
	 * @array 得点表
	 */
	protected static $point_table = [
		'meal' => [
			// 食事量：かなり多い
			5 => [
				// 内容
				5 => 8,
				4 => 6,
				3 => 4,
				2 => 2,
				1 => 1,
			],
			// 食事量：少し多い
			4 => [
				// 内容
				5 => 9,
				4 => 7,
				3 => 5,
				2 => 3,
				1 => 1,
			],
			// 食事量：普段通り
			3 => [
				// 内容
				5 => 10,
				4 => 8,
				3 => 6,
				2 => 4,
				1 => 2,
			],
			// 食事量：少し少ない
			2 => [
				// 内容
				5 => 9,
				4 => 7,
				3 => 5,
				2 => 3,
				1 => 1,
			],
			// 食事量：かなり少ない
			1 => [
				// 内容
				5 => 8,
				4 => 6,
				3 => 4,
				2 => 2,
				1 => 1,
			],
		],
		'activity' => [
			// 活動量：かなり多い
			5 => [
				// 内容
				5 => 8,
				4 => 6,
				3 => 4,
				2 => 2,
				1 => 1,
			],
			// 活動量：少し多い
			4 => [
				// 内容
				5 => 9,
				4 => 7,
				3 => 5,
				2 => 3,
				1 => 1,
			],
			// 活動量：普段通り
			3 => [
				// 内容
				5 => 10,
				4 => 8,
				3 => 6,
				2 => 4,
				1 => 2,
			],
			// 活動量：少し少ない
			2 => [
				// 内容
				5 => 9,
				4 => 7,
				3 => 5,
				2 => 3,
				1 => 1,
			],
			// 活動量：かなり少ない
			1 => [
				// 内容
				5 => 8,
				4 => 6,
				3 => 4,
				2 => 2,
				1 => 1,
			],
		],
		'feeling' => [
			// コミュニケーション量：かなり多い
			5 => [
				// 内容
				5 => 8,
				4 => 6,
				3 => 4,
				2 => 2,
				1 => 1,
			],
			// コミュニケーション量：少し多い
			4 => [
				// 内容
				5 => 9,
				4 => 7,
				3 => 5,
				2 => 3,
				1 => 1,
			],
			// コミュニケーション量：普段通り
			3 => [
				// 内容
				5 => 10,
				4 => 8,
				3 => 6,
				2 => 4,
				1 => 2,
			],
			// コミュニケーション量：少し少ない
			2 => [
				// 内容
				5 => 9,
				4 => 7,
				3 => 5,
				2 => 3,
				1 => 1,
			],
			// コミュニケーション量：かなり少ない
			1 => [
				// 内容
				5 => 8,
				4 => 6,
				3 => 4,
				2 => 2,
				1 => 1,
			],
		],
	];

	/**
	 * 期間を開始日、終了日に変換する
	 *
	 * @param string $date
	 * @param string $period
	 * @return [開始日、終了日]
	 */
	public static function period_to_date($date, $period)
	{
		$base = strtotime($date);
		switch ($period)
		{
			case '3months':
				$begin	 = date('Y-m-d', strtotime('first day of -2 months', $base));
				$end	 = date('Y-m-d', strtotime('last day of', $base));
				break;
			case '12months':
				$begin	 = date('Y-m-d', strtotime('first day of -11 months', $base));
				$end	 = date('Y-m-d', strtotime('last day of', $base));
				break;
			default:
				$begin	 = date('Y-m-d', strtotime('first day of', $base));
				$end	 = date('Y-m-d', strtotime('last day of', $base));
		}
		return [$begin, $end];
	}

	/**
	 * 指定日の聞き取り情報を返す
	 *
	 * @param \Model\Customer $hearings
	 * @param string $hearing_at YYYY-MM-DD
	 * @return array
	 */
	public static function find_by_hearing_at($hearings, $hearing_at)
	{
		foreach ($hearings as $hearing)
		{
			if ($hearing->hearing_at !== $hearing_at)
			{
				continue;
			}
			return json_decode($hearing->hearing_content, true);
		}

		return [];
	}

	/**
	 * 聞き取り内容から点数を算出する
	 *
	 * @param array $content
	 * @param string $target
	 * @return int | array
	 */
	public static function calculation($content, $target)
	{
		if (is_array($content) === false or count($content) === 0)
		{
			return null;
		}

		switch ($target)
		{
			// 食事について
			case 'meal':
				return self::calcMeal($content);
			// 活動について
			case 'activity':
				return self::calcActivity($content);
			// コミュニケーションについて
			case 'feeling':
				return self::calcFeeling($content);
			// 温度板
			case 'fever':
				return self::calcFever($content);
			// BMI
			case 'BMI':
				return self::calcBMI($content);
			default :
				return 0;
		}
	}

	/**
	 * 食事について
	 *  計算ロジック：https://docs.google.com/spreadsheets/d/1DNDyZTR57dAJOpNbf1WKGUbDsoYWAZd6E-Qewp2fqAs/edit#gid=1692964950
	 *
	 * @param array $content
	 * @return int
	 */
	public static function calcMeal($content)
	{
		$question_2	 = (int) Arr::get($content, 'question_2', 0);
		$question_3	 = (int) Arr::get($content, 'question_3', 0);
		if (count($question_2) === 0 or $question_3 === 0)
		{
			return null;
		}

		return Arr::get(self::$point_table, "meal.{$question_2}.{$question_3}", 1);
	}

	/**
	 * 活動について
	 *  計算ロジック：https://docs.google.com/spreadsheets/d/1DNDyZTR57dAJOpNbf1WKGUbDsoYWAZd6E-Qewp2fqAs/edit#gid=1692964950
	 *
	 * @param array $content
	 * @return int
	 */
	public static function calcActivity($content)
	{
		$question_4	 = (int) Arr::get($content, 'question_4', 0);
		$question_5	 = (int) Arr::get($content, 'question_5', 0);
		if (count($question_4) === 0 or $question_5 === 0)
		{
			return null;
		}

		return Arr::get(self::$point_table, "meal.{$question_4}.{$question_5}", 1);
	}

	/**
	 * コミュニケーションについて
	 *  計算ロジック：https://docs.google.com/spreadsheets/d/1DNDyZTR57dAJOpNbf1WKGUbDsoYWAZd6E-Qewp2fqAs/edit#gid=1692964950
	 *
	 * @param array $content
	 * @return int
	 */
	public static function calcFeeling($content)
	{
		$question_6	 = (int) Arr::get($content, 'question_6', 0);
		$question_7	 = (int) Arr::get($content, 'question_7', 0);
		if (count($question_6) === 0 or $question_7 === 0)
		{
			return null;
		}

		return Arr::get(self::$point_table, "meal.{$question_6}.{$question_7}", 1);
	}

	/**
	 * 温度板
	 *  [最高血圧、最低血圧、体温]
	 *
	 * @param array $content
	 * @return array
	 */
	public static function calcFever($content)
	{
		$systolic	 = Arr::get($content, 'body.blood.pressure.systolic');
		$diastolic	 = Arr::get($content, 'body.blood.pressure.diastolic');
		$temperature = Arr::get($content, 'body.temperature');

		return [$systolic, $diastolic, $temperature];
	}

	/**
	 * 気分の状態
	 *  計算式：体重(kg)/身長(m)^2
	 *
	 * @param array $content
	 * @return int
	 */
	public static function calcBMI($content)
	{
		$height	 = Arr::get($content, 'body.height', 0);
		$weight	 = Arr::get($content, 'body.weight', 0);
		if (empty($height) or empty($weight))
		{
			return null;
		}
		return (float) number_format($weight / pow($height / 100, 2), 1);
	}
}
