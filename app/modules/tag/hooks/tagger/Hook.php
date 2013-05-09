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
 * @version 	$Id: Hook.php 1518 2010-03-09 09:48:35Z huuphuoc $
 * @since		2.0.2
 */

class Tomato_Modules_Tag_Hooks_Tagger_Hook 
{
	public static function show($itemId = null, $itemName, $routeName, $detailsRouteName)
	{
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
		$view = $viewRenderer->view;
		
		if ($itemId) {
			$conn = Tomato_Core_Db_Connection::getMasterConnection();
			$select = $conn->select()
							->from(array('t' => Tomato_Core_Db_Connection::getDbPrefix().'tag'), array('tag_id', 'tag_text'))
							->joinInner(array('ti' => Tomato_Core_Db_Connection::getDbPrefix().'tag_item_assoc'), 't.tag_id = ti.tag_id', array('details_route_name'))
							->where('ti.item_id = ?', $itemId)
							->where('ti.item_name = ?', $itemName)
							->where('ti.route_name = ?', $routeName)
							->where('ti.details_route_name = ?', $detailsRouteName)
							->group('t.tag_id');
			$tagGateway = new Tomato_Modules_Tag_Model_TagGateway();
			$tagGateway->setDbConnection($conn);
			$rs = $select->query()->fetchAll();
			$tags = new Tomato_Core_Model_RecordSet($rs, $tagGateway);
			$view->assign('tags', $tags);
		}
		
		$view->assign('tagItemName', $itemName);
		$view->assign('tagItemRouteName', $routeName);
		$view->assign('tagDetailsRouteName', $detailsRouteName);
		$view->addScriptPath(TOMATO_APP_DIR.DS.'modules'.DS.'tag'.DS.'views'.DS.'scripts');
		echo $view->render('partial/_tagger.phtml');
	}
	
	public static function add($itemId)
	{
		$request = Zend_Controller_Front::getInstance()->getRequest();
		$itemName = $request->getParam('tagItemName');
		$itemRouteName = $request->getParam('tagItemRouteName');
		$tagDetailsRouteName = $request->getParam('tagDetailsRouteName');
		$tagIds = $request->getParam('tagIds');
		
		if ($tagIds) {
			$conn = Tomato_Core_Db_Connection::getMasterConnection();
			$conn->delete(Tomato_Core_Db_Connection::getDbPrefix().'tag_item_assoc', array(
				'item_id = '.$conn->quote($itemId),
				'item_name = '.$conn->quote($itemName),
				'route_name = '.$conn->quote($itemRouteName),
				'details_route_name = '.$conn->quote($tagDetailsRouteName),
			));
			
			foreach ($tagIds as $tagId) {
				$conn->insert(Tomato_Core_Db_Connection::getDbPrefix().'tag_item_assoc', array(
					'tag_id' => $tagId,
					'item_id' => $itemId,
					'item_name' => $itemName,
					'route_name' => $itemRouteName,
					'details_route_name' => $tagDetailsRouteName,
					'params' => $itemName.':'.$itemId,
				));
			}
		}
	}
}
