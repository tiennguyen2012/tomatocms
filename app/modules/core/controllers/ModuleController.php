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
 * @version 	$Id: ModuleController.php 1668 2010-03-22 06:24:11Z huuphuoc $
 */

class Core_ModuleController extends Zend_Controller_Action 
{
	/* ========== Backend actions =========================================== */
	
	public function listAction() 
	{
		$modules = array();
		$subDirs = Tomato_Core_Utility_File::getSubDir(TOMATO_APP_DIR.DS.'modules');
		foreach ($subDirs as $dir) {
			if ($dir == 'core') {
				continue;
			}
			$file = TOMATO_APP_DIR.DS.'modules'.DS.$dir.DS.'config'.DS.'about.xml';
			if (!file_exists($file)) {
				continue;
			}
			$xml = simplexml_load_file($file);
			$attr = $xml->description->attributes();
			$langKey = (string) $attr['langKey'];
			
			$description = $this->view->translator($langKey, $dir);
			$description = ($description == $langKey) ? (string)$xml->description : $description;
			
			$info = array(
				'name' => strtolower($xml->name),
				'description' => $description,
				'thumbnail' => $xml->thumbnail,
				'author' => $xml->author,
				'email' => $xml->email,
				'version' => $xml->version,
				'license' => $xml->license,
			);
			$info['required'] = array(
				'modules' => array(),
				'libs' => array(),
				'other' => null,
			);
			if ($xml->requires) {
				if ($xml->requires->requiredModules) {
					foreach ($xml->requires->requiredModules->requiredModule as $mod) {
						$attrs = $mod->attributes();
						$info['required']['modules'][] = (string) $attrs['name'];
					}
				}
				if ($xml->requires->libs) {
					foreach ($xml->requires->libs->lib as $lib) {
						$info['required']['libs'][] = array(
							'type' => $lib->type,
							'name' => $lib->name,
							'link' => $lib->link,
							'description' => $lib->description,
						);
					}
				}
				if ($xml->requires->other) {
					$info['required']['other'] = $xml->requires->other;
				}
			}
			$modules[] = $info;
		}
		$this->view->assign('modules', $modules);
		
		// Get the list of modules from database
		$conn = Tomato_Core_Db_Connection::getMasterConnection();
		$dbModules = array();
		$select = $conn->select()
						->from(array('m' => Tomato_Core_Db_Connection::getDbPrefix().'core_module'))
						->order('m.name ASC');
		$rs = $select->query()->fetchAll();
		if ($rs) {
			foreach ($rs as $row) {
				$key = $row->name; 
				$dbModules[$key] = $key.':'.$row->module_id;
			}
		}
		$this->view->assign('dbModules', $dbModules);
	}
	
	public function installAction() 
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		if ($this->_request->isPost()) {
			$name = $this->_request->getPost('name');
			$name = strtolower($name);
			if ($name == 'core') {
				$this->_response->setBody('RESULT_ERROR');
			} else {
				$conn = Tomato_Core_Db_Connection::getMasterConnection();
				$gateway = new Tomato_Modules_Core_Model_ModuleGateway();
				$gateway->setDbConnection($conn);
	
				$service = new Tomato_Modules_Core_Services_Install_Module();
				$service->setModuleGateway($gateway);
				
				$moduleObj = $service->install($name);
				$gateway->add($moduleObj);
				
				$this->_response->setBody('RESULT_OK');
			}
		}
	}
	
	public function uninstallAction() 
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		if ($this->_request->isPost()) {
			$name = $this->_request->getPost('name');
			$name = strtolower($name);
			if ($name == 'core') {
				$this->_response->setBody('RESULT_ERROR');
			} else {
				$conn = Tomato_Core_Db_Connection::getMasterConnection();
				$gateway = new Tomato_Modules_Core_Model_ModuleGateway();
				$gateway->setDbConnection($conn);
	
				$service = new Tomato_Modules_Core_Services_Install_Module();
				$service->setModuleGateway($gateway);
				
				$service->uninstall($name);
				
				$this->_response->setBody('RESULT_OK');
			}
		}
	}
	
	public function uploadAction() 
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		if ($this->_request->isPost()) {
			$file = $_FILES['file'];
			$prefix = 'module_'.time();
			
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
				$moduleName = $subDirs[0];
				
				// Check if the module does exist or not 
				if (!file_exists(TOMATO_APP_DIR.DS.'modules'.DS.$moduleName)) {
					// Copy to the plugins directory
					Tomato_Core_Utility_File::copyRescursiveDir($tempDir.DS.$moduleName, TOMATO_APP_DIR.DS.'modules'.DS.$moduleName);
				}
				
				// Remove all the temp files
				$zip->close();
				
				Tomato_Core_Utility_File::deleteRescursiveDir($tempDir);
				unlink($zipFile);
			}
			
			$this->_redirect($this->view->serverUrl().$this->view->url(array(), 'core_module_list'));
		}
	}
}
