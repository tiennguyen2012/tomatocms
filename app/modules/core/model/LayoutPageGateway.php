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
 * @version 	$Id: LayoutPageGateway.php 1669 2010-03-23 04:48:06Z huuphuoc $
 */

class Tomato_Modules_Core_Model_LayoutPageGateway extends Tomato_Core_Model_Gateway 
{
	/**
	 * (non-PHPdoc)
	 * @see app/core/model/Tomato_Core_Model_Gateway#convert($entity)
	 */	
	public function convert($entity) 
	{
		return new Tomato_Modules_Core_Model_LayoutPage($entity); 
	}
	
	/**
	 * @param string $name
	 * @return Tomato_Modules_Core_Model_LayoutPage
	 */
	public function getPageByName($name) 
	{
		$select = $this->_conn
					->select()
					->from(array('p' => $this->_prefix.'core_page'))
					->where('p.name = ?', $name)
					->limit(1);
		$row = $select->query()->fetch();
		return (null == $row) ? null : new Tomato_Modules_Core_Model_LayoutPage($row);
	}	
	
	/**
	 * @param int $id
	 * @return int
	 */
	public function delete($id) 
	{
		$where = array();
		$where[] = 'page_id = '.$this->_conn->quote($id);
		return $this->_conn->delete($this->_prefix.'core_page', $where);
	}
	
	/**
	 * Export all layout pages to INI file
	 * 
	 * @return boolean
	 */
	public function export($file) 
	{
		$select = $this->_conn
					->select()
					->from(array('p' => $this->_prefix.'core_page'))
					->order('p.ordering ASC');
		$rs = $select->query()->fetchAll();
		if (null == $rs) {
			return true;
		}
		$data = array();
		foreach ($rs as $row) {
			$data[$row->name] = array(
				'type' => $row->url_type,
				'url' => $row->url,
				'file' => $row->name.'.xml',
				'priority' => (int)$row->ordering,
			);
			if ($row->url_type == 'regex' && $row->params) {
				$data[$row->name]['map'] = array();
				$params = Zend_Json::decode($row->params);
				foreach ($params as $param => $index) {
					$data[$row->name]['map'][$index] = $param;
				}
			}
		}
		$output = array();
		$output['layouts']['layouts'] = $data;
		$config = new Zend_Config($output, array('allowModifications' => true));
		$writer = new Zend_Config_Writer_Ini();
		$writer->write($file, $config);
		
		return true;
	}
	
	/**
	 * Add new page
	 * 
	 * @param Tomato_Modules_Core_Model_LayoutPage $page
	 * @return int
	 */
	public function add($page) 
	{
		$this->_conn->insert($this->_prefix.'core_page', array(
			'name' => $page->name,
			'title' => $page->title,
			'description' => $page->description,
			'url' => $page->url,
			'url_type' => $page->url_type,
			'params' => $page->params,
			'ordering' => $page->ordering,		
		));
		return $this->_conn->lastInsertId($this->_prefix.'core_page');
	}
	
	/**
	 * Update page
	 * 
	 * @param Tomato_Modules_Core_Model_LayoutPage $page
	 * @return int
	 */
	public function update($page) 
	{
		$where[] = 'page_id = '.$this->_conn->quote($page->page_id);
		return $this->_conn->update($this->_prefix.'core_page', array(
				'title' => $page->title,
				'description' => $page->description,
				'url' => $page->url,
				'url_type' => $page->url_type,
				'params' => $page->params,
			), $where);
	}
}
