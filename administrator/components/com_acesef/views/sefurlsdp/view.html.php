<?php
/**
* @version		1.5.0
* @package		AceSEF
* @subpackage	AceSEF
* @copyright	2009-2010 JoomAce LLC, www.joomace.net
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// No Permission
defined('_JEXEC') or die('Restricted Access');

// View Class
class AcesefViewSefUrlsDp extends AcesefView {

	// View URLs
	function view($tpl = null) {
		// Get data from the model
		$this->assignRef('lists',		$this->get('Lists'));
		$this->assignRef('items',		$this->get('Items'));
		$this->assignRef('pagination',	$this->get('Pagination'));
		$this->assignRef('toolbar',		$this->get('ToolbarSelections'));
		$this->assignRef('sef',			$this->get('SefUrl'));

		parent::display($tpl);
	}
}
?>