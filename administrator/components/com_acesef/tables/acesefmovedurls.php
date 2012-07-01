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

class TableAcesefMovedUrls extends JTable {

	var $id 	 	= null;
	var $url_new 	= null;
	var $url_old 	= null;
	var $published	= null;
	var $hits		= null;
	var $last_hit	= null;

	function __construct(&$db) {
		parent::__construct('#__acesef_urls_moved', 'id', $db);
	}
}