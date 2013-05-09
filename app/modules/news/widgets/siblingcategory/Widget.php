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

class Tomato_Modules_News_Widgets_SiblingCategory_Widget extends Tomato_Core_Widget 
{
	protected function _prepareShow() 
	{
		$categoryId = $this->_request->getParam('category_id');
		$limit = $this->_request->getParam('limit', 8);
		
		// Get the parent node
		$conn = Tomato_Core_Db_Connection::getSlaveConnection();
		$categoryGateway = new Tomato_Modules_Category_Model_CategoryGateway();
		$categoryGateway->setDbConnection($conn);
		$select = $conn->select()
						->from(array('c1' => Tomato_Core_Db_Connection::getDbPrefix().'category'), array())
						->joinInner(array('c2' => Tomato_Core_Db_Connection::getDbPrefix().'category'), 'c1.left_id BETWEEN c2.left_id AND c2.right_id', array('category_id'))
						->where('c1.category_id = ?', $categoryId)
						->where('c2.category_id <> ?', $categoryId);
		$rs = $select->query()->fetchAll();
		$parentCategoryId = (count($rs) == 0) ? $categoryId : $rs[0]->category_id; 
		
		// Get the sub-categories
		$categories = $categoryGateway->getSubCategories($parentCategoryId);
		$this->_view->assign('categories', $categories);
		$this->_view->assign('limit', $limit);
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
