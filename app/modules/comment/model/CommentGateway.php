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
 * @version 	$Id: CommentGateway.php 1669 2010-03-23 04:48:06Z huuphuoc $
 */

class Tomato_Modules_Comment_Model_CommentGateway extends Tomato_Core_Model_Gateway 
{
	/**
	 * (non-PHPdoc)
	 * @see app/core/model/Tomato_Core_Model_Gateway#convert($entity)
	 */	
	public function convert($entity) 
	{
		return new Tomato_Modules_Comment_Model_Comment($entity); 
	}
	
	public function getCommentById($id) 
	{
		$select = $this->_conn
						->select()
						->from(array('c' => $this->_prefix.'comment'))
						->where('c.comment_id = ?', $id)
						->limit(1);
		$rs = $select->query()->fetchAll();
		$comments = new Tomato_Core_Model_RecordSet($rs, $this);
		return (count($comments) == 0) ? null : $comments[0];	
	}
	
	/**
	 * @param Tomato_Modules_Comment_Model_Comment $comment
	 * @return int
	 */
	public function add($comment) 
	{
		$this->_conn->insert($this->_prefix.'comment', array(
			'title'				=> $comment->title,
			'content'			=> $comment->content,
			'is_active'			=> $comment->is_active,
			'email'				=> $comment->email,
			'ip'				=> $comment->ip,
			'full_name'			=> $comment->full_name,
			'web_site'			=> $comment->web_site,
			'created_date'		=> $comment->created_date,
			'reply_to'			=> $comment->reply_to,
			'depth'				=> $comment->depth,
			'path'				=> $comment->path,
			'ordering'			=> $comment->ordering,
			'page_url'			=> $comment->page_url,
			'activate_date'		=> $comment->activate_date,
		));
		return $this->_conn->lastInsertId($this->_prefix.'comment');
	}
	
	/**
	 * @param Tomato_Modules_Comment_Model_Comment $comment
	 * @return void
	 */
	public function update($comment) 
	{
		$where[] = 'comment_id = '.$this->_conn->quote($comment->comment_id);
		$this->_conn->update($this->_prefix.'comment', array(
			'title'				=> $comment->title,
			'content'			=> $comment->content,
			'is_active'			=> $comment->is_active,
			'email'				=> $comment->email,
			'ip'				=> $comment->ip,
			'full_name'			=> $comment->full_name,
			'web_site'			=> $comment->web_site,
			'activate_date'		=> $comment->activate_date,
		), $where);
	}
	
	public function delete($id) 
	{
		$where[] = 'comment_id ='.$this->_conn->quote($id);
		return $this->_conn->delete($this->_prefix.'comment', $where);
	}
	
	/**
	 * @param Tomato_Modules_Comment_Model_Comment $comment
	 * @return int
	 */
	public function toggleActive($comment) 
	{
		$sql = 'UPDATE '.$this->_prefix.'comment SET is_active = 1 - is_active, 
				activate_date = '.$this->_conn->quote($comment->activate_date).'
				WHERE comment_id = '.$this->_conn->quote($comment->comment_id);
		return $this->_conn->query($sql);
	}
}
