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

class JElementCategoryList extends JElement {

	var $_name = 'CategoryList';

	function fetchElement($name, $value, &$node, $control_name) {
		// Base name of the HTML control
		$ctrl = $control_name .'['. $name .']';

		// Construct the various argument calls that are supported
		$attribs = ' ';
		$attribs .= 'size="10"';
		$attribs .= 'class="inputbox"';
		$attribs .= ' multiple="multiple"';
		$ctrl .= '[]';

		// Get rows
		static $tree;
		if (!isset($tree)) {
			$extension = AcesefUtility::getExtensionFromRequest();
			$acesef_ext = AcesefFactory::getExtension($extension);
			
			$db_query = "";
			if ($node->attributes('db_query')) {
				$db_query = $node->attributes('db_query');
			}
			
			$rows = $acesef_ext->getCategoryList($db_query);

			// Collect childrens
			$children = array();
			if (!empty($rows)) {
				foreach ($rows as $row) {
					// Not subcategories
					if (empty($row->parent)) {
						$row->parent = 0;
					}
					
					$pt = $row->parent;
					$list = @$children[$pt] ? $children[$pt] : array();
					array_push($list, $row);
					$children[$pt] = $list;
				}
			}

			// Not subcategories
			if (empty($rows[0]->parent)) {
				$rows[0]->parent = 0;
			}

			// Build Tree
			$tree = self::_buildTree(intval($rows[0]->parent), '', array(), $children);
		}

		$options = array();
		$options[] = array('id' => 'all', 'name' => JText::_('ACESEF_PARAMS_ALL_CATS'));

		foreach ($node->children() as $option) {
			$options[] = array('id' => $option->attributes('value'), 'name' => $option->data());
		}
		
		foreach ($tree as $item){
			$options[] = array('id' => $item->id, 'name' => $item->name);
		}

		return JHTML::_('select.genericlist', $options, $ctrl, $attribs, 'id', 'name', $value, $control_name.$name);
	}
	
	function _buildTree($id, $indent, $list, &$children) {
		if (@$children[$id]) {
			foreach ($children[$id] as $ch) {
				$id = $ch->id;

				$pre 	= '<sup>|_</sup>&nbsp;';
				$spacer = '.&nbsp;&nbsp;&nbsp;';

				if ($ch->parent == 0) {
					$txt = $ch->name;
				} else {
					$txt = $pre . $ch->name;
				}
				
				$list[$id] = $ch;
				$list[$id]->name = "$indent$txt";
				$list[$id]->children = count(@$children[$id]);
				$list = self::_buildTree($id, $indent . $spacer, $list, $children);
			}
		}
		return $list;
	}
}