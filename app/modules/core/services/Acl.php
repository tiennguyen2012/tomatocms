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
 * @version 	$Id: Acl.php 1421 2010-03-02 09:14:53Z huuphuoc $
 */

/**
 * Idea and most implementions here was taken from
 * http://www.phpclasses.org/browse/package/4100.html
 */
class Tomato_Modules_Core_Services_Acl extends Zend_Acl 
{
	/**
	 * @var Tomato_Modules_Core_Services_Acl
	 */
	private static $_instance = null;

	/**
	 * @return Tomato_Modules_Core_Services_Acl
	 */
	public static function getInstance() 
	{
		if (null == self::$_instance) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	private function __construct() 
	{
//		ini_set('max_execution_time', 120);
		
		$this->_buildResources();
		$this->_buildRoles();
		$this->_buildRules();
	}
	
	/**
	 * Create the resources
	 */
	private function _buildResources() 
	{
		$conn = Tomato_Core_Db_Connection::getSlaveConnection();
		$select = $conn->select()
						->from(array('re' => Tomato_Core_Db_Connection::getDbPrefix().'core_resource'), array('resource_id', 'parent_id', 'name' => 'CONCAT(module_name, ":", controller_name)'));
		$rs = $select->query()->fetchAll();
		if (null == $rs) {
	        return;
		}
		$allResources = array();
        // Map resource id to its name
        $map = array();
        foreach ($rs as $row) {
        	$allResources[] = $row->resource_id;
        	$map[$row->resource_id] = $row->name;
		}
        foreach ($rs as $row) {
            if ($row->parent_id !== null && !in_array($row->parent_id, $allResources)) {
                throw new Zend_Acl_Exception('Resource id "'.$row->parent_id.'" does not exist');
            }
		}
		
		$numResources = count($rs);
        $i = 0;
        while ($numResources > $i) {
            foreach ($rs as $row) {
                // Check if parent resource (if any) exists
                // Only add if this resource hasn't yet been added and its parent is known, if any
                $resId = $row->name;
                
                $has = false;
                if ($row->parent_id != null) {
					$parentName = isset($map[$row->parent_id])
								? $map[$row->parent_id]
								: null;
					if (null == $parentName) {
						$has = false;
					} else {
//						$parentResId = $this->_formatResource($parentName);
						$has = $this->has($parentName);
					}
                }
                
                if (!$this->has($resId)) {
                	if ($has) {
                    	$this->addResource(new Zend_Acl_Resource($resId), $parentResId);
                	} else {
                		$this->addResource(new Zend_Acl_Resource($resId));
                	}
                    $i++;
                }
			}
		}
	}
	
	/**
	 * Create roles
	 */
	private function _buildRoles() 
	{
		$conn = Tomato_Core_Db_Connection::getSlaveConnection();
		$select = $conn->select()
            		->from(array('r' => Tomato_Core_Db_Connection::getDbPrefix().'core_role'), array('r.name', 'r.role_id'))
            		->joinLeft(array('i' => Tomato_Core_Db_Connection::getDbPrefix().'core_role_inheritance'), 'r.role_id = i.child_id')
            		->order(array('role_id', 'ordering'));
		$rs = $select->query()->fetchAll();
		if (null == $rs) {
			$rs = array();
		}
		
		// Build map from role Id to role identifier (defined by function _formatRole)
		$map = array();
		foreach ($rs as $row) {
			$map[$row->role_id] = $this->_formatRole($row->role_id);
		}
		
		// Create an array that stores all roles and their parents
        $roles = array();
        foreach ($rs as $role) {
            if (!isset($roles[$this->_formatRole($role->role_id)])) {
                $roles[$this->_formatRole($role->role_id)] = array();
            }
            if (isset($map[$role->parent_id]) && $map[$role->parent_id] != null) {
            	$roles[$this->_formatRole($role->role_id)][] = $map[$role->parent_id];
            }
        }
        
        // Now add to the ACL
        $numRoles = count($roles);
        $i = 0;

        // While there are still elements left to be added
        while ($numRoles > $i) {
            // Check every element in the db
            foreach ($roles as $role => $parentRoles) {
                // Check if a parent is invalid to prevent an infinite loop
                // if the relational DBase works, this shouldn't happen
                foreach ($parentRoles as $childRole) {
                    if (!array_key_exists($childRole, $roles)) {
                        throw new Zend_Acl_Exception('Role id "'.$childRole.'" does not exist');
					}
				}
                if (!$this->hasRole($role) &&            		// If it has not yet been added to the ACL
                    (empty($parentRoles)  ||            		// and no parents exist or
						$this->_hasAllRolesOf($parentRoles) 	// we know them all
                    )) {
                    // We can add to ACL
                    $this->addRole(new Zend_Acl_Role($role), $parentRoles);
                    $i++;
				}
			}
		}
	}
	
	/**
	 * Create rules
	 */
	private function _buildRules() 
	{
		$conn = Tomato_Core_Db_Connection::getSlaveConnection();
		$sql = "SELECT CONCAT(ru.obj_type, '_', ru.obj_id) AS role_name, ru.allow, ru.resource_name AS resource_name_2, NULL AS privilege_name
				FROM ".Tomato_Core_Db_Connection::getDbPrefix()."core_rule AS ru
				WHERE ru.privilege_id IS NULL
				
				UNION 
				
				SELECT CONCAT(ru.obj_type, '_', ru.obj_id) AS role_name, ru.allow, CONCAT(p.module_name, ':', p.controller_name) AS resource_name_2, p.name AS privilege_name 
				FROM ".Tomato_Core_Db_Connection::getDbPrefix()."core_rule AS ru
				INNER JOIN ".Tomato_Core_Db_Connection::getDbPrefix()."core_privilege AS p ON ru.privilege_id = p.privilege_id";
		$rs = $conn->query($sql)->fetchAll();
		if ($rs != null) {
			foreach ($rs as $row) {
				if (!$this->hasRole($row->role_name)) {
					$this->addRole(new Zend_Acl_Role($row->role_name));
				}
				if ($row->allow == true) {
                	$this->allow($row->role_name, $row->resource_name_2, $row->privilege_name);
				} else {
					$this->deny($row->role_name, $row->resource_name_2, $row->privilege_name);
            	}
			}
		}
	}
	
	public function isUserOrRoleAllowed($roleId, $userId, $module, $controller, $action = null) 
	{
		if ($action != null) {
			$action = strtolower($action);
		}
		$resource = strtolower($module.':'.$controller);
		$roleId = $this->_formatRole($roleId);
		$userId = 'user_'.$userId;
		if (($this->hasRole($roleId) && $this->isAllowed($roleId, $resource, $action))
				|| ($this->hasRole($userId) && $this->isAllowed($userId, $resource, $action))) {
			return true;
		} 
		return false;
	}
	
	/**
	 * @param array $searchRoles
	 * @return bool
	 */
	private function _hasAllRolesOf($searchRoles) 
	{
        foreach ($searchRoles as $role) {
            if (!$this->hasRole($role)) {
                return false;
            }
        }
        return true;
	}

	/**
	 * Generate the role name which will be added to ACL based on the original role Id
	 * 
	 * @param string $roleId The role Id
	 * @return string
	 */
	private function _formatRole($roleId) 
	{
		return 'role_'.$roleId;
	}
}
