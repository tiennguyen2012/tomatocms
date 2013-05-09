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
 * @version 	$Id: CategoryGateway.php 1669 2010-03-23 04:48:06Z huuphuoc $
 */

class Tomato_Modules_Category_Model_CategoryGateway extends Tomato_Core_Model_Gateway 
{
	/**
	 * (non-PHPdoc)
	 * @see app/core/model/Tomato_Core_Model_Gateway#convert($entity)
	 */
	public function convert($entity) 
	{
		return new Tomato_Modules_Category_Model_Category($entity); 
	}
	
	/**
	 * @param int $id
	 * @return Tomato_Modules_Category_Model_Category
	 */
	public function getCategoryById($id) 
	{
		$select = $this->_conn
					->select()
					->from(array('c' => $this->_prefix.'category'))
					->where('c.category_id = ?', $id)
					->limit(1);
		$row = $select->query()->fetchAll();
		$categories = new Tomato_Core_Model_RecordSet($row, $this);
		return (count($categories) == 0) ? null : $categories[0]; 
	}
	
	/**
	 * Get sub-categories of given category
	 * 
	 * @param int $categoryId Category id
	 * @return Tomato_Core_Model_RecordSet
	 */
	public function getSubCategories($categoryId, $depth = 1) 
	{
		$sql = 'SELECT node.category_id, node.slug, node.name, (COUNT(parent.name) - (sub_tree.depth + 1)) AS depth,
						node.left_id, node.right_id
				FROM '.$this->_prefix.'category AS node,
					'.$this->_prefix.'category AS parent,
					'.$this->_prefix.'category AS sub_parent,
					(
						SELECT node.category_id, (COUNT(parent.name) - 1) AS depth
						FROM '.$this->_prefix.'category AS node,
						'.$this->_prefix.'category AS parent
						WHERE node.left_id BETWEEN parent.left_id AND parent.right_id
						AND node.category_id = '.$this->_conn->quote($categoryId).'
						GROUP BY node.name
						ORDER BY node.left_id
					) AS sub_tree
				WHERE node.left_id BETWEEN parent.left_id AND parent.right_id
				AND node.left_id BETWEEN sub_parent.left_id AND sub_parent.right_id
				
				AND sub_parent.category_id = sub_tree.category_id
				GROUP BY node.category_id';
		if ($depth) {
			$sql .= ' HAVING depth <= 1';
		}
		$sql .= ' ORDER BY node.left_id';
		
		$rs = $this->_conn->query($sql)->fetchAll();
		return new Tomato_Core_Model_RecordSet($rs, $this);
	}
	
	/**
	 * @param Tomato_Modules_Category_Model_Category $category
	 * @param int $parentId
	 * @return int
	 */
	public function add($category, $parentId = null) 
	{
		if ($parentId) {
			$sql = 'SELECT right_id FROM '.$this->_prefix.'category WHERE category_id = '.$this->_conn->quote($parentId);
		} else {
			$sql = 'SELECT MAX(right_id) as right_id FROM '.$this->_prefix.'category';
		}
		$right = $this->_conn->query($sql)->fetch();
		$rightId = ($parentId) ? $right->right_id : $right->right_id + 1;
		if ($rightId != null) {
			$sql = 'UPDATE '.$this->_prefix.'category SET left_id = IF(left_id > 
					'.$this->_conn->quote($rightId).', left_id + 2, left_id), 
					right_id = IF(right_id >= '.$this->_conn->quote($rightId).', 
					right_id + 2, right_id)';
			$this->_conn->query($sql);
			$data = array(
						'name'			=> $category->name,
						'slug'			=> $category->slug,
						'meta'			=> $category->meta,
						'created_date'	=> $category->created_date,
						'user_id'		=> $category->user_id,
						'left_id'		=> $rightId,
						'right_id'		=> $rightId + 1,
			);
			if (isset($category->category_id) && $category->category_id != null) {
				$data['category_id'] = $category->category_id;
			}
			$this->_conn->insert($this->_prefix.'category', $data);
			return $this->_conn->lastInsertId($this->_prefix.'category');
		}
	}
	
	/**
	 * @param Tomato_Modules_Category_Model_Category $category
	 * @param int $parentId
	 * @param bool $deleteCategory
	 * @param bool $includeChild
	 * @return void
	 */
	public function update($category, $parentId = null, $deleteCategory = false, $includeChild = true) 
	{
		if ($deleteCategory) {
			if ($includeChild) {
				$oldCategories = $this->getSubCategories($category->category_id, null);
				if (count($oldCategories) > 0) {
					/**
					 * Delete category
					 */
					$width = $category->right_id - $category->left_id + 1;
					$sql = 'DELETE FROM '.$this->_prefix.'category 
								WHERE left_id BETWEEN '.$category->left_id.' 
													AND '.$category->right_id;
					$this->_conn->query($sql);
					$sql = 'UPDATE '.$this->_prefix.'category 
								SET right_id = right_id - '.$width.' 
								WHERE right_id > '.$category->right_id;
					$this->_conn->query($sql);
					$sql = 'UPDATE '.$this->_prefix.'category 
								SET left_id = left_id - '.$width.' 
								WHERE left_id > '.$category->right_id;
					$this->_conn->query($sql);
					
					/**
					 * Add category
					 */
					$preDepth = null;
					$preCategoryId = null;
					foreach ($oldCategories as $oldCategory) {
						$parentId = (null != $preDepth && $oldCategory->depth > $preDepth) 
													? $preCategoryId : $parentId;
						$this->add($oldCategory, $parentId);
						$preDepth = $oldCategory->depth;
						$preCategoryId = $oldCategory->category_id; 
					} 
				}
				
			} else {
				$this->delete($category);
				$this->add($category, $parentId);
			}
		} else {
			$where[] = 'category_id = '.$this->_conn->quote($category->category_id);
			$this->_conn->update($this->_prefix.'category', array(
					'name'		=> $category->name,
					'slug' 		=> $category->slug,
				), $where);
		}
	}
	
	/**
	 * @param Tomato_Modules_Category_Model_Category $category
	 * @return void
	 */
	public function delete($category) 
	{
		if ($category != null) {
			$where[] = 'category_id = '.$this->_conn->quote($category->category_id);
			$this->_conn->delete($this->_prefix.'category', $where);
			
			$sql = 'UPDATE '.$this->_prefix.'category SET left_id = left_id - 1, right_id = right_id - 1'
					.' WHERE left_id BETWEEN '.$category->left_id.' AND '.$category->right_id;
			$this->_conn->query($sql);

			$sql = 'UPDATE '.$this->_prefix.'category SET right_id = right_id - 2'
					.' WHERE right_id > '.$this->_conn->quote($category->right_id);
			$this->_conn->query($sql);
			
			$sql = 'UPDATE '.$this->_prefix.'category SET left_id = left_id - 2'
					.' WHERE left_id > '.$this->_conn->quote($category->right_id);
			$this->_conn->query($sql);
		}
	}
	
	/**
	 * Build category tree with depth for each item
	 * 
	 * @return Tomato_Core_Model_RecordSet
	 */
	public function getCategoryTree() 
	{
		$sql = 'SELECT node.category_id, node.name, node.slug, (COUNT(parent.name) - 1) AS depth,
					node.left_id, node.right_id
				FROM '.$this->_prefix.'category AS node,
					'.$this->_prefix.'category AS parent
				WHERE node.left_id BETWEEN parent.left_id AND parent.right_id
				GROUP BY node.category_id
				ORDER BY node.left_id';
		$rs = $this->_conn->query($sql)->fetchAll();
		return new Tomato_Core_Model_RecordSet($rs, $this);
	}
	
	/**
	 * @param Tomato_Modules_Category_Model_Category $category
	 * @return Tomato_Core_Model_RecordSet
	 * @since 2.0.3
	 */
	public function getCategoryParent($category) {
		$select = $this->_conn->select()
						->from(array('c' => $this->_prefix.'category'))
						->where('c.left_id < '.$this->_conn->quote($category->left_id))
						->where('c.right_id > '.$this->_conn->quote($category->right_id))
						->order('c.left_id DESC')
						->limit(1);
		$row = $select->query()->fetchAll();
		$categories = new Tomato_Core_Model_RecordSet($row, $this);
		return (count($categories) == 0) ? null : $categories[0]; 
	}
}
