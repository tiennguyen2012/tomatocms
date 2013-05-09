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
 * @version 	$Id: MessageController.php 1592 2010-03-11 08:43:31Z huuphuoc $
 * @since		2.0.3
 */

class Core_MessageController extends Zend_Controller_Action 
{
	public function init()
	{
		Zend_Layout::getMvcInstance()->setLayout('message');
	}
	
	/* ========== Frontend actions ========================================== */

	/**
	 * If you want to throw data not found exception, add the following code to your controller action:
	 * <code>
	 * 	if (null == $data) {
	 * 		throw new Tomato_Core_Exception_NotFound();
	 * 	}
	 * </code>
	 */
	public function errorAction()
	{
		$error = $this->_request->getParam('error_handler');
		$this->view->assign('error', $error);
		
		$config = Tomato_Core_Config::getConfig();
		$debug = (isset($config->web->debug) && 'true' == $config->web->debug) ? true : false;
		$this->view->assign('debug', $debug);
		
		$class = get_class($error->exception);
		$content = '';
		switch ($class) {
			case 'Tomato_Core_Exception_NotFound':
				$this->_response->setRawHeader('HTTP/1.1 404 Not Found');
				$file = 'message/_errorNotFound.phtml';
			default:
				$file = 'message/_error.phtml';
				break;
		}
		$this->view->assign('details', $this->view->render($file));
	}
	
	/**
	 * Show offline message
	 */
	public function offlineAction() 
	{
		$config = Tomato_Core_Config::getConfig();
		$config = $config->toArray();
		$message = isset($config['web']['offline_message']) ? $config['web']['offline_message'] : null;
		$this->view->assign('message', $message);
	}
}