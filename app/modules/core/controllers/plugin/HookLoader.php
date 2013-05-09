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
 * @version 	$Id: HookLoader.php 1306 2010-02-24 08:39:21Z huuphuoc $
 */

class Tomato_Modules_Core_Controllers_Plugin_HookLoader extends Zend_Controller_Plugin_Abstract 
{
	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{
		$conn = Tomato_Core_Db_Connection::getSlaveConnection();
		$select = $conn->select()
						->from(array('t' => Tomato_Core_Db_Connection::getDbPrefix().'core_target'), array('target_name', 'hook_module', 'hook_name', 'hook_type'))
						->order('t.target_id DESC');
		$rs = $select->query()->fetchAll();
		if ($rs) {
			foreach ($rs as $row) {
				$hookClass = (null == $row->hook_module || '' == $row->hook_module)
						? 'Tomato_Hooks_'.$row->hook_name.'_Hook'
						: 'Tomato_Modules_'.$row->hook_module.'_Hooks_'.$row->hook_name.'_Hook';
				$hook = new $hookClass();
				if ($hook instanceof Tomato_Core_Hook) {
					Tomato_Core_Hook_Registry::getInstance()->register($row->target_name, array($hook, $row->hook_type));
				}
			}
		}
	}
}
