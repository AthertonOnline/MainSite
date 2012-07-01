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
class AcesefControllerUpgrade extends AcesefController {

	// Main constructer
	function __construct() {
		parent::__construct('upgrade');
	}
	
	// Upgrade
    function upgrade() {
		// Check token
		JRequest::checkToken() or jexit('Invalid Token');
		
		// Upgrade
		$this->_model->upgrade();
		
		// Return
		$this->setRedirect('index.php?option=com_acesef&controller=upgrade&task=view');
    }
}
?>