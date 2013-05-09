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
 * @version 	$Id: PluginController.php 1668 2010-03-22 06:24:11Z huuphuoc $
 */

class Core_PluginController extends Zend_Controller_Action 
{
	/* ========== Backend actions =========================================== */

	public function listAction() 
	{
		$plugins = array();
		$subDirs = Tomato_Core_Utility_File::getSubDir(TOMATO_APP_DIR.DS.'plugins');
		foreach ($subDirs as $pluginName) {
			$info = Tomato_Core_Plugin_Config::getPluginInfo($pluginName);
			if (null == $info) {
				continue;
			}
			$plugin = new Tomato_Modules_Core_Model_Plugin($info);
			$plugin->params = Tomato_Core_Plugin_Config::getParams($pluginName); 
			$plugins[] = $plugin;
		}
		$this->view->assign('plugins', $plugins);
		
		// Get the list of plugins from database
		$conn = Tomato_Core_Db_Connection::getMasterConnection();
		$dbPlugins = array();
		$select = $conn->select()
						->from(array('p' => Tomato_Core_Db_Connection::getDbPrefix().'core_plugin'))
						->order('p.name ASC');
		$rs = $select->query()->fetchAll();
		if ($rs) {
			foreach ($rs as $row) {
				$key = strtolower($row->name); 
				$dbPlugins[$key] = $key.':'.$row->plugin_id;
			}
		}
		$this->view->assign('dbPlugins', $dbPlugins);
	}
	
	public function installAction() 
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		if ($this->_request->isPost()) {
			$name = $this->_request->getPost('name');
			
			$info = Tomato_Core_Plugin_Config::getPluginInfo($name);
			if ($info) {
				$conn = Tomato_Core_Db_Connection::getMasterConnection();
				$gateway = new Tomato_Modules_Core_Model_PluginGateway();
				$gateway->setDbConnection($conn);
				$id = $gateway->add(new Tomato_Modules_Core_Model_Plugin($info));
				
				// Perform the action when plugin is activated
				$pluginClass = 'Tomato_Plugins_'.$name.'_Plugin';
				if (class_exists($pluginClass)) {
					$plugin = new $pluginClass();
					$plugin->activate();
				}
				
				$this->_response->setBody($name.':'.$id);
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
			$name = $this->_request->getPost('name');
			$id = $this->_request->getPost('id');
			
			$conn = Tomato_Core_Db_Connection::getMasterConnection();
			$gateway = new Tomato_Modules_Core_Model_PluginGateway();
			$gateway->setDbConnection($conn);
			$gateway->delete($id);
			
			// Perform the action when plugin is deactivated
			$pluginClass = 'Tomato_Plugins_'.$name.'_Plugin';
			if (class_exists($pluginClass)) {
				$plugin = new $pluginClass();
				$plugin->deactivate();
			}
			
			$this->_response->setBody($name);
		}
	}
	
	public function configAction() 
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		if ($this->_request->isPost()) {
			$plugin = $this->_request->getPost('plugin');
			$params = $this->_request->getPost('params');
			$params = Zend_Json::decode($params);
			Tomato_Core_Plugin_Config::saveParams($plugin, $params);
			
			$this->_response->setBody('RESULT_OK');
		}
	}
	
	public function uploadAction() 
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		if ($this->_request->isPost()) {
			$file = $_FILES['file'];
			$prefix = 'plugin_'.time();
			
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
				
				$info = Tomato_Core_Plugin_Config::getPluginInfoFromXml($xml);
				if ($info) {
					$plugin = new Tomato_Modules_Core_Model_Plugin($info);
					
					// TODO: Check whether the plugin was already installed					
					$conn = Tomato_Core_Db_Connection::getMasterConnection();
					$gateway = new Tomato_Modules_Core_Model_PluginGateway();
					$gateway->setDbConnection($conn);
					$id = $gateway->add($plugin);
					
					// Copy to the plugins directory
					$pluginDir = TOMATO_APP_DIR.DS.'plugins'.DS.$plugin->name;
					Tomato_Core_Utility_File::copyRescursiveDir($tempDir.DS.$subDirs[0], $pluginDir);
				} else {
					// TODO: Still add the plugin information to database without its about file
				}
				
				// Remove all the temp files
				$zip->close();
				
				Tomato_Core_Utility_File::deleteRescursiveDir($tempDir);
				unlink($zipFile);
			}
			
			$this->_redirect($this->view->serverUrl().$this->view->url(array(), 'core_plugin_list'));
		}
	}
}
