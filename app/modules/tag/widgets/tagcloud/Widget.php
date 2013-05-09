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
 * @version 	$Id: Widget.php 1199 2010-02-08 04:39:57Z huuphuoc $
 * @since		2.0.2
 */

class Tomato_Modules_Tag_Widgets_TagCloud_Widget extends Tomato_Core_Widget 
{
	protected function _prepareShow() 
	{
		$limit = $this->_request->getParam('limit');
		$router = Zend_Controller_Front::getInstance()->getRouter();
		$routeName = $router->getCurrentRouteName();
		$currRoute = $router->getCurrentRoute();
		$params = array();
		if (!($currRoute instanceof Zend_Controller_Router_Route_Regex)) {
			return;
		}
		$conn = Tomato_Core_Db_Connection::getSlaveConnection();
		$select = $conn->select()
						->from(array('ti' => Tomato_Core_Db_Connection::getDbPrefix().'tag_item_assoc'), array('details_route_name'))
						->joinInner(array('t' => Tomato_Core_Db_Connection::getDbPrefix().'tag'), 'ti.tag_id = t.tag_id', array('tag_id', 'tag_text', 'num_items' => 'COUNT(*)'))
						->where('ti.route_name = ?', $routeName)
						->group('tag_text');
		if ($limit) {
			$select->limit($limit);		
		}
		$rs = $select->query()->fetchAll();
		$items = array();
		foreach ($rs as $row) {
			$data = array(
				'tag_id' => $row->tag_id, 
				'tag_text' => $row->tag_text, 
				'details_route_name' => $row->details_route_name,
			);
			$items[] = array(
				'title' => $row->tag_text,
				'weight' => $row->num_items,
				'params' => array(
					'url' => $this->_view->url($data, 'tag_tag_details')
				),
			);
		}
		$cloud = new Zend_Tag_Cloud(array('tags' => $items));
		$this->_view->assign('cloud', $cloud);
	}
}
