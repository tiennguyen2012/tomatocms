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
 * @version 	$Id: LatestArticles.php 1186 2010-02-05 02:18:46Z huuphuoc $
 */

class News_View_Helper_LatestArticles extends Zend_View_Helper_Abstract 
{
	public function latestArticles($categoryId, $limit = 5) 
	{
		$conn = Tomato_Core_Db_Connection::getSlaveConnection();
		$select = $conn->select()
					->from(array('a' => Tomato_Core_Db_Connection::getDbPrefix().'news_article'), array('article_id', 'category_id', 'title', 'slug', 'description', 'created_date', 'image_general', 'image_square'))
					->joinInner(array('ac' => Tomato_Core_Db_Connection::getDbPrefix().'news_article_category_assoc'), 'a.article_id = ac.article_id', array('category_id'))
					->where('ac.category_id = ?', $categoryId)
					->where('a.status = ?', 'active')
					->order('a.activate_date DESC')
					->limit($limit);
		$rs = $select->query()->fetchAll();
		$articles = new Tomato_Core_Model_RecordSet($rs, new Tomato_Modules_News_Model_ArticleGateway());
		return $articles;
	}
}