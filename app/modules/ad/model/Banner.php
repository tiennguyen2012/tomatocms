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
 * @version 	$Id: Banner.php 959 2010-01-23 03:27:13Z huuphuoc $
 */

class Tomato_Modules_Ad_Model_Banner extends Tomato_Core_Model_Entity 
{
	protected $_properties = array(
		'banner_id' => null,
		'name' => null,
		'text' => null,
		'num_clicks' => null,
		'created_date' => null,
		'start_date' => null,
		'expired_date' => null,
		'publish_up' => null,
		'publish_down' => null,
		'client_id' => null,
		'code' => null,
		'click_url' => null,
		'target' => null, 
		'format' => null,
		'image_url' => null,
		'ordering' => null,
		'mode' => null,
		'purpose_id' => null,
		'timeout' => null,
		'status' => null,
		'more_info' => null,
	);
}