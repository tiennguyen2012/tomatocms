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
 * @version 	$Id: CommentController.php 2040 2010-04-02 08:13:30Z hoangninh $
 */

class Comment_CommentController extends Zend_Controller_Action 
{
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

		$user = Zend_Auth::getInstance()->getIdentity();
		$paramsString = null;
		$exp = array();
		$commentGateway = new Tomato_Modules_Comment_Model_CommentGateway();
		$commentGateway->setDbConnection($conn);
		
		$commentSql = 'SELECT c.* FROM '.Tomato_Core_Db_Connection::getDbPrefix().'comment c 
						WHERE c.comment_id IN (SELECT MAX(t.comment_id) FROM '.Tomato_Core_Db_Connection::getDbPrefix().'comment t GROUP BY t.page_url)
						ORDER BY c.comment_id DESC';
		$rsComments = $conn->query($commentSql)->fetchAll();
		$comments = new Tomato_Core_Model_RecordSet($rsComments, $commentGateway);
		
		$numCommentSql = 'SELECT COUNT(DISTINCT page_url) as num_page_url FROM '.Tomato_Core_Db_Connection::getDbPrefix().'comment';
		$rsNumComments = $conn->query($numCommentSql)->fetch();
		$numComments = $rsNumComments->num_page_url;
		
		// Paginator
		$paginator = new Zend_Paginator(new Tomato_Core_Utility_PaginatorAdapter($comments, $numComments));
		$paginator->setCurrentPageNumber($pageIndex);
		$paginator->setItemCountPerPage($perPage);
		$this->view->assign('paginator', $paginator);
		$this->view->assign('paginatorOptions', array(
			'path' => $this->view->url(array(), 'comment_list'),
			'itemLink' => (null == $paramsString) ? 'page-%d' : 'page-%d?q='.$paramsString,
		));
		
		$this->view->assign('numComments', $numComments);
		$this->view->assign('comments', $comments);
	}
		
	public function threadAction()
	{
		$user = Zend_Auth::getInstance()->getIdentity();
		$this->view->assign('user', $user);
		
		$queryString = $this->_request->getParam('paramsString');
		$this->view->assign('paramsString', $queryString);
		$paramsString = base64_decode(rawurldecode($queryString));
		$params = Zend_Json::decode($paramsString);
		$pageUrl = (isset($params['page_url'])) ? $params['page_url'] : null;
		$perPage = 10;
		$pageIndex = $this->_request->getParam('pageIndex');
		if (null == $pageIndex || '' == $pageIndex) {
			$pageIndex = 1;
		}
		$start = ($pageIndex - 1) * $perPage;
		$this->view->assign('pageIndex', $pageIndex);
		$this->view->assign('pageUrl', $pageUrl);
		
		$conn = Tomato_Core_Db_Connection::getMasterConnection();
		$commentGateway = new Tomato_Modules_Comment_Model_CommentGateway();
		$commentGateway->setDbConnection($conn);
		
		$num = $conn->select()
					->from(array('c' => Tomato_Core_Db_Connection::getDbPrefix().'comment'), array('num_comments' => 'COUNT(*)'))
					->where('c.page_url = ?', $pageUrl);
		$row = $num->query()->fetch();
		$numComments = $row->num_comments;
		$this->view->assign('numComments', $numComments);
		
		$select = $conn->select()
						->from(array('c' => Tomato_Core_Db_Connection::getDbPrefix().'comment'))
						->where('c.page_url = ?', $pageUrl)
						->order('c.ordering')
						->limit($perPage, $start);
		$rs = $select->query()->fetchAll();
		$comments = new Tomato_Core_Model_RecordSet($rs, $commentGateway);
		$this->view->assign('comments', $comments);
		
		// Paginator
		$paginator = new Zend_Paginator(new Tomato_Core_Utility_PaginatorAdapter($comments, $numComments));
		$paginator->setCurrentPageNumber($pageIndex);
		$paginator->setItemCountPerPage($perPage);
		$this->view->assign('paginator', $paginator);
		$this->view->assign('paginatorOptions', array(
			'path' => $this->view->url(array('paramsString' => ''), 'comment_thread'),
			'itemLink' => 'page-%d/'.$queryString,
		));
	}
	
	public function addAction() 
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		
		$queryString = $this->_request->getParam('paramsString');
		$paramsString = base64_decode(rawurldecode($queryString));
		$params = Zend_Json::decode($paramsString);
		
		$conn = Tomato_Core_Db_Connection::getMasterConnection();
		$commentGateway = new Tomato_Modules_Comment_Model_CommentGateway();
		$commentGateway->setDbConnection($conn);
		
		if ($this->_request->isPost() && isset($params['page_url'])) {
			$title = $this->_request->getPost('tCommentTitle');
			$content = $this->_request->getPost('tCommentContent');
			$fullName = $this->_request->getPost('tCommentFullName');
			$email = $this->_request->getPost('tCommentEmail');
			$website = $this->_request->getPost('tCommentWebsite');
			$isActive = 1;
			$pageUrl = $params['page_url'];
			
			$reply2CommentId = (int)$this->_request->getPost('tCommentReply');
			
			$comment = new Tomato_Modules_Comment_Model_Comment(array(
				'title' 		=> $title,
				'content' 		=> $content,
				'full_name' 	=> $fullName,
				'web_site'		=> $website,
				'email' 		=> $email,
				'ip' 			=> $this->_request->getClientIp(),
				'created_date' 	=> date('Y-m-d H:i:s'),
				'is_active' 	=> $isActive,
				'reply_to' 		=> $reply2CommentId,
				'page_url'		=> $pageUrl,
			));
			if ($isActive == 1) {
				$comment->activate_date = date('Y-m-d H:i:s');
			}
			$commentId = $commentGateway->add($comment);
			
			$ordering = $conn->select()
							->from(array('c' => Tomato_Core_Db_Connection::getDbPrefix().'comment'), array('max_ordering' => 'MAX(c.ordering)'))
							->where('c.page_url = ?', $pageUrl)
							->query()
							->fetch();
			$ordering = $ordering->max_ordering;
			$depth = 0;
			$path = $commentId.'-';
			
			$reply2Comment = $commentGateway->getCommentById($reply2CommentId);
			
			if ($reply2Comment != null) {
				$maxOrdering = $conn->select()
									->from(array('c' => Tomato_Core_Db_Connection::getDbPrefix().'comment'), array('max_ordering' => 'MAX(c.ordering)'))
									->where('c.path LIKE ?', $reply2Comment->path.'%')
									->query()
									->fetch();
				$ordering = (null == $maxOrdering) 
								? $reply2Comment->ordering : $maxOrdering->max_ordering;
				$path = $reply2Comment->path.$path;
				$depth = $reply2Comment->depth + 1;
				$sqlUpdate = 'UPDATE t_comment SET ordering = ordering + 1
							WHERE page_url = '.$conn->quote($pageUrl)
							.' AND ordering > '.$conn->quote($ordering);
				$conn->query($sqlUpdate);
			}
			$conn->update(Tomato_Core_Db_Connection::getDbPrefix().'comment', 
						array(
							'ordering' => $ordering + 1,
							'depth'	=> $depth,
							'path' => $path,
						), array('comment_id = '.$conn->quote($commentId)));
			$this->_helper->getHelper('FlashMessenger')->addMessage(
				$this->view->translator('comment_add_success')
			);
			$this->_redirect($this->view->serverUrl().$this->view->url(array('paramsString' => $queryString), 'comment_thread'));	
		}
	}
	
	public function editAction()
	{
		$conn = Tomato_Core_Db_Connection::getMasterConnection();
		$id = $this->_request->getParam('comment_id');
		$commentGateway = new Tomato_Modules_Comment_Model_CommentGateway();
		$commentGateway->setDbConnection($conn);
		$comment = $commentGateway->getCommentById($id);
		$this->view->assign('comment', $comment);
		
		if (null == $comment) {
			return;
		}
		
		$params = array('page_url' => $comment->page_url);
		$queryString = rawurlencode(base64_encode(Zend_Json::encode($params)));
		if (null === $queryString) {
			$queryString = '';
		}
		$this->view->assign('queryString', $queryString);
		
		if ($this->_request->isPost()) {
			$title = $this->_request->getPost('title');
			$content = $this->_request->getPost('content');
			$fullName = $this->_request->getPost('fullName');
			$email = $this->_request->getPost('email');
			$webSite = $this->_request->getPost('website');
			$isActive = $this->_request->getPost('status');
			
			$comment->title = $title;
			$comment->content = $content;
			$comment->full_name = $fullName;
			$comment->email = $email;
			$comment->web_site = $webSite;
			$comment->is_active = $isActive;
			if ($comment->is_active != 1 && $isActive == 1) {
				$comment->activate_date = date('Y-m-d H:i:s');
			}
			$commentGateway->update($comment);
			$this->_helper->getHelper('FlashMessenger')->addMessage(
				$this->view->translator('comment_edit_success')
			);
			$this->_redirect($this->view->serverUrl().$this->view->url(array('comment_id' => $id), 'comment_edit'));
		}
	}
	
	public function deleteAction() 
	{
		$this->_helper->getHelper('layout')->disableLayout();
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		if ($this->_request->isPost()) {
			$id = $this->_request->getPost('id');
			$conn = Tomato_Core_Db_Connection::getMasterConnection();
			$gateway = new Tomato_Modules_Comment_Model_CommentGateway();
			$gateway->setDbConnection($conn);
			$comment = $gateway->getCommentById($id);
			
			if (null == $comment) {
				$this->_response->setBody('RESULT_NOT_FOUND');
				return;
			} 

			$gateway->delete($id);
			$data = array(
				'title' => $this->view->escape($comment->title),
			);
			
			$this->_response->setBody(Zend_Json::encode($data));
			return;
		}
		$this->_response->setBody('RESULT_NOT_OK');
	}
	
	public function activateAction() 
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		if ($this->_request->isPost()) {
			$id = $this->_request->getPost('id');
						
			$conn = Tomato_Core_Db_Connection::getMasterConnection();
			$gateway = new Tomato_Modules_Comment_Model_CommentGateway();	
			$gateway->setDbConnection($conn);
			$comment = $gateway->getCommentById($id);
			if (null == $comment) {
				$this->_response->setBody('RESULT_NOT_FOUND');
				return;
			}
			$comment->activate_date = date('Y-m-d H:i:s');
			$gateway->toggleActive($comment);
			$isActive = 1 - $comment->is_active;
			$data = array(
				'title' 	=> $this->view->escape($comment->title),
				'is_active'	=> $isActive,
			);
			$this->_response->setBody(Zend_Json::encode($data));			
		}
	}
}
