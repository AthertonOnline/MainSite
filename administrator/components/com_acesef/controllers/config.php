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
class AcesefControllerConfig extends AcesefController {

	// Main constructer
 	function __construct() {
		parent::__construct('config');
	}
	
	// Save changes
	function save() {
		// Check token
		JRequest::checkToken() or jexit('Invalid Token');
		
		$this->_model->save();
		
		$this->setRedirect('index.php?option=com_acesef', JTEXT::_('ACESEF_CONFIG_SAVED'));
	}
	
	// Apply changes
	function apply() {
		// Check token
		JRequest::checkToken() or jexit('Invalid Token');
		
		$this->_model->save();
		
		$this->setRedirect('index.php?option=com_acesef&controller=config&task=edit', JTEXT::_('ACESEF_CONFIG_SAVED'));
	}
	
	// Cancel saving changes
	function cancel() {
		// Check token
		JRequest::checkToken() or jexit('Invalid Token');
		
		$this->setRedirect('index.php?option=com_acesef', JTEXT::_('ACESEF_CONFIG_NOT_SAVED'));
	}
}
?>