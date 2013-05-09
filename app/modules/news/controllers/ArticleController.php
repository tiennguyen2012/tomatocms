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
 * @version 	$Id: ArticleController.php 1963 2010-04-02 04:37:43Z hoangninh $
 */

class News_ArticleController extends Zend_Controller_Action 
{
	public function init() 
	{
		/**
		 * Register hooks
		 * @since 2.0.2
		 */
		Tomato_Core_Hook_Registry::getInstance()->register('News_Article_Add_ShowSidebar', 
			array(new Tomato_Modules_Tag_Hooks_Tagger_Hook(), 'show', array(null, 'article_id', 'news_article_details', 'news_tag_article')));
		Tomato_Core_Hook_Registry::getInstance()->register('News_Article_Add_Success',
			'Tomato_Modules_Tag_Hooks_Tagger_Hook::add');
		Tomato_Core_Hook_Registry::getInstance()->register('News_Article_Edit_Success',
			'Tomato_Modules_Tag_Hooks_Tagger_Hook::add');
	}
	
	/* ========== Frontend actions ========================================== */
	
	public function detailsAction()
	{
		$preview = $this->_request->getParam('preview');
		$preview = ($preview == 'true') ? true : false;
		if ($preview) {
			$revisionId = $this->_request->getParam('revision');
			if ($revisionId) {
				$this->_forward('preview', 'article', 'news', array('revision_id' => $revisionId));
				return;
			}
		}
		
		$articleId = $this->_request->getParam('article_id');
		$categoryId = $this->_request->getParam('category_id');
		$conn = Tomato_Core_Db_Connection::getSlaveConnection();
		$articleGateway = new Tomato_Modules_News_Model_ArticleGateway();
		$articleGateway->setDbConnection($conn);
		$article = $articleGateway->getArticleById($articleId);
		if (null == $article || ($article->status != 'active' && !$preview)) {
			throw new Tomato_Core_Exception_NotFound();
		}
		
		// Add meta description tag
		$description = strip_tags($article->description);
		$this->view->headMeta()->setName('description', $description);
		
		// Format content
		$article->content = Tomato_Core_Hook_Registry::getInstance()->executeFilter('News_Article_Details_FormatContent', $article->content);
		
		/**
		 * Add activate date
		 * @since 2.0.4
		 */
		if (null == $article->activate_date) {
			$article->activate_date = $article->created_date;
		}
		
		$this->view->assign('article', $article);
		
		$categoryGateway = new Tomato_Modules_Category_Model_CategoryGateway();
		$categoryGateway->setDbConnection($conn);
		$category = $categoryGateway->getCategoryById($categoryId);
		$this->view->assign('category', $category);
		
		/**
		 * Increase article views
		 */ 
		if (!$preview && $article->status != 'draft') {
			$cookieName = '__tomato_news_details_numviews';
			$viewed = false;
			if (!isset($_COOKIE[$cookieName])) {
				setcookie($cookieName, $articleId, time() + 3600);
			} else {
				if (strpos($_COOKIE[$cookieName], $articleId) === false) {
					$cookie = $_COOKIE[$cookieName].','.$articleId;
					setcookie($cookieName, $cookie);
				} else {
					$viewed = true;
				}
			}
			if (!$viewed) {
				$conn = Tomato_Core_Db_Connection::getMasterConnection();
				$query = 'UPDATE '.Tomato_Core_Db_Connection::getDbPrefix().'news_article SET num_views = num_views + 1 WHERE article_id = '.$conn->quote($articleId);
				$conn->query($query);
			}
		}
	}
	
	public function categoryAction() 
	{
		$categoryId = $this->_request->getParam('category_id');
		$pageIndex = $this->_request->getParam('page_index', 1);
		if (!$pageIndex) {
			$pageIndex = 1;
		}
		$perPage = 15;
		$start = ($pageIndex - 1) * $perPage;
		
		$conn = Tomato_Core_Db_Connection::getSlaveConnection();
		$categoryGateway = new Tomato_Modules_Category_Model_CategoryGateway();
		$categoryGateway->setDbConnection($conn);
		$category = $categoryGateway->getCategoryById($categoryId);
		
		if (null == $category) {
			throw new Tomato_Core_Exception_NotFound();
		}
		
		// Add RSS link
		$this->view->headLink(array(
			'rel' => 'alternate', 
			'type' => 'application/rss+xml', 
			'title' => 'RSS',
			'href' => $this->view->url($category->getProperties(), 'news_rss_category'),
		));
		
		// Add meta keyword tag
		if ($category->meta) {
			$keyword = strip_tags($category->meta);
			$this->view->headMeta()->setName('keyword', $keyword);
		}
		
		// Add meta description tag
		$description = strip_tags($category->name);
		$this->view->headMeta()->setName('description', $description);
		
		$this->view->assign('category', $category);
		
		$articleGateway = new Tomato_Modules_News_Model_ArticleGateway();
		$select = $conn->select()
						->from(array('a' => Tomato_Core_Db_Connection::getDbPrefix().'news_article'), array('article_id', 'title', 'slug', 'description', 'image_general', 'image_small', 'image_crop', 'activate_date', 'created_user_name', 'category_id', 'num_views', 'icons'))
						->joinInner(array('ac' => Tomato_Core_Db_Connection::getDbPrefix().'news_article_category_assoc'), 'a.article_id = ac.article_id', array('category_id'))
						->joinInner(array('c' => Tomato_Core_Db_Connection::getDbPrefix().'category'), 'a.category_id = c.category_id', array('category_name' => 'name'))
						->where('ac.category_id = ?', $categoryId)
						->where('a.status = ?', 'active')
						->order('a.activate_date DESC')
						->limit($perPage, $start);
		$rs = $select->query()->fetchAll();
		$articles = new Tomato_Core_Model_RecordSet($rs, $articleGateway);
		$this->view->assign('articles', $articles);
		$this->view->assign('category', $category);
		$this->view->assign('pageIndex', $pageIndex);
	}
	
	/**
	 * @since 2.0.2
	 */
	public function searchAction()
	{
		$keyword = $this->_request->getParam('q');
		$keyword = strip_tags($keyword);
		$this->view->assign('keyword', $keyword);
		
		if (null == $keyword) {
			return;
		}
		$conn = Tomato_Core_Db_Connection::getSlaveConnection();
		$articleGateway = new Tomato_Modules_News_Model_ArticleGateway();
		$articleGateway->setDbConnection($conn);
		
		$perPage = 10;
		$pageIndex = $this->_request->getParam('page_index');
		if (null == $pageIndex || '' == $pageIndex) {
			$pageIndex = 1;
		}
		$start = ($pageIndex - 1) * $perPage;
		$this->view->assign('pageIndex', $pageIndex);
		
		$select = $conn->select()
						->from(array('a' => Tomato_Core_Db_Connection::getDbPrefix().'news_article'))
						->where('a.title LIKE "%'.$keyword.'%"')
						->orWhere('a.description LIKE "%'.$keyword.'%"')
						->order('a.article_id DESC')
						->limit($perPage, $start);
		$rs = $select->query()->fetchAll();
		$articles = new Tomato_Core_Model_RecordSet($rs, $articleGateway);
		$this->view->assign('articles', $articles);
						
		$rs = $conn->select()
					->from(array('a' => Tomato_Core_Db_Connection::getDbPrefix().'news_article'), array('num_articles' => 'COUNT(*)'))
					->where('a.title LIKE "%'.$keyword.'%"')
					->limit(1)
					->query()
					->fetch();
		$numArticles = $rs->num_articles;
		$this->view->assign('numArticles', $numArticles);

		// Paginator
		$paginator = new Zend_Paginator(new Tomato_Core_Utility_PaginatorAdapter($articles, $numArticles));
		$paginator->setCurrentPageNumber($pageIndex);
		$paginator->setItemCountPerPage($perPage);
		$this->view->assign('paginator', $paginator);
		$this->view->assign('paginatorOptions', array(
			'path' => $this->view->url(array(), 'news_article_search'),
			'itemLink' => '?q='.$keyword.'&page_index=%d',
		));
		
		// TODO: Use properties from PaginationControl view helper 
		$from = ($numArticles > 0) ? $start + 1 :0;
		$to = 0;
		if ($numArticles > 0) {
			$to = ($numArticles > $pageIndex * $perPage) 
					? $numArticles - ($pageIndex * $perPage) + 1: $numArticles;	
		}		
		$this->view->assign('from', $from);
		$this->view->assign('to', $to);
	}	
	
	/**
	 * @since 2.0.3 
	 */
	public function suggestAction()
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		$conn = Tomato_Core_Db_Connection::getSlaveConnection();
		$articleGateway = new Tomato_Modules_News_Model_ArticleGateway();
		$articleGateway->setDbConnection($conn);
		
		$limit = $this->_request->getParam('limit');
		$keyword = $this->_request->getParam('q');
		$keyword = strip_tags($keyword);
		
		$select = $conn->select()
						->from(array('a' => Tomato_Core_Db_Connection::getDbPrefix().'news_article'))
						->where('a.title LIKE "%'.$keyword.'%"')
						->orWhere('a.description LIKE "%'.$keyword.'%"')
						->order('a.article_id DESC')
						->limit($limit);
		$rs = $select->query()->fetchAll();
		$articles = new Tomato_Core_Model_RecordSet($rs, $articleGateway);
		$response = null;
		foreach ($articles as $article) {
			$response .= $article->title.'|'.$article->article_id
									.'|'.$article->image_square."\n";
		}
		$this->_response->setBody($response);
	}
	
	/* ========== Backend actions =========================================== */
	
	public function addAction() 
	{
		$this->view->addHelperPath(TOMATO_APP_DIR.DS.'modules'.DS.'upload'.DS.'views'.DS.'helpers', 'Upload_View_Helper_');
		
		$conn = Tomato_Core_Db_Connection::getMasterConnection();
		$categoryGateway = new Tomato_Modules_Category_Model_CategoryGateway();
		$categoryGateway->setDbConnection($conn);
		
		$categories = $categoryGateway->getCategoryTree();
		$this->view->assign('categories', $categories); 
		$descriptionPrefix = Tomato_Core_Module_Config::getConfig('news')->general->description_prefix;
		if ($descriptionPrefix == null) {
			$descriptionPrefix = '';	
		}
		$this->view->assign('descriptionPrefix', stripslashes($descriptionPrefix)); 
		
		if ($this->_request->isPost()) {
			$user = Zend_Auth::getInstance()->getIdentity();
			
			$categoryId = $this->_request->getPost('category');
			$title = $this->_request->getPost('title');
			$subTitle = $this->_request->getPost('subTitle');
			$slug = $this->_request->getPost('slug');			
			$description = $this->_request->getPost('description');
			$content = $this->_request->getPost('content');
			$allowComment = $this->_request->getPost('allowComment');
			$hotArticle = $this->_request->getPost('hotArticle');
			$sticky = $this->_request->getPost('stickyCategory');
			$articleCategories = $this->_request->getPost('categories');
			$icons = $this->_request->getPost('icons'); 
			$articleImage = $this->_request->getPost('articleImage');
			$author = $this->_request->getPost('author');
					
			$preview = $this->_request->getPost('preview');
			$preview = ($preview == 'true') ? true : false;
			
			$imageUrls = Zend_Json::decode($articleImage);
			$articleIcons = "";
			if (count($icons) == 1 ) {
				$articleIcons = '{"'.$icons[0].'"}';
			}
			if (count($icons) == 2 ) {
				$articleIcons = '{"'.$icons[0].'","'.$icons[1].'"}';
			}
			$article = new Tomato_Modules_News_Model_Article(array(
				'category_id' => $categoryId,
				'title' => $title,	
				'sub_title' => $subTitle,
				'slug' => $slug,
				'description' => $description,
				'content' => $content,
				'allow_comment' => $allowComment,
				'created_date' => date('Y-m-d H:i:s'),
				'created_user_id' => $user->user_id,
				'created_user_name' => $user->user_name,
				'author' => $author,
				'icons' => $articleIcons,
				'sticky' => false,
			));

			if ($preview) {
				$article->status = 'draft';
			}
			
			if ($sticky == 1) {
				$article->sticky = true;
			}
			if (null != $imageUrls) {
				$article->image_square = $imageUrls['square'];
				$article->image_large = $imageUrls['large'];
				$article->image_general = $imageUrls['general'];
				$article->image_small = $imageUrls['small'];
				$article->image_crop = $imageUrls['crop'];
				$article->image_medium = $imageUrls['medium'];
			}
			$articleGateway = new Tomato_Modules_News_Model_ArticleGateway();
			$articleGateway->setDbConnection($conn);
			$id = $articleGateway->add($article);
			if ($id > 0) {				
				/**
				 * Save draft and preview article
				 * @since 2.0.4
				 */
				if ($preview) {
					$this->_helper->getHelper('viewRenderer')->setNoRender();
					$this->_helper->getHelper('layout')->disableLayout();
					
					$article->article_id = $id;
					$response = array(
									'article_id' => $id,
									'article_url' => $this->view->serverUrl().$this->view->url($article->getProperties(), 'news_article_details').'?preview=true',
									'article_edit_url' => $this->view->serverUrl().$this->view->url($article->getProperties(), 'news_article_edit'),
								);
					$this->_response->setBody(Zend_Json::encode($response));
				} else {
					$conn->insert(Tomato_Core_Db_Connection::getDbPrefix().'news_article_category_assoc', array(
							'category_id' => $categoryId,
							'article_id' => $id,
					));
						
					if ($articleCategories) {
						for ($i = 0; $i < count($articleCategories); $i++) {
							if ($articleCategories[$i] != $categoryId) {
								$conn->insert(Tomato_Core_Db_Connection::getDbPrefix().'news_article_category_assoc', array(
												'category_id' => $articleCategories[$i],
												'article_id' => $id));
							}
						}
					}
					
					if ($hotArticle == 1) {
						$conn->insert(Tomato_Core_Db_Connection::getDbPrefix().'news_article_hot', array(
											'article_id' 	=> $id,
											'created_date' 	=> date('Y-m-d H:i:s'),
											'ordering'		=> 1));
					}
					
					/**
					 * Add new revistion
					 * @since 2.0.4
					 */
					$revision = new Tomato_Modules_News_Model_ArticleRevision(array(
						'article_id' => $id,
						'category_id' => $categoryId,
						'title' => $title,	
						'sub_title' => $subTitle,
						'slug' => $slug,
						'description' => $description,
						'content' => $content,
						'created_date' => date('Y-m-d H:i:s'),
						'created_user_id' => $user->user_id,
						'created_user_name' => $user->user_name,
						'author' => $author,
						'icons' => $articleIcons,
					));
					
					$revisionGateway = new Tomato_Modules_News_Model_ArticleRevisionGateway();
					$revisionGateway->setDbConnection($conn);
					$revisionGateway->add($revision);
					
					/**
					 * Execute hooks
					 * @since 2.0.2
					 */
					Tomato_Core_Hook_Registry::getInstance()->executeAction('News_Article_Add_Success', $id);
					
					$this->_helper->getHelper('FlashMessenger')
						->addMessage($this->view->translator('article_add_success'));
					$this->_redirect($this->view->serverUrl().$this->view->url(array(), 'news_article_add'));
				}
			}
		}
	}
	
	public function editAction() 
	{
		$this->view->addHelperPath(TOMATO_APP_DIR.'/modules/upload/views/helpers', 'Upload_View_Helper_');
		
		$conn = Tomato_Core_Db_Connection::getMasterConnection();
		$categoryGateway = new Tomato_Modules_Category_Model_CategoryGateway();
		$categoryGateway->setDbConnection($conn);
		
		$categories = $categoryGateway->getCategoryTree();
		$this->view->assign('categories', $categories);
		
		$articleGateway = new Tomato_Modules_News_Model_ArticleGateway();
		$articleGateway->setDbConnection($conn);
		$articleId = $this->_request->getParam('article_id');
		
		/**
		 * Registry hook
		 * @since 2.0.2
		 */
		Tomato_Core_Hook_Registry::getInstance()->register('News_Article_Edit_ShowSidebar', 
			array(new Tomato_Modules_Tag_Hooks_Tagger_Hook(), 'show', array($articleId, 'article_id', 'news_article_details', 'news_tag_article')));
		
		$article = $articleGateway->getArticleById($articleId);
		$this->view->assign('article', $article);
		$this->view->assign('articleImages', Zend_Json::encode(array(
			'square' => $article->image_square,
			'large' => $article->image_large,
			'general' => $article->image_general,
			'small' => $article->image_small,
			'crop' => $article->image_crop,
			'medium' => $article->image_medium,
		)));
		
		$select = $conn->select()
						->from(array('a' => Tomato_Core_Db_Connection::getDbPrefix().'news_article_category_assoc'), array('category_id'))
						->where('a.article_id = ?', $articleId);
		$rs = $select->query()->fetchAll();
		$articleCategories = array();
		if ($rs) {
			foreach ($rs as $row) {
				$articleCategories[] = $row->category_id;
			}
		}
		$this->view->assign('articleCategories', $articleCategories);
		
		// Get list of hot articles
		$selectHotArticle = $conn->select()
							->from(Tomato_Core_Db_Connection::getDbPrefix().'news_article_hot')
							->where('article_id = ?', $articleId)
							->limit(1);
		$hotArticle = $selectHotArticle->query()->fetch();	
		$this->view->assign('hotArticle', $hotArticle);
		
		if ($this->_request->isPost()) {
			$user = Zend_Auth::getInstance()->getIdentity();
						
			$categoryId = $this->_request->getPost('category');
			$title = $this->_request->getPost('title');
			$subTitle = $this->_request->getPost('subTitle');
			$slug = $this->_request->getPost('slug');		
			$description = $this->_request->getPost('description');
			$content = $this->_request->getPost('content');
			$allowComment = $this->_request->getPost('allowComment');
			$hotArticle = $this->_request->getPost('hotArticle');
			$sticky = $this->_request->getPost('stickyCategory');
			$articleCategories = $this->_request->getPost('categories');
			$icons = $this->_request->getPost('icons'); 
			$articleImage = $this->_request->getPost('articleImage');
			$author = $this->_request->getPost('author');			
			$imageUrls = Zend_Json::decode($articleImage);
			
			$preview = $this->_request->getPost('preview');
			$preview = ($preview == 'true') ? true : false;
			
			$articleIcons = "";
			if (count($icons) == 1 ) {
				$articleIcons = '{"'.$icons[0].'"}';
			}
			if (count($icons) == 2 ) {
				$articleIcons = '{"'.$icons[0].'","'.$icons[1].'"}';
			}
			$article = new Tomato_Modules_News_Model_Article(array(
				'article_id' => $articleId,
				'category_id' => $categoryId,
				'title' => $title,	
				'sub_title' => $subTitle,
				'slug' => $slug,
				'description' => $description,
				'content' => $content,
				'allow_comment' => $allowComment,
				'created_date' => date('Y-m-d H:i:s'),
				'created_user_id' => $user->user_id,
				'created_user_name' => $user->user_name,
				'author' => $author,
				'icons' => $articleIcons,
				'sticky' => false,
			));
			
			if ($sticky == 1) {
				$article->sticky = true;
			}
			if (null != $imageUrls) {
				$article->image_square = $imageUrls['square'];
				$article->image_large = $imageUrls['large'];
				$article->image_general = $imageUrls['general'];
				$article->image_small = $imageUrls['small'];
				$article->image_crop = $imageUrls['crop'];
				$article->image_medium = $imageUrls['medium'];
			}
			$articleGateway = new Tomato_Modules_News_Model_ArticleGateway();
			$articleGateway->setDbConnection($conn);
			$result = $articleGateway->update($article);
			
			if ($preview) {
				$this->_helper->getHelper('viewRenderer')->setNoRender();
				$this->_helper->getHelper('layout')->disableLayout();
				
				$response = array(
								'article_id' => $article->article_id,
								'article_url' => $this->view->serverUrl().$this->view->url($article->getProperties(), 'news_article_details').'?preview=true',
							);
				$this->_response->setBody(Zend_Json::encode($response));
			} else {
				$conn->delete(Tomato_Core_Db_Connection::getDbPrefix().'news_article_category_assoc', array('article_id = '.$conn->quote($articleId)));
				$conn->insert(Tomato_Core_Db_Connection::getDbPrefix().'news_article_category_assoc', 
								array('category_id' => $categoryId, 'article_id' => $articleId));
				
				if ($articleCategories) {
					for ($i = 0; $i < count($articleCategories); $i++) {
						if ($articleCategories[$i] != $categoryId) {
							$conn->insert(Tomato_Core_Db_Connection::getDbPrefix().'news_article_category_assoc', 
										array('category_id' => $articleCategories[$i], 'article_id' => $articleId));
						}
					}
				}
				if ($hotArticle == 1) {
					$conn->delete(Tomato_Core_Db_Connection::getDbPrefix().'news_article_hot', array('article_id = '.$conn->quote($articleId))); 
					$conn->insert(Tomato_Core_Db_Connection::getDbPrefix().'news_article_hot', array(
										'article_id' 	=> $articleId,
										'created_date' 	=> date('Y-m-d H:i:s'),
										'ordering'		=> 1,
					));
				}
			
				/**
				 * Add new revistion
				 * @since 2.0.4
				 */
				$revision = new Tomato_Modules_News_Model_ArticleRevision(array(
					'article_id' => $articleId,
					'category_id' => $categoryId,
					'title' => $title,	
					'sub_title' => $subTitle,
					'slug' => $slug,
					'description' => $description,
					'content' => $content,
					'created_date' => date('Y-m-d H:i:s'),
					'created_user_id' => $user->user_id,
					'created_user_name' => $user->user_name,
					'author' => $author,
					'icons' => $articleIcons,
				));
				
				$revisionGateway = new Tomato_Modules_News_Model_ArticleRevisionGateway();
				$revisionGateway->setDbConnection($conn);
				$revisionGateway->add($revision);
				
				/**
				 * Execute hooks
				 * @since 2.0.2
				 */
				Tomato_Core_Hook_Registry::getInstance()->executeAction('News_Article_Edit_Success', $articleId);
				
				$this->_helper->getHelper('FlashMessenger')
					->addMessage($this->view->translator('article_edit_success'));
				$this->_redirect($this->view->serverUrl().$this->view->url(array('article_id' => $articleId), 'news_article_edit'));
			}
		}
	}
	
	public function listAction() 
	{
		$conn = Tomato_Core_Db_Connection::getMasterConnection();
		
		$categoryGateway = new Tomato_Modules_Category_Model_CategoryGateway();
		$categoryGateway->setDbConnection($conn);
		$categories = $categoryGateway->getCategoryTree();
		$this->view->assign('categories', $categories);
		
		$perPage = 20;
		$pageIndex = $this->_request->getParam('pageIndex');
		if (null == $pageIndex || '' == $pageIndex) {
			$pageIndex = 1;
		}
		$start = ($pageIndex - 1) * $perPage;
		$this->view->assign('pageIndex', $pageIndex);
		
		// Build article search expression
		$user = Zend_Auth::getInstance()->getIdentity();
		$paramsString = null;
		$exp = array(
			'created_user_id'	=> $user->user_id,
		);
		
		if ($this->_request->isPost()) {
			$id = $this->_request->getPost('articleId');
			$keyword = $this->_request->getPost('keyword');
			$categoryId = $this->_request->getPost('category');
			$status = $this->_request->getPost('status');
			$findMyArticles = $this->_request->getPost('findMyArticles');
			if ($keyword) {
				$exp['keyword'] = $keyword;
			}
			if ($id) {
				$exp['article_id'] = $id;
			}
			if ($categoryId) {
				$exp['category_id'] = $categoryId;
			}
			if (null == $findMyArticles) {
				$exp['created_user_id'] = null;
			}
			if ($status) {
				$exp['status'] = $status;
			}
			$paramsString = rawurlencode(base64_encode(Zend_Json::encode($exp)));
		} else {
			$paramsString = $this->_request->getParam('q');
			if (null != $paramsString) {
				$exp = rawurldecode(base64_decode($paramsString));
				$exp = Zend_Json::decode($exp); 
			} else {
				$paramsString = rawurlencode(base64_encode(Zend_Json::encode($exp)));
			}
		}
		$this->view->assign('exp', $exp);
		
		$articleGateway = new Tomato_Modules_News_Model_ArticleGateway();
		$articleGateway->setDbConnection($conn);
		$articles = $articleGateway->find($start, $perPage, $exp);
		$this->view->assign('articles', $articles);
		
		$numArticles = $articleGateway->count($exp);
		$this->view->assign('numArticles', $numArticles);
		
		// Paginator
		$paginator = new Zend_Paginator(new Tomato_Core_Utility_PaginatorAdapter($articles, $numArticles));
		$paginator->setCurrentPageNumber($pageIndex);
		$paginator->setItemCountPerPage($perPage);
		$this->view->assign('paginator', $paginator);
		$this->view->assign('paginatorOptions', array(
			'path' => $this->view->url(array(), 'news_article_list'),
			'itemLink' => (null == $paramsString) ? 'page-%d' : 'page-%d?q='.$paramsString,
		));
	}
	
	public function hotAction() 
	{
		$conn = Tomato_Core_Db_Connection::getMasterConnection();
		
		if ($this->_request->isPost()) {
			$this->_helper->getHelper('layout')->disableLayout();
			$this->_helper->getHelper('viewRenderer')->setNoRender();
			$articleIds = $this->_request->getPost('tArticleRow');
			$response = 'RESULT_NOT_OK';
			// Update ordering for all hot articles
			$conn->update(Tomato_Core_Db_Connection::getDbPrefix().'news_article_hot', array('ordering' => 1000));
			
			if (is_array($articleIds)) {
				for ($i=0; $i < count($articleIds); $i++) {
					$conn->update(Tomato_Core_Db_Connection::getDbPrefix().'news_article_hot', array(
								'ordering' => ($i + 1)
							), array('article_id = '.$conn->quote($articleIds[$i])));
				}
				$response = 'RESULT_OK';
			}
			$this->_response->setBody($response);
			return;
		}
		
		$select = $conn->select()
						->from(array('a' => Tomato_Core_Db_Connection::getDbPrefix().'news_article'), array('*'))
						->joinInner(array('h' => Tomato_Core_Db_Connection::getDbPrefix().'news_article_hot'),
							'a.article_id = h.article_id', 
							array('ordering', 'h_created_date' => 'created_date'))
						->order('h.ordering')
						->limit(20);
		$rs = $select->query()->fetchAll();
		$articleGateway = new Tomato_Modules_News_Model_ArticleGateway();
		$articleGateway->setDbConnection($conn);
		$articles = new Tomato_Core_Model_RecordSet($rs, $articleGateway);
		$this->view->assign('articles', $articles);
		$this->view->assign('numArticles', $articles->count());
	}
	
	public function activateAction() 
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		if ($this->_request->isPost()) {
			$id = $this->_request->getPost('id');
			$status = $this->_request->getPost('status');
			$status = ($status == 'active') ? 'inactive' : 'active';
			
			$conn = Tomato_Core_Db_Connection::getMasterConnection();
			$gateway = new Tomato_Modules_News_Model_ArticleGateway();	
			$gateway->setDbConnection($conn);
			$gateway->updateStatus($id, $status);
			
			// Update activate date
			$user = Zend_Auth::getInstance()->getIdentity();
			$where[] = 'article_id = '.$conn->quote($id);
			$conn->update(Tomato_Core_Db_Connection::getDbPrefix().'news_article', array(
							'activate_user_id' => $user->user_id,
							'activate_user_name' => $user->user_name,
							'activate_date' => date('Y-m-d H:i:s'),
						), $where);
			
			$this->_response->setBody($status);
		}
	}
	
	public function deleteAction() 
	{
		$this->_helper->getHelper('layout')->disableLayout();
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		
		$result = 'RESULT_ERROR';
		if ($this->_request->isPost()) {
			$conn = Tomato_Core_Db_Connection::getMasterConnection();
			$articleId = $this->_request->getPost('id');
						
			$articleGateway = new Tomato_Modules_News_Model_ArticleGateway();
			$articleGateway->setDbConnection($conn);
			$articleGateway->delete($articleId);
			$result = 'RESULT_OK';
		}
		$this->getResponse()->setBody($result);
	}
	
	/**
	 * @since 2.0.4
	 */
	public function previewAction() 
	{
		$revisionId = $this->_request->getUserParam('revision_id');
		
		$conn = Tomato_Core_Db_Connection::getMasterConnection();
		$revisionGateway = new Tomato_Modules_News_Model_ArticleRevisionGateway();
		$revisionGateway->setDbConnection($conn);
		$revision = $revisionGateway->getArticleRevisionById($revisionId);
		
		if (null == $revision) {
			throw new Tomato_Core_Exception_NotFound();
		}
		
		$this->view->assign('revision', $revision);
	}
}
