<?php

namespace Fuel\Migrations;

use Fuel\Core\DBUtil;

class Create_Indexs
{

	public function up()
	{
//        DBUtil::create_index('customer_relations', ['customer_id', 'is_emergency'], 'index_customer_relations_1');
//        DBUtil::create_index('customer_relations', ['login_id'], 'index_customer_relations_2');
//        DBUtil::create_index('twilio_history', ['user_id'], 'index_twilio_history_1');
//        DBUtil::create_index('twilio_history', ['customer_id'], 'index_twilio_history_2');
//        DBUtil::create_index('twilio_history', ['customer_id', 'data_created'], 'index_twilio_history_3');
//        DBUtil::create_index('hearing_surveys', ['twilio_history_id'], 'index_hearing_surveys_1');
//        DBUtil::create_index('hearing_surveys', ['customer_id'], 'index_hearing_surveys_2');
//        DBUtil::create_index('meal_data', ['is_temporary'], 'index_meal_data_1');
	}

	public function down()
	{
//        DBUtil::drop_index('customer_relations', 'index_customer_relations_1');
//        DBUtil::drop_index('customer_relations', 'index_customer_relations_2');
//        DBUtil::drop_index('twilio_history', 'index_twilio_history_1');
//        DBUtil::drop_index('twilio_history', 'index_twilio_history_2');
//        DBUtil::drop_index('twilio_history', 'index_twilio_history_3');
//        DBUtil::drop_index('hearing_surveys', 'index_hearing_surveys_1');
//        DBUtil::drop_index('hearing_surveys', 'index_hearing_surveys_2');
//        DBUtil::drop_index('meal_data', 'index_meal_data_1');
	}
}
