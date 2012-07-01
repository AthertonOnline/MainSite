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
class AcesefControllerPurgeUpdate extends AcesefController {
	
	// Main constructer
	function __construct() {
        parent::__construct('purgeupdate');
    }
	
	// Cache
	function cache() {
		$view = $this->getView('PurgeUpdate', 'cache');
		$view->setModel($this->_model, true);
		$view->display('cache');
	}
	
	// Update function
    function deleteUpdate() {
		// Check token
		JRequest::checkToken() or jexit('Invalid Token');
		
		// Get buttons
		$deleteURLs		= JRequest::getVar('deleteurls', 0, 'post');
		$updateURLs 	= JRequest::getVar('updateurls', 0, 'post');
		$deleteMeta 	= JRequest::getVar('deletemeta', 0, 'post');
		$updateMeta 	= JRequest::getVar('updatemeta', 0, 'post');
		
		// Get model
		$model =& $this->getModel('PurgeUpdate');
		
		// Delete URLs
		if ($deleteURLs) {
			if ($model->deleteURLs()) {
				JFactory::getApplication()->enqueueMessage(JText::_('ACESEF_PURGE_PURGED'));
			} else {
				JError::raiseWarning(500, JText::_('ACESEF_PURGE_NOT_PURGED'));
			}
		}
		
		// Update URLs
		if ($updateURLs) {
			$count = "";
			$count = $model->updateURLs();
			JFactory::getApplication()->enqueueMessage(JText::_('ACESEF_COMMON_UPDATED_URLS').' '.$count);
		}
		
		// Delete Meta
		if ($deleteMeta) {
			if ($model->deleteUpdateMeta('delete')) {
				JFactory::getApplication()->enqueueMessage(JText::_('ACESEF_PURGE_PURGED'));
			} else {
				JError::raiseWarning(500, JText::_('ACESEF_PURGE_NOT_PURGED'));
			}
		}
		
		// Update Meta
		if ($updateMeta) {
			$model->deleteUpdateMeta('update');
			JFactory::getApplication()->enqueueMessage(JText::_('ACESEF_COMMON_UPDATED_META'));
		}
    }
	
	// Show cache
	function cleanCache() {
		// Check token
		JRequest::checkToken() or jexit('Invalid Token');
		
		if (!$this->_model->cleanCache()) {
			return JError::raiseWarning(500, JText::_('ACESEF_CACHE_CLEANED_NOT'));
		} else {
			JFactory::getApplication()->enqueueMessage(JText::_('ACESEF_CACHE_CLEANED'));
		}
	}
}
?>