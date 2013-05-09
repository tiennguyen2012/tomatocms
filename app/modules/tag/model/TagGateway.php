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
 * @version 	$Id: TagGateway.php 1518 2010-03-09 09:48:35Z huuphuoc $
 * @since		2.0.2
 */

class Tomato_Modules_Tag_Model_TagGateway extends Tomato_Core_Model_Gateway
{
	/**
	 * (non-PHPdoc)
	 * @see app/core/model/Tomato_Core_Model_Gateway#convert($entity)
	 */
	public function convert($entity) 
	{
		return new Tomato_Modules_Tag_Model_Tag($entity);
	}
	
	/**
	 * Get tag by given id
	 * 
	 * @param int $id
	 * @return Tomato_Modules_Tag_Model_Tag
	 */
	public function getTagById($id) 
	{
		$select = $this->_conn
						->select()
						->from(array('t' => $this->_prefix.'tag'))
						->where('t.tag_id = ?', $id)
						->limit(1);
		$rs = $select->query()->fetchAll();
		$tags = new Tomato_Core_Model_RecordSet($rs, $this);
		return (count($tags) == 0) ? null : $tags[0];	
	}
	
	/**
	 * Check whether a tag exists or not
	 * 
	 * @param string $text
	 * @return boolean TRUE if tag exist
	 */
	public function exist($text)
	{
		$select = $this->_conn
						->select()
						->from(array('t' => $this->_prefix.'tag'), array('num_tags' => 'COUNT(*)'))
						->where('t.tag_text = ?', $text)
						->limit(1);
		return ($select->query()->fetch()->num_tags > 0);
	}
	
	/**
	 * Create new tag
	 * 
	 * @param Tomato_Modules_Tag_Model_Tag $tag
	 * @return int Id of tag that have been added
	 */
	public function add($tag) 
	{
		$this->_conn->insert($this->_prefix.'tag', array(
			'tag_text' => $tag->tag_text,
		));	
		return $this->_conn->lastInsertId($this->_prefix.'tag');
	}
	
	/**
	 * Delete tag by its id
	 * 
	 * @param int $tagId
	 * @return int
	 */
	public function delete($tagId) 
	{
		$where = array('tag_id = '.$this->_conn->quote($tagId));
		$this->_conn->delete($this->_prefix.'tag_item_assoc', $where);
		return $this->_conn->delete($this->_prefix.'tag', $where);
	}
	
	/**
	 * Search tags by keyword
	 * 
	 * @param string $keyword
	 * @param int $count
	 * @param int $offset
	 * @return array of tags
	 */
	public function find($keyword, $count, $offset) 
	{
		$select = $this->_conn
						->select()
						->from(array('t' => $this->_prefix.'tag'));
		if ($keyword != '') {
			$select->where('t.tag_text LIKE \'%'.addslashes($keyword).'%\'');
		}
		$select->order('tag_id')->limit($count, $offset);
		$rs = $select->query()->fetchAll();
		return new Tomato_Core_Model_RecordSet($rs, $this);
	}
	
	/**
	 * Count number of tags which like given keyword
	 * 
	 * @param string $keyword
	 * @return int
	 */
	public function count($keyword)
	{
		$select = $this->_conn
						->select()
						->from(array('t' => $this->_prefix.'tag'), array('num_tags' => 'COUNT(*)'));
		if ($keyword != '') {
			$select->where('t.tag_text LIKE \'%'.addslashes($keyword).'%\'');
		}
		$select->limit(1);
		return $select->query()->fetch()->num_tags;
	}
}
