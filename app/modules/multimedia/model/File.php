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
 * @version 	$Id: File.php 1525 2010-03-09 19:16:09Z huuphuoc $
 */

class Tomato_Modules_Multimedia_Model_File extends Tomato_Core_Model_Entity 
{
	protected $_properties = array(
		'file_id' => null,
		'category_id' => null,
		'title' => null,
		'slug' => null,
		'description' => null,
		'content' => null,
		'image_general' => null,
		'image_medium' => null,
		'image_thumbnail' => null,
		'image_crop' => null,
		'image_small' => null,
		'image_square' => null,
		'image_large' => null,
		'num_views' => null,
		'created_date' => null,
		'created_user' => null,
		'created_user_name' => null,
		'allow_comment' => null,
		'ordering' => null,
		'num_comments' => null,
		'url' => null,
		'html_code' => null,
		'is_active' => null,
		'file_type' => null,
	);
}
