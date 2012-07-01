<?php
/**
* @version		1.5.0
* @package		AceSEF
* @subpackage	AceSEF
* @copyright	2009-2010 JoomAce LLC, www.joomace.net
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// No Permission
defined('JPATH_BASE') or die('Restricted Access');

class TableAcesefBookmarks extends JTable {

	var $id 	 		= null;
	var $name 			= null;
	var $html 			= null;
	var $btype			= null;
	var $placeholder	= null;
	var $published		= null;

	function __construct(&$db) {
		parent::__construct('#__acesef_bookmarks', 'id', $db);
	}
}