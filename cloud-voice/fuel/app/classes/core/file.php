<?php

/**
 * \Fileの拡張
 */
class File extends \Fuel\Core\File
{

	/**
	 * URLからファイルを取得し、パスへ保存する
	 *
	 * @param  string $url URL
	 * @param  string $save_path 保存先のパス（ファイル名込み）
	 * @return bool
	 */
	public static function save_url($url, $save_path)
	{
		try
		{
			$curl	 = curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			$data	 = curl_exec($curl);
			file_put_contents($save_path, $data);
			curl_close($curl);
		}
		catch (Exception $e)
		{
			return false;
		}

		return true;
	}

}
