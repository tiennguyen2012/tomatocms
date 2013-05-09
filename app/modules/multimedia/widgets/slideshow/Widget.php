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
 * @version 	$Id: Widget.php 1525 2010-03-09 19:16:09Z huuphuoc $
 */

class Tomato_Modules_Multimedia_Widgets_Slideshow_Widget extends Tomato_Core_Widget 
{
	protected function _prepareShow() 
	{
		$limit = (int)$this->_request->getParam('limit', 9);
		$limit = ($limit == 0) ? 9 : $limit;
		$conn = Tomato_Core_Db_Connection::getSlaveConnection();
		$fileGateway = new Tomato_Modules_Multimedia_Model_FileGateway();
		$select = $conn->select()
					->from(array('f' => Tomato_Core_Db_Connection::getDbPrefix().'multimedia_file'), array('file_id', 'title', 'description', 'image_general', 'image_medium', 'image_large', 'url'))
					->where('f.is_active = ?', 1)
					->where('f.file_type = ?', 'image')
					->order('f.file_id DESC')
					->limit($limit);
		$rs = $select->query()->fetchAll();
		$photos = new Tomato_Core_Model_RecordSet($rs, $fileGateway);
		$this->_view->assign('photos', $photos);
	}
}
