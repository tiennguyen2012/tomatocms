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
 * @version 	$Id: AnswerGateway.php 1670 2010-03-23 04:48:42Z huuphuoc $
 */

class Tomato_Modules_Poll_Model_AnswerGateway extends Tomato_Core_Model_Gateway 
{
	/**
	 * (non-PHPdoc)
	 * @see app/core/model/Tomato_Core_Model_Gateway#convert($entity)
	 */	
	public function convert($entity) 
	{
		return new Tomato_Modules_Poll_Model_Answer($entity); 
	}
	
	/**
	 * @param Tomato_Modules_Poll_Model_Answer $answer
	 * @return int
	 */
	public function add($answer) 
	{
		$this->_conn->insert($this->_prefix.'poll_answer', array(
			'question_id' 	=> $answer->question_id,
			'title'			=> $answer->title,
			'content'		=> $answer->content,
			'position'		=> $answer->position,
			'user_id'		=> $answer->user_id,
			'num_views'		=> $answer->num_views,
		));
		return $this->_conn->lastInsertId($this->_prefix.'poll_answer');
	}
	
	/**
	 * @param Tomato_Modules_Poll_Model_Question $question
	 * @return unknown_type
	 */
	public function deleteByQuestion($question) 
	{
		$where[] = 'question_id ='.$this->_conn->quote($question->question_id);
		$this->_conn->delete($this->_prefix.'poll_answer', $where);
	}
	
	public function getAnswers($questionId) 
	{
		$select = $this->_conn
						->select()
						->from(array('a' => $this->_prefix.'poll_answer'))
						->where('a.question_id = ?', $questionId);
		$rs = $select->query()->fetchAll();
		return new Tomato_Core_Model_RecordSet($rs, $this);	
	}
}
