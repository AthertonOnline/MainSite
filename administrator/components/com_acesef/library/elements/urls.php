<?php
/**
* @version		1.7.0
* @package		AceSEF
* @subpackage	AceSEF
* @copyright	2009-2011 JoomAce LLC, www.joomace.net
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// No Permission
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.form.formfield');

class JFormFieldURLs extends JFormField {

	protected $type = 'URLs';

	protected function getInput() {
        JHtml::_('behavior.framework');
		JHtml::_('behavior.modal', 'a.modal');

        $script = array();
		$script[] = '	function selectURL(id, url_sef, object) {';
		$script[] = '		document.id("'.$this->id.'_id").value = id;';
		$script[] = '		document.id("'.$this->id.'_name").value = url_sef;';
		$script[] = '		SqueezeBox.close();';
		$script[] = '	}';
        JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));

		JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_acesef'.DS.'tables');
		$tag = &JTable::getInstance('AcesefSefUrls', 'Table');
		if ($this->value)	{
			$tag->loadByID($this->value);
		}
		else {
			$tag->url_sef = JText::_('Select URL');
		}

		$link = 'index.php?option=com_acesef&amp;controller=sefurls&amp;task=url&amp;tmpl=component&amp;id='.$this->name;

		$html = "\n".'<div style="float: left;"><input style="background: #ffffff;" type="text" id="'.$this->id.'_name" size="45" value="'.htmlspecialchars($tag->url_sef, ENT_QUOTES, 'UTF-8').'" disabled="disabled" /></div>';
		$html .= '<div class="button2-left"><div class="blank"><a class="modal" title="'.JText::_('Select URL').'"  href="'.$link.'" rel="{handler: \'iframe\', size: {x: 900, y: 500}}">'.JText::_('Select URL').'</a></div></div>'."\n";
		$html .= "\n".'<input type="hidden" id="'.$this->id.'_id" name="'.$this->name.'" value="'.(int)$this->value.'" />';

		return $html;
	}
}