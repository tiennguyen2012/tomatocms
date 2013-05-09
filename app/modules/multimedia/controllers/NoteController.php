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
 * @version 	$Id: NoteController.php 1862 2010-03-31 02:07:01Z huuphuoc $
 * @since		2.0.4
 */

class Multimedia_NoteController extends Zend_Controller_Action
{
	/* ========== Frontend actions ========================================== */
	
	public function addAction()
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		if ($this->_request->isPost()) {
			$conn = Tomato_Core_Db_Connection::getMasterConnection();
			$gateway = new Tomato_Modules_Multimedia_Model_NoteGateway($conn);

			$userId = null;
			$userName = null;
			if (Zend_Auth::getInstance()->hasIdentity()) {			
				$user = Zend_Auth::getInstance()->getIdentity();
				$userId = $user->user_id;
				$userName = $user->user_name;
			}
			
			$note = new Tomato_Modules_Multimedia_Model_Note(array(
				'file_id' => $this->_request->getPost('fileId'),
				'top' => $this->_request->getPost('top'),
				'left' => $this->_request->getPost('left'),
				'width' => $this->_request->getPost('width'),
				'height' => $this->_request->getPost('height'),
				'content' => $this->_request->getPost('content'),
				'user_id' => $userId,
				'user_name' => $userName,
			));
			$noteId = $gateway->add($note);
			$this->_response->setBody($noteId);
		}
	}
	
	/* ========== Backend actions =========================================== */
	
	public function listAction()
	{
		$conn = Tomato_Core_Db_Connection::getMasterConnection();

		$gateway = new Tomato_Modules_Multimedia_Model_NoteGateway();
		$gateway->setDbConnection($conn);
		
		$perPage = 20;
		$pageIndex = $this->_request->getParam('page_index');
		if (null == $pageIndex || '' == $pageIndex) {
			$pageIndex = 1;
		}
		$start = ($pageIndex - 1) * $perPage;
		$notes = $gateway->find($start, $perPage);
		
		$numNotes = $gateway->count();
		$this->view->assign('numNotes', $numNotes);
		
		// Paginator
		$paginator = new Zend_Paginator(new Tomato_Core_Utility_PaginatorAdapter($notes, $numNotes));
		$paginator->setCurrentPageNumber($pageIndex);
		$paginator->setItemCountPerPage($perPage);
		$this->view->assign('paginator', $paginator);
		$this->view->assign('paginatorOptions', array(
			'path' => $this->view->url(array(), 'multimedia_note_list'),
			'itemLink' => 'page-%d',
		));
		
		$this->view->assign('notes', $notes);
	}
	
	public function editAction()
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		if ($this->_request->isPost()) {
			$conn = Tomato_Core_Db_Connection::getMasterConnection();
			$gateway = new Tomato_Modules_Multimedia_Model_NoteGateway($conn);

			$user = Zend_Auth::getInstance()->getIdentity();
			
			$note = new Tomato_Modules_Multimedia_Model_Note(array(
				'note_id' => $this->_request->getPost('id'),
				'top' => $this->_request->getPost('top'),
				'left' => $this->_request->getPost('left'),
				'width' => $this->_request->getPost('width'),
				'height' => $this->_request->getPost('height'),
				'content' => $this->_request->getPost('content'),
				'user_id' => $user->user_id,
				'user_name' => $user->user_name,
			));
			$gateway->update($note);
			$this->_response->setBody('RESULT_OK');
		}
	}
	
	public function deleteAction()
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		if ($this->_request->isPost()) {
			$noteId = $this->_request->getPost('id');
			$conn = Tomato_Core_Db_Connection::getMasterConnection();
			$gateway = new Tomato_Modules_Multimedia_Model_NoteGateway($conn);
			$gateway->delete($noteId);

			$this->_response->setBody('RESULT_OK');
		}
	}
	
	public function activateAction()
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		if ($this->_request->isPost()) {
			$id = $this->_request->getPost('id');
			$status = $this->_request->getPost('status');
			$status = ($status == 1) ? 0 : 1;
			
			$conn = Tomato_Core_Db_Connection::getMasterConnection();
			$gateway = new Tomato_Modules_Multimedia_Model_NoteGateway();	
			$gateway->setDbConnection($conn);
			$gateway->updateStatus($id, $status);
			
			$this->_response->setBody($status);
		}
	}
}
