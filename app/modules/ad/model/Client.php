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
 * @version 	$Id: Client.php 959 2010-01-23 03:27:13Z huuphuoc $
 */

class Tomato_Modules_Ad_Model_Client extends Tomato_Core_Model_Entity 
{
	protected $_properties = array(
		'client_id' => null,
		'name' => null,
		'email' => null,
		'telephone' => null,
		'address' => null,
		'created_date' => null,
	);
}