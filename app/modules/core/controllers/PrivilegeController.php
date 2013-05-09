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
 * @version 	$Id: PrivilegeController.php 1306 2010-02-24 08:39:21Z huuphuoc $
 */

class Core_PrivilegeController extends Zend_Controller_Action 
{
	/* ========== Backend actions =========================================== */

	public function listAction() 
	{
		$modules = Tomato_Core_Utility_File::getSubDir(TOMATO_APP_DIR.DS.'modules');
		$this->view->assign('modules', $modules);
		
		$conn = Tomato_Core_Db_Connection::getMasterConnection();
		// Get resources
		$select = $conn->select()
						->from(array('r' => Tomato_Core_Db_Connection::getDbPrefix().'core_resource'), array('resource_id', 'description', 'module_name', 'controller_name'));
		$rs = $select->query()->fetchAll();
		$dbResources = array();
		if ($rs) {
			foreach ($rs as $row) {
				$dbResources[$row->module_name.':'.$row->controller_name] = $row->resource_id;
			}
		}
		$this->view->assign('dbResources', $dbResources);
		
		// Get privileges
		$select = $conn->select()
						->from(array('p' => Tomato_Core_Db_Connection::getDbPrefix().'core_privilege'), array('privilege_id', 'name', 'description', 'module_name', 'controller_name'));
		$rs = $select->query()->fetchAll();
		$dbPrivileges = array();
		if ($rs) {
			foreach ($rs as $row) {
				$dbPrivileges[$row->module_name.':'.$row->controller_name.':'.$row->name] = $row->privilege_id;
			}
		}
		$this->view->assign('dbPrivileges', $dbPrivileges);				
	}
	
	public function addAction() 
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		if ($this->_request->isPost()) {
			$mca = $this->_request->getPost('mca');
			$description = $this->_request->getPost('description');
			list($module, $controller, $action) = explode(':', $mca);
			
			$privilege = new Tomato_Modules_Core_Model_Privilege(array(
						'name' => $action,
						'description' => $description,
						'module_name' => $module,
						'controller_name' => $controller,
					));
			$conn = Tomato_Core_Db_Connection::getMasterConnection();
			$privilegeGateway = new Tomato_Modules_Core_Model_PrivilegeGateway();
			$privilegeGateway->setDbConnection($conn);
			$id = $privilegeGateway->add($privilege);
			
			$this->_response->setBody($id);
		}
	}
	
	public function deleteAction() 
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		if ($this->_request->isPost()) {
			$id = $this->_request->getPost('id');
			
			$conn = Tomato_Core_Db_Connection::getMasterConnection();
			$privilegeGateway = new Tomato_Modules_Core_Model_PrivilegeGateway();
			$privilegeGateway->setDbConnection($conn);
			$privilege = $privilegeGateway->getPrivilegeById($id);
			
			$data = array(
				'mca' => implode(array($privilege->module_name, $privilege->controller_name, $privilege->name), ':'), 
				'description' => $privilege->description,
			);
			$privilegeGateway->delete($id);
			
			// Remove from rule
			$where = array();
			$where[] = 'privilege_id = '.$conn->quote($id);
			$conn->delete(Tomato_Core_Db_Connection::getDbPrefix().'core_rule', $where);
			
			$this->_response->setBody(Zend_Json::encode($data));
		}
	}
}
