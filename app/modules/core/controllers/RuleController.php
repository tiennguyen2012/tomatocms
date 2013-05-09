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
 * @version 	$Id: RuleController.php 1545 2010-03-10 07:46:33Z huuphuoc $
 */

class Core_RuleController extends Zend_Controller_Action 
{
	/* ========== Backend actions =========================================== */
	
	public function roleAction() 
	{
		$modules = Tomato_Core_Utility_File::getSubDir(TOMATO_APP_DIR.DS.'modules');
		$this->view->assign('modules', $modules);

		$roleId = $this->_request->getParam('role_id');
		$conn = Tomato_Core_Db_Connection::getMasterConnection();
		$roleGateway = new Tomato_Modules_Core_Model_RoleGateway();
		$roleGateway->setDbConnection($conn);
		$role = $roleGateway->getRoleById($roleId);
		$this->view->assign('role', $role);
		if ($role->locked) {
			return;
		} 
		
		if ($this->_request->isPost()) {
			// Reset all the rules
			$where = array();
			$where[] = 'obj_id = '.$conn->quote($roleId);
			$where[] = 'obj_type = '.$conn->quote('role');
			$conn->delete(Tomato_Core_Db_Connection::getDbPrefix().'core_rule', $where);

			// Update new rule
			$privileges = $this->_request->getPost('privileges');
			if ($privileges) {
				foreach ($privileges as $priv) {
					list($privId, $resourceName) = explode('_', $priv);
					
					$conn->insert(Tomato_Core_Db_Connection::getDbPrefix().'core_rule', array(
						'obj_id' => $roleId,
						'obj_type' => 'role',
						'privilege_id' => $privId,
						'allow' => 1,
						'resource_name' => $resourceName, 
					));
				}
			}
			$this->_redirect($this->view->serverUrl().$this->view->url(array('role_id' => $roleId), 'core_rule_set_role'));
		}
	}
	
	public function userAction() 
	{
		$modules = Tomato_Core_Utility_File::getSubDir(TOMATO_APP_DIR.DS.'modules');
		$this->view->assign('modules', $modules);

		$userId = $this->_request->getParam('user_id');
		$conn = Tomato_Core_Db_Connection::getMasterConnection();
		$userGateway = new Tomato_Modules_Core_Model_UserGateway();
		$userGateway->setDbConnection($conn);
		$user = $userGateway->getUserById($userId);
		$this->view->assign('user', $user);
		
		if (!$user->is_active) {
			return;
		} 
		
		if ($this->_request->isPost()) {
			// Reset all the rules
			$where = array();
			$where[] = 'obj_id = '.$conn->quote($userId);
			$where[] = 'obj_type = '.$conn->quote('user');
			$conn->delete(Tomato_Core_Db_Connection::getDbPrefix().'core_rule', $where);

			// Update new rule
			$privileges = $this->_request->getPost('privileges');
			if ($privileges) {
				foreach ($privileges as $priv) {
					list($privId, $resourceName) = explode('_', $priv);
					
					$conn->insert(Tomato_Core_Db_Connection::getDbPrefix().'core_rule', array(
						'obj_id' => $userId,
						'obj_type' => 'user',
						'privilege_id' => $privId,
						'allow' => 1,
						'resource_name' => $resourceName, 
					));
				}
			}
			$this->_redirect($this->view->serverUrl().$this->view->url(array('user_id' => $userId), 'core_rule_set_user'));
		}
	}
}
