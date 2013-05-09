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
 * @version 	$Id: FileCounter.php 1525 2010-03-09 19:16:09Z huuphuoc $
 */

class Multimedia_View_Helper_FileCounter
{
	/**
	 * Count number of files in given set
	 * 
	 * @param int $setId Id of set
	 * @return int
	 */
	public function fileCounter($setId) 
	{
		$conn = Tomato_Core_Db_Connection::getSlaveConnection();		
		$select = $conn->select()
						->from(array('s' => Tomato_Core_Db_Connection::getDbPrefix().'multimedia_file_set_assoc'), array('num_files' => 'COUNT(*)'))
						->where('s.set_id = ?', $setId)
						->limit(1);
		return $select->query()->fetch()->num_files;
	}
}
