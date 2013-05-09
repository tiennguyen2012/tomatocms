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
 * @version 	$Id: TargetController.php 1315 2010-02-24 10:22:00Z huuphuoc $
 */

class Core_TargetController extends Zend_Controller_Action 
{
	/* ========== Backend actions =========================================== */
	
	public function listAction() 
	{
		$modules = Tomato_Core_Utility_File::getSubDir(TOMATO_APP_DIR.DS.'modules');
		$targets = array();
		foreach ($modules as $module) {
			$info = Tomato_Core_Hook_Config::getTargetInfo($module);
			if ($info) {
				$targets[$module] = $info;
			}
		}
		$this->view->assign('targets', $targets);
		
		$conn = Tomato_Core_Db_Connection::getMasterConnection();
		
		// Get the list of hook modules
		$hookModules = array();
		$hooks = array();
		$select = $conn->select()
					->from(array('h' => Tomato_Core_Db_Connection::getDbPrefix().'core_hook'), array('module'))
					->distinct()
					->order('module');
		$rs = $select->query()->fetchAll();
		if ($rs) {
			foreach ($rs as $row) {
				$module = (null == $row->module || '' == $row->module) ? '_' : $row->module;
				$hooks[$module] = array();
				$hookModules[] = $module;
			}
		}
		$this->view->assign('hookModules', $hookModules);
		
		// Get the list of hooks
		$select = $conn->select()
					->from(array('h' => Tomato_Core_Db_Connection::getDbPrefix().'core_hook'))
					->order('h.name ASC');
		$rs = $select->query()->fetchAll();
		if ($rs) {
			foreach ($rs as $row) {
				if (null == $row->module || '' == $row->module) {
					$row->module = '_';
				}
				$hooks[$row->module][$row->name] = new Tomato_Modules_Core_Model_Hook($row);
			}
		}
		$this->view->assign('hooks', $hooks);
					
		// and tagets
		$dbTargets = array();
		$select = $conn->select()
						->from(array('t' => Tomato_Core_Db_Connection::getDbPrefix().'core_target'));
		$rs = $select->query()->fetchAll();
		if ($rs) {
			foreach ($rs as $row) {
				if (!isset($dbTargets[$row->target_name])) {
					$dbTargets[$row->target_name] = array();
				}
				$module = (null == $row->hook_module) ? '_' : $row->hook_module;
				$dbTargets[$row->target_name][$row->target_id.''] = $module.':'.$row->hook_name;
			}
		}
		$this->view->assign('dbTargets', $dbTargets);
	}
	
	public function removeAction() 
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		if ($this->_request->isPost()) {
			$id = $this->_request->getPost('id');
			
			$conn = Tomato_Core_Db_Connection::getMasterConnection();
			$gateway = new Tomato_Modules_Core_Model_TargetGateway();
			$gateway->setDbConnection($conn);
			$gateway->delete($id);
			
			$this->_response->setBody('RESULT_OK');
		}
	}
	
	public function addAction() 
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		if ($this->_request->isPost()) {
			$module = $this->_request->getPost('mod', '');
			if ('_' == $module) {
				$module = '';
			}
			
			$hookName = $this->_request->getPost('hook');
			$target = $this->_request->getPost('target');
			$target = Zend_Json::decode($target);
			$target['hook_name'] = $hookName;
			$target['hook_module'] = $module;

			$target = new Tomato_Modules_Core_Model_Target($target);
			$conn = Tomato_Core_Db_Connection::getMasterConnection();
			$gateway = new Tomato_Modules_Core_Model_TargetGateway();
			$gateway->setDbConnection($conn);
			$id = $gateway->add($target);
			
			if ($id > 0) {
				$this->_response->setBody($id);
			} else {
				$this->_response->setBody('RESULT_ERROR');
			}
		}
	}
}
