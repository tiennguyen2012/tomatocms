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
 * @version 	$Id: Note.php 1832 2010-03-30 09:52:47Z huuphuoc $
 * @since		2.0.4
 */

class Tomato_Modules_Multimedia_Model_Note extends Tomato_Core_Model_Entity 
{
	protected $_properties = array(
		'note_id' => null,
		'file_id' => null,
		'top' => null,
		'left' => null,
		'width' => null,
		'height' => null,
		'content' => null,
		'is_active' => 0,
		'user_id' => null,
		'user_name' => null,
		'created_date' => null,
	);
}
