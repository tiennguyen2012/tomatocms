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
 * @version 	$Id: Installer.php 1660 2010-03-19 10:15:55Z huuphuoc $
 * @since 		2.0.1
 */

class Tomato_Installer extends Zend_Application_Bootstrap_Bootstrap 
{
	protected function _initAutoload()
	{
		require_once TOMATO_APP_DIR.'/core/Autoloader.php';
		$autoloader = Zend_Loader_Autoloader::getInstance();
		$autoloader->unshiftAutoloader(new Tomato_Core_Autoloader(), 'Tomato');
		return $autoloader;
	}

	protected function _initInstallChecker()
	{
		/**
		 * Check whether the app is installed or not
		 */
		$config = Tomato_Core_Config::getConfig();
		if ($config->install && $config->install->date) {
			$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
			if (null === $viewRenderer->view) {
				$viewRenderer->initView();
			}
			$view = $viewRenderer->view;
			//die($view->translator('install_already_installed', 'core'));
			
			// Redirect to the homepage
			header('Location: '.$config->web->url);
		}
	}
	
	protected function _initRoutes()
	{
		$this->bootstrap('FrontController');
        $front = $this->getResource('FrontController');
        
		/** 
		 * Load routes
		 */
		$router = $front->getRouter();
		
		// Don't use default route
		$router->removeDefaultRoutes();
		// Add installer routes
		$router->addRoute(
			'core_install',
    		new Zend_Controller_Router_Route('/',
				array(
					'module' => 'core', 
					'controller' => 'Install', 
					'action' => 'step1',
				))
		);
		$router->addRoute(
			'core_install_step2',
    		new Zend_Controller_Router_Route('/step2/',
				array(
					'module' => 'core', 
					'controller' => 'Install', 
					'action' => 'step2',
				))
		);
		$router->addRoute(
			'core_install_step3',
    		new Zend_Controller_Router_Route('/step3/',
				array(
					'module' => 'core', 
					'controller' => 'Install', 
					'action' => 'step3',
				))
		);
	}

	protected function _initView()
	{
		/** 
		 * Init view 
		 */
		$config = Tomato_Core_Config::getConfig();
		date_default_timezone_set($config->datetime->timezone);
		
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
		if (null === $viewRenderer->view) {
			$viewRenderer->initView();
		}
		$view = $viewRenderer->view;
		$view->doctype('XHTML1_STRICT');
		$view->assign('APP_SKIN', 'default');
		$view->assign('APP_TEMPLATE', 'default');
		$view->assign('SITE_NAME', $config->web->site_name);
		
		$view->addHelperPath(TOMATO_APP_DIR.DS.'core'.DS.'view'.DS.'helper', 'Tomato_Core_View_Helper');
		$view->addHelperPath(TOMATO_APP_DIR.DS.'modules'.DS.'core'.DS.'views'.DS.'helpers', 'Core_View_Helper');
		
		/** 
		 * Build root URL 
		 */
		$request = new Zend_Controller_Request_Http();
		$siteUrl = $request->getScheme().'://'.$request->getHttpHost();
		$basePath = $request->getBasePath();
		$siteUrl = ($basePath == '') ? $siteUrl : $siteUrl.'/'.ltrim($basePath, '/');
		$view->assign('APP_URL', $siteUrl);
		$view->assign('APP_STATIC_SERVER', $siteUrl);
		
		$view->getHelper('BaseUrl')->setBaseUrl(rtrim($siteUrl, '/').'/install.php');

		/** 
		 * Set layout path and default layout
		 */
		Zend_Layout::startMvc(array('layoutPath' => TOMATO_APP_DIR.DS.'templates'.DS.'admin'.DS.'layouts'));
		Zend_Layout::getMvcInstance()->setLayout('install');
		
		/** 
		 * Cache language if user used caching system
		 */
		$cache = Tomato_Core_Cache::getInstance();
		if ($cache) {
			 Zend_Translate::setCache($cache);
		}
	}
	
	protected function _initPlugins()
	{
		$this->bootstrap('FrontController');
        $front = $this->getResource('FrontController');
        
        /** 
		 * Error handler 
		 */
		$front->registerPlugin(new Zend_Controller_Plugin_ErrorHandler(array(
								    'module'     => 'core',
								    'controller' => 'message',
								    'action'     => 'error',
								)));
	}
}
