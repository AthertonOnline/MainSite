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
class AcesefModelSefUrls extends AcesefModel {
	
	// Main constructer
	function __construct() {
		parent::__construct('sefurls', 'urls');
		
		$this->_getUserStates();
		$this->_buildViewQuery();
	}
	
	function apply() {
        AcesefUtility::import('library.parameter');

		// Get variables
		$url_id			= JRequest::getVar('url_id');
		$url_sef		= JRequest::getVar('url_sef');
		$url_real		= JRequest::getVar('url_real');
		$url_used		= JRequest::getVar('url_used');
		$url_published	= JRequest::getVar('url_published');
		$url_locked		= JRequest::getVar('url_locked');
		$url_blocked	= JRequest::getVar('url_blocked');
		
		// Save
		foreach ($url_id as $id => $val) {
			AceDatabase::query("UPDATE #__acesef_urls SET url_sef = '{$url_sef[$id]}', url_real = '{$url_real[$id]}', used = '{$url_used[$id]}' WHERE id = {$id}");
			
			$row = AcesefFactory::getTable('AcesefSefUrls');
			if ($row->load($id)) {
                if (!isset($url_published[$id])) {
                    $url_published[$id] = 0;
                }
                if (!isset($url_locked[$id])) {
                    $url_locked[$id] = 0;
                }
                if (!isset($url_blocked[$id])) {
                    $url_blocked[$id] = 0;
                }
                
				$params = new AcesefParameter($row->params);
				$params->set('published', $url_published[$id]);
				$params->set('locked', $url_locked[$id]);
				$params->set('blocked', $url_blocked[$id]);
				$row->params = $params->toString('INI');

				if ($row->check()) {
					$row->store();
				}
			}
		}
	}
    
    function generateURLs() {
        AcesefUtility::import('library.parameter');

		$urls = AceDatabase::loadObjectList("SELECT DISTINCT url_sef FROM #__acesef_urls WHERE used != 1 AND params LIKE '%visited=1%' AND params LIKE '%notfound=0%' AND params LIKE '%trashed=0%' LIMIT 500");
		
		if (!empty($urls)) {
			foreach ($urls as $url) {
				$row = AcesefFactory::getTable('AcesefSefUrls');
				if ($row->loadbySEF($url->url_sef)) {
					$params = new AcesefParameter($row->params);
					$params->set('visited', '0');
					$row->params = $params->toString('INI');
					
					if ($row->check()) {
						$row->store();
					}
				}
			}
		}
		
        // Visit the homepage
        AcesefUtility::getRemoteData(JURI::root());
		
		$visited = array();
		for ($i = 0; $i < 4; $i ++) {
			$urls = AceDatabase::loadObjectList("SELECT DISTINCT url_sef FROM #__acesef_urls WHERE used != 1 AND params LIKE '%visited=0%' AND params LIKE '%notfound=0%' AND params LIKE '%trashed=0%' LIMIT 500");
			
			if (empty($urls)) {
				return true;
			}
			
			// Visit URLs
			foreach ($urls as $sef_url) {
				$row = AcesefFactory::getTable('AcesefSefUrls');
				if ($row->loadBySEF($sef_url->url_sef)) {
					$params = new AcesefParameter($row->params);
					$params->set('visited', '1');
					$row->params = $params->toString('INI');
					
					if ($row->check()) {
						$row->store();
					}
				}
				
				$u = $sef_url->url_sef;
				
				if (strpos($u, '.pdf')) {
					continue;
				}
				
				if (isset($visited[$u])) {
					continue;
				}
				
				$url = rtrim(JURI::root(), '/') . '/' . ltrim($u, '/'); 
				AcesefUtility::getRemoteData($url);
				
				$visited[$u] = "true";
			}
		}
        
        return true;
    }
	
	function getToolbarSelections() {
		$toolbar = new stdClass();
		
        // Actions
		if ($this->type == 'trashed') {
	        $act[] = JHTML::_('select.option', 'delete', JText::_('Delete'));
	        $act[] = JHTML::_('select.option', 'restore', JText::_('ACESEF_TOOLBAR_RESTORE'));
		} else {
	        $act[] = JHTML::_('select.option', 'delete', JText::_('Delete'));
			$act[] = JHTML::_('select.option', 'sep', '---');
	        $act[] = JHTML::_('select.option', 'trash', JText::_('ACESEF_TOOLBAR_TRASH'));
			$act[] = JHTML::_('select.option', 'sep', '---');
			if ($this->AcesefConfig->ui_sef_published == 1) {
		        $act[] = JHTML::_('select.option', 'publish', JText::_('Publish'));
		        $act[] = JHTML::_('select.option', 'unpublish', JText::_('ACESEF_TOOLBAR_PUBLISH_UN'));
				$act[] = JHTML::_('select.option', 'sep', '---');
			}
			if ($this->AcesefConfig->ui_sef_used == 1) {
		        $act[] = JHTML::_('select.option', 'used', JText::_('ACESEF_TOOLBAR_USE'));
		        $act[] = JHTML::_('select.option', 'resetused', JText::_('ACESEF_TOOLBAR_USE_RESET'));
				$act[] = JHTML::_('select.option', 'sep', '---');
			}
			if ($this->AcesefConfig->ui_sef_locked == 1) {
		        $act[] = JHTML::_('select.option', 'lock', JText::_('ACESEF_TOOLBAR_LOCK'));
		        $act[] = JHTML::_('select.option', 'unlock', JText::_('ACESEF_TOOLBAR_LOCK_UN'));
				$act[] = JHTML::_('select.option', 'sep', '---');
			}
			if ($this->AcesefConfig->ui_sef_blocked == 1) {
		        $act[] = JHTML::_('select.option', 'block', JText::_('ACESEF_TOOLBAR_BLOCK'));
		        $act[] = JHTML::_('select.option', 'unblock', JText::_('ACESEF_TOOLBAR_BLOCK_UN'));
				$act[] = JHTML::_('select.option', 'sep', '---');
			}
			if ($this->AcesefConfig->ui_sef_cached == 1) {
		        $act[] = JHTML::_('select.option', 'cache', JText::_('ACESEF_TOOLBAR_CACHE'));
		        $act[] = JHTML::_('select.option', 'uncache', JText::_('ACESEF_TOOLBAR_CACHE_UN'));
				$act[] = JHTML::_('select.option', 'sep', '---');
			}
	        $act[] = JHTML::_('select.option', 'settags', JText::_('ACESEF_TOOLBAR_SET_TAGS'));
	        $act[] = JHTML::_('select.option', 'setilinks', JText::_('ACESEF_TOOLBAR_SET_ILINKS'));
	        $act[] = JHTML::_('select.option', 'setbookmarks', JText::_('ACESEF_TOOLBAR_SET_BOOKMARKS'));
	        $act[] = JHTML::_('select.option', 'setunpublishtag', JText::_('ACESEF_TOOLBAR_SET_TAG_UNPUBLISH'));
			$act[] = JHTML::_('select.option', 'sep', '---');
	        $act[] = JHTML::_('select.option', 'copytometadata', JText::_('ACESEF_TOOLBAR_COPY_TO_METADATA'));
	        $act[] = JHTML::_('select.option', 'copytositemap', JText::_('ACESEF_TOOLBAR_COPY_TO_SITEMAP'));
			$act[] = JHTML::_('select.option', 'sep', '---');
	        $act[] = JHTML::_('select.option', 'backup', JText::_('ACESEF_TOOLBAR_BACKUP'));
		}
        $toolbar->action = JHTML::_('select.genericlist', $act, 'sefurls_action', 'class="inputbox" size="1" onchange="showInput();"');
		
        $sets[] = JHTML::_('select.option', '1', JText::_('Enabled'));
        $sets[] = JHTML::_('select.option', '0', JText::_('Disabled'));
        
		// Sets
		$toolbar->newtags = '<div id="divtags" style="display: none">'.JHTML::_('select.genericlist', $sets, 'tb_newtags', 'class="inputbox" size="1"', 'value', 'text', '1').'</div>';
        $toolbar->newilinks = '<div id="divilinks" style="display: none">'.JHTML::_('select.genericlist', $sets, 'tb_newilinks', 'class="inputbox" size="1"', 'value', 'text', '1').'</div>';
		$toolbar->newbookmarks= '<div id="divbookmarks" style="display: none">'.JHTML::_('select.genericlist', $sets, 'tb_newbookmarks', 'class="inputbox" size="1"', 'value', 'text', '1').'</div>';
		$toolbar->newtag = '<div id="divtag" style="display: none"><input type="text" id="tb_newtag" name="tb_newtag" size="15" value="" /></div>';
		
		// Selections
        $sel[] = JHTML::_('select.option', 'selected', JText::_('ACESEF_TOOLBAR_SELECTED'));
        $sel[] = JHTML::_('select.option', 'filtered', JText::_('ACESEF_TOOLBAR_FILTERED'));
        $toolbar->selection = JHTML::_('select.genericlist', $sel, 'sefurls_selection', 'class="inputbox" size="1"');
		
		// Button
        $toolbar->button = '<input type="button" value="'.JText::_('Apply').'" onclick="apply();" />';
		
		return $toolbar;
	}
	
	function delete($where) {
		if (!empty($where)) {
			$where = $where." AND params LIKE '%locked=0\nb%'";
		} else {
			$where = " WHERE params LIKE '%locked=0\nb%'";
		}
		
		if ($this->AcesefConfig->delete_other_sef == '1') {
			$sef_urls = AceDatabase::loadResultArray("SELECT url_sef FROM #__acesef_{$this->_table}{$where}");
		}
		
		if (!AceDatabase::query("DELETE FROM #__acesef_{$this->_table}{$where}")) {
			return false;
		}
		
		if (!empty($sef_urls)) {
			foreach ($sef_urls as $sef_url) {
				AceDatabase::query("DELETE FROM #__acesef_metadata WHERE url_sef = '{$sef_url}'");
				AceDatabase::query("DELETE FROM #__acesef_sitemap WHERE url_sef = '{$sef_url}'");
			}
		}
		
		return true;
	}
	
	function unpublishTag($tag) {
		if (empty($tag)) {
			return false;
		}
		
		$urls = self::_getURLs();
		if (!is_array($urls) || empty($urls)) {
			return false;
		}
		
		foreach ($urls as $url) {
			$found = AceDatabase::loadResult("SELECT url_sef FROM #__acesef_tags_map WHERE tag = '{$tag}' AND url_sef = '{$url}'");
			if ($found) {
				continue;
			}
			
			AceDatabase::query("INSERT INTO #__acesef_tags_map (tag, url_sef) VALUES ('{$tag}', '{$url}')");
		}
		
		return true;
	}
	
	function copyTo($table) {
		$urls = self::_getURLs();
		if (!is_array($urls) || empty($urls)) {
			return false;
		}
		
		foreach ($urls as $url) {
			AceDatabase::query("INSERT IGNORE INTO #__acesef_{$table} (url_sef) VALUES ('{$url}')");
		}
		
		return true;
	}
	
	function _getURLs() {
		$where = AcesefController::_getWhere($this);
		if (!$urls = AceDatabase::loadResultArray("SELECT url_sef FROM #__acesef_urls {$where}")) {
			return false;
		}
		
		return $urls;
	}
	
	function _getUserStates() {
		$this->filter_order		= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_order',		'filter_order',			'url_sef');
		$this->filter_order_Dir	= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_order_Dir',	'filter_order_Dir',		'ASC');
		$this->type				= parent::_getSecureUserState($this->_option . '.' . 'urls.type', 							'type', 				'sef');
		$this->search_sef		= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.search_sef', 		'search_sef', 			'');
        $this->search_real		= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.search_real', 		'search_real', 			'');
        $this->filter_component	= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_component', 'filter_component', 	'-1');
        $this->filter_lang		= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_lang', 		'filter_lang', 			'-1');
		$this->filter_published	= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_published',	'filter_published',		'-1');
		$this->filter_used		= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_used',		'filter_used',			'-1');
		$this->filter_locked	= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_locked', 	'filter_locked',		'-1');
		$this->filter_blocked	= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_blocked', 	'filter_blocked',		'-1');
		$this->fromdate	 		= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.fromdate', 		'fromdate', 			'');
		$this->todate	 		= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.todate', 			'todate', 				'');
        $this->filter_date		= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_date', 		'filter_date', 			'c');
        $this->search_hits		= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.search_hits', 		'search_hits', 			'');
        $this->search_id		= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.search_id', 		'search_id', 			'');
		$this->search_sef		= JString::strtolower($this->search_sef);
		$this->search_real		= JString::strtolower($this->search_real);
		$this->search_hits		= JString::strtolower($this->search_hits);
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
		
		// Search SEF URL
        $lists['search_sef'] = "<input type=\"text\" name=\"search_sef\" value=\"{$this->search_sef}\" size=\"50\" maxlength=\"255\" onchange=\"document.adminForm.submit();\" />";

		// Components List
		$com_list = array();
		$com_list[] = JHTML::_('select.option', '-1', JText::_('ACESEF_COMMON_COM_FILTER'));
		$com_list = array_merge($com_list, AcesefUtility::getComponents());
        $lists['component_list'] = JHTML::_('select.genericlist', $com_list, 'filter_component', 'class="inputbox" size="1"'.$javascript, 'value', 'text', $this->filter_component);
		
		// Languages list
        if ($this->AcesefConfig->ui_sef_language == 1) {
			$lang_list = array();
			$lang_list[] = JHTML::_('select.option', '-1', JText::_('ACESEF_COMMON_LANGUAGE_FILTER'));
			$lang_list = array_merge($lang_list, AcesefUtility::getLanguages());

	        $lists['lang_list'] = JHTML::_('select.genericlist', $lang_list, 'filter_lang', 'class="inputbox" size="1"'.$javascript, 'value', 'text', $this->filter_lang);
        }
        
		// Search Real URL
        $lists['search_real'] = "<input type=\"text\" name=\"search_real\" value=\"{$this->search_real}\" size=\"20\" maxlength=\"255\" onchange=\"document.adminForm.submit();\" />";
		
		// Published Filter
        if ($this->AcesefConfig->ui_sef_published == 1) {
			$published_list[] = JHTML::_('select.option', '-1', JText::_('ACESEF_COMMON_SELECT'));
			$published_list[] = JHTML::_('select.option', '1', JText::_('Yes'));
			$published_list[] = JHTML::_('select.option', '0', JText::_('No'));
	   	   	$lists['published_list'] = JHTML::_('select.genericlist', $published_list, 'filter_published', 'class="inputbox" size="1"'.$javascript,'value', 'text', $this->filter_published);
        }
        
   	   	// Used Filter
   	   	if ($this->AcesefConfig->ui_sef_used == 1) {
			$used_list[] = JHTML::_('select.option', '-1', JText::_('ACESEF_COMMON_SELECT'));
			$used_list[] = JHTML::_('select.option', '2', JText::_('Yes'));
			$used_list[] = JHTML::_('select.option', '1', JText::_('No'));
	   	   	$lists['used_list'] = JHTML::_('select.genericlist', $used_list, 'filter_used', 'class="inputbox" size="1"'.$javascript,'value', 'text', $this->filter_used);
   	   	}
   	   	
		// Locked Filter
   	   	if ($this->AcesefConfig->ui_sef_locked == 1) {
			$locked_list[] = JHTML::_('select.option', '-1', JText::_('ACESEF_COMMON_SELECT'));
			$locked_list[] = JHTML::_('select.option', '1', JText::_('Yes'));
			$locked_list[] = JHTML::_('select.option', '0', JText::_('No'));
	   	   	$lists['locked_list'] = JHTML::_('select.genericlist', $locked_list, 'filter_locked', 'class="inputbox" size="1"'.$javascript,'value', 'text', $this->filter_locked);
   	   	}
   	   	
		// Blocked Filter
   	   	if ($this->AcesefConfig->ui_sef_blocked == 1) {
	   	   	$blocked_list[] = JHTML::_('select.option', '-1', JText::_('ACESEF_COMMON_SELECT'));
			$blocked_list[] = JHTML::_('select.option', '1', JText::_('Yes'));
			$blocked_list[] = JHTML::_('select.option', '0', JText::_('No'));
	   	   	$lists['blocked_list'] = JHTML::_('select.genericlist', $blocked_list, 'filter_blocked', 'class="inputbox" size="1"'.$javascript,'value', 'text', $this->filter_blocked);
   	   	}
   	   	
		// Date Filter
		if ($this->AcesefConfig->ui_sef_date == 1) {
			// From Date
			$lists['fromdate'] = JHTML::_('calendar', $this->fromdate, 'fromdate', 'fromdate', '%d.%m.%y', array('class'=>'inputbox', 'size'=>'7', 'onchange'=>'document.adminForm.submit();',  'maxlength'=>'20'));

			// To Date
			$lists['todate'] = JHTML::_('calendar', $this->todate, 'todate', 'todate', '%d.%m.%y', array('class'=>'inputbox', 'size'=>'7', 'onchange'=>'document.adminForm.submit();',  'maxlength'=>'20'));

			$date_list[] = JHTML::_('select.option', 'c', JText::_('ACESEF_URL_SEF_SELECT_CREATED'));
			$date_list[] = JHTML::_('select.option', 'm', JText::_('ACESEF_URL_SEF_SELECT_MODIFIED'));
	   	   	$lists['date_list'] = JHTML::_('select.genericlist', $date_list, 'filter_date', 'class="inputbox" size="1"'.$javascript, 'value', 'text', $this->filter_date);
		}
		
		// Search hits
		if ($this->AcesefConfig->ui_sef_hits == 1) {
        	$lists['search_hits'] = "<input type=\"text\" name=\"search_hits\" value=\"{$this->search_hits}\" size=\"3\" maxlength=\"10\" onchange=\"document.adminForm.submit();\" />";
		}
		
		// Search id
		if ($this->AcesefConfig->ui_sef_id == 1) {
        	$lists['search_id'] = "<input type=\"text\" name=\"search_id\" value=\"{$this->search_id}\" size=\"3\" maxlength=\"10\" onchange=\"document.adminForm.submit();\" />";
		}
		
		return $lists;
	}
	
	function getDuplicates() {
		$duplicates = array();
		$skip = array();
		
		if (is_array($this->_data)) {
			foreach ($this->_data as $i => $object) {
				if (!isset($duplicates[$object->url_sef])) {
					$duplicates[$object->url_sef] = 1;
					
					if ($object->used == '0') {
						$skip[$object->url_sef] = 0;
					}
				}
				else {
					$skip[$object->url_sef] == 0;
					
					if ($object->used != '0') {
						$skip[$object->url_sef] = 1;
						$duplicates[$object->url_sef] = 1;
					}
					
					if ($skip[$object->url_sef] == 0) {
						$duplicates[$object->url_sef] = $duplicates[$object->url_sef] + 1;
					}
				}
			}
		}
		
		return $duplicates;
	}
	
	function getCache() {
		$urls = array();
		
		$cache = AcesefFactory::getCache();
		$urls = $cache->load('urls');
		
		return $urls;
	}
	
	// Change the used state
	function used($id) {
		// Set used to 2 for selected record
		$row = AcesefFactory::getTable('AcesefSefUrls');
		$row->load($id);
		$row->used = 2;
		$row->store();
		
		// Chagne the used state for other records
		$limit = "";
		if ($sefurl->used == 1 || $sefurl->used == 0) {
			$sefurl->used = 2;
			$used = 1;
		} elseif ($sefurl->used == 2) {
			$sefurl->used = 1;
			$used = 2;
			$limit = "LIMIT 1";
		}

		AceDatabase::query("UPDATE #__acesef_urls SET used = {$used} WHERE url_sef = '{$row->url_sef}' AND id != {$row->id} {$limit}");
	}
	
	// Query fileters
	function _buildViewWhere() {	
		$where = array();
		
		// Search SEF URL
		if ($this->search_sef != '') {
			$src = parent::secureQuery($this->search_sef, true);
			$where[] = "LOWER(url_sef) LIKE {$src}";
		}
		
		// Type Filter
		$type = $this->_db->getEscaped($this->type, true);
		if (isset($type)) {
			if ($type != 'trashed') {
				$where[] = "params LIKE '%trashed=0%'";
			}
			if ($type != 'notfound') {
				$where[] = "params LIKE '%notfound=0%'";
			}
			switch ($type) {
				case 'sef':
					$where[] = "params LIKE '%custom=0%'"; // SEF URLs
					break;
				case 'custom':
					$where[] = "params LIKE '%custom=1%'"; // Custom URLs
					break;
				case 'notfound':
					$where[] = "params LIKE '%notfound=1%'"; // 404 URLs
					break;
				case 'locked':
					$where[] = "params LIKE '%locked=1\nb%'"; // Locked URLs
					break;
				case 'blocked':
					$where[] = "params LIKE '%blocked=1%'"; // Blocked URLs
					break;
				case 'red':
					$where[] = "used = '0'"; // Red URLs
					break;
				case 'trashed':
					$where[] = "params LIKE '%trashed=1%'"; // Trashed URLs
					break;
				case 'moved':
					$this->_mainframe->redirect('index.php?option=com_acesef&controller=movedurls&task=view');
					break;
				default:
					$where[] = "params LIKE '%custom=0%'"; // SEF URLs
					break;
			}
		}
		
		// Search Real URL
		if ($this->search_real != '' && $type != 'notfound') {
			$src = parent::secureQuery($this->search_real, true);
			$where[] = "LOWER(url_real) LIKE {$src}";
		}
		
		// Component Filter
		if ($this->filter_component != '-1' && $type != 'notfound') {
			$src = $this->_db->getEscaped($this->filter_component, true);
			$where[]= "(LOWER(url_real) LIKE '%option={$src}&%' OR url_real LIKE '%option={$src}')";
		}
		
		// Language Filter
		if ($this->filter_lang != '-1') {
			$src = $this->_db->getEscaped($this->filter_lang, true);
			$where[]= "(LOWER(url_real) LIKE '%lang={$src}&%' OR url_real LIKE '%lang={$src}')";
		}
		
		// Published Filter
		if ($this->filter_published != '-1') {
			$src = $this->_db->getEscaped($this->filter_published, true);
			$where[] = "params LIKE '%published={$src}%'";
		}
	
		// Used Filter
		if ($this->filter_used != '-1') {
			$src = parent::secureQuery($this->filter_used);
			$where[] = "used = {$src}";
		}
		
		// Locked Filter
		if ($this->filter_locked != '-1') {
			$src = $this->_db->getEscaped($this->filter_locked, true);
			$where[] = "params LIKE '%locked={$src}\nb%'";
		}
		
		// Blocked Filter
		if ($this->filter_blocked != '-1') {
			$src = $this->_db->getEscaped($this->filter_blocked, true);
			$where[] = "params LIKE '%blocked={$src}%'";
		}
		
		// Date filtering
		$null_date = $this->_db->getNullDate();
		$null_date = parent::secureQuery($null_date);
		$from_date = $to_date = false;
		$fromdate = $this->_db->getEscaped($this->fromdate, true);
		if ($fromdate) {
			$dt = explode('.', $fromdate);
			$from_date = '20'.$dt[2].'-'.$dt[1].'-'.$dt[0];
			$from_date = parent::secureQuery($from_date);
		}
		
		$todate = $this->_db->getEscaped($this->todate, true);
		if ($todate) {
			$dt = explode('.', $todate);
			$to_date = '20'.$dt[2].'-'.$dt[1].'-'.$dt[0];
			$to_date = parent::secureQuery($to_date);
		}
		
		$filter_date = $this->_db->getEscaped($this->filter_date, true);
		if ($filter_date == 'c') {
			// From only
			if ($from_date && !$to_date) {
				$where[] = "cdate >= {$from_date}";
			}
			
			// To only
			if (!$from_date && $to_date) {
				$where[] = "cdate <= {$to_date}";
			}
			
			// Date range
			if ($from_date && $to_date) {
				$where[] = "(cdate >= {$from_date} AND cdate <= {$to_date})";
			}
		}
		
		if ($filter_date == 'm') {
			// From only
			if ($from_date && !$to_date) {
				$where[] = "(mdate >= {$from_date} OR (mdate = {$null_date} AND cdate >= {$from_date}))";
			}
			
			// To only
			if (!$from_date && $to_date) {
				$where[] = "(mdate <= {$to_date} OR (mdate = {$null_date} AND cdate <= {$to_date}))";
			}
			
			// Date range
			if ($from_date && $to_date) {
				$where[] = "((mdate >= {$from_date} OR (mdate = {$null_date} AND cdate >= {$from_date})) AND (mdate <= {$to_date} OR (mdate = {$null_date} AND cdate <= {$to_date})))";
			}
		}
		
		// Search hits
		if ($this->search_hits != '') {
			$src = parent::secureQuery($this->search_hits);
			$where[]= "hits = {$src}";
		}
		
		// Search id
		if ($this->search_id != '') {
			$src = parent::secureQuery($this->search_id);
			$where[]= "id = {$src}";
		}
		
		// Execute
		$where = (count($where) ? ' WHERE '. implode(' AND ', $where) : '');
		
		// Duplicated URLs
		if ($type == 'duplicated' || $type == 'red') {
			// Get ids
			$this->_db->setQuery("SELECT id FROM #__acesef_urls AS t1 INNER JOIN (SELECT url_sef FROM #__acesef_urls WHERE params LIKE '%trashed=0%' GROUP BY url_sef HAVING COUNT(url_sef) > 1) AS t2 ON t1.url_sef = t2.url_sef{$where}");
			$ids = $this->_db->loadResultArray();
			
			$where = " WHERE id = '-1'";
			if (count($ids) > 0) {
				$where = " WHERE id IN (" . implode(", ", $ids) . ")";
			}
		}
		
		return $where;
	}
	
	//
	// Edit methods
	//
	
	// Save & Moved
	function editSaveMoved($id = 0) {
		$row = AcesefFactory::getTable('AcesefMovedUrls'); 
		$row->url_old = JRequest::getVar('url_old');
		$row->url_new = JRequest::getVar('url_sef');
		
		// Make sure the record is valid
		if (!$row->check()) {
			return JError::raiseWarning(500, $row->getError());
		}
		
		// Save the changes
		if (!$row->store()) {
			return JError::raiseWarning(500, $row->getError());
		}
		
		return true;
	}
	
	function getEditDuplicates() {
		// Get vars
		$cid = JRequest::getVar('cid', array(0), 'method', 'array');
		$id = $cid[0];
		
		// Get URLs
		$rows = AceDatabase::loadObjectList("SELECT * FROM #__acesef_urls WHERE url_sef IN (SELECT url_sef FROM #__acesef_urls WHERE id = '{$id}') ORDER BY url_real");
	
		return $rows;
	}
	
	function _modifyPostData(&$post) {
		if (!isset($post['url_custom'])) {
			$post['url_custom'] = '0';
		}
		
		if (!isset($post['url_published'])) {
			$post['url_published'] = '0';
		}
		
		if (!isset($post['url_locked'])) {
			$post['url_locked'] = '0';
		}
		
		if (!isset($post['url_blocked'])) {
			$post['url_blocked'] = '0';
		}
		
		if (!isset($post['url_tags'])) {
			$post['url_tags'] = '0';
		}
		
		if (!isset($post['url_ilinks'])) {
			$post['url_ilinks'] = '0';
		}
		
		if (!isset($post['url_bookmarks'])) {
			$post['url_bookmarks'] = '0';
		}
		
		if (isset($post['url_cdate'])) {
			$post['cdate'] = $post['url_cdate'];
			unset($post['url_cdate']);
		}
		
		if (isset($post['url_mdate'])) {
			$post['mdate'] = $post['url_mdate'];
			unset($post['url_mdate']);
		}
		
		if (!isset($post['meta_published'])) {
			$post['meta_published'] = '0';
		}
		
		if (!isset($post['sm_published'])) {
			$post['sm_published'] = '0';
		}
		
		// Metadata
		$post2['metadata']['id'] = $post['meta_id'];
		$post2['metadata']['url_sef'] = $post['url_sef'];
		$post2['metadata']['published'] = $post['meta_published'];
		$post2['metadata']['title'] = $post['meta_title'];
		$post2['metadata']['description'] = $post['meta_desc'];
		$post2['metadata']['keywords'] = $post['meta_key'];
		$post2['metadata']['lang'] = $post['meta_lang'];
		$post2['metadata']['robots'] = $post['meta_robots'];
		$post2['metadata']['googlebot'] = $post['meta_googlebot'];
		$post2['metadata']['canonical'] = $post['meta_canonical'];
		unset($post['meta_id']);
		unset($post['meta_published']);
		unset($post['meta_title']);
		unset($post['meta_desc']);
		unset($post['meta_key']);
		unset($post['meta_lang']);
		unset($post['meta_robots']);
		unset($post['meta_google']);
		unset($post['meta_canonical']);
		
		// Sitemap
		$post2['sitemap']['id'] = $post['sm_id'];
		$post2['sitemap']['url_sef'] = $post['url_sef'];
		$post2['sitemap']['published'] = $post['sm_published'];
		$post2['sitemap']['sdate'] = $post['sm_date'];
		$post2['sitemap']['frequency'] = $post['sm_freq'];
		$post2['sitemap']['priority'] = $post['sm_priority'];
		unset($post['sm_id']);
		unset($post['sm_published']);
		unset($post['sm_date']);
		unset($post['sm_freq']);
		unset($post['sm_priority']);
		
		// Aliases
		$post2['aliases']['url_sef'] = $post['url_sef'];
		$post2['aliases']['url_alias'] = $post['url_alias'];
		unset($post['url_alias']);
		
		// Params
		$params = "custom=".$post['url_custom'];
		$params .= "\npublished=".$post['url_published'];
		$params .= "\nlocked=".$post['url_locked'];
		$params .= "\nblocked=".$post['url_blocked'];
		$params .= "\ntrashed=".$post['url_trashed'];
		$params .= "\nnotfound=".$post['url_notfound'];
		$params .= "\ntags=".$post['url_tags'];
		$params .= "\nilinks=".$post['url_ilinks'];
		$params .= "\nbookmarks=".$post['url_bookmarks'];
		$params .= "\nnotes=".$post['url_notes'];
		unset($post['url_custom']);
		unset($post['url_published']);
		unset($post['url_locked']);
		unset($post['url_blocked']);
		unset($post['url_trashed']);
		unset($post['url_notfound']);
		unset($post['url_tags']);
		unset($post['url_ilinks']);
		unset($post['url_bookmarks']);
		unset($post['url_notes']);
		
		$post['params'] = $params;
		
		return $post2;
	}
	
	function _saveAliases($aliases) {
		$url_sef = $aliases['url_sef'];
		$url_alias = $aliases['url_alias'];
		
		if (!empty($url_alias)) {
			// First delete all records
			AceDatabase::query("DELETE FROM #__acesef_urls_moved WHERE url_new = '{$url_sef}'");
			
			// Records the new entries
			$urls = explode("\n", trim($url_alias));
			foreach ($urls as $url_old) {
				$url_old = trim($url_old);
				$url_old = trim($url_old, "\n");
				
				if (!empty($url_old)) {
					AceDatabase::query("INSERT IGNORE INTO #__acesef_urls_moved (url_old, url_new) VALUES ('{$url_old}', '{$url_sef}')");
				}
			}
		}
	}
	
	function _saveMetadata($post) {
		if ($post['id'] == "") {
			$values = "('{$post['url_sef']}', '{$post['published']}', '{$post['title']}', '{$post['description']}', '{$post['keywords']}', '{$post['lang']}', '{$post['robots']}', '{$post['googlebot']}', '{$post['canonical']}')";
			AceDatabase::query("INSERT IGNORE INTO #__acesef_metadata (url_sef, published, title, description, keywords, lang, robots, googlebot, canonical) VALUES {$values}");
		} else {
			$fields = "url_sef = '{$post['url_sef']}', published = '{$post['published']}', title = '{$post['title']}', description = '{$post['description']}', keywords = '{$post['keywords']}', lang = '{$post['lang']}', robots = '{$post['robots']}', googlebot = '{$post['googlebot']}', canonical = '{$post['canonical']}'";
			AceDatabase::query("UPDATE #__acesef_metadata SET {$fields} WHERE id = {$post['id']}");
		}
	}
	
	function _saveSitemap($post) {
		if ($post['id'] == "") {
			$values = "('{$post['url_sef']}', '{$post['published']}', '{$post['sdate']}', '{$post['frequency']}', '{$post['priority']}')";
			AceDatabase::query("INSERT IGNORE INTO #__acesef_sitemap (url_sef, published, sdate, frequency, priority) VALUES {$values}");
		} else {
			$fields = "url_sef = '{$post['url_sef']}', published = '{$post['published']}', sdate = '{$post['sdate']}', frequency = '{$post['frequency']}', priority = '{$post['priority']}'";
			AceDatabase::query("UPDATE #__acesef_sitemap SET {$fields} WHERE id = {$post['id']}");
		}
	}
}
?>