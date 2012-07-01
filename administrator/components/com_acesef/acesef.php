<?php
/**
* @version		1.7.0
* @package		AceSEF
* @subpackage	AceSEF
* @copyright	2009-2011 JoomAce LLC, www.joomace.net
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// No Permission
defined('_JEXEC') or die('Restricted access');

// Access check
if (!JFactory::getUser()->authorise('core.manage', 'com_acesef')) {
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

JHTML::_('behavior.framework');

$lang =& JFactory::getLanguage();
$lang->load('com_acesef', JPATH_ADMINISTRATOR, 'en-GB', true);
$lang->load('com_acesef', JPATH_ADMINISTRATOR, $lang->getDefault(), true);
$lang->load('com_acesef', JPATH_ADMINISTRATOR, null, true);

// Load AceSEF library
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_acesef'.DS.'library'.DS.'loader.php');

// Set the tables directory
JTable::addIncludePath(JPATH_ACESEF_ADMIN.DS.'tables');

// Get controller
if($controller = JRequest::getCmd('controller')) {
	$path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
	if(file_exists($path)) {
	    require_once($path);
	} else {
	    $controller = '';
	}
}

$classname  = 'AcesefController'.$controller;
$controller = new $classname();

// Perform the Request task
$controller->execute(JRequest::getCmd('task'));

// Redirect if set by the controller
$controller->redirect();