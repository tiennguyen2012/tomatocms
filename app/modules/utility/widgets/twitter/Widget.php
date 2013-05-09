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
 * @version 	$Id: Widget.php 1270 2010-02-23 04:45:45Z huuphuoc $
 */

class Tomato_Modules_Utility_Widgets_Twitter_Widget extends Tomato_Core_Widget 
{
	protected function _prepareShow() 
	{
		$account = $this->_request->getParam('account');
    	$limit = $this->_request->getParam('limit', 5);
    	$dateFormat = array(
			'DAY' 			=> $this->_view->translator()->widget('diff_day_format'),
			'DAY_HOUR'		=> $this->_view->translator()->widget('diff_day_hour_format'),
			'HOUR' 			=> $this->_view->translator()->widget('diff_hour_format'),
			'HOUR_MINUTE' 	=> $this->_view->translator()->widget('diff_hour_minute_format'),
			'MINUTE' 		=> $this->_view->translator()->widget('diff_minute_format'),
			'MINUTE_SECOND'	=> $this->_view->translator()->widget('diff_minute_second_format'),
			'SECOND'		=> $this->_view->translator()->widget('diff_second_format'),
		);
    	
    	$url = 'http://twitter.com/statuses/user_timeline/'.$account.'.json';
    	$updates = Tomato_Core_Utility_HttpRequest::getResponse($url);
		$updates = Zend_Json::decode($updates);
    	
    	$limit = min(array($limit, count($updates)));
    	
    	$this->_view->assign('account', $account);
    	$this->_view->assign('limit', $limit);
    	$this->_view->assign('updates', $updates);
    	$this->_view->assign('dateFormat', $dateFormat);
	}
}
