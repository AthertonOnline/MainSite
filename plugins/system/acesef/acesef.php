<?php
/**
* @version		1.5.0
* @package		AceSEF
* @subpackage	AceSEF
* @copyright	2009-2010 JoomAce LLC, www.joomace.net
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// No Permission
defined('_JEXEC') or die('Restricted access');

class plgSystemAcesef extends JPlugin {

	function __construct(& $subject, $config) {
		parent::__construct($subject, $config);
		
		if (version_compare(PHP_VERSION, '5.2.0', '>=')) {
			// Get config object
			$factory_file = JPATH_ADMINISTRATOR.'/components/com_acesef/library/factory.php';

			if (file_exists($factory_file)) {
				require_once($factory_file);
				$this->AcesefConfig = AcesefFactory::getConfig();
			}
		}
	}

	function onAfterInitialise() {
		jimport('joomla.language.helper');
		
		if ($this->AcesefConfig->multilang == 1) {
			JFactory::getApplication()->set('menu_associations', 1);
		}
		
		if (!self::_systemCheckup(false)) {
			return true;
		}
		
		self::_loadLibrary();

		AcesefPlugin::onAfterInitialise();
		
		return true;
	}

    function onAfterRoute() {
		if (!self::_systemCheckup()) {
			return true;
		}
		
		self::_loadLibrary();
		
		AcesefPlugin::onAfterRoute();
		
		return true;
    }

    function onAfterDispatch() {
		if (!self::_systemCheckup()) {
			return true;
		}
		
		self::_loadLibrary();
		
		AcesefPlugin::onAfterDispatch();
		
		return true;
    }
	
	function onContentPrepare($context, &$article, &$params = null, $limitstart = 0) {
		if (!self::_systemCheckup()) {
			return true;
		}
		
		self::_loadLibrary();
		
		AcesefPlugin::onContentPrepare($article->text);
		
		return;
	}
    
	function onAfterRender() {
		if (!self::_systemCheckup()) {
			return true;
		}
		
		self::_loadLibrary();
		
		AcesefPlugin::onAfterRender();

		return true;
	}
	
	function onAcesefTags(&$text) {
		if (!self::_systemCheckup()) {
			return;
		}
		
		self::_loadLibrary();
		
		AcesefPlugin::onAcesefTags($text);
		
		return;
	}
	
	function onAcesefIlinks(&$text) {
		if (!self::_systemCheckup()) {
			return;
		}
		
		self::_loadLibrary();
		
		AcesefPlugin::onAcesefIlinks($text);
		
		return;
	}
	
	function onAcesefBookmarks(&$text) {
		if (!self::_systemCheckup()) {
			return;
		}
		
		self::_loadLibrary();
		
		AcesefPlugin::onAcesefBookmarks($text);
		
		return;
	}
	
	function _systemCheckup($check_raw = true) {
		if (version_compare(PHP_VERSION, '5.2.0', '<')) {
			JError::raiseWarning('100', JText::sprintf('AceSEF requires PHP 5.2.x to run, please contact your hosting company.'));
			return false;
		}
		
		// Is backend
		if (JFactory::getApplication()->isAdmin()) {
			return false;
		}

		// Joomla SEF is disabled
		if (!JFactory::getConfig()->getValue('sef')) {
			return false;
		}

		// Check if AceSEF is enabled
		if ($this->AcesefConfig->mode == 0) {
			return false;
		}
		
		// Is plugin enabled
		if (!JPluginHelper::isEnabled('system', 'acesef')) {
			return false;
		}
		
		$raw = ((JRequest::getCmd('format') == 'raw') || (JRequest::getCmd('format') == 'xml') || (JRequest::getCmd('tmpl') == 'raw'));
		if ($check_raw && $raw) {
			return false;
		}
		
		return true;
	}
	
	function _loadLibrary() {
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_acesef'.DS.'library'.DS.'loader.php');
	}
}
?>