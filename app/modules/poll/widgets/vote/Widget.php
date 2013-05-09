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
 * @version 	$Id: Widget.php 1994 2010-04-02 07:10:35Z hoangninh $
 */

class Tomato_Modules_Poll_Widgets_Vote_Widget extends Tomato_Core_Widget 
{
	protected function _prepareShow() 
	{
		$questionId = $this->_request->getParam('poll_id');
		$conn = Tomato_Core_Db_Connection::getSlaveConnection();
		
		$questionGateway = new Tomato_Modules_Poll_Model_QuestionGateway();
		$questionGateway->setDbConnection($conn);
		
		$select = $conn->select()
						->from(array('q' => Tomato_Core_Db_Connection::getDbPrefix().'poll_question'))
						->where('q.question_id = ?', $questionId)
						->where('q.is_active = ?', 1)
						->limit(1);
		$rs = $select->query()->fetchAll();
		$questions = new Tomato_Core_Model_RecordSet($rs, $questionGateway);
		$question = (count($questions) == 0) ? null : $questions[0];	
		$this->_view->assign('question', $question);
		
		$answerGateway = new Tomato_Modules_Poll_Model_AnswerGateway();
		$answerGateway->setDbConnection($conn);
		$answers = $answerGateway->getAnswers($questionId);
		$this->_view->assign('answers', $answers);
		
		$container = $this->_request->getParam('container');
		$this->_view->assign('container', $container);
		
		$data = Zend_Json::encode(array('poll_id' => $questionId, 'container' => $container));
		$this->_view->assign('data', $data);
	}
	
	protected function _prepareResult() 
	{
		$questionId = $this->_request->getParam('poll_id');
		$conn = Tomato_Core_Db_Connection::getSlaveConnection();
		
		$questionGateway = new Tomato_Modules_Poll_Model_QuestionGateway();
		$questionGateway->setDbConnection($conn);
		$question = $questionGateway->getQuestionById($questionId);
		$this->_view->assign('question', $question);

		$answerIds = $this->_request->getParam('answers');
		if ($answerIds) {
			// Update num views
			$masterConn = Tomato_Core_Db_Connection::getMasterConnection();
			$sql = 'UPDATE '.Tomato_Core_Db_Connection::getDbPrefix().'poll_answer SET num_views = num_views + 1 
					WHERE answer_id IN('.$answerIds.')';
			$masterConn->query($sql);
		}
		
		// Get result
		$answerGateway = new Tomato_Modules_Poll_Model_AnswerGateway();
		$answerGateway->setDbConnection($conn);
		$answers = $answerGateway->getAnswers($questionId);
		$this->_view->assign('answers', $answers);
		
		// Count the number of answers
		$select = $conn->select()
						->from(array('p' => Tomato_Core_Db_Connection::getDbPrefix().'poll_answer'), array('num_views' => 'SUM(num_views)'))
						->where('question_id = ?', $questionId)
						->limit(1);
		$row = $select->query()->fetch();
		$count = (null == $row) ? 0 : $row->num_views;
		$this->_view->assign('count', $count);
		
		$container = $this->_request->getParam('container');
		$this->_view->assign('container', $container);
		
		$data = Zend_Json::encode(array('poll_id' => $questionId, 'container' => $container));
		$this->_view->assign('data', $data);
	}
	
	protected function _prepareConfig() 
	{
		$conn = Tomato_Core_Db_Connection::getMasterConnection();
		$select = $conn->select()
					->from(array('q' => Tomato_Core_Db_Connection::getDbPrefix().'poll_question'), array('question_id', 'title'))
					->where('q.is_active = ?', 1)
					->order('question_id DESC');
		$rs = $select->query()->fetchAll();
		$questionGateway = new Tomato_Modules_Poll_Model_QuestionGateway();
		$questions = new Tomato_Core_Model_RecordSet($rs, $questionGateway);
		$this->_view->assign('questions', $questions);
	}
}
