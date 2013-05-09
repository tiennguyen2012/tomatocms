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
 * @version 	$Id: Auth.php 1306 2010-02-24 08:39:21Z huuphuoc $
 */

class Tomato_Modules_Core_Services_Auth implements Zend_Auth_Adapter_Interface 
{
	/**
	 * Authenticated success
	 * Its value must be greater than 0
	 */
	const SUCCESS = 1;
	
	/**
	 * Constant define that user has not been active
	 * Its value must be smaller than 0
	 */
	const NOT_ACTIVE = -1;
	
	/**
	 * General failure
	 * Its value must be smaller than 0
	 */
	const FAILURE = -2;
	
	private $_username;
	private $_password;
	
	public function __construct($username, $password) 
	{
		$this->_username = $username;
		$this->_password = $password;
	}

	/**
     * Performs an authentication attempt
     *
     * @throws Zend_Auth_Adapter_Exception If authentication cannot be performed
     * @return Zend_Auth_Result
     */
    public function authenticate() 
    {    	
		$password = md5($this->_password);
		
		$conn = Tomato_Core_Db_Connection::getSlaveConnection();
		$gateway = new Tomato_Modules_Core_Model_UserGateway();
		$select = $conn->select()
						->from(array('u' => Tomato_Core_Db_Connection::getDbPrefix().'core_user'))
						->joinLeft(array('r' => Tomato_Core_Db_Connection::getDbPrefix().'core_role'), 'u.role_id = r.role_id', array('role_name' => 'name'))
						->where('u.user_name = ?', $this->_username)
						->where('u.password = ?', $password)
						->limit(1);
    	$rs = $select->query()->fetchAll();
    	$users = new Tomato_Core_Model_RecordSet($rs, $gateway);
		if (count($users) == 0) {
			return new Zend_Auth_Result(self::FAILURE, null);
		}
		$user = $users[0];
    	if (!$user->is_active) {
    		return new Zend_Auth_Result(self::NOT_ACTIVE, null);
    	}
    	return new Zend_Auth_Result(self::SUCCESS, $user);
    }		
}
