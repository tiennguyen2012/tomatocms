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

class Tomato_Modules_News_Widgets_Latesthome_Widget extends Tomato_Core_Widget 
{
	protected function _prepareShow() 
	{
		$limit = $this->_request->getParam('limit', 5);
		$categoryIds = $this->_request->getParam('category_ids', null);
		if ($categoryIds != null && $categoryIds != '') {
			$categoryIds = explode(',', $categoryIds);
			$gateway = new Tomato_Modules_Category_Model_CategoryGateway();
			$gateway->setDbConnection(Tomato_Core_Db_Connection::getSlaveConnection());
			$categories = array();
			foreach ($categoryIds as $id) {
				$category = $gateway->getCategoryById($id);
				if ($category) {
					$categories[] = $category;
				}
			}
			$this->_view->assign('categories', $categories);
		}
		$this->_view->assign('limit', $limit);	
	}
	
	protected function _prepareConfig() 
	{
		$conn = Tomato_Core_Db_Connection::getMasterConnection();
		$categoryGateway = new Tomato_Modules_Category_Model_CategoryGateway();
		$categoryGateway->setDbConnection($conn);
		
		$categories = $categoryGateway->getCategoryTree();
		$this->_view->assign('categories', $categories);
		
		$params = $this->_request->getParam('params');
		$categoryIds = array();
		$categoryIdsString = '';
		if ($params) {
			$params = Zend_Json::decode($params);
			$categoryIdsString = $params['category_ids']['value'];
			$categoryIds = explode(',', $categoryIdsString);
		}
		$this->_view->assign('categoryIds', $categoryIds);
		$this->_view->assign('categoryIdsString', $categoryIdsString);

		$this->_view->assign('uniqueId', uniqid());
	}		
}
