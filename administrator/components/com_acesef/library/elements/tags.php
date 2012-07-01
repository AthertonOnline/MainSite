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

class JElementTags extends JElement {

	var	$_name = 'Tags';

	function fetchElement($name, $value, &$node, $control_name) {
		$doc 		= &JFactory::getDocument();
		$fieldName	= $control_name.'['.$name.']';

		JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_acesef'.DS.'tables');
		$tag = &JTable::getInstance('AcesefTags', 'Table');
		if ($value)	{
			$tag->loadByTitle($value);
		}
		else {
			$tag->title = JText::_('Select Tag');
		}

		$js = "
		function selectTag(title, object) {
			document.getElementById(object + '_id').value = title;
			document.getElementById(object + '_name').value = title;
			document.getElementById('sbox-window').close();
		}";
		$doc->addScriptDeclaration($js);

		$link = 'index.php?option=com_acesef&amp;controller=tags&amp;task=modal&amp;tmpl=component&amp;tag='.$name;

		JHTML::_('behavior.modal', 'a.modal');

		$html = "\n".'<div style="float: left;"><input style="background: #ffffff;" type="text" id="'.$name.'_name" value="'.htmlspecialchars($tag->title, ENT_QUOTES, 'UTF-8').'" disabled="disabled" /></div>';
		$html .= '<div class="button2-left"><div class="blank"><a class="modal" title="'.JText::_('Select Tag').'"  href="'.$link.'" rel="{handler: \'iframe\', size: {x: 650, y: 375}}">'.JText::_('Select').'</a></div></div>'."\n";
		$html .= "\n".'<input type="hidden" id="'.$name.'_id" name="'.$fieldName.'" value="'.(int)$value.'" />';

		return $html;
	}
}