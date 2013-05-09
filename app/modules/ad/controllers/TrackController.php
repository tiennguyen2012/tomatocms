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
 * @version 	$Id: TrackController.php 1510 2010-03-09 08:56:04Z huuphuoc $
 */

class Ad_TrackController extends Zend_Controller_Action 
{
	/* ========== Frontend actions ========================================== */
	
	public function redirectAction() 
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		$conn = Tomato_Core_Db_Connection::getMasterConnection();
		
		$bannerId = $this->_request->getParam('bannerId');
		$zoneId = $this->_request->getParam('zoneId');
		$gotoUrl = $this->_request->getParam('clickUrl');
		$pageId = $this->_request->getParam('pageId');
		$ip = $this->_request->getClientIp();
		$fromUrl = $this->_request->getServer('HTTP_REFERER');
		
		$track = new Tomato_Modules_Ad_Model_Track(array(
			'banner_id' => $bannerId,
			'zone_id' => $zoneId,
			'page_id' => $pageId,
			'clicked_date' => date('Y-m-d H:i:s'),
			'ip' => $ip,
			'from_url' => $fromUrl,
		));
		$trackGateway = new Tomato_Modules_Ad_Model_TrackGateway();
		$trackGateway->setDbConnection($conn);
		$trackGateway->add($track);
//		$this->_redirect($gotoUrl);

		// Use javascript redirect to support link that have format mailto
		echo '<script type="text/javascript">window.location="'.addslashes($gotoUrl).'"</script>';
	}
}
