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

// Model Class
class AcesefModelSefUrlsDp extends AcesefModel {
	
	// Main constructer
	function __construct() {
		parent::__construct('sefurlsdp', 'urls');
		
		$this->_getUserStates();
		$this->_buildViewQuery();
	}
	
	function _getUserStates() {
		$this->filter_order		= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_order',		'filter_order',		'url_real');
		$this->filter_order_Dir	= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_order_Dir',	'filter_order_Dir',	'ASC');
        $this->search_hits		= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.search_hits', 		'search_hits', 		'');
		$this->search_real		= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.search_real', 		'search_real', 		'');
        $this->filter_component	= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_component', 'filter_component', '');
        $this->filter_lang		= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_lang', 		'filter_lang', 		'');
		$this->filter_used		= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_used',		'filter_used',		'-1');
		$this->search_hits		= JString::strtolower($this->search_hits);
		$this->search_real		= JString::strtolower($this->search_real);
	}
	
	function getToolbarSelections() {
		$toolbar = new stdClass();
		
        // Actions
        $act[] = JHTML::_('select.option', 'delete', JText::_('Delete'));
		$act[] = JHTML::_('select.option', 'sep', '---');
        $act[] = JHTML::_('select.option', 'publish', JText::_('Publish'));
        $act[] = JHTML::_('select.option', 'unpublish', JText::_('ACESEF_TOOLBAR_PUBLISH_UN'));
		$act[] = JHTML::_('select.option', 'sep', '---');
        $act[] = JHTML::_('select.option', 'used', JText::_('ACESEF_TOOLBAR_USE'));
        $act[] = JHTML::_('select.option', 'resetused', JText::_('ACESEF_TOOLBAR_USE_RESET'));
		$act[] = JHTML::_('select.option', 'sep', '---');
        $act[] = JHTML::_('select.option', 'lock', JText::_('ACESEF_TOOLBAR_LOCK'));
        $act[] = JHTML::_('select.option', 'unlock', JText::_('ACESEF_TOOLBAR_LOCK_UN'));
		$act[] = JHTML::_('select.option', 'sep', '---');
        $act[] = JHTML::_('select.option', 'block', JText::_('ACESEF_TOOLBAR_BLOCK'));
        $act[] = JHTML::_('select.option', 'unblock', JText::_('ACESEF_TOOLBAR_BLOCK_UN'));
		$act[] = JHTML::_('select.option', 'sep', '---');
        $act[] = JHTML::_('select.option', 'cache', JText::_('ACESEF_TOOLBAR_CACHE'));
        $act[] = JHTML::_('select.option', 'uncache', JText::_('ACESEF_TOOLBAR_CACHE_UN'));
		$toolbar->action = JHTML::_('select.genericlist', $act, 'sefurlsdp_action', 'class="inputbox" size="1"');
		
		// Selections
        $sel[] = JHTML::_('select.option', 'selected', JText::_('ACESEF_TOOLBAR_SELECTED'));
        $sel[] = JHTML::_('select.option', 'filtered', JText::_('ACESEF_TOOLBAR_FILTERED'));
        $toolbar->selection = JHTML::_('select.genericlist', $sel, 'sefurlsdp_selection', 'class="inputbox" size="1"');
		
		// Button
        $toolbar->button = '<button onclick="apply();" />'.JText::_('Apply').'</button>';
		
		return $toolbar;
	}
	
	function getLists() {
		global $mainframe, $option;
		$lists = array();

		// Table ordering
		$lists['order_dir'] = $this->filter_order_Dir;
		$lists['order'] 	= $this->filter_order;
		
		// Reset filters
		$lists['reset_filters'] = '<button onclick="resetFilters();">'. JText::_('Reset') .'</button>';
		
		// Filter's action
		$javascript = 'onchange="document.adminForm.submit();"';
		
		// Components List
		$com_list = array();
		$com_list[] = JHTML::_('select.option', '', JText::_('ACESEF_COMMON_COM_FILTER'));
		$com_list = array_merge($com_list, AcesefUtility::getComponents());
        $lists['component_list'] = JHTML::_('select.genericlist', $com_list, 'filter_component', 'class="inputbox" size="1"'.$javascript, 'value', 'text', $this->filter_component);
		
		// Languages list
		$lang_list = array();
		$lang_list[] = JHTML::_('select.option', '', JText::_('ACESEF_COMMON_LANGUAGE_FILTER'));
		$lang_list = array_merge($lang_list, AcesefUtility::getLanguages());
        $lists['lang_list'] = JHTML::_('select.genericlist', $lang_list, 'filter_lang', 'class="inputbox" size="1"'.$javascript, 'value', 'text', $this->filter_lang);
		
		// Search Real URL
        $lists['search_real'] = "<input type=\"text\" name=\"search_real\" value=\"{$this->search_real}\" size=\"45\" maxlength=\"255\" onchange=\"document.adminForm.submit();\" />";
		
   	   	// Used Filter
		$used_list[] = JHTML::_('select.option', '-1', JText::_('ACESEF_COMMON_SELECT'));
		$used_list[] = JHTML::_('select.option', '2', JText::_('Yes'));
		$used_list[] = JHTML::_('select.option', '1', JText::_('No'));
   	   	$lists['used_list'] = JHTML::_('select.genericlist', $used_list, 'filter_used', 'class="inputbox" size="1"'.$javascript,'value', 'text', $this->filter_used);
		
		// Search hits
        $lists['search_hits'] = "<input type=\"text\" name=\"search_hits\" value=\"{$this->search_hits}\" size=\"3\" maxlength=\"10\" onchange=\"document.adminForm.submit();\" />";
		
		return $lists;
	}
	
	// Query fileters
	function _buildViewWhere() {
		$where = array();
		
		// Search Real URL
		if ($this->search_real != '') {
			$src = parent::secureQuery($this->search_real, true);
			$where[] = "LOWER(url_real) LIKE {$src}";
		}
		
		// Component Filter
		if ($this->filter_component != '') {
			$src = $this->_db->getEscaped($this->filter_component, true);
			$where[]= "(LOWER(url_real) LIKE '%option={$src}&%' OR url_real LIKE '%option={$src}')";
		}
		
		// Language Filter
		if ($this->filter_lang != '') {
			$src = $this->_db->getEscaped($this->filter_lang, true);
			$where[]= "(LOWER(url_real) LIKE '%lang={$src}&%' OR url_real LIKE '%lang={$src}')";
		}
		
		// Search hits
		if ($this->search_hits != '') {
			$src = parent::secureQuery($this->search_hits);
			$where[]= "hits = {$src}";
		}
		
		// Get SEF URL
		$sef = self::getSefUrl();
		$where[] = "url_sef = '{$sef}'";
		
		// Execute
		$where = (count($where) ? ' WHERE '. implode(' AND ', $where) : '');
		
		return $where;
	}
	
	function getSefUrl() {
		static $sef;
		
		$id = JRequest::getVar('id');
		if (!isset($sef) && is_numeric($id)) {
			$sef = AceDatabase::loadResult("SELECT url_sef FROM #__acesef_urls WHERE id = {$id}");
		}
		
		return $sef;
	}
}
?>