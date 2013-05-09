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
 * @version 	$Id$
 * @since		2.0.3
 */

class Tomato_Modules_News_Widgets_Rating_Widget extends Tomato_Core_Widget 
{
	protected function _prepareShow() 
	{
		$articleId = $this->_request->getParam('article_id');
		$width = $this->_request->getParam('width_star', 150);
		$numStar = $this->_request->getParam('num_star', 5);
		if ($articleId) {
			$conn = Tomato_Core_Db_Connection::getSlaveConnection();
			$select = $conn->select()
							->from(array('r' => Tomato_Core_Db_Connection::getDbPrefix().'news_article_rate'), array('total' => 'SUM(r.rate)'))
							->where('article_id = ?', $articleId)
							->group('r.article_id')
							->limit(1);
			$row = $select->query()->fetch();
			$total = (null == $row) ? 0 : $row->total;
			$currentRating = ($width > 0 && $numStar > 0) ? ceil($total * $numStar * 100 / $width) : 0;
			
			$this->_view->assign('articleId', $articleId);
			$this->_view->assign('total', $total);
			$this->_view->assign('currentRating', $currentRating);
			$this->_view->assign('numStar', $numStar);
			$this->_view->assign('width', $width);
			$this->_view->assign('unid', uniqid());
		}
	}
	
	protected function _prepareRating() 
	{
		$articleId = $this->_request->getParam('article_id');
		$total = $this->_request->getParam('current_rate');
		$rate = $this->_request->getParam('rate');
		$numStar = $this->_request->getParam('num_star');
		$width = $this->_request->getParam('width_star');
		
		if ($articleId > 0 && $rate > 0 && $rate <= $numStar) {
			$conn = Tomato_Core_Db_Connection::getMasterConnection();
			$conn->insert(Tomato_Core_Db_Connection::getDbPrefix().'news_article_rate',
										array(
											'article_id' => $articleId,
											'rate'	=> $rate,
											'ip' => $this->_request->getClientIp(),
											'created_date' => date('Y-m-d H:i:s'),
										));
		}
		
		$total = $total + $rate;
		$currentRating = ($width > 0 && $numStar > 0) ? ceil($total * $numStar * 100 / $width) : 0;
		
		$this->_view->assign('total', $total);
		$this->_view->assign('currentRating', $currentRating);
	}
}
