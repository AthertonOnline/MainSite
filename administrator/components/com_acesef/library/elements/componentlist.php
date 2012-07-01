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

jimport('joomla.form.formfield');

// Load AceSEF library
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_acesef'.DS.'library'.DS.'database.php');
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_acesef'.DS.'library'.DS.'utility.php');

class JFormFieldComponentList extends JFormField {

	protected $type = 'ComponentList';

	protected function getInput() {
		// Base name of the HTML control
        $control_name = 'components';
        $name = $this->name;
		$ctrl = $control_name .'['. $name .']';

		// Construct the various argument calls that are supported
		$attribs = ' ';
		$attribs .= 'size="15"';
		$attribs .= 'class="inputbox"';
		$attribs .= ' multiple="multiple"';
		$ctrl .= '[]';

		$filter = self::getSkippedComponents();
        $rows = AceDatabase::loadResultArray("SELECT `element` FROM `#__extensions` WHERE `type` = 'component' AND `element` NOT IN ({$filter}) ORDER BY `element`");

        $lang =& JFactory::getLanguage();

        $options = array();
        $options[] = array('option' => 'all', 'name' => JText::_('- All Components -'));

		foreach ($rows as $row){
            $lang->load($row.'.sys', JPATH_ADMINISTRATOR);
			$options[] = array('option' => $row, 'name' => JText::_($row));
		}
		
		return JHTML::_('select.genericlist', $options, $ctrl, $attribs, 'option', 'name', $this->value, $control_name.$name);
	}
}