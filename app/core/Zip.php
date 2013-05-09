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
 * @version 	$Id: Zip.php 1871 2010-03-31 02:45:30Z huuphuoc $
 * @since		2.0.4
 */

class Tomato_Core_Zip 
{
	/**
	 * @param string $file
	 * @param string $tool
	 * @return Tomato_Core_Zip_Abstract
	 */
	static public function factory($file, $adapter = null)
	{
		if (null == $adapter) {
			// Auto detect
			if (class_exists('ZipArchive')) {
				$adapter = 'ZipArchive';
			} else {
				$adapter = 'PclZip';
			}
		}
		$className = 'Tomato_Core_Zip_Abstract_'.$adapter;
		$object = new $className($file);
		if (!$object instanceof Tomato_Core_Zip_Abstract) {
			throw new Exception($className.' is not instance of Tomato_Core_Zip_Abstract');
		}
		return $object;
	}
}
