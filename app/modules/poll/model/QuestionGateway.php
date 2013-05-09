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
 * @version 	$Id: QuestionGateway.php 1670 2010-03-23 04:48:42Z huuphuoc $
 */

class Tomato_Modules_Poll_Model_QuestionGateway extends Tomato_Core_Model_Gateway 
{
	/**
	 * (non-PHPdoc)
	 * @see app/core/model/Tomato_Core_Model_Gateway#convert($entity)
	 */	
	public function convert($entity) 
	{
		return new Tomato_Modules_Poll_Model_Question($entity); 
	}
	
	public function getQuestionById($id) 
	{
		$select = $this->_conn
						->select()
						->from(array('q' => $this->_prefix.'poll_question'))
						->where('q.question_id = ?', $id)
						->limit(1);
		$rs = $select->query()->fetchAll();
		$questions = new Tomato_Core_Model_RecordSet($rs, $this);
		return (count($questions) == 0) ? null : $questions[0];	
	}
	
	/**
	 * @param Tomato_Modules_Poll_Model_Question $question
	 * @return int
	 */
	public function add($question) 
	{
		$this->_conn->insert($this->_prefix.'poll_question', array(
			'title'				=> $question->title,
			'content'			=> $question->content,
			'created_date'		=> $question->created_date,
			'start_date'		=> $question->start_date,
			'end_date'			=> $question->end_date,
			'is_active'			=> $question->is_active,
			'multiple_options'	=> $question->multiple_options,
			'user_id'			=> $question->user_id,
		));
		return $this->_conn->lastInsertId($this->_prefix.'poll_question');
	}
	
	/**
	 * @param Tomato_Modules_Poll_Model_Question $question
	 * @return void
	 */
	public function update($question) 
	{
		$where[] = 'question_id ='.$this->_conn->quote($question->question_id);
		$this->_conn->update($this->_prefix.'poll_question', array(
				'title'				=> $question->title,
				'content'			=> $question->content,
				'created_date'		=> $question->created_date,
				'start_date'		=> $question->start_date,
				'end_date'			=> $question->end_date,
				'is_active'			=> $question->is_active,
				'multiple_options'	=> $question->multiple_options,
				'user_id'			=> $question->user_id,
			), $where);
	}
	
	/**
	 * @param Tomato_Modules_Poll_Model_Question $question
	 * @return void
	 */
	public function delete($question) 
	{
		$where[] = 'question_id = '.$this->_conn->quote($question->question_id);
		$this->_conn->delete($this->_prefix.'poll_question', $where);
	}
	
	/**
	 * @param int $start
	 * @param int $offset
	 * @return Tomato_Core_Model_RecordSet
	 */
	public function find($start, $offset) 
	{
		$select = $this->_conn
				->select()
				->from(array('q' => $this->_prefix.'poll_question'));
		$select->order('q.question_id DESC')
				->limit($offset, $start);
		$rs = $select->query()->fetchAll();
		return new Tomato_Core_Model_RecordSet($rs, $this);
	}
	
	/**
	 * @return int
	 */
	public function count() 
	{
		$select = $this->_conn
				->select()
				->from(array('q' => $this->_prefix.'poll_question'), array('num_questions' => 'COUNT(*)'));
		$row = $select->query()->fetch();
		return $row->num_questions;
	}
	
	/**
	 * @param int $id
	 * @return int
	 */
	public function toggleActive($id) 
	{
		$sql = 'UPDATE '.$this->_prefix.'poll_question SET is_active = 1 - is_active 
				WHERE question_id = '.$this->_conn->quote($id);
		return $this->_conn->query($sql);
	}
}
