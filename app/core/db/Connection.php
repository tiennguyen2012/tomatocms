<?php
/**
 * TomatoCMS
 * 
 * LICENSE
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE Version 2 
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-2.0.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@tomatocms.com so we can send you a copy immediately.
 * 
 * @copyright	Copyright (c) 2009-2010 TIG Corporation (http://www.tig.vn)
 * @license		http://www.gnu.org/licenses/gpl-2.0.txt GNU GENERAL PUBLIC LICENSE Version 2
 * @version 	$Id: Connection.php 1437 2010-03-04 03:50:12Z huuphuoc $
 */

class Tomato_Core_Db_Connection
{
	const KEY = 'Tomato_Core_Db_Connection_Key';
	const PREFIX_KEY = 'Tomato_Core_Db_Connection_TablePrefix';
	
	/**
	 * Default table prefix
	 * 
	 * @var const
	 * @since 2.0.3
	 */
	const DEFAULT_PREFIX = 't_';
	
	/**
	 * Support master connection type
	 * 
	 * @return Zend_Db_Adapter_Abstract
	 */
	public static function getMasterConnection() 
	{
		return self::_getConnection('master');
	}
	
	/**
	 * Support slave connection type
	 * 
	 * @return Zend_Db_Adapter_Abstract
	 */
	public static function getSlaveConnection() 
	{
		return self::_getConnection('slave');
	}
	
	/**
	 * Get database table prefix
	 * 
	 * @since 2.0.3
	 * @return string
	 */
	public static function getDbPrefix() 
	{
		if (!Zend_Registry::isRegistered(self::PREFIX_KEY)) {
			$config = Tomato_Core_Config::getConfig();
			
			// Note that I use === operator that allows user to use empty prefix
			$prefix = (null === $config->db->prefix) ? self::DEFAULT_PREFIX : $config->db->prefix;
			Zend_Registry::set(self::PREFIX_KEY, $prefix);
		}
		return Zend_Registry::get(self::PREFIX_KEY);
	}
	
	/**
	 * @param string $type Type of connection. Must be slave or master
	 * @return Zend_Db_Adapter_Abstract
	 */
	private static function _getConnection($type) 
	{
		$key = self::KEY.'_'.$type;
		if (!Zend_Registry::isRegistered($key)) {
			$config = Tomato_Core_Config::getConfig();
			$servers = $config->db->$type;
			
			// Connect to random server
			$servers = $servers->toArray();
			$randomServer = array_rand($servers);
			
			// Get database prefix
			// @since 2.0.3
			$prefix = (null == $config->db->prefix) ? self::DEFAULT_PREFIX : $config->db->prefix;
			
			$servers[$randomServer]['prefix'] = $prefix;
			
			$db = Zend_Db::factory($config->db->adapter, $servers[$randomServer]);
			/**
			 * We also can get the database prefix as follow:
			 * <code>
			 * $dbConfig = Tomato_Core_Db_Connection::getSlaveConnection()->getConfig();
			 * $prefix = $dbConfig['prefix'];
			 * </code>
			 */
			
			$db->setFetchMode(Zend_Db::FETCH_OBJ);
			$db->query("SET CHARACTER SET 'utf8'");
			
			Zend_Registry::set($key, $db);
		}
		return Zend_Registry::get($key);		
	}
}