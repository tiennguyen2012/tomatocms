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
 * @version 	$Id: SessionHandler.php 1557 2010-03-10 10:54:24Z huuphuoc $
 */

class Tomato_Modules_Core_Services_SessionHandler implements Zend_Session_SaveHandler_Interface 
{
	/**
	 * @var Tomato_Modules_Core_Services_SessionHandler
	 */
	private static $_instance;
	
	private $_lifetime;
	
	private function __construct()
	{
		$config = Tomato_Core_Config::getConfig();
		$this->_lifetime = (isset($config->web->session_lifetime))
							? $config->web->session_lifetime
							: (int) ini_get('session.gc_maxlifetime');
	}
	
	/**
	 * @return Tomato_Modules_Core_Services_SessionHandler
	 */
	public static function getInstance() 
	{
		if (null == self::$_instance) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	public function close() 
	{
		return true;
	}
	
	public function destroy($id) 
	{
		$conn = Tomato_Core_Db_Connection::getMasterConnection();
		$where[] = 'session_id = ' . $conn->quote($id);
		return $conn->delete(Tomato_Core_Db_Connection::getDbPrefix().'core_session', $where);
	}
	
	public function gc($maxlifetime) 
	{
		$conn = Tomato_Core_Db_Connection::getMasterConnection();
		$where[] = 'modified + lifetime < ' . $conn->quote(time());
		$conn->delete(Tomato_Core_Db_Connection::getDbPrefix().'core_session', $where);
		return true;
	}
	
	public function open($save_path, $name) 
	{
		return true;	
	}
	
	public function read($id) 
	{
		$return = '';
		$conn = Tomato_Core_Db_Connection::getMasterConnection();
		$select = $conn->select()
						->from(array('s' => Tomato_Core_Db_Connection::getDbPrefix().'core_session'))
						->where('s.session_id = ?', $id)
						->limit(1);
		$row = $select->query()->fetch();
		if ($row != null) {
			$expirationTime = (int) $row->modified + $row->lifetime;
			if ($expirationTime > time()) {
				$return = $row->data;
			} else {
				$this->destroy($id);
			}
		}
		return $return;
	}
	
	public function write($id, $data) 
	{
		$conn = Tomato_Core_Db_Connection::getMasterConnection();
		
		$row = $conn->select()
					->from(array('s' => Tomato_Core_Db_Connection::getDbPrefix().'core_session'))
					->where('s.session_id = ?', $id)
					->limit(1)
					->query()
					->fetch();
		if (null == $row) {
			return $conn->insert(Tomato_Core_Db_Connection::getDbPrefix().'core_session', array(
				'session_id' => $id,
				'data' => $data,
				'modified' => time(),
				'lifetime' => $this->_lifetime,
			));
		} else {
			$where = array('session_id = '.$conn->quote($id));
			return $conn->update(Tomato_Core_Db_Connection::getDbPrefix().'core_session', array(
				'data' => $data,
				'modified' => time(),
				'lifetime' => $row->lifetime,
			), $where);
		}
	}
}
