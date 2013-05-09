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
 * @version 	$Id: Widget.php 1265 2010-02-23 04:27:52Z huuphuoc $
 * @since		2.0.2
 */

class Tomato_Modules_Menu_Widgets_Menu_Widget extends Tomato_Core_Widget
{
	protected function _prepareShow()
	{
		$conn = Tomato_Core_Db_Connection::getSlaveConnection();
		$menuId = $this->_request->getParam('menu_id');
		$menu = $conn->select()
					->from(array('m' => Tomato_Core_Db_Connection::getDbPrefix().'menu'))
					->where('menu_id = ?', $menuId)
					->limit(1)
					->query()
					->fetch();
		$menuData = null;			
		if ($menu != null) {
			$menuData = Zend_Json::decode($menu->json_data);
		}
		$this->_view->assign('menuData', $menuData);
		$this->_view->assign('uuid', uniqid());
	}
	
	protected function _prepareConfig()
	{
		$conn = Tomato_Core_Db_Connection::getSlaveConnection();
		$select = $conn->select()
						->from(array('m' => Tomato_Core_Db_Connection::getDbPrefix().'menu'), array('menu_id', 'name'))
						->order('menu_id DESC');
		$rs = $select->query()->fetchAll();
		$this->_view->assign('menus', $rs);
	}
}
