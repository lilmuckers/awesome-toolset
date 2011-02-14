<?php

class XboxLiveDefine extends BaseDBDescribe
{
	/**
	 * Define the tables we're going to use
	 * 
	 * @return XboxLiveDefine
	 */
	public function _construct()
	{
		$table = $this->_addTable('gamertag');
		$table->addColumn('id', 'int(11) NOT NULL auto_increment');
		$table->addColumn('game_id', 'int(11) NOT NULL');
		$table->addColumn('gamertag_id', 'int(11) NOT NULL');
		$table->addColumn('image', 'varchar(255) NOT NULL');
		$table->addColumn('name', 'varchar(255) NOT NULL');
		$table->addColumn('description', 'text NOT NULL');
		$table->addColumn('score', 'int(11) NOT NULL');
		$table->addColumn('acquired', 'datetime default NULL');
		$table->addColumn('slug', 'varchar(255) NOT NULL');
		$table->addColumn('login_data', 'blob');
		
		$table = $this->_addTable('game');
		$table->addColumn('id', 'int(11) NOT NULL auto_increment');
		$table->addColumn('name', 'varchar(255) NOT NULL');
		$table->addColumn('total_achievements', 'int(11) NOT NULL');
		$table->addColumn('total_score', 'int(11) NOT NULL');
		$table->addColumn('image_64', 'varchar(255) default NULL');
		$table->addColumn('gamertag_id', 'int(11) NOT NULL');
		$table->addColumn('last_played', 'date default NULL');
		$table->addColumn('achievements', 'int(11) NOT NULL default \'0\'');
		$table->addColumn('score', 'int(11) NOT NULL default \'0\'');
		$table->addColumn('slug', 'varchar(255) NOT NULL');
		$table->addColumn('link', 'varchar(255) NOT NULL');

		$table = $this->_addTable('achievement');
		$table->addColumn('id', 'int(11) NOT NULL auto_increment');
		$table->addColumn('game_id', 'int(11) NOT NULL');
		$table->addColumn('gamertag_id', 'int(11) NOT NULL');
		$table->addColumn('image', 'varchar(255) NOT NULL');
		$table->addColumn('name', 'varchar(255) NOT NULL');
		$table->addColumn('description', 'text NOT NULL');
		$table->addColumn('score', 'int(11) NOT NULL');
		$table->addColumn('acquired', 'datetime default NULL');
		$table->addColumn('slug', 'varchar(255) NOT NULL');
		
		return $this;
	}
}