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
 * @version 	$Id: BannerGateway.php 1669 2010-03-23 04:48:06Z huuphuoc $
 */

class Tomato_Modules_Ad_Model_BannerGateway extends Tomato_Core_Model_Gateway 
{
	/**
	 * (non-PHPdoc)
	 * @see app/core/model/Tomato_Core_Model_Gateway#convert($entity)
	 */	
	public function convert($entity) 
	{
		return new Tomato_Modules_Ad_Model_Banner($entity); 
	}
	
	public function getBannerById($id) 
	{
		$select = $this->_conn
						->select()
						->from(array('b' => $this->_prefix.'ad_banner'))
						->where('b.banner_id = ?', $id)
						->limit(1);
		$rs = $select->query()->fetchAll();
		$banners = new Tomato_Core_Model_RecordSet($rs, $this);
		return (count($banners) == 0) ? null : $banners[0];	
	}
	
	/**
	 * Add new banner
	 * 
	 * @param Tomato_Modules_Ad_Model_Banner $banner
	 * @return int
	 */
	public function add($banner) 
	{
		$this->_conn->insert($this->_prefix.'ad_banner', array(
			'name'			=> $banner->name,
			'text' 			=> $banner->text,
			'created_date' 	=> $banner->created_date,
			'start_date' 	=> $banner->start_date,
			'expired_date' 	=> $banner->expired_date,
			'code' 			=> $banner->code,
			'click_url' 	=> $banner->click_url,
			'target' 		=> $banner->target,
			'format' 		=> $banner->format,
			'image_url' 	=> $banner->image_url,
			'mode' 			=> $banner->mode,
			'timeout' 		=> $banner->timeout,
			'client_id' 	=> $banner->client_id,
			'status' 		=> $banner->status,
		));
		return $this->_conn->lastInsertId($this->_prefix.'ad_banner');
	}
	
	/**
	 * Update banner
	 * 
	 * @param Tomato_Modules_Ad_Model_Banner $banner
	 * @return int
	 */
	public function update($banner) 
	{
		$where[] = 'banner_id = '.$this->_conn->quote($banner->banner_id);
		return $this->_conn->update($this->_prefix.'ad_banner', array(
					'name'			=> $banner->name,
					'text' 			=> $banner->text,
					'start_date' 	=> $banner->start_date,
					'expired_date' 	=> $banner->expired_date,
					'code' 			=> $banner->code,
					'click_url' 	=> $banner->click_url,
					'target' 		=> $banner->target,
					'format' 		=> $banner->format,
					'image_url' 	=> $banner->image_url,
					'mode' 			=> $banner->mode,
					'timeout' 		=> $banner->timeout,
					'client_id' 	=> $banner->client_id,
					'status' 		=> $banner->status,
				), $where);			
	}
	
	/**
	 * @param int $start
	 * @param int $offset
	 * @param array $exp Search expression. An array contain various conditions, keys including:
	 * 'keyword', 'banner_id', 'page_id', 'status'
	 * @return Tomato_Core_Model_RecordSet
	 */
	public function find($start, $offset, $exp = null) 
	{
		$select = $this->_conn
				->select()
				->from(array('b' => $this->_prefix.'ad_banner'));
		if ($exp) {
			if (isset($exp['page_name'])) {
				$select->joinInner(array('pa' => $this->_prefix.'ad_page_assoc'), 'b.banner_id = pa.banner_id', array('')); 
				$select->where('pa.page_name = ?', $exp['page_name']);
			}
			if (isset($exp['banner_id'])) {
				$select->where('b.banner_id = ?', $exp['banner_id']);
			}
			if (isset($exp['status'])) {
				$select->where('b.status = ?', $exp['status']);
			}
			if (isset($exp['keyword'])) {
				$select->where('b.name LIKE \'%'.addslashes($exp['keyword']).'%\'');
			}
		}
		$select->order('b.banner_id DESC')
				->limit($offset, $start);
		$rs = $select->query()->fetchAll();
		return new Tomato_Core_Model_RecordSet($rs, $this);
	}
	
	/**
	 * @param array $exp Search expression (@see find)
	 * @return int
	 */
	public function count($exp = null) 
	{
		$select = $this->_conn
				->select()
				->from(array('b' => $this->_prefix.'ad_banner'), array('num_banners' => 'COUNT(*)'));
		if ($exp) {
			if (isset($exp['page_name'])) {
				$select->joinInner(array('pa' => $this->_prefix.'ad_page_assoc'), 'b.banner_id = pa.banner_id', array('')); 
				$select->where('pa.page_name = ?', $exp['page_name']);
			}
			if (isset($exp['banner_id'])) {
				$select->where('b.banner_id = ?', $exp['banner_id']);
			}
			if (isset($exp['status'])) {
				$select->where('b.status = ?', $exp['status']);
			}
			if (isset($exp['keyword'])) {
				$select->where('b.name LIKE \'%'.addslashes($exp['keyword']).'%\'');
			}
		}
		$row = $select->query()->fetch();
		return $row->num_banners;
	}
	
	/**
	 * @param int $id
	 * @return int
	 */
	public function delete($id) 
	{
		$where = array();
		$where[] = 'banner_id = '.$this->_conn->quote($id);
		return $this->_conn->delete($this->_prefix.'ad_banner', $where);
	}
	
	/**
	 * @param int $id
	 * @param string $id
	 * @return int
	 */
	public function updateStatus($id, $status) 
	{
		$where[] = 'banner_id = '.$this->_conn->quote($id);
		return $this->_conn->update($this->_prefix.'ad_banner', array('status' => $status), $where);			
	}
}
