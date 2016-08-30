<?php

use Fuel\Core\Log;
use Fuel\Core\Config;
use Fuel\Core\Validation;

/**
 * TwilioAPI
 */
class Controller_V1_Twilio extends Controller_V1_Abstract
{

	/**
	 * トークン取得
	 */
	public function get_token()
	{
		$staff			 = Model_Staff::logged_staff();
		$phone_number	 = $staff ? $staff->phone_number : Input::get('my_phone_number');
		if (Validation::_empty($phone_number))
		{
			Log::warning(print_r(['400 Bad Request VVVVVGGGGG', $phone_number], true));
			$this->response(['result' => 'ng', 'error' => '400 Bad Request AAAA'], 400);
			return;
		}

		$capability	 = new Services_Twilio_Capability(Config::get('twilio.account_sid'), Config::get('twilio.auth_token'));
		$capability->allowClientOutgoing(Config::get('twilio.app_sid'));
		$capability->allowClientIncoming($this->number_to_client_name($phone_number));
		$token		 = $capability->generateToken();

		Log::info(print_r([$phone_number, $token], true));
		$this->response(['result' => 'ok', 'token' => $token], 200);
	}

	/**
	 * 掛先電話番号を保持
	 *  通話ステータスと、通話先情報をセッションに
	 */
	public function post_connecting_phone_number()
	{
		$staff			 = Model_Staff::logged_staff();
		$phone_number	 = Input::post_json('phone_number');
		$customer_id	 = Input::post_json('customer_id');
		if ( ! $staff or ! $phone_number or ! $customer_id)
		{
			$this->response(['result' => 'ng', 'error' => '400 Bad Request'], 400);
			return;
		}

		$his				 = Model_Twilio_History::forge();
		$his->staff_id		 = $staff->id;
		$his->customer_id	 = $customer_id;
		$his->to			 = Str::to_local_phone_number($phone_number);
		$his->from			 = Str::to_local_phone_number($staff->phone_number);
		$his->save();

		$this->response(['result' => 'ok'], 200);
	}

	/**
	 * 受電電話番号を保持
	 *  通話ステータスと、通話先情報をセッションに
	 */
	public function post_incoming_phone_number()
	{
		$staff			 = Model_Staff::logged_staff();
		$phone_number	 = Input::post_json('phone_number');
		if ( ! $staff or ! $phone_number)
		{
			$this->response(['result' => 'ng', 'error' => '400 Bad Request'], 400);
			return;
		}

		$customer_id = null;
		$customer	 = Model_Customer::find_by_phone_number(Str::to_local_phone_number($phone_number));
		if ($customer)
		{
			$customer_id = $customer->id;
		}

		$his				 = Model_Twilio_History::forge();
		$his->staff_id		 = $staff->id;
		$his->customer_id	 = $customer_id;
		$his->to			 = Str::to_local_phone_number($staff->phone_number);
		$his->from			 = Str::to_local_phone_number($phone_number);
		$his->save();

		$this->response(['result' => 'ok'], 200);
	}

	/**
	 * 一月の通話履歴情報
	 */
	public function get_one_month()
	{
		$from		 = date('Y-m-01 00:00:00', strtotime(Input::get('date')));
		$to			 = date('Y-m-d 23:59:59', strtotime('+1 months -1 day '.$from));
		$hearings	 = Model_Twilio_History::find_by_period(Input::get('customer_id'), $from, $to);
		$this->response(['result' => 'ok', 'histories' => $hearings], 200);
	}

	/**
	 * SIDから通話履歴取得
	 */
	public function get_find_parent_call_sid()
	{
		$hearing = Model_Twilio_History::get_by_parent_call_sid(Input::get('sid'));
		if ($hearing === null)
		{
			$this->response(['result' => 'ng'], 200);
			return;
		}

		$this->response(['result' => 'ok', 'hearing' => $hearing], 200);
	}

	/**
	 * 電話番号をクライアント名へ変換
	 *
	 * @param $phone_number
	 * @return string
	 */
	private static function number_to_client_name($phone_number)
	{
		if (substr($phone_number, 0, 1) == '0')
		{
			$phone_number = '+81'.substr($phone_number, 1);
		}
		return 'cloudvoice'.preg_replace('/(\+|\-| )/', '', $phone_number);
	}

	/**
	 * 一月の通話履歴情報
	 */
	public function get_aaaa()
	{
		var_dump(Date::is_business_hours());die;
		
		$client = new Services_Twilio(Config::get('twilio.account_sid'), Config::get('twilio.auth_token'));

//        "Status" => "completed",
//        "StartTime>" => "2013-05-01",
//        "StartTime<" => "2013-05-10"
//		
		foreach ($client->account->calls->getIterator(0, 50, ['ParentCallSid' => 'CA9f822e9d582bb7972c54776ff33289a3']) as $call)//24
//		foreach ($client->account->calls->getIterator(0, 50, ['StartTime' => '2016-06-02']) as $call)//24
//		foreach ($client->account->calls->getIterator(0, 50, ['From' => '+815031874521']) as $call)
		{
//			if (Model_Twilio_History::get_by_sid($call->sid))
//			{
//				continue;
//			}
//
//			$history = Model_Twilio_History::forge([
//				'sid'				 => $call->sid,
//				'parent_call_sid'	 => $call->parent_call_sid,
//				'account_sid'		 => $call->account_sid,
//				'to'				 => $call->to,
//				'from'				 => $call->from,
//				'phone_number_sid'	 => $call->phone_number_sid,
//				'status'			 => $call->status,
//				'start_time'		 => $call->start_time,
//				'end_time'			 => $call->end_time,
//				'duration'			 => $call->duration,
//				'price'				 => $call->price,
//				'forwarded_from'	 => $call->forwarded_from,
//				'uri'				 => $call->uri,
//				'notifications'		 => $call->subresource_uris->notifications,
//				'recordings'		 => $call->subresource_uris->recordings,
//			]);
//			$history->save();
			echo "{$call->status} : {$call->from} -> {$call->to} {$call->start_time}-{$call->end_time} {$call->duration}秒<br>\n";
		}
	}

	/**
	 * 通話リクエスト
	 *  doc：https://jp.twilio.com/docs/api/twiml/dial
	 */
	public function get_request()
	{
		// 営業時間外
		if ( ! Date::is_business_hours())
		{
			$from		 = Config::get('const.system.business_hours.from');
			$to			 = Config::get('const.system.business_hours.to');
			$body		 = <<<BODY
<?xml version="1.0" encoding="UTF-8" ?>
<Response>
	<Say language="ja-JP">
		お電話ありがとうございます。
		申し訳ございませんが、
		ただいまの時間は営業時間外となっております。
		営業時間は、土日祝日を除く平日、
		{$from}時から{$to}時までです。
		恐れ入りますが、営業時間内に改めてお掛け直しください。
		お電話ありがとうございました。
   </Say>
</Response>
BODY;
			$response	 = \Fuel\Core\Response::forge($body);
			$response->set_header('Content-type', 'text/xml; charset=utf-8');
			return $response;
		}

		// "PhoneNumber"パラメータで受電か架電か判定
		$outgoing_number = Input::get('PhoneNumber');
		if (Validation::_empty($outgoing_number))
		{
			$caller_id		 = Input::get('Caller');
			$outgoing_number = $this->number_to_client_name(Input::get('To'));
		}
		else
		{
			$caller_id	 = Input::get('Caller');
			$caller_id	 = '+'.str_replace('client:cloudvoice', '', $caller_id);
		}

		if (preg_match("/^[\d\+\-\(\) ]+$/", $outgoing_number))
		{
			$dial_request = "<Number>{$outgoing_number}</Number>";
		}
		else
		{
			$dial_request = "<Client>{$outgoing_number}</Client>";
		}

		$api_base_url = Config::get('const.system.url.api');

		// twiml用XML
		$body		 = <<<BODY
<?xml version="1.0" encoding="UTF-8" ?>
<Response>
	<Dial callerId="{$caller_id}" action="{$api_base_url}api/v1/twilio/action.xml" record="true">
		{$dial_request}
	</Dial>
</Response>
BODY;
		$response	 = \Fuel\Core\Response::forge($body);
		$response->set_header('Content-type', 'text/xml; charset=utf-8');
		return $response;
	}

	/**
	 * 通話終了時のアクション
	 */
	public function action_action()
	{
		$his = null;

		// 通話履歴の保存
		$client	 = new Services_Twilio(Config::get('twilio.account_sid'), Config::get('twilio.auth_token'));
		$array	 = $client->account->calls->getIterator(0, 50, ['ParentCallSid' => Input::post('CallSid')]);
//		echo count($array).':'.Input::post('CallSid');
		foreach ($array as $call)
		{
			$his = Model_Twilio_History::find_epmty_twilio_log(Str::to_local_phone_number($call->to), Str::to_local_phone_number($call->from));
			if ($his)
			{
				$his->sid				 = $call->sid;
				$his->parent_call_sid	 = $call->parent_call_sid;
				$his->account_sid		 = $call->account_sid;
				$his->status			 = $call->status;
				$his->start_time		 = Date::format_datetime($call->start_time, 'Y/m/d H:i:s');
				$his->end_time			 = Date::format_datetime($call->end_time, 'Y/m/d H:i:s');
				$his->duration			 = $call->duration;
				$his->price				 = $call->price;
				$his->save();
			}
			break;
		}

		// 録音ファイルの保存
		//  ※通話が長時間になると、データのダウンロードに時間がかかるため処理を分けている
		$record_file_path	 = '';
		$recording_url		 = Input::post('RecordingUrl');
		if ($recording_url)
		{
			try
			{
				$record_file_path = $this->save_record_file($recording_url);
			}
			catch (Exception $ex)
			{
				Log::warning(print_r(['error', '録音ファイルの保存に失敗 : '.$ex->getMessage()], true));
			}
		}

		// 通話履歴の保存
		if ($his and $record_file_path !== '')
		{
			$his->record_file_path = $record_file_path;
			$his->save();
		}

		// twiml用の空XML
		$body		 = <<<BODY
<?xml version="1.0" encoding="UTF-8" ?>
<Response/>
BODY;
		$response	 = \Fuel\Core\Response::forge($body);
		$response->set_header('Content-type', 'text/xml; charset=utf-8');
		return $response;
	}

	/**
	 * 録音ファイルの保存
	 *
	 * @param string $recording_url 録音ファイルURL
	 * @return string ファイルパス
	 */
	private function save_record_file($recording_url)
	{
		// 保存ディレクトリの生成
		$dir_1		 = Str::random('alnum', 4);
		$dir_2		 = Str::random('alnum', 4);
		$dir_3		 = Str::random('alnum', 4);
		$record_path = Config::get('const.twilio.record.path.absolute').$dir_1.'/'.$dir_2.'/'.$dir_3;
		if ( ! is_dir($record_path))
		{
			mkdir($record_path, 0777, true);
		}

		// 一意のファイル名
		$filename = hash_hmac('sha1', $recording_url, date('YmdHis')).'.mp3';
		while (true)
		{
			if ( ! file_exists($record_path.'/'.$filename))
			{
				break;
			}
			$filename = Str::random('alnum', 40).'.mp3';
		}

		// 保存
		$file_path = '';
		if (File::save_url($recording_url.'.mp3', $record_path.'/'.$filename))
		{
			$file_path = $dir_1.'/'.$dir_2.'/'.$dir_3.'/'.$filename;
		}

		return $file_path;
	}

}
