<?php
// Get Autocompleter class
$library = dirname(dirname(__FILE__));
require_once($library.'/autocompleter.php');

// Get URLs
$urls = AcesefAutocompleter::loadResultArray("SELECT url_sef FROM", "acesef_urls", "WHERE params LIKE '%notfound=0%' ORDER BY url_sef");

if (!empty($urls)) {
	$q = $_REQUEST['q'];
	$added = array();
	foreach($urls as $index => $url) {
		if(!isset($added[$url]) && strpos(strtolower($url), $q) === 0) {
			echo $url . "\n";
			$added[$url] = "a";
		}
	}
}
?>
