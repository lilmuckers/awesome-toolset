<?php
namespace Awesome\Twitter\DB;

class Define extends \Base\DB\Describe
{
	/**
	 * Define the DB structure
	 * 
	 * @return void
	 */
	protected function _construct()
	{
		$table = $this->_addTable('twitter');
		$table->addColumn('id', 'int(11) NOT NULL auto_increment');
		$table->addColumn('username', 'varchar(255) NOT NULL');
		$table->addColumn('twitter_user_id', 'int(11) NOT NULL');
		$table->addColumn('token', 'varchar(255) NOT NULL');
		$table->addColumn('token_secret', 'varchar(255) NOT NULL');
		$table->addColumn('updated_at', 'datetime NOT NULL default \'0000-00-00 00:00:00\'');
		$table->addColumn('created_at', 'datetime NOT NULL default \'0000-00-00 00:00:00\'');
	}
}