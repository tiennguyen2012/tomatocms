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
 * @version 	$Id: ArticleRevisionGateway.php 1948 2010-04-02 03:58:39Z huuphuoc $
 * @since		2.0.4
 */

class Tomato_Modules_News_Model_ArticleRevisionGateway extends Tomato_Core_Model_Gateway 
{
	/**
	 * (non-PHPdoc)
	 * @see application/core/Model/Tomato_Core_Model_Gateway#convert($entity)
	 */	
	public function convert($entity) 
	{
		return new Tomato_Modules_News_Model_ArticleRevision($entity); 
	}
	
	public function getArticleRevisionById($id) 
	{
		$select = $this->_conn
						->select()
						->from(array('r' => $this->_prefix.'news_article_revision'))
						->where('r.revision_id = ?', $id)
						->limit(1);
		$rs = $select->query()->fetchAll();
		$revisions = new Tomato_Core_Model_RecordSet($rs, $this);
		return (count($revisions) == 0) ? null : $revisions[0];	
	}
	
	/**
	 * Add new article
	 * 
	 * @param Tomato_Modules_News_Model_ArticleRevision $articleRevision
	 * @return int
	 */
	public function add($articleRevision) 
	{
		$this->_conn->insert($this->_prefix.'news_article_revision', array(
			'article_id'		=> $articleRevision->article_id,
			'category_id' 		=> $articleRevision->category_id,
			'title' 			=> $articleRevision->title,
			'sub_title' 		=> $articleRevision->sub_title,
			'slug' 				=> $articleRevision->slug,
			'description' 		=> $articleRevision->description,
			'content' 			=> $articleRevision->content,
			'created_date' 		=> $articleRevision->created_date,
			'created_user_id' 	=> $articleRevision->created_user_id,
			'created_user_name' => $articleRevision->created_user_name,
			'author' 			=> $articleRevision->author,
			'icons' 			=> $articleRevision->icons,
		));
		return $this->_conn->lastInsertId($this->_prefix.'news_article_revision');
	}
	
	/**
	 * @param int $start
	 * @param int $offset
	 * @param array $exp Search expression. An array contain various conditions, keys including:
	 * 'article_id'
	 * @return Tomato_Core_Model_RecordSet
	 */
	public function find($start, $offset, $exp = null) 
	{
		$select = $this->_conn
					->select()
					->from(array('r' => $this->_prefix.'news_article_revision'));
		if ($exp) {
			if (isset($exp['article_id'])) {
				$select->where('r.article_id = ?', $exp['article_id']);
			}
		}
		$select->order('r.created_date DESC')
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
				->from(array('r' => $this->_prefix.'news_article_revision'), array('num_revisions' => 'COUNT(*)'));
		if ($exp) {
			if (isset($exp['article_id'])) {
				$select->where('r.article_id = ?', $exp['article_id']);
			}
		}
		$row = $select->query()->fetch();
		return $row->num_revisions;
	}
	
	/**
	 * @param int $id
	 * @return int
	 */
	public function delete($id) 
	{
		$where[] = 'revision_id = '.$this->_conn->quote($id);
		return $this->_conn->delete($this->_prefix.'news_article_revision', $where);
	}
}
