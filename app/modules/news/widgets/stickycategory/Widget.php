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
 * @version 	$Id: Widget.php 1267 2010-02-23 04:39:27Z huuphuoc $
 */

class Tomato_Modules_News_Widgets_StickyCategory_Widget extends Tomato_Core_Widget 
{
	protected function _prepareShow() 
	{
		$categoryId = $this->_request->getParam('category_id', null);
		if ($categoryId != null) {
			$categoryId = ltrim($categoryId, '');
			$categoryId = rtrim($categoryId, '');
			if ($categoryId == '') {
				$categoryId = null;
			}
		}
		
		$numArticlesPerRow = $this->_request->getParam('num_articles_per_row', 1);
		$limit = $this->_request->getParam('limit', 6);
		
		$conn = Tomato_Core_Db_Connection::getSlaveConnection();
		$articleGateway = new Tomato_Modules_News_Model_ArticleGateway();
		$select = $conn->select()
						->from(array('a' => Tomato_Core_Db_Connection::getDbPrefix().'news_article'), array('article_id', 'category_id', 'title', 'slug', 'description', 'image_crop'));
		if ($categoryId) {
			$select->where('a.category_id = ?', $categoryId);
		}
		$select->where('a.sticky = ?', 1)
				->where('a.status = ?', 'active')
				->order('a.activate_date DESC')
				->limit($limit);
		$rs = $select->query()->fetchAll();
		$articles = new Tomato_Core_Model_RecordSet($rs, $articleGateway);
		$this->_view->assign('stickyArticles', $articles);
		$this->_view->assign('numArticlesPerRow', $numArticlesPerRow);
	}
	
	protected function _prepareConfig() 
	{
		$conn = Tomato_Core_Db_Connection::getMasterConnection();
		$categoryGateway = new Tomato_Modules_Category_Model_CategoryGateway();
		$categoryGateway->setDbConnection($conn);
		
		$categories = $categoryGateway->getCategoryTree();
		$this->_view->assign('categories', $categories);
	}
}
