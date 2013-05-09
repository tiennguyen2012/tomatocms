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
 * @version 	$Id: ResourceController.php 1306 2010-02-24 08:39:21Z huuphuoc $
 */

class Core_ResourceController extends Zend_Controller_Action 
{
	/* ========== Backend actions =========================================== */

	public function addAction() 
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		if ($this->_request->isPost()) {
			$mc = $this->_request->getPost('mc');
			$description = $this->_request->getPost('description');
			list($module, $controller) = explode(':', $mc);
			
			$resource = new Tomato_Modules_Core_Model_Resource(array(
						'description' => $description,
						'module_name' => $module,
						'controller_name' => $controller,
					));
			$conn = Tomato_Core_Db_Connection::getMasterConnection();
			$resourceGateway = new Tomato_Modules_Core_Model_ResourceGateway();
			$resourceGateway->setDbConnection($conn);
			$id = $resourceGateway->add($resource);
			
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
			$resourceGateway = new Tomato_Modules_Core_Model_ResourceGateway();
			$resourceGateway->setDbConnection($conn);
			$resource = $resourceGateway->getResourceById($id);
			
			$resourceName = implode(array($resource->module_name, $resource->controller_name), ':');
			$data = array(
				'mc' => $resourceName, 
				'description' => $resource->description,
			);
			$resourceGateway->delete($id);
			
			// Remove from rule
			$where = array();
			$where[] = 'resource_name = '.$conn->quote($resourceName);
			$conn->delete(Tomato_Core_Db_Connection::getDbPrefix().'core_rule', $where);
			
			$this->_response->setBody(Zend_Json::encode($data));
		}
	}
}
