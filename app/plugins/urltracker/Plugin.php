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

class Tomato_Plugins_UrlTracker_Plugin extends Tomato_Core_Controller_Plugin 
{
	const SESSION_KEY = 'Tomato_Plugins_UrlTracker_Plugin_CurrentUri';
	
	public function __construct() 
	{
		parent::__construct();
	}
	
	public function preDispatch(Zend_Controller_Request_Abstract $request) 
	{
		$currentUri = $request->getRequestUri();
		if (null == $currentUri || '' == $currentUri) {
			return;
		}
//		$currentUri = strtolower($currentUri);
		$currentUri = Tomato_Core_Utility_String::normalizeUri($currentUri);
		
		$excludeUrls = $this->getParam('excludeUrls');
		if (null == $excludeUrls) {
			return;
		}
		$excludeUrls = explode(';', $excludeUrls);
		$excluded = false;
		foreach ($excludeUrls as $url) {
			$url = Tomato_Core_Utility_String::normalizeUri($url);
			$pattern = '/'.str_replace('/', '\/', '^'.$url).'/';
			if (preg_match($pattern, $currentUri, $matches)) {
				$excluded = true;
				break;
			}
		}
		if (!$excluded) {
			$uriSession = new Zend_Session_Namespace(self::SESSION_KEY);
			$uriSession->uri = $uri;
		}
	}
}