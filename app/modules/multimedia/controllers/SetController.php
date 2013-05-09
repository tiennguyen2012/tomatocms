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
 * @version 	$Id: SetController.php 1545 2010-03-10 07:46:33Z huuphuoc $
 */

class Multimedia_SetController extends Zend_Controller_Action 
{
	public function init() 
	{
		/**
		 * Register hooks
		 * @since 2.0.2
		 */
		Tomato_Core_Hook_Registry::getInstance()->register('Multimedia_Set_Add_ShowSidebar', 
			array(new Tomato_Modules_Tag_Hooks_Tagger_Hook(), 'show', array(null, 'set_id', 'multimedia_set_details', 'multimedia_tag_set')));
		Tomato_Core_Hook_Registry::getInstance()->register('Multimedia_Set_Add_Success',
			'Tomato_Modules_Tag_Hooks_Tagger_Hook::add');
		Tomato_Core_Hook_Registry::getInstance()->register('Multimedia_Set_Edit_Success',
			'Tomato_Modules_Tag_Hooks_Tagger_Hook::add');
	}	
	
	/* ========== Frontend actions ========================================== */
	
	/**
	 * @since 2.0.2
	 */
	public function detailsAction() 
	{
		$setId = $this->_request->getParam('set_id');
		$conn = Tomato_Core_Db_Connection::getSlaveConnection();		
		$setGateway = new Tomato_Modules_Multimedia_Model_SetGateway();
		$setGateway->setDbConnection($conn);
		$set = $setGateway->getSetById($setId);
		
		if (null == $set) {
			throw new Tomato_Core_Exception_NotFound();
		}
		
		$this->view->assign('set', $set);
		
		// Get the list of file that belongs to this set
		$perPage = 18;
		$pageIndex = $this->_request->getParam('page_index');
		if (null == $pageIndex || '' == $pageIndex) {
			$pageIndex = 1;
		}
		$start = ($pageIndex - 1) * $perPage;
		$fileGateway = new Tomato_Modules_Multimedia_Model_FileGateway();
		$fileGateway->setDbConnection($conn);
		$select = $conn->select()
					->from(array('f' => Tomato_Core_Db_Connection::getDbPrefix().'multimedia_file'), array('file_id', 'title', 'description', 'image_general', 'image_medium', 'image_large', 'url'))
					->joinInner(array('fs' => Tomato_Core_Db_Connection::getDbPrefix().'multimedia_file_set_assoc'), 'fs.file_id = f.file_id AND fs.set_id = '.$conn->quote($setId), array())
					->where('f.is_active = ?', 1)
					->order('f.file_id DESC')
					->limit($perPage, $start);
		$rs = $select->query()->fetchAll();
		$files = new Tomato_Core_Model_RecordSet($rs, $fileGateway);
		$this->view->assign('files', $files);
		
		// Count the files in set
		$select = $conn->select()
					->from(array('f' => Tomato_Core_Db_Connection::getDbPrefix().'multimedia_file'), array('num_files' => 'COUNT(*)'))
					->joinInner(array('fs' => Tomato_Core_Db_Connection::getDbPrefix().'multimedia_file_set_assoc'), 'fs.file_id = f.file_id AND fs.set_id = '.$conn->quote($setId), array())
					->where('f.is_active = ?', 1)
					->limit(1);
		$numFiles = $select->query()->fetch()->num_files; 
		
		// Paginator
		$paginator = new Zend_Paginator(new Tomato_Core_Utility_PaginatorAdapter($files, $numFiles));
		$paginator->setCurrentPageNumber($pageIndex);
		$paginator->setItemCountPerPage($perPage);
		$this->view->assign('paginator', $paginator);
		$this->view->assign('paginatorOptions', array(
			'path' => $this->view->url($set->getProperties(), 'multimedia_set_details'),
			'itemLink' => 'page-%d',
		));
	}
	
	/* ========== Backend actions =========================================== */
		
	public function listAction() 
	{
		$conn = Tomato_Core_Db_Connection::getMasterConnection();
		
		$perPage = 30;
		$pageIndex = $this->_request->getParam('pageIndex');
		if (null == $pageIndex || '' == $pageIndex) {
			$pageIndex = 1;
		}
		$start = ($pageIndex - 1) * $perPage;
		$this->view->assign('pageIndex', $pageIndex);
		
		// Build photo search expression
		$user = Zend_Auth::getInstance()->getIdentity();
		$paramsString = null;
		$exp = array(
			'created_user' => $user->user_id
		);
		
		if ($this->_request->isPost()) {
			$keyword = $this->_request->getPost('keyword');
			$findMySets = $this->_request->getPost('findMySets');
			if ($keyword) {
				$exp['keyword'] = $keyword;
			}
			if (null == $findMySets) {
				$exp['created_user'] = null;
			}
			$paramsString = rawurlencode(base64_encode(Zend_Json::encode($exp)));
		} else {
			$paramsString = $this->_request->getParam('q');
			if (null != $paramsString) {
				$exp = rawurldecode(base64_decode($paramsString));
				$exp = Zend_Json::decode($exp); 
			} else {
				$paramsString = rawurlencode(base64_encode(Zend_Json::encode($exp)));
			}
		}
		$setGateway = new Tomato_Modules_Multimedia_Model_SetGateway();
		$setGateway->setDbConnection($conn);
		$sets = $setGateway->find($start, $perPage, $exp);
		$numSets = $setGateway->count($exp);
		
		// Paginator
		$paginator = new Zend_Paginator(new Tomato_Core_Utility_PaginatorAdapter($sets, $numSets));
		$paginator->setCurrentPageNumber($pageIndex);
		$paginator->setItemCountPerPage($perPage);
		$this->view->assign('paginator', $paginator);
		$this->view->assign('paginatorOptions', array(
			'path' => $this->view->url(array(), 'multimedia_set_list'),
			'itemLink' => (null == $paramsString) ? 'page-%d' : 'page-%d?q='.$paramsString,
		));
		
		$this->view->assign('numSets', $numSets);
		$this->view->assign('sets', $sets);
		$this->view->assign('exp', $exp);
	}
	
	public function addAction() 
	{
		$this->view->addHelperPath(TOMATO_APP_DIR.DS.'modules'.DS.'upload'.DS.'views'.DS.'helpers', 'Upload_View_Helper_');
		$conn = Tomato_Core_Db_Connection::getMasterConnection();
		
		if ($this->_request->isPost()) {
			$user = Zend_Auth::getInstance()->getIdentity();			
			$title = $this->_request->getPost('title');
			$description = $this->_request->getPost('description');
			$setImage = $this->_request->getPost('setImage');
			$imageUrls = Zend_Json::decode($setImage);
			$photos = $this->_request->getPost('photos');
			
			$set = new Tomato_Modules_Multimedia_Model_Set(array(
				'title' => $title,
				'slug' => Tomato_Core_Utility_String::removeSign($title, '-', true),
				'description' => $description,
				'created_date' => date('Y-m-d H:i:s'),
				'created_user_id' => $user->user_id,
				'created_user_name' => $user->user_name,
				'is_active' => true,
			));
			if (null != $imageUrls) {
				$set->image_square = $imageUrls['square'];
				$set->image_large = $imageUrls['large'];
				$set->image_general = $imageUrls['general'];
				$set->image_small = $imageUrls['small'];
				$set->image_crop = $imageUrls['crop'];
				$set->image_medium = $imageUrls['medium'];
			}
			$setGateway = new Tomato_Modules_Multimedia_Model_SetGateway();
			$setGateway->setDbConnection($conn);
			$setId = $setGateway->add($set);
			
			if ($setId > 0) {
				if ($photos != null && is_array($photos)) {
					$fileGateway = new Tomato_Modules_Multimedia_Model_FileGateway();
					$fileGateway->setDbConnection($conn);
					
					foreach ($photos as $photo) {	
						$images = Zend_Json::decode($photo);
						
						$fileId = null;
						if ($images['file_id']) {
							$fileId = $images['file_id'];
						} else {
							$file = new Tomato_Modules_Multimedia_Model_File(array(
								'image_crop' => $images['crop'],
								'image_general' => $images['general'],
								'image_medium' => $images['medium'],
								'image_original' => $images['original'],
								'image_small' => $images['small'],
								'image_square' => $images['square'],
								'image_large' => $images['large'],
								'created_date' => date('Y-m-d H:i:s'),
								'created_user' => $user->user_id,
								'created_user_name' => $user->user_name,
								'url' => $images['original'],
								'file_type' => 'image',				
								'is_active' => true,
							));				
							$fileId = $fileGateway->add($file);
						}						
						$conn->insert(Tomato_Core_Db_Connection::getDbPrefix().'multimedia_file_set_assoc', array(
							'file_id' => $fileId,
							'set_id' => $setId,
						));						
					}
				}
				
				/**
				 * Execute hooks
				 * @since 2.0.2
				 */
				Tomato_Core_Hook_Registry::getInstance()->executeAction('Multimedia_Set_Add_Success', $setId);
				
				$this->_helper->getHelper('FlashMessenger')->addMessage(
					$this->view->translator('set_add_success')
				);
				$this->_redirect($this->view->serverUrl().$this->view->url(array(), 'multimedia_set_add'));
			}
		}
	}	
		
	public function editAction() 
	{			
		$this->view->addHelperPath(TOMATO_APP_DIR.DS.'modules'.DS.'upload'.DS.'views'.DS.'helpers', 'Upload_View_Helper_');
		$conn = Tomato_Core_Db_Connection::getMasterConnection();
		$setGateway = new Tomato_Modules_Multimedia_Model_SetGateway();
		$setGateway->setDbConnection($conn);
		
		$setId = $this->_request->getParam('set_id');
		$set = $setGateway->getSetById($setId);
		$this->view->assign('set', $set);
		$fileGateway = new Tomato_Modules_Multimedia_Model_FileGateway();
		$select = $conn->select()
					   ->from(array('f' => Tomato_Core_Db_Connection::getDbPrefix().'multimedia_file'))
					   ->joinInner(array('sc' => Tomato_Core_Db_Connection::getDbPrefix().'multimedia_file_set_assoc'), 'f.file_id = sc.file_id', array('set_id'))
					   ->where('sc.set_id = ?', $setId);
		$rs = $select->query()->fetchAll();
		$oldPhotos = new Tomato_Core_Model_RecordSet($rs, $fileGateway);
		$this->view->assign('oldPhotos', $oldPhotos);
		
		/**
		 * Register hooks
		 * @since 2.0.2
		 */
		Tomato_Core_Hook_Registry::getInstance()->register('Multimedia_Set_Edit_ShowSidebar', 
			array(new Tomato_Modules_Tag_Hooks_Tagger_Hook(), 'show', array($setId, 'set_id', 'multimedia_set_details', 'multimedia_tag_set')));
		
		if ($this->_request->isPost()) {
			$user = Zend_Auth::getInstance()->getIdentity();
			$setId = $this->_request->getPost('set_id');
			$title = $this->_request->getPost('title');
			$description = $this->_request->getPost('description');
			$setImage = $this->_request->getPost('setImage');
			$imageUrls = Zend_Json::decode($setImage);
			$photos = $this->_request->getPost('photos');
			
			$set = new Tomato_Modules_Multimedia_Model_Set(array(
				'set_id' => $setId,	
				'title' => $title,
				'slug' => Tomato_Core_Utility_String::removeSign($title, '-', true),
				'description' => $description,
				'created_date' => date('Y-m-d H:i:s'),
				'created_user_id' => $user->user_id,
				'created_user_name' => $user->user_name,
				'is_active' => true,
			));
			if (null != $imageUrls) {
				$set->image_square = $imageUrls['square'];
				$set->image_large = $imageUrls['large'];
				$set->image_general = $imageUrls['general'];
				$set->image_small = $imageUrls['small'];
				$set->image_crop = $imageUrls['crop'];
				$set->image_medium = $imageUrls['medium'];
			}
			$setGateway = new Tomato_Modules_Multimedia_Model_SetGateway();
			$setGateway->setDbConnection($conn);
			$setGateway->update($set);
			if ($setId > 0) {
				$conn->delete(Tomato_Core_Db_Connection::getDbPrefix().'multimedia_file_set_assoc', array('set_id = '.$conn->quote($setId)));
								
				if ($photos != null && is_array($photos)) {
					$fileGateway = new Tomato_Modules_Multimedia_Model_FileGateway();
					$fileGateway->setDbConnection($conn);
					
					foreach ($photos as $photo) {
						$images = Zend_Json::decode($photo);
						$fileId = null;
						if ($images['file_id']) {
							$fileId = $images['file_id'];
						} else {
							$file = new Tomato_Modules_Multimedia_Model_File(array(
								'image_crop' => $images['crop'],
								'image_general' => $images['general'],
								'image_medium' => $images['medium'],
								'image_original' => $images['original'],
								'image_small' => $images['small'],
								'image_square' => $images['square'],
								'image_large' => $images['large'],
								'created_date' => date('Y-m-d H:i:s'),
								'created_user' => $user->user_id,
								'created_user_name' => $user->user_name,
								'url' => $images['original'],
								'file_type' => 'image',				
								'is_active' => true,
							));
							$fileId = $fileGateway->add($file);
						}	
						$conn->insert(Tomato_Core_Db_Connection::getDbPrefix().'multimedia_file_set_assoc', array(
							'file_id' => $fileId,
							'set_id' => $setId,
						));
					}
				}
				
				/**
				 * Execute hooks
				 * @since 2.0.2
				 */
				Tomato_Core_Hook_Registry::getInstance()->executeAction('Multimedia_Set_Edit_Success', $setId);
				
				$this->_helper->getHelper('FlashMessenger')->addMessage(
					$this->view->translator('set_edit_success')
				);
				$this->_redirect($this->view->serverUrl().$this->view->url(array('set_id' => $setId), 'multimedia_set_edit'));
			}
		}
	}
	
	public function deleteAction() 
	{
		$this->_helper->getHelper('layout')->disableLayout();
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		
		if ($this->_request->isPost()) {
			$id = $this->_request->getPost('id');
			$conn = Tomato_Core_Db_Connection::getMasterConnection();			
			$gateway = new Tomato_Modules_Multimedia_Model_SetGateway();
			$gateway->setDbConnection($conn);
			$gateway->delete($id);
		}
		$this->_response->setBody('RESULT_OK');
	}
	
	public function activateAction() 
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		if ($this->_request->isPost()) {
			$id = $this->_request->getPost('id');
			$conn = Tomato_Core_Db_Connection::getMasterConnection();
			$gateway = new Tomato_Modules_Multimedia_Model_SetGateway();	
			$gateway->setDbConnection($conn);
			$gateway->toggleStatus($id);
			
			$status = $this->_request->getPost('status');
			$this->_response->setBody(1 - $status);
		}
	}
}
