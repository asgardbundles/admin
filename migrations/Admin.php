<?php
class Admin extends \Asgard\Migration\DBMigration {
	public function up() {
		$table = $this->container['config']['database.prefix'].'administrator';
		$this->container['schema']->create($table, function($table) {
			$table->addColumn('id', 'integer', [
				'integer' => 11,
				'autoincrement' => true,
			]);
			$table->addColumn('created_at', 'datetime');
			$table->addColumn('updated_at', 'datetime');
			$table->addColumn('username', 'string', [
				'length' => 255
			]);
			$table->addColumn('email', 'string', [
				'length' => 255
			]);
			$table->addColumn('password', 'string', [
				'length' => 255
			]);

			$table->setPrimaryKey(['id']);
		});

		\Admin\Entities\Administrator::create(array('username'=>'admin', 'password'=>'admin'));
	}
	
	public function down() {
		$this->container['schema']->drop($this->container['config']['database.prefix'].'administrator');
	}
}