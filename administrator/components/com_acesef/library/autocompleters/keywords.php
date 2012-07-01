<?php
// Get Autocompleter class
$library = dirname(dirname(__FILE__));
require_once($library.'/autocompleter.php');

// Get rows
$rows = AcesefAutocompleter::loadResultArray("SELECT keywords FROM", "acesef_metadata", "WHERE keywords != '' ORDER BY url_sef");

if (!empty($rows)) {
	$q = $_REQUEST['q'];
	$added = array();
	foreach($rows as $index => $row) {
		$keywords = explode(',', $row);
		foreach ($keywords as $key) {
			$key = trim($key);
			if(!isset($added[$key]) && strpos(strtolower($key), $q) === 0) {
				echo $key . "\n";
				$added[$key] = "a";
			}
		}
	}
}
?>
