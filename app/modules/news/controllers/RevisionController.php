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
 * @version 	$Id: RevisionController.php 1948 2010-04-02 03:58:39Z huuphuoc $
 * @since		2.0.4
 */

class News_RevisionController extends Zend_Controller_Action 
{
	/* ========== Backend actions =========================================== */
	
	public function addAction() 
	{
		$this->_helper->getHelper('layout')->disableLayout();
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		
		if ($this->_request->isPost()) {
			$user = Zend_Auth::getInstance()->getIdentity();
			
			$articleId = $this->_request->getPost('articleId');
			$categoryId = $this->_request->getPost('category');
			$subTitle = $this->_request->getPost('subTitle');
			$title = $this->_request->getPost('title');
			$slug = $this->_request->getPost('slug');
			$description = $this->_request->getPost('description');
			$content = $this->_request->getPost('content');
			$author = $this->_request->getPost('author');
			$icons = $this->_request->getPost('icons'); 
			
			$articleIcons = "";
			if (count($icons) == 1 ) {
				$articleIcons = '{"'.$icons[0].'"}';
			}
			if (count($icons) == 2 ) {
				$articleIcons = '{"'.$icons[0].'","'.$icons[1].'"}';
			}
			
			$revision = new Tomato_Modules_News_Model_ArticleRevision(array(
				'article_id' => $articleId,
				'category_id' => $categoryId,
				'title' => $title,	
				'sub_title' => $subTitle,
				'slug' => $slug,
				'description' => $description,
				'content' => $content,
				'created_date' => date('Y-m-d H:i:s'),
				'created_user_id' => $user->user_id,
				'created_user_name' => $user->user_name,
				'author' => $author,
				'icons' => $articleIcons,
			));
			
			$conn = Tomato_Core_Db_Connection::getMasterConnection();
			$revisionGateway = new Tomato_Modules_News_Model_ArticleRevisionGateway();
			$revisionGateway->setDbConnection($conn);
			$revisionId = $revisionGateway->add($revision);
			
			$properties = array(
							'article_id' => $articleId,
							'category_id' => $categoryId,
						);
			$url = $this->view->serverUrl().$this->view->url($properties, 'news_article_details');
			$url .= '?preview=true&revision='.$revisionId;	
					 
			$this->_redirect($url);
		}
	}
	
	public function deleteAction()
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		$response = 'RESULT_NOT_OK';
		if ($this->_request->isPost()) {
			$id = $this->_request->getPost('revision_id');
						
			$conn = Tomato_Core_Db_Connection::getMasterConnection();
			$revisionGateway = new Tomato_Modules_News_Model_ArticleRevisionGateway();	
			$revisionGateway->setDbConnection($conn);
									
			// Delete revision
			$revisionGateway->delete($id);
						
			$response = 'RESULT_OK';
		}
		
		$this->_response->setBody($response);
	}
	
	public function detailsAction()
	{
		$conn = Tomato_Core_Db_Connection::getSlaveConnection();
		
		$revisionId = $this->_request->getParam('revision_id');
		
		$revisionGateway = new Tomato_Modules_News_Model_ArticleRevisionGateway();
		$revisionGateway->setDbConnection($conn);
		$revision = $revisionGateway->getArticleRevisionById($revisionId);
		if (null == $revision) {
			throw new Tomato_Core_Exception_NotFound();
		}
		
		$this->view->assign('revision', $revision);
	}
	
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
		
		$articleId = $this->_request->getParam('article_id');
		$articleGateway = new Tomato_Modules_News_Model_ArticleGateway();
		$articleGateway->setDbConnection($conn);
		$article = $articleGateway->getArticleById($articleId);
		if (null == $article) {
			throw new Tomato_Core_Exception_NotFound();
		}
		
		$this->view->assign('article', $article);
		
		$exp = array(
			'article_id'	=> $articleId,
		);
		
		$this->view->assign('exp', $exp);
		
		$revisionGateway = new Tomato_Modules_News_Model_ArticleRevisionGateway();
		$revisionGateway->setDbConnection($conn);
		$revisions = $revisionGateway->find($start, $perPage, $exp);
		$this->view->assign('revisions', $revisions);
		
		$numRevisions = $revisionGateway->count($exp);
		$this->view->assign('numRevisions', $numRevisions);
		
		// Paginator
		$paginator = new Zend_Paginator(new Tomato_Core_Utility_PaginatorAdapter($revisions, $numRevisions));
		$paginator->setCurrentPageNumber($pageIndex);
		$paginator->setItemCountPerPage($perPage);
		$this->view->assign('paginator', $paginator);
		$this->view->assign('paginatorOptions', array(
			'path' => $this->view->url(array('article_id' => $articleId), 'news_revision_list'),
			'itemLink' => 'page-%d',
		));
	}
	
	public function restoreAction()
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		$response = 'RESULT_NOT_OK';
		if ($this->_request->isPost()) {
			$id = $this->_request->getPost('revision_id');
						
			$conn = Tomato_Core_Db_Connection::getMasterConnection();
			$revisionGateway = new Tomato_Modules_News_Model_ArticleRevisionGateway();	
			$revisionGateway->setDbConnection($conn);
			$revision = $revisionGateway->getArticleRevisionById($id);
			
			if (null == $response) {
				$this->_response->setBody($response);
				return;
			}
			
			$articleGateway = new Tomato_Modules_News_Model_ArticleGateway();
			$articleGateway->setDbConnection($conn);
			
			// Update article
			$user = Zend_Auth::getInstance()->getIdentity();
			$where[] = 'article_id = '.$conn->quote($revision->article_id);
			$conn->update(Tomato_Core_Db_Connection::getDbPrefix().'news_article', array(
							'updated_user_id' => $user->user_id,
							'updated_user_name' => $user->user_name,
							'updated_date' => date('Y-m-d H:i:s'),
							'title' => $revision->title,
							'sub_title' => $revision->sub_title,
							'slug' => $revision->slug,
							'description' => $revision->description,
							'content' => $revision->content,
							'author' => $revision->author,
							'icons' => $revision->icons,
							'category_id' => $revision->category_id,
						), $where);
						
			// Delete this revision
			$revisionGateway->delete($id);
			$this->_helper->getHelper('FlashMessenger')
						->addMessage($this->view->translator('revision_restore_success'));			
			$response = $this->view->url(array('article_id' => $revision->article_id), 'news_article_edit');
		}
		
		$this->_response->setBody($response);
	}
}
