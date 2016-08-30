<?php
/**
 * Fuel
 *
 * Fuel is a fast, lightweight, community driven PHP5 framework.
 *
 * @package    Fuel
 * @version    1.8
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2016 Fuel Development Team
 * @link       http://fuelphp.com
 */

return [
    /**
     * Default settings
     */
    'defaults' => [
        /**
         * Mail driver (mail, smtp, sendmail, noop)
         */
        'driver' => 'smtp', // mail
        /**
         * Email charset
		 *  ## Outlookでの文字化け対策
         */
        'charset' => 'ISO-2022-JP',
        /**
         * Ecoding (8bit, base64 or quoted-printable)
		 *  ## Outlookでの文字化け対策
         */
        'encoding' => '7bit',
        /**
         * SMTP settings
         */
        'smtp' => [
            'host' => '52.196.104.147',
            'port' => 25,
            'username' => 'info',
            'password' => 't3UShRJj',
            'timeout' => 5,
            'starttls' => false,
        ],
	],
];
