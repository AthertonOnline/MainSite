<?php
/**
* @version		1.5.0
* @package		AceSEF Library
* @subpackage	Model
* @copyright	2009-2010 JoomAce LLC, www.joomace.net
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// No Permission
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');
class AcesefModel extends JModel {
	
	public $_query;
	public $_data = null;
	public $_total = null;
	public $_pagination = null;
	public $_context;
	public $_mainframe;
	public $_option;
	public $_table;
	
	function __construct($context = '', $table = '') 	{
		parent::__construct();
		
		// Get config object
		$this->AcesefConfig = AcesefFactory::getConfig();
		
		// Get global vars
		$this->_mainframe =& JFactory::getApplication();
		if ($this->_mainframe->isAdmin()) {
			$this->_option = JAdministratorHelper::findOption();
		} else {
			$this->_option = JRequest::getCmd('option');
		}
		$this->_context = $context;
		
		$this->_table = $table;
		if ($table == '' && $this->_context != '') {
			$this->_table = $this->_context;
		}
		
		// Pagination
		if ($this->_context != '') {
			// Get the pagination request variables
			$limit		= $this->_mainframe->getUserStateFromRequest($this->_option . '.' . $this->_context . '.limit', 'limit', $this->_mainframe->getCfg('list_limit'), 'int');
			$limitstart	= $this->_mainframe->getUserStateFromRequest($this->_option . '.' . $this->_context . '.limitstart', 'limitstart', 0, 'int');
			
			// Limit has been changed, adjust it
			$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
			
			$this->setState($this->_option . '.' . $this->_context . '.limit', $limit);
			$this->setState($this->_option . '.' . $this->_context . '.limitstart', $limitstart);
		}
	}
	
	function _buildViewQuery() {
		$where		= $this->_buildViewWhere();
		$orderby	= " ORDER BY {$this->filter_order} {$this->filter_order_Dir}";
		
		$this->_query = "SELECT * FROM #__acesef_{$this->_table} {$where}{$orderby}";
	}
	
	function getItems() {
		if (empty($this->_data)) {
			$this->_data = $this->_getList($this->_query, $this->getState($this->_option.'.' . $this->_context . '.limitstart'), $this->getState($this->_option.'.' . $this->_context . '.limit'));
		}
		return $this->_data;
	}
	
	function getPagination() {
		if (empty($this->_pagination)) {
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination($this->getTotal(), $this->getState($this->_option.'.' . $this->_context . '.limitstart'), $this->getState($this->_option.'.' . $this->_context . '.limit'));
		}
		return $this->_pagination;
	}
	
	function getTotal() {
		if (empty($this->_total)) {			
			$this->_total = AceDatabase::loadResult("SELECT COUNT(*) FROM #__acesef_{$this->_table}".$this->_buildViewWhere());	
		}
		return $this->_total;
	}
	
	function getEditData($table) {
		// Get vars
		$cid = JRequest::getVar('cid', array(0), 'method', 'array');
		$id = $cid[0];
		
		// Load the record
		if (is_numeric($id)) {
			$row = AcesefFactory::getTable($table); 
			$row->load($id);
		}
	
		return $row;
	}
	
	function secureQuery($text, $all = false) {
		static $db;
		
		if (!isset($db)) {
			$db =& JFactory::getDBO();
		}
		
		$text = $db->getEscaped($text, true);
		
		if ($all) {
			$text = $db->Quote("%".$text."%", false);
		} else {
			$text = $db->Quote($text, false);
		}
		
		return $text;
	}
	
	function _getSecureUserState($long_name, $short_name, $default = null, $type = 'none') {
		$request = $this->_mainframe->getUserStateFromRequest($long_name, $short_name, $default, $type);
		
		if (is_string($request)) {
			$request = strip_tags(str_replace('"', '', $request));
		}
		
		return $request;
	}
}