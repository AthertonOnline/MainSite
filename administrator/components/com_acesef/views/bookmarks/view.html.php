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
class AcesefViewBookmarks extends AcesefView {

	// View URLs
	function view($tpl = null) {
		JToolBarHelper::title(JText::_('ACESEF_COMMON_BOOKMARKS' ), 'acesef');
		parent::display($tpl);
	}
}
?>