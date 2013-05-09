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
 * @version 	$Id: FileGateway.php 1671 2010-03-23 04:49:47Z huuphuoc $
 */

class Tomato_Modules_Multimedia_Model_FileGateway extends Tomato_Core_Model_Gateway 
{
	/**
	 * (non-PHPdoc)
	 * @see app/core/model/Tomato_Core_Model_Gateway#convert($entity)
	 */	
	public function convert($entity) 
	{
		return new Tomato_Modules_Multimedia_Model_File($entity); 
	}
	
	/**
	 * @param int $id
	 * @return Tomato_Modules_Multimedia_Model_File
	 */
	public function getFileById($id) 
	{
		$select = $this->_conn
						->select()
						->from(array('f' => $this->_prefix.'multimedia_file'))
						->where('f.file_id = ?', $id)
						->limit(1);
		$rs = $select->query()->fetchAll();
		$files = new Tomato_Core_Model_RecordSet($rs, $this);
		return (count($files) == 0) ? null : $files[0];	
	}
	
	/**
	 * Add new file
	 * 
	 * @param Tomato_Modules_Multimedia_Model_File $file
	 * @return int
	 */
	public function add($file) 
	{
		$this->_conn->insert($this->_prefix.'multimedia_file', array(
			'title' 			=> $file->title,
			'slug' 				=> $file->slug,
			'description' 		=> $file->description,
			'content' 			=> $file->content,
			'created_date' 		=> $file->created_date,
			'created_user' 		=> $file->created_user,
			'created_user_name' => $file->created_user_name,
			'image_medium' 		=> $file->image_medium,
			'image_square' 		=> $file->image_square,
			'image_general' 	=> $file->image_general,
			'image_small' 		=> $file->image_small,
			'image_crop' 		=> $file->image_crop,
			'image_large' 		=> $file->image_large,
			'image_original' 	=> $file->image_original,
			'url' 				=> $file->url,
			'html_code' 		=> $file->html_code,
			'file_type' 		=> $file->file_type,
			'is_active' 		=> $file->is_active,
		));
		return $this->_conn->lastInsertId($this->_prefix.'multimedia_file');
	}
	
	/**
	 * @param int $start
	 * @param int $offset
	 * @param array $exp Search expression. An array contain various conditions, keys including:
	 * 'keyword', 'file_id', 'created_user_id'
	 * @return Tomato_Core_Model_RecordSet
	 */
	public function find($start, $offset, $exp = null) 
	{
		$select = $this->_conn
				->select()
				->from(array('f' => $this->_prefix.'multimedia_file'));
		if ($exp) {
			if (isset($exp['file_id'])) {
				$select->where('f.file_id = ?', $exp['file_id']);
			}
			if (isset($exp['created_user'])) {
				$select->where('f.created_user = ?', $exp['created_user']);
			}
			if (isset($exp['file_type'])) {
				$select->where('f.file_type = ?', $exp['file_type']);
			}
			if ((isset($exp['photo']) && '1' == $exp['photo']) && (isset($exp['clip']) && '1' == $exp['clip'])) {
				$select->where('(f.file_type = \'image\' OR f.file_type = \'video\')');
			} elseif (isset($exp['photo']) && '1' == $exp['photo']) {
				$select->where('f.file_type = ?', 'image');
			} elseif (isset($exp['clip']) && '1' == $exp['clip']) {
				$select->where('f.file_type = ?', 'video');
			}
			if (isset($exp['keyword'])) {
				$select->where('f.title LIKE \'%'.addslashes($exp['keyword']).'%\'');
			}
		}
		$select->order('f.file_id DESC')
				->limit($offset, $start);
		$rs = $select->query()->fetchAll();
		return new Tomato_Core_Model_RecordSet($rs, $this);
	}
	
	/**
	 * @param array $exp Search expression (@see find)
	 * @return int
	 */
	public function count($exp = null) 
	{
		$select = $this->_conn
				->select()
				->from(array('f' => $this->_prefix.'multimedia_file'), array('num_files' => 'COUNT(*)'));
		if ($exp) {
			if (isset($exp['file_id'])) {
				$select->where('f.file_id = ?', $exp['file_id']);
			}
			if (isset($exp['created_user'])) {
				$select->where('f.created_user = ?', $exp['created_user']);
			}
			if (isset($exp['file_type'])) {
				$select->where('f.file_type = ?', $exp['file_type']);
			}
			if ((isset($exp['photo']) && '1' == $exp['photo']) && (isset($exp['clip']) && '1' == $exp['clip'])) {
				$select->where('(f.file_type = \'image\' OR f.file_type = \'video\')');
			} elseif (isset($exp['photo']) && '1' == $exp['photo']) {
				$select->where('f.file_type = ?', 'image');
			} elseif (isset($exp['clip']) && '1' == $exp['clip']) {
				$select->where('f.file_type = ?', 'video');
			}
			if (isset($exp['keyword'])) {
				$select->where('f.title LIKE \'%'.addslashes($exp['keyword']).'%\'');
			}
		}
		$row = $select->query()->fetch();
		return $row->num_files;
	}
	
	/**
	 * @param int $id
	 * @return int
	 */
	public function delete($id) 
	{
		$where = array();
		$where[] = 'file_id = '.$this->_conn->quote($id);
		return $this->_conn->delete($this->_prefix.'multimedia_file', $where);
	}

	/**
	 * Update file title/description
	 * 
	 * @param string $fileId
	 * @param string $title
	 * @param string $description
	 * @return int
	 */
	public function updateDescription($fileId, $title, $description = null) 
	{
		$where[] = 'file_id = '.$this->_conn->quote($fileId);
		$data = array();
		if (null != $title) {
			$data['title'] = $title;
			$data['slug'] = Tomato_Core_Utility_String::removeSign($title, '-', true);
		}
		if (null != $description) {
			$data['description'] = $description;
		} 
		return $this->_conn->update($this->_prefix.'multimedia_file', $data, $where);
	}
	
	/**
	 * @param int $id
	 * @return int
	 */
	public function toggleStatus($id) 
	{
		$sql = 'UPDATE '.$this->_prefix.'multimedia_file SET is_active = 1 - is_active WHERE file_id = '.$this->_conn->quote($id);
		return $this->_conn->query($sql);
	}
	
	/**
	 * Update file
	 * 
	 * @param Tomato_Modules_Multimedia_Model_File $file
	 * @return int
	 */
	public function update($file) 
	{
		$where[] = 'file_id = '.$this->_conn->quote($file->file_id);
		return $this->_conn->update($this->_prefix.'multimedia_file', 
					array(
						'title' 			=> $file->title,
						'slug' 				=> $file->slug,
						'description' 		=> $file->description,
						'content' 			=> $file->content,
						'image_medium' 		=> $file->image_medium,
						'image_square' 		=> $file->image_square,
						'image_general' 	=> $file->image_general,
						'image_small' 		=> $file->image_small,
						'image_crop' 		=> $file->image_crop,
						'image_large' 		=> $file->image_large,
						'image_original'	=> $file->image_original,
						'url' 				=> $file->url,
						'html_code' 		=> $file->html_code,
						'file_type' 		=> $file->file_type,
					), $where);		
	}
}
