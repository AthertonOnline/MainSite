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
class AcesefControllerMetadata extends AcesefController {

	// Main constructer
	function __construct() 	{
		parent::__construct('metadata');
	}
	
	// Apply changes
	function apply() {
		// Check token
		JRequest::checkToken() or jexit('Invalid Token');
		
		// Save
		$this->_model->apply();
		
		// Return
		parent::route(JTEXT::_('ACESEF_METADATA_SAVED'));
	}
	
	function generateMetadata() {
		// Check token
		JRequest::checkToken() or jexit('Invalid Token');
	
		// Action
		if ($this->_model->generateMetadata()) {
			$msg = JText::_('ACESEF_METADATA_GENERATED_OK');
		} else {
			$msg = JText::_('ACESEF_METADATA_GENERATED_NO');
		}
		
		// Return
		parent::route($msg);
	}
	
	function deleteEmptyReal() {
		// Check token
		JRequest::checkToken() or jexit('Invalid Token');
		
		$where = parent::_getWhere($this->_model);
	
		// Action
		if ($this->_model->deleteEmptyReal($where)) {
			$msg = JText::_('ACESEF_COMMON_RECORDS_DELETED');
		} else {
			$msg = JText::_('ACESEF_COMMON_RECORDS_DELETED_NOT');
		}
		
		// Return
		parent::route($msg);
	}
	
	function clean() {
		// Check token
		JRequest::checkToken() or jexit('Invalid Token');
		
		// Get where
		$where = parent::_getWhere($this->_model);
		
		$meta_fields = JRequest::getVar('fields', 'all', 'post');
		
		$fields = array();
		if ($meta_fields == 'all') {
			$fields[] = 'title';
			$fields[] = 'description';
			$fields[] = 'keywords';
		} else {
			$fields[] = $meta_fields;
		}
		
		// Action
		if (!AcesefMetadata::clean($where, $fields)) {
			$msg = JText::_('ACESEF_METADATA_CLEANED_NO');
		} else {
			$msg = JText::_('ACESEF_METADATA_CLEANED_OK');
		}
		
		// Return
		parent::route();
	}
	
	function update() {
		// Check token
		JRequest::checkToken() or jexit('Invalid Token');
		
		// Get where
		$where = parent::_getWhere($this->_model, "m.");
		
		$meta_fields = JRequest::getVar('fields', 'all', 'post');
		
		$fields = array();
		if ($meta_fields == 'all') {
			$fields[] = 'title';
			$fields[] = 'description';
			$fields[] = 'keywords';
		} else {
			$fields[] = $meta_fields;
		}
		
		// Action
		$where = str_replace(' WHERE ', ' AND ', $where);
		$msg = AcesefMetadata::update($where, $fields);
		
		// Return
		parent::route($msg);
	}
	
	function cache() {
		// Check token
		JRequest::checkToken() or jexit('Invalid Token');
	
		// Action
		parent::updateCache($this->_context, 'url_sef', '*', 1, $this->_model);
		
		// Return
		parent::route();
	}
	
	function uncache() {
		// Check token
		JRequest::checkToken() or jexit('Invalid Token');
	
		// Action
		parent::updateCache($this->_context, 'url_sef', '*', 0, $this->_model);
		
		// Return
		parent::route();
	}
}
?>