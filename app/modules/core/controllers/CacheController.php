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
 * @version 	$Id: CacheController.php 1306 2010-02-24 08:39:21Z huuphuoc $
 */

class Core_CacheController extends Zend_Controller_Action 
{
	/* ========== Frontend actions ========================================== */
	
	public function htmlAction() 
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		if (Zend_Layout::getMvcInstance() != null) {
			$this->_helper->getHelper('layout')->disableLayout();	
		}
		
		$type = $this->_request->getParam('__cacheType');
		$key = $this->_request->getParam('__cacheKey');

		$content = Tomato_Core_Cache_File::fromCache($type, $key);
		$this->_response->setBody($content);
	}
	
	/* ========== Backend actions =========================================== */
	
	public function listAction() 
	{
		$cache = Tomato_Core_Cache::getInstance();
		$this->view->assign('cache', $cache);
		if (!$cache) {
			return;
		}
		
		$backend = $cache->getBackend();
		$supportListIds = false;
		$supportTags = false;
		if ($backend instanceof Zend_Cache_Backend_ExtendedInterface) {
			$capabilities = $backend->getCapabilities();
			$supportListIds = $capabilities['get_list'];
			$supportTags = $capabilities['tags'];
		}
		$this->view->assign('supportListIds', $supportListIds);
		$this->view->assign('supportTags', $supportTags);
		
		$fillingPercentage = !($cache instanceof Zend_Cache_Backend_ExtendedInterface)
						? null
						: $cache->getFillingPercentage();
		$this->view->assign('fillingPercentage', $fillingPercentage);

		$this->view->assign('backend', get_class($backend));
		
		if ($supportListIds && $supportTags) {
			$tags = $cache ? $cache->getTags() : null;
			$this->view->assign('tags', $tags);
			$cacheIds = array();
			if ($tags) {
				foreach ($tags as $tag) {
					$cacheIds[$tag] = $cache->getIdsMatchingTags(array($tag));
				}
			}
			$this->view->assign('cacheIds', $cacheIds);
		}
	}
	
	public function deleteAction() 
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		Zend_Layout::getMvcInstance()->disableLayout();
		
		if ($this->_request->isPost()) {
			$type = $this->_request->getPost('type');
			$id = $this->_request->getPost('id');
			$cache = Tomato_Core_Cache::getInstance();
			if ($cache) {
				switch ($type) {
					case 'id':
						$cache->remove($id);
						break;
					case 'tag':
						$cache->clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array($id));
						break;
				}
			}
			$this->_response->setBody('RESULT_OK');
		}
	}
	
	public function clearAction() 
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		Zend_Layout::getMvcInstance()->disableLayout();
		
		if ($this->_request->isPost()) {
			$cache = Tomato_Core_Cache::getInstance();
			if ($cache) {
				$cache->clean();
			}
			$this->_response->setBody('RESULT_OK');
		}
	}
}
