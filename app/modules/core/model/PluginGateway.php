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
 * @version 	$Id: PluginGateway.php 1669 2010-03-23 04:48:06Z huuphuoc $
 */

class Tomato_Modules_Core_Model_PluginGateway extends Tomato_Core_Model_Gateway 
{
	/**
	 * (non-PHPdoc)
	 * @see app/core/model/Tomato_Core_Model_Gateway#convert($entity)
	 */	
	public function convert($entity) 
	{
		return new Tomato_Modules_Core_Model_Plugin($entity); 
	}
	
	/**
	 * @param Tomato_Modules_Core_Model_Plugin $plugin
	 * @return int
	 */
	public function add($plugin) 
	{
		$this->_conn->insert($this->_prefix.'core_plugin', array(
			'name' => $plugin->name,
			'description' => $plugin->description,
			'thumbnail' => $plugin->thumbnail,
			'author' => $plugin->author,
			'email' => $plugin->email,
			'version' => $plugin->version,
			'license' => $plugin->license,
			'ordering' => $plugin->ordering,
		));
		return $this->_conn->lastInsertId($this->_prefix.'core_plugin');
	}
	
	/**
	 * @param int $id
	 * @return int
	 */
	public function delete($id) 
	{
		$where = array();
		$where[] = 'plugin_id = '.$this->_conn->quote($id);
		return $this->_conn->delete($this->_prefix.'core_plugin', $where);	
	}
	
	/**
	 * @return Tomato_Core_Model_RecordSet
	 */
	public function getOrderedPlugins() 
	{
		$select = $this->_conn
						->select()
						->from(array('p' => $this->_prefix.'core_plugin'))
						->order('p.ordering ASC');
		$rs = $select->query()->fetchAll();
		return new Tomato_Core_Model_RecordSet($rs, $this);
	}
}
