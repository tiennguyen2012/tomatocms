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
 * @version 	$Id: TrackGateway.php 1669 2010-03-23 04:48:06Z huuphuoc $
 */

class Tomato_Modules_Ad_Model_TrackGateway extends Tomato_Core_Model_Gateway 
{
	/**
	 * (non-PHPdoc)
	 * @see app/core/model/Tomato_Core_Model_Gateway#convert($entity)
	 */	
	public function convert($entity) 
	{
		return new Tomato_Modules_Ad_Model_Track($entity);
	}
	
	/**
	 * Add new track
	 * 
	 * @param Tomato_Modules_Ad_Model_Track $track
	 * @return int
	 */
	public function add($track) 
	{
		$this->_conn->insert($this->_prefix.'ad_click', array(
				'banner_id' => $track->banner_id,
				'zone_id' => $track->zone_id,
				'page_id' => $track->page_id,
				'clicked_date' => $track->clicked_date,
				'ip' => $track->ip,
				'from_url' => $track->from_url,
			)
		);
		return $this->_conn->lastInsertId($this->_prefix.'ad_click');
	}
}