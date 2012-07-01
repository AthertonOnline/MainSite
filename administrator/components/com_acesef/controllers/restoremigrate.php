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
class AcesefControllerRestoreMigrate extends AcesefController {

	// Main constructer
    function __construct() {
        parent::__construct('restoremigrate');
    }
	
	// Backup data
    function backup() {
		// Check token
		JRequest::checkToken() or jexit('Invalid Token');
		
		// Backup
		if(!$this->_model->backup()){
			JError::raiseWarning(500, JText::_('ACESEF_RESTOREMIGRATE_MSG_BACKUP_NO'));
		}
    }
    
	// Restore data
    function restore() {
		// Check token
		JRequest::checkToken() or jexit('Invalid Token');
		
		// Restore
		if(!$this->_model->restore()){
			$msg = JText::_('ACESEF_RESTOREMIGRATE_MSG_RESTORE_NO');
		} else {
			$msg = JText::_('ACESEF_RESTOREMIGRATE_MSG_RESTORE_OK');
		}
		
		// Return
		parent::route($msg);
    }
}
?>