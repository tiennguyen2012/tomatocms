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
 * @version 	$Id: Widget.php 2033 2010-04-02 07:59:11Z hoangninh $
 */

class Tomato_Modules_Utility_Widgets_YoutubePlayer_Widget extends Tomato_Core_Widget 
{
	protected function _prepareShow() 
	{
		$url = $this->_request->getParam('url');
		$width = $this->_request->getParam('width', 240);
    	$height = $this->_request->getParam('height', 160);
    	$this->_view->assign('width', $width);
    	$this->_view->assign('height', $height);
    	
		try {
			$content = Tomato_Core_Utility_HttpRequest::getResponse($url);
			if (null == $content) {
				return;
			}
			preg_match('/"video_id": \"[\w]{11}\"/', $content, $videoIdTemp);
			
			preg_match('/[\w]{11}/', $videoIdTemp[0], $videoIdTemp2);
			
			if (isset($videoIdTemp2[0]) && $videoIdTemp2[0]) {
				$videoId = $videoIdTemp2[0];
				
				$youtube = new Zend_Gdata_YouTube();
				$entry = $youtube->getVideoEntry($videoId);
				
				// Get clip URL
				$url = null;
			 	foreach ($entry->mediaGroup->content as $content) {
		        	if ($content->type === 'application/x-shockwave-flash') {
		            	$url = $content->url;
		            	break;
		        	}
		    	}
		    	// Get clip thumbnail
		    	$thumbnail = $entry->mediaGroup->thumbnail[0]->url;
		    	$this->_view->assign('url', $url);
		    	$this->_view->assign('thumbnail', $thumbnail);
		    	
		    	// Get clip title
		    	$title = $this->_request->getParam('title', $entry->mediaGroup->title);
		    	$this->_view->assign('title', $title);
			}
		} catch (Exception $ex) {
		}
	}
}
