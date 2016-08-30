<?php

return [
	'active'	 => 'default',
	'default'	 => [
		/**
		 * 設定
		 */
		'per_page'		 => 100,
		'uri_segment'    => 'page',
		/**
		 * テンプレート
		 */
		'wrapper'				 => "<div class=\"pagination\">\n\t{pagination}\n</div>\n",
		'first'					 => "<span class=\"first\">\n\t{link}\n</span>\n",
		'first-marker'			 => "&laquo;&laquo;",
		'first-link'			 => "\t\t<a href=\"{uri}\">{page}</a>\n",
		'first-inactive'		 => "",
		'first-inactive-link'	 => "",
		'previous'				 => "<span class=\"previous\">\n\t{link}\n</span>\n",
		'previous-marker'		 => "&laquo;",
		'previous-link'			 => "\t\t<a href=\"{uri}\" rel=\"prev\">{page}</a>\n",
		'previous-inactive'		 => "<span class=\"previous-inactive\">\n\t{link}\n</span>\n",
		'previous-inactive-link' => "\t\t<a href=\"#\" rel=\"prev\">{page}</a>\n",
		'regular'				 => "<span>\n\t{link}\n</span>\n",
		'regular-link'			 => "\t\t<a href=\"{uri}\">{page}</a>\n",
		'active'				 => "<span class=\"active\">\n\t{link}\n</span>\n",
		'active-link'			 => "\t\t<a href=\"#\">{page}</a>\n",
		'next'					 => "<span class=\"next\">\n\t{link}\n</span>\n",
		'next-marker'			 => "&raquo;",
		'next-link'				 => "\t\t<a href=\"{uri}\" rel=\"next\">{page}</a>\n",
		'next-inactive'			 => "<span class=\"next-inactive\">\n\t{link}\n</span>\n",
		'next-inactive-link'	 => "\t\t<a href=\"#\" rel=\"next\">{page}</a>\n",
		'last'					 => "<span class=\"last\">\n\t{link}\n</span>\n",
		'last-marker'			 => "&raquo;&raquo;",
		'last-link'				 => "\t\t<a href=\"{uri}\">{page}</a>\n",
		'last-inactive'			 => "",
		'last-inactive-link'	 => "",
	],
];
