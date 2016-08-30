<?php

return [
	'driver'	 => 'db',
	'db'		 => [
		'cookie_name'		=> 'cloud_voice_v2',
		'database'			=> null,
		'table'				=> 'sessions',
		'gc_probability'	=> 5,
		'expiration_time'	=> 604800, // 1 week
	],
];
