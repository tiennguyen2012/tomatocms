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
 * @version 	$Id: MenuController.php 1545 2010-03-10 07:46:33Z huuphuoc $
 * @since		2.0.2
 */

class Menu_MenuController extends Zend_Controller_Action 
{
	/* ========== Backend actions =========================================== */
	
	public function listAction() 
	{
		$conn = Tomato_Core_Db_Connection::getSlaveConnection();
		
		$perPage = 20;
		$pageIndex = $this->_request->getParam('pageIndex');
		if (null == $pageIndex || '' == $pageIndex) {
			$pageIndex = 1;
		}
		$start = ($pageIndex - 1) * $perPage;
		$this->view->assign('pageIndex', $pageIndex);

		$menuGateway = new Tomato_Modules_Menu_Model_MenuGateway();
		$menuGateway->setDbConnection($conn);
		$menus = $menuGateway->find($start, $perPage);
		$this->view->assign('menus', $menus);
		
		$numMenus = $menuGateway->count();
		$this->view->assign('numMenus', $numMenus);
		
		// Paginator
		$paginator = new Zend_Paginator(new Tomato_Core_Utility_PaginatorAdapter($menus, $numMenus));
		$paginator->setCurrentPageNumber($pageIndex);
		$paginator->setItemCountPerPage($perPage);
		$this->view->assign('paginator', $paginator);
		$this->view->assign('paginatorOptions', array(
			'path' => $this->view->url(array(), 'menu_list'),
			'itemLink' => 'page-%d',
		));		
	}
	
	public function buildAction() 
	{
		if ($this->getRequest()->isPost()) {
			$menuName = $this->_request->getPost('menu_name');
			$menuDescription = $this->_request->getPost('menu_description');
			$menuData = $this->_request->getPost('menu_html_data');
			
			$user = Zend_Auth::getInstance()->getIdentity();
			$menu = new Tomato_Modules_Menu_Model_Menu(array(
						'name'				=> $menuName,
						'description'		=> $menuDescription,
						'json_data'			=> $menuData,
						'user_id'			=> $user->user_id,
						'user_name'			=> $user->user_name,
						'created_date'		=> date('Y-m-d H:i:s'), 		
					));
					
			$conn = Tomato_Core_Db_Connection::getMasterConnection();
			$menuGateway = new Tomato_Modules_Menu_Model_MenuGateway();
			$menuGateway->setDbConnection($conn);
			$menuGateway->add($menu);
			
			$this->_helper->getHelper('FlashMessenger')->addMessage(
					$this->view->translator('menu_build_success')
			);
			$this->_redirect($this->view->serverUrl().$this->view->url(array(), 'menu_build'));
		}
	}
	
	public function editAction() 
	{
		$conn = Tomato_Core_Db_Connection::getMasterConnection();
		$this->view->assign('baseUrl', $this->_request->getBaseUrl());
		$id = $this->_request->getParam('menu_id');
		$menuGateway = new Tomato_Modules_Menu_Model_MenuGateway();
		$menuGateway->setDbConnection($conn);
		$menu = $menuGateway->getMenuById($id);
		if (null == $menu) {
			return;
		}
		$this->view->assign('menu', $menu);	
		$this->view->assign('menuData', Zend_Json::decode($menu->json_data));
		
		if ($this->_request->isPost()) {
			$menuName = $this->_request->getPost('menu_name');
			$menuDescription = $this->_request->getPost('menu_description');
			$menuData = $this->_request->getPost('menu_html_data');
			
			$menu->name = $menuName;
			$menu->description = $menuDescription;
			$menu->json_data = $menuData;
			$menuGateway->update($menu);
			
			$this->_helper->getHelper('FlashMessenger')->addMessage(
					$this->view->translator('menu_edit_success')
			);
			$this->_redirect($this->view->serverUrl().$this->view->url(array(), 'menu_list'));
		}
	} 
	
	public function deleteAction() 
	{
		$this->_helper->getHelper('layout')->disableLayout();
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		if ($this->_request->isPost()) {
			$id = $this->_request->getPost('id');
			$conn = Tomato_Core_Db_Connection::getMasterConnection();
			$gateway = new Tomato_Modules_Menu_Model_MenuGateway();
			$gateway->setDbConnection($conn);
			$menu = $gateway->getMenuById($id);
			
			if (null == $menu) {
				$this->_response->setBody('RESULT_NOT_FOUND');
				return;
			} 

			$gateway->delete($id);
			$data = array(
				'name' => $menu->name
			);
			
			$this->_response->setBody(Zend_Json::encode($data));
			return;
		}
		$this->_response->setBody('RESULT_NOT_OK');
	}
}
