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
 * @version 	$Id: Helper.php 1575 2010-03-11 03:19:39Z hoangninh $
 */

class News_Widgets_Latesthome_Helper extends Zend_View_Helper_Abstract 
{
	public function helper() 
	{
		return $this;
	}
	
	public function getLatestArticles($categoryId, $limit = 5) 
	{
		$conn = Tomato_Core_Db_Connection::getSlaveConnection();
		$select = $conn->select()
					->from(array('a' => Tomato_Core_Db_Connection::getDbPrefix().'news_article'), array('article_id', 'category_id', 'title', 'slug', 'description', 'content', 'image_general', 'image_crop', 'created_date', 'activate_date', 'created_user_name', 'num_views', 'icons'))
					->joinInner(array('c' => Tomato_Core_Db_Connection::getDbPrefix().'category'), 'a.category_id = c.category_id', array('category_name' => 'name'));
		if ($categoryId) {
			$select->where('a.category_id = ?', $categoryId);
		}
		$select->where('a.status = ?', 'active')
				->order('a.activate_date DESC')
				->limit($limit);		
		$rs = $select->query()->fetchAll();
		$articles = new Tomato_Core_Model_RecordSet($rs, new Tomato_Modules_News_Model_ArticleGateway());
		return $articles;
	}
}
