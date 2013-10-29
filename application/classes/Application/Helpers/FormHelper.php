<?php
namespace Application\Helpers;

class FormHelper {
	public function echoChecked($array, $key, $value) {
		echo (isset($array[$key]) && $value === $array[$key] ? 'checked="checked"': '');
	}
}