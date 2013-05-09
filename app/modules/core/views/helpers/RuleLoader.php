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
 * @version 	$Id: RuleLoader.php 1535 2010-03-10 04:27:56Z huuphuoc $
 */

class Core_View_Helper_RuleLoader extends Zend_View_Helper_Abstract 
{
	public function ruleLoader() 
	{
		return $this;
	}
	
	public function getResources($module) 
	{
		$gateway = new Tomato_Modules_Core_Model_ResourceGateway();
		$conn = Tomato_Core_Db_Connection::getMasterConnection();
		$select = $conn->select()
						->from(array('r' => Tomato_Core_Db_Connection::getDbPrefix().'core_resource'))
						->where('r.module_name = ?', $module);
		$rs = $select->query()->fetchAll();
		$resources = new Tomato_Core_Model_RecordSet($rs, $gateway);
		return $resources;
	}
	
	public function getPrivilegesByRole($resource, $roleId) 
	{
		$module = $resource->module_name;
		$controller = $resource->controller_name;
		$gateway = new Tomato_Modules_Core_Model_PrivilegeGateway();
		$conn = Tomato_Core_Db_Connection::getMasterConnection();
		$select = $conn->select()
						->from(array('p' => Tomato_Core_Db_Connection::getDbPrefix().'core_privilege'), array('privilege_id', 'name', 'description'))
						->joinLeft(array('r' => Tomato_Core_Db_Connection::getDbPrefix().'core_rule'), 'r.obj_type = "role" AND r.obj_id = '.$conn->quote($roleId).' AND ((r.privilege_id IS NULL AND r.resource_name IS NULL) OR (r.privilege_id IS NULL AND (r.resource_name = '.$conn->quote($module.':'.$controller).')) OR ((r.resource_name = '.$conn->quote($module.':'.$controller).') AND (r.privilege_id = p.privilege_id)))', array('allow'))
						->where('p.module_name = ?', $module)
						->where('p.controller_name = ?', $controller);
		$rs = $select->query()->fetchAll();
		$privileges = new Tomato_Core_Model_RecordSet($rs, $gateway);
		return $privileges;
	}
	
	public function getPrivilegesByUser($resource, $userId) 
	{
		$module = $resource->module_name;
		$controller = $resource->controller_name;
		$gateway = new Tomato_Modules_Core_Model_PrivilegeGateway();
		$conn = Tomato_Core_Db_Connection::getMasterConnection();
		$select = $conn->select()
						->from(array('p' => Tomato_Core_Db_Connection::getDbPrefix().'core_privilege'), array('privilege_id', 'name', 'description'))
						->joinLeft(array('r' => Tomato_Core_Db_Connection::getDbPrefix().'core_rule'), 'r.obj_type = "user" AND r.obj_id = '.$conn->quote($userId).' AND ((r.privilege_id IS NULL AND r.resource_name IS NULL) OR (r.privilege_id IS NULL AND (r.resource_name = '.$conn->quote($module.':'.$controller).')) OR ((r.resource_name = '.$conn->quote($module.':'.$controller).') AND (r.privilege_id = p.privilege_id)))', array('allow'))
						->where('p.module_name = ?', $module)
						->where('p.controller_name = ?', $controller);
		$rs = $select->query()->fetchAll();
		$privileges = new Tomato_Core_Model_RecordSet($rs, $gateway);
		return $privileges;
	}
}
