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
 * @version 	$Id: PageController.php 1545 2010-03-10 07:46:33Z huuphuoc $
 */

class Core_PageController extends Zend_Controller_Action 
{
	/* ========== Backend actions =========================================== */

	public function listAction() 
	{
		$template = $this->_request->getParam('template');
		$this->view->assign('template', $template);
		
		$conn = Tomato_Core_Db_Connection::getMasterConnection();
		$select = $conn->select()
						->from(array('p' => Tomato_Core_Db_Connection::getDbPrefix().'core_page'))
						->order('p.ordering ASC')
						->order('p.name ASC');
		$rs = $select->query()->fetchAll();
		$gateway = new Tomato_Modules_Core_Model_LayoutPageGateway();
		$pages = new Tomato_Core_Model_RecordSet($rs, $gateway);
		$this->view->assign('pages', $pages);
	}
	
	public function orderingAction() 
	{
		$this->_helper->getHelper('layout')->disableLayout();
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		
		if ($this->_request->isPost()) {
			$ids = $this->_request->getPost('pageId');
			
			$conn = Tomato_Core_Db_Connection::getMasterConnection();
			// Reset the order
			$conn->update(Tomato_Core_Db_Connection::getDbPrefix().'core_page', array('ordering' => 0));
			
			// Update new order
			if ($ids != null) {
				for ($i = 0; $i < count($ids); $i++) {
					$sql = 'UPDATE '.Tomato_Core_Db_Connection::getDbPrefix().'core_page SET ordering=? WHERE page_id=?';
					$conn->query($sql, array($i, $ids[$i]));
				}
			}
			
			// Rewrite layout config file
			$gateway = new Tomato_Modules_Core_Model_LayoutPageGateway();
			$gateway->setDbConnection($conn);
			$file = TOMATO_APP_DIR.DS.'config'.DS.'layout.ini';
			$gateway->export($file);
		}
		
		$this->_response->setBody('RESULT_OK');
	}
	
	public function deleteAction() 
	{
		$this->_helper->getHelper('layout')->disableLayout();
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		
		if ($this->_request->isPost()) {
			$id = $this->_request->getPost('id');
			$conn = Tomato_Core_Db_Connection::getMasterConnection();
			
			$gateway = new Tomato_Modules_Core_Model_LayoutPageGateway();
			$gateway->setDbConnection($conn);
			$gateway->delete($id);
			
			$select = $conn->select()
					->from(array('p' => Tomato_Core_Db_Connection::getDbPrefix().'core_page'))
					->order('p.ordering ASC')
					->order('p.name ASC');
			$rs = $select->query()->fetchAll();
			$gateway = new Tomato_Modules_Core_Model_LayoutPageGateway();
			$pages = new Tomato_Core_Model_RecordSet($rs, $gateway);
			for ($i = 0; $i < count($pages); $i++) {
				$sql = 'UPDATE '.Tomato_Core_Db_Connection::getDbPrefix().'core_page SET ordering=? WHERE page_id=?';
				$conn->query($sql, array($i, $pages[$i]->page_id));
			}
			
			// Rewrite layout config file
			$layoutPageGateway = new Tomato_Modules_Core_Model_LayoutPageGateway();
			$layoutPageGateway->setDbConnection($conn);
			$file = TOMATO_APP_DIR.DS.'config'.DS.'layout.ini';
			$layoutPageGateway->export($file);
		}
		$this->_response->setBody('RESULT_OK');
	}
	
	public function layoutAction() 
	{
		$template = $this->_request->getParam('template');
		$this->view->assign('template', $template);
		
		$modules = Tomato_Core_Utility_File::getSubDir(TOMATO_APP_DIR.DS.'modules');
		$this->view->assign('modules', $modules);
		
		// Get widgets
		$conn = Tomato_Core_Db_Connection::getMasterConnection();
		$select = $conn->select()
					->from(array('w' => Tomato_Core_Db_Connection::getDbPrefix().'core_widget'), array('module'))
					->group('w.module')
					->order('w.module');
		$widgetModules = $select->query()->fetchAll();
		$this->view->assign('widgetModules', $widgetModules);
		
		$pageName = $this->_request->getParam('page_name');
		$pageGateway = new Tomato_Modules_Core_Model_LayoutPageGateway();
		$pageGateway->setDbConnection($conn);
		$page = $pageGateway->getPageByName($pageName);
		$this->view->assign('page', $page);
		
		// Load layout data from JSON file
		$jsonData = null;
		$jsonFile = TOMATO_APP_DIR.DS.'templates'.DS.$template.DS.'layouts'.DS.$pageName.'.json';
		$xmlFile = TOMATO_APP_DIR.DS.'templates'.DS.$template.DS.'layouts'.DS.$pageName.'.xml';
		if (file_exists($jsonFile)) {
			$jsonData = file_get_contents($jsonFile);
		} else if (file_exists($xmlFile)) {
			// Try to build JSON file if it does not exist
			$array = Tomato_Core_Layout::load($xmlFile);
			$array = Zend_Json::encode($array);
			file_put_contents($jsonFile, $array);
			$jsonData = file_get_contents($jsonFile);
		}
		$this->view->assign('jsonData', $jsonData);
	}
	
	public function savelayoutAction() 
	{
		$this->_helper->getHelper('layout')->disableLayout();
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		
		if ($this->_request->isPost()) {
			$template = $this->_request->getPost('template');
			$page = $this->_request->getPost('page');
			$jsonLayout = $this->_request->getPost('layout');
			$layout = Zend_Json::decode($jsonLayout);
			
			// Save data in JSON format for reading process more easy later
			$jsonFile = TOMATO_APP_DIR.DS.'templates'.DS.$template.DS.'layouts'.DS.$page.'.json';
			$f = fopen($jsonFile, 'w');
			fwrite($f, $jsonLayout);
			fclose($f);
			
			$xmlFile = TOMATO_APP_DIR.DS.'templates'.DS.$template.DS.'layouts'.DS.$page.'.xml';
			Tomato_Core_Layout::save($xmlFile, $layout);
			
			$this->_response->setBody('RESULT_OK');			
		}
	}
	
	public function widgetsAction() 
	{
		$this->_helper->getHelper('layout')->disableLayout();
		$module = $this->_request->getParam('mod');

		$pageIndex = $this->_request->getParam('page', 1);
		$perPage = 10;
		$start = ($pageIndex - 1) * $perPage;
		
		$conn = Tomato_Core_Db_Connection::getMasterConnection();
		
		// Get the number of widgets in the module
		$select = $conn->select()
					->from(array('w' => Tomato_Core_Db_Connection::getDbPrefix().'core_widget'), array('num_widgets' => 'COUNT(widget_id)'))
					->where('w.module = ?', $module)
					->limit(1);
		$numWidgets = $select->query()->fetch()->num_widgets;
					
		// List widgets
		$select = $conn->select()
					->from(array('w' => Tomato_Core_Db_Connection::getDbPrefix().'core_widget'))
					->where('w.module = ?', $module)
					->limit($perPage, $start);
		$rs = $select->query()->fetchAll();
		$widgetGateway = new Tomato_Modules_Core_Model_WidgetGateway();
		$widgets = new Tomato_Core_Model_RecordSet($rs, $widgetGateway);
		$this->view->assign('widgets', $widgets);
		
		// Paginator
		$paginator = new Zend_Paginator(new Tomato_Core_Utility_PaginatorAdapter($widgets, $numWidgets));
		$paginator->setCurrentPageNumber($pageIndex);
		$paginator->setItemCountPerPage($perPage);
		$this->view->assign('paginator', $paginator);
		$this->view->assign('paginatorOptions', array(
			'path' => '',
			'itemLink' => "javascript: loadWidgets(%d, '$module');",
		));
	}
	
	public function addAction() 
	{
		$conn = Tomato_Core_Db_Connection::getMasterConnection();
		if ($this->_request->isPost()) {
			$name = $this->_request->getPost('name');
			$title = $this->_request->getPost('title');
			$description = $this->_request->getPost('description');
			$url = $this->_request->getPost('url');
			$urlType = $this->_request->getPost('url_type');
			$paramsName = $this->_request->getPost('params_name');
			$paramsValue = $this->_request->getPost('params_value');
			$params = null;
			switch ($urlType) {
				case 'regex':
					if (!empty($paramsName) && !empty($paramsValue)) {
						for ($i=0; $i < count($paramsName); $i++) {
							$params .= (null == $params) ? '' : ',';
							$params .= '"'.trim($paramsName[$i]).'":"'.trim($paramsValue[$i]).'"';
						}
						$params = (null == $params) ? null : '{'.$params.'}';
					}
					break;
				default:
					break;
			}
			
			$select = $conn->select()
					->from(array('p' => Tomato_Core_Db_Connection::getDbPrefix().'core_page'))
					->order('p.ordering ASC')
					->order('p.name ASC');
			$rs = $select->query()->fetchAll();
			$gateway = new Tomato_Modules_Core_Model_LayoutPageGateway();
			$pages = new Tomato_Core_Model_RecordSet($rs, $gateway);
			for ($i = 0; $i < count($pages); $i++) {
				$sql = 'UPDATE '.Tomato_Core_Db_Connection::getDbPrefix().'core_page SET ordering=? WHERE page_id=?';
				$conn->query($sql, array($i, $pages[$i]->page_id));
			}
			$ordering = $i;
			
			$page = new Tomato_Modules_Core_Model_LayoutPage(array(
				'name' => $name,
				'title' => $title,
				'description' => $description,
				'url' => $url,
				'url_type' => $urlType,
				'params' => $params,
				'ordering' => $ordering,
			));
			$pageGateway = new Tomato_Modules_Core_Model_LayoutPageGateway();
			$pageGateway->setDbConnection($conn);
			$id = $pageGateway->add($page);
			if ($id > 0) {
				// Rewrite layout config file
				$gateway = new Tomato_Modules_Core_Model_LayoutPageGateway();
				$gateway->setDbConnection($conn);
				$file = TOMATO_APP_DIR.DS.'config'.DS.'layout.ini';
				$gateway->export($file);
				
				$this->_helper->getHelper('FlashMessenger')->addMessage(
					$this->view->translator('page_add_success')
				);
				$this->_redirect($this->view->serverUrl().$this->view->url(array(), 'core_page_add'));
			}
		}
	}
	
	/**
	 * Check if the page name has been existed or not
	 */
	public function checkAction() 
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		$checkType = $this->_request->getParam('check_type');
		
		$conn = Tomato_Core_Db_Connection::getMasterConnection();
		$select = $conn->select()
					->from(array('p' => Tomato_Core_Db_Connection::getDbPrefix().'core_page'), array('num_pages' => 'COUNT(*)'));
					
		$original = $this->_request->getParam('original');
		$result = false;			
		switch ($checkType) {
			case 'name':
				$name = $this->_request->getParam('name');
				if ($original == null || ($original != null && $name != $original)) {
					$select->where('p.name = ?', $name)
						   ->limit(1);
					$rs = $select->query()->fetch();
					$numPages = $rs->num_pages;
					$result = ($numPages == 0) ? false : true;
				}
				break;
			case 'url':
				$url = $this->_request->getParam('url');
				if ($original == null || ($original != null && $url != $original)) {
					$select->where('p.url = ?', $url)
						   ->limit(1);
					$rs = $select->query()->fetch();
					$numPages = $rs->num_pages;
					$result = ($numPages == 0) ? false : true;
				}
				break;
		}			
		($result == true) ? $this->getResponse()->setBody('false') 
						: $this->getResponse()->setBody('true');		
	}
	
	public function editAction() 
	{
		$conn = Tomato_Core_Db_Connection::getMasterConnection();
		$pageGateway = new Tomato_Modules_Core_Model_LayoutPageGateway();
		$pageGateway->setDbConnection($conn);
		
		$pageName = $this->_request->getParam('page_name');
		$template = $this->_request->getParam('template');
		$page = $pageGateway->getPageByName($pageName);
		$this->view->assign('page', $page);
		$this->view->assign('template', $template);
		
		$params = str_replace('{', '', $page->params);
		$params = str_replace('}', '', $params);
		$paramsName = array();
		$paramsValue = array();
		$params = explode(',', $params);
		foreach ($params as $param) {
			$nameValue = explode(':', $param);
			if (count($nameValue) == 2) {
				$paramsName[] = str_replace('"', '', trim($nameValue[0]));
				$paramsValue[] = str_replace('"', '', trim($nameValue[1]));
			}
		}
		$this->view->assign('paramsName', $paramsName);
		$this->view->assign('paramsValue', $paramsValue);
		
		if ($this->_request->isPost()) {
			$pageId = $this->_request->getPost('page_id');
			$name = $this->_request->getPost('name');
			$title = $this->_request->getPost('title');
			$description = $this->_request->getPost('description');
			$url = $this->_request->getPost('url');
			$urlType = $this->_request->getPost('url_type');
			$paramsName = $this->_request->getPost('params_name');
			$paramsValue = $this->_request->getPost('params_value');
			$params = null;
			switch ($urlType) {
				case 'regex':
					if (!empty($paramsName) && !empty($paramsValue)) {
						for ($i=0; $i < count($paramsName); $i++) {
							$params .= (null == $params) ? '' : ',';
							$params .= '"'.trim($paramsName[$i]).'":"'.trim($paramsValue[$i]).'"';
						}
						$params = (null == $params) ? null : '{'.$params.'}';
					}
					break;
				default:
					break;
			}
			
			$page = new Tomato_Modules_Core_Model_LayoutPage(array(
				'page_id' => $pageId,
				'title' => $title,
				'description' => $description,
				'url' => $url,
				'url_type' => $urlType,
				'params' => $params,
			));
			$result = $pageGateway->update($page);
			
			// Rewrite layout config file
			$layoutPageGateway = new Tomato_Modules_Core_Model_LayoutPageGateway();
			$layoutPageGateway->setDbConnection($conn);
			$file = TOMATO_APP_DIR.DS.'config'.DS.'layout.ini';
			$layoutPageGateway->export($file);
			
			$this->_helper->getHelper('FlashMessenger')->addMessage(
				$this->view->translator('page_edit_success')
			);
			$this->_redirect($this->view->serverUrl().$this->view->url(array('template' => $template, 'page_name' => $pageName), 'core_page_edit')); 
		}
	}
}
