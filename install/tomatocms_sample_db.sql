-- MySQL dump 10.11
--
-- Host: localhost    Database: tomato_cms
-- ------------------------------------------------------
-- Server version	5.0.87-community-nt

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `ad_banner`
--

DROP TABLE IF EXISTS `ad_banner`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ad_banner` (
  `banner_id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(200) default NULL,
  `text` varchar(255) default NULL,
  `more_info` text,
  `num_clicks` int(11) default '0',
  `created_date` date default NULL,
  `start_date` date default NULL,
  `expired_date` date default NULL,
  `publish_up` datetime default NULL,
  `publish_down` datetime default NULL,
  `client_id` int(11) default NULL,
  `code` text,
  `click_url` text,
  `target` enum('new_tab','new_window','same_window') default 'new_tab',
  `format` enum('image','flash','text','html') default 'image',
  `image_url` varchar(255) default NULL,
  `ordering` int(11) default '0',
  `mode` enum('unique','share','alternate') default 'unique',
  `timeout` int(11) default '15',
  `status` enum('active','inactive') default 'active',
  PRIMARY KEY  (`banner_id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ad_banner`
--

LOCK TABLES `ad_banner` WRITE;
/*!40000 ALTER TABLE `ad_banner` DISABLE KEYS */;
/*!40000 ALTER TABLE `ad_banner` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ad_click`
--

DROP TABLE IF EXISTS `ad_click`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ad_click` (
  `banner_id` int(11) NOT NULL,
  `zone_id` int(11) NOT NULL,
  `page_id` int(11) default NULL,
  `clicked_date` datetime NOT NULL,
  `ip` varchar(30) NOT NULL,
  `from_url` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ad_click`
--

LOCK TABLES `ad_click` WRITE;
/*!40000 ALTER TABLE `ad_click` DISABLE KEYS */;
/*!40000 ALTER TABLE `ad_click` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ad_client`
--

DROP TABLE IF EXISTS `ad_client`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ad_client` (
  `client_id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(200) NOT NULL,
  `email` varchar(200) default NULL,
  `telephone` varchar(50) default NULL,
  `address` text,
  `created_date` datetime NOT NULL,
  PRIMARY KEY  (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ad_client`
--

LOCK TABLES `ad_client` WRITE;
/*!40000 ALTER TABLE `ad_client` DISABLE KEYS */;
/*!40000 ALTER TABLE `ad_client` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ad_page_assoc`
--

DROP TABLE IF EXISTS `ad_page_assoc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ad_page_assoc` (
  `ad_page_id` int(10) NOT NULL auto_increment,
  `banner_id` int(11) NOT NULL,
  `page_name` varchar(200) default NULL,
  `zone_id` int(11) NOT NULL,
  `page_url` varchar(200) default NULL,
  PRIMARY KEY  (`ad_page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ad_page_assoc`
--

LOCK TABLES `ad_page_assoc` WRITE;
/*!40000 ALTER TABLE `ad_page_assoc` DISABLE KEYS */;
/*!40000 ALTER TABLE `ad_page_assoc` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ad_zone`
--

DROP TABLE IF EXISTS `ad_zone`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ad_zone` (
  `zone_id` int(10) unsigned NOT NULL auto_increment,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` varchar(255) default NULL,
  `price` varchar(255) default NULL,
  PRIMARY KEY  (`zone_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ad_zone`
--

LOCK TABLES `ad_zone` WRITE;
/*!40000 ALTER TABLE `ad_zone` DISABLE KEYS */;
/*!40000 ALTER TABLE `ad_zone` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `category`
--

DROP TABLE IF EXISTS `category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `category` (
  `category_id` smallint(6) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `meta` text,
  `left_id` int(11) NOT NULL,
  `right_id` int(11) NOT NULL,
  `num_views` int(11) default NULL,
  `is_active` tinyint(4) default '1',
  `created_date` datetime default NULL,
  `modified_date` datetime default NULL,
  `user_id` int(11) default NULL,
  PRIMARY KEY  (`category_id`),
  KEY `idx_left_right` (`left_id`,`right_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `category`
--

LOCK TABLES `category` WRITE;
/*!40000 ALTER TABLE `category` DISABLE KEYS */;
INSERT INTO `category` VALUES (1,'Versions','versions','',1,6,NULL,1,'2010-03-15 07:25:45',NULL,1),(2,'Released Versions','released-versions','',2,3,NULL,1,'2010-03-15 07:25:57',NULL,1),(3,'What\'s next','what-is-next','',4,5,NULL,1,'2010-03-15 07:26:19',NULL,1),(4,'User Guide','user-guide','',7,10,NULL,1,'2010-03-15 07:26:32',NULL,1),(5,'Installation Guide','installation-guide','',8,9,NULL,1,'2010-03-15 07:26:51',NULL,1),(6,'Developer Guide','developer-guide','',11,12,NULL,1,'2010-03-15 07:27:03',NULL,1);
/*!40000 ALTER TABLE `category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comment`
--

DROP TABLE IF EXISTS `comment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comment` (
  `comment_id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `full_name` varchar(255) default NULL,
  `web_site` varchar(255) default NULL,
  `email` varchar(100) NOT NULL,
  `user_id` int(11) default NULL,
  `user_name` varchar(100) default NULL,
  `page_url` varchar(255) default NULL,
  `ip` varchar(40) NOT NULL,
  `created_date` datetime NOT NULL,
  `is_active` tinyint(4) NOT NULL,
  `activate_date` datetime default NULL,
  `path` varchar(255) default NULL,
  `ordering` int(11) default '0',
  `depth` int(11) default '0',
  `reply_to` int(11) default '0',
  PRIMARY KEY  (`comment_id`),
  KEY `idx_latest` (`page_url`,`is_active`,`ordering`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comment`
--

LOCK TABLES `comment` WRITE;
/*!40000 ALTER TABLE `comment` DISABLE KEYS */;
INSERT INTO `comment` VALUES (1,'Xin chao','<p>Bai viet ok lem he he he!</p>','Lang tu','http://vietvbb.vn','asdksajdsa@gmail.com',NULL,NULL,'/news/article/view/6/7/','113.22.34.167','2010-03-25 04:25:27',0,NULL,'1-',1,0,0),(2,'test','<p>testddddddddddddddddjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjj</p>','pong','Website','it_commer@hotmail.com',NULL,NULL,'/news/article/view/1/6/','58.147.24.33','2010-03-28 13:56:07',0,NULL,'2-',1,0,0);
/*!40000 ALTER TABLE `comment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `core_hook`
--

DROP TABLE IF EXISTS `core_hook`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `core_hook` (
  `hook_id` int(10) unsigned NOT NULL auto_increment,
  `module` varchar(100) default NULL,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `thumbnail` text,
  `author` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `version` varchar(20) default NULL,
  `license` text,
  PRIMARY KEY  (`hook_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `core_hook`
--

LOCK TABLES `core_hook` WRITE;
/*!40000 ALTER TABLE `core_hook` DISABLE KEYS */;
INSERT INTO `core_hook` VALUES (1,'','syntaxhighlighter','\n		Highligh syntax of code section in content. Powered by SyntaxHighlighter library from http://alexgorbatchev.com \n	','','TomatoCMS Core Team','core@tomatocms.com','2.0.1','free');
/*!40000 ALTER TABLE `core_hook` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `core_module`
--

DROP TABLE IF EXISTS `core_module`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `core_module` (
  `module_id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `thumbnail` text,
  `author` varchar(255) default NULL,
  `email` varchar(100) default NULL,
  `version` varchar(20) default NULL,
  `license` text,
  PRIMARY KEY  (`module_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `core_module`
--

LOCK TABLES `core_module` WRITE;
/*!40000 ALTER TABLE `core_module` DISABLE KEYS */;
INSERT INTO `core_module` VALUES (1,'comment','Manage comments','','TomatoCMS Core Team','core@tomatocms.com','2.0.1','free'),(2,'utility','Provide utilities. Most of utility widgets belong to this module','','TomatoCMS Core Team','core@tomatocms.com','2.0.1','free'),(3,'ad','Manage banners','','TomatoCMS Core Team','core@tomatocms.com','2.0.1','free'),(4,'menu','Manage menus','','TomatoCMS Core Team','core@tomatocms.com','2.0.2','free'),(5,'upload','Upload file and manage uploaded files','','TomatoCMS Core Team','core@tomatocms.com','2.0.1','free'),(6,'poll','Manage polls','','TomatoCMS Core Team','core@tomatocms.com','2.0.1','free'),(7,'seo','Provide utilities which make your site is better for SEO','','TomatoCMS Core Team','core@tomatocms.com','2.0.3','free'),(8,'core','Core module. This module will be installed at the first time you install website','','TomatoCMS Core Team','core@tomatocms.com','2.0.1','free'),(9,'multimedia','Multimedia module: Manage photos and clips','','TomatoCMS Core Team','core@tomatocms.com','2.0.1','free'),(10,'tag','Manage tags','','TomatoCMS Core Team','core@tomatocms.com','2.0.2','free'),(11,'news','Manage articles','','TomatoCMS Core Team','core@tomatocms.com','2.0.1','free'),(12,'category','Manage categories','','TomatoCMS Core Team','core@tomatocms.com','2.0.1','free');
/*!40000 ALTER TABLE `core_module` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `core_page`
--

DROP TABLE IF EXISTS `core_page`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `core_page` (
  `page_id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(50) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text,
  `url` varchar(255) NOT NULL,
  `url_type` enum('static','regex') default 'static',
  `params` text,
  `ordering` smallint(6) default '0',
  PRIMARY KEY  (`page_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `core_page`
--

LOCK TABLES `core_page` WRITE;
/*!40000 ALTER TABLE `core_page` DISABLE KEYS */;
INSERT INTO `core_page` VALUES (1,'home','Homepage',NULL,'/','static',NULL,0),(2,'news_article_category','View articles in category',NULL,'news/category/view/(\\d+)','regex','{\"category_id\": \"1\"}',2),(3,'news_article_details','View article details',NULL,'news/article/view/(\\d+)/(\\d+)','regex','{\"category_id\": \"1\", \"article_id\": \"2\"}',1),(4,'news_article_search','Search for articles',NULL,'news/search','regex','{}',3),(5,'multimedia_file_details','View multimedia file details',NULL,'multimedia/file/details/(\\d+)','regex','{\"file_id\": \"1\"}',4),(6,'multimedia_set_details','View set details',NULL,'multimedia/set/details/(\\d+)','regex','{\"set_id\": \"1\"}',5),(7,'tag_tag_details','List of items tagged by given tag',NULL,'tag/details/(\\w+)/(\\d+)','regex','{\"details_route_name\": \"1\", \"set_id\": \"2\"}',6);
/*!40000 ALTER TABLE `core_page` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `core_plugin`
--

DROP TABLE IF EXISTS `core_plugin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `core_plugin` (
  `plugin_id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `thumbnail` text,
  `author` varchar(255) default NULL,
  `email` varchar(100) default NULL,
  `version` varchar(20) default NULL,
  `license` text,
  `ordering` smallint(6) default NULL,
  PRIMARY KEY  (`plugin_id`),
  KEY `idx_ordering` (`ordering`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `core_plugin`
--

LOCK TABLES `core_plugin` WRITE;
/*!40000 ALTER TABLE `core_plugin` DISABLE KEYS */;
/*!40000 ALTER TABLE `core_plugin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `core_privilege`
--

DROP TABLE IF EXISTS `core_privilege`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `core_privilege` (
  `privilege_id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `description` text,
  `module_name` varchar(100) default NULL,
  `controller_name` varchar(100) default NULL,
  PRIMARY KEY  (`privilege_id`)
) ENGINE=InnoDB AUTO_INCREMENT=125 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `core_privilege`
--

LOCK TABLES `core_privilege` WRITE;
/*!40000 ALTER TABLE `core_privilege` DISABLE KEYS */;
INSERT INTO `core_privilege` VALUES (1,'add','Add/Reply comment','comment','comment'),(2,'activate','Activate comment','comment','comment'),(3,'delete','Delete comment','comment','comment'),(4,'edit','Edit comment','comment','comment'),(5,'list','View the list of comments','comment','comment'),(6,'thread','View the list comments in thread','comment','comment'),(7,'list','View the list of banners','ad','banner'),(8,'add','Create new banner','ad','banner'),(9,'edit','Edit a banner','ad','banner'),(10,'delete','Delete a banner','ad','banner'),(11,'list','View the list of clients','ad','client'),(12,'add','Add new client','ad','client'),(13,'edit','Edit client information','ad','client'),(14,'delete','Delete client','ad','client'),(15,'list','View the list of zones','ad','zone'),(16,'add','Create new zone','ad','zone'),(17,'edit','Edit a zone','ad','zone'),(18,'delete','Delete a zone','ad','zone'),(19,'list','View the list of menus','menu','menu'),(20,'edit','Edit a menu','menu','menu'),(21,'build','Build new menu','menu','menu'),(22,'upload','Upload file to server','upload','file'),(23,'browse','Browse uploaded files','upload','file'),(24,'list','View the list of polls','poll','poll'),(25,'add','Create new poll','poll','poll'),(26,'edit','Update a poll','poll','poll'),(27,'delete','Delete a poll','poll','poll'),(28,'activate','Activate a poll','poll','poll'),(29,'clear','Clear all cached data','core','cache'),(30,'delete','Delete cached item','core','cache'),(31,'list','View the list of cached items by id','core','cache'),(32,'app','Configure application\'s settings','core','config'),(33,'add','Add setting','core','config'),(34,'delete','Delete settings','core','config'),(35,'edit','Edit setting','core','config'),(36,'list','View the list of settings','core','config'),(37,'update','Update setting','core','config'),(38,'config','Config hook','core','hook'),(39,'install','Install hook','core','hook'),(40,'list','View the list of hooks','core','hook'),(41,'uninstall','Uninstall hook','core','hook'),(42,'upload','Upload new hook','core','hook'),(43,'dashboard','Administrator Dashboard','core','index'),(44,'add','Add item to language file','core','language'),(45,'delete','Delete item from language file','core','language'),(46,'edit','Edit language file','core','language'),(47,'list','View the list of language files for module/widget','core','language'),(48,'new','Create new language file','core','language'),(49,'update','Update item in language file','core','language'),(50,'upload','Upload new language package','core','language'),(51,'install','Install module','core','module'),(52,'list','View the list of modules','core','module'),(53,'uninstall','Uninstall module','core','module'),(54,'upload','Upload new module','core','module'),(55,'add','Create new page','core','page'),(56,'edit','Edit page','core','page'),(57,'layout','Update layout of page','core','page'),(58,'list','View the list of pages','core','page'),(59,'ordering','Update order of pages','core','page'),(60,'config','Config plugin','core','plugin'),(61,'install','Install plugin','core','plugin'),(62,'list','View the list of plugins','core','plugin'),(63,'ordering','Update order of plugins','core','plugin'),(64,'uninstall','Uninstall plugin','core','plugin'),(65,'upload','Upload new plugin','core','plugin'),(66,'add','Add action','core','privilege'),(67,'delete','Delete action','core','privilege'),(68,'list','View the list of actions','core','privilege'),(69,'add','Add resource','core','resource'),(70,'delete','Delete resource','core','resource'),(71,'add','Add role','core','role'),(72,'delete','Delete role','core','role'),(73,'list','View the list of roles','core','role'),(74,'lock','Lock/unlock role','core','role'),(75,'role','Set rules for role','core','rule'),(76,'user','Set rules for user','core','rule'),(77,'add','Apply hook for target','core','target'),(78,'list','View the list of targets','core','target'),(79,'remove','Remove hook from target','core','target'),(80,'activate','Activate template','core','template'),(81,'editskin','Edit skin of template','core','template'),(82,'list','View the list of templates','core','template'),(83,'skin','Set skin for current template','core','template'),(84,'activate','Activate/deactivate an user','core','user'),(85,'add','Add user','core','user'),(86,'changepass','Update password','core','user'),(87,'edit','Update user information','core','user'),(88,'list','View the list of users','core','user'),(89,'install','Install widget','core','widget'),(90,'list','View the list of widgets','core','widget'),(91,'uninstall','Uninstall widget','core','widget'),(92,'upload','Upload new widget','core','widget'),(93,'add','Create new file','multimedia','file'),(94,'edit','Edit given file','multimedia','file'),(95,'delete','Delete a file','multimedia','file'),(96,'activate','Activate a file','multimedia','file'),(97,'editor','Image editor','multimedia','file'),(98,'list','View list of notes','multimedia','note'),(99,'edit','Edit given note','multimedia','note'),(100,'delete','Delete a note','multimedia','note'),(101,'activate','Activate a note','multimedia','note'),(102,'upload','Upload new photo','multimedia','photo'),(103,'add','Create new set','multimedia','set'),(104,'edit','Edit given set','multimedia','set'),(105,'delete','Delete a set','multimedia','set'),(106,'activate','Activate a set','multimedia','set'),(107,'list','View the list of tags','tag','tag'),(108,'add','Create new tag','tag','tag'),(109,'delete','Delete a tag','tag','tag'),(110,'activate','Activate article','news','article'),(111,'add','Add new article','news','article'),(112,'delete','Delete article','news','article'),(113,'edit','Edit article','news','article'),(114,'hot','Manage hot articles','news','article'),(115,'list','View the list of articles','news','article'),(116,'preview','Preview article','news','article'),(117,'add','Add new revision','news','revision'),(118,'delete','Delete revision','news','revision'),(119,'list','View the list of article revisions','news','revision'),(120,'restore','Restore revision','news','revision'),(121,'list','View the list of categories','category','category'),(122,'add','Create new category','category','category'),(123,'edit','Edit a category','category','category'),(124,'delete','Delete a category','category','category');
/*!40000 ALTER TABLE `core_privilege` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `core_request_log`
--

DROP TABLE IF EXISTS `core_request_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `core_request_log` (
  `log_id` int(10) unsigned NOT NULL auto_increment,
  `ip` varchar(30) NOT NULL,
  `agent` varchar(255) default NULL,
  `browser` varchar(100) default NULL,
  `version` varchar(30) default NULL,
  `platform` varchar(30) default NULL,
  `bot` varchar(100) default NULL,
  `uri` text,
  `full_url` text,
  `refer_url` text,
  `access_time` datetime NOT NULL,
  PRIMARY KEY  (`log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `core_request_log`
--

LOCK TABLES `core_request_log` WRITE;
/*!40000 ALTER TABLE `core_request_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `core_request_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `core_resource`
--

DROP TABLE IF EXISTS `core_resource`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `core_resource` (
  `resource_id` int(10) unsigned NOT NULL auto_increment,
  `description` text,
  `parent_id` varchar(50) default NULL,
  `module_name` varchar(255) default NULL,
  `controller_name` varchar(255) default NULL,
  PRIMARY KEY  (`resource_id`),
  UNIQUE KEY `idx_name_parent` (`parent_id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `core_resource`
--

LOCK TABLES `core_resource` WRITE;
/*!40000 ALTER TABLE `core_resource` DISABLE KEYS */;
INSERT INTO `core_resource` VALUES (1,'Manage user comments',NULL,'comment','comment'),(2,'Manage banners',NULL,'ad','banner'),(3,'Manage clients',NULL,'ad','client'),(4,'Manage zones',NULL,'ad','zone'),(5,'Manage menu',NULL,'menu','menu'),(6,'Manage uploaded files',NULL,'upload','file'),(7,'Manage polls',NULL,'poll','poll'),(8,'Manage cache',NULL,'core','cache'),(9,'Manage settings',NULL,'core','config'),(10,'Manage hooks',NULL,'core','hook'),(11,'Administrator section',NULL,'core','index'),(12,'Manage languages',NULL,'core','language'),(13,'Manage modules',NULL,'core','module'),(14,'Manage pages',NULL,'core','page'),(15,'Manage plugins',NULL,'core','plugin'),(16,'Manage actions to resource',NULL,'core','privilege'),(17,'Manage resources',NULL,'core','resource'),(18,'Manage roles',NULL,'core','role'),(19,'Manage rules',NULL,'core','rule'),(20,'Manage hook targets',NULL,'core','target'),(21,'Manage templates',NULL,'core','template'),(22,'Manage users',NULL,'core','user'),(23,'Manage widgets',NULL,'core','widget'),(24,'Manage files',NULL,'multimedia','file'),(25,'Manage notes',NULL,'multimedia','note'),(26,'Manage photos',NULL,'multimedia','photo'),(27,'Manage sets',NULL,'multimedia','set'),(28,'Manage tags',NULL,'tag','tag'),(29,'Manage articles',NULL,'news','article'),(30,'Manage revisions',NULL,'news','revision'),(31,'Manage categories',NULL,'category','category');
/*!40000 ALTER TABLE `core_resource` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `core_role`
--

DROP TABLE IF EXISTS `core_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `core_role` (
  `role_id` int(50) unsigned NOT NULL auto_increment,
  `name` varchar(200) NOT NULL,
  `description` varchar(255) NOT NULL,
  `locked` tinyint(4) NOT NULL,
  PRIMARY KEY  (`role_id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `core_role`
--

LOCK TABLES `core_role` WRITE;
/*!40000 ALTER TABLE `core_role` DISABLE KEYS */;
INSERT INTO `core_role` VALUES (1,'admin','Administrator',1);
/*!40000 ALTER TABLE `core_role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `core_role_inheritance`
--

DROP TABLE IF EXISTS `core_role_inheritance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `core_role_inheritance` (
  `child_id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `ordering` int(11) NOT NULL default '0',
  PRIMARY KEY  (`child_id`,`parent_id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `core_role_inheritance`
--

LOCK TABLES `core_role_inheritance` WRITE;
/*!40000 ALTER TABLE `core_role_inheritance` DISABLE KEYS */;
/*!40000 ALTER TABLE `core_role_inheritance` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `core_rule`
--

DROP TABLE IF EXISTS `core_rule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `core_rule` (
  `rule_id` int(10) unsigned NOT NULL auto_increment,
  `obj_id` int(50) NOT NULL,
  `obj_type` enum('user','role') default 'role',
  `privilege_id` int(11) default NULL,
  `allow` tinyint(1) NOT NULL default '0',
  `resource_name` varchar(100) default NULL,
  PRIMARY KEY  (`rule_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `core_rule`
--

LOCK TABLES `core_rule` WRITE;
/*!40000 ALTER TABLE `core_rule` DISABLE KEYS */;
INSERT INTO `core_rule` VALUES (1,1,'role',NULL,1,NULL);
/*!40000 ALTER TABLE `core_rule` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `core_session`
--

DROP TABLE IF EXISTS `core_session`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `core_session` (
  `session_id` varchar(255) NOT NULL,
  `data` text NOT NULL,
  `modified` int(11) default NULL,
  `lifetime` int(11) default NULL,
  PRIMARY KEY  (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `core_session`
--

LOCK TABLES `core_session` WRITE;
/*!40000 ALTER TABLE `core_session` DISABLE KEYS */;
INSERT INTO `core_session` VALUES ('p329ko400g3lfnrdqokfdqlh02','Zend_Auth|a:1:{s:7:\"storage\";O:30:\"Tomato_Modules_Core_Model_User\":1:{s:14:\"\0*\0_properties\";a:11:{s:7:\"user_id\";s:1:\"1\";s:9:\"user_name\";s:5:\"admin\";s:8:\"password\";s:32:\"21232f297a57a5a743894a0e4a801fc3\";s:9:\"full_name\";s:13:\"Administrator\";s:5:\"email\";s:15:\"admin@email.com\";s:9:\"is_active\";s:1:\"1\";s:12:\"created_date\";N;s:14:\"logged_in_date\";N;s:9:\"is_online\";s:1:\"0\";s:7:\"role_id\";s:1:\"1\";s:9:\"role_name\";s:5:\"admin\";}}}',1270197024,3600),('pi4aa6k9o8hoh8ak9ib2mp4rc3','',1270197034,3600);
/*!40000 ALTER TABLE `core_session` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `core_target`
--

DROP TABLE IF EXISTS `core_target`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `core_target` (
  `target_id` int(10) unsigned NOT NULL auto_increment,
  `target_module` varchar(100) default NULL,
  `target_name` varchar(255) NOT NULL,
  `description` text,
  `hook_module` varchar(100) default NULL,
  `hook_name` varchar(100) NOT NULL,
  `hook_type` enum('action','filter') default 'action',
  PRIMARY KEY  (`target_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `core_target`
--

LOCK TABLES `core_target` WRITE;
/*!40000 ALTER TABLE `core_target` DISABLE KEYS */;
INSERT INTO `core_target` VALUES (1,'comment','Comment_Widget_Comment_FormatContent','Format content of comment','','syntaxhighlighter','filter'),(2,'news','News_Article_Details_FormatContent','Format content of article','','syntaxhighlighter','filter');
/*!40000 ALTER TABLE `core_target` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `core_user`
--

DROP TABLE IF EXISTS `core_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `core_user` (
  `user_id` int(10) unsigned NOT NULL auto_increment,
  `role_id` int(11) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `password` varchar(50) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `is_active` tinyint(4) NOT NULL default '0',
  `created_date` datetime default NULL,
  `logged_in_date` datetime default NULL,
  `is_online` tinyint(4) default '0',
  PRIMARY KEY  (`user_id`),
  UNIQUE KEY `user_name` (`user_name`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `core_user`
--

LOCK TABLES `core_user` WRITE;
/*!40000 ALTER TABLE `core_user` DISABLE KEYS */;
INSERT INTO `core_user` VALUES (1,1,'admin','21232f297a57a5a743894a0e4a801fc3','Administrator','admin@email.com',1,NULL,NULL,0);
/*!40000 ALTER TABLE `core_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `core_widget`
--

DROP TABLE IF EXISTS `core_widget`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `core_widget` (
  `widget_id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(100) NOT NULL,
  `title` varchar(100) default NULL,
  `module` varchar(100) NOT NULL,
  `description` text,
  `thumbnail` text,
  `author` varchar(255) default NULL,
  `email` varchar(100) default NULL,
  `version` varchar(20) default NULL,
  `license` text,
  PRIMARY KEY  (`widget_id`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `core_widget`
--

LOCK TABLES `core_widget` WRITE;
/*!40000 ALTER TABLE `core_widget` DISABLE KEYS */;
INSERT INTO `core_widget` VALUES (1,'latestcomment','Latest comments','comment','Show the latest comments','','TomatoCMS Core Team','core@tomatocms.com','2.0.1','free'),(2,'comment','Latest comments','comment','Show the latest comments','','TomatoCMS Core Team','core@tomatocms.com','2.0.1','free'),(3,'disqus','Disqus comments','comment','Show comments powered by Disqus','','TomatoCMS Core Team','core@tomatocms.com','2.0.3','free'),(4,'countdown','Countdown','utility','Show a countdown to given event','','TomatoCMS Core Team','core@tomatocms.com','2.0.1','free'),(5,'twitter','Update from Twitter','utility','Show latest updates from Twitter account','','TomatoCMS Core Team','core@tomatocms.com','2.0.1','free'),(6,'feed','Feed','utility','Show entries from RSS channel','','TomatoCMS Core Team','core@tomatocms.com','2.0.1','free'),(7,'clipplayer','Clip','utility','Show the vide clip','','TomatoCMS Core Team','core@tomatocms.com','2.0.1','free'),(8,'textresizer','Text Resizer','utility','Allows user to select the smaller or larger font','','TomatoCMS Core Team','core@tomatocms.com','2.0.3','free'),(9,'youtubeplayer','YouTube clip','utility','Show the clip from YouTube','','TomatoCMS Core Team','core@tomatocms.com','2.0.1','free'),(10,'flickr','Flickr photos','utility','Show the photos from Flickr','','TomatoCMS Core Team','core@tomatocms.com','2.0.3','free'),(11,'socialshare','Share via social networks','utility','Share links via some popular social networks','','TomatoCMS Core Team','core@tomatocms.com','2.0.1','free'),(12,'zone','Banner','ad','Show the banner at given zone','','TomatoCMS Core Team','core@tomatocms.com','2.0.1','free'),(13,'menu','Menu','menu','Show menu','','TomatoCMS Core Team','core@tomatocms.com','2.0.2','free'),(14,'vote','Poll','poll','Show a poll','','TomatoCMS Core Team','core@tomatocms.com','2.0.1','free'),(15,'googler','Welcome Googler','seo','Show message when user visit website from Google','','TomatoCMS Core Team','core@tomatocms.com','2.0.3','free'),(16,'html','HTML content','core','Show HTML content','','TomatoCMS Core Team','core@tomatocms.com','2.0.1','free'),(17,'skinselector','Skin selector','core','User can change skin of website','','TomatoCMS Core Team','core@tomatocms.com','2.0.1','free'),(18,'itomato','iTomato','core','User can drag and drop widgets on page','','TomatoCMS Core Team','core@tomatocms.com','2.0.1','free'),(19,'slideshow','Slideshow','multimedia','Slideshow consist of latest photos','','TomatoCMS Core Team','core@tomatocms.com','2.0.1','free'),(20,'latestsets','Latest sets','multimedia','Show latest sets','','TomatoCMS Core Team','core@tomatocms.com','2.0.2','free'),(21,'player','Latest video clips','multimedia','Show the latest video clips','','TomatoCMS Core Team','core@tomatocms.com','2.0.1','free'),(22,'filesets','File sets','multimedia','Show list of sets which file belongs to','','TomatoCMS Core Team','core@tomatocms.com','2.0.2','free'),(23,'tagcloud','Tag Cloud','tag','Show tag cloud associated with items which have the same type: article, photo, for example','','TomatoCMS Core Team','core@tomatocms.com','2.0.2','free'),(24,'tags','Tags','tag','Show tags associated with given object','','TomatoCMS Core Team','core@tomatocms.com','2.0.2','free'),(25,'newer','Newer articles','news','Show the articles that are newer than current being viewed article','','TomatoCMS Core Team','core@tomatocms.com','2.0.1','free'),(26,'breadcump','Breadcump','news','Show breadcump to given category','','TomatoCMS Core Team','core@tomatocms.com','2.0.1','free'),(27,'mostrated','Most rated','news','Show the most rated articles','','TomatoCMS Core Team','core@tomatocms.com','2.0.3','free'),(28,'rating','Rate article','news','Allow user to rate articles','','TomatoCMS Core Team','core@tomatocms.com','2.0.3','free'),(29,'stickycategory','Sticky articles','news','Show the sticky articles of given category','','TomatoCMS Core Team','core@tomatocms.com','2.0.1','free'),(30,'latesthome','Latest articles in homepage','news','Show the latest articles in homepage','','TomatoCMS Core Team','core@tomatocms.com','2.0.1','free'),(31,'newest','Newest articles','news','Show the newest articles','','TomatoCMS Core Team','core@tomatocms.com','2.0.1','free'),(32,'older','Older articles','news','Show the articles that are older than current article','','TomatoCMS Core Team','core@tomatocms.com','2.0.1','free'),(33,'siblingcategory','Sibling category','news','Show the sibling category and latest articles from them','','TomatoCMS Core Team','core@tomatocms.com','2.0.1','free'),(34,'hotest','Hotest','news','Show the hotest articles','','TomatoCMS Core Team','core@tomatocms.com','2.0.1','free'),(35,'mostviewed','Most viewed','news','Show the most viewed articles','','TomatoCMS Core Team','core@tomatocms.com','2.0.1','free'),(36,'latestcategory','Latest articles','news','Show the latest articles of given category','','TomatoCMS Core Team','core@tomatocms.com','2.0.1','free'),(37,'category','Category','news','List of categories and link to them','','TomatoCMS Core Team','core@tomatocms.com','2.0.1','free'),(38,'searchbox','Search Box','news','News search box','','TomatoCMS Core Team','core@tomatocms.com','2.0.2','free');
/*!40000 ALTER TABLE `core_widget` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menu`
--

DROP TABLE IF EXISTS `menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `menu` (
  `menu_id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `description` text,
  `json_data` text,
  `user_id` int(11) default NULL,
  `user_name` varchar(255) default NULL,
  `created_date` datetime default NULL,
  PRIMARY KEY  (`menu_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu`
--

LOCK TABLES `menu` WRITE;
/*!40000 ALTER TABLE `menu` DISABLE KEYS */;
INSERT INTO `menu` VALUES (1,'Top menu','Top menu','\"<ul><li class=\\\"t_a_menu_item\\\" id=\\\"t_a_menu_0\\\"><a href=\\\"http://localhost/tomatocms/index.php\\\">Home</a></li><li class=\\\"t_a_menu_item\\\" id=\\\"t_a_menu_1\\\"><a href=\\\"http://localhost/tomatocms/index.php/news/category/view/1/\\\">Versions</a></li><li class=\\\"t_a_menu_item\\\" id=\\\"t_a_menu_2\\\"><a href=\\\"http://localhost/tomatocms/index.php/news/category/view/4/\\\">User Guide</a></li><li class=\\\"t_a_menu_item\\\" id=\\\"t_a_menu_3\\\"><a href=\\\"http://localhost/tomatocms/index.php/news/category/view/6/\\\">Developer Guide</a></li></ul>\"',1,'admin','2010-03-15 18:05:58');
/*!40000 ALTER TABLE `menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `multimedia_file`
--

DROP TABLE IF EXISTS `multimedia_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `multimedia_file` (
  `file_id` int(11) unsigned NOT NULL auto_increment,
  `category_id` int(11) default '0',
  `title` varchar(255) default NULL,
  `slug` varchar(255) default NULL,
  `description` text,
  `content` text,
  `image_general` text,
  `image_medium` text,
  `image_crop` text,
  `image_small` text,
  `image_square` text,
  `image_large` text,
  `num_views` int(11) default '0',
  `created_date` datetime default NULL,
  `created_user` int(11) default NULL,
  `created_user_name` varchar(255) default NULL,
  `allow_comment` tinyint(4) default '1',
  `ordering` int(11) default '0',
  `num_comments` int(11) default '0',
  `url` varchar(255) default NULL,
  `html_code` text,
  `is_active` tinyint(1) default '1',
  `file_type` enum('image','video','audio','game') default 'image',
  `image_original` text,
  PRIMARY KEY  (`file_id`),
  KEY `idx_latest` (`is_active`,`file_type`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `multimedia_file`
--

LOCK TABLES `multimedia_file` WRITE;
/*!40000 ALTER TABLE `multimedia_file` DISABLE KEYS */;
INSERT INTO `multimedia_file` VALUES (1,0,'ACL based permission system','acl-based-permission-system',NULL,NULL,'http://demo.tomatocms.com/upload/multimedia/admin/2010/03/4b9e7ebde0c91_general.png','http://demo.tomatocms.com/upload/multimedia/admin/2010/03/4b9e7ebde0c91_medium.png','http://demo.tomatocms.com/upload/multimedia/admin/2010/03/4b9e7ebde0c91_crop.png','http://demo.tomatocms.com/upload/multimedia/admin/2010/03/4b9e7ebde0c91_small.png','http://demo.tomatocms.com/upload/multimedia/admin/2010/03/4b9e7ebde0c91_square.png','http://demo.tomatocms.com/upload/multimedia/admin/2010/03/4b9e7ebde0c91_large.png',0,'2010-03-15 18:39:44',1,'admin',1,0,0,'http://demo.tomatocms.com/upload/multimedia/admin/2010/03/4b9e7ebde0c91.png',NULL,1,'image','http://demo.tomatocms.com/upload/multimedia/admin/2010/03/4b9e7ebde0c91.png'),(2,0,'Support hook architecture','support-hook-architecture',NULL,NULL,'http://demo.tomatocms.com/upload/multimedia/admin/2010/03/4b9e7ece5a8a7_general.png','http://demo.tomatocms.com/upload/multimedia/admin/2010/03/4b9e7ece5a8a7_medium.png','http://demo.tomatocms.com/upload/multimedia/admin/2010/03/4b9e7ece5a8a7_crop.png','http://demo.tomatocms.com/upload/multimedia/admin/2010/03/4b9e7ece5a8a7_small.png','http://demo.tomatocms.com/upload/multimedia/admin/2010/03/4b9e7ece5a8a7_square.png','http://demo.tomatocms.com/upload/multimedia/admin/2010/03/4b9e7ece5a8a7_large.png',0,'2010-03-15 18:39:44',1,'admin',1,0,0,'http://demo.tomatocms.com/upload/multimedia/admin/2010/03/4b9e7ece5a8a7.png',NULL,1,'image','http://demo.tomatocms.com/upload/multimedia/admin/2010/03/4b9e7ece5a8a7.png'),(3,0,'Provide many built-in modules','provide-many-built-in-modules',NULL,NULL,'http://demo.tomatocms.com/upload/multimedia/admin/2010/03/4b9e7ed052b49_general.png','http://demo.tomatocms.com/upload/multimedia/admin/2010/03/4b9e7ed052b49_medium.png','http://demo.tomatocms.com/upload/multimedia/admin/2010/03/4b9e7ed052b49_crop.png','http://demo.tomatocms.com/upload/multimedia/admin/2010/03/4b9e7ed052b49_small.png','http://demo.tomatocms.com/upload/multimedia/admin/2010/03/4b9e7ed052b49_square.png','http://demo.tomatocms.com/upload/multimedia/admin/2010/03/4b9e7ed052b49_large.png',0,'2010-03-15 18:39:44',1,'admin',1,0,0,'http://demo.tomatocms.com/upload/multimedia/admin/2010/03/4b9e7ed052b49.png',NULL,1,'image','http://demo.tomatocms.com/upload/multimedia/admin/2010/03/4b9e7ed052b49.png'),(4,0,'Support plugin architecture','support-plugin-architecture',NULL,NULL,'http://demo.tomatocms.com/upload/multimedia/admin/2010/03/4b9e7ed25c728_general.png','http://demo.tomatocms.com/upload/multimedia/admin/2010/03/4b9e7ed25c728_medium.png','http://demo.tomatocms.com/upload/multimedia/admin/2010/03/4b9e7ed25c728_crop.png','http://demo.tomatocms.com/upload/multimedia/admin/2010/03/4b9e7ed25c728_small.png','http://demo.tomatocms.com/upload/multimedia/admin/2010/03/4b9e7ed25c728_square.png','http://demo.tomatocms.com/upload/multimedia/admin/2010/03/4b9e7ed25c728_large.png',0,'2010-03-15 18:39:44',1,'admin',1,0,0,'http://demo.tomatocms.com/upload/multimedia/admin/2010/03/4b9e7ed25c728.png',NULL,1,'image','http://demo.tomatocms.com/upload/multimedia/admin/2010/03/4b9e7ed25c728.png'),(5,0,'Blog Template','blog-template',NULL,NULL,'http://demo.tomatocms.com/upload/multimedia/admin/2010/03/4b9e7ed7404fb_general.png','http://demo.tomatocms.com/upload/multimedia/admin/2010/03/4b9e7ed7404fb_medium.png','http://demo.tomatocms.com/upload/multimedia/admin/2010/03/4b9e7ed7404fb_crop.png','http://demo.tomatocms.com/upload/multimedia/admin/2010/03/4b9e7ed7404fb_small.png','http://demo.tomatocms.com/upload/multimedia/admin/2010/03/4b9e7ed7404fb_square.png','http://demo.tomatocms.com/upload/multimedia/admin/2010/03/4b9e7ed7404fb_large.png',0,'2010-03-15 18:39:44',1,'admin',1,0,0,'http://demo.tomatocms.com/upload/multimedia/admin/2010/03/4b9e7ed7404fb.png',NULL,1,'image','http://demo.tomatocms.com/upload/multimedia/admin/2010/03/4b9e7ed7404fb.png'),(6,0,'Default template','default-template',NULL,NULL,'http://demo.tomatocms.com/upload/multimedia/admin/2010/03/4b9e7eddab682_general.png','http://demo.tomatocms.com/upload/multimedia/admin/2010/03/4b9e7eddab682_medium.png','http://demo.tomatocms.com/upload/multimedia/admin/2010/03/4b9e7eddab682_crop.png','http://demo.tomatocms.com/upload/multimedia/admin/2010/03/4b9e7eddab682_small.png','http://demo.tomatocms.com/upload/multimedia/admin/2010/03/4b9e7eddab682_square.png','http://demo.tomatocms.com/upload/multimedia/admin/2010/03/4b9e7eddab682_large.png',0,'2010-03-15 18:39:44',1,'admin',1,0,0,'http://demo.tomatocms.com/upload/multimedia/admin/2010/03/4b9e7eddab682.png',NULL,1,'image','http://demo.tomatocms.com/upload/multimedia/admin/2010/03/4b9e7eddab682.png'),(7,0,'Support multilingual site for both front-end and back-end','support-multilingual-site-for-both-front-end-and-back-end',NULL,NULL,'http://demo.tomatocms.com/upload/multimedia/admin/2010/03/4b9e7ee013c4b_general.png','http://demo.tomatocms.com/upload/multimedia/admin/2010/03/4b9e7ee013c4b_medium.png','http://demo.tomatocms.com/upload/multimedia/admin/2010/03/4b9e7ee013c4b_crop.png','http://demo.tomatocms.com/upload/multimedia/admin/2010/03/4b9e7ee013c4b_small.png','http://demo.tomatocms.com/upload/multimedia/admin/2010/03/4b9e7ee013c4b_square.png','http://demo.tomatocms.com/upload/multimedia/admin/2010/03/4b9e7ee013c4b_large.png',0,'2010-03-15 18:39:44',1,'admin',1,0,0,'http://demo.tomatocms.com/upload/multimedia/admin/2010/03/4b9e7ee013c4b.png',NULL,1,'image','http://demo.tomatocms.com/upload/multimedia/admin/2010/03/4b9e7ee013c4b.png'),(8,0,'Widgets management','widgets-management',NULL,NULL,'http://demo.tomatocms.com/upload/multimedia/admin/2010/03/4b9e7ee237e00_general.png','http://demo.tomatocms.com/upload/multimedia/admin/2010/03/4b9e7ee237e00_medium.png','http://demo.tomatocms.com/upload/multimedia/admin/2010/03/4b9e7ee237e00_crop.png','http://demo.tomatocms.com/upload/multimedia/admin/2010/03/4b9e7ee237e00_small.png','http://demo.tomatocms.com/upload/multimedia/admin/2010/03/4b9e7ee237e00_square.png','http://demo.tomatocms.com/upload/multimedia/admin/2010/03/4b9e7ee237e00_large.png',0,'2010-03-15 18:39:44',1,'admin',1,0,0,'http://demo.tomatocms.com/upload/multimedia/admin/2010/03/4b9e7ee237e00.png',NULL,1,'image','http://demo.tomatocms.com/upload/multimedia/admin/2010/03/4b9e7ee237e00.png');
/*!40000 ALTER TABLE `multimedia_file` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `multimedia_file_set_assoc`
--

DROP TABLE IF EXISTS `multimedia_file_set_assoc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `multimedia_file_set_assoc` (
  `file_id` int(11) NOT NULL,
  `set_id` int(11) NOT NULL,
  PRIMARY KEY  (`file_id`,`set_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `multimedia_file_set_assoc`
--

LOCK TABLES `multimedia_file_set_assoc` WRITE;
/*!40000 ALTER TABLE `multimedia_file_set_assoc` DISABLE KEYS */;
INSERT INTO `multimedia_file_set_assoc` VALUES (1,1),(2,1),(3,1),(4,1),(5,1),(6,1),(7,1),(8,1);
/*!40000 ALTER TABLE `multimedia_file_set_assoc` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `multimedia_note`
--

DROP TABLE IF EXISTS `multimedia_note`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `multimedia_note` (
  `note_id` int(10) unsigned NOT NULL auto_increment,
  `file_id` int(11) NOT NULL,
  `top` int(11) NOT NULL,
  `left` int(11) NOT NULL,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `content` varchar(200) default NULL,
  `is_active` tinyint(1) default '0',
  `user_id` int(11) default NULL,
  `user_name` varchar(100) default NULL,
  `created_date` datetime NOT NULL,
  PRIMARY KEY  (`note_id`),
  KEY `file_id` (`file_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `multimedia_note`
--

LOCK TABLES `multimedia_note` WRITE;
/*!40000 ALTER TABLE `multimedia_note` DISABLE KEYS */;
/*!40000 ALTER TABLE `multimedia_note` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `multimedia_set`
--

DROP TABLE IF EXISTS `multimedia_set`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `multimedia_set` (
  `set_id` int(11) NOT NULL auto_increment,
  `slug` varchar(255) default NULL,
  `title` varchar(255) NOT NULL,
  `description` text,
  `created_date` datetime default NULL,
  `updated_date` datetime default NULL,
  `created_user_id` int(11) default NULL,
  `created_user_name` varchar(255) default NULL,
  `num_views` int(11) default '0',
  `num_comments` int(11) default '0',
  `is_active` tinyint(4) default '1',
  `image_general` text,
  `image_medium` text,
  `image_crop` text,
  `image_small` text,
  `image_square` text,
  `image_large` text,
  PRIMARY KEY  (`set_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `multimedia_set`
--

LOCK TABLES `multimedia_set` WRITE;
/*!40000 ALTER TABLE `multimedia_set` DISABLE KEYS */;
INSERT INTO `multimedia_set` VALUES (1,'tomatocms-features','TomatoCMS features','<p>This set presents features of TomatoCMS.</p>','2010-03-15 18:39:44',NULL,1,'admin',0,0,1,'http://demo.tomatocms.com/upload/multimedia/admin/2010/03/4b9e7eddab682_general.png','http://demo.tomatocms.com/upload/multimedia/admin/2010/03/4b9e7eddab682_medium.png','http://demo.tomatocms.com/upload/multimedia/admin/2010/03/4b9e7eddab682_crop.png','http://demo.tomatocms.com/upload/multimedia/admin/2010/03/4b9e7eddab682_small.png','http://demo.tomatocms.com/upload/multimedia/admin/2010/03/4b9e7eddab682_square.png','http://demo.tomatocms.com/upload/multimedia/admin/2010/03/4b9e7eddab682_large.png');
/*!40000 ALTER TABLE `multimedia_set` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `news_article`
--

DROP TABLE IF EXISTS `news_article`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `news_article` (
  `article_id` int(11) NOT NULL auto_increment,
  `category_id` smallint(6) default NULL,
  `title` varchar(255) default NULL,
  `sub_title` varchar(255) default NULL,
  `slug` varchar(255) default NULL,
  `description` text,
  `content` mediumtext,
  `author` varchar(255) default NULL,
  `icons` varchar(255) default NULL,
  `image_square` varchar(255) default NULL,
  `image_thumbnail` varchar(255) default NULL,
  `image_small` varchar(255) default NULL,
  `image_general` varchar(255) default NULL,
  `image_crop` varchar(255) default NULL,
  `image_medium` varchar(255) default NULL,
  `image_large` varchar(255) default NULL,
  `status` enum('deleted','draft','inactive','active') default 'inactive',
  `num_views` int(11) default '0',
  `created_date` datetime default NULL,
  `created_user_id` int(11) default NULL,
  `created_user_name` varchar(255) default NULL,
  `updated_date` datetime default NULL,
  `updated_user_id` int(11) default NULL,
  `updated_user_name` varchar(255) default NULL,
  `activate_user_id` int(11) default NULL,
  `activate_user_name` varchar(50) default NULL,
  `activate_date` datetime default NULL,
  `allow_comment` tinyint(4) default NULL,
  `num_comments` int(11) default '0',
  `is_hot` tinyint(4) default '0',
  `ordering` int(11) default '0',
  `show_date` date default NULL,
  `sticky` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`article_id`),
  KEY `idx_latest` (`status`,`activate_date`),
  KEY `idx_latest_category` (`category_id`,`status`,`activate_date`),
  KEY `idx_most_commented` (`category_id`,`status`,`num_comments`),
  KEY `idx_most_viewed` (`category_id`,`status`,`num_views`),
  KEY `idx_most_viewed_2` (`status`,`num_views`),
  KEY `idx_created_user` (`created_user_id`,`article_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `news_article`
--

LOCK TABLES `news_article` WRITE;
/*!40000 ALTER TABLE `news_article` DISABLE KEYS */;
INSERT INTO `news_article` VALUES (1,1,'January, 4th, 2010: TomatoCMS 2.0.0 released','','january-4th-2010-tomatocms-2-0-0-released','<p>This is first release of TomatoCMS.</p>','<p>This is first release of TomatoCMS.</p>\r\n<p>&nbsp;</p>\r\n<p><img src=\"http://demo.tomatocms.com/upload/news/admin/2010/03/4b9de32bc7c47_medium.png\" alt=\"\" /></p>','Nguyen Huu Phuoc','','http://demo.tomatocms.com/upload/news/admin/2010/03/4b9de32bc7c47_square.png',NULL,'http://demo.tomatocms.com/upload/news/admin/2010/03/4b9de32bc7c47_small.png','http://demo.tomatocms.com/upload/news/admin/2010/03/4b9de32bc7c47_general.png','http://demo.tomatocms.com/upload/news/admin/2010/03/4b9de32bc7c47_crop.png','http://demo.tomatocms.com/upload/news/admin/2010/03/4b9de32bc7c47_medium.png','http://demo.tomatocms.com/upload/news/admin/2010/03/4b9de32bc7c47_large.png','active',72,'2010-03-15 07:35:49',1,'admin',NULL,NULL,NULL,1,'admin','2010-03-15 07:35:53',0,0,0,0,NULL,0),(2,1,'January, 15h, 2010: TomatoCMS 2.0.1 released','','january-15h-2010-tomatocms-2-0-1-released','<p>We released TomatoCMS 2.0.1 version on Jan, 15th, 2010.</p>\r\n<p>This release comes with Install Wizard, Nested Comment System, Update Informer and many bugs fixed.</p>','<h1>What\'s new?</h1>\r\n<h2><strong>1) Install Wizard</strong></h2>\r\n<p>Now, Install Wizard only take three steps to install TomatoCMS. You can install it in root web directory or its sub-directory.</p>\r\n<p>&nbsp;</p>\r\n<p><img src=\"http://demo.tomatocms.com/upload/news/admin/2010/03/4b9db975db775_medium.png\" alt=\"\" /></p>\r\n<p>&nbsp;</p>\r\n<h2>2) Nested Comment System</h2>\r\n<p>- Support nested, unlimitted level comments:</p>\r\n<p>&nbsp;</p>\r\n<p><img src=\"http://demo.tomatocms.com/upload/news/admin/2010/03/4b9db97bef537_medium.png\" alt=\"\" /></p>\r\n<p>&nbsp;</p>\r\n<p>- Shows avatar of commenters:</p>\r\n<p><img src=\"http://demo.tomatocms.com/upload/news/admin/2010/03/4b9db97717700_medium.png\" alt=\"\" /></p>\r\n<p>&nbsp;</p>\r\n<p>- Allows users to use some simple HTML tags in comment:</p>\r\n<p><img src=\"http://demo.tomatocms.com/upload/news/admin/2010/03/4b9db97a16fdd_medium.png\" alt=\"\" /></p>\r\n<p>&nbsp;</p>\r\n<p>- User can reply any comment in thread:</p>\r\n<p><img src=\"http://demo.tomatocms.com/upload/news/admin/2010/03/4b9db97b16c2e_medium.png\" alt=\"\" /></p>\r\n<p>&nbsp;</p>\r\n<p>Administrator can apply hooks for filtering the content of comments:<br style=\"padding: 0px; margin: 0px;\" />Below is two examples which was built already in TomatoCMS. The first one replaces special characters with emotion icons as follow:</p>\r\n<p><img src=\"http://demo.tomatocms.com/upload/news/admin/2010/03/4b9db977db016_medium.png\" alt=\"\" /></p>\r\n<p>&nbsp;</p>\r\n<p>And the second one formats comment in pre-defined programming language style which is very useful for programmers\' blogs</p>\r\n<p>&nbsp;</p>\r\n<p><img src=\"http://demo.tomatocms.com/upload/news/admin/2010/03/4b9db978f2b3d_medium.png\" alt=\"\" /></p>\r\n<p>&nbsp;</p>\r\n<p>- Prevents spams<br style=\"padding: 0px; margin: 0px;\" />At this version, we made an attempt at preventing spams by using the service provided by Akismet.<br style=\"padding: 0px; margin: 0px;\" />To use this, you have to register an free Akismet API key.</p>\r\n<p>&nbsp;</p>\r\n<h2>3) Update informer</h2>\r\n<p>In backend, user will receive the message that informs new version is available if any.</p>\r\n<p>&nbsp;</p>\r\n<p><img src=\"http://demo.tomatocms.com/upload/news/admin/2010/03/4b9db97d5f4d8_medium.png\" alt=\"\" /></p>\r\n<p>&nbsp;</p>\r\n<h1>Bugs fixed</h1>\r\n<p>This version fix many bugs from 2.0 version:</p>\r\n<p>- #0000001: Vote widget: can choose multiple options</p>\r\n<p>- #0000002: Published date in RSS output is incorrect</p>\r\n<p>- #0000003: Generates RSS links incorrectly</p>\r\n<p>- #0000005: player widget do not play the video clip which was inserted embed code</p>\r\n<p>- #0000006: Remove unnecessary folders (data, test)</p>\r\n<p>- #0000008: Change default timezone in application configuration file to Europe/London</p>\r\n<p>- #0000009: Remove superfish widget from menu module</p>\r\n<p>- #0000010: Improve iTomato widget</p>\r\n<p>- #0000011: Remove unnecessary libraries</p>\r\n<p>- #0000012:&nbsp;Add DTD format for hook, module, widget configuration file</p>\r\n<p>- #0000013: Allows user to add &lt;pre&gt; tag in content of TinyMCE Editor</p>\r\n<p>- #0000015: Layout Editor: Make some improvements</p>\r\n<p>- #0000016: Show number of views of articles in category page, details page, latesthome widget</p>\r\n<p>- #0000017: TwitterUpdate widget: Make all links in content are clickable</p>\r\n<p>- #0000018: Informs user if there is newer version</p>\r\n<p>- #0000019: Improve usability of some widgets which use cycle library</p>\r\n<p>- #0000020: Make install wizard</p>\r\n<p>- #0000021: latesthome widget: Throw an exception if there is no category</p>\r\n<p>- #0000023: Add syntaxhighlighter hook: Highlight code</p>\r\n<p>- #0000025: Improve comment system</p>\r\n<p>- #0000026: Don\'t use short_open_tag to render the output</p>\r\n<p>- #0000027: Convert all links to full path</p>\r\n<p>- #0000028: clipplayer widget do not work correctly</p>\r\n<p>- #0000029: In app settings, rename static.server to static_server</p>\r\n<p>- #0000030: User can goto the admin without logging in if user install to URL of&nbsp;<a href=\"http://domain/tomatocms/\">http://domain/tomatocms/</a></p>\r\n<p>- #0000031: Layout Editor does not work correctly if user install to URL of&nbsp;<a href=\"http://domain/tomatocms/\">http://domain/tomatocms/</a></p>\r\n<p>- #0000032: Exits the app if the version of PHP does not meet the requirements</p>\r\n<p>- #0000033: Remove chanel_link in module configuration, use url from app configuration</p>\r\n<p>- #0000034: Could not load layout when select other template</p>\r\n<p>- #0000035: Could not get the value of hook parameter</p>\r\n<p>- #0000036: Could not load the layout if user install TomatoCMS in subdirectory of web root</p>\r\n<p>- #0000037: Still received the message that informs the new version is available although it\'s latest version already</p>\r\n<p>- #0000053: Change generate thumnail image library</p>','Nguyen Huu Phuoc','{\"image\"}','http://demo.tomatocms.com/upload/news/admin/2010/03/4b9de407cdfcd_square.png',NULL,'http://demo.tomatocms.com/upload/news/admin/2010/03/4b9de407cdfcd_small.png','http://demo.tomatocms.com/upload/news/admin/2010/03/4b9de407cdfcd_general.png','http://demo.tomatocms.com/upload/news/admin/2010/03/4b9de407cdfcd_crop.png','http://demo.tomatocms.com/upload/news/admin/2010/03/4b9de407cdfcd_medium.png','http://demo.tomatocms.com/upload/news/admin/2010/03/4b9de407cdfcd_large.png','active',123,'2010-03-15 07:39:23',1,'admin',NULL,NULL,NULL,1,'admin','2010-03-15 07:39:27',0,0,0,0,NULL,1),(3,1,'January, 29th, 2010: TomatoCMS 2.0.2 released','','january-29th-2010-tomatocms-2-0-2-released','<p>TomatoCMS 2.0.2 released on Jan 29th, 2010. This version comes with Tag system, Front-end section for multimedia module, Menu builder and ability of customizing URLs</p>','<h1>What\'s new?</h1>\r\n<h2>1) Tag system</h2>\r\n<p>User can tag not only the articles but also photos, sets:</p>\r\n<p><img src=\"http://demo.tomatocms.com/upload/news/admin/2010/03/4b9dc038cc931_medium.png\" alt=\"\" /></p>\r\n<p>&nbsp;</p>\r\n<p>User can manage tags via back-end:</p>\r\n<p><img src=\"http://demo.tomatocms.com/upload/news/admin/2010/03/4b9dc039f174a_medium.png\" alt=\"\" /></p>\r\n<p>&nbsp;</p>\r\n<p>In addition, this version provide two widgets for tagging.<br style=\"padding: 0px; margin: 0px;\" />The first one named&nbsp;<span style=\"font-weight: bold; padding: 0px; margin: 0px;\">tag</span> show list of tags for given item (item here can be article, photo or photo set):</p>\r\n<p><img src=\"http://demo.tomatocms.com/upload/news/admin/2010/03/4b9dc03be3142_medium.png\" alt=\"\" /></p>\r\n<p>&nbsp;</p>\r\n<p>and the second one is&nbsp;<span style=\"font-weight: bold; padding: 0px; margin: 0px;\">Tag Cloud</span> which show list of tags:</p>\r\n<p><img src=\"http://demo.tomatocms.com/upload/news/admin/2010/03/4b9dc03d046ff_medium.png\" alt=\"\" /></p>\r\n<p>&nbsp;</p>\r\n<h2>2) Front-end for multimedia module</h2>\r\n<p>Now, you can view photo, clip on frontend section.<br style=\"padding: 0px; margin: 0px;\" />(Try to click on photo in slideshow on homepage):</p>\r\n<p><img src=\"http://demo.tomatocms.com/upload/news/admin/2010/03/4b9dc0363a4c4_medium.png\" alt=\"\" /></p>\r\n<p>&nbsp;</p>\r\n<p>There are new widgets in this version which are:<br style=\"padding: 0px; margin: 0px;\" />- filesets: Show list of set that current photo/clip belongs to:</p>\r\n<p><img src=\"http://demo.tomatocms.com/upload/news/admin/2010/03/4b9dc03497897_medium.png\" alt=\"\" /></p>\r\n<p>&nbsp;</p>\r\n<p>- latestsets: Show the latest sets</p>\r\n<p><img src=\"http://demo.tomatocms.com/upload/news/admin/2010/03/4b9dc03554689_medium.png\" alt=\"\" /></p>\r\n<p>&nbsp;</p>\r\n<p>Beside this, we improved backend strongly for more friendly:</p>\r\n<p>&nbsp;</p>\r\n<h2>3) Menu builder</h2>\r\n<p>The menu is most important component of website. In this version, we released an initial version for menu module</p>\r\n<p><img src=\"http://demo.tomatocms.com/upload/news/admin/2010/03/4b9dc03106779_medium.png\" alt=\"\" /></p>\r\n<p>&nbsp;</p>\r\n<h2>4) Ability of customizing URLs</h2>\r\n<p>In 2.0.1 version and earlier, the default URL of article is&nbsp;<a class=\"postlink\" style=\"outline-style: none; outline-width: initial; outline-color: initial; text-decoration: none; color: #105289; border-bottom-width: 1px; border-bottom-style: solid; border-bottom-color: #368ad2; padding: 0px; margin: 0px;\" href=\"http://site.com/news/article/view/1/1/\">http://site.com/news/article/view/1/1/</a><br style=\"padding: 0px; margin: 0px;\" />Now, with 2.0.2 version, developer can customize URLs of article, category, photo pages.<br style=\"padding: 0px; margin: 0px;\" />This is better for SEO.<br style=\"padding: 0px; margin: 0px;\" /><br style=\"padding: 0px; margin: 0px;\" />In following figure, I configured article page\'s URL to format of&nbsp;<br style=\"padding: 0px; margin: 0px;\" />/news/article/view/<span style=\"font-weight: bold; padding: 0px; margin: 0px;\">ID_Of_Category</span>/<span style=\"font-weight: bold; padding: 0px; margin: 0px;\">ID_Of_Article</span>-<span style=\"font-weight: bold; padding: 0px; margin: 0px;\">Slug_Of_Article</span>.html</p>\r\n<p>&nbsp;</p>\r\n<p><img src=\"http://demo.tomatocms.com/upload/news/admin/2010/03/4b9dc02f9e0f2_medium.png\" alt=\"\" /></p>\r\n<p>&nbsp;</p>\r\n<p>Currently, developers have to configure it in route files manually.</p>\r\n<p>&nbsp;</p>\r\n<h1>Bugs fixed</h1>\r\n<p>This version fixed bugs from previous version.</p>\r\n<p>- #0000022: Add searchbox widget</p>\r\n<p>- #0000039: Layout Editor does not load the styles of widget if user preview widget</p>\r\n<p>- #0000040: Layout Editor has error when edit layout of homepage</p>\r\n<p>- #0000041: Comment widget redirect to url admin/core/page/layout/default/news_article when click preview button</p>\r\n<p>- #0000042: Older widget in news module has error</p>\r\n<p>- #0000043: SkinSelector widget has error</p>\r\n<p>- #0000047: Add DTD format for template information file</p>\r\n<p>- #0000048: Could not preview layout if all widgets are in the initial status</p>','Nguyen Huu Phuoc','{\"image\"}','http://demo.tomatocms.com/upload/news/admin/2010/03/4b9de4a549622_square.png',NULL,'http://demo.tomatocms.com/upload/news/admin/2010/03/4b9de4a549622_small.png','http://demo.tomatocms.com/upload/news/admin/2010/03/4b9de4a549622_general.png','http://demo.tomatocms.com/upload/news/admin/2010/03/4b9de4a549622_crop.png','http://demo.tomatocms.com/upload/news/admin/2010/03/4b9de4a549622_medium.png','http://demo.tomatocms.com/upload/news/admin/2010/03/4b9de4a549622_large.png','active',142,'2010-03-15 07:42:04',1,'admin',NULL,NULL,NULL,1,'admin','2010-03-15 07:42:09',0,0,0,0,NULL,1),(4,1,'March, 1st, 2010: TomatoCMS 2.0.3 released','','march-1st-2010-tomatocms-2-0-3-released','<p>TomatoCMS 2.0.3 version released on 1st, March, 2010. This version improved core module: support hook in module level; support database prefix. It used more Zend Framework components. This version also supports RTL language in both front-end and back-end sections.</p>','<h1>What\'s new?</h1>\r\n<h2>1) Use more Zend Framework components</h2>\r\n<p>- &nbsp;Remove PEAR JSON Service from Twitter widget and use Zend_Json</p>\r\n<p>- Use Zend_Paginator and Zend_View_Helper_PaginationControl to render the paginator instead of PEAR_Pager</p>\r\n<p>- Use Zend_Application to create and bootstrap the application</p>\r\n<p>- Fixed to work with Zend Framework 1.10.0.<br />Also, Zend Framework 1.10.0 is attached in download package.</p>\r\n<p>&nbsp;</p>\r\n<h2>2) Improve core module</h2>\r\n<p>- Support database prefix. You can set it when install TomatoCMS.</p>\r\n<p>Now, it\'s more easy to translate language data in modules and widgets without caring about current module, widget.</p>\r\n<p>&nbsp;</p>\r\n<p>In modules:<br />BEFORE:</p>\r\n<pre class=\"brush: php\">&lt;?php $this-&gt;translates[\'news\']-&gt;_(\'article_add_page_title\'); ?&gt;</pre>\r\n<p>NOW:</p>\r\n<pre class=\"brush: php\">&lt;?php $this-&gt;translator(\'article_add_page_title\'); ?&gt;</pre>\r\n<p>&nbsp;</p>\r\n<p>In widgets:<br />BEFORE:</p>\r\n<pre class=\"brush: php\">&lt;?php $this-&gt;translates[\'news_mostviewed\']-&gt;_(\'most_viewed\'); ?&gt;</pre>\r\n<p>NOW:</p>\r\n<pre class=\"brush: php\">&lt;?php $this-&gt;translator()-&gt;widget(\'most_viewed\'); ?&gt;</pre>\r\n<p>- With 2.0.3 version, you can install and apply hook in module level.<br />Install hook:</p>\r\n<p><img src=\"http://demo.tomatocms.com/upload/news/admin/2010/03/4b9dd50066196_medium.png\" alt=\"\" /></p>\r\n<p>&nbsp;</p>\r\n<p>Apply hook:</p>\r\n<p><img src=\"http://demo.tomatocms.com/upload/news/admin/2010/03/4b9dd4fcc8aea_medium.png\" alt=\"\" /></p>\r\n<p>&nbsp;</p>\r\n<h2>3) New SEO module</h2>\r\n<p>This 2.0.3 version built new SEO module which provide SEO utilities.<br />Currently, it comes with</p>\r\n<p>- New hook named&nbsp;<span style=\"font-weight: bold; padding: 0px; margin: 0px;\">gatracker</span> that allows you to insert Google Analytic tracker code.</p>\r\n<p><img src=\"http://demo.tomatocms.com/upload/news/admin/2010/03/4b9dd4fe7f76b_medium.png\" alt=\"\" /></p>\r\n<p>&nbsp;</p>\r\n<p>- New widget named&nbsp;<span style=\"font-weight: bold; padding: 0px; margin: 0px;\">googler</span> that show configurable welcome message if user visit your site from Google\'s searching result</p>\r\n<p><img src=\"http://demo.tomatocms.com/upload/news/admin/2010/03/4b9dd4ff6692d_medium.png\" alt=\"\" /></p>\r\n<p>&nbsp;</p>\r\n<h2>4) Support RTL languages</h2>\r\n<p>2.0.3 version supports RTL languages as Arabic, Iranian, ... for both front-end and back-end sections.<br />In front-end:</p>\r\n<p><img src=\"http://demo.tomatocms.com/upload/news/admin/2010/03/4b9dd504dfbe9_medium.png\" alt=\"\" /></p>\r\n<p>&nbsp;</p>\r\n<p>and back-end:</p>\r\n<p><img src=\"http://demo.tomatocms.com/upload/news/admin/2010/03/4b9dd5039504e_medium.png\" alt=\"\" /></p>\r\n<p>&nbsp;</p>\r\n<p>&nbsp;</p>\r\n<h2>5) New widgets</h2>\r\n<p>There\'re two built-in widgets in this version.<br />- The first widget is&nbsp;<span style=\"font-weight: bold; padding: 0px; margin: 0px;\">Flickr</span> that show latest Flickr images from given account</p>\r\n<p><img src=\"http://demo.tomatocms.com/upload/news/admin/2010/03/4b9dd4fdb27a6_medium.png\" alt=\"\" /></p>\r\n<p>- The second one is&nbsp;<span style=\"font-weight: bold; padding: 0px; margin: 0px;\">TextResizer</span> that allows user to set the smaller or larger font size for pages</p>\r\n<p>&nbsp;</p>\r\n<h2>6) Offline mode</h2>\r\n<p>This version allows you to set website in offline mode</p>\r\n<p><img src=\"http://demo.tomatocms.com/upload/news/admin/2010/03/4b9dd50297b0d_medium.png\" alt=\"\" /></p>\r\n<p>&nbsp;</p>\r\n<p>You can configure this message when install using Install Wizard or in back-end</p>\r\n<p><img src=\"http://demo.tomatocms.com/upload/news/admin/2010/03/4b9dd50195b93_medium.png\" alt=\"\" /></p>\r\n<p>&nbsp;</p>\r\n<h1>Bugs fixed</h1>\r\n<p>This 2.0.3 version fixed various bugs which are listed as follow:</p>\r\n<p><br />- #0000049: Could not load result when make a vote for Vote widget<br />- #0000054: Don\'t work with ZF 1.10.0<br />- #0000055: Remove the temp files in root directory when generate thumbnails by GD<br />- #0000056: Allows user to add Google Analytic code to site<br />- #0000058: Could not uninstall zfdebug plugin<br />- #0000063: Generate random password for admin account after installing<br />- #0000061: There are errors if user don\'t connect to the Internet directly<br />- #0000065: Redirect to install page if user have just downloaded TomatoCMS and have not installed yet<br />- #0000066: Install Wizard: Execute most important queries to ensure the website will work without importing sample data<br />- #0000075: Show message when user want the site to be in offline mode<br />- #0000098: Front-end links are not correct, if user install in sub directory<br />- #0000104: At the final step of Install Wizard, show the link to backend<br />- #0000106: Could not update category<br />- #0000107: Convert entry point class of application to the one extending from Zend_Application_Bootstrap_Bootstrap (Includes App and Installer class)<br />- #0000108: Remove PEAR Services from lib<br />- #0000110: Use Zend_Paginator instead of PEAR Pager<br />- #0000111: Links in pager of page that lists comments in same thread are not correct if user install TomatoCMS in sub-directory<br />- #0000113: Rename twitterupdate widget to twitter<br />- #0000114: There is an error at comment widget section in article details page<br />- #0000117: Back-end menu does not work correctly when switch to blog template<br />- #0000118: At step 2 of Install Wizard, allows user to select date time format more easy<br />- #0000119: Create new view helper to translate<br />- #0000121: Create new plugin that disable magic quote and turn on this plugin during install if magic quote is on</p>','Nguyen Huu Phuoc','{\"image\"}','http://demo.tomatocms.com/upload/news/admin/2010/03/4b9de61fc8d6f_square.png',NULL,'http://demo.tomatocms.com/upload/news/admin/2010/03/4b9de61fc8d6f_small.png','http://demo.tomatocms.com/upload/news/admin/2010/03/4b9de61fc8d6f_general.png','http://demo.tomatocms.com/upload/news/admin/2010/03/4b9de61fc8d6f_crop.png','http://demo.tomatocms.com/upload/news/admin/2010/03/4b9de61fc8d6f_medium.png','http://demo.tomatocms.com/upload/news/admin/2010/03/4b9de61fc8d6f_large.png','active',91,'2010-03-15 07:48:07',1,'admin',NULL,NULL,NULL,1,'admin','2010-03-15 07:48:10',0,0,0,0,NULL,1),(5,1,'March, 2nd, 2010: TomatoCMS 2.0.3.1430 minor version released','','march-2nd-2010-tomatocms-2-0-3-1430-minor-version-released','<p>TomatoCMS 2.0.3.1430 minor version released.</p>','<p>This version fixed some critical bugs from 2.0.3 version.</p>\r\n<h1>Bugs fixed</h1>\r\n<p>- #0000059: Support RTL (right to left) interface: Fix front-end section on Chrome<br />- #0000142: After update article, the article lost its status<br />- #0000143: The Widget Paginator does not work in Layout Editor<br />- #0000144: Error if user access to module which has not been install<br />- #0000145: The vote widget: There\'s javascript error if poll is not found<br />- #0000147: Newer and Older widgets do not load articles as expected<br />- #0000149: Add title for images in Flickr widget<br />- #0000150: Don\'t create slug for article automatically<br />- #0000151: Error after installing zfdebug plugin<br />- #0000154: There\'s a JavaScript error when removing hook from targets<br />- #0000156: Suggest articles when typing in search box<br />- #0000159: User still can view the inactive article on front-end</p>','Nguyen Huu Phuoc','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',42,'2010-03-15 07:49:40',1,'admin',NULL,NULL,NULL,1,'admin','2010-03-15 07:49:45',0,0,0,0,NULL,1),(6,1,'March, 14th, 2010: TomatoCMS 2.0.3.1622 minor version released','','march-14th-2010-tomatocms-2-0-3-1622-minor-version-released','<p>This is final release for 2.0.3 branch. This minor version support session lifetime, debug mode and fixed most critical bugs.</p>','<h1>What\'s new?</h1>\r\n<h2>1) Support session lifetime</h2>\r\n<p>Now, users don\'t have to remove expired session manually. Users can set session lifetime (in seconds) in <span style=\"font-weight: bold; padding: 0px; margin: 0px;\">app.ini</span> (TomatoCMS_Installed_Folder/app/config/app.ini):</p>\r\n<pre class=\"brush: plain\">...\r\n[web]\r\nsession_lifetime = \"3600\"\r\n...\r\n</pre>\r\n<h2>2) Debug mode</h2>\r\n<p>This version allows developer to set the website in debug mode by setting following option in <span style=\"font-weight: bold; padding: 0px; margin: 0px;\">app.ini</span></p>\r\n<pre class=\"brush: plain\">...\r\n[web]\r\ndebug = \"true\"\r\n...\r\n</pre>\r\n<h1>Bugs fixed</h1>\r\n<p>This minor version fixed bugs from previous version:<br /><br />- #0000141: When install in sub-directory, thumbnails received invalid URLs after uploading<br />- #0000148: The article does not show image or clip icon although it was set in back-end<br />- #0000161: Could not load menu at the front-end due to javascript error<br />- #0000162: Error when apply hooks which its type do not match target<br />- #0000165: Could not generate thumbnails after uploading png images<br />- #0000167: Fix UI of banners listing page if there\'s flash banner<br />- #0000168: After updating banner, the banner zones are swapped<br />- #0000169: Not found error after creating config without selecting group<br />- #0000171: Could not install if set the database prefix empty<br />- #0000172: Javascript error occurred in adding/updating template page<br />- #0000174: Could not comment in front-end if install TomatoCMS in sub-directory<br />- #0000175: In back-end, the date of comments don\'t display correctly<br />- #0000176: Show the message after saving order of pages<br />- #0000177: Improve Translator widget and remove the big loop in Init plugin that initialize language data<br />- #0000178: Could not load banners if user installed TomatoCMS in sub-directory<br />- #0000179: Could not load new created static page<br />- #0000180: Countdown widget: Allows user to select the date time from select boxes instead of inputing to textbox<br />- #0000181: Improve editing/adding page for template<br />- #0000182: Update layout route file (layout.ini) after adding/updating/deleting page<br />- #0000183: When install in sub-directory, tracking link of banner is invalid<br />- #0000185: Fix UI errors on IE<br />- #0000189: The suggestions of search box limit the search results<br />- #0000190: In front-end, hide paginators when data is empty<br />- #0000191: stickycategory widget: The last slide is empty<br />- #0000192: Could not reply comments in front-end<br />- #0000193: comment widget: The link of avatar is not correct<br />- #0000196: In updating menu page, the administrator menu is flicker<br />- #0000197: When install in sub-directory, RSS link in head section of category page is not correct<br />- #0000106: Add session lifetime setting: Destroy session after given inactivate seconds<br />- #0000208: After installing, there are not available widgets to select in Layout Editor</p>\r\n<h1><strong>Upgrade to this version</strong></h1>\r\n<p>If your site is running an older TomatoCMS and want to upgrade to this version, you have to execute following SQL queries (via phpMyAdmin or any MySQL client tool):</p>\r\n<pre class=\"brush: sql\">ALTER TABLE `t_core_session` DROP COLUMN `access_time`;\r\nALTER TABLE `t_core_session` ADD `modified` INT;\r\nALTER TABLE `t_core_session` ADD `lifetime` INT;\r\n</pre>','Nguyen Huu Phuoc','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',94,'2010-03-15 07:51:02',1,'admin',NULL,NULL,NULL,1,'admin','2010-03-15 07:51:07',0,0,0,0,NULL,1),(7,6,'Guide on creating new template - Part 1: Template and skin directories','','guide-on-creating-new-template-part-1-template-and-skin-directories','<p><span>In this series, I will show you how to create a new template for TomatoCMS.&nbsp;Thank&nbsp;<span style=\"font-weight: bold;\">sliver163</span>&nbsp;who sent me this template which was already done in HTML and CSS formats.</span></p>','<p>This probably is long waiting post. In this guide, I will show you how to create a new template which looks like following screenshot:</p>\r\n<p> </p>\r\n<p><img src=\"http://demo.tomatocms.com/upload/news/admin/2010/03/4b9df63fe06bd_medium.png\" alt=\"\" /></p>\r\n<p> </p>\r\n<p>The template and skin directories are <strong>app/templates</strong> and <strong>skin</strong> respectively. Each template can have multiple skin which allows your website to switch between skins.<br style=\"padding: 0px; margin: 0px;\" />Follow is the structure of template and skin directories:</p>\r\n<pre class=\"brush: plain\">TomatoCMS_Root_Dir\r\n|___app\r\n|   |___templates\r\n|   |   |___blog\r\n|   |   |___default\r\n|___skin\r\n   |___blog\r\n   |   |___default\r\n   |      |___default.css\r\n   |___default\r\n      |___default\r\n         |___default.css\r\n</pre>\r\n<p>In other words, the general directory is:</p>\r\n<pre class=\"brush: plain\">TomatoCMS_Root_Dir\r\n|___app\r\n|   |___templates\r\n|   |   |___NameOfTemplate\r\n|___skin\r\n   |___NameOfTemplate\r\n      |___NameOfSkin\r\n      |   |___default.css\r\n      |___OtherSkin\r\n         |___default.css\r\n</pre>\r\n<p>I assume that we\'ll create new template named <span style=\"font-weight: bold; padding: 0px; margin: 0px;\">sample</span> and associated skin named <span style=\"font-weight: bold; padding: 0px; margin: 0px;\">plus</span>.<br style=\"padding: 0px; margin: 0px;\" />Let\'s create its directories:</p>\r\n<pre class=\"brush: plain\">TomatoCMS_Root_Dir\r\n|___app\r\n|   |___templates\r\n|   |   |___sample\r\n|___skin\r\n   |___sample\r\n      |___plus\r\n         |___default.css\r\n</pre>\r\n<p><span style=\"font-weight: bold;\">default.css</span> file in <span style=\"font-weight: bold;\">app/skin/sample/plus</span> is used to define skin styles</p>\r\n<p>(To be continued ...)</p>','Nguyen Huu Phuoc','','http://demo.tomatocms.com/upload/news/admin/2010/03/4b9df63fe06bd_square.png',NULL,'http://demo.tomatocms.com/upload/news/admin/2010/03/4b9df63fe06bd_small.png','http://demo.tomatocms.com/upload/news/admin/2010/03/4b9df63fe06bd_general.png','http://demo.tomatocms.com/upload/news/admin/2010/03/4b9df63fe06bd_crop.png','http://demo.tomatocms.com/upload/news/admin/2010/03/4b9df63fe06bd_medium.png','http://demo.tomatocms.com/upload/news/admin/2010/03/4b9df63fe06bd_large.png','active',177,'2010-03-15 08:57:57',1,'admin',NULL,NULL,NULL,1,'admin','2010-03-15 08:58:01',0,0,0,0,NULL,1),(8,6,'Guide on creating new template - Part 2: Switch to new template and skin','','guide-on-creating-new-template-part-2-switch-to-new-template-and-skin','<p>It\'s simple to switch to new template and skin.</p>','<p>Open the <strong>app/config/app.ini</strong> file and set the template and skin for our website</p>\r\n<pre class=\"brush: plain\">...\r\ntemplate = \"sample\"\r\nskin = \"plus\"\r\n...\r\n</pre>\r\n<p>(To be continued...)</p>','Nguyen Huu Phuoc','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',70,'2010-03-15 09:04:25',1,'admin',NULL,NULL,NULL,1,'admin','2010-03-15 09:04:57',0,0,0,0,NULL,1),(9,6,'Guide on creating new template - Part 3: Layout of template','','guide-on-creating-new-template-part-3-layout-of-template','<p>In this post, we will find out how to create layout template supposed to apply for all web pages. The simple template should include header, main content and footer.</p>','<p>The layout of templates are defined in layouts directory. Lets create this directory in app/templates/sample directory.Our template consist of three parts: header, footer and content as follow:</p>\r\n<p><img src=\"http://demo.tomatocms.com/upload/news/admin/2010/03/4b9dfa2a2c3f2_medium.png\" alt=\"\" /></p>\r\n<p>Look back to the layouts directory of default template (app/templates/default/layouts), you\'ll see four layout files:</p>\r\n<pre class=\"brush: plain\">TomatoCMS_Root_Dir\r\n|___app\r\n   |___templates\r\n      |___default\r\n         |___layouts\r\n            |___admin.phtml\r\n            |___auth.phtml\r\n            |___default.phtml\r\n            |___install.phtml\r\n</pre>\r\n<p>As these layout names, four files was used to define the layout for</p>\r\n<p>- Administrator pages (admin.phtml)</p>\r\n<p>- Authenticate pages (login, logout) (auth.phtml)</p>\r\n<p>- Default pages, i.e front-end section (default.phtml)</p>\r\n<p>- Install Wizard (install.phtml)</p>\r\n<p> </p>\r\n<p>So, in sample template, we also have to create these layouts.</p>\r\n<p>For purpose of this post, I create file <strong>default.phtml</strong> which defines layout for all front-end pages.</p>\r\n<p>Below is content of <strong>app/templates/sample/layout/default.phtml</strong>:</p>\r\n<pre class=\"brush: php\">&lt;!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\"&gt;\r\n&lt;html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\"&gt;\r\n&lt;head&gt;\r\n   &lt;meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" /&gt;\r\n   &lt;meta name=\"robots\" content=\"index, follow\" /&gt;   \r\n   &lt;link rel=\"stylesheet\" type=\"text/css\" href=\"&lt;?php echo $this-&gt;APP_STATIC_SERVER; ?&gt;/css/960/all.min.css\" /&gt;\r\n   &lt;link rel=\"stylesheet\" type=\"text/css\" href=\"&lt;?php echo $this-&gt;APP_STATIC_SERVER; ?&gt;/skin/&lt;?php echo $this-&gt;APP_TEMPLATE; ?&gt;/&lt;?php echo $this-&gt;APP_SKIN; ?&gt;/default.css\" /&gt;\r\n   &lt;script type=\"text/javascript\" src=\"&lt;?php echo $this-&gt;APP_STATIC_SERVER; ?&gt;/js/jquery/jquery-1.3.2.min.js\"&gt;&lt;/script&gt;\r\n   &lt;script type=\"text/javascript\" src=\"&lt;?php echo $this-&gt;APP_STATIC_SERVER; ?&gt;/js/jquery_plugins/jquery.ajaxq.min.js\"&gt;&lt;/script&gt;\r\n   &lt;script type=\"text/javascript\" src=\"&lt;?php echo $this-&gt;APP_STATIC_SERVER; ?&gt;/js/jquery_plugins/jquery.json-1.3.min.js\"&gt;&lt;/script&gt;\r\n   &lt;script type=\"text/javascript\" src=\"&lt;?php echo $this-&gt;APP_STATIC_SERVER; ?&gt;/js/tomato/namespace.js\"&gt;&lt;/script&gt;\r\n   &lt;script type=\"text/javascript\" src=\"&lt;?php echo $this-&gt;APP_STATIC_SERVER; ?&gt;/js/tomato/core/widget.loader.js\"&gt;&lt;/script&gt;\r\n   &lt;?php echo $this-&gt;headMeta(); ?&gt;\r\n   &lt;?php echo $this-&gt;headTitle(); ?&gt;\r\n   &lt;?php echo $this-&gt;headScript(); ?&gt;\r\n   &lt;?php echo $this-&gt;headLink(); ?&gt;\r\n   &lt;script type=\"text/javascript\"&gt;\r\n   Tomato.Core.Widget.Loader.baseUrl = \'&lt;?php echo $this-&gt;APP_URL; ?&gt;\';\r\n   &lt;/script&gt;\r\n&lt;/head&gt;\r\n&lt;body&gt;\r\n   &lt;div&gt;\r\n      &lt;div id=\"header\"&gt;\r\n         &lt;div class=\"container_12\"&gt;\r\n            &lt;?php echo $this-&gt;render(\'_header.phtml\'); ?&gt;\r\n         &lt;/div&gt;\r\n      &lt;/div&gt;\r\n   \r\n      &lt;div&gt;\r\n         &lt;?php echo $this-&gt;layoutLoader(); ?&gt;\r\n      &lt;/div&gt;\r\n      \r\n      &lt;div id=\"footer\" class=\"clearfix\"&gt;\r\n         &lt;div class=\"container_12\"&gt;\r\n            &lt;?php echo $this-&gt;render(\'_footer.phtml\'); ?&gt;\r\n         &lt;/div&gt;\r\n      &lt;/div&gt;\r\n   &lt;/div&gt;\r\n&lt;/body&gt;\r\n&lt;/html&gt;\r\n</pre>\r\n<p>Let me explain some lines:</p>\r\n<p>- TomatoCMS use 960 Grid System framework to layout pages, hence I add style sheets defined by the framework:</p>\r\n<pre class=\"brush: php\">&lt;link rel=\"stylesheet\" type=\"text/css\" href=\"&lt;?php echo $this-&gt;APP_STATIC_SERVER; ?&gt;/css/960/all.min.css\" /&gt;\r\n</pre>\r\n<p>- The next line defines the skin used for the layout:</p>\r\n<pre class=\"brush: php\">&lt;link rel=\"stylesheet\" type=\"text/css\" href=\"&lt;?php echo $this-&gt;APP_STATIC_SERVER; ?&gt;/skin/&lt;?php echo $this-&gt;APP_TEMPLATE; ?&gt;/&lt;?php echo $this-&gt;APP_SKIN; ?&gt;/default.css\" /&gt;</pre>\r\n<p>- Next lines defines JavaScript used by TomatoCMS, don\'t change it:</p>\r\n<pre class=\"brush: php\">&lt;script type=\"text/javascript\" src=\"&lt;?php echo $this-&gt;APP_STATIC_SERVER; ?&gt;/js/jquery/jquery-1.3.2.min.js\"&gt;&lt;/script&gt;\r\n&lt;script type=\"text/javascript\" src=\"&lt;?php echo $this-&gt;APP_STATIC_SERVER; ?&gt;/js/jquery_plugins/jquery.ajaxq.min.js\"&gt;&lt;/script&gt;\r\n&lt;script type=\"text/javascript\" src=\"&lt;?php echo $this-&gt;APP_STATIC_SERVER; ?&gt;/js/jquery_plugins/jquery.json-1.3.min.js\"&gt;&lt;/script&gt;\r\n&lt;script type=\"text/javascript\" src=\"&lt;?php echo $this-&gt;APP_STATIC_SERVER; ?&gt;/js/tomato/namespace.js\"&gt;&lt;/script&gt;\r\n&lt;script type=\"text/javascript\" src=\"&lt;?php echo $this-&gt;APP_STATIC_SERVER; ?&gt;/js/tomato/core/widget.loader.js\"&gt;&lt;/script&gt;\r\n</pre>\r\n<p>- Next line allows us to set the title, add link to other style sheets, javascript libraries or even add meta keyword for pages:</p>\r\n<pre class=\"brush: php\">&lt;?php echo $this-&gt;headMeta(); ?&gt;\r\n&lt;?php echo $this-&gt;headTitle(); ?&gt;\r\n&lt;?php echo $this-&gt;headScript(); ?&gt;\r\n&lt;?php echo $this-&gt;headLink(); ?&gt;\r\n</pre>\r\n<p>These functions was defined by Zend Framework. Don\'t worry about them, lets add these line to ensure our front-end work correctly.</p>\r\n<p>- Final line of head section define the base URL if you want to load widgets by Ajax. Don\'t worry about it, leave it as default template:</p>\r\n<pre class=\"brush: php\">&lt;script type=\"text/javascript\"&gt;\r\nTomato.Core.Widget.Loader.baseUrl = \'&lt;?php echo $this-&gt;APP_URL; ?&gt;\';\r\n&lt;/script&gt;\r\n</pre>\r\n<p>Also, I want you to notice about APP_STATIC_SERVER, APP_TEMPLATE, APP_SKIN, APP_URL constants. These constant was loaded by TomatoCMS and its values are defined in application configuration file (<strong>app/config/app.ini</strong>):</p>\r\n<pre class=\"brush: plain\">...\r\n[web]\r\nurl = \"http://localhost/tomatocms/index.php\" ==&gt; APP_URL (Base URL of your site)\r\nstatic_server = \"http://localhost/tomatocms\" ==&gt; APP_STATIC_SERVER (The URL that contain all static resources as CSS, JS)\r\ntemplate = \"sample\" ==&gt; APP_TEMPLATE (Template name)\r\nskin = \"plus\" ==&gt; APP_SKIN (Skin name)\r\n...\r\n</pre>\r\n<p>That\'s all details about head section.</p>\r\n<p>Next is body section of layout.</p>\r\n<p>As I said earlier, our pages will contain 3 parts which are header, footer and content. The header and footer sections should be used by all pages, so I use:</p>\r\n<pre class=\"brush: plain\">&lt;?php echo $this-&gt;render(\'_header.phtml\'); ?&gt;</pre>\r\n<p>and:</p>\r\n<pre class=\"brush: plain\">&lt;?php echo $this-&gt;render(\'_footer.phtml\'); ?&gt;</pre>\r\n<p>to render the content of header and footer, respectively.</p>\r\n<p>Off course, we have to create _header.phtml and _footer.phtml files in layouts directory first.</p>\r\n<p><strong>// app/templates/sample/layouts/_header.phtml</strong></p>\r\n<pre class=\"brush: html\">&lt;h1&gt;Header&lt;/h1&gt;</pre>\r\n<p>and:</p>\r\n<p><strong>// app/templates/sample/layouts/_footer.phtml</strong></p>\r\n<pre class=\"brush: html\">&lt;h1&gt;Footer&lt;/h1&gt;</pre>\r\n<p>The main content will be loaded by:</p>\r\n<pre class=\"brush: php\">&lt;?php echo $this-&gt;layoutLoader(); ?&gt;</pre>\r\n<p>You don\'t have to know details about it, just leave it as default template. TomatoCMS uses it to load layout and content of pages.</p>\r\n<p>Now, go to http://localhost/ to see what happens.</p>\r\n<p>Hey, I only see the blank page. Don\'t worry, this is because we have not created the XML files which define layout of pages.</p>\r\n<p>Let\'s read next post to resolve this problem.</p>\r\n<p>(To be continued...)</p>','Nguyen Huu Phuoc','','http://demo.tomatocms.com/upload/news/admin/2010/03/4b9dfa2a2c3f2_square.png',NULL,'http://demo.tomatocms.com/upload/news/admin/2010/03/4b9dfa2a2c3f2_small.png','http://demo.tomatocms.com/upload/news/admin/2010/03/4b9dfa2a2c3f2_general.png','http://demo.tomatocms.com/upload/news/admin/2010/03/4b9dfa2a2c3f2_crop.png','http://demo.tomatocms.com/upload/news/admin/2010/03/4b9dfa2a2c3f2_medium.png','http://demo.tomatocms.com/upload/news/admin/2010/03/4b9dfa2a2c3f2_large.png','active',141,'2010-03-15 09:36:16',1,'admin',NULL,NULL,NULL,1,'admin','2010-03-15 09:45:46',0,0,0,0,NULL,1),(10,6,'Guide on creating new template - Part 4: Use XML files to define layout of page','','guide-on-creating-new-template-part-4-use-xml-files-to-define-layout-of-page','<p>TomatoCMS uses XML files to configure the layout of page. In this post, I will show you how to configure layout for a page manually.</p>','<p>In default template, we created XML files which configure layout of all front-end pages.</p>\r\n<p> </p>\r\n<p>See the <strong>app/templates/default/layouts</strong> and you\'ll see 7 XML files:</p>\r\n<p>- <strong>home.xml</strong>: Configure layout of homepage as http://demo.tomatocms.com/</p>\r\n<p> </p>\r\n<p>- <strong>multimedia_file_details.xml</strong>: Configure layout of page that show details of photo, clip as http://demo.tomatocms.com/multimedia/file/details/63/</p>\r\n<p> </p>\r\n<p>- <strong>multimedia_set_details.xml</strong>: Configure layout of page that show details of photo, clip set as http://demo.tomatocms.com/multimedia/set/details/4/</p>\r\n<p> </p>\r\n<p>- <strong>news_article_category.xml</strong>: Configure layout of page that list of article in certain category as http://demo.tomatocms.com/news/category/view/1/</p>\r\n<p> </p>\r\n<p>- <strong>news_article_details.xml</strong>: Configure layout of page that show details of article as http://demo.tomatocms.com/news/article/view/1/1/</p>\r\n<p> </p>\r\n<p>- <strong>news_article_search.xml</strong>: Configure layout of page that show result of article searching as http://demo.tomatocms.com/news/search?q=ante</p>\r\n<p> </p>\r\n<p>- <strong>tag_tag_details.xml</strong>: Configure layout of page that list of items tagged by same tag as http://demo.tomatocms.com/tag/details/news_tag_article/4/</p>\r\n<p> </p>\r\n<p>Back to our sample template, create <strong>home.xml</strong> file in <strong>app/templates/sample/layouts</strong> to define the layout of homepage.</p>\r\n<p> </p>\r\n<p><strong>// app/templates/sample/layouts/home.xml</strong></p>\r\n<pre class=\"brush: xml\">&lt;?xml version=\"1.0\" encoding=\"UTF-8\"?&gt;\r\n&lt;!DOCTYPE layout SYSTEM \"http://schemas.tomatocms.com/dtd/layout.dtd\"&gt;\r\n&lt;layout&gt;\r\n&lt;/layout&gt;\r\n</pre>\r\n<p>Go to http://localhost/ and you\'ll see that our homepage loads header and footer successfully:</p>\r\n<p> </p>\r\n<p><img src=\"http://demo.tomatocms.com/upload/news/admin/2010/03/4b9e07a960ec1_medium.png\" alt=\"\" /></p>\r\n<p> </p>\r\n<p>With basic knowledge of HTML and CSS, you can easily insert static content and make styles for header and footer:</p>\r\n<div id=\"_mcePaste\" style=\"position: absolute; left: -10000px; top: 8px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;\">// app/templates/sample/layouts/_header.phtml:</div>\r\n<p>With basic knowledge of HTML and CSS, you can easily insert static content and make styles for header and footer:</p>\r\n<p><strong>// app/templates/sample/layouts/_header.phtml</strong></p>\r\n<pre class=\"brush: html\">&lt;div id=\"logo\"&gt;\r\n  &lt;h1&gt;&lt;a href=\"#\"&gt;+Plus&lt;/a&gt;&lt;/h1&gt;\r\n  &lt;p&gt;&lt;/p&gt;\r\n&lt;/div&gt;\r\n</pre>\r\n<p><strong>// app/templates/sample/layouts/_footer.phtml</strong></p>\r\n<pre class=\"brush: html\">&lt;p&gt;Copyright © 2010 - All Rights Reserved - &lt;a href=\"#\"&gt;Domain Name&lt;/a&gt;&lt;/p&gt;\r\n&lt;div class=\"clearfix\"&gt;&lt;/div&gt;\r\n</pre>\r\n<p><strong>// skin/sample/plus/default.css</strong></p>\r\n<p>Reset styles:</p>\r\n<pre class=\"brush: css\">body { \r\n  margin: 0; \r\n  padding: 0; \r\n  font-size: 13px; \r\n  font-family: verdana, Arial, Helvetica, sans-serif; \r\n  color: #333333; \r\n  background-color: #FFFFFF; \r\n}\r\nimg { \r\n  display: block; \r\n  margin: 0; \r\n  padding: 0; \r\n  border: none; \r\n}\r\na { \r\n  color: #B2C629; \r\n  background-color: #FFFFFF; \r\n  outline: none; \r\n  text-decoration: none; \r\n}\r\nh1 { \r\n  margin: 0; \r\n  padding: 0; \r\n  font-size: 20px; \r\n  font-weight: normal; \r\n  line-height: normal; \r\n  font-family: Georgia, \"Times New Roman\", Times, serif; \r\n}\r\n</pre>\r\n<p>Styles for header:</p>\r\n<pre class=\"brush: css\">#header { \r\n  height: 55px; \r\n  color: #CCCCCC; \r\n  background-color: #000000; \r\n  padding: 20px 0; \r\n}\r\n#header #logo { \r\n  display: block; \r\n  float: left; \r\n  width: 300px; \r\n  overflow: hidden; \r\n}\r\n#header #logo h1 { \r\n  margin: 0; \r\n  padding: 0; \r\n  line-height: normal; \r\n  font-size: 36px; \r\n  text-transform: uppercase; \r\n}\r\n#header h1 a { \r\n  color: #B2C629; \r\n  background-color: #000000; \r\n}\r\n</pre>\r\n<p>And styles for footer:</p>\r\n<pre class=\"brush: css\">#footer { \r\n  padding: 10px 0; \r\n}\r\n#footer p { \r\n  margin: 0; \r\n  padding: 0; \r\n}\r\n#footer, #footer a { \r\n  color: #333333; \r\n  background-color: #95AD19; \r\n}\r\n</pre>\r\n<p> </p>\r\n<p>Refresh our homepage (http://localhost) and you\'ll see result:</p>\r\n<p> </p>\r\n<p><img src=\"http://demo.tomatocms.com/upload/news/admin/2010/03/4b9e07a9c34eb_medium.png\" alt=\"\" /></p>\r\n<p>(To be continued ...)</p>','Nguyen Huu Phuoc','','http://demo.tomatocms.com/upload/news/admin/2010/03/4b9e07a960ec1_square.png',NULL,'http://demo.tomatocms.com/upload/news/admin/2010/03/4b9e07a960ec1_small.png','http://demo.tomatocms.com/upload/news/admin/2010/03/4b9e07a960ec1_general.png','http://demo.tomatocms.com/upload/news/admin/2010/03/4b9e07a960ec1_crop.png','http://demo.tomatocms.com/upload/news/admin/2010/03/4b9e07a960ec1_medium.png','http://demo.tomatocms.com/upload/news/admin/2010/03/4b9e07a960ec1_large.png','active',70,'2010-03-15 10:16:33',1,'admin',NULL,NULL,NULL,1,'admin','2010-03-15 10:16:38',0,0,0,0,NULL,1),(11,6,'Guide on creating new template - Part 5: Define the widgets','','guide-on-creating-new-template-part-5-define-the-widgets','<p>In previous post, you know that layout of each page is configured by XML file. In this guide, I will show you how to choose a widget and configure the widgets in this XML file.</p>','<p>TomatoCMS uses 960 grid system framework to layout the page.Basically, our webpage have width of 960 pixel and will be split into 12 or 16 columns (or even 24 columns which is not a version used by TomatoCMS at this time of writing this post). TomatoCMS uses 12 columns layout.Set of columns is called as container. A full row which consist of 12 columns is called as full-row container</p>\r\n<p> </p>\r\n<p>Now, image that the layout of homepage:</p>\r\n<p> </p>\r\n<p><img src=\"http://demo.tomatocms.com/upload/news/admin/2010/03/4b9e0ead2ba48_medium.png\" alt=\"\" /></p>\r\n<p> </p>\r\n<p>will be organized as follow:</p>\r\n<p> </p>\r\n<p><img src=\"http://demo.tomatocms.com/upload/news/admin/2010/03/4b9e0eae058c4_medium.png\" alt=\"\" /></p>\r\n<p><br />Or more clear:</p>\r\n<p> </p>\r\n<p><img src=\"http://demo.tomatocms.com/upload/news/admin/2010/03/4b9e0eaeceb60_medium.png\" alt=\"\" /><br /><br />First, change the content of default layout (default.phtml) to indicate that the main content will be in 12-columns container:</p>\r\n<p><strong>// app/templates/sample/layouts/default.phtml</strong></p>\r\n<p>BEFORE:</p>\r\n<pre class=\"brush: php\">...\r\n&lt;div&gt;\r\n   &lt;?php echo $this-&gt;layoutLoader(); ?&gt;\r\n   &lt;div class=\"clearfix\"&gt;&lt;/div&gt;\r\n&lt;/div&gt;\r\n...\r\n</pre>\r\n<p>AFTER:</p>\r\n<pre class=\"brush: php\">...\r\n&lt;div class=\"container_12\"&gt;\r\n   &lt;?php echo $this-&gt;layoutLoader(); ?&gt;\r\n   &lt;div class=\"clearfix\"&gt;&lt;/div&gt;\r\n&lt;/div&gt;\r\n...\r\n</pre>\r\n<p>Next, create containers in <strong>home.xml</strong>:</p>\r\n<p><strong>// app/templates/sample/layouts/home.xml</strong></p>\r\n<pre class=\"brush: xml\">&lt;?xml version=\"1.0\" encoding=\"UTF-8\"?&gt;\r\n&lt;!DOCTYPE layout SYSTEM \"http://schemas.tomatocms.com/dtd/layout.dtd\"&gt;\r\n&lt;layout&gt;\r\n  &lt;container cols=\"12\"&gt;\r\n  &lt;/container&gt;\r\n  &lt;container cols=\"12\"&gt;\r\n  &lt;/container&gt;\r\n  &lt;container cols=\"12\"&gt;\r\n    &lt;container cols=\"4\" position=\"first\"&gt;\r\n    &lt;/container&gt;\r\n    &lt;container cols=\"4\"&gt;\r\n    &lt;/container&gt;\r\n    &lt;container cols=\"4\" position=\"last\"&gt;\r\n    &lt;/container&gt;\r\n  &lt;/container&gt;\r\n&lt;/layout&gt;\r\n</pre>\r\n<p>In <strong>home.xml</strong> file above:</p>\r\n<p>- container tag to create new container. The cols property defines the number of columns in container</p>\r\n<p>- Containers can be nested. The final 12-columns container consist of three 4-columns containers.</p>\r\n<p>- If a container is first container in children of parent container, set <strong>position=\"first\"</strong>.And set <strong>position=\"last\"</strong> if you want container to be the last one in children of parent container.</p>\r\n<p> </p>\r\n<h2>Question is how to put sample HTML content to test our layout configurations?</h2>\r\n<p>Answer is use HTML widget from core module. This widget allows you to put static HTML content and show it on page. I will try to insert widget in the first 12-columns container:</p>\r\n<p><strong>// app/templates/sample/layouts/home.xml</strong></p>\r\n<pre class=\"brush: xml\">...\r\n&lt;layout&gt;\r\n  &lt;container cols=\"12\"&gt;\r\n    &lt;widget module=\"core\" name=\"html\"&gt;\r\n      &lt;params&gt;\r\n        &lt;param name=\"content\"&gt;\r\n          &lt;value&gt;&lt;![CDATA[Display slide of hotest aritcles]]&gt;&lt;/value&gt;\r\n        &lt;/param&gt;\r\n      &lt;/params&gt;\r\n    &lt;/widget&gt;\r\n  &lt;/container&gt;\r\n   ...\r\n&lt;/layout&gt;\r\n...\r\n</pre>\r\n<p>Here it comes with widget configuration:</p>\r\n<p>- Use widget tag to insert a widget</p>\r\n<p>- module and name properties define module and name of widgets</p>\r\n<p>- params list all params of widget- param tag define widget param. The name property define name of param</p>\r\n<p>- value tag define the value of param. You can use CDATA for value of param.</p>\r\n<p> </p>\r\n<p><strong>NOTES:</strong></p>\r\n<p>- Widgets come from module. Each module may have a sub-directory named widget that consist all of widgets belonging to module.</p>\r\n<p>- To view the list of widget\'s param, see the <strong>about.xml</strong> in widget directory.</p>\r\n<p>For example, you can see the full list params of html widget in <strong>app/modules/core/widgets/html/about.xml</strong> file.</p>\r\n<p>Refresh our homepage. Now, the result is as follow:</p>\r\n<p> </p>\r\n<p><img src=\"http://demo.tomatocms.com/upload/news/admin/2010/03/4b9e0eafb9761_medium.png\" alt=\"\" /></p>\r\n<p>I decide to use this widget to show the sample content of container. Also, I use HTML widget to show content of \"Company info\" and \"Contact info\" widget:</p>\r\n<p><strong>// app/templates/sample/layouts/home.xml</strong></p>\r\n<pre class=\"brush: xml\">&lt;?xml version=\"1.0\" encoding=\"UTF-8\"?&gt;\r\n&lt;!DOCTYPE layout SYSTEM \"http://schemas.tomatocms.com/dtd/layout.dtd\"&gt;\r\n&lt;layout&gt;\r\n  &lt;container cols=\"12\"&gt;\r\n    &lt;widget module=\"core\" name=\"html\"&gt;\r\n      &lt;params&gt;\r\n        &lt;param name=\"content\"&gt;\r\n          &lt;value&gt;&lt;![CDATA[Display slide of hotest aritcles]]&gt;&lt;/value&gt;\r\n        &lt;/param&gt;\r\n      &lt;/params&gt;\r\n    &lt;/widget&gt;\r\n  &lt;/container&gt;\r\n  &lt;container cols=\"12\"&gt;\r\n    &lt;widget module=\"core\" name=\"html\"&gt;\r\n      &lt;params&gt;\r\n        &lt;param name=\"content\"&gt;\r\n          &lt;value&gt;&lt;![CDATA[Display 3 newest articles]]&gt;&lt;/value&gt;\r\n        &lt;/param&gt;\r\n      &lt;/params&gt;\r\n    &lt;/widget&gt;\r\n  &lt;/container&gt;\r\n  &lt;container cols=\"12\"&gt;\r\n    &lt;container cols=\"4\" position=\"first\"&gt;\r\n      &lt;widget module=\"core\" name=\"html\"&gt;\r\n        &lt;params&gt;\r\n          &lt;param name=\"content\"&gt;\r\n            &lt;value&gt;&lt;![CDATA[\r\n&lt;div class=\"company\"&gt;\r\n   &lt;h2&gt;A Little Company Information !&lt;/h2&gt;\r\n   &lt;img class=\"company_logo\" src=\"/skin/sample/plus/images/demo/left.gif\" alt=\"\" /&gt;\r\n   &lt;p&gt;Morbitincidunt maurisque eros molest nunc anteget sed vel lacus mus semper. Anterdumnullam interdum eros dui urna consequam ac nisl nullam ligula vestassa. Condimentumfelis et amet tellent quisquet a leo lacus nec augue&lt;/p&gt;\r\n   &lt;p&gt;Portortornec condimenterdum eget consectetuer condis.&lt;/p&gt;\r\n&lt;/div&gt;                  \r\n]]&gt;&lt;/value&gt;\r\n          &lt;/param&gt;\r\n        &lt;/params&gt;\r\n      &lt;/widget&gt;\r\n    &lt;/container&gt;\r\n    &lt;container cols=\"4\"&gt;\r\n      &lt;widget module=\"core\" name=\"html\"&gt;\r\n        &lt;params&gt;\r\n          &lt;param name=\"content\"&gt;\r\n            &lt;value&gt;&lt;![CDATA[Show Flickr images]]&gt;&lt;/value&gt;\r\n          &lt;/param&gt;\r\n        &lt;/params&gt;\r\n      &lt;/widget&gt;\r\n    &lt;/container&gt;\r\n    &lt;container cols=\"4\" position=\"last\"&gt;\r\n      &lt;widget module=\"core\" name=\"html\"&gt;\r\n        &lt;params&gt;\r\n          &lt;param name=\"content\"&gt;\r\n            &lt;value&gt;&lt;![CDATA[\r\n&lt;div class=\"contact\"&gt;\r\n   &lt;h2&gt;Our Contact Details !&lt;/h2&gt;\r\n   &lt;ul&gt;\r\n      &lt;li&gt;Company Name&lt;/li&gt;\r\n        &lt;li&gt;Street Name &amp; Number&lt;/li&gt;\r\n        &lt;li&gt;Town&lt;/li&gt;\r\n        &lt;li&gt;Postcode/Zip&lt;/li&gt;\r\n        &lt;li&gt;Tel: xxxxx xxxxxxxxxx&lt;/li&gt;\r\n        &lt;li&gt;Fax: xxxxx xxxxxxxxxx&lt;/li&gt;\r\n        &lt;li&gt;Email: info@domain.com&lt;/li&gt;\r\n        &lt;li class=\"last\"&gt;LinkedIn: &lt;a href=\"#\"&gt;Company Profile&lt;/a&gt;&lt;/li&gt;\r\n   &lt;/ul&gt;\r\n&lt;/div&gt;                  \r\n]]&gt;&lt;/value&gt;\r\n          &lt;/param&gt;\r\n        &lt;/params&gt;\r\n      &lt;/widget&gt;\r\n    &lt;/container&gt;\r\n  &lt;/container&gt;\r\n&lt;/layout&gt;\r\n</pre>\r\n<p>Now, our homepage looks like:</p>\r\n<p> </p>\r\n<p><img src=\"http://demo.tomatocms.com/upload/news/admin/2010/03/4b9e0eb027382_medium.png\" alt=\"\" /></p>\r\n<p>Add some style sheets in our CSS for company and contact info:</p>\r\n<p><strong>// skin/sample/plus/default.css</strong></p>\r\n<pre class=\"brush: css\">.company a, .contact a { \r\n  color: #95AD19; \r\n  background-color: #000000; \r\n}\r\n.company ul, .contact ul { \r\n  margin: 0; \r\n  padding: 0; \r\n  list-style: none; \r\n}\r\n.company h2, .contact h2 { \r\n  margin: 0 0 15px 0; \r\n  padding: 0 0 8px 0; \r\n  font-size: 18px; \r\n  color: #CCCCCC; \r\n  background-color: #000000; \r\n  border-bottom: 1px dotted #CCCCCC; \r\n  font-family: Georgia,\"Times New Roman\",Times,serif; \r\n  font-weight: normal; \r\n  line-height: normal; \r\n}\r\n.company { \r\n  color: #FFFFFF; \r\n  background-color: #000000; \r\n  height: 310px; \r\n  line-height: 1.6em; \r\n}\r\n.company .company_logo { \r\n  float: left; \r\n  margin: 0 8px 8px 0; \r\n  clear: left; \r\n  border: 1px solid #CCCCCC; \r\n  padding: 5px; \r\n  color: #FFFFFF; \r\n  background-color: #000000; \r\n}\r\n.contact { \r\n  color: #FFFFFF; \r\n  background-color: #000000; \r\n  height: 310px; \r\n  padding: 0; \r\n}\r\n.contact li { \r\n  margin: 0; \r\n  padding: 0; \r\n}\r\n</pre>\r\n<p>The result will be:</p>\r\n<p> </p>\r\n<p><img src=\"http://demo.tomatocms.com/upload/news/admin/2010/03/4b9e0eb0b973e_medium.png\" alt=\"\" /></p>\r\n<p> </p>\r\n<h2>Add Flickr images</h2>\r\n<p>For simply, I also set HTML content to show some Flickr images:</p>\r\n<p><strong>// app/templates/sample/layouts/home.xml</strong></p>\r\n<p>BEFORE:</p>\r\n<pre class=\"brush: xml\">...\r\n&lt;widget module=\"core\" name=\"html\"&gt;\r\n  &lt;params&gt;\r\n    &lt;param name=\"content\"&gt;\r\n      &lt;value&gt;&lt;![CDATA[Show Flickr images]]&gt;&lt;/value&gt;\r\n    &lt;/param&gt;\r\n  &lt;/params&gt;\r\n&lt;/widget&gt;\r\n...\r\n</pre>\r\n<p>AFTER:</p>\r\n<pre class=\"brush: xml\">...\r\n&lt;widget module=\"core\" name=\"html\"&gt;\r\n  &lt;params&gt;\r\n    &lt;param name=\"content\"&gt;\r\n      &lt;value&gt;&lt;![CDATA[\r\n&lt;div class=\"flickr\"&gt;         \r\n   &lt;h2&gt;Flickr Images&lt;/h2&gt; \r\n   &lt;ul&gt; \r\n      &lt;li&gt;&lt;a href=\"http://www.flickr.com/photos/47962180@N04/4393483631/sizes/sq/\"&gt;&lt;img src=\"http://farm5.static.flickr.com/4042/4393483631_e65360ce1a_s.jpg\" /&gt;&lt;/a&gt;&lt;/li&gt; \r\n      &lt;li&gt;&lt;a href=\"http://www.flickr.com/photos/47962180@N04/4393483243/sizes/sq/\"&gt;&lt;img src=\"http://farm5.static.flickr.com/4040/4393483243_af2ec8b778_s.jpg\" /&gt;&lt;/a&gt;&lt;/li&gt; \r\n      &lt;li&gt;&lt;a href=\"http://www.flickr.com/photos/47962180@N04/4393482841/sizes/sq/\"&gt;&lt;img src=\"http://farm5.static.flickr.com/4037/4393482841_d34596aec7_s.jpg\" /&gt;&lt;/a&gt;&lt;/li&gt; \r\n      &lt;li&gt;&lt;a href=\"http://www.flickr.com/photos/47962180@N04/4393482353/sizes/sq/\"&gt;&lt;img src=\"http://farm5.static.flickr.com/4056/4393482353_284d433ec3_s.jpg\" /&gt;&lt;/a&gt;&lt;/li&gt; \r\n      &lt;li&gt;&lt;a href=\"http://www.flickr.com/photos/47962180@N04/4393481845/sizes/sq/\"&gt;&lt;img src=\"http://farm5.static.flickr.com/4013/4393481845_934d1b4638_s.jpg\" /&gt;&lt;/a&gt;&lt;/li&gt; \r\n      &lt;li&gt;&lt;a href=\"http://www.flickr.com/photos/47962180@N04/4394247530/sizes/sq/\"&gt;&lt;img src=\"http://farm5.static.flickr.com/4058/4394247530_6897bc8841_s.jpg\" /&gt;&lt;/a&gt;&lt;/li&gt; \r\n   &lt;/ul&gt;         \r\n   &lt;div class=\"clearfix\"&gt;&lt;/div&gt;\r\n&lt;/div&gt;\r\n]]&gt;&lt;/value&gt;\r\n    &lt;/param&gt;\r\n  &lt;/params&gt;\r\n&lt;/widget&gt;\r\n...\r\n</pre>\r\n<p>Refresh our homepage to see the result:</p>\r\n<p> </p>\r\n<p><img src=\"http://demo.tomatocms.com/upload/news/admin/2010/03/4b9e0eb13f9f7_medium.png\" alt=\"\" /></p>\r\n<p>Add styles for flickr div:</p>\r\n<p><strong>// skin/sample/plus/default.css</strong></p>\r\n<pre class=\"brush: css\">.flickr { \r\n  color: #FFFFFF; \r\n  background-color: #000000; \r\n  height: 310px; \r\n} \r\n.flickr a { \r\n  color: #95AD19; \r\n  background-color: #000000; \r\n}\r\n.flickr ul { \r\n  margin: 5px 0 0 0; \r\n  padding: 0; \r\n  list-style: none; \r\n}\r\n.flickr h2 { \r\n  margin: 0 0 15px 0; \r\n  padding: 0 0 8px 0; \r\n  font-size: 18px; \r\n  color: #CCCCCC; \r\n  background-color: #000000; \r\n  border-bottom: 1px dotted #CCCCCC; \r\n  font-family: Georgia,\"Times New Roman\",Times,serif; \r\n  font-weight: normal; \r\n  line-height: normal; \r\n}\r\n.flickr li { \r\n  float: left; \r\n  margin-left: 10px; \r\n  margin-bottom: 10px; \r\n}\r\n.flickr li img { \r\n  width: 60px; \r\n  height: 60px; \r\n}\r\n</pre>\r\n<p>At this time, our homepage looks like:</p>\r\n<p> </p>\r\n<p><img src=\"http://demo.tomatocms.com/upload/news/admin/2010/03/4b9e0eb1d2588_medium.png\" alt=\"\" /></p>\r\n<p>But there\'s a white space between \"Company\", \"Flickr\" and \"Contact\" containers.</p>\r\n<p>How to fix it?</p>\r\n<p>The space is a distinct between two consecutive div defined by 960 Grid System. The width of distinct is 20px. The tip here is change the style for <strong>.company</strong> and <strong>.flickr</strong>:</p>\r\n<p><strong>// skin/sample/plus/default.css</strong></p>\r\n<pre class=\"brush: css\">...\r\n.company { \r\n  ...\r\n  margin-right: -20px; \r\n} \r\n.flickr { \r\n  ...\r\n  margin-right: -20px; \r\n} \r\n</pre>\r\n<p>The result is better:</p>\r\n<p> </p>\r\n<p><img src=\"http://demo.tomatocms.com/upload/news/admin/2010/03/4b9e0eb2712c0_medium.png\" alt=\"\" /></p>\r\n<p> </p>\r\n<div id=\"_mcePaste\" style=\"position: absolute; left: -10000px; top: 5649px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;\">At this time, all content are static HTML.</div>\r\n<div id=\"_mcePaste\" style=\"position: absolute; left: -10000px; top: 5649px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;\">In the next post, I will guide you how to show the dynamic data taken from database in first and second 12-columns containers.</div>\r\n<p>At this time, all content are static HTML. In the next post, I will guide you how to show the dynamic data taken from database in first and second 12-columns containers.</p>\r\n<p>(To be continued ...)</p>','Nguyen Huu Phuoc','{\"image\"}','http://demo.tomatocms.com/upload/news/admin/2010/03/4b9e0f2e4ea18_square.png',NULL,'http://demo.tomatocms.com/upload/news/admin/2010/03/4b9e0f2e4ea18_small.png','http://demo.tomatocms.com/upload/news/admin/2010/03/4b9e0f2e4ea18_general.png','http://demo.tomatocms.com/upload/news/admin/2010/03/4b9e0f2e4ea18_crop.png','http://demo.tomatocms.com/upload/news/admin/2010/03/4b9e0f2e4ea18_medium.png','http://demo.tomatocms.com/upload/news/admin/2010/03/4b9e0f2e4ea18_large.png','active',161,'2010-03-15 10:42:29',1,'admin',NULL,NULL,NULL,1,'admin','2010-03-15 11:09:11',0,0,0,0,NULL,1),(12,6,'Guide on creating new template - Part 6: Cutomize widget output','','guide-on-creating-new-template-part-6-cutomize-widget-output','<p>In the previous post, you know how to use HTML widget from core module to show HTML content. Hene, it\'s similar to show dynamic data in first and second 12-columns containers for our template. In this post, I will show you how to customize the output of widget.</p>','<p>The first container show the hotest (popular) articles. You can use hotest widget from news module to do this. As I said in previous post, you should open widget information file (<strong>about.xml</strong>) in widget directory to get the full list of widget parameters.</p>\r\n<p> </p>\r\n<p>In this case, <strong>app/modules/news/widget/hotest/about.xml</strong> file lets you know that this widget have only one parameter named limit which defines limit number of hotest articles.</p>\r\n<p> </p>\r\n<p>So, I decide to use this widget. Let\'s insert this widget configuration to our <strong>home.xml</strong> file:</p>\r\n<p><strong>// app/templates/sample/layouts/home.xml</strong></p>\r\n<p>BEFORE:</p>\r\n<pre class=\"brush: xml\">...\r\n&lt;widget module=\"core\" name=\"html\"&gt;\r\n  &lt;params&gt;\r\n    &lt;param name=\"content\"&gt;\r\n      &lt;value&gt;&lt;![CDATA[Display slide of hotest aritcles]]&gt;&lt;/value&gt;\r\n    &lt;/param&gt;\r\n  &lt;/params&gt;\r\n&lt;/widget&gt;\r\n...\r\n</pre>\r\n<p>AFTER:</p>\r\n<pre class=\"brush: xml\">...\r\n&lt;widget name=\"hotest\" module=\"news\"&gt;\r\n  &lt;params&gt;\r\n    &lt;param name=\"limit\"&gt;\r\n      &lt;value&gt;&lt;![CDATA[3]]&gt;&lt;/value&gt;\r\n    &lt;/param&gt;\r\n  &lt;/params&gt;\r\n&lt;/widget&gt;\r\n...\r\n</pre>\r\n<p>(I want to make a slide that consist of 3 hotest articles, so I set 3 for value of limit parameter)</p>\r\n<p>Then, what will happens to our homepage?</p>\r\n<p> </p>\r\n<p><img src=\"http://demo.tomatocms.com/upload/news/admin/2010/03/4b9e5ca335b2f_medium.png\" alt=\"\" /></p>\r\n<p> </p>\r\n<p> </p>\r\n<h2>What is file TomatoCMS uses to show the output of widget?</h2>\r\n<p>In each widget directory, TomatoCMS will find show.phtml file and uses it to show the output of widget.</p>\r\n<p>In this case, <strong>app/modules/news/widget/hotest/show.phtml</strong> was used to show the output of widget.</p>\r\n<p> </p>\r\n<h2>How to customize the output of widget in new template?</h2>\r\n<p>Fortunately, TomatoCMS allows you to customize the default output of widget in current template.</p>\r\n<p>In template directory (<strong>app/templates/sample</strong>), create sub-directories, files as following structure:</p>\r\n<pre class=\"brush: plain\">TomatoCMS_Root_Dir\r\n|_app\r\n  |_templates\r\n    |_sample\r\n      |_layouts\r\n      |_views\r\n        |_news\r\n          |_widgets\r\n            |_hotest\r\n              |_show.phtml\r\n</pre>\r\n<p> </p>\r\n<p>In structure above:</p>\r\n<p>- <strong>views</strong> directory consist of default output of widgets and scripts</p>\r\n<p>- <strong>views/news</strong>: news is name of module</p>\r\n<p>- <strong>views/news/widgets</strong>: this directory consist of customized output of widgets belonging to news module</p>\r\n<p>- <strong>views/news/widgets/hotest</strong>: This directory consist of customized output of hotest widgets</p>\r\n<p> </p>\r\n<p>TomatoCMS will uses <strong>app/templates/sample/views/news/widgets/hotest/show.phtml</strong> to show the output of hotest widget.</p>\r\n<p> </p>\r\n<p><strong>// app/templates/sample/views/news/widgets/hotest/show.phtml</strong></p>\r\n<pre class=\"brush: html\">&lt;h1&gt;Output of hotest widgets goes here!&lt;/h1&gt;</pre>\r\n<p>Our homepage now looks like:</p>\r\n<p> </p>\r\n<p><img src=\"http://demo.tomatocms.com/upload/news/admin/2010/03/4b9e5ca53f70f_medium.png\" alt=\"\" /></p>\r\n<p> </p>\r\n<p>Now, open the default output of hotest widget (<strong>app/modules/news/widgets/hotest/show.phtml</strong>) and you will see the code that make of loop through the hotest articles:</p>\r\n<pre class=\"brush: php\">&lt;?php if ($this-&gt;articles != null) : ?&gt;\r\n  &lt;?php foreach ($this-&gt;articles as $index =&gt; $article) : ?&gt;\r\n  &lt;ul style=\"&lt;?php if ($index == 0) : ?&gt;dispaly: block&lt;?php else : ?&gt;display: none&lt;?php endif; ?&gt;\"&gt;\r\n    &lt;li&gt;\r\n      &lt;?php if ($article-&gt;image_crop != null) : ?&gt;&lt;a href=\"&lt;?php echo $this-&gt;url($article-&gt;getProperties(), \'news_article_details\'); ?&gt;\"&gt;&lt;img src=\"&lt;?php echo $article-&gt;image_crop; ?&gt;\" width=\"292px\" height=\"219px\" /&gt;&lt;/a&gt;&lt;?php endif; ?&gt;\r\n      &lt;h4&gt;&lt;a href=\"&lt;?php echo $this-&gt;url($article-&gt;getProperties(), \'news_article_details\'); ?&gt;\"&gt;&lt;?php echo $article-&gt;title; ?&gt;&lt;/a&gt;&lt;/h4&gt;\r\n      &lt;div&gt;&lt;?php echo $this-&gt;subString($article-&gt;description, 350); ?&gt;&lt;/div&gt;\r\n    &lt;/li&gt;\r\n  &lt;/ul&gt;\r\n  &lt;?php endforeach; ?&gt;\r\n&lt;?php endif; ?&gt;\r\n</pre>\r\n<p> </p>\r\n<p>Use this script and make some change to suit with our HTML output:</p>\r\n<p><strong>// app/templates/sample/views/news/widgets/hotest/show.phtml</strong></p>\r\n<p> </p>\r\n<pre class=\"brush: php\">&lt;link rel=\"stylesheet\" type=\"text/css\" href=\"/js/jquery_plugins/jcarousel/jquery.jcarousel.css\" /&gt;               \r\n&lt;script type=\"text/javascript\" src=\"/js/jquery_plugins/jcarousel/jquery.jcarousel.pack.js\"&gt;&lt;/script&gt;\r\n&lt;div class=\"t_new_hotest\"&gt;\r\n  &lt;div id=\"t_new_hotest_slide\"&gt;\r\n    &lt;div id=\"t_new_hotest_content\"&gt;\r\n      &lt;?php if ($this-&gt;articles != null) : ?&gt;\r\n      &lt;ul&gt;\r\n        &lt;?php foreach ($this-&gt;articles as $index =&gt; $article) : ?&gt;\r\n        &lt;li&gt;\r\n          &lt;?php if ($article-&gt;image_crop != null) : ?&gt;&lt;img src=\"&lt;?php echo $article-&gt;image_crop; ?&gt;\" /&gt;&lt;?php endif; ?&gt;\r\n          &lt;div&gt;\r\n            &lt;h2&gt;&lt;a href=\"&lt;?php echo $this-&gt;url($article-&gt;getProperties(), \'news_article_details\'); ?&gt;\"&gt;&lt;?php echo $article-&gt;title; ?&gt;&lt;/a&gt;&lt;/h2&gt;\r\n            &lt;p&gt;&lt;?php echo $this-&gt;subString($article-&gt;description, 300); ?&gt;&lt;/p&gt;\r\n            &lt;p class=\"t_new_hotest_more\"&gt;&lt;a href=\"&lt;?php echo $this-&gt;url($article-&gt;getProperties(), \'news_article_details\'); ?&gt;\"&gt;&lt;?php echo $this-&gt;translator()-&gt;widget(\'more\'); ?&gt;&lt;/a&gt;&lt;/p&gt;\r\n          &lt;/div&gt;\r\n        &lt;/li&gt;\r\n        &lt;?php endforeach; ?&gt;\r\n      &lt;/ul&gt;\r\n      &lt;?php endif; ?&gt;   \r\n    &lt;/div&gt;\r\n    &lt;a href=\"javascript:void(0);\" id=\"t_new_hotest_prev\"&gt;&lt;img src=\"/skin/sample/plus/images/prev.png\" /&gt;&lt;/a&gt;\r\n    &lt;a href=\"javascript:void(0);\" id=\"t_new_hotest_next\"&gt;&lt;img src=\"/skin/sample/plus/images/next.png\" /&gt;&lt;/a&gt;\r\n  &lt;/div&gt;\r\n&lt;/div&gt;\r\n&lt;script type=\"text/javascript\"&gt;\r\n$(document).ready(function () {\r\n    function carouselInitCallback(carousel) {\r\n        $(\'#t_new_hotest_next\').bind(\'click\', function () {\r\n            carousel.next();\r\n            return false;\r\n        });\r\n        $(\'#t_new_hotest_prev\').bind(\'click\', function () {\r\n            carousel.prev();\r\n            return false;\r\n        });\r\n\r\n        // Disable autoscrolling if the user clicks the prev or next button.\r\n        carousel.buttonNext.bind(\'click\', function () {\r\n            carousel.startAuto(0);\r\n        });\r\n\r\n        carousel.buttonPrev.bind(\'click\', function () {\r\n            carousel.startAuto(0);\r\n        });\r\n\r\n        // Pause autoscrolling if the user moves with the cursor over the clip.\r\n        carousel.clip.hover(function () {\r\n            carousel.stopAuto();\r\n        }, function () {\r\n            carousel.startAuto();\r\n        });\r\n    };\r\n    $(\'#t_new_hotest_slide\').jcarousel({\r\n        scroll: 1,\r\n        auto: 5, // Sets the time delay between automatic scrolling of the panel\r\n        wrap: \'last\',\r\n        initCallback: carouselInitCallback,\r\n        // This tells jCarousel NOT to autobuild prev/next buttons\r\n        buttonNextHTML: null,\r\n        buttonPrevHTML: null\r\n    });\r\n});\r\n&lt;/script&gt;</pre>\r\n<p>Here, I use jquery.jcarousel plugin to slide the list of hotest articles. So, first, download the jcarousel library from http://sorgalla.com/jcarousel/ and put them to <strong>/js/jquery_plugins/jcarousel</strong> directory:</p>\r\n<pre class=\"brush: plain\">TomatoCMS_Root_Dir\r\n|___js\r\n   |___jquery_plugins\r\n      |___jcarousel\r\n         |___jquery.jcarousel.css\r\n         |___jquery.jcarousel.pack.js\r\n</pre>\r\n<p>Also, I add styles for this slide:</p>\r\n<p><strong>// skin/sample/plus/default.css</strong></p>\r\n<pre class=\"brush: css\">...\r\n/* ========== Widgets ======================================================= */\r\n\r\n/**\r\n * news/hotest\r\n */\r\n.t_new_hotest { \r\n  width: 100%; \r\n  color: #333333; \r\n  background-color: #B2C629; \r\n}\r\n#t_new_hotest_slide { \r\n  position: relative; \r\n  margin: 0 auto 0; \r\n  display: block; \r\n  width: 840px; \r\n  height: 260px; \r\n  padding: 20px 50px; \r\n  overflow: hidden; \r\n  font-size: 12px; \r\n  font-family: Verdana, Arial, Helvetica, sans-serif; \r\n}\r\n#t_new_hotest_slide a { \r\n  color: #FFFFFF; \r\n  background-color: #B2C629; \r\n}\r\n#t_new_hotest_slide a, #t_new_hotest_slide ul, #t_new_hotest_slide img { \r\n  margin: 0; \r\n  padding: 0; \r\n  border: none; \r\n  outline: none; \r\n  list-style: none; \r\n  text-decoration: none; \r\n}\r\n#t_new_hotest_slide h1, #t_new_hotest_slide h2 { \r\n  margin: 15px 0 10px 0; \r\n  padding: 0; \r\n  line-height: normal; \r\n  font-size: 36px; \r\n  font-weight: normal; \r\n  font-family: Georgia, \"Times New Roman\", Times, serif; \r\n}\r\n#t_new_hotest_content, #t_new_hotest_content ul { \r\n  display: block; \r\n  width: 840px; \r\n  height: 260px; \r\n  margin: 0; \r\n  padding: 0; \r\n  list-style: none; \r\n  overflow: hidden; \r\n}\r\n#t_new_hotest_content li { \r\n  display: block; \r\n  position: relative; \r\n  width: 840px; \r\n  height: 260px; \r\n  overflow: hidden; \r\n  margin-left: 0px; \r\n}\r\n#t_new_hotest_content img { \r\n  display: block; \r\n  float: right; \r\n  width: 380px; \r\n  height: 250px; \r\n  margin: 0 10px 0 0; \r\n  padding: 4px; \r\n  border: 1px solid #FFFFFF; \r\n}\r\n#t_new_hotest_content li div:first-child { \r\n  display: block; \r\n  float: left; \r\n  width: 400px; \r\n  height: 250px; \r\n  margin: 0 0 0 10px; \r\n  padding: 0; \r\n  overflow: hidden; \r\n}\r\n#t_new_hotest_content p { \r\n  margin: 0 0 20px 0; \r\n  padding: 0; \r\n  line-height: 1.6em; \r\n}\r\n#t_new_hotest_content p.t_new_hotest_more { \r\n  display: block; \r\n  width: 100%; \r\n  margin: 0; \r\n  padding: 0; \r\n  text-align: right; \r\n  line-height: normal; \r\n  font-weight: bold; \r\n}\r\n#t_new_hotest_content p.t_new_hotest_more a { \r\n  padding: 8px 15px 10px; \r\n  color: #FFFFFF; \r\n  background-color: #95AD19; \r\n}\r\n#t_new_hotest_prev, #t_new_hotest_next { \r\n  display: block; \r\n  position: absolute; \r\n  top: 118px; \r\n  width: 36px; \r\n  height: 64px; \r\n}\r\n#t_new_hotest_prev { \r\n  left: 5px; \r\n}\r\n#t_new_hotest_next { \r\n  right: 5px; \r\n}\r\n</pre>\r\n<p>Notes that I named <strong>t_new_hotest</strong> for maintaining easy later. This name allows me to know that this styles is for hotest widget from news module.</p>\r\n<p>Our homepage now looks like:</p>\r\n<p> </p>\r\n<p><img src=\"http://demo.tomatocms.com/upload/news/admin/2010/03/4b9e5ca9843cc_medium.png\" alt=\"\" /></p>\r\n<p> </p>\r\n<h2>Repeat steps to customize output of news/newest widget</h2>\r\n<p>Now, it\'s easy to repeat similar steps to show the newest articles in second 12-columns container.</p>\r\n<p>- Configure home.xml:</p>\r\n<p><strong>// app/templates/sample/layouts/home.xml</strong></p>\r\n<p>BEFORE:</p>\r\n<pre class=\"brush: xml\">...\r\n&lt;widget module=\"core\" name=\"html\"&gt;\r\n  &lt;params&gt;\r\n    &lt;param name=\"content\"&gt;\r\n      &lt;value&gt;&lt;![CDATA[Display 3 newest articles]]&gt;&lt;/value&gt;\r\n    &lt;/param&gt;\r\n  &lt;/params&gt;\r\n&lt;/widget&gt;\r\n...\r\n</pre>\r\n<p>AFTER:</p>\r\n<pre class=\"brush: xml\">...\r\n&lt;widget name=\"newest\" module=\"news\"&gt;\r\n  &lt;params&gt;\r\n    &lt;param name=\"limit\"&gt;\r\n      &lt;value&gt;&lt;![CDATA[3]]&gt;&lt;/value&gt;\r\n    &lt;/param&gt;\r\n  &lt;/params&gt;\r\n&lt;/widget&gt;\r\n...\r\n</pre>\r\n<p> </p>\r\n<p>- Create directory named newest in <strong>app/templates/sample/views/news/widgets</strong> directory.</p>\r\n<p>Create <strong>show.phtml</strong> in the directory you have just created above.</p>\r\n<p> </p>\r\n<p>- Open the default output of widget (<strong>app/modules/news/widgets/newest/show.phtml</strong>) to know how to get the data:</p>\r\n<p><strong>// app/modules/news/widgets/newest/show.phtml</strong></p>\r\n<pre class=\"brush: php\">...\r\n&lt;?php if ($this-&gt;articles != null) : ?&gt;\r\n  &lt;?php foreach ($this-&gt;articles as $index =&gt; $article) : ?&gt;\r\n  &lt;?php if ($index % 5 == 0) : ?&gt;&lt;ul style=\"&lt;?php if ($index == 0) : ?&gt;dispaly: block&lt;?php else : ?&gt;display: none&lt;?php endif; ?&gt;\"&gt;&lt;?php endif;?&gt;\r\n  &lt;li&gt;\r\n    &lt;?php if ($article-&gt;image_square) : ?&gt;&lt;a href=\"&lt;?php echo $this-&gt;url($article-&gt;getProperties(), \'news_article_details\'); ?&gt;\"&gt;&lt;img src=\"&lt;?php echo $article-&gt;image_square; ?&gt;\" width=\"60px\" height=\"60px\" /&gt;&lt;/a&gt;&lt;?php endif; ?&gt;\r\n    &lt;strong&gt;&lt;a href=\"&lt;?php echo $this-&gt;url($article-&gt;getProperties(), \'news_article_details\'); ?&gt;\"&gt;&lt;?php echo $article-&gt;title; ?&gt;&lt;/a&gt;&lt;/strong&gt;\r\n    &lt;div&gt;&lt;?php echo date($this-&gt;globalConfig(\'datetime\')-&gt;date_time_format, strtotime($article-&gt;activate_date)); ?&gt;&lt;/div&gt;\r\n  &lt;/li&gt;\r\n  &lt;?php if ($index % 5 &gt;= 0 &amp;&amp; $index % 5 &lt;= 3) : ?&gt;&lt;li style=\"margin: 0; padding-top: 5px; height: 5px; line-height: 5px\"&gt;&lt;hr class=\"t_g_dot\" /&gt;&lt;/li&gt;&lt;?php endif; ?&gt;\r\n  &lt;?php if ($index % 5 == 4) : ?&gt;&lt;/ul&gt;&lt;?php endif; ?&gt;\r\n  &lt;?php endforeach; ?&gt;\r\n&lt;?php endif; ?&gt;\r\n...\r\n</pre>\r\n<p>Copy it and paste it in new output. Make some changes to customize the output:</p>\r\n<p><strong>// app/templates/sample/views/news/widgets/newest/show.phtml</strong></p>\r\n<pre class=\"brush: php\">&lt;div class=\"t_news_newest\"&gt;\r\n  &lt;?php if ($this-&gt;articles != null) : ?&gt;\r\n  &lt;ul&gt;\r\n    &lt;?php foreach ($this-&gt;articles as $index =&gt; $article) : ?&gt;\r\n    &lt;li&gt;\r\n      &lt;h2&gt;&lt;?php if ($article-&gt;image_square) : ?&gt;&lt;img src=\"&lt;?php echo $article-&gt;image_square; ?&gt;\" width=\"60px\" height=\"60px\" /&gt;&lt;?php endif; ?&gt;&lt;?php echo $article-&gt;title; ?&gt;&lt;/h2&gt;\r\n      &lt;p&gt;&lt;?php echo $this-&gt;subString($article-&gt;description, 300); ?&gt;&lt;/p&gt;\r\n      &lt;p class=\"t_news_newest_more\"&gt;&lt;a href=\"&lt;?php echo $this-&gt;url($article-&gt;getProperties(), \'news_article_details\'); ?&gt;\"&gt;&lt;?php echo $this-&gt;translator()-&gt;widget(\'more\'); ?&gt;&lt;/a&gt;&lt;/p&gt;\r\n    &lt;/li&gt;\r\n    &lt;?php endforeach; ?&gt;\r\n  &lt;/ul&gt;\r\n  &lt;?php endif; ?&gt;\r\n  &lt;div class=\"clearfix\"&gt;&lt;/div&gt;\r\n&lt;/div&gt;\r\n</pre>\r\n<p>- Add styles for this widget:</p>\r\n<p><strong>// skin/sample/plus/default.css</strong></p>\r\n<pre class=\"brush: css\">...\r\n/**\r\n * news/newest\r\n */\r\n.t_news_newest { \r\n  display: block; \r\n  width: 960px; \r\n  margin: 15px 0 0 0; \r\n  padding: 20px 0; \r\n  color: #777777; \r\n  background-color: #FFFFFF; \r\n}\r\n.t_news_newest a {\r\n  color: #95AD19; \r\n  background-color: #FFFFFF; \r\n}\r\n.t_news_newest ul { \r\n  margin: 0; \r\n  padding: 0; \r\n  list-style: none; \r\n}\r\n.t_news_newest li { \r\n  display: block; \r\n  float: left; \r\n  width: 300px; \r\n  margin: 0 30px 0 0; \r\n  padding: 0; \r\n}\r\n.t_news_newest li:last-child { \r\n  margin-right: 0; \r\n}\r\n.t_news_newest li h2 { \r\n  display: block; \r\n  width: 100%; \r\n  height: 65px; \r\n  margin: 0 0 15px; \r\n  padding: 0 0 8px; \r\n  font-size: 18px; \r\n  font-weight: normal; \r\n  line-height: normal; \r\n  border-bottom: 1px solid #E7E6E6; \r\n}\r\n.t_news_newest li h2 img { \r\n  float: left; \r\n  margin: -15px 8px 0 0; \r\n  padding: 5px; \r\n  border: 1px solid #999999; \r\n}\r\n.t_news_newest p { \r\n  margin: 0 0 25px 0; \r\n  padding: 0; \r\n  line-height: 1.6em; \r\n  height: 155px; \r\n}\r\n.t_news_newest p.t_news_newest_more { \r\n  display: block; \r\n  text-align: right; \r\n  line-height: normal; \r\n  font-weight: bold; \r\n  height: auto; \r\n}\r\n.t_news_newest p.t_news_newest_more a { \r\n  padding: 8px 15px 10px; \r\n  color: #FFFFFF; \r\n  background-color: #95AD19; \r\n}\r\n</pre>\r\n<p> </p>\r\n<p>The most complete result:</p>\r\n<p> </p>\r\n<p><img src=\"http://demo.tomatocms.com/upload/news/admin/2010/03/4b9e5cac8183e_medium.png\" alt=\"\" /></p>\r\n<p> </p>\r\n<p>Our homepage after performing this step:</p>\r\n<p> </p>\r\n<p><img src=\"http://demo.tomatocms.com/upload/news/admin/2010/03/4b9e5cb055472_medium.png\" alt=\"\" /></p>\r\n<p>But it does not look like our design exactly. In next post, I will guide you do some changes that make our homepage is the same as design version.</p>\r\n<p>(To be continued ...)</p>','Nguyen Huu Phuoc','{\"image\"}','http://demo.tomatocms.com/upload/news/admin/2010/03/4b9e5cb055472_square.png',NULL,'http://demo.tomatocms.com/upload/news/admin/2010/03/4b9e5cb055472_small.png','http://demo.tomatocms.com/upload/news/admin/2010/03/4b9e5cb055472_general.png','http://demo.tomatocms.com/upload/news/admin/2010/03/4b9e5cb055472_crop.png','http://demo.tomatocms.com/upload/news/admin/2010/03/4b9e5cb055472_medium.png','http://demo.tomatocms.com/upload/news/admin/2010/03/4b9e5cb055472_large.png','active',253,'2010-03-15 16:42:10',1,'admin',NULL,NULL,NULL,1,'admin','2010-03-15 16:42:23',0,0,0,0,NULL,1),(13,6,'Guide on creating new template - Part 7: Define CSS class for container, widget','','guide-on-creating-new-template-part-7-define-css-class-for-container-widget','<p>In previous post, creating new template is almost complete. But it does not look like our design exactly. This post will show how to improve template.</p>','<p>After previous post, our template now looks like:</p>\r\n<p> </p>\r\n<p><img src=\"http://demo.tomatocms.com/upload/news/admin/2010/03/4b9ef87669933_medium.png\" alt=\"\" /></p>\r\n<p> </p>\r\n<p>What we have to do is improve this so that it will look like the design version:</p>\r\n<p> </p>\r\n<p><img src=\"http://demo.tomatocms.com/upload/news/admin/2010/03/4b9ef87478000_medium.png\" alt=\"\" /></p>\r\n<p> </p>\r\n<p>If you look at two images above, the differences are the containers which contain slideshow at the top and company information at the bottom.</p>\r\n<p>TomatoCMS use 960 Grid System to layout the page, it means that the width of page is always 960 pixels.</p>\r\n<p>Currently, all content is bounded in div container which was defined by <strong>container_12</strong> class.</p>\r\n<p><strong>// app/templates/sample/layouts/default.phtml</strong></p>\r\n<pre class=\"brush: php\">&lt;div&gt;\r\n  ...\r\n  &lt;div class=\"container_12\"&gt;\r\n    &lt;?php echo $this-&gt;layoutLoader(); ?&gt;\r\n    &lt;div class=\"clearfix\"&gt;&lt;/div&gt;\r\n  &lt;/div&gt;  \r\n  ...\r\n&lt;/div&gt;\r\n</pre>\r\n<p>So, we have to remove this class firstly:</p>\r\n<pre class=\"brush: php\">&lt;div&gt;\r\n  ...\r\n  &lt;div&gt;\r\n    &lt;?php echo $this-&gt;layoutLoader(); ?&gt;\r\n    &lt;div class=\"clearfix\"&gt;&lt;/div&gt;\r\n  &lt;/div&gt;  \r\n  ...\r\n&lt;/div&gt;\r\n</pre>\r\n<p>But, now all content are left as follow:</p>\r\n<p> </p>\r\n<p><img src=\"http://demo.tomatocms.com/upload/news/admin/2010/03/4b9efd01ce2c6_medium.png\" alt=\"\" /></p>\r\n<p> </p>\r\n<p>Don\'t worry! I will guide you how to modify the layout configuration file (home.xml, in this case) to match our design.</p>\r\n<p>Open our layout file (<strong>app/templates/sample/layouts/home.xml</strong>).</p>\r\n<p>The first container is used to show the slides of hottest articles:</p>\r\n<pre class=\"brush: xml\">...\r\n&lt;container cols=\"12\"&gt;\r\n  &lt;widget name=\"hotest\" module=\"news\"&gt;\r\n    &lt;params&gt;\r\n      &lt;param name=\"limit\"&gt;&lt;value&gt;&lt;![CDATA[3]]&gt;&lt;/value&gt;&lt;/param&gt;\r\n    &lt;/params&gt;\r\n  &lt;/widget&gt;\r\n&lt;/container&gt;\r\n...\r\n</pre>\r\n<p>Remove the <strong>cols</strong> property as follow:</p>\r\n<pre class=\"brush: xml\">...\r\n&lt;container&gt;\r\n  &lt;widget name=\"hotest\" module=\"news\"&gt;\r\n    &lt;params&gt;\r\n      &lt;param name=\"limit\"&gt;&lt;value&gt;&lt;![CDATA[3]]&gt;&lt;/value&gt;&lt;/param&gt;\r\n    &lt;/params&gt;\r\n  &lt;/widget&gt;\r\n&lt;/container&gt;\r\n...\r\n</pre>\r\n<p>Now, the first container looks like what we expected:</p>\r\n<p> </p>\r\n<p><img src=\"http://demo.tomatocms.com/upload/news/admin/2010/03/4b9eff3e9928c_medium.png\" alt=\"\" /></p>\r\n<p>The first container is centered, because we defined the styles for this in CSS:</p>\r\n<p><strong>// skin/sample/plus/default.css</strong></p>\r\n<pre class=\"brush: css\">#t_new_hotest_slide {\r\n  ...\r\n  margin: 0 auto 0; \r\n  width: 840px;\r\n  ...\r\n}\r\n</pre>\r\n<h2>Set cssClass property for containers, widgets</h2>\r\n<p>The next mission is second container which show the 3 latest articles.</p>\r\n<p>The current configuration for this container is:</p>\r\n<p><strong>// </strong><strong>app/templates/sample/layouts/home.xml</strong></p>\r\n<pre class=\"brush: xml\">...\r\n&lt;container cols=\"12\"&gt;\r\n  &lt;widget name=\"newest\" module=\"news\"&gt;\r\n    &lt;params&gt;\r\n      &lt;param name=\"limit\"&gt;&lt;value&gt;&lt;![CDATA[3]]&gt;&lt;/value&gt;&lt;/param&gt;\r\n    &lt;/params&gt;\r\n  &lt;/widget&gt;\r\n&lt;/container&gt;\r\n...\r\n</pre>\r\n<p>I wish I can set container_12 class for container which bound the newest widget. Fortunately, TomatoCMS allows you to do this.</p>\r\n<p>I remove cols property and set cssClass property as follow:</p>\r\n<pre class=\"brush: xml\">...\r\n&lt;container cssClass=\"container_12\"&gt;\r\n  &lt;widget name=\"newest\" module=\"news\"&gt;\r\n    &lt;params&gt;\r\n      &lt;param name=\"limit\"&gt;&lt;value&gt;&lt;![CDATA[3]]&gt;&lt;/value&gt;&lt;/param&gt;\r\n    &lt;/params&gt;\r\n  &lt;/widget&gt;\r\n&lt;/container&gt;\r\n...\r\n</pre>\r\n<p>Refresh our homepage:</p>\r\n<p> </p>\r\n<p><img src=\"http://demo.tomatocms.com/upload/news/admin/2010/03/4b9f02a238d61_medium.png\" alt=\"\" /></p>\r\n<p> </p>\r\n<p>Well, the last thing have to do is fix the bottom container.</p>\r\n<p>Currently, the bottom container is full-row container and is split into three 4-columns containers:</p>\r\n<p><strong>// app/templates/sample/layouts/home.xml</strong></p>\r\n<pre class=\"brush: xml\">...\r\n&lt;container cols=\"12\"&gt;\r\n  &lt;container cols=\"4\" position=\"first\"&gt;\r\n    ... \r\n  &lt;/container&gt;\r\n  &lt;container cols=\"4\"&gt;\r\n    ...\r\n  &lt;/container&gt;\r\n  &lt;container cols=\"4\" position=\"last\"&gt;\r\n    ...\r\n  &lt;/container&gt;\r\n&lt;/container&gt;\r\n...\r\n</pre>\r\n<p>I will make a small modification. I put this container in other containers which is used to centered the bottom container:</p>\r\n<p><strong>// app/templates/sample/layouts/home.xml</strong></p>\r\n<pre class=\"brush: xml\">...\r\n&lt;container cssClass=\"bottom\"&gt;\r\n  &lt;container cssClass=\"container_12\"&gt;\r\n    &lt;container cols=\"12\"&gt;\r\n    ...\r\n    &lt;/container&gt;\r\n  &lt;/container&gt;\r\n&lt;/container&gt;\r\n...\r\n</pre>\r\n<p>The following image illustrates the structure of containers:</p>\r\n<p><img src=\"http://demo.tomatocms.com/upload/news/admin/2010/03/4b9f0605bff90_medium.png\" alt=\"\" /></p>\r\n<p> </p>\r\n<p>Finally, we have to define the styles for <strong>.bottom</strong> class:</p>\r\n<p><strong>// skin/sample/plus/default.css</strong></p>\r\n<pre class=\"brush: css\">...\r\n#footer, #footer a { \r\n  ...\r\n}\r\n.bottom { \r\n  padding: 20px 0; \r\n  color: #FFFFFF; \r\n  background-color: #000000; \r\n  height: 300px; \r\n}\r\n...\r\n</pre>\r\n<p>Now our home page looks like our design exactly:</p>\r\n<p> </p>\r\n<p><img src=\"http://demo.tomatocms.com/upload/news/admin/2010/03/4b9f078e31c01_medium.png\" alt=\"\" /></p>\r\n<p> </p>\r\n<p>It works great!</p>\r\n<p>In this post, you know that you can remove the cols property from container. Also, you can set cssClass property for containers and widgets.</p>\r\n<p>I close the series of guides on customizing template here. I hope you can create new template by yourself after reading this series.</p>\r\n<p>Thank and wait for your comments!</p>','Nguyen Huu Phuoc','','http://demo.tomatocms.com/upload/news/admin/2010/03/4b9ef87478000_square.png',NULL,'http://demo.tomatocms.com/upload/news/admin/2010/03/4b9ef87478000_small.png','http://demo.tomatocms.com/upload/news/admin/2010/03/4b9ef87478000_general.png','http://demo.tomatocms.com/upload/news/admin/2010/03/4b9ef87478000_crop.png','http://demo.tomatocms.com/upload/news/admin/2010/03/4b9ef87478000_medium.png','http://demo.tomatocms.com/upload/news/admin/2010/03/4b9ef87478000_large.png','active',302,'2010-03-16 03:19:24',1,'admin',NULL,NULL,NULL,1,'admin','2010-03-16 04:27:46',0,0,0,0,NULL,1);
/*!40000 ALTER TABLE `news_article` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `news_article_category_assoc`
--

DROP TABLE IF EXISTS `news_article_category_assoc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `news_article_category_assoc` (
  `article_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  PRIMARY KEY  (`article_id`,`category_id`),
  KEY `idx_category` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `news_article_category_assoc`
--

LOCK TABLES `news_article_category_assoc` WRITE;
/*!40000 ALTER TABLE `news_article_category_assoc` DISABLE KEYS */;
INSERT INTO `news_article_category_assoc` VALUES (1,1),(2,1),(3,1),(4,1),(5,1),(6,1),(1,2),(2,2),(3,2),(4,2),(5,2),(6,2),(7,6),(8,6),(9,6),(10,6),(11,6),(12,6),(13,6);
/*!40000 ALTER TABLE `news_article_category_assoc` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `news_article_hot`
--

DROP TABLE IF EXISTS `news_article_hot`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `news_article_hot` (
  `article_id` int(11) NOT NULL,
  `created_date` datetime default NULL,
  `ordering` smallint(6) default NULL,
  PRIMARY KEY  (`article_id`),
  KEY `idx_ordering` (`ordering`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `news_article_hot`
--

LOCK TABLES `news_article_hot` WRITE;
/*!40000 ALTER TABLE `news_article_hot` DISABLE KEYS */;
INSERT INTO `news_article_hot` VALUES (3,'2010-03-15 07:42:04',1),(6,'2010-03-15 18:01:03',1),(7,'2010-03-15 17:50:34',1),(9,'2010-03-15 17:48:31',1),(10,'2010-03-15 17:46:53',1),(11,'2010-03-15 17:45:51',1),(12,'2010-03-15 17:42:58',1),(13,'2010-03-16 04:29:18',1);
/*!40000 ALTER TABLE `news_article_hot` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `news_article_rate`
--

DROP TABLE IF EXISTS `news_article_rate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `news_article_rate` (
  `article_id` int(11) NOT NULL,
  `rate` enum('1','2','3','4','5') NOT NULL,
  `ip` varchar(40) NOT NULL,
  `created_date` datetime default NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `news_article_rate`
--

LOCK TABLES `news_article_rate` WRITE;
/*!40000 ALTER TABLE `news_article_rate` DISABLE KEYS */;
/*!40000 ALTER TABLE `news_article_rate` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `news_article_revision`
--

DROP TABLE IF EXISTS `news_article_revision`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `news_article_revision` (
  `revision_id` int(11) NOT NULL auto_increment,
  `article_id` int(11) NOT NULL,
  `category_id` smallint(6) default NULL,
  `title` varchar(255) default NULL,
  `sub_title` varchar(255) default NULL,
  `slug` varchar(255) default NULL,
  `description` text,
  `content` mediumtext,
  `author` varchar(255) default NULL,
  `icons` varchar(255) default NULL,
  `created_date` datetime default NULL,
  `created_user_id` int(11) default NULL,
  `created_user_name` varchar(255) default NULL,
  PRIMARY KEY  (`revision_id`),
  KEY `idx_article_id` (`article_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `news_article_revision`
--

LOCK TABLES `news_article_revision` WRITE;
/*!40000 ALTER TABLE `news_article_revision` DISABLE KEYS */;
/*!40000 ALTER TABLE `news_article_revision` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `poll_answer`
--

DROP TABLE IF EXISTS `poll_answer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `poll_answer` (
  `answer_id` int(10) unsigned NOT NULL auto_increment,
  `question_id` int(11) NOT NULL,
  `position` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text,
  `is_correct` tinyint(4) default NULL,
  `user_id` int(11) NOT NULL,
  `num_views` int(11) NOT NULL default '0',
  PRIMARY KEY  (`answer_id`),
  KEY `question_id` (`question_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `poll_answer`
--

LOCK TABLES `poll_answer` WRITE;
/*!40000 ALTER TABLE `poll_answer` DISABLE KEYS */;
/*!40000 ALTER TABLE `poll_answer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `poll_question`
--

DROP TABLE IF EXISTS `poll_question`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `poll_question` (
  `question_id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL,
  `content` varchar(255) default NULL,
  `created_date` datetime NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime default NULL,
  `is_active` tinyint(1) NOT NULL,
  `multiple_options` tinyint(1) NOT NULL,
  `user_id` int(11) NOT NULL,
  `num_views` int(11) default NULL,
  PRIMARY KEY  (`question_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `poll_question`
--

LOCK TABLES `poll_question` WRITE;
/*!40000 ALTER TABLE `poll_question` DISABLE KEYS */;
/*!40000 ALTER TABLE `poll_question` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tag`
--

DROP TABLE IF EXISTS `tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tag` (
  `tag_id` int(10) unsigned NOT NULL auto_increment,
  `tag_text` varchar(255) NOT NULL,
  PRIMARY KEY  (`tag_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tag`
--

LOCK TABLES `tag` WRITE;
/*!40000 ALTER TABLE `tag` DISABLE KEYS */;
INSERT INTO `tag` VALUES (1,'zend-framework'),(2,'template'),(3,'feature');
/*!40000 ALTER TABLE `tag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tag_item_assoc`
--

DROP TABLE IF EXISTS `tag_item_assoc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tag_item_assoc` (
  `tag_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `item_name` varchar(30) NOT NULL,
  `route_name` varchar(200) NOT NULL,
  `details_route_name` varchar(200) NOT NULL,
  `params` varchar(255) default NULL,
  PRIMARY KEY  (`tag_id`,`item_id`,`item_name`),
  KEY `tag_id` (`tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tag_item_assoc`
--

LOCK TABLES `tag_item_assoc` WRITE;
/*!40000 ALTER TABLE `tag_item_assoc` DISABLE KEYS */;
INSERT INTO `tag_item_assoc` VALUES (2,7,'article_id','news_article_details','news_tag_article','article_id:7'),(2,8,'article_id','news_article_details','news_tag_article','article_id:8'),(2,9,'article_id','news_article_details','news_tag_article','article_id:9'),(2,10,'article_id','news_article_details','news_tag_article','article_id:10'),(2,11,'article_id','news_article_details','news_tag_article','article_id:11'),(2,12,'article_id','news_article_details','news_tag_article','article_id:12'),(2,13,'article_id','news_article_details','news_tag_article','article_id:13'),(3,1,'set_id','multimedia_set_details','multimedia_tag_set','set_id:1');
/*!40000 ALTER TABLE `tag_item_assoc` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2010-04-02 15:30:45
