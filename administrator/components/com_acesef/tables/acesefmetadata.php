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

class TableAcesefMetadata extends JTable {

	var $id 	 		= null;
	var $url_sef 		= null;
	var $published		= null;
	var $title 			= null;
	var $description	= null;
	var $keywords		= null;
	var $lang			= null;
	var $robots			= null;
	var $googlebot		= null;
	var $canonical		= null;

	function __construct(&$db) {
		parent::__construct('#__acesef_metadata', 'id', $db);
	}
}