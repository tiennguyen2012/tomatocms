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
 * @version 	$Id: App.php 1568 2010-03-11 02:51:37Z huuphuoc $
 */

class Tomato_App extends Zend_Application_Bootstrap_Bootstrap
{
	protected function _initAutoload()
	{
		require_once TOMATO_APP_DIR.'/core/Autoloader.php';
		$autoloader = Zend_Loader_Autoloader::getInstance();
		$autoloader->unshiftAutoloader(new Tomato_Core_Autoloader(), 'Tomato');
		return $autoloader;
	}

	/**
	 * Redirect to the install page if user have not installed yet
	 * @since 2.0.3
	 */
	protected function _initInstallChecker()
	{
		$config = Tomato_Core_Config::getConfig();
		if (null == $config->install || null == $config->install->date) {
			header('Location: install.php');
			exit;
		}
	}
	
	protected function _initRoutes()
	{
		$this->bootstrap('FrontController');
        $front = $this->getResource('FrontController');
        
		$routes = Tomato_Core_Module_Loader::getInstance()->getRoutes();
		$front->setRouter($routes);
		// Don't use default route
		$front->getRouter()->removeDefaultRoutes();
		
		/**
		 * Zend Framework 1.10.0 requires route which matchs with "/"
		 * @since 2.0.3
		 */
		$front->getRouter()->addRoute(
			'index',
    		new Zend_Controller_Router_Route('/',
				array(
					'module' => 'default',
					'controller' => 'Index', 
					'action' => 'index',
				))
		);
		
		// Add routes for static pages
		$config = TOMATO_APP_DIR.DS.'config'.DS.'layout.ini';
		if (file_exists($config)) {
			$config = new Zend_Config_Ini($config, 'layouts');
			$config = $config->toArray();
			$config = $config['layouts'];
			foreach ($config as $key => $value) {
				if ('static' == $value['type']) {
					$front->getRouter()->addRoute(
						'index_static_'.$key,
			    		new Zend_Controller_Router_Route($value['url'],
							array(
								'module' => 'default',
								'controller' => 'Index', 
								'action' => 'index',
							))
					);
				}
			}
		}
	}
	
	/**
	 * TODO: Use Zend_Application_Resource_Session
	 */
	protected function _initSession()
	{
		/** 
		 * Registry session handler 
		 */
		Zend_Session::setSaveHandler(Tomato_Modules_Core_Services_SessionHandler::getInstance());
		if (isset($_GET['PHPSESSID'])) {
			session_id($_GET['PHPSESSID']);
		} else if (isset($_POST['PHPSESSID'])) {
			session_id($_POST['PHPSESSID']);
		}
	}
	
	protected function _initPlugins()
	{
		$this->bootstrap('FrontController');
        $front = $this->getResource('FrontController');
        
		/** 
		 * Registry plugins
		 * The alternative way is that put plugin to app.ini:
		 * resources.frontController.plugins.pluginName = "Plugin_Class"
		 */
		$front->registerPlugin(new Tomato_Core_Controller_Plugin_Init())
				->registerPlugin(new Tomato_Core_Controller_Plugin_Admin())
		 		->registerPlugin(new Tomato_Core_Controller_Plugin_Template())
		 		->registerPlugin(new Tomato_Modules_Core_Controllers_Plugin_HookLoader())
		 		->registerPlugin(new Tomato_Modules_Core_Controllers_Plugin_Auth());	
		 		
		// Error handler
		$front->registerPlugin(new Zend_Controller_Plugin_ErrorHandler(array(
								    'module'     => 'core',
								    'controller' => 'message',
								    'action'     => 'error',
								)));
		
		$conn = Tomato_Core_Db_Connection::getSlaveConnection();
		$pluginGateway = new Tomato_Modules_Core_Model_PluginGateway();
		$pluginGateway->setDbConnection($conn);
		$plugins = $pluginGateway->getOrderedPlugins();
		foreach ($plugins as $plugin) {
			$pluginClass = 'Tomato_Plugins_'.$plugin->name.'_Plugin';
			if (class_exists($pluginClass)) {
				$pluginInstance = new $pluginClass();
				if ($pluginInstance instanceof Tomato_Core_Controller_Plugin) {
					$front->registerPlugin($pluginInstance);
				}
			} else {
				//throw new Tomato_Core_Plugin_Exception('Plugin '.$plugin->name.' not found');
			}
		}
	}
}
