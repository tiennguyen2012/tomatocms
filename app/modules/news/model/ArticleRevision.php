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
 * @version 	$Id: ArticleRevision.php 1948 2010-04-02 03:58:39Z huuphuoc $
 * @since		2.0.4
 */

class Tomato_Modules_News_Model_ArticleRevision extends Tomato_Core_Model_Entity 
{
	protected $_properties = array(
		'revision_id' => null,
		'article_id' => null,
		'title' => null,
		'sub_title' => null,
		'slug' => null,
		'description' => null,		
		'content' => null,
		'icons' => null,
		'created_date' => null,
		'created_user_id' => null,
		'created_user_name' => null,
		'author' => null,
	);
}
