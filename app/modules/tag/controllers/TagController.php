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
 * @version 	$Id: TagController.php 1545 2010-03-10 07:46:33Z huuphuoc $
 * @since		2.0.2
 */

class Tag_TagController extends Zend_Controller_Action 
{
	/* ========== Frontend actions ========================================== */
	
	public function suggestAction()
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		$q = $this->_request->getParam('q');
		$limit = $this->_request->getParam('limit', 10);
		$conn = Tomato_Core_Db_Connection::getSlaveConnection();
		$tagGateway = new Tomato_Modules_Tag_Model_TagGateway();
		$tagGateway->setDbConnection($conn);
		$tags = $tagGateway->find($q, $limit, 0);
		$return = '';
		foreach ($tags as $tag) {
			$return .= $tag->tag_text.'|'.$tag->tag_id . "\n";
		}
		$this->_response->setBody($return);  
	}
	
	public function detailsAction() 
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();

		$tagId = $this->_request->getParam('tag_id');
		$routeName = $this->_request->getParam('details_route_name');
		$router = Zend_Controller_Front::getInstance()->getRouter();
		if ($router->hasRoute($routeName)) {
			$route = $router->getRoute($routeName);
			if ($route instanceof Zend_Controller_Router_Route_Regex) {
				$defaults = $route->getDefaults();
				$this->_forward($defaults['action'], $defaults['controller'], $defaults['module'], array('tag_id' => $tagId, 'details_route_name' => $routeName));
			}
		}
	}
	
	/* ========== Backend actions =========================================== */
	
	public function listAction()
	{
		$perPage = 100;
		$pageIndex = $this->_request->getParam('page_index');
		if (null == $pageIndex || '' == $pageIndex) {
			$pageIndex = 1;
		}
		$offset = ($pageIndex - 1) * $perPage;
		
		$paramsString = null;
		$keyword = '';
		if ($this->_request->isPost()) {
			$keyword = $this->_request->getPost('keyword');
			if ($keyword) {
				$paramsString = rawurlencode(base64_encode($keyword));
			}
		} else {
			$paramsString = $this->_request->getParam('q');
			if (null != $paramsString) {
				$keyword = rawurldecode(base64_decode($paramsString));
			} else {
				$paramsString = rawurlencode(base64_encode($keyword));
			}
		}
		
		$this->view->assign('keyword', $keyword);
		
		$conn = Tomato_Core_Db_Connection::getMasterConnection();
		$tagGateway = new Tomato_Modules_Tag_Model_TagGateway();
		$tagGateway->setDbConnection($conn);
		$tags = $tagGateway->find($keyword, $perPage, $offset);
		$this->view->assign('tags', $tags);
		
		$numTags = $tagGateway->count($keyword);
		$this->view->assign('numTags', $numTags);

		// Paginator
		$paginator = new Zend_Paginator(new Tomato_Core_Utility_PaginatorAdapter($tags, $numTags));
		$paginator->setCurrentPageNumber($pageIndex);
		$paginator->setItemCountPerPage($perPage);
		$this->view->assign('paginator', $paginator);
		$this->view->assign('paginatorOptions', array(
			'path' => $this->view->url(array(), 'tag_tag_list'),
			'itemLink' => (null == $paramsString) ? 'page-%d' : 'page-%d?q='.$paramsString,
		));		
	}
	
	public function addAction()
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		if ($this->_request->isPost()) {
			$text = $this->_request->getPost('keyword');
			if ($text != null && $text != '') {
				$conn = Tomato_Core_Db_Connection::getMasterConnection();
				$tagGateway = new Tomato_Modules_Tag_Model_TagGateway();
				$tagGateway->setDbConnection($conn);
				
				// Check whether the tag exists or not
				if (!$tagGateway->exist($text)) {
					$tagGateway->add(new Tomato_Modules_Tag_Model_Tag(
						array(
							'tag_text' => $text,
						)
					));
					
					// Redirect
					$this->_helper->getHelper('FlashMessenger')
						->addMessage($this->view->translator('tag_add_success'));
				}		
			}
			$this->_redirect($this->view->serverUrl().$this->view->url(array(), 'tag_tag_list'));
		}
	}
	
	public function deleteAction()
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		if ($this->_request->isPost()) {
			$tagId = $this->_request->getParam('id');
			$conn = Tomato_Core_Db_Connection::getMasterConnection();
			$tagGateway = new Tomato_Modules_Tag_Model_TagGateway();
			$tagGateway->setDbConnection($conn);
			$tagGateway->delete($tagId);
			
			$this->_response->setBody('RESULT_OK');
		}
	}
}
