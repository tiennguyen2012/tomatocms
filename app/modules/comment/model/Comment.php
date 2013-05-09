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
 * @version 	$Id: Comment.php 961 2010-01-23 03:35:19Z huuphuoc $
 */

class Tomato_Modules_Comment_Model_Comment extends Tomato_Core_Model_Entity 
{
	protected $_properties = array(
		'comment_id' => null,
		'title' => null,
		'content' => null,
		'full_name' => 0,
		'web_site' => null,
		'email' => null,
		'user_id' => null,
		'user_name' => null,
		'ip' => null,
		'created_date' => null,
		'is_active' => 0,
		'reply_to' => null,
		'depth'	=> 0,
		'path' => null,
		'ordering'	=> 0,
		'page_url' => null,
	);
}