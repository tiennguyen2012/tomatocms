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
 * @version 	$Id: TagController.php 1582 2010-03-11 06:50:17Z huuphuoc $
 * @since		2.0.2
 */

class News_TagController extends Zend_Controller_Action
{
	/**
	 * Show list of articles by given tag
	 * @since 2.0.2
	 */
	public function articleAction() 
	{
		$tagId = $this->_request->getParam('tag_id');
		$detailsRouteName = $this->_request->getParam('details_route_name');
		$perPage = 20;
		$pageIndex = $this->_request->getParam('page_index');
		if (null === $pageIndex) {
			$pageIndex = 1;
		}
		$start = ($pageIndex - 1) * $perPage;
		
		$conn = Tomato_Core_Db_Connection::getSlaveConnection();
		
		$tagGateway = new Tomato_Modules_Tag_Model_TagGateway();
		$tagGateway->setDbConnection($conn);
		$tag = $tagGateway->getTagById($tagId);
		
		if (null == $tag) {
			throw new Tomato_Core_Exception_NotFound();
		}
		
		$tag->details_route_name = $detailsRouteName;
		$this->view->assign('tag', $tag);
		
		// Get the list of articles tagged by the tag
		$gateway = new Tomato_Modules_News_Model_ArticleGateway();
		$gateway->setDbConnection($conn);
		$select = $conn->select()
					->from(array('a' => Tomato_Core_Db_Connection::getDbPrefix().'news_article'), array('article_id', 'category_id', 'title', 'slug', 'description', 'image_general', 'icons'))
					->joinInner(array('ti' => Tomato_Core_Db_Connection::getDbPrefix().'tag_item_assoc'), 'a.article_id = ti.item_id', array())
					->where('ti.tag_id = ?', $tagId)
					->where('ti.item_name = ?', 'article_id')
					->where('a.status = ?', 'active')
					->group('a.article_id')
					->order('a.article_id DESC')
					->limit($perPage, $start);
		$rs = $select->query()->fetchAll();
		$articles = new Tomato_Core_Model_RecordSet($rs, $gateway);
		$this->view->assign('articles', $articles);
		
		// Count number of articles tagged by the tag
		$select = $conn->select()
					->from(array('a' => Tomato_Core_Db_Connection::getDbPrefix().'news_article'), array('num_articles' => 'COUNT(article_id)'))
					->joinInner(array('ti' => Tomato_Core_Db_Connection::getDbPrefix().'tag_item_assoc'), 'a.article_id = ti.item_id', array())
					->where('ti.tag_id = ?', $tagId)
					->where('ti.item_name = ?', 'article_id')
					->where('a.status = ?', 'active');
		$numArticles = $select->query()->fetch()->num_articles;
		
		// Paginator
		$paginator = new Zend_Paginator(new Tomato_Core_Utility_PaginatorAdapter($articles, $numArticles));
		$paginator->setCurrentPageNumber($pageIndex);
		$paginator->setItemCountPerPage($perPage);
		$this->view->assign('paginator', $paginator);
		$this->view->assign('paginatorOptions', array(
			'path' => $this->view->url($tag->getProperties(), 'tag_tag_details'),
			'itemLink' => 'page-%d',
		));
	}
}
