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
 * @version 	$Id: Layout.php 958 2010-01-23 03:17:30Z huuphuoc $
 */

class Tomato_Core_Layout 
{
	const LAYOUT_DOCTYPE = '<!DOCTYPE layout SYSTEM "http://schemas.tomatocms.com/dtd/layout.dtd">';
	
	const CACHE_LIFETIME_PARAM 			= '___cacheLifetime';
	const LOAD_AJAX_PARAM				= '___loadAjax';
	const PREVIEW_MODE_PARAM			= '___widgetPreviewMode';
	
	const WIDGET_JS_CLASS					= 'Tomato.Core.Layout.Widget';
	const WIDGET_DEFAULT_OUTPUT_JS_CLASS	= 'Tomato.Core.Layout.DefaultOutput';
	
	/**
	 * Save layout config to XML file
	 * 
	 * @param string $file The file name
	 * @param array $layout
	 */
	public static function save($file, $layout) 
	{
		// TODO: Should we use XMLElement to write the output file
		// to avoid from generating not well-form XML
		$output = '<?xml version="1.0" encoding="UTF-8"?>'.self::LAYOUT_DOCTYPE.'<layout>';
		$output .= self::_saveContainer($layout);
		$output .= '</layout>';
		
		// Write to file
		$f = fopen($file, 'w');
		fwrite($f, $output);
		fclose($f);
	}
	
	private static function _saveContainer($container) 
	{
		$isRoot = ($container['isRoot'] == 1);
		$output = '';
		$pos = isset($container['position']) ? $container['position'] : '';
//		if (!$isRoot && $pos == 'first') {
//			$output .= '<container cols="12">';
//		}
		
		$pos2 = isset($container['position']) ? ' position="'.$container['position'].'"' : '';
		if (!$isRoot) {
			$output .= '<container cols="'.$container['cols'].'"'.$pos2.'>';
		}
		
		// Output child containers
		foreach ($container['containers'] as $index => $childContainer) {
			$output .= self::_saveContainer($childContainer);
		}
		// Output widgets
		foreach ($container['widgets'] as $index => $widget) {
			$output .= self::_saveWidget($widget);
		}
		if (!$isRoot) {
			$output .= '</container>';
		}
		
//		if (!$isRoot && $pos == 'last') {
//			$output .= '</container>';
//		}
		
		return $output;
	}
	
	private static function _saveWidget($widget) 
	{
		if ($widget['cls'] == self::WIDGET_DEFAULT_OUTPUT_JS_CLASS) {
			return '<defaultOutput />';
		}
		
		$load = 'php';
		if (count($widget['params']) > 0 && isset($widget['params'][self::LOAD_AJAX_PARAM]) 
				&& $widget['params'][self::LOAD_AJAX_PARAM]['value'] != '') {
			$load = 'ajax';	
		}
		unset($widget['params'][self::LOAD_AJAX_PARAM]);
		
		$output = '<widget module="'.$widget['module'].'" name="'.$widget['name'].'" load="'.$load.'">';
		// Output title
		if ($widget['title']) {
			$output .= '<title><![CDATA['.$widget['title'].']]></title>';	
		}
		
		// Output resources
		if (count($widget['resources']) > 0) {
			$output .= '<resources>';
			foreach ($widget['resources']['css'] as $index => $css) {
				$output .= '<resource type="css" src="'.$css.'" />';
			}
			foreach ($widget['resources']['javascript'] as $index => $js) {
				$output .= '<resource type="javascript" src="'.$js.'" />';
			}
			$output .= '</resources>';
		}
		// Output params
		$cacheLifetime = null;
		if (count($widget['params']) > 0) {
			$output .= '<params>';
			foreach ($widget['params'] as $param => $data) {
				if ($data['type'] == 'global') {
					$output .= '<param name="'.$param.'" type="global" />';
				} else {
					// Use CDATA to store the value of param
					$value = ltrim($data['value'], ' ');
					$value = rtrim($value, ' ');
					
					// Store cache settings
					if ($param == self::CACHE_LIFETIME_PARAM) {
						$cacheLifetime = $data['value'];	
					} else {
						$output .= '<param name="'.$param.'"><value><![CDATA['.$value.']]></value></param>';
					}
				}
			}
			$output .= '</params>';
		}
		if ($cacheLifetime) {
			$output .= '<cache lifetime="'.$cacheLifetime.'" />';
		}
		
		$output .= '</widget>';
		return $output;
	}
	
	/**
	 * Load layout from XML file
	 * 
	 * @param string $file
	 * @return array
	 */
	public static function load($file) 
	{
		$xml = simplexml_load_file($file);
		$array = self::_loadContainer($xml);
		$return = array(
			'isRoot' => 1,
			'cols' => 12,
			'containers' => $array['containers'],
			'widgets' => $array['widgets'],
		);
		return $return;
	}
	
	private static function _loadContainer($containerNode) 
	{
		$return = array(
			'containers' => null,
			'widgets' => null,
		);
		if (null == $containerNode) {
			return $return;
		}
		$attrs = $containerNode->attributes();
		$return = array(
			'isRoot' => 0,
			'cols' => (string) $attrs['cols'],
			'containers' => array(),
			'widgets' => array(),
		);
		if (($pos = (string) $attrs['position'])) {
			$return['position'] = $pos;
		}
		foreach ($containerNode->container as $node) {
			$return['containers'][] = self::_loadContainer($node);
		}
		if ($containerNode->defaultOutput) {
			$return['widgets'][] = array(
				'cls' => self::WIDGET_DEFAULT_OUTPUT_JS_CLASS,
				'module' => null,
				'name' => null,
				'title' => null,
				'resources' => null,
				'params' => null,
			);
		}
		foreach ($containerNode->widget as $node) {
			$return['widgets'][] = self::_loadWidget($node);
		}
		return $return;
	}
	
	private static function _loadWidget($widgetNode) 
	{
		if (null == $widgetNode) {
			return array();
		}
		$attrs = $widgetNode->attributes();
		$title = isset($widgetNode->title) ? (string)$widgetNode->title : '';
		$return = array(
			'cls' => self::WIDGET_JS_CLASS,
			'module' => (string) $attrs['module'],
			'name' => (string) $attrs['name'],
			'title' => $title,
			'resources' => array(),
			'params' => array(),
		);
		// Load method
		if (isset($attrs['load']) && ((string)$attrs['load'] == 'ajax')) {
			$return['params'][self::LOAD_AJAX_PARAM] = array('value' => true);
		}
		
		if ($widgetNode->resources) {
			foreach ($widgetNode->resources->resource as $resource) {
				$attrs = $resource->attributes();
				$type = (string) $attrs['type'];
				$src = (string) $attrs['src'];
				if (!isset($return['resources'][$type])) {
					$return['resources'][$type] = array();
				}
				$return['resources'][$type][] = $src;
			}
		}
		if ($widgetNode->params) {
			foreach ($widgetNode->params->param as $param) {
				$attrs = $param->attributes();
				$name = (string) $attrs['name'];
				$return['params'][$name] = array(
					'value' => (string) $param->value, //$attrs['value'],
					'type' => isset($attrs['type']) ? 'global' : '',
				);
			}
		}
		
		// Cache setting
		if ($widgetNode->cache) {
			$attrs = $widgetNode->cache->attributes();
			$return['params'][self::CACHE_LIFETIME_PARAM] = array('value' => (string)$attrs['lifetime']);
		}
		
		return $return;
	}
}