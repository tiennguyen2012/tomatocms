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
 * @version 	$Id: WidgetGateway.php 1669 2010-03-23 04:48:06Z huuphuoc $
 */

class Tomato_Modules_Core_Model_WidgetGateway extends Tomato_Core_Model_Gateway 
{
	/**
	 * (non-PHPdoc)
	 * @see app/core/model/Tomato_Core_Model_Gateway#convert($entity)
	 */	
	public function convert($entity) 
	{
		return new Tomato_Modules_Core_Model_Widget($entity); 
	}
	
	/**
	 * @param Tomato_Modules_Core_Model_Widget $widget
	 * @return int
	 */
	public function add($widget) 
	{
		$this->_conn->insert($this->_prefix.'core_widget', array(
			'name' => $widget->name,
			'title' => $widget->title,
			'module' => $widget->module,
			'description' => $widget->description,
			'thumbnail' => $widget->thumbnail,
			'author' => $widget->author,
			'email' => $widget->email,
			'version' => $widget->version,
			'license' => $widget->license,
		));
		return $this->_conn->lastInsertId($this->_prefix.'core_widget');
	}
	
	/**
	 * @param int $id
	 * @return int
	 */
	public function delete($id) 
	{
		$where = array();
		$where[] = 'widget_id = '.$this->_conn->quote($id);
		return $this->_conn->delete($this->_prefix.'core_widget', $where);	
	}
}
