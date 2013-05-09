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
 * @version 	$Id: Widget.php 1186 2010-02-05 02:18:46Z huuphuoc $
 * @since		2.0.2
 */

class Tomato_Modules_Comment_Widgets_Latestcomment_Widget extends Tomato_Core_Widget 
{
	protected function _prepareShow()
	{
		$limit = $this->_request->getParam('limit', 5);
		$conn = Tomato_Core_Db_Connection::getSlaveConnection();
		$commentGateway = new Tomato_Modules_Comment_Model_CommentGateway();
		$select = $conn->select()
					->from(array('c' => Tomato_Core_Db_Connection::getDbPrefix().'comment'), array('title', 'content', 'email', 'full_name', 'created_date'))
					->where('c.is_active = ?', 1)
					->order('c.activate_date DESC')
					->limit($limit);
		$rs = $select->query()->fetchAll();
		$comments = new Tomato_Core_Model_RecordSet($rs, $commentGateway);
		$this->_view->assign('comments', $comments);
	}
}