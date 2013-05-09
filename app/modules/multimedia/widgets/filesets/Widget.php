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
 * @version 	$Id: Widget.php 1266 2010-02-23 04:32:10Z huuphuoc $
 * @since		2.0.2
 */

class Tomato_Modules_Multimedia_Widgets_FileSets_Widget extends Tomato_Core_Widget 
{
	protected function _prepareShow() 
	{
		$fileId = $this->_request->getParam('file_id', 10);
		$limit = $this->_request->getParam('limit', 10);
		$conn = Tomato_Core_Db_Connection::getSlaveConnection();
		$setGateway = new Tomato_Modules_Multimedia_Model_SetGateway();
		$select = $conn->select()
					->from(array('s' => Tomato_Core_Db_Connection::getDbPrefix().'multimedia_set'), 
					array('set_id', 'slug', 'title', 'description', 'image_square', 'image_general', 'image_small', 'image_crop', 'image_medium', 'image_large'))
					->joinInner(array('fs' => Tomato_Core_Db_Connection::getDbPrefix().'multimedia_file_set_assoc'), 's.set_id = fs.set_id AND fs.file_id = '.$conn->quote($fileId))
					->where('s.is_active = ?', 1)
					->order('s.set_id DESC')
					->limit($limit);
		$rs = $select->query()->fetchAll();
		$sets = new Tomato_Core_Model_RecordSet($rs, $setGateway);
		$this->_view->assign('sets', $sets);
	}
}
