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
 * @version 	$Id: NoteGateway.php 1861 2010-03-31 02:06:53Z huuphuoc $
 * @since		2.0.4
 */

class Tomato_Modules_Multimedia_Model_NoteGateway extends Tomato_Core_Model_Gateway 
{
	/**
	 * (non-PHPdoc)
	 * @see app/core/model/Tomato_Core_Model_Gateway#convert($entity)
	 */	
	public function convert($entity) 
	{
		return new Tomato_Modules_Multimedia_Model_Note($entity); 
	}
	
	public function add($note)
	{
		$this->_conn->insert($this->_prefix.'multimedia_note', array(
			'file_id' => $note->file_id,
			'top' => $note->top,
			'left' => $note->left,
			'width' => $note->width,
			'height' => $note->height,
			'content' => $note->content,
			'user_id' => $note->user_id,
			'user_name' => $note->user_name,
			'created_date' => date('Y-m-d H:i:s'),
		));
		return $this->_conn->lastInsertId($this->_prefix.'multimedia_note');
	}
	
	public function delete($id)
	{
		$where = array('note_id = '.$this->_conn->quote($id));
		return $this->_conn->delete($this->_prefix.'multimedia_note', $where);
	}
	
	public function update($note)
	{
		$where = array('note_id = '.$this->_conn->quote($note->note_id));
		return $this->_conn->update($this->_prefix.'multimedia_note', 
						array(
							'top' => $note->top,
							'left' => $note->left,
							'width' => $note->width,
							'height' => $note->height,
							'content' => $note->content,
							'user_id' => $note->user_id,
							'user_name' => $note->user_name,
							'created_date' => date('Y-m-d H:i:s'),
						), $where);
	}
	
	public function find($start = null, $count = null, $exp = null)
	{
		$select = $this->_conn
						->select()
						->from(array('n' => $this->_prefix.'multimedia_note'))
						->joinInner(array('f' => $this->_prefix.'multimedia_file'), 'n.file_id = f.file_id', 
							array('image_general', 'image_medium', 'image_crop', 'image_small', 'image_square', 'image_large'));
		if ($exp) {
			if (isset($exp['file_id'])) {
				$select->where('n.file_id = ?', $exp['file_id']);
			}
			if (isset($exp['is_active'])) {
				$select->where('n.is_active = ?', $exp['is_active']);
			}
		}
		$select->order('note_id DESC');
		if (is_int($start) && is_int($count)) {
			$select->limit($count, $start);
		}
		$rs = $select->query()->fetchAll(); 
		return new Tomato_Core_Model_RecordSet($rs, $this);
	}
	
	public function count($exp = null)
	{
		$select = $this->_conn
						->select()
						->from(array('n' => $this->_prefix.'multimedia_note'), array('num_notes' => 'COUNT(*)'));
		if ($exp) {
			if (isset($exp['file_id'])) {
				$select->where('file_id = ?', $exp['file_id']);
			}
			if (isset($exp['is_active'])) {
				$select->where('is_active = ?', $exp['is_active']);
			}
		}
		$row = $select->query()->fetch();
		return $row->num_notes;
	}
	
	public function updateStatus($id, $status)
	{
		$where[] = 'note_id = '.$this->_conn->quote($id);
		return $this->_conn->update($this->_prefix.'multimedia_note', array('is_active' => $status), $where);	
	}
}
