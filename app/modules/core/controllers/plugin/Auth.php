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
 * @version 	$Id: Auth.php 1660 2010-03-19 10:15:55Z huuphuoc $
 */

/**
 * Base on the request URL and role/permisson of current user, forward the user
 * to the login page if the user have not logged in 
 */
class Tomato_Modules_Core_Controllers_Plugin_Auth extends Zend_Controller_Plugin_Abstract 
{
	public function preDispatch(Zend_Controller_Request_Abstract $request) 
	{
		$uri = $request->getRequestUri();
		$uri = strtolower($uri);

		$uri = rtrim($uri, '/').'/';
		if (strpos($uri, '/admin/') === false) {
			return;
		}
		
		/**
		 * Switch to admin template
		 * @since 2.0.4
		 */
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
		if (null === $viewRenderer->view) {
			$viewRenderer->initView();
		}
		$view = $viewRenderer->view;
		$view->assign('APP_TEMPLATE', 'admin');
		Zend_Layout::startMvc(array('layoutPath' => TOMATO_APP_DIR.DS.'templates'.DS.'admin'.DS.'layouts'));
		Zend_Layout::getMvcInstance()->setLayout('admin');
		
		$isAllowed = false;
		if (Zend_Auth::getInstance()->hasIdentity()) {
			$user = Zend_Auth::getInstance()->getIdentity();
			$module = $request->getModuleName();
			$controller = $request->getControllerName();
			$action = $request->getActionName();
			
			// Fix #0000144
			// Add 'core:message' resource that allows show the friendly error message
			$acl = Tomato_Modules_Core_Services_Acl::getInstance();
			if (!$acl->has('core:message')) {
				$acl->addResource('core:message');
			}
			
			$isAllowed = $acl->isUserOrRoleAllowed($user->role_id, $user->user_id, $module, $controller, $action);
		}
		if (!$isAllowed) {
			$forwardAction = Zend_Auth::getInstance()->hasIdentity() ? 'deny' : 'login';
			
			/**
			 * DON'T use redirect! as folow:
			 * $this->getResponse()->setRedirect('/Login/');
			 */
			$request->setModuleName('core')
					->setControllerName('Auth')
					->setActionName($forwardAction)
					->setDispatched(true);
		}
	}
}
