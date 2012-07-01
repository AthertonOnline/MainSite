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

// Import View Class
class AcesefViewRestoreMigrate extends AcesefView {

	// View
	function view($tpl = null) {		
		// Toolbar
		JToolBarHelper::title(JText::_('ACESEF_COMMON_RESTOREMIGRATE'), 'acesef');
		$this->toolbar->appendButton('Popup', 'help1', JText::_('Help'), 'http://www.joomace.net/support/docs/acesef/user-manual/restore-migrate?tmpl=component', 650, 500);
		
		parent::display($tpl);
	}
}