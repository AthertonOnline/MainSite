<?php

if (!isset ($_GET['admin']))
	die ('restricted access');

define( '_JEXEC', 1 );
define( 'DS', DIRECTORY_SEPARATOR );

$jpathbase15 = str_replace(DS.'plugins'.DS.'editors'.DS.'yoonique_markdown','',dirname(__FILE__));
$jpathbase16 = str_replace(DS.'plugins'.DS.'editors'.DS.'yoonique_markdown'.DS.'yoonique_markdown','',dirname(__FILE__));

if ($_GET['admin']==1) {

	define('JPATH_BASE15', $jpathbase15 . DS . 'administrator');
	define('JPATH_BASE16', $jpathbase16 . DS . 'administrator');

} else {

	define('JPATH_BASE15', $jpathbase15);
	define('JPATH_BASE16', $jpathbase16);

}

if (file_exists(JPATH_BASE15 .DS.'includes'.DS.'defines.php') && file_exists(JPATH_BASE15 .DS.'includes'.DS.'framework.php')) {

	define('JPATH_BASE', JPATH_BASE15);
	require_once ( JPATH_BASE15 .DS.'includes'.DS.'defines.php' );
	require_once ( JPATH_BASE15 .DS.'includes'.DS.'framework.php' );

} else {

	define('JPATH_BASE', JPATH_BASE16);
	require_once ( JPATH_BASE16 .DS.'includes'.DS.'defines.php' );
	require_once ( JPATH_BASE16 .DS.'includes'.DS.'framework.php' );

}



if ($_GET['admin']==1)
	$mainframe =& JFactory::getApplication('administrator');
else
	$mainframe =& JFactory::getApplication('site');

JRequest::checkToken( 'get' ) or die( 'Invalid Token' );

require_once "markdown.php";

if (isset($_POST['data'])) {
	$markdown = New MarkdownExtra_Parser;
	echo $markdown->transform( $_POST['data'] );
}
