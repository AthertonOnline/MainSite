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
class AcesefViewMovedUrls extends AcesefView {

	// Edit URL
	function edit($tpl = null) {
		// Get data from model
		$model =& $this->getModel();
		$row = $model->getEditData('AcesefMovedUrls');
		
		// Toolbar
		JToolBarHelper::title(JText::_('ACESEF_COMMON_URLS_MOVED').': '.$row->url_old, 'acesef');
		JToolBarHelper::custom('editSave', 'save1.png', 'save1.png', JTEXT::_('Save'), false);
		JToolBarHelper::custom('editApply', 'apply1.png', 'apply1.png', JTEXT::_('Apply'), false);
		JToolBarHelper::custom('editCancel', 'cancel1.png', 'cancel1.png', JTEXT::_('Cancel'), false);
		JToolBarHelper::divider();
		$this->toolbar->appendButton('Popup', 'help1', JText::_('Help'), 'http://www.joomace.net/support/docs/acesef/user-manual/urls?tmpl=component', 650, 500);
		
		// Options array
		$select = array();
		$select[] = JHTML::_('select.option', '1', JTEXT::_('Yes'));
		$select[] = JHTML::_('select.option', '0', JTEXT::_('No'));
		
		// Published list
   	   	$lists['published'] = JHTML::_('select.genericlist', $select, 'published', 'class="inputbox" size="1 "','value', 'text', $row->published);
		
		// Get behaviors
		JHTML::_('behavior.modal', 'a.modal', array('onClose'=>'\function(){location.reload(true);}'));
		
		// Get jQuery
		if ($this->AcesefConfig->jquery_mode == 1) {
			$this->document->addScript('components/com_acesef/assets/js/jquery-1.4.2.min.js');
			$this->document->addScript('components/com_acesef/assets/js/jquery.bgiframe.min.js');
			$this->document->addScript('components/com_acesef/assets/js/jquery.autocomplete.js');
		}
		
		// Assign values
		$this->assignRef('row', 	$row);
		$this->assignRef('lists',	$lists);

		parent::display($tpl);
	}
	
	function getSefURL($id) {
		$url = "";
		
		if (is_numeric($id)) {
			$url = AceDatabase::loadResult("SELECT url_sef FROM #__acesef_urls WHERE id = {$id}");
		}
		
		return $url;
	}
}
?>