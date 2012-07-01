<?php
/*
* @package		AceSEF
* @subpackage	Wrapper
* @copyright	2009-2010 JoomAce LLC, www.joomace.net
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// No Permission
defined('_JEXEC') or die('Restricted access');

class AceSEF_com_wrapper extends AcesefExtension {
	
	function beforeBuild(&$uri) {
        if (is_null($uri->getVar('view'))) {
            $uri->setVar('view', 'wrapper');
		}
    }

	function build(&$vars, &$segments, &$do_sef, &$metadata, &$item_limitstart) {
		// Extract variables
        extract($vars);
		
		unset($vars['view']);
		
		$metadata = parent::getMetaData($vars, $item_limitstart);
		
		unset($vars['limit']);
		unset($vars['limitstart']);
	}
}
?>