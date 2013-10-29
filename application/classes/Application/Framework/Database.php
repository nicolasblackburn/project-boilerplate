<?php
namespace Application\Framework;

class Database extends \PDO {

	public function __construct($config) {
		switch ($config['vendor']) {
			case 'mysql':
				if (PHP_VERSION_ID < 50360) {
					$db = new Application\Framework\Database("mysql:host={$config['host']};dbname={$config['database']}", $config['user'], $config['password']);
					$db->exec("SET NAMES {$config['charset']}");
				} else {
					$db = new Application\Framework\Database("mysql:host={$config['host']};dbname={$config['database']};charset={$config['charset']}", $config['user'], $config['password']);
				}
				return $db;
				break;
		}
	}

	public function prepareExecute($query, $params = array(), $driver_options = array()) {
		$stmt = parent::prepare($query, $driver_options);
		$stmt->execute($params);
		return $stmt;
	}
	
}