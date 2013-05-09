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
 * @version 	$Id: CategoryController.php 1545 2010-03-10 07:46:33Z huuphuoc $
 */

class Category_CategoryController extends Zend_Controller_Action 
{
	/* ========== Backend actions =========================================== */

	public function listAction() 
	{
		$conn = Tomato_Core_Db_Connection::getMasterConnection();
		$categoryGateway = new Tomato_Modules_Category_Model_CategoryGateway();
		$categoryGateway->setDbConnection($conn);
		$categories = $categoryGateway->getCategoryTree();
		$this->view->assign('categories', $categories);
	}
	
	public function addAction() 
	{
		$conn = Tomato_Core_Db_Connection::getMasterConnection();
		$categoryGateway = new Tomato_Modules_Category_Model_CategoryGateway();
		$categoryGateway->setDbConnection($conn);
		$categories = $categoryGateway->getCategoryTree();
		$this->view->assign('categories', $categories);
		
		if ($this->_request->isPost()) {
			$user = Zend_Auth::getInstance()->getIdentity();
			$name = $this->_request->getPost('name');
			$slug = $this->_request->getPost('slug');
			$parentId = $this->_request->getPost('parentId');
			$meta = $this->_request->getPost('meta');
			$category = new Tomato_Modules_Category_Model_Category(array(
				'name'			=> $name,
				'slug'			=> $slug,
				'meta'			=> $meta,
				'created_date'	=> date('Y-m-d H:i:s'),
				'user_id'		=> $user->user_id,
			));
			$id = $categoryGateway->add($category, $parentId);
			if ($id > 0) {
				$this->_helper->getHelper('FlashMessenger')->addMessage(
					$this->view->translator('category_add_success')
				);
				$this->_redirect($this->view->serverUrl().$this->view->url(array(), 'category_category_add'));
			}
		}
	}
	
	public function editAction() 
	{
		$conn = Tomato_Core_Db_Connection::getMasterConnection();
		$categoryGateway = new Tomato_Modules_Category_Model_CategoryGateway();
		$categoryGateway->setDbConnection($conn);
		$categories = $categoryGateway->getCategoryTree();
		$this->view->assign('categories', $categories);
		
		$id = $this->_request->getParam('category_id');
		$category = $categoryGateway->getCategoryById($id);
		$this->view->assign('category', $category);
		
		if ($this->_request->isPost()) {
			$deleteCategory = true;
			$name = $this->_request->getPost('name');
			$slug = $this->_request->getPost('slug');
			$parentId = $this->_request->getPost('parentId');
			$meta = $this->_request->getPost('meta');
			$includeChildCategory = $this->_request->getPost('include_child_category');
			$includeChildCategory = ($includeChildCategory == 1) ? true : false;
			
			$category->name = $name;
			$category->slug = $slug;
			$category->meta = $meta;
			$category->modified_date = date('Y-m-d H:i:s');

			/**
			 * Check change parent category
			 */
			$curParent = $categoryGateway->getCategoryParent($category);
			if ((null == $curParent && $parentId == 0) 
					|| ($curParent != null && $curParent->category_id == $parentId)) {
				$deleteCategory = false;
			}
			$categoryGateway->update($category, $parentId, $deleteCategory, $includeChildCategory);
			$this->_helper->getHelper('FlashMessenger')->addMessage(
				$this->view->translator('category_edit_success')
			);
			$this->_redirect($this->view->serverUrl().$this->view->url(array('category_id' => $id), 'category_category_edit'));
		}
	}
	
	public function deleteAction() 
	{
		$this->_helper->getHelper('layout')->disableLayout();
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$response = 'RESULT_ERROR';
		if ($this->_request->isPost()) {
			$id = $this->_request->getPost('id');
			$conn = Tomato_Core_Db_Connection::getMasterConnection();
			$categoryGateway = new Tomato_Modules_Category_Model_CategoryGateway();
			$categoryGateway->setDbConnection($conn);
			$category = $categoryGateway->getCategoryById($id);
			
			if ($category != null) {
				$categoryGateway->delete($category);
				$response = 'RESULT_OK';
			}
		}
		$this->getResponse()->setBody($response);
	}
}
	