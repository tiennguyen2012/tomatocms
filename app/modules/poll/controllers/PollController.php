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
 * @version 	$Id: PollController.php 1545 2010-03-10 07:46:33Z huuphuoc $
 */

class Poll_PollController extends Zend_Controller_Action 
{
	/* ========== Backend actions =========================================== */

	public function listAction() 
	{
		$conn = Tomato_Core_Db_Connection::getSlaveConnection();
		
		$perPage = 20;
		$pageIndex = $this->_request->getParam('pageIndex');
		if (null == $pageIndex || '' == $pageIndex) {
			$pageIndex = 1;
		}
		$start = ($pageIndex - 1) * $perPage;
		$this->view->assign('pageIndex', $pageIndex);
		
		$questionGateway = new Tomato_Modules_Poll_Model_QuestionGateway();
		$questionGateway->setDbConnection($conn);
		$questions = $questionGateway->find($start, $perPage);
		$this->view->assign('questions', $questions);
		
		$numQuestions = $questionGateway->count();
		$this->view->assign('numQuestions', $numQuestions);
		
		// Paginator
		$paginator = new Zend_Paginator(new Tomato_Core_Utility_PaginatorAdapter($questions, $numQuestions));
		$paginator->setCurrentPageNumber($pageIndex);
		$paginator->setItemCountPerPage($perPage);
		$this->view->assign('paginator', $paginator);
		$this->view->assign('paginatorOptions', array(
			'path' => $this->view->url(array(), 'poll_list'),
			'itemLink' => 'page-%d',
		));
	}
	
	public function addAction() 
	{
		if ($this->_request->isPost()) {
			$user = Zend_Auth::getInstance()->getIdentity();
			$title = $this->_request->getPost('title');
			$content = $this->_request->getPost('content');
			$multipleOptions = (int)$this->_request->getPost('multiOption');
			$status = $this->_request->getPost('status');
			$startDate = $this->_request->getPost('start_date');
			$endDate = $this->_request->getPost('end_date');
			$answers = $this->_request->getPost('answers');
			
			$conn = Tomato_Core_Db_Connection::getMasterConnection();
			$questionGateway = new Tomato_Modules_Poll_Model_QuestionGateway();
			$questionGateway->setDbConnection($conn);
			
			$question = new Tomato_Modules_Poll_Model_Question(array(
				'title'				=> $title,
				'content'			=> $content,
				'created_date'		=> date('Y-m-d H:i:s'),
				'start_date'		=> $startDate,
				'end_date'			=> $endDate,
				'is_active'			=> $status,
				'multiple_options'	=> $multipleOptions,
				'user_id'			=> $user->user_id,
			));
			$questionId = $questionGateway->add($question);
			if ($answers != null && $questionId != null) {
				$answerGateway = new Tomato_Modules_Poll_Model_AnswerGateway();
				$answerGateway->setDbConnection($conn);
				for ($i = 0; $i < count($answers); $i++) {
					$answer = new Tomato_Modules_Poll_Model_Answer(array(
						'question_id' 	=> $questionId,
						'title'			=> $answers[$i],
						'position'		=> $i + 1,
						'user_id'		=> $user->user_id,
						'num_views'		=> 0,
					));
					$answerGateway->add($answer);
				}
			}
			$this->_redirect($this->view->serverUrl().$this->view->url(array(), 'poll_add'));
		}
	}
	
	public function editAction() 
	{
		$conn = Tomato_Core_Db_Connection::getMasterConnection();
		$id = $this->_request->getParam('poll_id');
		$questionGateway = new Tomato_Modules_Poll_Model_QuestionGateway();
		$questionGateway->setDbConnection($conn);
		$question = $questionGateway->getQuestionById($id);
		$this->view->assign('question', $question);
		
		$answerGateway = new Tomato_Modules_Poll_Model_AnswerGateway();
		$answerGateway->setDbConnection($conn);
		$answers = $answerGateway->getAnswers($id);
		$this->view->assign('answers', $answers);
		
		if ($this->_request->isPost()) {
			$user = Zend_Auth::getInstance()->getIdentity();
			$title = $this->_request->getPost('title');
			$content = $this->_request->getPost('content');
			$multipleOptions = (int)$this->_request->getPost('multiOption');
			$status = $this->_request->getPost('status');
			$startDate = $this->_request->getPost('start_date');
			$endDate = $this->_request->getPost('end_date');
			$answers = $this->_request->getPost('answers');
			
			$conn = Tomato_Core_Db_Connection::getMasterConnection();
			$questionGateway = new Tomato_Modules_Poll_Model_QuestionGateway();
			$questionGateway->setDbConnection($conn);
			
			$question->title = $title;
			$question->content = $content;
			$question->multiple_options = $multipleOptions;
			$question->is_active = $status;
			$question->start_date = $startDate;
			$question->end_date = $endDate;
			
			$questionGateway->update($question);
			
			$questionId = $question->question_id;
			
			if ($answers != null && $questionId != null) {
				$answerGateway = new Tomato_Modules_Poll_Model_AnswerGateway();
				$answerGateway->setDbConnection($conn);
				$answerGateway->deleteByQuestion($question);
				
				for ($i = 0; $i < count($answers); $i++) {
					$answer = new Tomato_Modules_Poll_Model_Answer(array(
						'question_id' 	=> $questionId,
						'title'			=> $answers[$i],
						'position'		=> $i + 1,
						'user_id'		=> $user->user_id,
						'num_views'		=> 0,
					));
					$answerGateway->add($answer);
				}
			}
			$this->_helper->getHelper('FlashMessenger')->addMessage(
				$this->view->translator('poll_edit_success')
			);
			$this->_redirect($this->view->serverUrl().$this->view->url(array(), 'poll_list'));
		}
	}
	
	public function deleteAction() 
	{
		$this->_helper->getHelper('layout')->disableLayout();
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		if ($this->_request->isPost()) {
			$id = $this->_request->getPost('id');
			$conn = Tomato_Core_Db_Connection::getMasterConnection();
			$questionGateway = new Tomato_Modules_Poll_Model_QuestionGateway();
			$questionGateway->setDbConnection($conn);
			$question = $questionGateway->getQuestionById($id);
			
			if (null == $question) {
				$this->_response->setBody('RESULT_NOT_FOUND');
				return;
			} 
			$conn->delete(Tomato_Core_Db_Connection::getDbPrefix().'poll_answer', array(
						'question_id ='.$conn->quote($question->question_id)
			));
			$questionGateway->delete($question);
			
			$data = array(
				'title' => $question->title
			);
			$this->_response->setBody(Zend_Json::encode($data));
			return;
		}
		$this->_response->setBody('RESULT_NOT_OK');
	}
	
	public function activateAction() 
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		if ($this->_request->isPost()) {
			$id = $this->_request->getPost('id');
						
			$conn = Tomato_Core_Db_Connection::getMasterConnection();
			$gateway = new Tomato_Modules_Poll_Model_QuestionGateway();	
			$gateway->setDbConnection($conn);
			$question = $gateway->getQuestionById($id);
			if (null == $question) {
				$this->_response->setBody('RESULT_NOT_FOUND');
				return;
			}
			$gateway->toggleActive($question->question_id);
			$isActive = 1 - $question->is_active;
			$data = array(
				'title' 	=> $question->title,
				'is_active'	=> $isActive
			);
			$this->_response->setBody(Zend_Json::encode($data));			
		}
	}
}
