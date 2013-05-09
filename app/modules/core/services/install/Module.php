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
 * @version 	$Id: Module.php 1800 2010-03-26 08:23:34Z huuphuoc $
 */

class Tomato_Modules_Core_Services_Install_Module
{
	/**
	 * @var Tomato_Modules_Core_Model_ModuleGateway
	 */
	private $_moduleGateway;
	
	/**
	 * Set module gateway
	 * 
	 * @param Tomato_Modules_Core_Model_ModuleGateway $gateway
	 */
	public function setModuleGateway($gateway) 
	{
		$this->_moduleGateway = $gateway;
	}
	
	/**
	 * Install a module
	 * 
	 * @param string $module Name of module
	 * @return Tomato_Modules_Core_Model_Module
	 */
	public function install($module) 
	{
		$file = TOMATO_APP_DIR.DS.'modules'.DS.$module.DS.'config'.DS.'about.xml';
		if (!file_exists($file)) {
			return null;
		}
		
		$xml = simplexml_load_file($file);
		$moduleObj = new Tomato_Modules_Core_Model_Module(array(
			'name' => strtolower($xml->name),
			'description' => $xml->description,
			'thumbnail' => $xml->thumbnail,
			'author' => $xml->author,
			'email' => $xml->email,
			'version' => $xml->version,
			'license' => $xml->license,
		));		
		
		// Execute install scripts
		$queries = $xml->install->query;
		$ok = true;
		if ($queries) {
			$conn = $this->_moduleGateway->getDbConnection();
			foreach ($queries as $query) {
				try {
					$conn->beginTransaction();
					$query = str_replace('###', Tomato_Core_Db_Connection::getDbPrefix(), (string)$query);
					$conn->query($query);
					$conn->commit();
				} catch (Exception $ex) {
					$conn->rollBack();
					$ok = false;
					break;
				}
			}
		}
		
		return $moduleObj;
	}
	
	/**
	 * Uninstall a module
	 * 
	 * @param string $module Name of module
	 */
	public function uninstall($module) 
	{
		$ret = $this->_moduleGateway->delete($module);
		
		$file = TOMATO_APP_DIR.DS.'modules'.DS.$module.DS.'config'.DS.'about.xml';
		if (!file_exists($file)) {
			return 0;
		}
		$xml = simplexml_load_file($file);
		
		// Execute uninstall scripts
		$queries = $xml->uninstall->query;
		if ($queries) {
			$conn = $this->_moduleGateway->getDbConnection();
			foreach ($queries as $query) {
				try {
					$conn->beginTransaction();
					$query = str_replace('###', Tomato_Core_Db_Connection::getDbPrefix(), (string)$query);
					$conn->query($query);
					$conn->commit();
				} catch (Exception $ex) {
					$conn->rollBack();
					break;
				}
			}
		}
		return $ret;
	}
}
