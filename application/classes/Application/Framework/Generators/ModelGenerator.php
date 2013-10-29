<?php
namespace Application\Framework\Generators;

class ModelGenerator {
	protected $db;
	protected $stringHelper;

	function __construct($db, $stringHelper) {
		$this->db = $db;
		$this->stringHelper = $stringHelper;
	}

	function generateAllModelsSource($namespace, $classNamePattern, $tablePrefix) {
		$db = $this->db;
		$stringHelper = $this->stringHelper;

		$sources = array();

		$row = $db->query("SELECT DATABASE()")->fetch();
		$database = '`' . $row[0] . '`';

		foreach ($db->query("
				SHOW TABLES;
			")->fetchAll() as $rowId => $row) {

			$table = "`{$row[0]}`";
			$pk = $db->query("SHOW INDEX FROM $table WHERE Key_name = 'PRIMARY'")->fetchAll();
			$columns = $db->query("DESCRIBE $table")->fetchAll();
			$table = preg_replace("`^{$tablePrefix}`", '', $row[0]);

			$aProperties = array();
			$aMethods = array();

			$aProperties['components'] = "	protected \$components;";

			$aMethods['__construct'] = 
"	public function __construct(\$components) {
		\$this->components = \$components;
	}";

			// delete

			// fetch

			$params = implode(', ', array_map(function($col) {
				return "\${$col['Column_name']}";
			}, $pk) );
			$where = implode(" \n\t\t\t\tAND ", array_map(function($col) use ($table) {
				return "`{\$prefix}$table`.`{$col['Column_name']}` = :{$col['Column_name']}";
			}, $pk) );
			$execParams = implode(",\n\t\t\t\t\t", array_map(function($col) use ($table) {
				return "':{$col['Column_name']}' => \${$col['Column_name']}";
			}, $pk) );

			$aMethods['fetch'] = 
"	public function fetch($params) {
		\$db = \$this->components->db;
		\$prefix = \$this->components->config['db']['prefix'];
		
		\$stmt = \$db->prepare(\"
				SELECT `{\$prefix}$table`.* 
				FROM `{\$prefix}$table` 
				WHERE $where 
				LIMIT 1
			\"); 

		\$stmt->execute(
				array(
					$execParams
				)
			);

		return \$stmt->fetch();
	}";

			// insert

			$columnsStmt = implode(', ', array_map(function($col) {
				return "'{$col['Field']}'";
			}, $columns) );

			$aMethods['insert'] = 
"	public function insert(\$data) {
		\$db = \$this->components->db;
		\$prefix = \$this->components->config['db']['prefix'];
		
		\$columns = array($columnsStmt);

		\$columnsStmt = '';

		\$valuesStmt = '';

		\$execParams = array();

		foreach (\$columns as \$columnIndex => \$column) {
			if (isset(\$data[\$column])) {
				\$columnsStmt .= (strlen(\$columnsStmt) ? ', ': '') . \"`{\$column}`\";
				\$valuesStmt .= (strlen(\$valuesStmt) ? ', ': '') . \":{\$column}\";
				\$execParams[\$column] = \$data[\$column];
			}
		}

		\$stmt = \$db->prepare(\"
				INSERT INTO `{\$prefix}$table` ({\$columnsStmt}) 
				VALUES ({\$valuesStmt})
			\"); 

		\$result = \$stmt->execute(\$execParams);

		return \$result;
	}";

			// lastInsertId

			$aMethods['lastInsertId'] = 
"	public function lastInsertId(\$query) {
		\$db = \$this->components->db;
		
		return \$db->lastInsertId(); 
	}";

			// update

			$columnsStmt = implode(', ', array_map(function($col) {
				return "'{$col['Field']}'";
			}, $columns) );

			$pkStmt = implode(", ", array_map(function($col) use ($table) {
				return "'{$col['Column_name']}'";
			}, $pk) );

			$aMethods['update'] = 
"	public function update(\$data) {
		\$db = \$this->components->db;
		\$prefix = \$this->components->config['db']['prefix'];
		
		\$columns = array($columnsStmt);

		\$pk = array($pkStmt);

		\$whereStmt = '';

		\$valuesStmt = '';

		\$execParams = array();

		foreach (\$columns as \$columnIndex => \$column) {
			if (isset(\$data[\$column])) {
				if (in_array(\$column, \$pk)) {
					\$whereStmt .= (strlen(\$valuesStmt) ? ' AND ': '') . \"`{\$column}` = :{\$column}\";
				} else {
					\$valuesStmt .= (strlen(\$valuesStmt) ? ', ': '') . \"`{\$column}` = :{\$column}\";
				}
				\$execParams[\$column] = \$data[\$column];
			}
		}

		\$stmt = \$db->prepare(\"
				UPDATE `{\$prefix}$table` SET {\$valuesStmt} 
				WHERE {\$whereStmt}
			\"); 

		\$result = \$stmt->execute(\$execParams);

		return \$result;
	}";

			$name = $stringHelper->underscoreToCamelCase( preg_replace( "`^$tablePrefix`", '', $row[0] ) );
			$class = sprintf($classNamePattern, $name);
			$filename = $class . '.php';

			$properties = implode("\n", $aProperties);
			$methods = implode("\n\n", $aMethods);

			$sources[$filename] = 
"<?php
namespace $namespace;

abstract class $class {
$properties

$methods
}
";
		}
		
		return $sources;
	}
}