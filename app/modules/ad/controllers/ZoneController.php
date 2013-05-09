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
 * @version 	$Id: ZoneController.php 1545 2010-03-10 07:46:33Z huuphuoc $
 */

class Ad_ZoneController extends Zend_Controller_Action 
{
	const KEY = 'TOMATO_MODULES_AD_ZONE_INIT';
	
	/* ========== Frontend actions ========================================== */
	
	public function loadAction() 
	{
		Zend_Registry::set(Tomato_Core_GlobalKey::LOG_REQUEST, false);
		
		$this->getHelper('layout')->disableLayout();
		$this->_response->setHeader('Content-type', 'application/x-javascript');
//		header('Content-type', 'application/x-javascript');
		
		if (!Zend_Registry::isRegistered(self::KEY) || null == Zend_Registry::get(self::KEY)) {
			// Load global banners and zones for the once time
			Zend_Registry::set(self::KEY, true);
			
			$conn = Tomato_Core_Db_Connection::getSlaveConnection();
			$zoneGateway = new Tomato_Modules_Ad_Model_ZoneGateway();
			$zoneGateway->setDbConnection($conn);
			
			// Get the list of zones
			$zones = $zoneGateway->getAllZones();
			$this->view->assign('zones', $zones);
			
			// Get the list of banners
			$bannerGateway = new Tomato_Modules_Ad_Model_BannerGateway();
			$bannerGateway->setDbConnection($conn);
			$select = $conn->select()
							->from(array('b' => Tomato_Core_Db_Connection::getDbPrefix().'ad_banner'))
							->joinInner(array('pa' => Tomato_Core_Db_Connection::getDbPrefix().'ad_page_assoc'),
								'b.banner_id = pa.banner_id', array('banner_zone_id' => 'zone_id', 'page_name', 'page_url'))
							->where('b.status = ?', 'active');
			$rs = $select->query()->fetchAll();
			$banners = new Tomato_Core_Model_RecordSet($rs, $bannerGateway);
			$this->view->assign('banners', $banners);
		}
	}
	
	/* ========== Backend actions =========================================== */
	
	public function listAction() 
	{
		$conn = Tomato_Core_Db_Connection::getSlaveConnection();
		$zoneGateway = new Tomato_Modules_Ad_Model_ZoneGateway();
		$zoneGateway->setDbConnection($conn);
		$zones = $zoneGateway->getAllZones();
		$this->view->assign('zones', $zones);
	}
	
	public function deleteAction() 
	{
		$this->_helper->getHelper('layout')->disableLayout();
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		
		if ($this->_request->isPost()) {
			$id = $this->_request->getPost('id');
			$conn = Tomato_Core_Db_Connection::getMasterConnection();
			
			$gateway = new Tomato_Modules_Ad_Model_ZoneGateway();
			$gateway->setDbConnection($conn);
			$gateway->delete($id);
		}
		$this->_response->setBody('RESULT_OK');
	}
	
	public function editAction() 
	{
		$conn = Tomato_Core_Db_Connection::getMasterConnection();
		$zoneGateway = new Tomato_Modules_Ad_Model_ZoneGateway();
		$zoneGateway->setDbConnection($conn);
		
		$zoneId = $this->_request->getParam('zone_id');
		$zone = $zoneGateway->getZoneById($zoneId);
		$this->view->assign('zone', $zone);
		
		if ($this->_request->isPost()) {
			$zoneId = $this->_request->getPost('zone_id');
			$name = $this->_request->getPost('name');
			$description = $this->_request->getPost('description');
			$width = $this->_request->getPost('width');
			$height = $this->_request->getPost('height');
			
			$zone = new Tomato_Modules_Ad_Model_Zone(array(
				'zone_id' => $zoneId,
				'name' => $name,
				'description' => $description,
				'width' => $width,
				'height' => $height,
			));
			$result = $zoneGateway->update($zone);
			if ($result > 0) {
				$this->_helper->getHelper('FlashMessenger')->addMessage(
					$this->view->translator('zone_edit_success')
				);
				$this->_redirect($this->view->serverUrl().$this->view->url(array('zone_id' => $zoneId), 'ad_zone_edit'));
			}
		}
	}
	
	/**
	 * Check if the zone name has been existed or not
	 */
	public function checkAction() 
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		$conn = Tomato_Core_Db_Connection::getMasterConnection();
		$select = $conn->select()
					->from(array('z' => Tomato_Core_Db_Connection::getDbPrefix().'ad_zone'), array('num_zones' => 'COUNT(*)'));
					
		$original = $this->_request->getParam('original');
		$result = false;			
		$name = $this->_request->getParam('name');
		if ($original == null || ($original != null && $name != $original)) {
			$select->where('z.name = ?', $name)
				   ->limit(1);
			$rs = $select->query()->fetch();
			$numZones = $rs->num_zones;
			$result = ($numZones == 0) ? false : true;
		}
		($result == true) ? $this->getResponse()->setBody('false') 
						: $this->getResponse()->setBody('true');		
	}
	
	public function addAction() 
	{
		$conn = Tomato_Core_Db_Connection::getMasterConnection();
		if ($this->_request->isPost()) {
			$name = $this->_request->getPost('name');
			$description = $this->_request->getPost('description');
			$width = $this->_request->getPost('width');
			$height = $this->_request->getPost('height');
			$params = $this->_request->getPost('params');
			
			$zone = new Tomato_Modules_Ad_Model_Zone(array(
				'name' => $name,
				'description' => $description,
				'width' => $width,
				'height' => $height,
			));
			$zoneGateway = new Tomato_Modules_Ad_Model_ZoneGateway();
			$zoneGateway->setDbConnection($conn);
			$id = $zoneGateway->add($zone);
			if ($id > 0) {
				$this->_helper->getHelper('FlashMessenger')->addMessage(
					$this->view->translator('zone_add_success')
				);
				$this->_redirect($this->view->serverUrl().$this->view->url(array(), 'ad_zone_add'));
			}
		}
	}
}
