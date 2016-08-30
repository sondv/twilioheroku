<?php

use \Fuel\Core\DB;

/**
 * 聞き取りAPI
 */
class Controller_V1_Hearing extends Controller_V1_Abstract
{

	/**
	 * 1件のみ検索
	 */
	public function get_find_one()
	{
		$hearing = Model_Hearing::find_one(Input::get('customer_id'), Input::get('hearing_at'));
		if (empty($hearing))
		{
			$this->response(['result' => 'ng'], 200);
			return;
		}

		if ($hearing->hearing_content)
		{
			$hearing->hearing_content = json_decode($hearing->hearing_content);
		}

		$this->response(['result' => 'ok', 'hearing' => Date::date_string_to_iso8601($hearing->to_array())], 200);
	}

	/**
	 * 保存
	 */
	public function post_save()
	{
		$json		 = Input::post_json();
		$customer	 = Model_Customer::find(Arr::get($json, 'hearing.customer_id'));
		if ($customer === null)
		{
			$this->response(['result' => 'ng'], 200);
			return;
		}

		$validate = Model_Hearing::validate('save', $json);
		if ($validate->run($json) === false)
		{
			$this->response(['result' => 'ng', 'error' => $validate->get_messages()], 200);
			return;
		}

		try
		{
			DB::start_transaction();
			Model_Hearing::save_json($json);

			// 聞き取りが完了したことをメールで送信
			$sent_mail = false;
			if ($customer->share and $customer->share->hash)
			{
				$all_to = [
					$customer->share_email_1,
					$customer->share_email_2,
					$customer->share_email_3,
					$customer->share_email_4,
					$customer->share_email_5,
				];
				$sent_mail = Mail::send_hearing_mails($all_to, $customer, Arr::get($json, 'hearing.hearing_at'));
			}

			DB::commit_transaction();
			$this->response(['result' => 'ok', 'sent_mail' => $sent_mail], 200);
		}
		catch (Exception $e)
		{
			DB::rollback_transaction();
			$this->response(['result' => 'ng', 'error' => $e->getMessage()], 200);
		}
	}

	/**
	 * 状態グラフの情報
	 */
	public function get_condition()
	{
		list($begin, $end) = \Mark::period_to_date(Input::get('date'), Input::get('period'));
		$hearings = Model_Hearing::find_by_period(Input::get('customer_id'), $begin, $end);

		if (Input::get('target') === 'fever')
		{
			$data = [
				'title'		 => '温度板',
				'labels'	 => [],
				'datasets'	 => [
					['label' => '最低血圧', 'data' => []],
					['label' => '最高血圧', 'data' => []],
					['label' => '体温', 'data' => []],
				]
			];

			while ($begin <= $end)
			{
				$content						 = \Mark::find_by_hearing_at($hearings, $begin);
				$data['labels'][]				 = Date::format_datetime($begin, 'Y年n月j日(w)');
				// 最高血圧、最低血圧、体温
				list($systolic, $diastolic, $temperature) = \Mark::calculation($content, Input::get('target'));
				$data['datasets'][0]['data'][]	 = $diastolic;
				$data['datasets'][1]['data'][]	 = $systolic;
				$data['datasets'][2]['data'][]	 = $temperature;

				$begin = date('Y-m-d', strtotime("{$begin} 00:00:00") + Date::DAY);
			}
		}
		else
		{
			$data = [
				'title'		 => Config::get('const.hearing.target.'.Input::get('target')),
				'labels'	 => [],
				'datasets'	 => [
					['label' => Config::get('const.hearing.target.'.Input::get('target')), 'data' => []],
					['label' => 'BMI', 'data' => []]
				]
			];

			while ($begin <= $end)
			{
				$content						 = \Mark::find_by_hearing_at($hearings, $begin);
				$data['labels'][]				 = Date::format_datetime($begin, 'Y年n月j日(w)');
				$data['datasets'][0]['data'][]	 = \Mark::calculation($content, Input::get('target'));
				$data['datasets'][1]['data'][]	 = \Mark::calculation($content, 'BMI');

				$begin = date('Y-m-d', strtotime("{$begin} 00:00:00") + Date::DAY);
			}

			// 点数の平均値を求める
			$sum = [10 => 0, 9 => 0, 8 => 0, 7 => 0, 6 => 0, 5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
			foreach ($data['datasets'][0]['data'] as $var)
			{
				if ($var === null)
				{
					continue;
				}
				$sum[$var] ++;
			}
			$average		 = array_sum($sum) > 0 ? (10 * $sum[10] + 9 * $sum[9] + 8 * $sum[8] + 7 * $sum[7] + 6 * $sum[6] + 5 * $sum[5] + 4 * $sum[4] + 3 * $sum[3] + 2 * $sum[2] + 1 * $sum[1]) / array_sum($sum) : 0;
			$data['sum']	 = $sum;
			$data['average'] = (float) number_format($average, 1);
		}

		$this->response(['result' => 'ok', 'data' => $data], 200);
	}

	/**
	 * 最近の状態
	 */
	public function get_recently_condition()
	{
		$recently = Model_Hearing::recently_with_average(Input::get('customer_id'));
		$this->response(['result' => 'ok', 'recently' => $recently], 200);
	}

	/**
	 * 最新の状態
	 */
	public function get_latest_condition()
	{
		$latest = Model_Hearing::recently_with_average(Input::get('customer_id'), 1);
		$this->response(['result' => 'ok', 'latest' => $latest], 200);
	}

	/**
	 * 一月の聞き取り情報
	 */
	public function get_one_month()
	{
		$from		 = date('Y-m-01', strtotime(Input::get('date')));
		$to			 = date('Y-m-d', strtotime('+1 months -1 day '.$from));
		$hearings	 = Model_Hearing::find_by_period(Input::get('customer_id'), $from, $to);
		$this->response(['result' => 'ok', 'hearings' => $hearings], 200);
	}

	/**
	 * 定数
	 */
	public function get_const()
	{
		$this->response(['result' => 'ok', 'const' => Config::get('const.hearing')], 200);
	}

}
