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
 * @version 	$Id: ResourceGateway.php 1669 2010-03-23 04:48:06Z huuphuoc $
 */

class Tomato_Modules_Core_Model_ResourceGateway extends Tomato_Core_Model_Gateway 
{
	/**
	 * (non-PHPdoc)
	 * @see app/core/model/Tomato_Core_Model_Gateway#convert($entity)
	 */	
	public function convert($entity) 
	{
		return new Tomato_Modules_Core_Model_Resource($entity); 
	}
	
	/**
	 * @param int $id
	 * @return Tomato_Modules_Core_Model_Resource
	 */
	public function getResourceById($id) 
	{
		$select = $this->_conn
					->select()
					->from(array('r' => $this->_prefix.'core_resource'))
					->where('r.resource_id = ?', $id)
					->limit(1);
		$row = $select->query()->fetchAll();
		$resources = new Tomato_Core_Model_RecordSet($row, $this);
		return (count($resources) == 0) ? null : $resources[0]; 
	}
	
	/**
	 * @param Tomato_Modules_Core_Model_Resource $resource
	 * @return int
	 */
	public function add($resource) 
	{
		$this->_conn->insert($this->_prefix.'core_resource', array(
			'description' => $resource->description,
			'parent_id' => $resource->parent_id,
			'module_name' => $resource->module_name,
			'controller_name' => $resource->controller_name,
		));
		return $this->_conn->lastInsertId($this->_prefix.'core_resource');
	}
	
	/**
	 * @param int $id
	 * @return int
	 */
	public function delete($id) 
	{
		$where = array();
		$where[] = 'resource_id = '.$this->_conn->quote($id);
		return $this->_conn->delete($this->_prefix.'core_resource', $where);
	}
}
