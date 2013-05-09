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
 * @version 	$Id: TemplateController.php 1545 2010-03-10 07:46:33Z huuphuoc $
 */

class Core_TemplateController extends Zend_Controller_Action 
{
	/* ========== Backend actions =========================================== */

	public function listAction() 
	{
		$config = Tomato_Core_Config::getConfig();
		$this->view->assign('currTemplate', $config->web->template);
		$this->view->assign('currSkin', $config->web->skin);
		
		$subDirs = Tomato_Core_Utility_File::getSubDir(TOMATO_APP_DIR.DS.'templates');
		$templates = array();
		foreach ($subDirs as $dir) {
			// Load template info
			$file = TOMATO_APP_DIR.DS.'templates'.DS.$dir.DS.'about.xml';
			if (!file_exists($file)) {
				continue;
			}
			$xml = simplexml_load_file($file);
			if ((string)$xml->selectable == 'false') {
				continue;
			}
			$template = array(
				'name' => strtolower($xml->name),
				'description' => (string)$xml->description,
				'thumbnail' => (string)$xml->thumbnail,
				'author' => (string)$xml->author,
				'email' => (string)$xml->email,
				'version' => (string)$xml->version,
				'license' => (string)$xml->license,
			);
			$template['skin'] = array();
			foreach ($xml->skins->skin as $skin) {
				$attrs = $skin->attributes();
				$template['skin'][] = array(
					'name' => (string)$attrs['name'],
					'color' => (string)$skin->color,
					'description' => (string)$skin->description,
				);
			}
			
			$templates[] = $template;
		}
		$this->view->assign('templates', $templates);
	}
	
	public function activateAction() 
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		if ($this->_request->isPost()) {
			$template = $this->_request->getPost('template');
			$conn = Tomato_Core_Db_Connection::getMasterConnection();

            $host = $_SERVER['SERVER_NAME'];
            $host = (substr($host, 0, 3) == 'www') ? substr($host, 4) : $host;

			// Update config file
			$file = TOMATO_APP_DIR.DS.'config'.DS.$host.'.ini';
            if(!file_exists($file)){
                $file = TOMATO_APP_DIR.DS.'config'.DS.'app.ini';
            }
			$config = new Zend_Config_Ini($file, null, array('allowModifications' => true));
			$config->web->template = $template;
			$writer = new Zend_Config_Writer_Ini();
			$writer->write($file, $config);
			
			// Create template pages
			Tomato_Modules_Core_Services_Install_Template::install($template);
			
			// Rewrite layout config file
			$gateway = new Tomato_Modules_Core_Model_LayoutPageGateway();
			$gateway->setDbConnection($conn);
			$file = TOMATO_APP_DIR.DS.'config'.DS.'layout.ini';
			$gateway->export($file);
			
			$this->_response->setBody('RESULT_OK');
		}
	}
	
	public function skinAction() 
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		if ($this->_request->isPost()) {
			$skin = $this->_request->getPost('skin');
			
			// Update config file
			$file = TOMATO_APP_DIR.DS.'config'.DS.'app.ini';
			$config = new Zend_Config_Ini($file, null, array('allowModifications' => true));
			$config->web->skin = $skin;
			
			// Remove skin from cookie
			if (isset($_COOKIE['APP_SKIN'])) {
				unset($_COOKIE['APP_SKIN']);
			}
			
			$writer = new Zend_Config_Writer_Ini();
			$writer->write($file, $config);
			
			$this->_response->setBody('RESULT_OK');
		}
	}
	
	public function editskinAction() 
	{
		$template = $this->_request->getParam('template');
		$skin = $this->_request->getParam('skin');
		
		$this->view->assign('template', $template);
		$this->view->assign('skin', $skin);
		
		$file = TOMATO_ROOT_DIR.DS.'skin'.DS.$template.DS.$skin.DS.'default.css';
		if (!$this->_request->isPost()) {
			$content = file_get_contents($file);
			$this->view->assign('content', $content); 
		} else {
			$content = $this->_request->getPost('content');
			@file_put_contents($file, $content);
			
			$this->_redirect($this->view->serverUrl().$this->view->url(array('template' => $template, 'skin' => $skin), 'core_template_editskin'));
		}
	}
}
