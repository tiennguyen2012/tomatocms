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
 * @version 	$Id: UrlCache.php 958 2010-01-23 03:17:30Z huuphuoc $
 */

class Tomato_Core_Controller_Plugin_UrlCache extends Zend_Controller_Plugin_Abstract 
{
	private $_config = null;
	
	public function __construct() 
	{
		$file = TOMATO_APP_DIR.DS.'config'.DS.'cache_url.ini';
		if (!file_exists($file)) {
			return;
		}
		$this->_config = new Zend_Config_Ini($file, 'urls');
	}
	
	public function preDispatch(Zend_Controller_Request_Abstract $request) 
	{
		if (null == $this->_config) {
			return;
		}
		$currUri = $request->getRequestUri();
		$currUri = self::_normalizeUri($currUri);
		
		$array = $this->_config->urls->toArray();
		$match = false;
		$file = null;
		foreach ($array as $key => $value) {
			$uri = self::_normalizeUri($value['url']);
			 
			if ($value['type'] == 'static' && $uri == $currUri) {
				$match = true;
				$file = $value['file'];
			} elseif ($value['type'] == 'regex') {
				$pattern = '/'.str_replace('/', '\/', '^'.$uri).'/';
				// Meet the URI which match the pattern
				if (preg_match($pattern, $currUri, $matches)) {
					$match = true;
					$file = $value['file'];
					for ($i = 2; $i <= count($matches); $i++) {
						$file = str_replace('${'.($i-1).'}', $matches[$i - 1], $file);	
					}
				}
			}
			
			if ($match) {
				$file = str_replace('/', '__', $file);
				
				// Add template as file prefix
				// Because the user can browse by PC or mobile device which the templates are different
				// hence the cache version for same URL should be difference
				$template = (Zend_Registry::isRegistered('APP_TEMPLATE') 
							&& Zend_Registry::get('APP_TEMPLATE') != null)
							? Zend_Registry::get('APP_TEMPLATE') : '';
				$file = $template.'_'.$file;				
				
				$timeout = $value['timeout'];
				if (Tomato_Core_Cache_File::isCached(Tomato_Core_Cache_File::CACHE_URL, 
					$file, $timeout)
				) {
					$request->setModuleName('core')
							->setControllerName('Cache')
							->setActionName('html')
							->setParam('__cacheType', Tomato_Core_Cache_File::CACHE_URL)
							->setParam('__cacheKey', $file)
							->setDispatched(true);
				} else {
					// Continue action and assign flag to save output to cache later
					$request->setParam('__isCacheURL', true)
							->setParam('__key', $file);
				}
				// Exit the loop
				return;
			}
		}
	}
	
	public function postDispatch(Zend_Controller_Request_Abstract $request) 
	{
		if ($request->getParam('__isCacheURL') == true) {
			$key = $request->getParam('__key');
			$content = $this->getResponse()->getBody();
			$content .= '<!-- cached version from '.date('Y-m-d H:i:s').' -->';
			Tomato_Core_Cache_File::cache(Tomato_Core_Cache_File::CACHE_URL, $key, $content);
		}
	}
	
	private static function _normalizeUri($uri) 
	{
		$uri = ltrim($uri, '/');
		$uri = rtrim($uri, '/');
		return $uri;
	}
}