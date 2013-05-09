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
 * @version 	$Id: ClientGateway.php 1669 2010-03-23 04:48:06Z huuphuoc $
 */

class Tomato_Modules_Ad_Model_ClientGateway extends Tomato_Core_Model_Gateway 
{
	/**
	 * (non-PHPdoc)
	 * @see app/core/model/Tomato_Core_Model_Gateway#convert($entity)
	 */	
	public function convert($entity) 
	{
		return new Tomato_Modules_Ad_Model_Client($entity); 
	}
	
	/**
	 * @param int $id
	 * @return Tomato_Modules_Ad_Model_Client
	 */
	public function getClientById($id) 
	{
		$select = $this->_conn
						->select()
						->from(array('z' => $this->_prefix.'ad_client'))
						->where('z.client_id = ?', $id)
						->limit(1);
		$rs = $select->query()->fetchAll();
		$clients = new Tomato_Core_Model_RecordSet($rs, $this);
		return (count($clients) == 0) ? null : $clients[0];	
	}
	
	/**
	 * Get all clients
	 * 
	 * @return Tomato_Core_Model_RecordSet
	 */
	public function getAllClients() 
	{
		$select = $this->_conn
						->select()
						->from(array('c' => $this->_prefix.'ad_client'));
		$rs = $select->query()->fetchAll();
		return new Tomato_Core_Model_RecordSet($rs, $this);
	}
	
	/**
	 * @param int $id
	 * @return int
	 */
	public function delete($id) 
	{
		$where = array();
		$where[] = 'client_id = '.$this->_conn->quote($id);
		return $this->_conn->delete($this->_prefix.'ad_client', $where);
	}
	
	/**
	 * Update page
	 * 
	 * @param Tomato_Modules_Ad_Model_Client $client
	 * @return int
	 */
	public function update($client) 
	{
		$where[] = 'client_id = '.$this->_conn->quote($client->client_id);
		return $this->_conn->update($this->_prefix.'ad_client', 
						array(
							'name' => $client->name,
							'email' => $client->email,
							'telephone' => $client->telephone,
							'address' => $client->address,
						), $where);		
	}
	
	/**
	 * Add new client
	 * 
	 * @param Tomato_Modules_Ad_Model_Client $client
	 * @return int
	 */
	public function add($client) 
	{
		$this->_conn->insert(
			$this->_prefix.'ad_client', array(
				'name' => $client->name,
				'email' => $client->email,
				'telephone' => $client->telephone,
				'address' => $client->address,
				'created_date' => $client->created_date,
			)
		);
		return $this->_conn->lastInsertId($this->_prefix.'ad_client');
	}
	
	/**
	 * @param int $start
	 * @param int $offset
	 * @param array $exp Search expression. An array contain various conditions, keys including:
	 * 'name', 'email', 'address'
	 * @return Tomato_Core_Model_RecordSet
	 */
	public function find($start, $offset, $exp = null) 
	{
		$select = $this->_conn
				->select()
				->from(array('c' => $this->_prefix.'ad_client'));
		if ($exp) {
			if (isset($exp['name'])) {
				$select->where('c.name LIKE \'%'.addslashes($exp['name']).'%\'');
			}
			if (isset($exp['email'])) {
				$select->where('c.email LIKE \'%'.addslashes($exp['email']).'%\'');
			}
			
			if (isset($exp['address'])) {
				$select->where('c.address LIKE \'%'.addslashes($exp['address']).'%\'');
			}
			
		}
		$select->order('c.client_id DESC')
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
				->from(array('c' => $this->_prefix.'ad_client'), array('num_clients' => 'COUNT(*)'));
		if ($exp) {
			if (isset($exp['name'])) {
				$select->where('c.name LIKE \'%'.addslashes($exp['name']).'%\'');
			}
			if (isset($exp['email'])) {
				$select->where('c.email LIKE \'%'.addslashes($exp['email']).'%\'');
			}
			if (isset($exp['address'])) {
				$select->where('c.address LIKE \'%'.addslashes($exp['address']).'%\'');
			}
		}
		$row = $select->query()->fetch();
		return $row->num_clients;
	}
}