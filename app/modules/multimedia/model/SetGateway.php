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
 * @version 	$Id: SetGateway.php 1670 2010-03-23 04:48:42Z huuphuoc $
 */

class Tomato_Modules_Multimedia_Model_SetGateway extends Tomato_Core_Model_Gateway 
{
	/**
	 * (non-PHPdoc)
	 * @see app/core/model/Tomato_Core_Model_Gateway#convert($entity)
	 */	
	public function convert($entity) 
	{
		return new Tomato_Modules_Multimedia_Model_Set($entity); 
	}
	
	/**
	 * @param int $id
	 * @return Tomato_Modules_Multimedia_Model_Set
	 */
	public function getSetById($id) 
	{
		$select = $this->_conn
						->select()
						->from(array('s' => $this->_prefix.'multimedia_set'))
						->where('s.set_id = ?', $id)
						->limit(1);
		$rs = $select->query()->fetch();
		return (null == $rs) ? null : new Tomato_Modules_Multimedia_Model_Set($rs);	
	}
	
	/**
	 * Add new set
	 * 
	 * @param Tomato_Modules_Multimedia_Model_Set $set
	 * @return int
	 */
	public function add($set) 
	{
		$this->_conn->insert($this->_prefix.'multimedia_set', array(
			'title' 			=> $set->title,
			'slug' 				=> $set->slug,
			'description' 		=> $set->description,
			'created_date' 		=> $set->created_date,
			'created_user_id' 	=> $set->created_user_id,
			'created_user_name' => $set->created_user_name,
			'image_medium' 		=> $set->image_medium,
			'image_square' 		=> $set->image_square,
			'image_general' 	=> $set->image_general,
			'image_small' 		=> $set->image_small,
			'image_crop' 		=> $set->image_crop,
			'image_large' 		=> $set->image_large,
			'is_active' 		=> $set->is_active,
		));
		return $this->_conn->lastInsertId($this->_prefix.'multimedia_set');
	}
	
	/**
	 * Update set
	 * 
	 * @param Tomato_Modules_Multimedia_Model_Set $set
	 * @return int
	 */
	public function update($set) 
	{
		$where[] = 'set_id = '.$this->_conn->quote($set->set_id);
		return $this->_conn->update($this->_prefix.'multimedia_set', array(
				'title'      		=> $set->title,
				'slug' 				=> $set->slug,
				'description' 		=> $set->description,
				'image_medium' 		=> $set->image_medium,
				'image_square' 		=> $set->image_square,
				'image_general' 	=> $set->image_general,
				'image_small' 		=> $set->image_small,
				'image_crop' 		=> $set->image_crop,
				'image_large' 		=> $set->image_large,
			), $where);			
	}
	
	/**
	 * Get all sets
	 * 
	 * @return Tomato_Core_Model_RecordSet
	 */
	public function getAllSets() 
	{
		$select = $this->_conn
					->select()
					->from(array('s' => $this->_prefix.'multimedia_set'));
		$rs = $select->query()->fetchAll();
		return new Tomato_Core_Model_RecordSet($rs, $this);
	}
	
	/**
	 * @param int $start
	 * @param int $offset
	 * @param array $exp Search expression. An array contain various conditions, keys including:
	 * 'keyword', 'set_id', 'created_user_id'
	 * @return Tomato_Core_Model_RecordSet
	 */
	public function find($start, $offset, $exp = null) 
	{
		$select = $this->_conn
				->select()
				->from(array('s' => $this->_prefix.'multimedia_set'));
		if ($exp) {
			if (isset($exp['created_user_id'])) {
				$select->where('s.created_user_id = ?', $exp['created_user_id']);
			}
			if (isset($exp['keyword'])) {
				$select->where('s.title LIKE \'%'.addslashes($exp['keyword']).'%\'');
			}
		}
		$select->order('s.set_id DESC')
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
				->from(array('s' => $this->_prefix.'multimedia_set'), array('num_sets' => 'COUNT(*)'));
		if ($exp) {
			if (isset($exp['created_user'])) {
				$select->where('s.created_user_id = ?', $exp['created_user']);
			}
			if (isset($exp['keyword'])) {
				$select->where('s.title LIKE \'%'.addslashes($exp['keyword']).'%\'');
			}
		}
		$row = $select->query()->fetch();
		return $row->num_sets;
	}
	
	/**
	 * @param $id:set_id
	 * @return int
	 */
	public function countSet($id) 
	{
		$select = $this->_conn
				->select()
				->from(array('s' => $this->_prefix.'multimedia_file_set_assoc'), array('num_sets' => 'COUNT(*)'));

		$select->where('s.created_user_id = ?', $id);
		$row = $select->query()->fetch();
		return $row->num_sets;
	}
	
	/**
	 * @param int $id
	 * @return int
	 */
	public function delete($id) 
	{
		$where = array();
		$where[] = 'set_id = '.$this->_conn->quote($id);
		$this->_conn->delete($this->_prefix.'multimedia_file_set_assoc', $where);
		return $this->_conn->delete($this->_prefix.'multimedia_set', $where);
	}

	/**
	 * Update set title/description
	 * 
	 * @param string $setId
	 * @param string $title
	 * @param string $description
	 * @return int
	 */
	public function updateDescription($setId, $title, $description = null) 
	{
		$where[] = 'set_id = '.$this->_conn->quote($setId);
		$data = array();
		if (null != $title) {
			$data['title'] = $title;
			$data['slug'] = Tomato_Core_Utility_String::removeSign($title, '-', true);
		}
		if (null != $description) {
			$data['description'] = $description;
		} 
		return $this->_conn->update($this->_prefix.'multimedia_set', $data, $where);
	}
	
	/**
	 * @param int $id
	 * @return int
	 */
	public function toggleStatus($id) 
	{
		$sql = 'UPDATE '.$this->_prefix.'multimedia_set SET is_active = 1 - is_active WHERE set_id = '.$this->_conn->quote($id);
		return $this->_conn->query($sql);
	}
}
