<?php

namespace Fuel\Migrations;

use Fuel;
use Fuel\Core\DBUtil;

class Insert_Data
{

	public function up()
	{
		if (Fuel::$env !== 'development')
		{
			return;
		}

		$customer					 = new \Model_Staff();
		$customer->login_id			 = "hoge";
		$customer->password			 = "xlyUES0M6Zqak7z5B/a477HRfcQkuQfp8iVh8+9i3Qo="; // hoge
		$customer->family_name		 = "山田";
		$customer->given_name		 = "太郎";
		$customer->family_name_kana	 = "ヤマダ";
		$customer->given_name_kana	 = "タロウ";
		$customer->email			 = "info@eyemovic.com";
		$customer->phone			 = "098-999-9999";
		$customer->save();
	}

	public function down()
	{
		if (Fuel::$env !== 'development')
		{
			return;
		}

		DBUtil::truncate_table('staffs');
		//        DBUtil::truncate_table('customer_relations');
		//        DBUtil::truncate_table('customer_profile');
	}

}
