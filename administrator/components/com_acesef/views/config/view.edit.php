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
class AcesefViewConfig extends AcesefView {

	// Edit configuration
	function edit($tpl = null) {		
		// Toolbar
		JToolBarHelper::title(JText::_('ACESEF_COMMON_CONFIGURATION'), 'acesef');
		JToolBarHelper::custom('save', 'save1.png', 'save1.png', JText::_('Save'), false);
		JToolBarHelper::custom('apply', 'apply1.png', 'apply1.png', JText::_('Apply'), false);
		JToolBarHelper::custom('cancel', 'cancel1.png', 'cancel1.png', JText::_('Cancel'), false);
		JToolBarHelper::divider();
		$this->toolbar->appendButton('Popup', 'purgeupdate', JText::_('ACESEF_COMMON_PURGEUPDATE'), 'index.php?option=com_acesef&amp;controller=purgeupdate&amp;task=view&amp;tmpl=component', 470, 320);
		$this->toolbar->appendButton('Popup', 'cache', JText::_('ACESEF_CACHE_CLEAN'), 'index.php?option=com_acesef&amp;controller=purgeupdate&amp;task=cache&amp;tmpl=component', 300, 380);
		JToolBarHelper::divider();
		$this->toolbar->appendButton('Popup', 'help1', JText::_('Help'), 'http://www.joomace.net/support/docs/acesef/user-manual/configuration?tmpl=component', 650, 500);

		// Get behaviors
  		JHTML::_('behavior.mootools');
		JHTML::_('behavior.tooltip');

		// Import JPane
		jimport('joomla.html.pane');
		$pane =& JPane::getInstance('Tabs');

		// Import Editor
		$editor =& JFactory::getEditor();
		
		if ($this->AcesefConfig->sm_auto_cron_last == "") {
			$this->AcesefConfig->sm_auto_cron_last = time();
		}
		
		// Get data from the model
		$this->assignRef('pane',	$pane);
		$this->assignRef('editor',	$editor);
		$this->assignRef('lists',	$this->get('Lists'));
		
		parent::display($tpl) ;
	}
}
?>