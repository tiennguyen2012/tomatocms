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
 * @version 	$Id: FileController.php 1955 2010-04-02 04:23:10Z hoangninh $
 */

class Multimedia_FileController extends Zend_Controller_Action 
{
	public function init() 
	{
		/**
		 * Register hooks
		 * @since 2.0.2
		 */
		Tomato_Core_Hook_Registry::getInstance()->register('Multimedia_File_Add_ShowSidebar', 
			array(new Tomato_Modules_Tag_Hooks_Tagger_Hook(), 'show', array(null, 'file_id', 'multimedia_file_details', 'multimedia_tag_file')));
		Tomato_Core_Hook_Registry::getInstance()->register('Multimedia_File_Add_Success',
			'Tomato_Modules_Tag_Hooks_Tagger_Hook::add');
		Tomato_Core_Hook_Registry::getInstance()->register('Multimedia_File_Edit_Success',
			'Tomato_Modules_Tag_Hooks_Tagger_Hook::add');
	}
	
	/* ========== Frontend actions ========================================== */
	
	/**
	 * @since 2.0.2
	 */
	public function detailsAction() 
	{
		$fileId = $this->_request->getParam('file_id');
		$conn = Tomato_Core_Db_Connection::getSlaveConnection();		
		$gateway = new Tomato_Modules_Multimedia_Model_FileGateway();
		$gateway->setDbConnection($conn);
		$file = $gateway->getFileById($fileId);
		
		if (null == $file) {
			throw new Tomato_Core_Exception_NotFound();
		}
		
		/**
		 * Show all notes
		 * @since 2.0.4
		 */
		$noteGateway = new Tomato_Modules_Multimedia_Model_NoteGateway($conn);
		$notes = $noteGateway->find(null, null, array('file_id' => $fileId, 'is_active' => 1));
		
		$this->view->assign('file', $file);
		$this->view->assign('notes', $notes);
	}	
	
	/* ========== Backend actions =========================================== */
		
	public function listAction() 
	{
		$conn = Tomato_Core_Db_Connection::getMasterConnection();
		
		$perPage = 20;
		$pageIndex = $this->_request->getParam('pageIndex');
		if (null == $pageIndex || '' == $pageIndex) {
			$pageIndex = 1;
		}
		$start = ($pageIndex - 1) * $perPage;
		$this->view->assign('pageIndex', $pageIndex);
		
		// Build file search expression
		$user = Zend_Auth::getInstance()->getIdentity();
		$paramsString = null;
		$exp = array(
			'created_user'	=> $user->user_id,
		);
		
		if ($this->_request->isPost()) {
			$id = $this->_request->getPost('fileId');
			$keyword = $this->_request->getPost('keyword');
			$findMyFiles = $this->_request->getPost('findMyFiles');
			$findPhotos = $this->_request->getPost('findPhotos');
			$findClips = $this->_request->getPost('findClips');
			if ($keyword) {
				$exp['keyword'] = $keyword;
			}
			if ($id) {
				$exp['file_id'] = $id;
			}
			if (null == $findMyFiles) {
				$exp['created_user'] = null;
			}
			if ($findPhotos) {
				$exp['photo'] = $findPhotos;
			}
			if ($findClips) {
				$exp['clip'] = $findClips;
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
		
		$fileGateway = new Tomato_Modules_Multimedia_Model_FileGateway();
		$fileGateway->setDbConnection($conn);
		$files = $fileGateway->find($start, $perPage, $exp);
		$numFiles = $fileGateway->count($exp);
		
		// Paginator
		$paginator = new Zend_Paginator(new Tomato_Core_Utility_PaginatorAdapter($files, $numFiles));
		$paginator->setCurrentPageNumber($pageIndex);
		$paginator->setItemCountPerPage($perPage);
		$this->view->assign('paginator', $paginator);
		$paginatorOptions = array(
			'path' => $this->view->url(array(), 'multimedia_file_list'),
			'itemLink' => (null == $paramsString) ? 'page-%d' : 'page-%d?q='.$paramsString,
		);
		$this->view->assign('paginatorOptions', $paginatorOptions);
		
		/**
		 * Support searching from other page
		 * For example, search files at adding set page
		 * @since 2.0.2
		 */
		if (isset($exp['format']) && $exp['format'] == 'JSON') {
			$this->_helper->getHelper('viewRenderer')->setNoRender();
			$this->_helper->getHelper('layout')->disableLayout();

			$res = array(
				'files' => array(),
				'paginator' => $this->view->paginator()->slide($paginator, $paginatorOptions),
			);
			foreach ($files as $f) {
				$res['files'][] = array(
					'file_id' => $f->file_id,
					'original' => $f->image_original,
					'square' => $f->image_square,
					'general' => $f->image_general,
					'small' => $f->image_small,
					'crop' => $f->image_crop,
					'medium' => $f->image_medium,
					'large' => $f->image_large,
					'url' => $f->url,
					'html_code' => $f->html_code,
					'file_type' => $f->file_type,
				);
			}
			$this->_response->setBody(Zend_Json::encode($res));
		} else {
			$this->view->assign('numFiles', $numFiles);
			$this->view->assign('files', $files);
			$this->view->assign('exp', $exp);
		}
	}
		
	public function deleteAction() 
	{
		$this->_helper->getHelper('layout')->disableLayout();
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		
		if ($this->_request->isPost()) {
			$id = $this->_request->getPost('id');
			$conn = Tomato_Core_Db_Connection::getMasterConnection();
			
			$gateway = new Tomato_Modules_Multimedia_Model_FileGateway();
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
			$gateway = new Tomato_Modules_Multimedia_Model_FileGateway();	
			$gateway->setDbConnection($conn);
			$gateway->toggleStatus($id);
			
			$status = $this->_request->getPost('status');
			$this->_response->setBody(1 - $status);
		}
	}
	
	public function editAction() 
	{
		$this->view->addHelperPath(TOMATO_APP_DIR.DS.'modules'.DS.'upload'.DS.'views'.DS.'helpers', 'Upload_View_Helper_');
		
		$conn = Tomato_Core_Db_Connection::getMasterConnection();
		$fileGateway = new Tomato_Modules_Multimedia_Model_FileGateway();
		$fileGateway->setDbConnection($conn);
		
		$fileId = $this->_request->getParam('file_id');
		$file = $fileGateway->getFileById($fileId);
		$this->view->assign('file', $file);
		
		$imageData = array(
						'image_square' => $file->image_square,
						'image_crop' => $file->image_crop,
						'image_general' => $file->image_general,
						'image_small' => $file->image_small,
						'image_medium' => $file->image_medium,
						'image_large' => $file->image_large,
					);
		$this->view->assign('imageData', rawurlencode(base64_encode(Zend_Json::encode($imageData))));
		/**
		 * Register hooks
		 * @since 2.0.2
		 */
		Tomato_Core_Hook_Registry::getInstance()->register('Multimedia_File_Edit_ShowSidebar', 
			array(new Tomato_Modules_Tag_Hooks_Tagger_Hook(), 'show', array($fileId, 'file_id', 'multimedia_file_details', 'multimedia_tag_file')));
				
		if ($this->_request->isPost()) {
			$keySection = $this->_request->getPost('keySection');
			$newValue = $this->_request->getPost('value');
			if ($keySection && $newValue) {
				$arr = explode("_", $keySection);			
				switch ($arr[0]) {
					case 'title':
						$fileGateway->updateDescription($arr[1], $newValue);
						break;
					case 'description':
						$fileGateway->updateDescription($arr[1], null, $newValue);
						break;
				}
				$this->_response->setBody('RESULT_OK');
			} else {
				$user = Zend_Auth::getInstance()->getIdentity();
				$fileId = $this->_request->getPost('file_id');
				$title = $this->_request->getPost('title');
				$description = $this->_request->getPost('description');
				$image = $this->_request->getPost('image');
				$fileType = $this->_request->getPost('file_type');
				$imageUrls = Zend_Json::decode($image);
				$url = $this->_request->getPost('url');
				$htmlCode = $this->_request->getPost('html_code');
				
				$file = new Tomato_Modules_Multimedia_Model_Set(array(
					'file_id' => $fileId,
					'title' => $title,
					'slug' => Tomato_Core_Utility_String::removeSign($title, '-', true),
					'description' => $description,
					'url' => $url,
					'html_code' => $htmlCode,
					'file_type' => $fileType,		
				));
				if (null != $imageUrls) {
					$file->image_square = $imageUrls['square'];
					$file->image_large = $imageUrls['large'];
					$file->image_general = $imageUrls['general'];
					$file->image_small = $imageUrls['small'];
					$file->image_crop = $imageUrls['crop'];
					$file->image_medium = $imageUrls['medium'];
				}
				$result = $fileGateway->update($file);
				
				/**
				 * Execute hooks
			 	 * @since 2.0.2
			 	 */
				Tomato_Core_Hook_Registry::getInstance()->executeAction('Multimedia_File_Edit_Success', $fileId);
			
				$this->_helper->getHelper('FlashMessenger')->addMessage(
					$this->view->translator('file_edit_success')
				);
				$this->_redirect($this->view->serverUrl().$this->view->url(array('file_id' => $fileId), 'multimedia_file_edit'));
			}
		}
	}
	
	public function addAction() 
	{
		$this->view->addHelperPath(TOMATO_APP_DIR.DS.'modules'.DS.'upload'.DS.'views'.DS.'helpers', 'Upload_View_Helper_');
		
		$conn = Tomato_Core_Db_Connection::getMasterConnection();		
		if ($this->_request->isPost()) {
			$user = Zend_Auth::getInstance()->getIdentity();
			
			$title = $this->_request->getPost('title');
			$description = $this->_request->getPost('description');
			$image = $this->_request->getPost('image');
			$fileType = $this->_request->getPost('file_type');
			$imageUrls = Zend_Json::decode($image);
			$url = $this->_request->getPost('url');
			$htmlCode = $this->_request->getPost('html_code');
			
			$file = new Tomato_Modules_Multimedia_Model_Set(array(
				'title' => $title,
				'slug' => Tomato_Core_Utility_String::removeSign($title, '-', true),
				'description' => $description,
				'created_date' => date('Y-m-d H:i:s'),
				'created_user' => $user->user_id,
				'created_user_name' => $user->user_name,
				'url' => $url,
				'html_code' => $htmlCode,
				'file_type' => $fileType,		
				'is_active' => true,
			));
			if (null != $imageUrls) {
				$file->image_square = $imageUrls['square'];
				$file->image_large = $imageUrls['large'];
				$file->image_general = $imageUrls['general'];
				$file->image_small = $imageUrls['small'];
				$file->image_crop = $imageUrls['crop'];
				$file->image_medium = $imageUrls['medium'];
			}
			$fileGateway = new Tomato_Modules_Multimedia_Model_FileGateway();
			$fileGateway->setDbConnection($conn);
			$fileId = $fileGateway->add($file);
			if ($fileId > 0) {
				/**
				 * Execute hooks
				 * @since 2.0.2
				 */
				Tomato_Core_Hook_Registry::getInstance()->executeAction('Multimedia_File_Add_Success', $fileId);
				
				$this->_helper->getHelper('FlashMessenger')->addMessage(
					$this->view->translator('file_add_success')
				);
				$this->_redirect($this->view->serverUrl().$this->view->url(array(), 'multimedia_file_add'));
			}
		}
	}
	
	/**
	 * Image Editor
	 * @since 2.0.4
	 */
	public function editorAction()
	{
		$this->_helper->getHelper('layout')->disableLayout();

		$load = $this->_request->getPost('load');
		if ($this->_request->isPost() && $load == 'edit') {
			$this->_helper->getHelper('viewRenderer')->setNoRender();
			$response = 'RESLUT_NOT_OK';
			
			$action = $this->_request->getPost('action');
			$source = $this->_request->getPost('source');
			$source = explode('?', $source);
			$source = $source[0];
			
			$type = null;
			$desData = $this->_request->getPost('des');
			if ($desData != null) {
				$desData = explode('|', $desData);
				$des = $desData[0];
				$type = $desData[1];
			} else {
				$des = $source;
			}
			$fileId = $this->_request->getPost('file_id');
			$maxWidth = $this->_request->getPost('max_width');
			
			// Remove script filename from base URL
			$baseUrl = $this->view->baseUrl();
			if (isset($_SERVER['SCRIPT_NAME']) && ($pos = strripos($baseUrl, basename($_SERVER['SCRIPT_NAME']))) !== false) {
	            $baseUrl = substr($baseUrl, 0, $pos);
	        }
		        
			if (strpos($des, $baseUrl) === false) {
				return;
			}
			
			$ret = $des;
			$des = TOMATO_ROOT_DIR . DS .str_replace($baseUrl, '', $des);
			$source = TOMATO_ROOT_DIR . DS .str_replace($baseUrl, '', $source);
			
			// Get config
			$config = Tomato_Core_Module_Config::getConfig('upload');
			$tool = $config->thumbnail->tool;
			$service = null;
			switch (strtolower($tool)) {
				case 'imagemagick':
					$service = new Tomato_Modules_Upload_Services_ImageMagick();
					break;
				case 'gd':
					$service = new Tomato_Modules_Upload_Services_GD();
					break;
			}
			
			$service->setFile($source);
			switch ($action) {
				case 'rotate':
					$degrees = $this->_request->getPost('degrees');
					
					$service->rotate($des, $degrees);
					break;
				case 'flip':
					$mode = $this->_request->getPost('mode');
					
					$service->flip($des, $mode);
					break;
				case 'crop':
					$info = getimagesize($source);
					$width = $info[0];
					
					$cropX = $this->_request->getPost('x1');
					$cropY = $this->_request->getPost('y1');
					
					if ($width > $maxWidth) {
						$ratio = $width / $maxWidth;
						$cropX = floor($cropX * $ratio);
						$cropY = floor($cropY * $ratio);
					}
			
					$newWidth = $this->_request->getPost('width');
					$newHeight = $this->_request->getPost('height');
					
					$service->crop($des, $newWidth, $newHeight, false, $cropX, $cropY);
					break;
			}
						
			$response = array(
							'type' => $type,
							'image_url' => $ret,
						);
			$response = Zend_Json::encode($response);
			$this->_response->setBody($response);
			return;
		}
		
		$conn = Tomato_Core_Db_Connection::getMasterConnection();
		$fileGateway = new Tomato_Modules_Multimedia_Model_FileGateway();
		$fileGateway->setDbConnection($conn);
		
		$fileId = $this->_request->getParam('file_id');
		$file = $fileGateway->getFileById($fileId);
		$this->view->assign('file', $file);
		$data = array(
			'image_square' => $file->image_square,
			'image_general' => $file->image_general,
			'image_small' => $file->image_small,
			'image_crop' => $file->image_crop,
			'image_medium' => $file->image_medium,
			'image_large' => $file->image_large,
		);
		$this->view->assign('data', $data);
		
		$dataString = rawurlencode(base64_encode(Zend_Json::encode($data)));
		$this->view->assign('dataString', $dataString);
		
		// Remove script filename from base URL
		$baseUrl = $this->view->baseUrl();
		if (isset($_SERVER['SCRIPT_NAME']) && ($pos = strripos($baseUrl, basename($_SERVER['SCRIPT_NAME']))) !== false) {
            $baseUrl = substr($baseUrl, 0, $pos);
        }
        $this->view->assign('baseUrl', $baseUrl);
	}
}
