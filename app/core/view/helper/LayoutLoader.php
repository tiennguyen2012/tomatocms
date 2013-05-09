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
 * @version 	$Id: LayoutLoader.php 1541 2010-03-10 07:10:45Z huuphuoc $
 */

class Tomato_Core_View_Helper_LayoutLoader extends Zend_View_Helper_Abstract 
{
	const CONTAINER_ID_PREFIX 	= 't_g_container_';
	const CONTAINER_CLASS 		= 't_g_widget_container';
	const WIDGET_CLASS 			= 't_g_widget';
	
	/**
	 * Use 960 grid framework for layout with total of 12 columns
	 */
	const TOTAL_COLUMNS = 12;
	private static $_posToClass = array('first' => 'alpha', 'last' => 'omega'); 
	
	private $_layoutMap = null;
	
	private $_resources = array('javascript' => array(), 'css' => array());
	private $_inlineScripts = array();
	
	public function __construct() 
	{
		$layout = TOMATO_APP_DIR.DS.'config'.DS.'layout.ini';
		if (!file_exists($layout)) {
			return;
		}
		$this->_layoutMap = new Zend_Config_Ini($layout, 'layouts');
		$this->_layoutMap = $this->_layoutMap->layouts->toArray();
	}
	
	public function layoutLoader() 
	{
		if (null == $this->_layoutMap) {
			return $this->view->layout()->content;
		}
		
		// We don't need to sort the layouts by priority
		// because it was sorted already when loaded from database
//		$sorter = new Tomato_Core_Layout_Sorter($this->_layoutMap);
//		$this->_layoutMap = $sorter->sortByPriority();
		
		/**
		 * Get the layout configuration file based on the current request
		 */
		// Fixed error: Could not load the layout if user install TomatoCMS
		// in subdirectory of web root
		//$currUri = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
		$currUri = Zend_Controller_Front::getInstance()->getRequest()->getPathInfo();
		$currUri = Tomato_Core_Utility_String::normalizeUri($currUri);
		$layoutFile = null;
		$requestParams = array();
		$layoutName = null;
		
		foreach ($this->_layoutMap as $layoutName => $value) {
			$uri = Tomato_Core_Utility_String::normalizeUri($value['url']);
			/*
			if (!get_magic_quotes_gpc()) {
				$uri = stripslashes($uri);
			}
			*/
		
			if ($value['type'] == 'static' && $currUri == $uri) {
				$layoutFile = $this->_layoutMap[$layoutName]['file'];
				break;
			} elseif ($value['type'] == 'regex') {
				$pattern = '/'.str_replace('/', '\/', '^'.$uri).'/';
				if (preg_match($pattern, $currUri, $matches)) {
					$layoutFile = $this->_layoutMap[$layoutName]['file'];
					if (isset($this->_layoutMap[$layoutName]['map'])) {
						foreach ($this->_layoutMap[$layoutName]['map'] as $index => $paramName) {
							$requestParams[$paramName] = $matches[$index];
							$this->view->assign($paramName, $matches[$index]);
						}
					}
					
					break;
				}
			}
		}
		if (null == $layoutFile) {
			return $this->view->layout()->content;
		}
		$file = Zend_Layout::getMvcInstance()->getLayoutPath().DS.$layoutFile;
		if (!file_exists($file)) {
			throw new Tomato_Core_View_Exception('The layout configuration file '.$layoutFile.' does not exist');
		}
		
		/**
		 * Process layout file
		 */
		ob_start();
		/*
		$cacheFile = TOMATO_TEMP_DIR.DS.'cache'.DS.'layout_'.$layoutName.'.php';
		if (file_exists($cacheFile)) {
			include $cacheFile;
			return ob_get_clean();
		}
		$f = fopen($cacheFile, 'w');
		*/
		$reader = new XMLReader();
		$reader->open($file, 'UTF-8');
		
		$module = null;
		$widgetName = null;
		$load = null;
		$containerId = 0;
		$widgetContainerId = 0;
		$tabId= 0; 
		$tabContainerId = 0;
		$params = array();
		$params2 = array();
		
		$paramName = null;
		$from = null;
		
		$search = array('{APP_URL}', '{APP_STATIC_SERVER}');
		$replace = array($this->view->baseUrl(), $this->view->APP_STATIC_SERVER);
		
		while ($reader->read()) {
			$str = $reader->nodeType.'_'.$reader->localName;
			switch ($str) {
				/** 
				 * Meet the open tag
				 */
				case XMLReader::ELEMENT.'_layout':
					break;
				case XMLReader::ELEMENT.'_container':
					$containerId++;
					$widgetContainerId = 0;
					$cols = $reader->getAttribute('cols');
					if ($cols == null) {
						$class = '';
					} else {
						$class = (($position = $reader->getAttribute('position')) != null) 
									? "grid_".$cols." ".self::$_posToClass[$position]
									: "grid_".$cols;
					}
								
					if ($cols == self::TOTAL_COLUMNS) {
						$class .= " t_space_bottom";
					}
					$cssClass = $reader->getAttribute('cssClass');
					if (isset($cssClass)) {
						$class .= ' '.$cssClass;
					}
					$str = '<div class="'.self::CONTAINER_CLASS.' '.$class.'" id="'.self::CONTAINER_ID_PREFIX.$containerId.'">'; 
					echo $str;
					/*fwrite($f, $str);*/
					break;
				case XMLReader::ELEMENT.'_tabs':
					$tabContainerId++;
					$tabId = 0;
					echo '<div id="t_g_tab_container_'.$tabContainerId.'">';
					$xml = new SimpleXMLElement($reader->readOuterXml());
					echo '<ul>';
					for ($i = 0; $i < count($xml->tab); $i++) {
						$attrs = $xml->tab[$i]->attributes();
						echo '<li><a href="#t_g_tab_'.$tabContainerId.'_'.($i + 1).'"><span>'.(string)$attrs['label'].'</span></a></li>';
					}
					echo '</ul>';
					echo '<script type="text/javascript">$(document).ready(function() { $("#t_g_tab_container_'.$tabContainerId.'").tabs(); });</script>';
					break;
				case XMLReader::ELEMENT.'_tab':
					$tabId++;
					echo '<div id="t_g_tab_'.$tabContainerId.'_'.$tabId.'">';
					break;	
				case XMLReader::ELEMENT.'_defaultOutput':
					// Render the script normally
					echo $this->view->layout()->content;
					/*fwrite($f, '<?php echo $this->view->layout()->content; ?>');*/
					break;
				case XMLReader::ELEMENT.'_widget':
					$module = $reader->getAttribute('module');
					$widgetName = $reader->getAttribute('name');
					$load = $reader->getAttribute('load');
					if (!isset($load)) {
						$load = 'php';
					}
					
					$cssClass = $reader->getAttribute('cssClass');
					$cssClass = (!isset($cssClass)) ? '' : ' '.$cssClass;
					
					$widgetContainerId++;
					$divId = self::CONTAINER_ID_PREFIX.$containerId.'_'.$widgetContainerId;
					$params['container'] = $divId;				
					$params2['container'] = '"'.$divId.'"';
					$str = '<div class="'.self::WIDGET_CLASS.''.$cssClass.'" id="'.$divId.'">';
					echo $str;
					/*fwrite($f, $str);*/
					break;
				case XMLReader::ELEMENT.'_params':
					break;
				case XMLReader::ELEMENT.'_param':
					$paramName = $reader->getAttribute('name');
					$from = ($reader->getAttribute('from') == null) 
								? $paramName : $reader->getAttribute('from');
					if ($reader->getAttribute('type') == 'global' && isset($requestParams[$from])) {
						$params[$paramName] = $requestParams[$from];
						$params2[$paramName] = '$this->view->'.$from;
					}
					break;
				case XMLReader::ELEMENT.'_cache':
					$params['___cacheLifetime'] = $params2['___cacheLifetime'] = $reader->getAttribute('lifetime');
					break;
				case XMLReader::CDATA:
					$paramValue = ($reader->value == null) 
								? $reader->readString() : $reader->value;
					if ($reader->getAttribute('type') != 'global' || !isset($requestParams[$from])) {
						$params[$paramName] = $paramValue;
						$params2[$paramName] = '"'.addslashes($paramValue).'"';
					}
					break;
				case XMLReader::ELEMENT.'_resources':
					break;
				case XMLReader::ELEMENT.'_resource':
					$resourceType = $reader->getAttribute('type');
					$src = $reader->getAttribute('src');
					
					if (in_array($resourceType, array('javascript', 'css'))) {
						if (!in_array($src, $this->_resources[$resourceType])) {
							$this->_resources[$resourceType][] = $src;				
						}
						
						// TODO: Load CSS at the head section by javascript
						if ($resourceType == 'css') {
							echo '<link rel="stylesheet" type="text/css" href="'.str_replace($search, $replace, $src).'" />';
//							echo $this->view->headLink()->appendStylesheet(str_replace($search, $replace, $src));
						}
					} else {
						throw new Tomato_Core_View_Exception('Does not support '.$resourceType.' for type of resource');
					}
					break;
					
				/**
				 * Meet the close tag
				 */
				case XMLReader::END_ELEMENT.'_layout':
					break;
				case XMLReader::END_ELEMENT.'_container':
					echo '</div>';
					/*fwrite($f, '</div>');*/
					break;
				case XMLReader::END_ELEMENT.'_tabs':
					echo '</div>';
					break;
				case XMLReader::END_ELEMENT.'_tab':
					echo '</div>';
					break;
				case XMLReader::END_ELEMENT.'_widget':
					if ($module != null) {
						if ($load == 'php') {
							echo $this->view->widget($module, $widgetName, $params);
							/*fwrite($f, '<?php echo $this->view->widget("'.$module.'", "'.$widgetName.'", '.$this->_arrayToString($params2).'); ?>');*/
						} elseif ($load == 'ajax') {
							// Load widget by ajax call
							$data = Zend_Json::encode($params);
							$id = self::CONTAINER_ID_PREFIX.$containerId.'_'.$widgetContainerId;
							$this->_inlineScripts[] = "Tomato.Core.Widget.Loader.queue('$module', '$widgetName', '$data', '$id');"; 
						}
					}
					// Reset variables
					$module = null;
					$widgetName = null;
					$load = null;
					$paramName = null;
					$from = null;
					$params = $params2 = array();
					echo '</div>';
					/*fwrite($f, '</div>');*/
					break;
				case XMLReader::END_ELEMENT.'_params':
					break;
				case XMLReader::END_ELEMENT.'_param':
					break;
			}
		}
		$reader->close();
		
		// Improve performance by placing the script section at the bottom of page
		foreach ($this->_resources['javascript'] as $resource) {
			$resource = str_replace($search, $replace, $resource);
			$str = '<script type="text/javascript" src="'.$resource.'"></script>';
			echo $str;
			/*fwrite($f, $str);*/
		}
		echo '<script type="text/javascript">';
		/*fwrite($f, '<script type="text/javascript">');*/
		foreach ($this->_inlineScripts as $script) {
			echo $script;
			/*fwrite($f, $script);*/
		}
		echo '</script>';
		/*
		fwrite($f, '</script>');
		fclose($f);
		*/
		$return = ob_get_clean();
		return $return;
	}
	
	private function _arrayToString($param) 
	{
		$str = 'array(';
		foreach ($param as $key => $value) {
			$str .= '"'.$key.'" => '.$value.', ';
		}
		$str = substr($str, 0, -2).')';
		return $str;
	}
}