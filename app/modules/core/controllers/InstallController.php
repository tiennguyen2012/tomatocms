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
 * @version 	$Id: InstallController.php 1821 2010-03-30 06:44:37Z huuphuoc $
 * @since		2.0.1
 */

class Core_InstallController extends Zend_Controller_Action 
{
	/**
	 * List of supported timezones
	 * http://www.php.net/manual/en/timezones.php
	 * http://unicode.org/repos/cldr/trunk/docs/design/formatting/zone_log.html#windows_ids
	 */
	private static $_TIME_ZONES = array(
		'Pacific/Apia'			=> '(GMT-11:00) Midway Island, Samoa',
		'Pacific/Honolulu'		=> '(GMT-10:00) Hawaii',
		'America/Anchorage'		=> '(GMT-09:00) Alaska',
		'America/Los_Angeles'	=> '(GMT-08:00) Pacific Time (US & Canada); Tijuana',
		'America/Phoenix'		=> '(GMT-07:00) Arizona',
		'America/Denver'		=> '(GMT-07:00) Mountain Time (US & Canada)',
		'America/Chihuahua'		=> '(GMT-07:00) Chihuahua, La Paz, Mazatlan',
		'America/Managua'		=> '(GMT-06:00) Central America',
		'America/Regina'		=> '(GMT-06:00) Saskatchewan',
		'America/Mexico_City'	=> '(GMT-06:00) Guadalajara, Mexico City, Monterrey',
		'America/Chicago'		=> '(GMT-06:00) Central Time (US & Canada)',
		'America/Indianapolis'	=> '(GMT-05:00) Indiana (East)',
		'America/Bogota'		=> '(GMT-05:00) Bogota, Lima, Quito',
		'America/New_York'		=> '(GMT-05:00) Eastern Time (US & Canada)',
		'America/Caracas'		=> '(GMT-04:00) Caracas, La Paz',
		'America/Santiago'		=> '(GMT-04:00) Santiago',
		'America/Halifax'		=> '(GMT-04:00) Atlantic Time (Canada)',
		'America/St_Johns'		=> '(GMT-03:30) Newfoundland',
		'America/Buenos_Aires'	=> '(GMT-03:00) Buenos Aires, Georgetown',
		'America/Godthab'		=> '(GMT-03:00) Greenland',
		'America/Sao_Paulo'		=> '(GMT-03:00) Brasilia',
		'America/Noronha'		=> '(GMT-02:00) Mid-Atlantic',
		'Atlantic/Cape_Verde'	=> '(GMT-01:00) Cape Verde Is.',
		'Atlantic/Azores'		=> '(GMT-01:00) Azores',
		'Africa/Casablanca'		=> '(GMT) Casablanca, Monrovia',
		'Europe/London'			=> '(GMT) Greenwich Mean Time: Dublin, Edinburgh, Lisbon, London',
		'Africa/Lagos'			=> '(GMT+01:00) West Central Africa',
		'Europe/Berlin'			=> '(GMT+01:00) Amsterdam, Berlin, Bern, Rome, Stockholm, Vienna',
		'Europe/Paris'			=> '(GMT+01:00) Brussels, Copenhagen, Madrid, Paris',
		'Europe/Sarajevo'		=> '(GMT+01:00) Sarajevo, Skopje, Warsaw, Zagreb',
		'Europe/Belgrade'		=> '(GMT+01:00) Belgrade, Bratislava, Budapest, Ljubljana, Prague',
		'Africa/Johannesburg'	=> '(GMT+02:00) Harare, Pretoria',
		'Asia/Jerusalem'		=> '(GMT+02:00) Jerusalem',
		'Europe/Istanbul'		=> '(GMT+02:00) Athens, Istanbul, Minsk',
		'Europe/Helsinki'		=> '(GMT+02:00) Helsinki, Kyiv, Riga, Sofia, Tallinn, Vilnius',
		'Africa/Cairo'			=> '(GMT+02:00) Cairo',
		'Europe/Bucharest'		=> '(GMT+02:00) Bucharest',
		'Africa/Nairobi'		=> '(GMT+03:00) Nairobi',
		'Asia/Riyadh'			=> '(GMT+03:00) Kuwait, Riyadh',
		'Europe/Moscow'			=> '(GMT+03:00) Moscow, St. Petersburg, Volgograd',
		'Asia/Baghdad'			=> '(GMT+03:00) Baghdad',
		'Asia/Tehran'			=> '(GMT+03:30) Tehran',
		'Asia/Muscat'			=> '(GMT+04:00) Abu Dhabi, Muscat',
		'Asia/Tbilisi'			=> '(GMT+04:00) Baku, Tbilisi, Yerevan',
		'Asia/Kabul'			=> '(GMT+04:30) Kabul',
		'Asia/Karachi'			=> '(GMT+05:00) Islamabad, Karachi, Tashkent',
		'Asia/Yekaterinburg'	=> '(GMT+05:00) Ekaterinburg',
		'Asia/Calcutta'			=> '(GMT+05:30) Chennai, Kolkata, Mumbai, New Delhi',
		'Asia/Katmandu'			=> '(GMT+05:45) Kathmandu',
		'Asia/Colombo'			=> '(GMT+06:00) Sri Jayawardenepura',
		'Asia/Dhaka'			=> '(GMT+06:00) Astana, Dhaka',
		'Asia/Novosibirsk'		=> '(GMT+06:00) Almaty, Novosibirsk',
		'Asia/Rangoon'			=> '(GMT+06:30) Rangoon',
		'Asia/Bangkok'			=> '(GMT+07:00) Bangkok, Hanoi, Jakarta',
		'Asia/Krasnoyarsk'		=> '(GMT+07:00) Krasnoyarsk',
		'Australia/Perth'		=> '(GMT+08:00) Perth',
		'Asia/Taipei'			=> '(GMT+08:00) Taipei',
		'Asia/Singapore'		=> '(GMT+08:00) Kuala Lumpur, Singapore',
		'Asia/Hong_Kong'		=> '(GMT+08:00) Beijing, Chongqing, Hong Kong, Urumqi',
		'Asia/Irkutsk'			=> '(GMT+08:00) Irkutsk, Ulaan Bataar',
		'Asia/Tokyo'			=> '(GMT+09:00) Osaka, Sapporo, Tokyo',
		'Asia/Seoul'			=> '(GMT+09:00) Seoul',
		'Asia/Yakutsk'			=> '(GMT+09:00) Yakutsk',
		'Australia/Darwin'		=> '(GMT+09:30) Darwin',
		'Australia/Adelaide'	=> '(GMT+09:30) Adelaide',
		'Pacific/Guam'			=> '(GMT+10:00) Guam, Port Moresby',
		'Australia/Brisbane'	=> '(GMT+10:00) Brisbane',
		'Asia/Vladivostok'		=> '(GMT+10:00) Vladivostok',
		'Australia/Hobart'		=> '(GMT+10:00) Hobart',
		'Australia/Sydney'		=> '(GMT+10:00) Canberra, Melbourne, Sydney',
		'Asia/Magadan'			=> '(GMT+11:00) Magadan, Solomon Is., New Caledonia',
		'Pacific/Fiji'			=> '(GMT+12:00) Fiji, Kamchatka, Marshall Is.',
		'Pacific/Auckland'		=> '(GMT+12:00) Auckland, Wellington',
		'Pacific/Tongatapu'		=> '(GMT+13:00) Nuku\'alofa',
	);
	
	/**
	 * Available date time formats
	 * @since 2.0.3
	 */
	private static $_DATE_FORMATS = array(
		'm-d-Y',			// 02-22-2010
		'd-m-Y',			// 22-02-2010 
		'm.d.Y', 			// 02.22.2010
		'Y-m-d', 			// 2010-02-22
		'm/d/Y', 			// 02/22/2010
		'm/d/y',			// 02/22/10
		'F d, Y',			// February 22, 2010
		'M. d, y', 			// Feb. 22, 10
		'd F Y', 			// 22 February 2010
		'd-M-y',			// 22-Feb-10
		'l, F d, Y',		// Monday, February 22, 2010
	);
	
	private static $_DATETIME_FORMATS = array(
		'm-d-Y H:i:s', 'm-d-Y h:i:s A',
		'd-m-Y H:i:s', 'd-m-Y h:i:s A', 
		'm.d.Y H:i:s', 'm.d.Y h:i:s A', 
		'Y-m-d H:i:s', 'Y-m-d h:i:s A',
		'm/d/Y H:i:s', 'm/d/Y h:i:s A',
		'm/d/y H:i:s', 'm/d/y h:i:s A', 
		'F d, Y H:i:s', 'F d, Y h:i:s A', 
		'M. d, y H:i:s', 'M. d, y h:i:s A', 
		'd F Y H:i:s', 'd F Y h:i:s A', 
		'd-M-y H:i:s', 'd-M-y h:i:s A', 
		'l, F d, Y H:i:s', 'l, F d, Y h:i:s A',
	);
	
	/**
	 * The sample data file
	 * @since 2.0.4
	 */
	const SAMPLE_DATA = '/install/tomatocms_sample_db.sql';
	
	public function step1Action() 
	{
		// Required extensions
		$extensions = array(
			'gd', 'json', 'mbstring', 
			'mysql', 'pdo', 'pdo_mysql', 
			'simplexml', 'xml', 'xmlreader',
		);
		$pass = true;
		$requiredExtensions = array();
		foreach ($extensions as $ext) {
			$requiredExtensions[$ext] = extension_loaded($ext);
			$pass = $pass && $requiredExtensions[$ext];
		}
		$this->view->assign('requiredExtensions', $requiredExtensions);
		
		// Files/folders must have writing permission
		$files = array(
			'app'.DS.'config'.DS.'app.ini', 
			'app'.DS.'config'.DS.'layout.ini', 
			'app'.DS.'templates',
			'temp',
			'upload',
		);
		$writableFiles = array();
		foreach ($files as $f) {
			$writableFiles[$f] = is_writeable(TOMATO_ROOT_DIR.DS.$f);
			$pass = $pass && $writableFiles[$f];
		}
		$this->view->assign('writableFiles', $writableFiles);
		$this->view->assign('pass', $pass);
		
		if ($this->_request->isPost() && $pass) {
			$this->_redirect($this->view->serverUrl().$this->view->url(array(), 'core_install_step2'));	
		}
	}

	public function step2Action() 
	{
		$mode = $this->_request->getParam('mode', 'install');
		$this->view->assign('mode', $mode);
		
		// Increase execution time
		@ini_set('execution_time', '500');
		
		$sections = array();
		$config = Tomato_Core_Config::getConfig();
		foreach ($config->toArray() as $section => $data) {
			$sections[$section] = $data;				
		}
		
		/**
		 * Database
		 */		
		$master = isset($sections['db']['master']) ? $sections['db']['master'] : null;
		if (null != $master) {
			foreach ($master as $server => $value) {
				$master = $master[$server];
				break;
			}
		}
			
		/**
		 * Web
		 */
		$siteName = isset($sections['web']['site_name']) ? $sections['web']['site_name'] : null;
		$currentTemplate = isset($sections['web']['template']) ? $sections['web']['template'] : null;
		$skin = isset($sections['web']['skin']) ? $sections['web']['skin'] : null;
		$lang = isset($sections['web']['lang']) ? $sections['web']['lang'] : null;
		$metaKeyword = isset($sections['web']['meta_keyword']) ? $sections['web']['meta_keyword'] : null;
		$metaDescription = isset($sections['web']['meta_description']) ? $sections['web']['meta_description'] : null;
		$defaultTitle = isset($sections['web']['default_title']) ? $sections['web']['default_title'] : null;
		
		$currentTimeZone = isset($sections['datetime']['timezone']) ? $sections['datetime']['timezone'] : null;
		$dateTimeFormat = isset($sections['datetime']['date_time_format']) ? $sections['datetime']['date_time_format'] : null;
		$dateFormat = isset($sections['datetime']['date_format']) ? $sections['datetime']['date_format'] : null;
		
		// Get the list of templates
		$subDirs = Tomato_Core_Utility_File::getSubDir(TOMATO_APP_DIR.DS.'templates');
		$templates = array();
		foreach ($subDirs as $dir) {
			// Load template info
			$file = TOMATO_APP_DIR.DS.'templates'.DS.$dir.DS.'about.xml';
			if (!file_exists($file)) {
				continue;
			}
			$xml = simplexml_load_file($file);
			if ((string)$xml->selectable == 'false') {
				continue;
			}
			$template = array();			
			foreach ($xml->skins->skin as $skin) {
				$attrs = $skin->attributes();
				$template[] = (string)$attrs['name'];
			}
			$templates[strtolower($xml->name)] = $template;
		}		
		
		$siteUrl = $this->_request->getScheme().'://'.$this->_request->getHttpHost();
		$basePath = $this->_request->getBasePath();
		if ($basePath != '') {
			$basePath = ltrim($basePath, '/');
			$basePath = rtrim($basePath, '/');
		}
		$url = ($basePath == '') ? $siteUrl : $siteUrl.'/'.$basePath.'/index.php';
		$staticUrl = ($basePath == '') ? $siteUrl : $siteUrl.'/'.$basePath;
		
		$this->view->assign('master', $master);
		$this->view->assign('dbPrefix', $sections['db']['prefix']);
		
		$this->view->assign('siteName', $siteName);
		$this->view->assign('url', $url);
		$this->view->assign('currentTemplate', $currentTemplate);		
		$this->view->assign('lang', $lang);
		$this->view->assign('langDirection', isset($sections['web']['lang_direction']) ? $sections['web']['lang_direction'] : 'ltr');
		$this->view->assign('metaKeyword', $metaKeyword);
		$this->view->assign('metaDescription', $metaDescription);
		$this->view->assign('defaultTitle', $defaultTitle);
		$this->view->assign('offline', isset($sections['web']['offline']) ? $sections['web']['offline'] : null);
		$this->view->assign('offlineMessage', isset($sections['web']['offline_message']) ? $sections['web']['offline_message'] : null);

		$this->view->assign('timeZones', self::$_TIME_ZONES);		
		$this->view->assign('availableDateTimeFormats', self::$_DATETIME_FORMATS);
		$this->view->assign('availableDateFormats', self::$_DATE_FORMATS);
		
		$this->view->assign('currentTimeZone', $currentTimeZone);
		$this->view->assign('dateTimeFormat', $dateTimeFormat);
		$this->view->assign('dateFormat', $dateFormat);
		
		$this->view->assign('templates', $templates);	
		
		if ('saveConfig' == $mode || $this->_request->isPost()) {
			$siteName =  $this->_request->getPost('siteName');
			
			$url = $this->_request->getPost('url');
			$url = rtrim($url, '/');			
			
			$master = array();
			foreach (array('host', 'port', 'username', 'password', 'dbname') as $key) {
				$master[$key] = $this->_request->getPost($key);
			}
			
			$datetimeFormat = $this->_request->getPost('datetimeFormat');			
			$dateFormat = $this->_request->getPost('dateFormat');
		
			$file = TOMATO_APP_DIR.DS.'config'.DS.'app.ini';			
			$config = new Zend_Config_Ini($file, null, array('allowModifications' => true));
			$config = $config->toArray();
			
			/** 
			 * Set settings 
			 */
			unset($config['db']['master']);
			unset($config['db']['slave']);
			
			/**
			 * Allows user to set database prefix
			 * @since 2.0.3
			 */
			unset($config['db']['prefix']);
			$prefix = $this->_request->getPost('prefix');
			$prefix = preg_replace("/\s+/", '', $prefix);
			$config['db']['prefix'] = $prefix;
						
			$config['db']['adapter'] = 'Pdo_Mysql';
			$config['db']['master']['server1']['host'] = $config['db']['slave']['server2']['host'] = $master['host'];
			$config['db']['master']['server1']['port'] = $config['db']['slave']['server2']['port'] = $master['port'];
			$config['db']['master']['server1']['dbname'] = $config['db']['slave']['server2']['dbname'] = $master['dbname'];
			$config['db']['master']['server1']['username'] = $config['db']['slave']['server2']['username'] = $master['username'];
			$config['db']['master']['server1']['password'] = $config['db']['slave']['server2']['password'] = $master['password'];
			$config['db']['master']['server1']['charset'] = $config['db']['slave']['server2']['charset'] = 'utf8';
			
			$config['web']['site_name'] = $siteName;
			$config['web']['url'] = $url;
			$config['web']['template'] = $this->_request->getPost('template');
			$config['web']['skin'] = $this->_request->getPost('skin');
			$config['web']['static_server'] = $staticUrl;
			$config['web']['lang'] = $this->_request->getPost('lang');
			
			$config['web']['lang_direction'] = $this->_request->getPost('langDirection');
			
			$config['web']['meta_keyword'] = preg_replace("/\s+/", ' ', strip_tags($this->_request->getPost('metaKeyword')));
			$config['web']['meta_description'] = $this->_request->getPost('metaDescription');
			$config['web']['default_title'] = $this->_request->getPost('title');

			/**
			 * Set baseURL
			 * @since 2.0.3
			 */
			if ('' != $basePath) {
				$config['production']['resources']['frontController']['baseUrl'] = '/'.$basePath.'/index.php';
			} else {
				$config['production']['resources']['frontController']['baseUrl'] = '/';	
			}
			
			/**
			 * Allows user to set website in offline message
			 * @since 2.0.3
			 */
			$offline = $this->_request->getPost('offline');
			$offline = ($offline) ? 1 : 0;
			if ($offline){
				$config['web']['offline'] = 'true';
				$config['production']['resources']['frontController']['plugins']['offlineMessage'] = 'Tomato_Modules_Core_Controllers_Plugin_OfflineMessage';
			} else {				
				$config['web']['offline'] = 'false';
				if (isset($config['production']['resources']['frontController']['plugins']['offlineMessage'])) {
					unset($config['production']['resources']['frontController']['plugins']['offlineMessage']);			
				} 	
			}
			
			$config['web']['offline_message'] = $this->_request->getPost('offlineMessage');
			
			$config['datetime']['timezone'] = $this->_request->getPost('timezone');
			$config['datetime']['date_time_format'] = $datetimeFormat;
			$config['datetime']['date_format'] = $dateFormat;
			
			/**
			 * Turn on MagicQuote plugin which disable magic quote setting if there is
			 * @since 2.0.3
			 */
			if (get_magic_quotes_gpc()) {
				$config['production']['resources']['frontController']['plugins']['magicQuote'] = 'Tomato_Core_Controller_Plugin_MagicQuote';	
			} else {
				unset($config['production']['resources']['frontController']['plugins']['magicQuote']);
			}

			/**
			 * Write configuration to file
			 */
			$writer = new Zend_Config_Writer_Ini();
			$writer->write($file, new Zend_Config($config));
			if ($mode != 'install') {
				$this->_helper->getHelper('FlashMessenger')->addMessage(
					$this->view->translator('install_step2_success')
				);
				$this->_redirect($this->view->APP_URL.'/admin/core/config/app/');
			} else {			
				/**
				 * Unregistry objects
				 */
				Zend_Registry::_unsetInstance();
				
				/**
				 * Create database tables and init data
				 */
				$ok = true;
				try {
					$conn = Tomato_Core_Db_Connection::getMasterConnection();
				} catch(Exception $ex) {
					$ok = false;
					$this->_helper->getHelper('FlashMessenger')
							->addMessage(sprintf($this->view->translator('install_step2_database_connect_error'), $master['dbname']));					
				}
				if (!$ok) {
					$this->_redirect($this->view->serverUrl().$this->view->url(array(), 'core_install_step2'));
				}
				
				try {
					$moduleDirs = Tomato_Core_Utility_File::getSubDir(TOMATO_APP_DIR.DS.'modules');
						
					// Install modules
					$modules = array();
					
					$moduleGateway = new Tomato_Modules_Core_Model_ModuleGateway();
					$moduleGateway->setDbConnection($conn);
					$moduleInstaller = new Tomato_Modules_Core_Services_Install_Module();
					$moduleInstaller->setModuleGateway($moduleGateway);
					foreach ($moduleDirs as $module) {
						$modules[] = $moduleInstaller->install($module);
					}
					foreach ($modules as $module) {
						if ($module) {
							$moduleGateway->add($module);
						}
					}
					
					/**
					 * Install widgets
					 * @since 2.0.3
					 */
					$widgetGateway = new Tomato_Modules_Core_Model_WidgetGateway($conn);
					$widgetInstaller = new Tomato_Modules_Core_Services_Install_Widget();
					$widgetInstaller->setWidgetGateway($widgetGateway);
					foreach ($moduleDirs as $module) {
						$widgetInstaller->install($module);
					}
	
					// Create resources and previleges
					$resourceGateway = new Tomato_Modules_Core_Model_ResourceGateway($conn);
					$previlegeGateway = new Tomato_Modules_Core_Model_PrivilegeGateway($conn);
					$previlegeInstaller = new Tomato_Modules_Core_Services_Install_Previlege();
					$previlegeInstaller->setPrivilegeGateway($previlegeGateway);
					$previlegeInstaller->setResourceGateway($resourceGateway);
					foreach ($moduleDirs as $module) {
						$previlegeInstaller->install($module);
					}
					
					// Install selected template
					Tomato_Modules_Core_Services_Install_Template::install($config['web']['template']);
					
					// Finally, init data
					$dbFile = TOMATO_ROOT_DIR.DS.'install'.DS.'db.xml';
					if (file_exists($dbFile)) {
						$xml = simplexml_load_file($dbFile);
						if ($xml->init) {
							foreach ($xml->init->module as $nodes) {
								foreach ($nodes->query as $q) {
									$q = str_replace('###', $config['db']['prefix'], (string)$q);
									$conn->query($q);
								}
							}
						}
					}
					
					/**
					 * Allows user to import sample data
					 * @since 2.0.4
					 */
					$importSampleData = $this->_request->getPost('importSampleData');
					if ($importSampleData) {
						$file = TOMATO_ROOT_DIR.self::SAMPLE_DATA;
						Tomato_Modules_Core_Services_Install_Importer::import($file);	
					}
				} catch (Exception $ex) {
					$ok = false;
					$this->_helper->getHelper('FlashMessenger')->addMessage($ex->getMessage());
				}
				
				if ($ok) {
					$this->_redirect($this->view->serverUrl().$this->view->url(array(), 'core_install_step3'));
				} else {
					$this->_redirect($this->view->serverUrl().$this->view->url(array(), 'core_install_step2'));
				}
			}
		}
	}
	
	public function step3Action() 
	{
		$baseUrl = $this->view->baseUrl();
		if ('' != $baseUrl) {
			$baseUrl = rtrim($baseUrl, '/');
			$baseUrl = ltrim($baseUrl, '/');
		}
		$url = ('' == $baseUrl) ? $this->view->APP_URL : $this->view->APP_URL.'/index.php';
		$this->view->assign('url', $url);	
		
		if ($this->_request->isPost()) {
			/**
			 * Generate install info 
			 */
			$file = TOMATO_APP_DIR.DS.'config'.DS.'app.ini';			
			$config = new Zend_Config_Ini($file, null, array('allowModifications' => true));
			$config = $config->toArray();
			$config['install']['date'] = date('m-d-Y H:i:s');
			
			/**
			 * Add version info
			 * @since 2.0.3
			 */
			$config['install']['version'] = Tomato_Core_Version::getVersion();
			
			$writer = new Zend_Config_Writer_Ini();
			$writer->write($file, new Zend_Config($config));
			
			$frontend = $this->_request->getPost('gotoFrontend');
			$backend = $this->_request->getPost('gotoBackend', null);
			
					
			if ($backend != null) {
				$this->_redirect($url.'/admin/');
			} else {
				$this->_redirect($url);
			}
		} else {
			/**
			 * Set random password for admin account
			 * @since 2.0.3
			 */
			$password = substr(md5(rand(100000, 999999)), 0, 8);
			$conn = Tomato_Core_Db_Connection::getMasterConnection();
			$conn->update(Tomato_Core_Db_Connection::getDbPrefix().'core_user', 
					array('password' => md5($password)), 
					array('user_name = "admin"'));
			$this->view->assign('password', $password);
		}		
	}
}
