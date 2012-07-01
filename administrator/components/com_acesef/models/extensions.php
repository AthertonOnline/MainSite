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

// Imports
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport('joomla.installer.helper');
jimport('joomla.installer.installer');
require_once(JPATH_ACESEF_ADMIN.DS.'adapters'.DS.'acesef_ext.php');

// Extensions Model Class
class AcesefModelExtensions extends AcesefModel {
	
	// Main constructer
	function __construct()	{
		parent::__construct('extensions');
		
		$this->_getUserStates();
		$this->_buildViewQuery();
	}
	
	function _getUserStates() {
		$this->filter_order		= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_order',		'filter_order',		'name');
		$this->filter_order_Dir	= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_order_Dir',	'filter_order_Dir',	'ASC');
		$this->search_name		= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.search_name', 		'search_name', 		'');
        $this->filter_router	= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_router', 	'filter_router', 	'-1');
		$this->search_prefix	= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.search_prefix', 	'search_prefix', 	'');
        $this->filter_skipmenu	= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_skipmenu', 	'filter_skipmenu', 	'-1');
		$this->search_name		= JString::strtolower($this->search_name);
		$this->search_prefix	= JString::strtolower($this->search_prefix);
	}
	
	function getToolbarSelections() {
		$toolbar = new stdClass();
		
        // Actions
        $act[] = JHTML::_('select.option', 'savedeleteurls', JText::_('Save') .' & '. JText::_('ACESEF_TOOLBAR_DELETE_URLS'));
        $act[] = JHTML::_('select.option', 'saveupdateurls', JText::_('Save') .' & '. JText::_('ACESEF_TOOLBAR_UPDATE_URLS'));
        $toolbar->action = JHTML::_('select.genericlist', $act, 'ext_action', 'class="inputbox" size="1"');
		
		// Selections
        $sel[] = JHTML::_('select.option', 'selected', JText::_('ACESEF_TOOLBAR_SELECTED'));
        $sel[] = JHTML::_('select.option', 'filtered', JText::_('ACESEF_TOOLBAR_FILTERED'));
        $toolbar->selection = JHTML::_('select.genericlist', $sel, 'ext_selection', 'class="inputbox" size="1"');
		
		// Button
        $toolbar->button = '<input type="button" value="'.JText::_('Apply').'" onclick="apply();" />';
		
		return $toolbar;
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
		
		// Search name
        $lists['search_name'] = "<input type=\"text\" name=\"search_name\" value=\"{$this->search_name}\" size=\"25\" maxlength=\"255\" onchange=\"document.adminForm.submit();\" />";

        // Search prefix
        $lists['search_prefix'] = "<input type=\"text\" name=\"search_prefix\" value=\"{$this->search_prefix}\" size=\"25\" maxlength=\"255\" onchange=\"document.adminForm.submit();\" />";
        
		// Router Filter
		$router_list[] = JHTML::_('select.option', '-1', JText::_('ACESEF_COMMON_SELECT'));
		$router_list[] = JHTML::_('select.option', '3', JText::_('ACESEF_EXTENSIONS_VIEW_SELECT_EXTENSION'));
		$router_list[] = JHTML::_('select.option', '2', JText::_('ACESEF_EXTENSIONS_VIEW_SELECT_15_ROUTER'));
		$router_list[] = JHTML::_('select.option', '1', JText::_('ACESEF_EXTENSIONS_VIEW_SELECT_ACESEF'));
		$router_list[] = JHTML::_('select.option', '0', JText::_('ACESEF_EXTENSIONS_VIEW_SELECT_DISABLE'));
   	   	$lists['router_list'] = JHTML::_('select.genericlist', $router_list, 'filter_router', 'class="inputbox" size="1"'.$javascript,'value', 'text', $this->filter_router);
		
		// Skip title Filter
		$skip_list[] = JHTML::_('select.option', '-1', JText::_('ACESEF_COMMON_SELECT'));
		$skip_list[] = JHTML::_('select.option', '1', JText::_('Yes'));
		$skip_list[] = JHTML::_('select.option', '0', JText::_('No'));
   	   	$lists['skip_list'] = JHTML::_('select.genericlist', $skip_list, 'filter_skipmenu', 'class="inputbox" size="1"'.$javascript,'value', 'text', $this->filter_skipmenu);
		
		return $lists;
	}
	
	// Routers state
	function checkComponents() {
		$filter = AcesefUtility::getSkippedComponents();
		$components = AceDatabase::loadResultArray("SELECT `element` FROM `#__extensions` WHERE `type` = 'component' AND `element` NOT IN ({$filter}) ORDER BY `element`");

		foreach ($components as $component) {
			// Check if there is already a record available
			$total = AceDatabase::loadResult("SELECT COUNT(*) FROM #__acesef_extensions WHERE extension = '{$component}'");
			
			if ($total < 1) {
				$name = "";
				$routed = false;
				
				if(!$routed){
					$ext = JPATH_ACESEF_ADMIN.DS.'extensions'.DS.$component.'.php';
					if (file_exists($ext)) {
						$name = AcesefUtility::getXmlText(JPATH_ACESEF_ADMIN.DS.'extensions'.DS.$component.'.xml', 'name');
						$router = 3;
						$routed = true;
					}
				}
				
				if(!$routed){
					$router = JPATH_SITE.DS.'components'.DS.$component.DS.'router.php';
					if (file_exists($router)) {
						$router = 2;
						$routed = true;
					}
				}
				
				if(!$routed){
					$router = 1;
					$routed = true;
				}
				
				if($routed){
					$params = "router={$router}";
					$params .= "\nprefix=";
					$params .= "\nskip_menu=0";
					AceDatabase::query("INSERT INTO #__acesef_extensions (name, extension, params) VALUES ('{$name}', '{$component}', '{$params}')");
				}
			}
		}
	}
	
	// Install / Upgrade extensions
	function installUpgrade() {
		// Check if the extensions directory is writable
		$directory = JPATH_ACESEF_ADMIN.DS.'extensions';
		if (!is_writable($directory)) {
			JError::raiseWarning('1001', JText::_('ACESEF_EXTENSIONS_VIEW_INSTALL_DIR_CHMOD_ERROR'));
		}
		
		$result = false;
		
		// Get vars
		$userfile 	= JRequest::getVar('install_package', null, 'files', 'array');
		$ext_url 	= JRequest::getVar('joomaceurl');
		
		// Manual upgrade or install
		AcesefUtility::import('library.installer');
		if ($userfile) {
			$package = AcesefInstaller::getPackageFromUpload($userfile);
		}
		// Automatic upgrade
		elseif($ext_url) {
			// Download the package
			$package = AcesefInstaller::getPackageFromServer($ext_url);
		}

		// Get an installer instance
		$installer =& JInstaller::getInstance();
        $adapter = new JInstallerAcesef_Ext($installer);
		$installer->setAdapter('acesef_ext', $adapter);

		// Install the package
		if (!$installer->install($package['dir'])) {
			// There was an error installing the package
			$msg = JText::sprintf('INSTALLEXT', JText::_($package['type']), JText::_('Error'));
			$result = false;
		} else {
			// Package installed sucessfully
			$msg = JText::sprintf('INSTALLEXT', JText::_($package['type']), JText::_('Success'));
			$result = true;
		}

		return $result;
	}
	
	// Uninstall extensions
	function uninstall() {
		// Get where
		$where = AcesefController::_buildSelectedWhere();
		
		// Get extensions
		$extensions = AceDatabase::loadAssocList("SELECT id, extension, params FROM #__acesef_extensions {$where}", "id");
		
		// Action
		foreach ($extensions as $id => $record) {
			$extension = $record['extension'];
			
			// Remove already created URLs for this extension from database
			if ($this->AcesefConfig->purge_ext_urls == 1) {
				AceDatabase::query("DELETE FROM #__acesef_urls WHERE (url_real LIKE '%option={$extension}&%' OR url_real LIKE '%option={$extension}') AND params LIKE '%locked=0\nb%'");
			}
			
			// Update router param
			$params = array();
			$router = 1;
			if (file_exists(JPATH_SITE.DS.'components'.DS.$extension.DS.'router.php')) {
				$router = 2;
			}
			
			$p = new JParameter($record['params']);
			
			$params['router'] = $router;
			$params['prefix'] = $p->get('prefix', '');
			$params['skip_menu'] = $p->get('skip_menu', '0');
			AcesefUtility::storeParams('AcesefExtensions', $id, 'params', $params);
			
			// Remove the extension files
			if (file_exists(JPATH_ACESEF_ADMIN.DS.'extensions'.DS.$extension.'.php')){
				JFile::delete(JPATH_ACESEF_ADMIN.DS.'extensions'.DS.$extension.'.xml');
				JFile::delete(JPATH_ACESEF_ADMIN.DS.'extensions'.DS.$extension.'.php');
			}
		}
		
		return;
	}
	
	// Save changes
	function save($ext_id = null, $function = "", $action = "") {
		$ids 		= JRequest::getVar('id');
		$router 	= JRequest::getVar('router');
		$prefix 	= JRequest::getVar('prefix');
		$skip_menu 	= JRequest::getVar('skip_menu');

		foreach ($ids as $id => $val) {
			$params = array();
			$params['router'] = $router[$id];
			$params['prefix'] = $prefix[$id];
			$params['skip_menu'] = $skip_menu[$id];
			AcesefUtility::storeParams('AcesefExtensions', $id, 'params', $params);
			
			if(!empty($function) && $id == $ext_id) {
				include_once(JPATH_ACESEF_ADMIN.DS.'models'.DS.'purgeupdate.php');
				$model = new AcesefModelPurgeUpdate();
				return $model->$function($ext_id, $action);
			}
		}
	}
	
	function getInfo() {
		static $information;
		
		$information = array();
		if ($this->AcesefConfig->version_checker == 1) {
			$information = AcesefUtility::getRemoteInfo();
			unset($information['acesef']);
		}
		
		return $information;
    }
	
	function getParams() {
		$params = AceDatabase::loadObjectList("SELECT extension, params FROM #__acesef_extensions", "extension");
		return $params;
	}
	
	function _buildViewQuery() {
		$where		= $this->_buildViewWhere();
		$orderby	= " ORDER BY {$this->filter_order} {$this->filter_order_Dir}, extension";
		
		$this->_query = "SELECT * FROM #__acesef_{$this->_table} {$where}{$orderby}";
	}
	
	// Filters function
	function _buildViewWhere() {
		$where = array();
		
		if ($this->search_name) {
			$src = parent::secureQuery($this->search_name, true);
			$where[] = "LOWER(name) LIKE {$src} OR LOWER(extension) LIKE {$src}";
		}
		
		if ($this->filter_router != -1) {
			$src = $this->_db->getEscaped($this->filter_router, true);
			$where[] = "params LIKE '%router={$src}%'";
		}
		
		if ($this->search_prefix) {
			$src = $this->_db->getEscaped($this->search_prefix, true);
			$where[] = "params LIKE '%prefix={$src}%'";
		}
		
		if ($this->filter_skipmenu != -1) {
			$src = $this->_db->getEscaped($this->filter_skipmenu, true);
			$where[] = "params LIKE '%skip_menu={$src}%'";
		}
		
		$where = (count($where) ? ' WHERE '. implode(' AND ', $where) : '');
		return $where;
	}
}
?>