<?php
/**
* @version		1.5.0
* @package		AceSEF Library
* @subpackage	Load library
* @copyright	2009-2010 JoomAce LLC, www.joomace.net
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// No Permission
defined('_JEXEC') or die('Restricted access');

// Import Joomla Libraries
jimport('joomla.html.parameter');

// Set defines
define('ACESEF_PACK', 'basic');
define('JPATH_ACESEF', JPATH_ROOT.DS.'components'.DS.'com_acesef');
define('JPATH_ACESEF_ADMIN', JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_acesef');

$library = dirname(__FILE__);

// Register classes
JLoader::register('AcesefCache', $library.'/cache.php');
JLoader::register('AcesefExtension', $library.'/extension.php');
JLoader::register('AcesefFactory', $library.'/factory.php');
JLoader::register('AcesefLanguage', $library.'/language.php');
JLoader::register('AcesefMetadata', $library.'/metadata.php');
JLoader::register('AcesefPlugin', $library.'/plugin.php');
JLoader::register('JRouterAcesef', $library.'/router.php');
JLoader::register('AcesefURI', $library.'/uri.php');
JLoader::register('AcesefUtility', $library.'/utility.php');

if (!class_exists('AceDatabase')) {
	JLoader::register('AceDatabase', $library.'/database.php');
}

if (ACESEF_PACK == 'pro') {
	JLoader::register('AcesefBookmarks', $library.'/bookmarks.php');
	JLoader::register('AcesefIlinks', $library.'/ilinks.php');
	JLoader::register('AcesefTags', $library.'/tags.php');
	JLoader::register('AcesefSitemap', $library.'/sitemap.php');
}

if (JFactory::getApplication()->isAdmin()) {
	JLoader::register('AcesefController', $library.'/controller.php');
	JLoader::register('AcesefModel', $library.'/model.php');
	JLoader::register('AcesefView', $library.'/view.php');
}