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
 * @version 	$Id: Translator.php 1452 2010-03-04 10:22:29Z huuphuoc $
 * @since		2.0.3
 */

class Tomato_Core_View_Helper_Translator extends Zend_View_Helper_Abstract
{
	/**
	 * Current application's language
	 * 
	 * @var string
	 */
	private static $_lang = null;
	
	public function __construct()
	{
		self::$_lang = Tomato_Core_Config::getConfig()->web->lang;
	}
	
	/**
	 * @param string $key Key to translate
	 * @param string $module Name of module. If this is not specified, 
	 * it will take the current module
	 * @return string
	 */
	public function translator($key = null, $module = null)
	{
		if (null == $key && null == $module) {
			return $this;
		}
		
		if (null == $module) {
			// Get current module
			$module = Zend_Controller_Front::getInstance()->getRequest()->getModuleName();
		}
		
		// Get current language
		$lang = self::$_lang;
		$file = TOMATO_APP_DIR . DS . 'modules' . DS . $module . DS
				 . 'languages' . DS . 'lang.' . $lang . '.ini';
		if (file_exists($file) && file_get_contents($file) != '') {
			$translate = new Zend_Translate('Ini', $file, $lang);
			return $translate->_($key);
		} 
		return $key;
	}
	
	/**
	 * @param string $key Key to translate
	 * @return string
	 */
	public function widget($key)
	{
		// Get current widget instance which have been set in 
		// __call() of Tomato_Core_Widget
		if (!Zend_Registry::isRegistered(Tomato_Core_Widget::CURRENT_WIDGET_KEY)) {
			return $key;
		}
		$widget = Zend_Registry::get(Tomato_Core_Widget::CURRENT_WIDGET_KEY);
		if (null == $widget || !($widget instanceof Tomato_Core_Widget)) {
			return $key;
		}
		
		$lang = self::$_lang;
	 	$file = TOMATO_APP_DIR . DS . 'modules' . DS . strtolower($widget->getModule()) . DS 
	 			. 'widgets' . DS . strtolower($widget->getName()) . DS . 'lang.' . $lang . '.ini';
		if (file_exists($file) && file_get_contents($file) != '') {
            $translate = new Zend_Translate('Ini', $file, $lang);
			return $translate->_($key);
		}
		return $key;
	}
}
