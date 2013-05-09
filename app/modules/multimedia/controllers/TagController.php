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
 * @version 	$Id: TagController.php 1525 2010-03-09 19:16:09Z huuphuoc $
 * @since		2.0.2
 */

class Multimedia_TagController extends Zend_Controller_Action
{
	/* ========== Frontend actions ========================================== */
	
	/**
	 * Show list of files by given tag
	 * @since 2.0.2
	 */
	public function fileAction() 
	{
		$tagId = $this->_request->getParam('tag_id');
		$detailsRouteName = $this->_request->getParam('details_route_name');
		$perPage = 18;
		$pageIndex = $this->_request->getParam('page_index');
		if (null === $pageIndex) {
			$pageIndex = 1;
		}
		$start = ($pageIndex - 1) * $perPage;
		
		$conn = Tomato_Core_Db_Connection::getSlaveConnection();
		
		$tagGateway = new Tomato_Modules_Tag_Model_TagGateway();
		$tagGateway->setDbConnection($conn);
		$tag = $tagGateway->getTagById($tagId);
		if (null == $tag) {
			throw new Tomato_Core_Exception_NotFound();
		}
		
		$tag->details_route_name = $detailsRouteName;
		$this->view->assign('tag', $tag);
		
		// Get the list of files tagged by the tag
		$gateway = new Tomato_Modules_Multimedia_Model_FileGateway();
		$gateway->setDbConnection($conn);
		$select = $conn->select()
					->from(array('f' => Tomato_Core_Db_Connection::getDbPrefix().'multimedia_file'), array('file_id', 'title', 'description', 'image_general', 'image_medium', 'image_large', 'url'))
					->joinInner(array('ti' => Tomato_Core_Db_Connection::getDbPrefix().'tag_item_assoc'), 'f.file_id = ti.item_id', array())
					->where('ti.tag_id = ?', $tagId)
					->where('ti.item_name = ?', 'file_id')
					->where('f.is_active = ?', 1)
					//->group('f.file_id')
					->order('f.file_id DESC')
					->limit($perPage, $start);
		$rs = $select->query()->fetchAll();
		$files = new Tomato_Core_Model_RecordSet($rs, $gateway);
		$this->view->assign('files', $files);
		
		// Count number of files tagged by the tag
		$select = $conn->select()
					->from(array('f' => Tomato_Core_Db_Connection::getDbPrefix().'multimedia_file'), array('num_files' => 'COUNT(file_id)'))
					->joinInner(array('ti' => Tomato_Core_Db_Connection::getDbPrefix().'tag_item_assoc'), 'f.file_id = ti.item_id', array())
					->where('ti.tag_id = ?', $tagId)
					->where('ti.item_name = ?', 'file_id')
					->where('f.is_active = ?', 1);
		$numFiles = $select->query()->fetch()->num_files;
		
		// Paginator
		$paginator = new Zend_Paginator(new Tomato_Core_Utility_PaginatorAdapter($files, $numFiles));
		$paginator->setCurrentPageNumber($pageIndex);
		$paginator->setItemCountPerPage($perPage);
		$this->view->assign('paginator', $paginator);
		$this->view->assign('paginatorOptions', array(
			'path' => $this->view->url($tag->getProperties(), 'tag_tag_details'),
			'itemLink' => 'page-%d',
		));
	}
	
	/**
	 * Show list of sets by given tag
	 * @since 2.0.2
	 */
	public function setAction() 
	{
		$tagId = $this->_request->getParam('tag_id');
		$detailsRouteName = $this->_request->getParam('details_route_name');
		$perPage = 18;
		$pageIndex = $this->_request->getParam('page_index');
		if (null === $pageIndex) {
			$pageIndex = 1;
		}
		$start = ($pageIndex - 1) * $perPage;
		
		$conn = Tomato_Core_Db_Connection::getSlaveConnection();
		
		$tagGateway = new Tomato_Modules_Tag_Model_TagGateway();
		$tagGateway->setDbConnection($conn);
		$tag = $tagGateway->getTagById($tagId);
		if (null == $tag) {
			throw new Tomato_Core_Exception_NotFound();
		}
		
		$tag->details_route_name = $detailsRouteName;
		$this->view->assign('tag', $tag);
		
		// Get the list of files tagged by the tag
		$gateway = new Tomato_Modules_Multimedia_Model_SetGateway();
		$gateway->setDbConnection($conn);
		$select = $conn->select()
					->from(array('s' => Tomato_Core_Db_Connection::getDbPrefix().'multimedia_set'), array('set_id', 'title', 'description', 'image_general', 'image_medium', 'image_large'))
					->joinInner(array('ti' => Tomato_Core_Db_Connection::getDbPrefix().'tag_item_assoc'), 's.set_id = ti.item_id', array())
					->where('ti.tag_id = ?', $tagId)
					->where('ti.item_name = ?', 'set_id')
					->where('s.is_active = ?', 1)
					//->group('s.set_id')
					->order('s.set_id DESC')
					->limit($perPage, $start);
		$rs = $select->query()->fetchAll();
		$sets = new Tomato_Core_Model_RecordSet($rs, $gateway);
		$this->view->assign('sets', $sets);
		
		// Count number of sets tagged by the tag
		$select = $conn->select()
					->from(array('s' => Tomato_Core_Db_Connection::getDbPrefix().'multimedia_set'), array('num_sets' => 'COUNT(set_id)'))
					->joinInner(array('ti' => Tomato_Core_Db_Connection::getDbPrefix().'tag_item_assoc'), 's.set_id = ti.item_id', array())
					->where('ti.tag_id = ?', $tagId)
					->where('ti.item_name = ?', 'set_id')
					->where('s.is_active = ?', 1);
		$numSets = $select->query()->fetch()->num_sets;
		
		// Paginator
		$paginator = new Zend_Paginator(new Tomato_Core_Utility_PaginatorAdapter($sets, $numSets));
		$paginator->setCurrentPageNumber($pageIndex);
		$paginator->setItemCountPerPage($perPage);
		$this->view->assign('paginator', $paginator);
		$this->view->assign('paginatorOptions', array(
			'path' => $this->view->url($tag->getProperties(), 'tag_tag_details'),
			'itemLink' => 'page-%d',
		));
	}
}
