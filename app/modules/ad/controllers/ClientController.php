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
 * @version 	$Id: ClientController.php 1545 2010-03-10 07:46:33Z huuphuoc $
 */

class Ad_ClientController extends Zend_Controller_Action 
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
		
		// Build article search expression
		$user = Zend_Auth::getInstance()->getIdentity();
		$paramsString = null;
		$exp = array(
			'name'		=> null,
			'email'		=> null,
			'address'	=> null,
		);
		
		if ($this->_request->isPost()) {
			$name = $this->_request->getPost('name');
			$email = $this->_request->getPost('email');
			$address = $this->_request->getPost('address');
			if ($name) {
				$exp['name'] = $name;
			}
			if ($email) {
				$exp['email'] = $email;
			}
			if ($address) {
				$exp['address'] = $address;
			}
			$paramsString = rawurlencode(base64_encode(Zend_Json::encode($exp)));
		} else {
			$paramsString = $this->_request->getParam('q');
			if (null != $paramsString) {
				$exp = rawurldecode(base64_decode($paramsString));
				$exp = Zend_Json::decode($exp); 
			} else {
				$paramsString = rawurlencode(base64_encode(Zend_Json::encode($exp)));
			}
		}
		
		$clientGateway = new Tomato_Modules_Ad_Model_ClientGateway();
		$clientGateway->setDbConnection($conn);
		$clients = $clientGateway->find($start, $perPage, $exp);
		$numClients = $clientGateway->count($exp);
		
		// Paginator
		$paginator = new Zend_Paginator(new Tomato_Core_Utility_PaginatorAdapter($clients, $numClients));
		$paginator->setCurrentPageNumber($pageIndex);
		$paginator->setItemCountPerPage($perPage);
		$this->view->assign('paginator', $paginator);
		$this->view->assign('paginatorOptions', array(
			'path' => $this->view->url(array(), 'ad_client_list'),
			'itemLink' => (null == $paramsString) ? 'page-%d' : 'page-%d?q='.$paramsString,
		));
		
		$this->view->assign('numClients', $numClients);
		$this->view->assign('clients', $clients);
		$this->view->assign('exp', $exp);
	}
	
	public function deleteAction() 
	{
		$this->_helper->getHelper('layout')->disableLayout();
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		
		if ($this->_request->isPost()) {
			$id = $this->_request->getPost('id');
			$conn = Tomato_Core_Db_Connection::getMasterConnection();
			
			$gateway = new Tomato_Modules_Ad_Model_ClientGateway();
			$gateway->setDbConnection($conn);
			$gateway->delete($id);
		}
		$this->_response->setBody('RESULT_OK');
	}
	
	public function editAction() 
	{
		$conn = Tomato_Core_Db_Connection::getMasterConnection();
		$clientGateway = new Tomato_Modules_Ad_Model_ClientGateway();
		$clientGateway->setDbConnection($conn);
		
		$clientId = $this->_request->getParam('client_id');
		$client = $clientGateway->getClientById($clientId);
		$this->view->assign('client', $client);
		
		if ($this->_request->isPost()) {
			$clientId = $this->_request->getPost('client_id');
			$name = $this->_request->getPost('name');
			$email = $this->_request->getPost('email');
			$telephone = $this->_request->getPost('telephone');
			$address = $this->_request->getPost('address');
			
			$client = new Tomato_Modules_Ad_Model_Client(array(
				'client_id' => $clientId,
				'name' => $name,
				'email' => $email,
				'telephone' => $telephone,
				'address' => $address,
			));
			$result = $clientGateway->update($client);
			if ($result > 0) {
				$this->_helper->getHelper('FlashMessenger')->addMessage(
					$this->view->translator('client_edit_success')
				);
				$this->_redirect($this->view->serverUrl().$this->view->url(array('client_id' => $clientId), 'ad_client_edit'));
			}
		}
	}
	
	/**
	 * Check if the client name has been existed or not
	 */
	public function checkAction() 
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		$conn = Tomato_Core_Db_Connection::getMasterConnection();
		$select = $conn->select()
					->from(array('c' => Tomato_Core_Db_Connection::getDbPrefix().'ad_client'), array('num_clients' => 'COUNT(*)'));
					
		$original = $this->_request->getParam('original');
		$result = false;			
		$name = $this->_request->getParam('name');
		if ($original == null || ($original != null && $name != $original)) {
			$select->where('c.name = ?', $name)
				   ->limit(1);
			$rs = $select->query()->fetch();
			$numClients = $rs->num_clients;
			$result = ($numClients == 0) ? false : true;
		}
		($result == true) ? $this->getResponse()->setBody('false') 
						: $this->getResponse()->setBody('true');		
	}
	
	public function addAction() 
	{
		$conn = Tomato_Core_Db_Connection::getMasterConnection();
		if ($this->_request->isPost()) {
			$name = $this->_request->getPost('name');
			$email = $this->_request->getPost('email');
			$telephone = $this->_request->getPost('telephone');
			$address = $this->_request->getPost('address');
			
			$client = new Tomato_Modules_Ad_Model_Client(array(
				'name' => $name,
				'email' => $email,
				'telephone' => $telephone,
				'address' => $address,
				'created_date' => date('Y-m-d H:i:s'),
			));
			$clientGateway = new Tomato_Modules_Ad_Model_ClientGateway();
			$clientGateway->setDbConnection($conn);
			$id = $clientGateway->add($client);
			if ($id > 0) {
				$this->_helper->getHelper('FlashMessenger')->addMessage(
					$this->view->translator('client_add_success')
				);
				$this->_redirect($this->view->serverUrl().$this->view->url(array(), 'ad_client_add'));
			}
		}
	}
}
