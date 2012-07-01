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
class AcesefControllerMovedUrls extends AcesefController {

	// Main constructer
	function __construct() {
		parent::__construct('movedurls', 'urls_moved');
	}
	
	function cache() {
		// Check token
		JRequest::checkToken() or jexit('Invalid Token');
	
		// Action
		parent::updateCache($this->_table, 'url_old', '*', 1, $this->_model);
		
		// Return
		parent::route();
	}
	
	function uncache() {
		// Check token
		JRequest::checkToken() or jexit('Invalid Token');
	
		// Action
		parent::updateCache($this->_table, 'url_old', '*', 0, $this->_model);
		
		// Return
		parent::route();
	}
	
	//
	// Edit methods
	//
	
	// Save changes
	function editSave() {
		// Check token
		JRequest::checkToken() or jexit('Invalid Token');
		
		// Get post
		$post = JRequest::get('post');
		
		// Save record
		if (!parent::saveRecord($post, 'AcesefMovedUrls', $post['id'])) {
			return JError::raiseWarning(500, JText::_('ACESEF_COMMON_RECORD_SAVED_NOT'));
		} else {
			$sefid = JRequest::getInt('sefid', 0);
			if (!empty($sefid)) {
				AceDatabase::query("DELETE FROM #__acesef_urls WHERE id = {$sefid}");
			}
			
			if ($post['modal'] == '1') {
				// Display message
				JFactory::getApplication()->enqueueMessage(JText::_('ACESEF_COMMON_RECORD_SAVED'));
			} else {
				// Return
				parent::route(JText::_('ACESEF_COMMON_RECORD_SAVED'));
			}
		}
	}
}
?>