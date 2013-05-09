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
 * @copyright	Copyright (c) 2008-2009 TIG Corporation (http://www.tig.vn)
 * @license		GNU GPL license, see http://www.tomatocms.com/license.txt or license.txt
 * @version 	$Id: Widget.php 1876 2010-03-31 02:48:08Z hoangninh $
 */

class Tomato_Modules_Comment_Widgets_Comment_Widget extends Tomato_Core_Widget 
{
	protected function _prepareShow() 
	{
		$perPage = $this->_request->getParam('limit', 10);
		$allowComment = $this->_request->getParam('allow_comment');
		switch ($allowComment) {
			case 1:
				$allowComment = true;
				break;
			case 2:
				$user = Zend_Auth::getInstance()->getIdentity();
				$allowComment = (null == $user) ? false : true;
				break;
			case 0:
			default:
				$allowComment = false;
				break;	
		}
		
		$allowComment = ($allowComment == 1) ? true : false;
		$isPreviewing = $this->_request->getParam(Tomato_Core_Layout::PREVIEW_MODE_PARAM);
		$isPreviewing = $isPreviewing ? true : false;
		
		$showAvatar = $this->_request->getParam('show_avatar');
		$showAvatar = ($showAvatar == 1) ? true : false;
		
		$pageUrl = $this->_request->getPathInfo();
		$pageUrl = rtrim($pageUrl, '/').'/';
		$avatarSize = $this->_request->getParam('avatar_size');
		
		$message = $this->_request->getParam('message');
		
		$this->_view->assign('isPreviewing', $isPreviewing);
		$this->_view->assign('limit', $perPage);
		$this->_view->assign('allowComment', $allowComment);
		$this->_view->assign('showAvatar', $showAvatar);
		$this->_view->assign('pageUrl', $pageUrl);
		$this->_view->assign('avatarSize', $avatarSize);
		$this->_view->assign('message', $message);
		
		$pageIndex = $this->_request->getParam('pageIndex');
		if (null == $pageIndex || '' == $pageIndex) {
			$pageIndex = 1;
		}
		$start = ($pageIndex - 1) * $perPage;
		$this->_view->assign('pageIndex', $pageIndex);
		
		$conn = Tomato_Core_Db_Connection::getMasterConnection();
		$commentGateway = new Tomato_Modules_Comment_Model_CommentGateway();
		$commentGateway->setDbConnection($conn);
		
		$select = $conn->select()
					->from(array('c' => Tomato_Core_Db_Connection::getDbPrefix().'comment'), array('num_comments' => 'COUNT(*)'))
					->where('c.page_url = ?', $pageUrl)
					->where('c.is_active = ?', 1);
		$numComments = $select->query()->fetch()->num_comments;
		
		$select = $conn->select()
						->from(array('c' => Tomato_Core_Db_Connection::getDbPrefix().'comment'))
						->where('c.page_url = ?', $pageUrl)
						->where('c.is_active = ?', 1)
						->order('c.ordering')
						->limit($perPage, $start);
		$rs = $select->query()->fetchAll();
		$comments = new Tomato_Core_Model_RecordSet($rs, $commentGateway);
		$this->_view->assign('comments', $comments);
		
		// Paginator
		$paginator = new Zend_Paginator(new Tomato_Core_Utility_PaginatorAdapter($comments, $numComments));
		$paginator->setCurrentPageNumber($pageIndex);
		$paginator->setItemCountPerPage($perPage);
		$this->_view->assign('paginator', $paginator);
		$this->_view->assign('paginatorOptions', array(
			'path' => $_SERVER['REQUEST_URI'],
			'itemLink' => 'javascript: Tomato.Modules.Comment.Widgets.Comment.loadComments(%d);',
		));		
		
		$ok = true;
		if ($this->_request->isPost('tCommentEmail') && $allowComment) {
			$url = $this->_request->getPost('tCommentUrl');
			$title = $this->_request->getPost('tCommentTitle');
			$content = $this->_request->getPost('tCommentContent');
			$fullName = $this->_request->getPost('tCommentFullName');
			$email = $this->_request->getPost('tCommentEmail');
			$website = $this->_request->getPost('tCommentWebsite');
			$reply2CommentId = (int)$this->_request->getPost('tCommentReply');
			
			$comment = new Tomato_Modules_Comment_Model_Comment(array(
				'title' 		=> $title,
				'content' 		=> $content,
				'full_name' 	=> $fullName,
				'email' 		=> $email,
				'web_site'		=> $website,
				'ip' 			=> $this->_request->getClientIp(),
				'created_date' 	=> date('Y-m-d H:i:s'),
				'is_active' 	=> 0,
				'reply_to' 		=> $reply2CommentId,
				'page_url'		=> $pageUrl,
			));
			
			$ok = false;
			$moduleConfig = Tomato_Core_Module_Config::getConfig('comment');	
			if ($moduleConfig != null && $moduleConfig->akismet->api_key && $moduleConfig->akismet->api_key != '') {
				$akismetService = new Zend_Service_Akismet($moduleConfig->akismet->api_key, Tomato_Core_Config::getConfig()->web->url);
     			$params = array(
     				'user_ip' => $comment->ip,
     				'user_agent' => $this->_request->getServer('HTTP_USER_AGENT'),
     				'comment_type' => 'comment',
     				'comment_author' => $comment->full_name,
     				'comment_author_email' => $comment->email,
     				'comment_content' => $comment->content, 
     			);
				$ok = ($akismetService->verifyKey() && !$akismetService->isSpam($params));
			} else {
				$ok = true;
			}
			if ($ok) {
				$commentId = $commentGateway->add($comment);
				$ordering = $conn->select()
							->from(array('c' => Tomato_Core_Db_Connection::getDbPrefix().'comment'), array('max_ordering' => 'MAX(c.ordering)'))
							->where('c.page_url = ?', $pageUrl)
							->query()
							->fetch();
				$ordering = $ordering->max_ordering;
				$depth = 0;
				$path = $commentId.'-';
				if ($reply2CommentId) {
					$reply2Comment = $conn->select()
										->from(array('c' => Tomato_Core_Db_Connection::getDbPrefix().'comment'))
										->where('c.comment_id = ?', $reply2CommentId)
										->limit(1)->query()->fetch();	
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
									WHERE page_url = '.$conn->quote($pageUrl).' AND ordering > '.$conn->quote($ordering);
						$conn->query($sqlUpdate);
					}
				}
				$conn->update(Tomato_Core_Db_Connection::getDbPrefix().'comment', 
					array(
						'ordering' => $ordering + 1,
						'depth'	=> $depth,
						'path' => $path,
					), array('comment_id = '.$conn->quote($commentId)));

				$flashMsgHelper = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');
				$flashMsgHelper->addMessage($this->_view->translator()->widget('send_comment_success'));	
					
				// Redirect to the original page
				$helperRedirector = Zend_Controller_Action_HelperBroker::getStaticHelper('Redirector');
				$helperRedirector->gotoUrl($this->_view->APP_URL.$pageUrl.'#commentForm');	
			}		
		}
		$this->_view->assign('ok', $ok);
	}
	
	protected function _prepareLoad() 
	{
		$pageUrl = $this->_request->getParam('page_url');
		$perPage = $this->_request->getParam('limit', 10);
		
		$showAvatar = $this->_request->getParam('show_avatar');
		$showAvatar = ($showAvatar == 1) ? true : false;
		
		$avatarSize = $this->_request->getParam('avatar_size');
		
		$this->_view->assign('showAvatar', $showAvatar);
		$this->_view->assign('avatarSize', $avatarSize);
		
		$pageIndex = $this->_request->getParam('pageIndex');
		if (null == $pageIndex || '' == $pageIndex) {
			$pageIndex = 1;
		}
		$start = ($pageIndex - 1) * $perPage;
		
		$conn = Tomato_Core_Db_Connection::getMasterConnection();
		$commentGateway = new Tomato_Modules_Comment_Model_CommentGateway();
		$commentGateway->setDbConnection($conn);
		
		$select = $conn->select()
					->from(array('c' => Tomato_Core_Db_Connection::getDbPrefix().'comment'), array('num_comments' => 'COUNT(*)'))
					->where('c.page_url = ?', $pageUrl)
					->where('c.is_active = ?', 1);
		$numComments = $select->query()->fetch()->num_comments;
		
		$select = $conn->select()
						->from(array('c' => Tomato_Core_Db_Connection::getDbPrefix().'comment'))
						->where('c.page_url = ?', $pageUrl)
						->where('c.is_active = ?', 1)
						->order('c.ordering')
						->limit($perPage, $start);
		$rs = $select->query()->fetchAll();
		$comments = new Tomato_Core_Model_RecordSet($rs, $commentGateway);
		$this->_view->assign('comments', $comments);
		
		// Paginator
		$paginator = new Zend_Paginator(new Tomato_Core_Utility_PaginatorAdapter($comments, $numComments));
		$paginator->setCurrentPageNumber($pageIndex);
		$paginator->setItemCountPerPage($perPage);
		$this->_view->assign('paginator', $paginator);
		$this->_view->assign('paginatorOptions', array(
			'path' => $_SERVER['REQUEST_URI'],
			'itemLink' => 'javascript: Tomato.Modules.Comment.Widgets.Comment.loadComments(%d);',
		));		
	}
}
