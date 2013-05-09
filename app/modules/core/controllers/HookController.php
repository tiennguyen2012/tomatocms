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
 * @version 	$Id: HookController.php 1668 2010-03-22 06:24:11Z huuphuoc $
 */

class Core_HookController extends Zend_Controller_Action 
{
	/* ========== Backend actions =========================================== */
	
	public function listAction() 
	{
		$hooks = array(
			/**
			 * Store global hooks which can be used to apply for multiple modules
			 */
			'_' => array(),
		);
		
		// Get global hooks
		$subDirs = Tomato_Core_Utility_File::getSubDir(TOMATO_APP_DIR.DS.'hooks');
		foreach ($subDirs as $hookName) {
			$info = Tomato_Core_Hook_Config::getHookInfo($hookName);
			if (null == $info) {
				continue;
			}
			$hook = new Tomato_Modules_Core_Model_Hook($info);
			$hook->params = Tomato_Core_Hook_Config::getParams($hookName); 
			$hooks['_'][] = $hook;
		}
		
		// Get hooks from modules
		$modules = Tomato_Core_Utility_File::getSubDir(TOMATO_APP_DIR . DS . 'modules');
		foreach ($modules as $module) {
			$hooks[$module] = array();
			$subDirs = Tomato_Core_Utility_File::getSubDir(TOMATO_APP_DIR . DS . 'modules' . DS . $module . DS . 'hooks');
			foreach ($subDirs as $hookName) {
				$info = Tomato_Core_Hook_Config::getHookInfo($hookName, $module);
				if ($info != null) {
					$hook = new Tomato_Modules_Core_Model_Hook($info);
					$hook->params = Tomato_Core_Hook_Config::getParams($hookName, $module); 
					$hooks[$module][] = $hook;
				}
			}
		}
		
		$this->view->assign('hooks', $hooks);
		
		$conn = Tomato_Core_Db_Connection::getMasterConnection();
		$dbHooks = array();
		$select = $conn->select()
						->from(array('h' => Tomato_Core_Db_Connection::getDbPrefix().'core_hook'))
						->order('h.name ASC');
		$rs = $select->query()->fetchAll();
		if ($rs) {
			foreach ($rs as $row) {
				$key = ((null == $row->module || '' == $row->module) ? '_' : $row->module)
						. ':' . strtolower($row->name);
				$dbHooks[$key] = $key . ':' . $row->hook_id;
			}
		}
		$this->view->assign('dbHooks', $dbHooks);
	}
	
	public function installAction() 
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		if ($this->_request->isPost()) {
			$module = $this->_request->getPost('mod');
			$name = $this->_request->getPost('name');
			
			if ('_' == $module) {
				// We're going to install global hook
				$module = null;
			}
			
			$info = Tomato_Core_Hook_Config::getHookInfo($name, $module);
			if ($info) {
				$hook = new Tomato_Modules_Core_Model_Hook($info);
				
				$conn = Tomato_Core_Db_Connection::getMasterConnection();
				$gateway = new Tomato_Modules_Core_Model_HookGateway();
				$gateway->setDbConnection($conn);
				$id = $gateway->install($hook);
				
				$this->_response->setBody($module.':'.$name.':'.$id);
			} else {
				$this->_response->setBody('RESULT_ERROR');
			}
		}
	}

	public function uninstallAction() 
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		if ($this->_request->isPost()) {
			$module = $this->_request->getPost('mod');
			$name = $this->_request->getPost('name');
			$id = $this->_request->getPost('id');
			
			$hook = new Tomato_Modules_Core_Model_Hook(array(
				'hook_id' => $id,
				'module' => ('_' == $module) ? null : $module,
				'name' => $name,
			));
			
			$conn = Tomato_Core_Db_Connection::getMasterConnection();
			$gateway = new Tomato_Modules_Core_Model_HookGateway();
			$gateway->setDbConnection($conn);
			$gateway->uninstall($hook);
			
			$this->_response->setBody($module.':'.$name);
		}
	}
	
	public function configAction() 
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		if ($this->_request->isPost()) {
			$module = $this->_request->getPost('mod');
			
			if ('_' == $module) {
				// We are saving config for global hook
				$module = null;
			}
			
			$hook = $this->_request->getPost('name');
			$params = $this->_request->getPost('params');
			$params = Zend_Json::decode($params);
			Tomato_Core_Hook_Config::saveParams($params, $hook, $module);
			
			$this->_response->setBody('RESULT_OK');
		}
	}

	public function uploadAction() 
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		if ($this->_request->isPost()) {
			$file = $_FILES['file'];
			$prefix = 'hook_'.time();
			
			$zipFile = TOMATO_TEMP_DIR.DS.$prefix.$file['name'];
			move_uploaded_file($file['tmp_name'], $zipFile);
			
			// Process uploaded file
			$zip = Tomato_Core_Zip::factory($zipFile);
			$res = $zip->open();
			if ($res === true) {
				$tempDir = TOMATO_TEMP_DIR.DS.$prefix;
				if (!file_exists($tempDir)) {
					mkdir($tempDir);
				}
				$zip->extract($tempDir);
				
				// Get the first (and only) sub-forder
				$subDirs = Tomato_Core_Utility_File::getSubDir($tempDir);
				$xml = $tempDir.DS.$subDirs[0].DS.'about.xml';
				
				$info = Tomato_Core_Hook_Config::getHookInfoFromXml($xml);
				if ($info) {
					$hook = new Tomato_Modules_Core_Model_Hook($info);
					
					$conn = Tomato_Core_Db_Connection::getMasterConnection();
					$gateway = new Tomato_Modules_Core_Model_HookGateway();
					$gateway->setDbConnection($conn);
					
					// Check whether the hook was already installed
					if (!$gateway->exist($hook)) {
						$id = $gateway->add($hook);
						
						// Copy to the hooks directory
						$hookDir = TOMATO_APP_DIR.DS.'hooks'.DS.$hook->name;
						Tomato_Core_Utility_File::copyRescursiveDir($tempDir.DS.$subDirs[0], $hookDir);
					}
				} else {
					// TODO: Still add the hook information to database without its about file
				}
				
				// Remove all the temp files
				$zip->close();
				
				Tomato_Core_Utility_File::deleteRescursiveDir($tempDir);
				unlink($zipFile);
			}
			
			$this->_redirect($this->view->serverUrl().$this->view->url(array(), 'core_hook_list'));
		}
	}	
}
