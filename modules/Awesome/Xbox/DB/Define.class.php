<?php
namespace Awesome\Xbox\DB;

class Define extends \Base\DB\Describe
{
	/**
	 * Define the DB structure
	 * 
	 * @return void
	 */
	protected function _construct()
	{
		$this->_gameTable()
			->_gameRatingsTables()
			->_achievementTable()
			->_gameImageTable()
			->_gameBuyTable()
			->_gamertagTable()
			->_gamersGameTable()
			->_gamersAchievementTable()
			->_notificationTable();
	}
	
	/**
	 * Define the DB structure for gamertag table
	 * 
	 * @return \Awesome\Xbox\DB\Define
	 */
	protected function _gamertagTable()
	{
		$table = $this->_addTable('gamertag');
		$table->setEngine(\Base\DB\Table::ENGINE_INNODB);
		$table->addColumn('id', 'int(11) NOT NULL auto_increment');
		$table->addColumn('gamertag', 'varchar(255) NOT NULL');
		$table->addColumn('activity', 'text NOT NULL');
		$table->addColumn('avatar', 'varchar(255) NOT NULL');
		$table->addColumn('picture', 'varchar(255) NOT NULL');
		$table->addColumn('score', 'int(10) NOT NULL');
		$table->addColumn('location', 'varchar(255) NOT NULL');
		$table->addColumn('motto', 'varchar(255) NOT NULL');
		$table->addColumn('bio', 'varchar(255) NOT NULL');
		$table->addColumn('login_data', 'blob');
		$table->addColumn('updated_at', 'datetime NOT NULL default \'0000-00-00 00:00:00\'');
		$table->addColumn('created_at', 'datetime NOT NULL default \'0000-00-00 00:00:00\'');
		return $this;
	}
	
	/**
	 * Define the DB structure for the gamertags game table
	 * 
	 * @return \Awesome\Xbox\DB\Define
	 */
	protected function _gamersGameTable()
	{
		$table = $this->_addTable('gamertag_game');
		$table->setEngine(\Base\DB\Table::ENGINE_INNODB);
		$table->addColumn('id', 'int(11) NOT NULL auto_increment');
		$table->addColumn('game_id', 'int(11) NOT NULL');
		$table->addForeignKey('game_id', 'game');
		$table->addColumn('gamertag_id', 'int(11) NOT NULL');
		$table->addForeignKey('gamertag_id', 'gamertag');
		$table->addColumn('last_played', 'datetime NOT NULL default \'0000-00-00 00:00:00\'');
		$table->addColumn('score', 'int(11) NOT NULL');
		$table->addColumn('achievements', 'int(11) NOT NULL');
		$table->addColumn('updated_at', 'datetime NOT NULL default \'0000-00-00 00:00:00\'');
		$table->addColumn('created_at', 'datetime NOT NULL default \'0000-00-00 00:00:00\'');
		return $this;
	}
	
	/**
	 * Define the DB structure for the game table
	 * 
	 * @return \Awesome\Xbox\DB\Define
	 */
	protected function _gameTable()
	{
		$table = $this->_addTable('game');
		$table->setEngine(\Base\DB\Table::ENGINE_INNODB);
		$table->addColumn('id', 'int(11) NOT NULL auto_increment');
		$table->addColumn('xbl_id', 'int(11) NOT NULL');
		$table->addColumn('slug', 'varchar(255) NOT NULL');
		$table->addColumn('title', 'varchar(255) NOT NULL');
		$table->addColumn('description', 'text');
		$table->addColumn('manual', 'varchar(255)');
		$table->addColumn('small_boxart', 'varchar(255) NOT NULL');
		$table->addColumn('large_boxart', 'varchar(255)');
		$table->addColumn('banner', 'varchar(255)');
		$table->addColumn('marketplace_link', 'varchar(255)');
		$table->addColumn('achievements_link', 'varchar(255) NOT NULL');
		$table->addColumn('total_score', 'int(11) NOT NULL');
		$table->addColumn('total_achievements', 'int(11) NOT NULL');
		$table->addColumn('updated_at', 'datetime NOT NULL default \'0000-00-00 00:00:00\'');
		$table->addColumn('created_at', 'datetime NOT NULL default \'0000-00-00 00:00:00\'');
		return $this;
	}
	
	/**
	 * Define the DB structure for the game table
	 * 
	 * @return \Awesome\Xbox\DB\Define
	 */
	protected function _gameRatingsTables()
	{
		$table = $this->_addTable('game_rating');
		$table->setEngine(\Base\DB\Table::ENGINE_INNODB);
		$table->addColumn('id', 'int(11) NOT NULL auto_increment');
		$table->addColumn('title', 'varchar(255) NOT NULL');
		$table->addColumn('image', 'varchar(255) NOT NULL');
		$table->addColumn('updated_at', 'datetime NOT NULL default \'0000-00-00 00:00:00\'');
		$table->addColumn('created_at', 'datetime NOT NULL default \'0000-00-00 00:00:00\'');
		
		$table = $this->_addTable('game_rating_link');
		$table->setEngine(\Base\DB\Table::ENGINE_INNODB);
		$table->addColumn('id', 'int(11) NOT NULL');
		$table->addColumn('rating_id', 'int(11) NOT NULL');
		$table->addForeignKey('rating_id', 'game_rating');
		$table->addColumn('game_id', 'int(11) NOT NULL');
		$table->addForeignKey('game_id', 'game');
		$table->addColumn('main', 'int(1) NOT NULL DEFAULT 0');
		$table->addColumn('updated_at', 'datetime NOT NULL default \'0000-00-00 00:00:00\'');
		$table->addColumn('created_at', 'datetime NOT NULL default \'0000-00-00 00:00:00\'');
		return $this;
	}
	
	/**
	 * Define the DB structure for the game table
	 * 
	 * @return \Awesome\Xbox\DB\Define
	 */
	protected function _gameImageTable()
	{
		$table = $this->_addTable('game_image');
		$table->setEngine(\Base\DB\Table::ENGINE_INNODB);
		$table->addColumn('id', 'int(11) NOT NULL auto_increment');
		$table->addColumn('game_id', 'int(11) NOT NULL');
		$table->addForeignKey('game_id', 'game');
		$table->addColumn('image', 'varchar(255) NOT NULL');
		$table->addColumn('updated_at', 'datetime NOT NULL default \'0000-00-00 00:00:00\'');
		$table->addColumn('created_at', 'datetime NOT NULL default \'0000-00-00 00:00:00\'');
		return $this;
	}
	
	/**
	 * Define the DB structure for the game table
	 * 
	 * @return \Awesome\Xbox\DB\Define
	 */
	protected function _gameBuyTable()
	{
		$table = $this->_addTable('game_buy');
		$table->setEngine(\Base\DB\Table::ENGINE_INNODB);
		$table->addColumn('id', 'int(11) NOT NULL auto_increment');
		$table->addColumn('game_id', 'int(11) NOT NULL');
		$table->addForeignKey('game_id', 'game');
		$table->addColumn('link', 'varchar(255) NOT NULL');
		$table->addColumn('description', 'varchar(255) NOT NULL');
		$table->addColumn('price', 'varchar(255) NOT NULL');
		$table->addColumn('updated_at', 'datetime NOT NULL default \'0000-00-00 00:00:00\'');
		$table->addColumn('created_at', 'datetime NOT NULL default \'0000-00-00 00:00:00\'');
		return $this;
	}
	
	/**
	 * Define the DB structure for the achievement table
	 * 
	 * @return \Awesome\Xbox\DB\Define
	 */
	protected function _gamersAchievementTable()
	{
		$table = $this->_addTable('gamertag_game_achievement');
		$table->setEngine(\Base\DB\Table::ENGINE_INNODB);
		$table->addColumn('id', 'int(11) NOT NULL auto_increment');
		$table->addColumn('game_id', 'int(11) NOT NULL');
		$table->addForeignKey('game_id', 'game');
		$table->addColumn('gamertag_id', 'int(11) NOT NULL');
		$table->addForeignKey('gamertag_id', 'gamertag');
		$table->addColumn('achievement_id', 'int(11) NOT NULL');
		$table->addForeignKey('achievement_id', 'game_achievement');
		$table->addColumn('acquired', 'datetime default \'0000-00-00 00:00:00\'');
		$table->addColumn('updated_at', 'datetime NOT NULL default \'0000-00-00 00:00:00\'');
		$table->addColumn('created_at', 'datetime NOT NULL default \'0000-00-00 00:00:00\'');
		return $this;
	}
	
	/**
	 * Define the DB structure for the achievement table
	 * 
	 * @return \Awesome\Xbox\DB\Define
	 */
	protected function _achievementTable()
	{
		$table = $this->_addTable('game_achievement');
		$table->setEngine(\Base\DB\Table::ENGINE_INNODB);
		$table->addColumn('id', 'int(11) NOT NULL auto_increment');
		$table->addColumn('slug', 'varchar(255) NOT NULL');
		$table->addColumn('title', 'varchar(255) NOT NULL');
		$table->addColumn('description', 'text');
		$table->addColumn('score', 'int(11) NOT NULL');
		$table->addColumn('image', 'varchar(255) NOT NULL');
		$table->addColumn('inactive_image', 'varchar(255)');
		$table->addColumn('game_id', 'int(11) NOT NULL');
		$table->addForeignKey('game_id', 'game');
		$table->addColumn('updated_at', 'datetime NOT NULL default \'0000-00-00 00:00:00\'');
		$table->addColumn('created_at', 'datetime NOT NULL default \'0000-00-00 00:00:00\'');
		return $this;
	}
	
	/**
	 * Define the DB structure for the profile notification table
	 * 
	 * @return \Awesome\Xbox\DB\Define
	 */
	protected function _notificationTable()
	{
		$table = $this->_addTable('gamertag_notification');
		$table->setEngine(\Base\DB\Table::ENGINE_INNODB);
		$table->addColumn('id', 'int(11) NOT NULL auto_increment');
		$table->addColumn('gamertag_id', 'int(11) NOT NULL');
		$table->addForeignKey('gamertag_id', 'gamertag');
		$table->addColumn('notification_type', 'varchar(255) NOT NULL');
		$table->addColumn('account_identifier', 'varchar(255) NOT NULL');
		$table->addColumn('template', 'varchar(255)');
		$table->addColumn('updated_at', 'datetime NOT NULL default \'0000-00-00 00:00:00\'');
		$table->addColumn('created_at', 'datetime NOT NULL default \'0000-00-00 00:00:00\'');
		return $this;
	}
}