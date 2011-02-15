<?php

class XboxLiveDefine extends BaseDBDescribe
{
	/**
	 * Define the tables we're going to use
	 * 
	 * @return XboxLiveDefine
	 */
	protected function _construct()
	{
		$table = $this->_addTable('gamertag');
		$table->addColumn('id', 'int(11) NOT NULL auto_increment');
		$table->addColumn('gamertag', 'varchar(255) NOT NULL');
		$table->addColumn('score', 'int(11) NOT NULL default \'0\'');
		$table->addColumn('last_checked', 'datetime NOT NULL default \'0000-00-00 00:00:00\'');
		$table->addColumn('account_type', 'enum(\'GOLD\', \'SILVER\') NOT NULL default \'SILVER\'');
		$table->addColumn('link', 'varchar(255) default NULL');
		$table->addColumn('avatar', 'varchar(255) default NULL');
		$table->addColumn('zone', 'varchar(255) default NULL');
		$table->addColumn('reputation_stars', 'varchar(255) default NULL');
		$table->addColumn('login_data', 'blob');
		
		$gameTable = $this->_addTable('game');
		$gameTable->addColumn('id', 'int(11) NOT NULL auto_increment');
		$gameTable->addColumn('name', 'varchar(255) NOT NULL');
		$gameTable->addColumn('total_achievements', 'int(11) NOT NULL');
		$gameTable->addColumn('total_score', 'int(11) NOT NULL');
		$gameTable->addColumn('image_64', 'varchar(255) default NULL');
		$gameTable->addColumn('gamertag_id', 'int(11) NOT NULL');
		$gameTable->addColumn('last_played', 'date default NULL');
		$gameTable->addColumn('achievements', 'int(11) NOT NULL default \'0\'');
		$gameTable->addColumn('score', 'int(11) NOT NULL default \'0\'');
		$gameTable->addColumn('slug', 'varchar(255) NOT NULL');
		$gameTable->addColumn('link', 'varchar(255) NOT NULL');

		
		$achievementTable = $this->_addTable('achievement');
		$achievementTable->addColumn('id', 'int(11) NOT NULL auto_increment');
		$achievementTable->addColumn('game_id', 'int(11) NOT NULL');
		$achievementTable->addColumn('gamertag_id', 'int(11) NOT NULL');
		$achievementTable->addColumn('image', 'varchar(255) NOT NULL');
		$achievementTable->addColumn('name', 'varchar(255) NOT NULL');
		$achievementTable->addColumn('description', 'text NOT NULL');
		$achievementTable->addColumn('score', 'int(11) NOT NULL');
		$achievementTable->addColumn('acquired', 'datetime default NULL');
		$achievementTable->addColumn('slug', 'varchar(255) NOT NULL');
		
		return $this;
	}
}