<?php
/**
* @version		1.5.0
* @package		AceSEF
* @subpackage	AceSEF
* @copyright	2009-2010 JoomAce LLC, www.joomace.net
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// No Permission
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.html.parameter.element');

class JElementRouterList extends JElement {

	var $_name = 'RouterList';

	function fetchElement($name, $value, &$node, $control_name) {
		// Base name of the HTML control
		$class = ($node->attributes('class') ? 'class="'.$node->attributes('class').'"' : 'class="inputbox"');
		
        $extension = AcesefUtility::getExtensionFromRequest();
		
		$options = AcesefUtility::getRouterList($extension);

		return JHTML::_('select.genericlist', $options, ''.$control_name.'['.$name.']', $class, 'value', 'text', $value, $control_name.$name);
	}
}