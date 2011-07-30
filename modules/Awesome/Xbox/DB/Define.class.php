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
	}
	
	/**
	 * Define the DB structure for gamertag table
	 * 
	 * @return void
	 */
	protected function _gamertagTable()
	{
		$table = $this->_addTable('gamertag');
		$table->addColumn('id', 'int(11) NOT NULL auto_increment');
		$table->addColumn('gamertag', 'varchar(255) NOT NULL');
		$table->addColumn('avatar', 'varchar(255) NOT NULL');
		$table->addColumn('picture', 'varchar(255) NOT NULL');
		$table->addColumn('score', 'int(10) NOT NULL');
		$table->addColumn('location', 'varchar(255) NOT NULL');
		$table->addColumn('motto', 'varchar(255) NOT NULL');
		$table->addColumn('bio', 'varchar(255) NOT NULL');
		$table->addColumn('updated_at', 'datetime NOT NULL default \'0000-00-00 00:00:00\'');
		$table->addColumn('last_checked_at', 'datetime NOT NULL default \'0000-00-00 00:00:00\'');
		$table->addColumn('created_at', 'datetime NOT NULL default \'0000-00-00 00:00:00\'');
	}
	
	/**
	 * Define the DB structure for the gamertags game table
	 * 
	 * @return void
	 */
	protected function _gamersGameTable()
	{
		$table = $this->_addTable('gamertag_game');
		$table->addColumn('id', 'int(11) NOT NULL auto_increment');
		$table->addColumn('game_id', 'int(11) NOT NULL');
		$table->addColumn('last_played', 'datetime NOT NULL default \'0000-00-00 00:00:00\'');
		$table->addColumn('score', 'int(11) NOT NULL');
		$table->addColumn('achievements', 'int(11) NOT NULL');
		$table->addColumn('updated_at', 'datetime NOT NULL default \'0000-00-00 00:00:00\'');
		$table->addColumn('created_at', 'datetime NOT NULL default \'0000-00-00 00:00:00\'');
	}
	
	/**
	 * Define the DB structure for the game table
	 * 
	 * @return void
	 */
	protected function _gameTable()
	{
		$table = $this->_addTable('game');
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
	}
	
	/**
	 * Define the DB structure for the game table
	 * 
	 * @return void
	 */
	protected function _gameRatingsTables()
	{
		$table = $this->_addTable('game_rating');
		$table->addColumn('id', 'int(11) NOT NULL auto_increment');
		$table->addColumn('title', 'varchar(255) NOT NULL');
		$table->addColumn('image', 'varchar(255) NOT NULL');
		$table->addColumn('updated_at', 'datetime NOT NULL default \'0000-00-00 00:00:00\'');
		$table->addColumn('created_at', 'datetime NOT NULL default \'0000-00-00 00:00:00\'');
		
		$table = $this->_addTable('game_rating_link');
		$table->addColumn('game_id', 'int(11) NOT NULL');
		$table->addColumn('rating_id', 'int(11) NOT NULL');
		$table->addColumn('main', 'int(1) NOT NULL DEFAULT 0');
		$table->addColumn('updated_at', 'datetime NOT NULL default \'0000-00-00 00:00:00\'');
		$table->addColumn('created_at', 'datetime NOT NULL default \'0000-00-00 00:00:00\'');
	}
	
	/**
	 * Define the DB structure for the game table
	 * 
	 * @return void
	 */
	protected function _gameImageTable()
	{
		$table = $this->_addTable('game_image');
		$table->addColumn('id', 'int(11) NOT NULL auto_increment');
		$table->addColumn('game_id', 'int(11) NOT NULL');
		$table->addColumn('image', 'varchar(255) NOT NULL');
		$table->addColumn('updated_at', 'datetime NOT NULL default \'0000-00-00 00:00:00\'');
		$table->addColumn('created_at', 'datetime NOT NULL default \'0000-00-00 00:00:00\'');
	}
	
	/**
	 * Define the DB structure for the game table
	 * 
	 * @return void
	 */
	protected function _gameBuyTable()
	{
		$table = $this->_addTable('game_buy');
		$table->addColumn('id', 'int(11) NOT NULL auto_increment');
		$table->addColumn('game_id', 'int(11) NOT NULL');
		$table->addColumn('link', 'varchar(255) NOT NULL');
		$table->addColumn('description', 'varchar(255) NOT NULL');
		$table->addColumn('price', 'varchar(255) NOT NULL');
		$table->addColumn('updated_at', 'datetime NOT NULL default \'0000-00-00 00:00:00\'');
		$table->addColumn('created_at', 'datetime NOT NULL default \'0000-00-00 00:00:00\'');
	}
	
	/**
	 * Define the DB structure for the achievement table
	 * 
	 * @return void
	 */
	protected function _achievementTable()
	{
		
	}
	
	/**
	 * Define the DB structure for the profile notification table
	 * 
	 * @return void
	 */
	protected function _notificationTable()
	{
		
	}
}