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
 * @version 	$Id: PrivilegeGateway.php 1669 2010-03-23 04:48:06Z huuphuoc $
 */

class Tomato_Modules_Core_Model_PrivilegeGateway extends Tomato_Core_Model_Gateway 
{
	/**
	 * (non-PHPdoc)
	 * @see app/core/model/Tomato_Core_Model_Gateway#convert($entity)
	 */	
	public function convert($entity) 
	{
		return new Tomato_Modules_Core_Model_Privilege($entity); 
	}
	
	/**
	 * @param int $id
	 * @return Tomato_Modules_Core_Model_Privilege
	 */
	public function getPrivilegeById($id) 
	{
		$select = $this->_conn
					->select()
					->from(array('p' => $this->_prefix.'core_privilege'))
					->where('p.privilege_id = ?', $id)
					->limit(1);
		$row = $select->query()->fetchAll();
		$privileges = new Tomato_Core_Model_RecordSet($row, $this);
		return (count($privileges) == 0) ? null : $privileges[0]; 
	}
	
	/**
	 * @param Tomato_Modules_Core_Model_Privilege $privilege
	 * @return int
	 */
	public function add($privilege) 
	{
		$this->_conn->insert($this->_prefix.'core_privilege', array(
			'name' => $privilege->name,
			'description' => $privilege->description,
			'module_name' => $privilege->module_name,
			'controller_name' => $privilege->controller_name,
		));
		return $this->_conn->lastInsertId($this->_prefix.'core_privilege');
	}
	
	/**
	 * @param int $id
	 * @return int
	 */
	public function delete($id) 
	{
		$where = array();
		$where[] = 'privilege_id = '.$this->_conn->quote($id);
		return $this->_conn->delete($this->_prefix.'core_privilege', $where);
	}
}
