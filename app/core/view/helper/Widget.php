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
 * @version 	$Id: Widget.php 958 2010-01-23 03:17:30Z huuphuoc $
 */

class Tomato_Core_View_Helper_Widget extends Zend_View_Helper_Abstract 
{
	public function widget($module, $name, array $params = array()) 
	{
		$module = strtolower($module);
		$name = strtolower($name);
		
		$timeout = isset($params[Tomato_Core_Layout::CACHE_LIFETIME_PARAM]) 
					? $params[Tomato_Core_Layout::CACHE_LIFETIME_PARAM] : null;
		$cache = Tomato_Core_Cache::getInstance();
		$widgetClass = 'Tomato_Modules_'.$module.'_Widgets_'.$name.'_Widget';
		
		if (!class_exists($widgetClass)) {
			// TODO: Should we inform to user that the widget does not exist
			return '';
		}
		
		if ($cache && $timeout != null) {
			// The cache key ensure we will get the same cached value 
			// if the widget has been cached on other pages
			$cacheKey = $widgetClass.'_'.md5($module.'_'.$name.'_'.serialize($params));
			$cache->setLifetime($timeout);
			
			if (!($fromCache = $cache->load($cacheKey))) {
				$widget = new $widgetClass($module, $name);
				$content = $widget->show($params);
				$cache->save($content, $cacheKey, array('Tomato_Modules_'.$module.'_Widgets'));
				return $content;
			} else {
				return $fromCache;
			}
		} else {
			$widget = new $widgetClass($module, $name);
			return $widget->show($params);
		}
	}
}