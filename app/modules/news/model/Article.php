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
 * @version 	$Id: Article.php 1524 2010-03-09 10:08:43Z huuphuoc $
 */

class Tomato_Modules_News_Model_Article extends Tomato_Core_Model_Entity 
{
	protected $_properties = array(
		'article_id' => null,
		'title' => null,
		'sub_title' => null,
		'slug' => null,
		'description' => null,		
		'content' => null,
		'icons' => null,
		'image_square' => null,
		'image_general' => null,
		'image_small' => null,
		'image_crop' => null,
		'image_medium' => null,
		'image_large' => null,
		'status' => null,
		'num_views' => 0,
		'created_date' => null,
		'created_user_id' => null,
		'created_user_name' => null,
	
		'updated_date' => null,
		'updated_user_id' => null,
		'updated_user_name' => null,
	
		'activate_date' => null,
		'activate_user_id' => null,
		'activate_user_name' => null,

		'author' => null,
		'allow_comment' => 0,
		'sticky' => 0,
	);
}
