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
 * @version 	$Id: BannerController.php 1545 2010-03-10 07:46:33Z huuphuoc $
 */

class Ad_BannerController extends Zend_Controller_Action 
{
	/* ========== Backend actions =========================================== */
	
	public function listAction()
	{
		$conn = Tomato_Core_Db_Connection::getSlaveConnection();
		$pageGateway = new Tomato_Modules_Core_Model_LayoutPageGateway();
		$select = $conn->select()
					   ->from(array('p' => Tomato_Core_Db_Connection::getDbPrefix().'core_page'), array('page_id', 'title', 'name'));
		$rs = $select->query()->fetchAll();
		$pages = new Tomato_Core_Model_RecordSet($rs, $pageGateway);
		$this->view->assign('pages', $pages);
		
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
			'keyword'	=> null,
			'banner_id'	=> null,
			'page_name'	=> null,
			'status'	=> null,
		);
		
		if ($this->_request->isPost()) {
			$id = $this->_request->getPost('bannerId');
			$keyword = $this->_request->getPost('keyword');
			$pageName = $this->_request->getPost('page');
			$status = $this->_request->getPost('status');
			if ($keyword) {
				$exp['keyword'] = $keyword;
			}
			if ($id) {
				$exp['banner_id'] = $id;
			}
			if ($pageName) {
				$exp['page_name'] = $pageName;
			}
			if ($status) {
				$exp['status'] = $status;
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
		
		$bannerGateway = new Tomato_Modules_Ad_Model_BannerGateway();
		$bannerGateway->setDbConnection($conn);
		$banners = $bannerGateway->find($start, $perPage, $exp);
		$numBanners = $bannerGateway->count($exp);
		
		// Paginator
		$paginator = new Zend_Paginator(new Tomato_Core_Utility_PaginatorAdapter($banners, $numBanners));
		$paginator->setCurrentPageNumber($pageIndex);
		$paginator->setItemCountPerPage($perPage);
		$this->view->assign('paginator', $paginator);
		$this->view->assign('paginatorOptions', array(
			'path' => $this->view->url(array(), 'ad_banner_list'),
			'itemLink' => (null == $paramsString) ? 'page-%d' : 'page-%d?q='.$paramsString,
		));
		
		$this->view->assign('numBanners', $numBanners);
		$this->view->assign('banners', $banners);
		$this->view->assign('exp', $exp);
	}
	
	public function deleteAction() 
	{
		$this->_helper->getHelper('layout')->disableLayout();
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		
		if ($this->_request->isPost()) {
			$id = $this->_request->getPost('id');
			$conn = Tomato_Core_Db_Connection::getMasterConnection();
			
			$gateway = new Tomato_Modules_Ad_Model_BannerGateway();
			$gateway->setDbConnection($conn);
			$gateway->delete($id);
		}
		$this->_response->setBody('RESULT_OK');
	}
	
	public function editAction() 
	{
		$this->view->addHelperPath(TOMATO_APP_DIR.DS.'modules'.DS.'upload'.DS.'views'.DS.'helpers', 'Upload_View_Helper_');
		$conn = Tomato_Core_Db_Connection::getMasterConnection();
		
		$bannerGateway = new Tomato_Modules_Ad_Model_BannerGateway();
		$bannerGateway->setDbConnection($conn);		
		$bannerId = $this->_request->getParam('banner_id');
		$banner = $bannerGateway->getBannerById($bannerId);
		$this->view->assign('banner', $banner);
		$clientGateway = new Tomato_Modules_Ad_Model_ClientGateway();
		$clientGateway->setDbConnection($conn);
		$clients = $clientGateway->getAllClients();
		$this->view->assign('clients', $clients);
		
		$pageGateway = new Tomato_Modules_Core_Model_LayoutPageGateway();
		$select = $conn->select()
					   ->from(array('p' => Tomato_Core_Db_Connection::getDbPrefix().'core_page'), array('page_id', 'title', 'name', 'url'));
		$rs = $select->query()->fetchAll();
		$pages = new Tomato_Core_Model_RecordSet($rs, $pageGateway);
		$this->view->assign('pages', $pages);
		
		$select = $conn->select()
					   ->from(array('pa' => Tomato_Core_Db_Connection::getDbPrefix().'ad_page_assoc'))
					   ->where('pa.banner_id = ?', $bannerId);
		$rs = $select->query()->fetchAll();		
		$bannerPageNames = array();
		$bannerZones = array();
		$bannerPageUrls = array();
		if ($rs) {
			foreach ($rs as $row) {
				$bannerPageNames[] = $row->page_name;
				$bannerZones[] = $row->zone_id;
				$bannerPageUrls[] = $row->page_url;;
			}
		}
		$this->view->assign('bannerPageNames', $bannerPageNames);
		$this->view->assign('bannerZones', $bannerZones);
		$this->view->assign('bannerPageUrls', $bannerPageUrls);
		$zoneGateway = new Tomato_Modules_Ad_Model_ZoneGateway();
		$zoneGateway->setDbConnection($conn);
		$zones = $zoneGateway->getAllZones();
		$this->view->assign('zones', $zones);
		
		if ($this->_request->isPost()) {
			$bannerId = $this->_request->getPost('banner_id');
			$name = $this->_request->getPost('name');
			$text = $this->_request->getPost('text');
			$startDate = $this->_request->getPost('startDate');
			$expiredDate = $this->_request->getPost('expiredDate');
			$code = $this->_request->getPost('code');
			$clickUrl = $this->_request->getPost('clickUrl');
			$target = $this->_request->getPost('target');
			$format = $this->_request->getPost('format');
			$mode = $this->_request->getPost('mode');
			$timeout = $this->_request->getPost('timeout');	
			$status = $this->_request->getPost('status');		
			$pages = $this->_request->getPost('pages');
			$zones = $this->_request->getPost('zones');
			$otherZones = $this->_request->getPost('otherZones');
			$otherPages = $this->_request->getPost('otherPages');
			$otherUrls = $this->_request->getPost('otherUrls');
			$imageUrl = $this->_request->getPost('imageUrl');
			$clientId = $this->_request->getPost('client');
			$banner = new Tomato_Modules_Ad_Model_Banner(array(
				'banner_id' 			=> $bannerId,
				'name' 			        => $name,
				'text' 					=> $text,
				'code' 					=> $code,
				'click_url' 			=> $clickUrl,
				'format' 				=> $format,
				'image_url' 			=> $imageUrl,
				'mode' 					=> $mode,
				'timeout' 				=> 0,
			));
			if ($timeout) {
				$banner->timeout = $timeout;
			}
			if ($target) {
				$banner->target = $target;
			}
			if ($status) {
				$banner->status = $status;
			}
			if ($clientId) {
				$banner->client_id = $clientId;
			}
			if ($startDate) {
				$banner->start_date = date('Y-m-d', strtotime($startDate));
			}
			if ($expiredDate) {
				$banner->expired_date = date('Y-m-d', strtotime($expiredDate));
			}
			$result = $bannerGateway->update($banner);
				
			$conn->delete(Tomato_Core_Db_Connection::getDbPrefix().'ad_page_assoc', array('banner_id = '.$conn->quote($bannerId)));
			if ($pages) {
				if ($pages) {
					for ($i = 0; $i < count($pages); $i++) {
						$arr = explode("__", $pages[$i]);
						
						$conn->insert(Tomato_Core_Db_Connection::getDbPrefix().'ad_page_assoc', array(
										'page_name' => $arr[0],
										'page_url' => $arr[1],
										'zone_id' => $zones[$i],
										'banner_id' => $bannerId));
					}
				}
				if ($otherUrls) {
					for ($i = 0; $i < count($otherUrls); $i++) {
						$conn->insert(Tomato_Core_Db_Connection::getDbPrefix().'ad_page_assoc', array(
										'page_name' => $otherPages[$i],
										'page_url' => $otherUrls[$i],
										'zone_id' => $otherZones[$i],
										'banner_id' => $bannerId));
					}
				}
			}
			
			$this->_helper->getHelper('FlashMessenger')->addMessage(
				$this->view->translator('banner_edit_success')
			);
			$this->_redirect($this->view->serverUrl().$this->view->url(array('banner_id' => $bannerId), 'ad_banner_edit'));
		}
	}
	
	public function addAction() 
	{
		$this->view->addHelperPath(TOMATO_APP_DIR.DS.'modules'.DS.'upload'.DS.'views'.DS.'helpers', 'Upload_View_Helper_');
		$conn = Tomato_Core_Db_Connection::getMasterConnection();
		$pageGateway = new Tomato_Modules_Core_Model_LayoutPageGateway();
		$select = $conn->select()
					   ->from(array('p' => Tomato_Core_Db_Connection::getDbPrefix().'core_page'), array('page_id', 'title','name','url'));
		$rs = $select->query()->fetchAll();
		$pages = new Tomato_Core_Model_RecordSet($rs, $pageGateway);
		$this->view->assign('pages', $pages);
		
		$zoneGateway = new Tomato_Modules_Ad_Model_ZoneGateway();
		$zoneGateway->setDbConnection($conn);
		$zones = $zoneGateway->getAllZones();
		$this->view->assign('zones', $zones);
		
		$clientGateway = new Tomato_Modules_Ad_Model_ClientGateway();
		$clientGateway->setDbConnection($conn);
		$clients = $clientGateway->getAllClients();
		$this->view->assign('clients', $clients);
		
		if ($this->_request->isPost()) {
			$name = $this->_request->getPost('name');
			$text = $this->_request->getPost('text');
			$startDate = $this->_request->getPost('startDate');
			$expiredDate = $this->_request->getPost('expiredDate');
			$code = $this->_request->getPost('code');
			$clickUrl = $this->_request->getPost('clickUrl');
			$target = $this->_request->getPost('target');
			$format = $this->_request->getPost('format');
			$mode = $this->_request->getPost('mode');
			$timeout = $this->_request->getPost('timeout');
			$status = $this->_request->getPost('status');
			$pages = $this->_request->getPost('pages');
			$zones = $this->_request->getPost('zones');
			$otherZones = $this->_request->getPost('otherZones');
			$otherUrls = $this->_request->getPost('otherUrls');
			$otherPages = $this->_request->getPost('otherPages');
			$imageUrl = $this->_request->getPost('imageUrl');
			$clientId = $this->_request->getPost('client');
			$banner = new Tomato_Modules_Ad_Model_Banner(array(
				'name' 			        => $name,
				'text' 					=> $text,
				'created_date' 			=> date('Y-m-d H:i:s'),
				'code' 					=> $code,
				'click_url' 			=> $clickUrl,
				'format' 				=> $format,
				'image_url' 			=> $imageUrl,
				'mode' 					=> $mode,
				'timeout' 				=> 0,
			));
			if ($timeout) {
				$banner->timeout = $timeout;
			}
			if ($target) {
				$banner->target = $target;
			}
			if ($status) {
				$banner->status = $status;
			}
			if ($clientId) {
				$banner->client_id = $clientId;
			}
			if ($startDate) {
				$banner->start_date = date('Y-m-d', strtotime($startDate));
			}
			if ($expiredDate) {
				$banner->expired_date = date('Y-m-d', strtotime($expiredDate));
			}
			$bannerGateway = new Tomato_Modules_Ad_Model_BannerGateway();
			$bannerGateway->setDbConnection($conn);
			$id = $bannerGateway->add($banner);
			if ($id > 0) {
				if ($pages) {
					for ($i = 0; $i < count($pages); $i++) {
						$arr = explode("__", $pages[$i]);
						
						$conn->insert(Tomato_Core_Db_Connection::getDbPrefix().'ad_page_assoc', array(
										'page_name' => $arr[0],
										'page_url' => $arr[1],
										'zone_id' => $zones[$i],
										'banner_id' => $id));
					}
				}
				if ($otherUrls) {
					for ($i = 0; $i < count($otherUrls); $i++) {
						var_dump($otherUrls[$i]);
						$conn->insert(Tomato_Core_Db_Connection::getDbPrefix().'ad_page_assoc', array(
										'page_name' => $otherPages[$i],
										'page_url' => $otherUrls[$i],
										'zone_id' => $otherZones[$i],
										'banner_id' => $id));
					}
				}
				$this->_helper->getHelper('FlashMessenger')->addMessage(
					$this->view->translator('banner_add_success')
				);
				$this->_redirect($this->view->serverUrl().$this->view->url(array(), 'ad_banner_add'));
			}
		}
	}

	public function activateAction()
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		if ($this->_request->isPost()) {
			$id = $this->_request->getPost('id');
			$status = $this->_request->getPost('status');
			$status = ($status == 'inactive') ? 'active' : 'inactive';
			
			$conn = Tomato_Core_Db_Connection::getMasterConnection();
			$gateway = new Tomato_Modules_Ad_Model_BannerGateway();	
			$gateway->setDbConnection($conn);
			$gateway->updateStatus($id, $status);
			
			$this->_response->setBody($status);			
		}
	}
}
