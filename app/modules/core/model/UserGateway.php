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
 * @version 	$Id: UserGateway.php 1669 2010-03-23 04:48:06Z huuphuoc $
 */

class Tomato_Modules_Core_Model_UserGateway extends Tomato_Core_Model_Gateway 
{
	/**
	 * (non-PHPdoc)
	 * @see app/core/model/Tomato_Core_Model_Gateway#convert($entity)
	 */	
	public function convert($entity) 
	{
		return new Tomato_Modules_Core_Model_User($entity); 
	}
	
	/**
	 * @param int $id
	 * @return Tomato_Modules_Core_Model_User
	 */
	public function getUserById($id) 
	{
		$select = $this->_conn
					->select()
					->from(array('u' => $this->_prefix.'core_user'))
					->where('u.user_id = ?', $id)
					->limit(1);
		$row = $select->query()->fetchAll();
		$users = new Tomato_Core_Model_RecordSet($row, $this);
		return (count($users) == 0) ? null : $users[0]; 
	}
	
	/**
	 * @param int $id
	 * @return int
	 */
	public function toggleStatus($id) 
	{
		$sql = 'UPDATE '.$this->_prefix.'core_user SET is_active = 1 - is_active WHERE user_id = '.$this->_conn->quote($id);
		return $this->_conn->query($sql);
	}
	
	/**
	 * Add new user
	 * 
	 * @param Tomato_Modules_Core_Model_User $user
	 * @return int
	 */
	public function add($user) 
	{
		$this->_conn->insert($this->_prefix.'core_user', array(
			'user_name' => $user->user_name,
			'password' => md5($user->password),
			'full_name' => $user->full_name,
			'email' => $user->email,
			'is_active' => $user->is_active,
			'created_date' => $user->created_date,
			'logged_in_date' => $user->logged_in_date,
			'is_online' => $user->is_online,
			'role_id' => $user->role_id
		));
		return $this->_conn->lastInsertId($this->_prefix.'core_user');
	}
	
	/**
	 * Update user information
	 * 
	 * @param Tomato_Modules_Core_Model_User $user
	 * @return int
	 */
	public function update($user) 
	{
		$where[] = 'user_id = '.$this->_conn->quote($user->user_id);
		$data = array(
			'user_name' => $user->user_name,
			'full_name' => $user->full_name,
			'email' => $user->email,
			'role_id' => $user->role_id,
		);
		if (null != $user->password && $user->password != '') {
			$data['password'] = md5($user->password);
		} 
		return $this->_conn->update($this->_prefix.'core_user', $data, $where);
	}
	
	/**
	 * Update password
	 * 
	 * @param Tomato_Modules_Core_Model_User $user
	 * @return int
	 */
	public function updatePassword($user) 
	{
		$where[] = 'user_id = '.$this->_conn->quote($user->user_id);
		return $this->_conn->update($this->_prefix.'core_user', array(
				'password' => md5($user->password),
			), $where);
	}
}
