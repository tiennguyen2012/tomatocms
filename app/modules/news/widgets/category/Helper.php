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
 * @version 	$Id: Helper.php 1186 2010-02-05 02:18:46Z huuphuoc $
 */

class News_Widgets_Category_Helper extends Zend_View_Helper_Abstract 
{
	public function helper() 
	{
		return $this;
	}
	
	public function count($categoryId) 
	{
		$conn = Tomato_Core_Db_Connection::getSlaveConnection();
		$select = $conn->select()
					->from(array('a' => Tomato_Core_Db_Connection::getDbPrefix().'news_article'), array('num_articles' => 'COUNT(*)'))
					->where('a.category_id = ?', $categoryId)
					->where('a.status = ?', 'active')
					->limit(1);
		$row = $select->query()->fetch();
		return $row->num_articles;
	}
}