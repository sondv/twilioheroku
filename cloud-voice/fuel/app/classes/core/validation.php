<?php

/**
 * \Fuel\Core\Validationの拡張
 */
class Validation extends \Fuel\Core\Validation
{

	/**
	 * 独自ルール：相対パス
	 *
	 * @param type $val
	 * @return bool
	 */
	public function _validation_valid_path($val)
	{
		return $this->_validation_valid_url($val) || preg_match('/^([-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)$/', $val);
	}

	public function get_messages()
	{
		$errors = [];
		foreach ($this->error() as $error)
		{
			$errors[$error->field->name] = $error->get_message();
		}
		return $errors;
	}
}
