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
 * @version 	$Id: RoleGateway.php 1669 2010-03-23 04:48:06Z huuphuoc $
 */

class Tomato_Modules_Core_Model_RoleGateway extends Tomato_Core_Model_Gateway 
{
	/**
	 * (non-PHPdoc)
	 * @see app/core/model/Tomato_Core_Model_Gateway#convert($entity)
	 */	
	public function convert($entity) 
	{
		return new Tomato_Modules_Core_Model_Role($entity); 
	}
	
	/**
	 * @param int $id
	 * @return Tomato_Modules_Core_Model_Role
	 */
	public function getRoleById($id) 
	{
		$select = $this->_conn
					->select()
					->from(array('r' => $this->_prefix.'core_role'))
					->where('r.role_id = ?', $id)
					->limit(1);
		$row = $select->query()->fetchAll();
		$roles = new Tomato_Core_Model_RecordSet($row, $this);
		return (count($roles) == 0) ? null : $roles[0]; 
	}
	
	/**
	 * @return Tomato_Core_Model_RecordSet
	 */
	public function getRoles() 
	{
		$select = $this->_conn
					->select()
					->from(array('r' => $this->_prefix.'core_role'));
		$rs = $select->query()->fetchAll();
		return new Tomato_Core_Model_RecordSet($rs, $this);
	}
	
	/**
	 * @param Tomato_Modules_Core_Model_Role $role
	 * @return int
	 */
	public function add($role) 
	{
		$this->_conn->insert($this->_prefix.'core_role', array(
			'name' => $role->name,
			'description' => $role->description,
			'locked' => $role->locked,
		));
		return $this->_conn->lastInsertId($this->_prefix.'core_role');
	}
	
	/**
	 * @param int $id
	 * @return int
	 */
	public function toggleLock($id) 
	{
		$sql = 'UPDATE '.$this->_prefix.'core_role SET locked = 1 - locked WHERE role_id = '.$this->_conn->quote($id);
		return $this->_conn->query($sql);
	}
	
	/**
	 * @param int $id
	 * @return int
	 */
	public function delete($id) 
	{
		$where = array();
		$where[] = 'role_id = '.$this->_conn->quote($id);
		return $this->_conn->delete($this->_prefix.'core_role', $where);	
	}
}
