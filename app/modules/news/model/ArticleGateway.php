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
 * @version 	$Id: ArticleGateway.php 1670 2010-03-23 04:48:42Z huuphuoc $
 */

class Tomato_Modules_News_Model_ArticleGateway extends Tomato_Core_Model_Gateway 
{
	/**
	 * (non-PHPdoc)
	 * @see app/core/model/Tomato_Core_Model_Gateway#convert($entity)
	 */	
	public function convert($entity) 
	{
		return new Tomato_Modules_News_Model_Article($entity); 
	}
	
	public function getArticleById($id) 
	{
		$select = $this->_conn
						->select()
						->from(array('a' => $this->_prefix.'news_article'))
						->where('a.article_id = ?', $id)
						->limit(1);
		$rs = $select->query()->fetchAll();
		$articles = new Tomato_Core_Model_RecordSet($rs, $this);
		return (count($articles) == 0) ? null : $articles[0];	
	}
	
	/**
	 * Add new article
	 * 
	 * @param Tomato_Modules_News_Model_Article $article
	 * @return int
	 */
	public function add($article) 
	{
		$this->_conn->insert($this->_prefix.'news_article', array(
			'category_id' 		=> $article->category_id,
			'title' 			=> $article->title,
			'sub_title' 		=> $article->sub_title,
			'slug' 				=> $article->slug,
			'description' 		=> $article->description,
			'content' 			=> $article->content,
			'created_date' 		=> $article->created_date,
			'created_user_id' 	=> $article->created_user_id,
			'created_user_name' => $article->created_user_name,
			'author' 			=> $article->author,
			'allow_comment' 	=> (int)$article->allow_comment,
			'image_medium' 		=> $article->image_medium,
			'image_square' 		=> $article->image_square,
			'image_large' 		=> $article->image_large,
			'image_general' 	=> $article->image_general,
			'image_small' 		=> $article->image_small,
			'image_crop' 		=> $article->image_crop,
			'image_thumbnail' 	=> $article->image_thumbnail,
			'sticky' 			=> (int)$article->sticky,
			'status' 			=> $article->status,
			'icons' 			=> $article->icons,
		));
		return $this->_conn->lastInsertId($this->_prefix.'news_article');
	}
	
	/**
	 * Update article
	 * 
	 * @param Tomato_Modules_News_Model_Article $article
	 * @return int
	 */
	public function update($article) 
	{
		$where[] = 'article_id = '.$this->_conn->quote($article->article_id);
		return $this->_conn->update($this->_prefix.'news_article', array(
				'category_id' 		=> $article->category_id,
				'title' 			=> $article->title,
				'sub_title' 		=> $article->sub_title,
				'slug' 				=> $article->slug,
				'description' 		=> $article->description,
				'content' 			=> $article->content,
				'updated_date' 		=> $article->updated_date,
				'updated_user_id' 	=> $article->updated_user_id,
				'updated_user_name' => $article->updated_user_name,
				'author' 			=> $article->author,
				'allow_comment' 	=> (int)$article->allow_comment,
				'image_medium' 		=> $article->image_medium,
				'image_square' 		=> $article->image_square,
				'image_large' 		=> $article->image_large,
				'image_general' 	=> $article->image_general,
				'image_small' 		=> $article->image_small,
				'image_crop' 		=> $article->image_crop,
				'image_thumbnail' 	=> $article->image_thumbnail,
				'sticky' 			=> (int)$article->sticky,
				'icons' 			=> $article->icons,
			), $where);			
	}
	
	/**
	 * @param int $start
	 * @param int $offset
	 * @param array $exp Search expression. An array contain various conditions, keys including:
	 * 'keyword', 'article_id', 'category_id', 'created_user_id', 'status'
	 * @return Tomato_Core_Model_RecordSet
	 */
	public function find($start, $offset, $exp = null) 
	{
		$select = $this->_conn
					->select()
					->from(array('a' => $this->_prefix.'news_article'));
		if ($exp) {
			if (isset($exp['article_id'])) {
				$select->where('a.article_id = ?', $exp['article_id']);
			}
			if (isset($exp['category_id'])) {
				$select->where('a.category_id = ?', $exp['category_id']);
			}
			if (isset($exp['created_user_id'])) {
				$select->where('a.created_user_id = ?', $exp['created_user_id']);
			}
			if (isset($exp['status'])) {
				$select->where('a.status = ?', $exp['status']);
			}
			if (isset($exp['keyword'])) {
				$select->where('a.title LIKE \'%'.addslashes($exp['keyword']).'%\'');
			}
		}
		$select->order('a.article_id DESC')
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
				->from(array('a' => $this->_prefix.'news_article'), array('num_articles' => 'COUNT(*)'));
		if ($exp) {
			if (isset($exp['article_id'])) {
				$select->where('a.article_id = ?', $exp['article_id']);
			}
			if (isset($exp['category_id'])) {
				$select->where('a.category_id = ?', $exp['category_id']);
			}
			if (isset($exp['created_user_id'])) {
				$select->where('a.created_user_id = ?', $exp['created_user_id']);
			}
			if (isset($exp['status'])) {
				$select->where('a.status = ?', $exp['status']);
			}
			if (isset($exp['keyword'])) {
				$select->where('a.title LIKE \'%'.addslashes($exp['keyword']).'%\'');
			}
		}
		$row = $select->query()->fetch();
		return $row->num_articles;
	}
	
	/**
	 * @param int $id
	 * @return int
	 */
	public function delete($id) 
	{
		$where[] = 'article_id = '.$this->_conn->quote($id);
		return $this->_conn->update($this->_prefix.'news_article', array('status' => 'deleted'), $where);
	}
	
	/**
	 * @param int $id
	 * @param string $id
	 * @return int
	 */
	public function updateStatus($id, $status) 
	{
		$where[] = 'article_id = '.$this->_conn->quote($id);
		return $this->_conn->update($this->_prefix.'news_article', array('status' => $status), $where);			
	}
}
