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

// Control Panel Controller Class
class AcesefControllerAcesef extends AcesefController {
	
	// Main constructer
    function __construct() {
        parent::__construct('acesef');
    }
	
	function sefStatus() {
		// Check token
		JRequest::checkToken() or jexit('Invalid Token');
		
		$model = $this->getModel('Acesef');
		$msg = $model->sefStatus();
        
        $this->setRedirect('index.php?option=com_acesef', $msg);
    }
	
	function saveDownloadID() {
		// Check token
		JRequest::checkToken() or jexit('Invalid Token');
		
		$model = $this->getModel('Acesef');
		$msg = $model->saveDownloadID();
        
        $this->setRedirect('index.php?option=com_acesef', $msg);
    }
}
?>