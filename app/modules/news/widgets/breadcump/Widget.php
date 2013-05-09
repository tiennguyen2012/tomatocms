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
 * @version 	$Id: Widget.php 1663 2010-03-22 04:08:59Z hoangninh $
 */

class Tomato_Modules_News_Widgets_Breadcump_Widget extends Tomato_Core_Widget 
{
	protected function _prepareShow() 
	{
		$categoryId = $this->_request->getParam('category_id');
		$conn = Tomato_Core_Db_Connection::getSlaveConnection();
		$gateway = new Tomato_Modules_Category_Model_CategoryGateway();
		$gateway->setDbConnection($conn);
		
		$sql = 'SELECT parent.category_id, parent.slug, parent.name 
				FROM '.Tomato_Core_Db_Connection::getDbPrefix().'category AS node, '.Tomato_Core_Db_Connection::getDbPrefix().'category AS parent
				WHERE node.left_id BETWEEN parent.left_id AND parent.right_id
				AND node.category_id = '.$conn->quote($categoryId).'
				ORDER BY parent.left_id';
		$rs = $conn->query($sql)->fetchAll();
		$categories = new Tomato_Core_Model_RecordSet($rs, $gateway);
		$this->_view->assign('categories', $categories);
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
