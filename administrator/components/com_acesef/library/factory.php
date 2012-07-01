<?php
/**
* @version		1.6.0
* @package		AceSEF Library
* @subpackage	Factory
* @copyright	2009-2011 JoomAce LLC, www.joomace.net
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// No Permission
defined('_JEXEC') or die('Restricted Access');

// Factory class
abstract class AcesefFactory {
	
	static function &getConfig() {
		static $instance;
		
		if (!is_object($instance)) {
			$instance = new stdClass();
			
			$db =& JFactory::getDBO();
			$db->setQuery('SELECT params FROM #__extensions WHERE element = "com_acesef" AND type = "component"');
			$params = $db->loadResult();
			
			$params = explode("\n", $params);
			if (!empty($params)) {
				$array_keys = array('force_ssl', 'sm_auto_components', 'tags_components', 'tags_auto_components', 'ilinks_components', 'bookmarks_components');
				
				foreach ($params as $param){
					$pos = strpos($param, '=');
					
					$key = trim(substr($param, 0, $pos));
					$value = trim(substr($param, $pos + 1));
					
					if (empty($key)) {
						continue;
					}
					
					if (!isset($value)) {
						$value = '';
					}
					
					if (in_array($key, $array_keys)) {
						$value = json_decode(stripslashes($value), true);
					}
					
					$instance->$key = $value;
				}
			}
		}
		
		return $instance;
	}
	
	static function &getCache($lifetime = '315360000') {
		static $instances = array();
		
		if (!isset($instances[$lifetime])) {
			require_once(JPATH_ADMINISTRATOR.'/components/com_acesef/library/cache.php');
			
			$instances[$lifetime] = new AcesefCache($lifetime);
		}
		
		return $instances[$lifetime];
	}
	
	static function getTable($name) {
		static $tables = array();
		
		if (!isset($tables[$name])) {
			JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_acesef'.DS.'tables');
			$tables[$name] =& JTable::getInstance($name, 'Table');
		}
		
		return $tables[$name];
	}
	
	static function &getExtension($option) {
		static $instances = array();
		
		if (!isset($instances[$option])) {
			jimport('joomla.html.parameter');
			require_once(JPATH_ADMINISTRATOR.'/components/com_acesef/library/extension.php');
			
			$file = JPATH_ADMINISTRATOR.'/components/com_acesef/extensions/'.$option.'.php';
			
			if (!file_exists($file)) {
				$instances[$option] = null;
				
				return $instances[$option];
			}
			
			require_once($file);
			
			$cache = self::getCache();
			$ext_params = $cache->getExtensionParams($option);
			if (!$ext_params) {
				$ext_params = new JParameter('');
			}
			
			$class_name = 'AceSEF_'.$option;
			
			$instances[$option] = new $class_name($ext_params);
		}
		
		$instances[$option]->resetMetadata();
		$instances[$option]->skipMenu(false);
		
		return $instances[$option];
	}

    static function &getClass($class) {
        static $instances = array();

		if (!isset($instances[$class])) {
			require_once(JPATH_ADMINISTRATOR.'/components/com_acesef/library/'.$class.'.php');

            $class_name = 'Acesef'.ucfirst($class);
			$instance[$class] = new $class_name();
		}

		return $instances[$class];
    }
}