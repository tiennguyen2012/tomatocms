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
 * @version 	$Id: IndexController.php 958 2010-01-23 03:17:30Z huuphuoc $
 */

class IndexController extends Zend_Controller_Action 
{
	/**
	 * Default action which will be dispatched when user browse to /
	 */
	public function indexAction() 
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		
		// Add meta keyword tag
		$config = Tomato_Core_Config::getConfig();
		if ($keyword = $config->web->meta_keyword) {
			$keyword = strip_tags($keyword);
			$this->view->headMeta()->setName('keyword', $keyword);
		}
		
		// Add meta description tag
		if ($description = $config->web->meta_description) {
			$description = strip_tags($description);
			$this->view->headMeta()->setName('description', $description);
		}
		$this->view->headTitle($config->web->default_title);
	}
}