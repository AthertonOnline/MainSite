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
class AcesefViewExtensions extends AcesefView {

	// Display extensions
	function view($tpl = null) {
		$toolbar = $this->get('ToolbarSelections');
		
		// Toolbar
		JToolBarHelper::title(JText::_('ACESEF_COMMON_EXTENSIONS'), 'acesef');
		$this->toolbar->appendButton('Confirm', JText::_('ACESEF_EXTENSIONS_VIEW_BTN_REMOVE_WARN'), 'uninstall', JText::_('Uninstall'), 'uninstall', true, false);
		JToolBarHelper::custom('save', 'save1.png', 'save1.png', JText::_('Save'), false);
		JToolBarHelper::divider();
		JToolBarHelper::spacer();
		$this->toolbar->appendButton('Custom', $toolbar->action);
		$this->toolbar->appendButton('Custom', $toolbar->selection);
		$this->toolbar->appendButton('Custom', $toolbar->button);
		JToolBarHelper::divider();
		$this->toolbar->appendButton('Popup', 'cache', JText::_('ACESEF_CACHE_CLEAN'), 'index.php?option=com_acesef&amp;controller=purgeupdate&amp;task=cache&amp;tmpl=component', 300, 380);
		JToolBarHelper::divider();
		$this->toolbar->appendButton('Popup', 'help1', JText::_('Help'), 'http://www.joomace.net/support/docs/acesef/user-manual/extensions?tmpl=component', 650, 500);
		
		// Get behaviors
		JHTML::_('behavior.tooltip');
		JHTML::_('behavior.modal', 'a.modal', array('onClose'=>'\function(){location.reload(true);}'));
		
		// Get data from the model
		$this->assignRef('lists',		$this->get('Lists'));
		$this->assignRef('info',		$this->get('Info'));
		$this->assignRef('params',		$this->get('Params'));
		$this->assignRef('items',		$this->get('Items'));
		$this->assignRef('pagination',	$this->get('Pagination'));

		parent::display($tpl);
	}
}
?>