<?php
class Admin extends \Asgard\Migration\DBMigration {
	public function up() {
		$table = $this->app['config']['database.prefix'].'administrator';
		$this->app['schema']->create($table, function($table) {	
			$table->add('id', 'int(11)')
				->autoincrement()
				->primary();	
			$table->add('created_at', 'datetime')
				->nullable();	
			$table->add('updated_at', 'datetime')
				->nullable();	
			$table->add('username', 'varchar(255)')
				->nullable();	
			$table->add('email', 'varchar(250)')
				->nullable();	
			$table->add('password', 'varchar(255)')
				->nullable();
		});

		\Admin\Entities\Administrator::create(array('username'=>'admin', 'password'=>'admin'));
	}
	
	public function down() {
		$this->app['schema']->drop($this->app['config']['database.prefix'].'administrator');
	}
}