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
class AcesefModelMetadata extends AcesefModel {
	
	// Main constructer
	function __construct()	{
		parent::__construct('metadata');
		
		$this->_getUserStates();
		$this->_buildViewQuery();
	}
	
	// Save changes
	function apply() {
		// Get variables
		$sef_id		= JRequest::getVar('sef_id');
		$metatitle	= JRequest::getVar('metatitle');
		$metadesc	= JRequest::getVar('metadesc');
		$metakey	= JRequest::getVar('metakey');
		
		// Save
		foreach ($sef_id as $id => $val) {
			// Make some clean up
			$title = str_replace(array('\n\n', '\n', '\r', '\\', '"', '\'', '>'), array('', '', '', '', '\"', '\\\'', ''), $metatitle[$id]);
			$description = str_replace(array('\n\n', '\n', '\r', '\\', '"', '\'', ';'), array('', '', '', '', '\"', '\\\'', ''), $metadesc[$id]);
			$keywords = str_replace(array('\n\n', '\n', '\r', '\\', '"', '\'', ';'), array('', '', '', '', '\"', '\\\'', ''), $metakey[$id]);
			
			// Save
			AceDatabase::query("UPDATE #__acesef_metadata SET title = '{$title}', description = '{$description}', keywords = '{$keywords}' WHERE id = {$id}");
		}
	}
	
	function generateMetadata() {
		$where = " WHERE params LIKE '%published=1%' AND params LIKE '%trashed=0%' AND params LIKE '%notfound=0%'";
		$rows = AceDatabase::loadObjectList("SELECT url_sef, url_real FROM #__acesef_urls {$where}");
	
		if (!is_array($rows) || empty($rows)) {
			return false;
		}
		
		jimport('joomla.language.helper');
		
		$ret = false;
		
		foreach ($rows as $row) {
			$component = AcesefUtility::getOptionFromRealURL($row->url_real);
			
			if (file_exists(JPATH_ACESEF_ADMIN.DS.'extensions'.DS.$component.'.php')) {
				$acesef_ext = AcesefFactory::getExtension($component);
				
				$uri = AcesefURI::_createURI($row->url_real);
				
				$acesef_ext->beforeBuild($uri);
				$segments = array();
				$do_sef = true;
				$meta = null;
				$item_limitstart = false;
                $vars = $uri->getQuery(true);
				$acesef_ext->build($vars, $segments, $do_sef, $meta, $item_limitstart);
				
				if (is_array($meta) && count($meta) > 0) {
					AcesefMetadata::autoMetadata($row->url_sef, $meta);
					$ret = true;
				}
			}
		}
		
    	return $ret;
	}
	
	function deleteEmptyReal($where) {
		if (!$urls = AceDatabase::loadResultArray("SELECT url_sef FROM #__acesef_metadata {$where}")) {
			return false;
		}
		
		foreach ($urls as $url) {
			$url_real = AceDatabase::loadObject("SELECT url_real FROM #__acesef_urls WHERE url_sef = '{$url}'");
			if (!is_object($url_real)) {
				AceDatabase::query("DELETE FROM #__acesef_metadata WHERE url_sef = '{$url}'");
			}
		}

		return true;
	}
	
	function getToolbarSelections() {
		$toolbar = new stdClass();
		
        // Actions
        $act[] = JHTML::_('select.option', 'delete', JText::_('Delete'));
        $act[] = JHTML::_('select.option', 'deleteemptyreal', JText::_('ACESEF_TOOLBAR_DELETE_EMPTY_REAL'));
		$act[] = JHTML::_('select.option', 'sep', '---');
		if ($this->AcesefConfig->ui_metadata_published == 1) {
	        $act[] = JHTML::_('select.option', 'publish', JText::_('Publish'));
	        $act[] = JHTML::_('select.option', 'unpublish', JText::_('ACESEF_TOOLBAR_PUBLISH_UN'));
			$act[] = JHTML::_('select.option', 'sep', '---');
		}
        $act[] = JHTML::_('select.option', 'clean', JText::_('ACESEF_TOOLBAR_CLEAN'));
        $act[] = JHTML::_('select.option', 'update', JText::_('ACESEF_TOOLBAR_UPDATE'));
		$act[] = JHTML::_('select.option', 'sep', '---');
		if ($this->AcesefConfig->ui_metadata_cached == 1) {
	        $act[] = JHTML::_('select.option', 'cache', JText::_('ACESEF_TOOLBAR_CACHE'));
	        $act[] = JHTML::_('select.option', 'uncache', JText::_('ACESEF_TOOLBAR_CACHE_UN'));
			$act[] = JHTML::_('select.option', 'sep', '---');
		}
        $act[] = JHTML::_('select.option', 'backup', JText::_('ACESEF_TOOLBAR_BACKUP'));
        $toolbar->action = JHTML::_('select.genericlist', $act, 'meta_action', 'class="inputbox" size="1" onchange="showInput();"');
		
		// Purge/Update
		$fields[] = JHTML::_('select.option', 'all', JText::_('ACESEF_METADATA_ALL_FIELDS'));
		$fields[] = JHTML::_('select.option', 'title', JText::_('ACESEF_COMMON_TITLE'));
        $fields[] = JHTML::_('select.option', 'description', JText::_('ACESEF_COMMON_DESCRIPTION'));
        $fields[] = JHTML::_('select.option', 'keywords', JText::_('ACESEF_COMMON_KEYWORDS'));
        //$toolbar->fields = JHTML::_('select.genericlist', $fields, 'meta_fields', 'class="inputbox" size="1"');
		$toolbar->fields = '<div id="meta_fields" style="display: none">'.JHTML::_('select.genericlist', $fields, 'tb_newfields', 'class="inputbox" size="1"', 'value', 'text', 'all').'</div>';
		
		// Selections
        $sel[] = JHTML::_('select.option', 'selected', JText::_('ACESEF_TOOLBAR_SELECTED'));
        $sel[] = JHTML::_('select.option', 'filtered', JText::_('ACESEF_TOOLBAR_FILTERED'));
        $toolbar->selection = JHTML::_('select.genericlist', $sel, 'meta_selection', 'class="inputbox" size="1"');
		
		// Button
        $toolbar->button = '<input type="button" value="'.JText::_('Apply').'" onclick="apply();" />';
		
		return $toolbar;
	}
	
	function _getUserStates() {		
		$this->filter_order		= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_order',		'filter_order',		'url_sef');
		$this->filter_order_Dir	= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_order_Dir',	'filter_order_Dir',	'ASC');
		$this->type				= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.type', 			'type', 			'all');
		$this->search_url		= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.search_url', 		'search_url', 		'');
		$this->filter_component	= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_component', 'filter_component', '-1');
		$this->search_title		= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.search_title', 	'search_title', 	'');
		$this->filter_title		= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_title',		'filter_title',		'-1');
		$this->search_desc		= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.search_desc', 		'search_desc', 		'');
		$this->filter_desc		= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_desc',		'filter_desc',		'-1');
		$this->search_key		= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.search_key', 		'search_key', 		'');
		$this->filter_key		= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_key',		'filter_key',		'-1');
		$this->filter_published	= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_published',	'filter_published',	'-1');
        $this->search_id		= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.search_id', 		'search_id', 		'');
		$this->search_url		= JString::strtolower($this->search_url);
		$this->search_title		= JString::strtolower($this->search_title);
		$this->search_desc		= JString::strtolower($this->search_desc);
		$this->search_key		= JString::strtolower($this->search_key);
		$this->search_id		= JString::strtolower($this->search_id);
	}
	
	function getLists() {
		$lists = array();

		// Table ordering
		$lists['order_dir'] = $this->filter_order_Dir;
		$lists['order'] 	= $this->filter_order;
		
		// Reset filters
		$lists['reset_filters'] = '<button onclick="resetFilters();">'. JText::_('Reset') .'</button>';
		
		// Filter's action
		$javascript = 'onchange="document.adminForm.submit();"';
		
		// Search URL
        $lists['search_url'] = "<input type=\"text\" name=\"search_url\" value=\"{$this->search_url}\" size=\"30\" maxlength=\"255\" onchange=\"document.adminForm.submit();\" />";
		
		// Components List
		$com_list = array();
		$com_list[] = JHTML::_('select.option', '-1', JText::_('ACESEF_COMMON_COM_FILTER'));
		$com_list = array_merge($com_list, AcesefUtility::getComponents());
        $lists['component_list'] = JHTML::_('select.genericlist', $com_list, 'filter_component', 'class="inputbox" size="1"'.$javascript, 'value', 'text', $this->filter_component);
   	   	
		// Search Title
        $lists['search_title'] = "<input type=\"text\" name=\"search_title\" value=\"{$this->search_title}\" size=\"20\" maxlength=\"255\" onchange=\"document.adminForm.submit();\" />";
		
		// Title Filter
		$title_list[] = JHTML::_('select.option', '-1', JText::_('ACESEF_COMMON_SELECT'));
		$title_list[] = JHTML::_('select.option', '1', JText::_('ACESEF_METADATA_SELECT_EMPTY'));
		$title_list[] = JHTML::_('select.option', '2', JText::_('ACESEF_METADATA_SELECT_FULL'));
   	   	$lists['title_list'] = JHTML::_('select.genericlist', $title_list, 'filter_title', 'class="inputbox" size="1"'.$javascript,'value', 'text', $this->filter_title);
		
		// Search Description
        $lists['search_desc'] = "<input type=\"text\" name=\"search_desc\" value=\"{$this->search_desc}\" size=\"20\" maxlength=\"255\" onchange=\"document.adminForm.submit();\" />";
		
		// Description Filter
		$desc_list[] = JHTML::_('select.option', '-1', JText::_('ACESEF_COMMON_SELECT'));
		$desc_list[] = JHTML::_('select.option', '1', JText::_('ACESEF_METADATA_SELECT_EMPTY'));
		$desc_list[] = JHTML::_('select.option', '2', JText::_('ACESEF_METADATA_SELECT_FULL'));
   	   	$lists['desc_list'] = JHTML::_('select.genericlist', $desc_list, 'filter_desc', 'class="inputbox" size="1"'.$javascript,'value', 'text', $this->filter_desc);
		
		// Search Keywords
        if ($this->AcesefConfig->ui_metadata_keys == 1) {
       		$lists['search_key'] = "<input type=\"text\" name=\"search_key\" value=\"{$this->search_key}\" size=\"20\" maxlength=\"255\" onchange=\"document.adminForm.submit();\" />";
       		
			$key_list[] = JHTML::_('select.option', '-1', JText::_('ACESEF_COMMON_SELECT'));
			$key_list[] = JHTML::_('select.option', '1', JText::_('ACESEF_METADATA_SELECT_EMPTY'));
			$key_list[] = JHTML::_('select.option', '2', JText::_('ACESEF_METADATA_SELECT_FULL'));
	   	   	$lists['key_list'] = JHTML::_('select.genericlist', $key_list, 'filter_key', 'class="inputbox" size="1"'.$javascript,'value', 'text', $this->filter_key);
        }
        
		// Published Filter
        if ($this->AcesefConfig->ui_metadata_published == 1) {
			$published_list[] = JHTML::_('select.option', '-1', JText::_('ACESEF_COMMON_SELECT'));
			$published_list[] = JHTML::_('select.option', '1', JText::_('Yes'));
			$published_list[] = JHTML::_('select.option', '0', JText::_('No'));
	   	   	$lists['published_list'] = JHTML::_('select.genericlist', $published_list, 'filter_published', 'class="inputbox" size="1"'.$javascript,'value', 'text', $this->filter_published);
        }
        
		// Search id
		if ($this->AcesefConfig->ui_metadata_id == 1) {
        	$lists['search_id'] = "<input type=\"text\" name=\"search_id\" value=\"{$this->search_id}\" size=\"3\" maxlength=\"10\" onchange=\"document.adminForm.submit();\" />";
		}
		return $lists;
	}
	
	function getCache() {
		$metadata = array();
		
		$cache = AcesefFactory::getCache();
		$metadata = $cache->load('metadata');
		
		return $metadata;
	}
	
	function getURLs() {
		static $urls;
		
		if (!isset($urls) && !empty($this->_data)) {
			$items = '';
			foreach ($this->_data as $item) {
				$items .= $item->id.', ';
			}
			
			$items = rtrim($items, ', ');
			$select = 'u.url_sef, u.id, u.url_real, u.used';
			$tables = '#__acesef_urls AS u, #__acesef_metadata AS m';
			$where = "u.url_sef = m.url_sef AND m.id IN ({$items}) AND u.params LIKE '%notfound=0%'";
			
    		$urls = AceDatabase::loadObjectList("SELECT {$select} FROM {$tables} WHERE {$where} ORDER BY u.url_sef {$this->filter_order_Dir}");
		}
		
    	return $urls;
	}

	// Query fileters
	function _buildViewWhere($prefix = "") {
		$where = array();
		
		// Search URL
		if ($this->search_url != '') {
			$src = parent::secureQuery($this->search_url, true);
			$where[] = "LOWER({$prefix}url_sef) LIKE {$src}";
		}
		
		// Search Title
		if ($this->search_title != '') {
			$src = parent::secureQuery($this->search_title, true);
			$where[] = "{$prefix}title LIKE {$src}";
		}
		
		// Title Filter
		if ($this->filter_title != -1) {
			if ($this->filter_title == 1) {
				$where[]= "{$prefix}title = ''";
			} elseif ($this->filter_title == 2){
				$where[]= "{$prefix}title != ''";
			}
		}
		
		// Search Description
		if ($this->search_desc != '') {
			$src = parent::secureQuery($this->search_desc, true);
			$where[] = "{$prefix}description LIKE {$src}";
		}
		
		// Description Filter
		if ($this->filter_desc != -1) {
			if ($this->filter_desc == 1){
				$where[]= "{$prefix}description = ''";
			} elseif ($this->filter_desc == 2){
				$where[]= "{$prefix}description != ''";
			}
		}
		
		// Search Keywords
		if ($this->search_key != '') {
			$src = parent::secureQuery($this->search_key, true);
			$where[] = "{$prefix}keywords LIKE {$src}";
		}
		
		// Keywords Filter
		if ($this->filter_key != -1) {
			if ($this->filter_key == 1){
				$where[]= "{$prefix}keywords = ''";
			} elseif ($this->filter_key == 2){
				$where[]= "{$prefix}keywords != ''";
			}
		}
		
		// Published Filter
		if ($this->filter_published != -1) {
			$src = parent::secureQuery($this->filter_published);
			$where[] = "{$prefix}published = {$src}";
		}
		
		// Search id
		if ($this->search_id != '') {
			$src = parent::secureQuery($this->search_id);
			$where[]= "{$prefix}id = {$src}";
		}
		
		// Execute
		$where = (count($where) ? " WHERE ". implode(" AND ", $where) : "");
		
		// Component Filter
		if ($this->filter_component != '-1') {
			$src = $this->_db->getEscaped($this->filter_component);
			
			$where = str_replace(' WHERE ', ' AND ', $where);
			$where = str_replace('AND LOWER(url_sef) LIKE', "AND LOWER(m.url_sef) LIKE", $where);
			
			// Get ids
			$ids = AceDatabase::loadResultArray("SELECT m.id FROM #__acesef_metadata AS m, #__acesef_urls AS u WHERE m.url_sef = u.url_sef AND u.url_real LIKE '%option={$src}%' {$where}");
			
			$where = ' WHERE id = 0';
			if (count($ids) > 0) {
				$where = " WHERE {$prefix}id IN (" . implode(", ", $ids) . ")";
			}
		}
		
		// Duplicated title
		if ($this->type == 'dtitle') {
			// Get ids
			$this->_db->setQuery("SELECT id FROM #__acesef_metadata AS t1 INNER JOIN (SELECT title FROM #__acesef_metadata GROUP BY title HAVING COUNT(title) > 1) AS t2 ON t1.title = t2.title {$where}");
			$ids = $this->_db->loadResultArray();
			
			$where = " WHERE id = '-1'";
			if (count($ids) > 0) {
				$where = " WHERE {$prefix}id IN (" . implode(", ", $ids) . ")";
			}
		}
		
		// Duplicated description
		if ($this->type == 'ddesc') {
			// Get ids
			$this->_db->setQuery("SELECT id FROM #__acesef_metadata AS t1 INNER JOIN (SELECT description FROM #__acesef_metadata GROUP BY description HAVING COUNT(description) > 1) AS t2 ON t1.description = t2.description {$where}");
			$ids = $this->_db->loadResultArray();
			
			$where = " WHERE id = '-1'";
			if (count($ids) > 0) {
				$$where = " WHERE {$prefix}id IN (" . implode(", ", $ids) . ")";
			}
		}
		
		return $where;
	}
}
?>