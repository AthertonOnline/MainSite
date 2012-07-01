<?php
/**
* @version		1.5.0
* @package		AceSEF Library
* @subpackage	Autocompleter
* @copyright	2009-2010 JoomAce LLC, www.joomace.net
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// Autocompleter class
class AcesefAutocompleter {
	
	function getJConfig() {
		static $jconfig;
		
		if (!isset($jconfig)) {
			$root = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
			require_once($root.'/configuration.php');
			
			$jconfig = new JConfig();
		}
		
		return $jconfig;
	}

	function loadResultArray($select, $table, $where, $index = 0) {
		$jconfig = self::getJConfig();
		
		$query = $select." ".$jconfig->dbprefix . $table." ".$where;
		
		if (!($results = self::_getResults($query))) {
			return null;
		}

		$rows = array();
		$db_fetch = $jconfig->dbtype . '_fetch_row';
		while ($row = $db_fetch($results)) {
			$rows[] = $row[$index];
		}
		
		$db_free_result = $jconfig->dbtype . '_free_result';
		$db_free_result($results);
		
		return $rows;
	}
	
	function _getResults($query) {
		$jconfig = self::getJConfig();
		
		$db_connect = $jconfig->dbtype . '_connect';
		$db_select = $jconfig->dbtype . '_select_db';
		$db_query = $jconfig->dbtype . '_query';
		
		$connection = $db_connect($jconfig->host, $jconfig->user, $jconfig->password) or die("Could not connect: " . mysql_error());
		
		if ($jconfig->dbtype == 'mysql') {
			$db_select($jconfig->db, $connection);
			$results = $db_query($query, $connection);
		}
		elseif ($jconfig->dbtype == 'mysqli') {
			$db_select($connection, $jconfig->db);
			$results = $db_query($connection, $query);
		}
		
		if (!$results) {
			return false;
		}
		
		return $results;
	}
}
?>