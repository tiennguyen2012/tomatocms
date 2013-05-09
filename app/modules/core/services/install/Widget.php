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
 * @version 	$Id: Widget.php 1604 2010-03-12 08:40:51Z huuphuoc $
 * @since		2.0.3
 */

/**
 * Install all widgets belong to given module
 */
class Tomato_Modules_Core_Services_Install_Widget
{
	/**
	 * @var Tomato_Modules_Core_Model_WidgetGateway
	 */
	private $_widgetGateway;
	
	/**
	 * Set widget gateway
	 * 
	 * @param Tomato_Modules_Core_Model_WidgetGateway $gateway
	 */
	public function setWidgetGateway($gateway) 
	{
		$this->_widgetGateway = $gateway;
	}
	
	/**
	 * Install all widgets in module
	 * 
	 * @param string $module Name of module
	 */
	public function install($module) 
	{
		// Load all widgets from module
		$widgetDirs = Tomato_Core_Utility_File::getSubDir(TOMATO_APP_DIR.DS.'modules'.DS.$module.DS.'widgets');
		if (0 == count($widgetDirs)) {
			return;
		}
		foreach ($widgetDirs as $widgetName) {
			$info = Tomato_Core_Widget_Config::getWidgetInfo($module, $widgetName);
			if ($info != null) { 				
				$widget = new Tomato_Modules_Core_Model_Widget($info);
				$this->_widgetGateway->add($widget);
			}
		}
	}
}
