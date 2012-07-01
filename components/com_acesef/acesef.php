<?php
/**
* @version		1.5.0
* @package		AceSEF
* @subpackage	AceSEF
* @copyright	2009-2010 JoomAce LLC, www.joomace.net
* @license		GNU GPL
*/

//No Permision
defined('_JEXEC') or die('Restricted access');

$lang =& JFactory::getLanguage();
$lang->load('com_acesef', JPATH_SITE, 'en-GB', true);
$lang->load('com_acesef', JPATH_SITE, $lang->getDefault(), true);
$lang->load('com_acesef', JPATH_SITE, null, true);

// Get controller
if($controller = JRequest::getCmd('view')) {
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
$controller->execute(JRequest::getCmd('view'));

// Redirect if set by the controller
$controller->redirect();