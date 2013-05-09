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
 * @version 	$Id: UserController.php 2021 2010-04-02 07:26:56Z hoangninh $
 */

class Core_UserController extends Zend_Controller_Action 
{
	/* ========== Backend actions =========================================== */
	
	public function listAction() 
	{
		$conn = Tomato_Core_Db_Connection::getMasterConnection();

		// Get roles
		$roleGateway = new Tomato_Modules_Core_Model_RoleGateway();
		$roleGateway->setDbConnection($conn);
		$roles = $roleGateway->getRoles();
		$this->view->assign('roles', $roles);
		
		// Get users
		$perPage = 15;
		
		$query = $this->_request->getParam('query', '');
		$username = $email = $role = $status = null;
		if ($query == '') {
			$params = array();
			$pageIndex = 1;
			$params['pageIndex'] = $pageIndex;
		} else {
			$params = Zend_Json::decode($query);
			$pageIndex = $params['pageIndex'];
			$username = isset($params['username']) ? $params['username'] : null;
			$email = isset($params['email']) ? $params['email'] : null;
			$role = isset($params['role']) ? $params['role'] : null;
			$status = isset($params['status']) ? $params['status'] : null; 
		}
		$start = ($pageIndex - 1) * $perPage;
		$select = $conn->select()
						->from(array('u' => Tomato_Core_Db_Connection::getDbPrefix().'core_user'));
		if ($username) {
			$select->where('u.user_name = ?', $username);
		}
		if ($email) {
			$select->where('u.email = ?', $email);
		}
		if ($role && $role != '') {
			$select->where('u.role_id = ?', $role);
		}
		if ($status == '0' || $status == 1) {
			$select->where('u.is_active = ?', $status);
		}
		$select->limit($perPage, $start);
		
		$rs = $select->query()->fetchAll();
		$gateway = new Tomato_Modules_Core_Model_UserGateway();
		$users = new Tomato_Core_Model_RecordSet($rs, $gateway);
		$this->view->assign('users', $users);

		// Count the number of users
		$select = $conn->select()
					->from(array('u' => Tomato_Core_Db_Connection::getDbPrefix().'core_user'), array('num_users' => 'COUNT(*)'));
		if ($username) {
			$select->where('u.user_name = ?', $username);
		}
		if ($email) {
			$select->where('u.email = ?', $email);
		}
		if ($role && $role != '') {
			$select->where('u.role_id = ?', $role);
		}
		if ($status && $status != '') {
			$select->where('u.is_active = ?', $status);
		}
		$select->limit(1);
		$numUsers = $select->query()->fetch()->num_users;
		
		// Paginator
		$paginator = new Zend_Paginator(new Tomato_Core_Utility_PaginatorAdapter($users, $numUsers));
		$paginator->setCurrentPageNumber($pageIndex);
		$paginator->setItemCountPerPage($perPage);
		$this->view->assign('paginator', $paginator);
		$this->view->assign('paginatorOptions', array(
			'path' => '',
			'itemLink' => 'javascript: filterUsers(%d, '.Zend_Json::encode($params).');',
		));
		
		if ($query != '') {
			$this->_helper->getHelper('viewRenderer')->setNoRender();
			$this->_helper->getHelper('layout')->disableLayout();
			
			$content = $this->view->render('user/_filter.phtml');
			$this->_response->setBody($content); 
		}
	}

	public function activateAction() 
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		if ($this->_request->isPost()) {
			$id = $this->_request->getPost('id');
			$conn = Tomato_Core_Db_Connection::getMasterConnection();
			$gateway = new Tomato_Modules_Core_Model_UserGateway();	
			$gateway->setDbConnection($conn);
			$gateway->toggleStatus($id);
			
			$status = $this->_request->getPost('status');
			$this->_response->setBody(1 - $status);
		}
	}
	
	public function addAction() 
	{
		$conn = Tomato_Core_Db_Connection::getMasterConnection();
		$select = $conn->select()
						->from(array('r' => Tomato_Core_Db_Connection::getDbPrefix().'core_role'));
		$rs = $select->query()->fetchAll(); 				
		$roleGateway = new Tomato_Modules_Core_Model_RoleGateway();
		$roles = new Tomato_Core_Model_RecordSet($rs, $roleGateway);
		
		$this->view->assign('roles', $roles);
		
		if ($this->_request->isPost()) {
			$fullname = $this->_request->getPost('full_name');
			$username = $this->_request->getPost('username');
			$password = $this->_request->getPost('password');
			$password2 = $this->_request->getPost('password2');
			$email = $this->_request->getPost('email');
			$roleId = $this->_request->getPost('role');
			
			$user = new Tomato_Modules_Core_Model_User(array(
				'user_name' => $username,
				'password' => $password,
				'full_name' => $fullname,
				'email' => $email,
				'is_active' => 0,
				'created_date' => date('Y-m-d H:i:s'),
				'logged_in_date' => null,
				'is_online' => 0,
				'role_id' => $roleId,
			));
			$userGateway = new Tomato_Modules_Core_Model_UserGateway();
			$userGateway->setDbConnection($conn);
			$id = $userGateway->add($user);
			if ($id > 0) {
				$this->_helper->getHelper('FlashMessenger')->addMessage(
					$this->view->translator('user_add_success')
				);
				$this->_redirect($this->view->serverUrl().$this->view->url(array(), 'core_user_add'));
			}
		}
	}
	
	public function checkAction() 
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		$checkType = $this->_request->getParam('check_type');
		
		$conn = Tomato_Core_Db_Connection::getMasterConnection();
		$select = $conn->select()
					->from(array('u' => Tomato_Core_Db_Connection::getDbPrefix().'core_user'), array('num_users' => 'COUNT(*)'));
					
		$original = $this->_request->getParam('original');
		$result = false;			
		switch ($checkType) {
			case 'username':
				$username = $this->_request->getParam('username');
				if ($original == null || ($original != null && $username != $original)) {
					$select->where('u.user_name = ?', $username)
						   ->limit(1);
					$rs = $select->query()->fetch();
					$numUsers = $rs->num_users;
					($numUsers == 0) ? $result = false : $result = true;
				}
				break;
			case 'email':
				$email = $this->_request->getParam('email');
				if ($original == null || ($original != null && $email != $original)) {
					$select->where('u.email = ?', $email)
						   ->limit(1);
					$rs = $select->query()->fetch();
					$numUsers = $rs->num_users;
					($numUsers == 0) ? $result = false : $result = true;
				}
				break;
		}			
		($result == true) ? $this->getResponse()->setBody('false') 
						: $this->getResponse()->setBody('true');
	}
	
	public function editAction() 
	{
		$conn = Tomato_Core_Db_Connection::getMasterConnection();
		$userGateway = new Tomato_Modules_Core_Model_UserGateway();
		$userGateway->setDbConnection($conn);
		
		$select = $conn->select()
						->from(array('r' => Tomato_Core_Db_Connection::getDbPrefix().'core_role'));
		$rs = $select->query()->fetchAll(); 				
		$roleGateway = new Tomato_Modules_Core_Model_RoleGateway();
		$roles = new Tomato_Core_Model_RecordSet($rs, $roleGateway);
		$this->view->assign('roles', $roles);
				
		$userId = $this->_request->getParam('user_id');
		$user = $userGateway->getUserById($userId);
		$this->view->assign('user', $user);
		
		if ($this->_request->isPost()) {
			$fullname = $this->_request->getPost('fullname');
			$username = $this->_request->getPost('username');
			$password = $this->_request->getPost('confirmPassword');
			$email = $this->_request->getPost('email');
			$roleId = $this->_request->getPost('role');
			
			$user = new Tomato_Modules_Core_Model_User(array(
				'user_id' => $userId,
				'user_name' => $username,
				'password' => $password,
				'full_name' => $fullname,
				'email' => $email,
				'role_id' => $roleId,
			));
			$result = $userGateway->update($user);
			if ($result > 0) {
				$this->_helper->getHelper('FlashMessenger')->addMessage(
					$this->view->translator('user_edit_success')
				);
				$this->_redirect($this->view->serverUrl().$this->view->url(array('user_id' => $userId), 'core_user_edit'));
			}
		}
	}
	
	public function changepassAction() 
	{
		$conn = Tomato_Core_Db_Connection::getMasterConnection();
		$userGateway = new Tomato_Modules_Core_Model_UserGateway();
		$userGateway->setDbConnection($conn);
				
		$user = Zend_Auth::getInstance()->getIdentity();
		
		if ($this->_request->isPost()) {
			$password = $this->_request->getPost('password');
			$user = new Tomato_Modules_Core_Model_User(array(
				'user_id' => $user->user_id,
				'password' => $password,
			));
			$result = $userGateway->updatePassword($user);
			if ($result > 0) {
				$this->_helper->getHelper('FlashMessenger')->addMessage(
					$this->view->translator('user_changepass_update_success')
				);
				$this->_redirect($this->view->serverUrl().$this->view->url(array(), 'core_user_changepass'));
			}
		}
	}
}
