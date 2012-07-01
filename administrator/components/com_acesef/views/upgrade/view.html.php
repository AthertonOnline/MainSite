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

// Upgrade View Class
class AceSEFViewUpgrade extends AcesefView {
	
	// View
	function view($tpl = null) {		
		// Toolbar
		JToolBarHelper::title(JText::_('ACESEF_COMMON_UPGRADE'), 'acesef');
		$this->toolbar->appendButton('Popup', 'help1', JText::_('Help'), 'http://www.joomace.net/support/docs/acesef/installation-upgrading/upgrade?tmpl=component', 650, 500);
		
		$versions = array(2);
		$version_info = AcesefUtility::getRemoteInfo();
		$versions['latest'] = $version_info['acesef'];
		$versions['installed'] = AcesefUtility::getXmlText(JPATH_ACESEF_ADMIN.'/acesef.xml', 'version');
		
		$this->assignRef('versions', $versions);
		
		parent::display($tpl);
	}
}
