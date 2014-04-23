<?php
class Admin {
	public static function up() {
		$table = \Asgard\Core\App::get('config')->get('database/prefix').'administrator';
		\Asgard\Core\App::get('schema')->create($table, function($table) {	
			$table->add('id', 'int(11)')
				->autoincrement()
				->primary();	
			$table->add('created_at', 'datetime')
				->nullable();	
			$table->add('updated_at', 'datetime')
				->nullable();	
			$table->add('username', 'varchar(100)')
				->nullable();	
			$table->add('password', 'varchar(100)')
				->nullable();
		});

		$yaml = new \Symfony\Component\Yaml\Parser;
		$raw = $yaml->parse(file_get_contents(_DIR_.'migrations/fixtures/admin.yml'));
		foreach($raw as $entityClass=>$rows) {
			foreach($rows as $row)
				$entityClass::create($row);
		}
	}
	
	public static function down() {
		\Asgard\Core\App::get('schema')->drop(\Asgard\Core\App::get('config')->get('database/prefix').'administrator');
	}
}