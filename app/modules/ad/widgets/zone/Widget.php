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
 * @version 	$Id: Widget.php 1461 2010-03-05 04:07:47Z huuphuoc $
 */

class Tomato_Modules_Ad_Widgets_Zone_Widget extends Tomato_Core_Widget 
{
	protected function _prepareShow() 
	{
		$containerId = $this->_request->getParam('container');
		$zoneId = $this->_request->getParam('code');
		$url = $this->_request->getParam('url', 
			//$this->_request->getRequestUri()
			$this->_request->getPathInfo()
		);

		$this->_view->assign('zoneId', $zoneId);
		$this->_view->assign('containerId', $containerId);
		$this->_view->assign('url', $url);
	}
	
	protected function _prepareConfig() 
	{
		$conn = Tomato_Core_Db_Connection::getSlaveConnection();
		$zoneGateway = new Tomato_Modules_Ad_Model_ZoneGateway();
		$zoneGateway->setDbConnection($conn);
		
		// Get the list of zones
		$zones = $zoneGateway->getAllZones();
		$this->_view->assign('zones', $zones);
	}
}