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
 * @version 	$Id: Plugin.php 958 2010-01-23 03:17:30Z huuphuoc $
 */

class Tomato_Plugins_IpLock_Plugin extends Tomato_Core_Controller_Plugin 
{
	public function __construct() 
	{
		parent::__construct();
	}
	
	public function routeStartup(Zend_Controller_Request_Abstract $request) 
	{
		$ips = $this->getParam('ips');
		if (null == $ips || '' == $ips) {
			return;
		}
		$ips = explode(',', $ips);
		$ip = $request->getClientIp();
		if (in_array($ip, $ips)) {
			$request->setModuleName('core')
					->setControllerName('Auth')
					->setActionName('deny')
					->setDispatched(true);
		}
	}
}