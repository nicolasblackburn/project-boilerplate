<?php
namespace Application\Helpers;

class StringHelper {
	public function underscoreToCamelCase($str) {
		return implode('', array_map('ucfirst', explode('_', $str)));
	}

	public function latinToAscii($str) {
		$map = array(
			'á' => 'a',
			'à' => 'a',
			'â' => 'a',
			'ä' => 'a',
			'é' => 'e',
			'è' => 'e',
			'ê' => 'e',
			'ë' => 'e',
			'í' => 'i',
			'ì' => 'i',
			'î' => 'i',
			'ï' => 'i',
			'ó' => 'o',
			'ò' => 'o',
			'ô' => 'o',
			'ö' => 'o',
			'ú' => 'u',
			'ù' => 'u',
			'û' => 'u',
			'ü' => 'u',
			'ç' => 'c',
			'ñ' => 'n',
			'Á' => 'A',
			'À' => 'A',
			'Â' => 'A',
			'Ä' => 'A',
			'É' => 'E',
			'È' => 'E',
			'Ê' => 'E',
			'Ë' => 'E',
			'Í' => 'I',
			'Ì' => 'I',
			'Î' => 'I',
			'Ï' => 'I',
			'Ó' => 'O',
			'Ò' => 'O',
			'Ô' => 'O',
			'Ö' => 'O',
			'Ú' => 'U',
			'Ù' => 'U',
			'Û' => 'U',
			'Ü' => 'U',
			'Ç' => 'C',
			'Ñ' => 'N',
		);
		$search = array_keys($map);
		$replace = array_values($map);
		return str_replace($search, $replace, $str);
	}
}