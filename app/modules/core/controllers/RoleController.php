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
 * @version 	$Id: RoleController.php 1545 2010-03-10 07:46:33Z huuphuoc $
 */

class Core_RoleController extends Zend_Controller_Action 
{
	/* ========== Backend actions =========================================== */
	
	public function listAction() 
	{
		$conn = Tomato_Core_Db_Connection::getMasterConnection();
		$sql = 'SELECT r.*, u2.num_users
				FROM '.Tomato_Core_Db_Connection::getDbPrefix().'core_role AS r
				LEFT JOIN
				(
					SELECT role_id, COUNT(*) AS num_users
					FROM '.Tomato_Core_Db_Connection::getDbPrefix().'core_user AS u
					WHERE role_id IN (SELECT role_id FROM '.Tomato_Core_Db_Connection::getDbPrefix().'core_role)
					GROUP BY role_id
				) AS u2 ON r.role_id = u2.role_id';
		$rs = $conn->query($sql)->fetchAll();
		$roles = new Tomato_Core_Model_RecordSet($rs, new Tomato_Modules_Core_Model_RoleGateway());
		$this->view->assign('roles', $roles);
	}
	
	public function addAction() 
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		if ($this->_request->isPost()) {
			$name = $this->_request->getPost('name');
			$description = $this->_request->getPost('description');
			$lock = $this->_request->getPost('lock');
			$lock = ($lock) ? 1 : 0;
			
			$conn = Tomato_Core_Db_Connection::getMasterConnection();
			$gateway = new Tomato_Modules_Core_Model_RoleGateway();	
			$gateway->setDbConnection($conn);
			$gateway->add(new Tomato_Modules_Core_Model_Role(
				array(
					'name' => $name,
					'description' => $description,
					'locked' => $lock,
				)
			));
			
			$this->_redirect($this->view->serverUrl().$this->view->url(array(), 'core_role_list'));
		}	
	}
	
	public function lockAction() 
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		if ($this->_request->isPost()) {
			$id = $this->_request->getPost('id');
			$conn = Tomato_Core_Db_Connection::getMasterConnection();
			$gateway = new Tomato_Modules_Core_Model_RoleGateway();	
			$gateway->setDbConnection($conn);
			$gateway->toggleLock($id);
			
			$lock = $this->_request->getPost('lock');
			$this->_response->setBody(1 - $lock);
		}
	}
	
	public function deleteAction() 
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		if ($this->_request->isPost()) {
			$id = $this->_request->getPost('id');
			
			// Count the user in this role
			$conn = Tomato_Core_Db_Connection::getMasterConnection();
			$select = $conn->select()
						->from(array('u' => Tomato_Core_Db_Connection::getDbPrefix().'core_user'), array('num_users' => 'COUNT(user_id)'))
						->where('u.role_id = ?', $id)
						->limit(1);
			$rs = $select->query()->fetchAll();
			$numUsers = (null == $rs || count($rs) == 0) ? 0 : $rs[0]->num_users;
			if ($numUsers == 0) {
				$gateway = new Tomato_Modules_Core_Model_RoleGateway();	
				$gateway->setDbConnection($conn);
				$gateway->delete($id);
				
				$this->_response->setBody('RESULT_OK');
			} else {
				$this->_response->setBody('RESULT_ERROR');
			}
		}
	}
}
