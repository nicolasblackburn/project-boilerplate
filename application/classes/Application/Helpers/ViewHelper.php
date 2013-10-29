<?php
namespace Application\Helpers;

class ViewHelper {
	public function getVariables($templateFile) {
		$source = file_get_contents($templateFile);

		$tokens = token_get_all($source);

		$variables = array();

		while (sizeof($tokens)) {
			$token = array_shift($tokens);

			if (is_array($token)) {
				$id = isset($token[0]) ? $token[0]: '';
				$text = isset($token[1]) ? $token[1]: '';
				$line = isset($token[2]) ? $token[2]: '';

				switch ($id) {
					case T_DOLLAR_OPEN_CURLY_BRACES:
			 		case T_NUM_STRING: 
			 		case T_STRING_VARNAME:
			 			$varname = substr($text, 1);
			 			$variables[$varname] = NULL;
			 			break;

			 		case T_VARIABLE:
			 			$nextToken = array_shift($tokens);
			 			if ('=' !== $nextToken) {
			 				$varname = substr($text, 1);
			 				$variables[$varname] = NULL;
			 			}
			 			break;
				}
			} 
		}

		return $variables;
	}
}