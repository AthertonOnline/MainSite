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
class AcesefViewSupport extends AcesefView {

	function display($tpl = null) {
		// Toolbar
		JToolBarHelper::title(JText::_('ACESEF_COMMON_SUPPORT'), 'acesef');		
		JToolBarHelper::back(JText::_('Back'), 'index.php?option=com_acesef');
		
		if (JRequest::getCmd('task', '') == 'translators') {
			$this->document->setCharset('iso-8859-9');
		}
		
		parent::display($tpl);
	}
}