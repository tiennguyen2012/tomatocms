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
 * @version 	$Id: LanguageController.php 1878 2010-03-31 02:52:46Z huuphuoc $
 */

class Core_LanguageController extends Zend_Controller_Action 
{
	/* ========== Backend actions =========================================== */
	
	public function listAction() 
	{
		$modules = Tomato_Core_Utility_File::getSubDir(TOMATO_APP_DIR.DS.'modules');
		$this->view->assign('modules', $modules);
		
		$widgets = array();
		foreach ($modules as $module) {
			$widgets[$module] = Tomato_Core_Utility_File::getSubDir(TOMATO_APP_DIR.DS.'modules'.DS.$module.DS.'widgets');
		}
		$this->view->assign('widgets', Zend_Json::encode($widgets));
	}
	
	public function newAction() 
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		if ($this->_request->isPost()) {
			$module = $this->_request->getPost('module_name');
			$widget = $this->_request->getPost('widget');
			$language = $this->_request->getPost('language');
			
			$file = TOMATO_APP_DIR.DS.'modules'.DS.$module;
			$file = ($widget) 
						? $file.DS.'widgets'.DS.$widget.DS.'lang.'.$language.'.ini'
						: $file.DS.'languages'.DS.'lang.'.$language.'.ini';
			if (!file_exists($file)) {
				// Create new file
				$f = fopen($file, 'w');
				fclose($f);
			}
			$this->_redirect($this->view->serverUrl().$this->view->url(array(), 'core_language_list'));
		}
	}
	
	public function editAction() 
	{
		$module = $this->_request->getParam('module_name');
		$language = $this->_request->getParam('language');
		$widget = $this->_request->getParam('widget', '');
		
		$this->view->assign('moduleName', $module);
		$this->view->assign('language', $language);
		if ($widget != '') {
			$this->view->assign('widget', $widget);	
		}
		
		$file = TOMATO_APP_DIR.DS.'modules'.DS.$module;
		$file = ($widget != '')
					? $file.DS.'widgets'.DS.$widget.DS.'lang.'.$language.'.ini'
					: $file.DS.'languages'.DS.'lang.'.$language.'.ini';
		if (!file_exists($file)) {
			return;
		}
		$config = new Zend_Config_Ini($file, null, array('allowModifications' => true));
		$config = $config->toArray();
		$this->view->assign('data', $config);
		$this->view->assign('sections', array_keys($config));
//		$writer = new Zend_Config_Writer_Ini();
//		$writer->write($file, $config);
	}
	
	public function updateAction() 
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		if ($this->_request->isPost()) {
			$keySection = $this->_request->getPost('keySection');
			$newValue = $this->_request->getPost('value');
			
			$keySection = substr($keySection, strlen('_valueFor_'));
			
			// Update language file
			if ($newValue != '') {
				$module = $this->_request->getPost('module_name');
				$widget = $this->_request->getPost('widget');
				$language = $this->_request->getPost('language');
				
				$file = TOMATO_APP_DIR.DS.'modules'.DS.$module;
				$file = ($widget)
					? $file.DS.'widgets'.DS.$widget.DS.'lang.'.$language.'.ini'
					: $file.DS.'languages'.DS.'lang.'.$language.'.ini';
				
				$config = new Zend_Config_Ini($file, null, array('allowModifications' => true));
				
				list($section, $key) = explode('___', $keySection);
				$config->$section->$key = $newValue;
				$writer = new Zend_Config_Writer_Ini();
				$writer->write($file, $config);
			}
			
			$this->_response->setBody('RESULT_OK');
		}
	}
	
	public function addAction() 
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		if ($this->_request->isPost()) {
			$module = $this->_request->getPost('module_name');
			$language = $this->_request->getPost('language');
			$widget = $this->_request->getPost('widget');
			
			$section1 = $this->_request->getPost('section'); 
			$section2 = $this->_request->getPost('new_section');
			$section = ($section1 != '') ? $section1 : $section2;
			
			$key = $this->_request->getPost('key');
			$value = $this->_request->getPost('value');
			
			$file = TOMATO_APP_DIR.DS.'modules'.DS.$module;
			$file = ($widget)
					? $file.DS.'widgets'.DS.$widget.DS.'lang.'.$language.'.ini'
					: $file.DS.'languages'.DS.'lang.'.$language.'.ini';
			
			$config = new Zend_Config_Ini($file, null, array('allowModifications' => true));
			
			// Could not create new section for config as follow:
			// $config->$section->$key = $value;
			// $writer->write($file, $config);
			// so, below is trick
			$config = $config->toArray();
			if ($section1 == '') {
				$config[$section] = array();
			}
			$config[$section][$key] = $value;
			$writer = new Zend_Config_Writer_Ini();
			$writer->write($file, new Zend_Config($config));
			
			$this->_helper->getHelper('FlashMessenger')
					->addMessage($this->view->translator('lang_add_successful'));
					
			$url = ($widget != '')
					? $this->view->url(array('module_name' => $module, 'widget' => $widget, 'language' => $language), 'core_language_edit_widget')
					: $this->view->url(array('module_name' => $module, 'language' => $language), 'core_language_edit_module');
			$this->_redirect($this->view->serverUrl().$url);
		}	
	}
	
	public function deleteAction() 
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		if ($this->_request->isPost()) {
			$module = $this->_request->getPost('module_name');
			$language = $this->_request->getPost('language');
			$widget = $this->_request->getPost('widget');
			$section = $this->_request->getPost('section');
			$key = $this->_request->getPost('key');
			
			$file = TOMATO_APP_DIR.DS.'modules'.DS.$module;
			$file = ($widget != '')
					? $file.DS.'widgets'.DS.$widget.DS.'lang.'.$language.'.ini'
					: $file.DS.'languages'.DS.'lang.'.$language.'.ini';
			
			$config = new Zend_Config_Ini($file, null, array('allowModifications' => true));
			unset($config->$section->$key);
			$writer = new Zend_Config_Writer_Ini();
			$writer->write($file, $config);
			
			$this->_response->setBody('RESULT_OK');
		}
	}
	
	/**
	 * Allows user to upload language packages in *.zip format
	 * The zip file includes files formated as follow:
	 * 1) TomatoCMS directory structure
	 * 	app
	 * 	|___modules
	 * 		|___<ModuleName>
	 * 			|___languages
	 * 			|	|___lang.LanguageCode.ini
	 * 			|___widgets
	 * 				|___<WidgetName>
	 * 					|___lang.LanguageCode.ini
	 * 2) 
	 * - ModuleName.lang.LanguageCode.ini
	 * - ModuleName.widgets.WidgetName.lang.LanguageCode.ini
	 * 
	 * For example, a French language package should consist of files:
	 * news.lang.fr_FR.ini
	 * news.widgets.breadcump.lang.fr_FR.ini 
	 * 
	 * @since 2.0.4
	 */
	public function uploadAction()
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		if ($this->_request->isPost()) {
			$file = $_FILES['file'];
			$prefix = 'language_'.time();
			
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
				
				// Copy language files to associated folder
				$dirIterator = new DirectoryIterator($tempDir);
				$language = null;
				foreach ($dirIterator as $file) {
		            if ($file->isDot()) {
		                continue;
		            }
					$name = $file->getFilename();
		            if (preg_match('/^[^a-z]/i', $name) || ('CVS' == $name) 
		            		|| ('.svn' == strtolower($name))) {
		                continue;
		            }
		            if ($file->isDir() && 'app' == $name) {
		            	Tomato_Core_Utility_File::copyRescursiveDir($tempDir.DS.$name, TOMATO_APP_DIR);
		            } else {
						$arr = explode('.', $name);
						if (is_array($arr) && count($arr) > 3) {
							continue;
						}
						if (null == $language) {
							$language = implode('.', array_slice($arr, -3));
						}
						$source = $tempDir.DS.$name;
						switch (count($arr)) {
							case 4:
								$des = TOMATO_APP_DIR.DS.'modules'.DS.$arr[0].DS.'languages'.DS.$language;
								copy($source, $des);
								break;
							case 6:
								$des = TOMATO_APP_DIR.DS.'modules'.DS.$arr[0].DS.'widgets'.DS.$arr[2].DS.$language;
								copy($source, $des);
								break;
						}
		            }
		        }
				
				// Remove all the temp files
				$zip->close();
				
				Tomato_Core_Utility_File::deleteRescursiveDir($tempDir);
				unlink($zipFile);
				
				$this->_redirect($this->view->serverUrl().$this->view->url(array(), 'core_language_list'));
			}
		}
	}
}
