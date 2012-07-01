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
class AcesefControllerSupport extends AcesefController {

	// Main constructer
    function __construct() {
        parent::__construct('support');
    }
	
	// Support page
    function support() {
        JRequest::setVar('view', $this->_context);
        JRequest::setVar('layout' , $this->_context);

        parent::display();
    }
    
	// Translators page
    function translators() {
        JRequest::setVar('view', $this->_context);
        JRequest::setVar('layout', 'translators');
        
        parent::display();
    }
}
?>