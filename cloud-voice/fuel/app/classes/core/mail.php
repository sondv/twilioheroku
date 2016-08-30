<?php

/**
 * メールクラス
 */
class Mail
{

	/**
	 * メールの送信
	 *
	 * @param string $to
	 * @param string $from
	 * @param string $from_name
	 * @param string $subject
	 * @param string $body
	 * @return bool
	 */
	public static function send($to, $from, $from_name, $subject, $body)
	{
		try
		{
			// Outlookの文字化け対策で、ISO-2022-JPで送信
			$from_name	 = mb_convert_encoding($from_name, 'ISO-2022-JP');
			$subject	 = mb_convert_encoding($subject, 'ISO-2022-JP');
			$body		 = mb_convert_encoding($body, 'ISO-2022-JP');

			$email = \Email\Email::forge();
			$email->from($from, $from_name);
			$email->to($to);
			$email->subject($subject);
			$email->body($body);
			$email->send();
		}
		catch (\EmailValidationFailedException $ex)
		{
			\Log::error('メールのバリデーションに失敗しました : '.$ex->getMessage());
			return false;
		}
		catch (\EmailSendingFailedException $ex)
		{
			\Log::error('メールの送信に失敗しました : '.$ex->getMessage());
			return false;
		}

		return true;
	}

	/**
	 * 聞き取りメールを複数アドレスに送信
	 *
	 * @param array  $all_to
	 * @param Model_Customer $customer
	 * @param string $date
	 * @return bool
	 */
	public static function send_hearing_mails($all_to, $customer, $date)
	{
		if ( ! $customer or ! $customer->share or ! $customer->share->hash)
		{
			return false;
		}

		$data		 = [
			'name'		 => $customer->family_name.' '.$customer->given_name,
			'date'		 => $date,
			'url'		 => Uri::create_share_url($customer->share->hash, 'hearing', $date),
		];
		$from		 = 'info@eyemovic.com';
		$from_name	 = 'アイムービック';
		$subject	 = 'Callwellから健康状態登録のお知らせ';
		$body		 = View::forge('mails/hearing', $data);

		$sent = false;
		foreach ($all_to as $to)
		{
			if (empty($to))
			{
				continue;
			}

			$result	 = self::send($to, $from, $from_name, $subject, $body);
			$sent	 = $result ? true : $result;
		}
		return $sent;
	}
}
