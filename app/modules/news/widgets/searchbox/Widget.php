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
 * @version 	$Id: Widget.php 887 2010-01-20 04:44:39Z huuphuoc $
 */

class Tomato_Modules_News_Widgets_SearchBox_Widget extends Tomato_Core_Widget 
{
	protected function _prepareShow() 
	{
		$limit = $this->_request->getParam('limit', 10);
		$this->_view->assign('limit', $limit);
		$this->_view->assign('unid', uniqid());
	}
}