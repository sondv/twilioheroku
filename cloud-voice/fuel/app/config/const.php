<?php

return [
	/**
	 * システム
	 */
	'system'	 => [
		/**
		 * URL
		 */
		'url'			 => [
			'www'		 => 'https://joincallwell.com/',
			'manage'	 => 'https://manage.joincallwell.com/',
			'share'		 => 'https://share.joincallwell.com/',
			'assets'	 => 'https://assets.joincallwell.com/',
			'resource'	 => 'https://resource.joincallwell.com/',
			'api'		 => 'https://share.joincallwell.com/',
		],
		/**
		 * 国際
		 */
		'international'	 => [
			/**
			 * 国際電話番号
			 */
			'calling_code' => [
				'jp' => '+81', // 日本
				'tw' => '+886', // 台湾
				'ch' => '+86', // 中国 (本土)
				'hk' => '+852', // 香港
				'kr' => '+82', // 韓国
				'us' => '+1', // アメリカ (本土)
			],
		],
		/**
		 * 曜日
		 */
		'week_short'	 => [
			0	 => '日',
			1	 => '月',
			2	 => '火',
			3	 => '水',
			4	 => '木',
			5	 => '金',
			6	 => '土',
		],
		/**
		 * 営業時間
		 */
		'business_hours' => [
			'from'	 => '9',
			'to'	 => '18',
		],
	],
	/**
	 * twilio
	 */
	'twilio'	 => [
		/**
		 * 録音
		 */
		'record' => [
			'path' => [
				'absolute'	 => realpath(DOCROOT.'../../resource/record/').DS,
				'relative'	 => '/resource/record/'
			],
		],
	],
	/**
	 * 利用者
	 */
	'customer'	 => [
		/**
		 * 顔写真
		 */
		'photo'		 => [
			/**
			 * パス
			 */
			'path' => [
				'absolute'	 => realpath(DOCROOT.'../../resource/customer/').DS,
				'relative'	 => '/resource/customer/'
			],
		],
		/**
		 * 血液型
		 */
		'blood_type' => [
			['value' => 'A', 'label' => 'A型'],
			['value' => 'B', 'label' => 'B型'],
			['value' => 'O', 'label' => 'O型'],
			['value' => 'AB', 'label' => 'AB型'],
		],
	],
	/**
	 * 聞き取り
	 */
	'hearing'	 => [
		/**
		 * 対象
		 */
		'target'		 => [
			'condition'	 => '体調について',
			'meal'		 => '食欲・食事について',
			'activity'	 => '活動・運動について',
			'feeling'	 => 'コミュニケーション・気持ちについて',
		],
		/**
		 * 質問
		 */
		'questions'		 => [
			1	 => [
				'question'	 => '体調',
				'answers'	 => [
					['value' => 5, 'label' => '元気'],
					['value' => 4, 'label' => '少ししんどい'],
					['value' => 3, 'label' => '体調悪い'],
					['value' => 2, 'label' => '風邪気味'],
					['value' => 1, 'label' => '体が痛い'],
				],
			],
			2	 => [
				'question'	 => '食欲',
				'answers'	 => [
					['value' => 3, 'label' => 'いつもよりある'],
					['value' => 2, 'label' => 'ある'],
					['value' => 1, 'label' => 'あまりない'],
				],
			],
			3	 => [
				'question'	 => '食事の回数',
				'answers'	 => [
					['value' => 1, 'label' => '朝食'],
					['value' => 2, 'label' => '昼食'],
					['value' => 3, 'label' => '夕食'],
					['value' => 4, 'label' => '間食（お菓子、果物）'],
				],
			],
			4	 => [
				'question'	 => '食べた量',
				'answers'	 => [
					['value' => 4, 'label' => 'お腹いっぱい'],
					['value' => 3, 'label' => '腹8分'],
					['value' => 2, 'label' => '腹5分'],
					['value' => 1, 'label' => 'あまり食べれてない'],
				],
			],
			5	 => [
				'question'	 => '体を動かす活動や運動',
				'answers'	 => [
					['value' => 1, 'label' => '仕事に出かけた'],
					['value' => 2, 'label' => '買い物に出かけた'],
					['value' => 3, 'label' => 'カルチャースクールなどに出かけた'],
					['value' => 4, 'label' => '友人・家族と会いに出かけた'],
					['value' => 5, 'label' => '軽い運動した'],
					['value' => 6, 'label' => '外出していない'],
				],
			],
			6	 => [
				'question'	 => '活動量',
				'answers'	 => [
					['value' => 3, 'label' => 'よく動かした'],
					['value' => 2, 'label' => '少し動かした'],
					['value' => 1, 'label' => '余り動かさなかった'],
				],
			],
			7	 => [
				'question'	 => '疲れは残ってますか',
				'answers'	 => [
					['value' => 3, 'label' => '疲れてない'],
					['value' => 2, 'label' => 'ちょっと疲れている'],
					['value' => 1, 'label' => 'しんどい'],
				],
			],
			8	 => [
				'question'	 => '人と話しをしたか',
				'answers'	 => [
					['value' => 3, 'label' => 'よく話した'],
					['value' => 2, 'label' => '少し話した'],
					['value' => 1, 'label' => '全く話してない'],
				],
			],
			9	 => [
				'question'	 => '会話の内容を覚えているか',
				'answers'	 => [
					['value' => 3, 'label' => 'よく覚えている'],
					['value' => 2, 'label' => '余り覚えてない'],
					['value' => 1, 'label' => '全く覚えていない'],
				],
			],
			10	 => [
				'question'	 => '今日の気持ち、心の状態',
				'answers'	 => [
					['value' => 6, 'label' => '楽しかった'],
					['value' => 5, 'label' => '普通だった'],
					['value' => 4, 'label' => 'しんどかった'],
					['value' => 3, 'label' => '不安だった'],
					['value' => 2, 'label' => '寂しかった'],
					['value' => 1, 'label' => 'イライラした'],
				],
			],
		],
		/**
		 * 感想・印象
		 */
		'impressions'	 => [
			1 => [
				'question'	 => '声のトーン、会話の様子（5段階評価）',
				'answers'	 => [
					['value' => 5, 'label' => '5 声の張りもあり元気そうです'],
					['value' => 4, 'label' => '4 普段の生活をされている感じです'],
					['value' => 3, 'label' => '3 あまり元気がありませんでした'],
					['value' => 2, 'label' => '2 少し会話がちぐはぐしていました'],
					['value' => 1, 'label' => '1 様子がおかしく感じました（連絡してあげてください）'],
				],
			],
		],
		/**
		 * 食事
		 */
		'meal'			 => [
			/**
			 * 単位
			 */
			'unit' => [
				'人前',
				'cc',
				'杯',
				'枚',
				'切',
				'袋',
			],
		],
	],
];
