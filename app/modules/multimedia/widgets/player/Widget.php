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
 */

class Tomato_Modules_Multimedia_Widgets_Player_Widget extends Tomato_Core_Widget 
{
	protected function _prepareShow() 
	{
		$limit = $this->_request->getParam('limit', 9);
		
		$conn = Tomato_Core_Db_Connection::getSlaveConnection();
		$fileGateway = new Tomato_Modules_Multimedia_Model_FileGateway();
		$select = $conn->select()
					->from(array('f' => Tomato_Core_Db_Connection::getDbPrefix().'multimedia_file'), array('file_id', 'title', 'description', 'image_crop', 'image_square', 'url', 'html_code'))
					->where('f.is_active = ?', 1)
					->where('f.file_type = ?', 'video')
					->order('f.file_id DESC')
					->limit($limit);
		$rs = $select->query()->fetchAll();
		$files = new Tomato_Core_Model_RecordSet($rs, $fileGateway);
		$this->_view->assign('files', $files);
		
		$width = $this->_request->getParam('width', 280);
    	$height = $this->_request->getParam('height', 210);
    	$this->_view->assign('width', $width);
    	$this->_view->assign('height', $height);
	}
}
