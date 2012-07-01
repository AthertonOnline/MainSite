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

class TableAcesefIlinks extends JTable {

	var $id 	 	= null;
	var $word 		= null;
	var $link 		= null;
	var $published	= null;
	var $nofollow	= null;
	var $iblank		= null;
	var $ilimit		= null;

	function __construct(&$db) {
		parent::__construct('#__acesef_ilinks', 'id', $db);
	}
}