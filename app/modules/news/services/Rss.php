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
 * @version 	$Id: Rss.php 1524 2010-03-09 10:08:43Z huuphuoc $
 */

class Tomato_Modules_News_Services_Rss 
{
	// 60 minutes
	const CACHE_TIME = 3600;
	const LIMIT = 20;

	public static function feed($categoryId = null) 
	{
		$config = Tomato_Core_Config::getConfig();
		$baseUrl = $config->web->url;
		
		$prefix = TOMATO_TEMP_DIR.DS.'cache'.DS.'news_rss_';
		$file = (null == $categoryId)
				? $prefix.'latest.xml' 
				: $prefix.'category_'.$categoryId.'.xml';

		if (Tomato_Core_Cache_File::isFileNewerThan($file, time() - self::CACHE_TIME)) {
			$output = file_get_contents($file);
			return $output;
		}
		
		/** 
		 * Get the latest articles
		 */
		$conn = Tomato_Core_Db_Connection::getSlaveConnection();
		$category = null;
		if ($categoryId) {
			$categoryGateway = new Tomato_Modules_Category_Model_CategoryGateway();
			$categoryGateway->setDbConnection($conn);
			$category = $categoryGateway->getCategoryById($categoryId);
		}
		
		$articleGateway = new Tomato_Modules_News_Model_ArticleGateway();
		$select = $conn->select()
						->from(array('a' => Tomato_Core_Db_Connection::getDbPrefix().'news_article'), array('article_id', 'category_id', 'title', 'slug', 'description', 'image_general', 'activate_date'));
		if ($categoryId) {
			$select->where('a.category_id = ?', $categoryId);
		}
		$select->where('a.status = ?', 'active')
				->order('a.activate_date DESC')
				->limit(self::LIMIT);
		$rs = $select->query()->fetchAll();
		$articles = new Tomato_Core_Model_RecordSet($rs, $articleGateway);
		
		$newsConfig = Tomato_Core_Module_Config::getConfig('news');			

		/**
		 * Create RSS items ...
		 */
		$router = Zend_Controller_Front::getInstance()->getRouter();
		$entries = array();
		if ($articles != null && count($articles) > 0) {
			foreach ($articles as $article) {
				$link = $router->assemble($article->getProperties(), 'news_article_details');		
						
				$description = $article->description;
				$image = $article->image_general;
				$description = (null == $image || '' == $image) 
							? $description
							: '<a href="'.$baseUrl.$link.'" title="'.addslashes($article->title).'"><img src="'.$image.'" alt="'.addslashes($article->title).'" /></a>' . $description;
				
				$entry = array(
						'title' => $article->title,
						'guid' => $baseUrl.$link, 
						'link' => $baseUrl.$link,
						'description' => $description,
						'lastUpdate' => strtotime($article->activate_date),
					);
				$entries[] = $entry;
			}
		} 
		
		/**
		 * ... and write to file
		 */
		$link = (null == $category)
				? $baseUrl
				: $baseUrl.$router->assemble($category->getProperties(), 'news_article_category');

		$generator = ($newsConfig->rss->channel_generator != '') 
						? $newsConfig->rss->channel_generator 
						: 'TomatoCMS version '.Tomato_Core_Version::getVersion(); 
		$buildDate = strtotime(date('D, d M Y h:i:s'));
		$data = array(
			'title' => $newsConfig->rss->channel_title,
			'link' => $link,
			'description' => $newsConfig->rss->channel_description,
			'copyright' => $newsConfig->rss->channel_copyright,
			'generator' => $generator,
			'lastUpdate' => $buildDate,
			'published' => $buildDate,
			'charset' => 'UTF-8',
			'entries' => $entries,
		);
		$feed = Zend_Feed::importArray($data, 'rss');
		$rssFeed = $feed->saveXML();
		
		$fh = fopen($file, 'w');
		fwrite($fh, $rssFeed);
		fclose($fh);
		
		return $rssFeed;
	}
}
