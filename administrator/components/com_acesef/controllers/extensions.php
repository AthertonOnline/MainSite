<?php
/**
* @version		1.5.0
* @package		AceSEF
* @subpackage	AceSEF
* @copyright	2009-2010 JoomAce LLC, www.joomace.net
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// No permission
defined('_JEXEC') or die('Restricted Access');

// Controller Class
class AcesefControllerExtensions extends AcesefController {

	// Main constructer
	function __construct() 	{
		parent::__construct('extensions');
	}

	// Display
	function view() {
		$this->_model->checkComponents();
		
		$view = $this->getView(ucfirst($this->_context), 'html');
		$view->setModel($this->_model, true);
		$view->view();
	}
	
	// Uninstall extensions
	function uninstall() {
		// Check token
		JRequest::checkToken() or jexit('Invalid Token');
		
		// Uninstall selected extensions
		$this->_model->uninstall();

		// Return
		parent::route(JText::_('ACESEF_EXTENSIONS_VIEW_REMOVED'));
	}
	
	// Save changes
	function save() {
		// Check token
		JRequest::checkToken() or jexit('Invalid Token');
		
		// Save
		$this->_model->save();
		
		// Return
		parent::route(JText::_('ACESEF_EXTENSIONS_VIEW_SAVED'));
	}
	
	// Save changes & Delete URLs
	function saveDeleteURLs() {
		// Check token
		JRequest::checkToken() or jexit('Invalid Token');
		
		$ids = parent::_getIDs($this->_context, $this->_model);
		
		foreach ($ids as $id) {
			$this->_model->save($id, 'deleteURLs');
		}
		// Return
		parent::route(JText::_('ACESEF_EXTENSIONS_VIEW_SAVED_URL_PURGED'));
	}
	
	// Save changes & Update URLs
	function saveUpdateURLs() {
		// Check token
		JRequest::checkToken() or jexit('Invalid Token');
		
		$count = 0;
		$ids = parent::_getIDs($this->_context, $this->_model);
		
		foreach ($ids as $id) {
			$urls = $this->_model->save($id, 'updateURLs');
			$count += $urls;
		}
		// Return
		parent::route(JText::_('ACESEF_COMMON_UPDATED_URLS').' '.$count);
	}
	
	// Install a new extension
	function installUpgrade() {
		// Check token
		JRequest::checkToken() or jexit('Invalid Token');
		
		if(!$this->_model->installUpgrade()){
			JError::raiseWarning('1001', JText::_('ACESEF_EXTENSIONS_VIEW_NOT_INSTALLED'));
		} else {
			parent::route(JText::_('ACESEF_EXTENSIONS_VIEW_INSTALLED'));
		}
	}
}
?>