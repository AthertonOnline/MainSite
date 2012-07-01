<?php
/**
* @version		1.5.0
* @package		AceSEF Library
* @subpackage	Backup/Restore
* @copyright	2009-2010 JoomAce LLC, www.joomace.net
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// Backup/Restore class
class AcesefBackupRestore {
	
	protected $_dbprefix;
	protected $_table;
	protected $_where;

	function __construct($options = "") {
		if (is_array($options)) {
			if (isset($options['_table'])) {
				$this->_table = $options['_table'];
			}
			
			if (isset($options['_where'])) {
				$this->_where = $options['_where'];
			}
		}
		
		$this->_dbprefix = JFactory::getConfig()->getValue('config.dbprefix');
	}
	
	function backupSefUrls() {
		$filename = "acesef_urls.sql";
		$fields = array('url_sef', 'url_real', 'used', 'cdate', 'mdate', 'hits', 'source', 'params');
		$line = "INSERT INTO {$this->_dbprefix}acesef_{$this->_table} (".implode(', ', $fields).")";
		$query = "SELECT url_sef, url_real, used, cdate, mdate, hits, source, params FROM #__acesef_{$this->_table}{$this->_where}";
		
		return array($query, $filename, $fields, $line);
	}
	
	function backupMovedUrls() {
		$filename = "acesef_moved_urls.sql";
		$fields = array('url_new', 'url_old', 'published', 'hits', 'last_hit');
		$line = "INSERT INTO {$this->_dbprefix}acesef_{$this->_table} (".implode(', ', $fields).")";
		$query = "SELECT url_new, url_old, published, hits, last_hit FROM #__acesef_{$this->_table} {$this->_where}";
		
		return array($query, $filename, $fields, $line);
	}
	
	function backupMetadata() {
		$filename = "acesef_metadata.sql";
		$fields = array('url_sef', 'published', 'title', 'description', 'keywords', 'lang', 'robots', 'googlebot', 'canonical');
		$line = "INSERT INTO {$this->_dbprefix}acesef_{$this->_table} (".implode(', ', $fields).")";
		$query = "SELECT url_sef, published, title, description, keywords, lang, robots, googlebot, canonical FROM #__acesef_{$this->_table} {$this->_where}";
		
		return array($query, $filename, $fields, $line);
	}
	
	function backupSitemap() {
		$filename = "acesef_sitemap.sql";
		$fields = array('url_sef', 'title', 'published', 'sdate', 'frequency', 'priority', 'sparent', 'sorder');
		$line = "INSERT INTO {$this->_dbprefix}acesef_{$this->_table} (".implode(', ', $fields).")";
		$query = "SELECT url_sef, title, published, sdate, frequency, priority, sparent, sorder FROM #__acesef_{$this->_table} {$this->_where}";
		
		return array($query, $filename, $fields, $line);
	}
	
	function backupTags() {
		$filename = "acesef_tags.sql";
		$fields = array('title', 'alias', 'published', 'description', 'ordering', 'hits');
		$line = "INSERT INTO {$this->_dbprefix}acesef_{$this->_table} (".implode(', ', $fields).")";
		$query = "SELECT title, alias, published, description, ordering, hits FROM #__acesef_{$this->_table} {$this->_where}";
		
		return array($query, $filename, $fields, $line);
	}
	
	function backupIlinks() {
		$filename = "acesef_internal_links.sql";
		$fields = array('word', 'link', 'published', 'nofollow', 'iblank', 'ilimit');
		$line = "INSERT INTO {$this->_dbprefix}acesef_{$this->_table} (".implode(', ', $fields).")";
		$query = "SELECT word, link, published, nofollow, iblank, ilimit FROM #__acesef_{$this->_table}{$this->_where}";
		
		return array($query, $filename, $fields, $line);
	}
	
	function backupBookmarks() {
		$filename = "acesef_bookmarks.sql";
		$fields = array('name', 'html', 'btype', 'placeholder', 'published');
		$line = "INSERT INTO {$this->_dbprefix}acesef_{$this->_table} (".implode(', ', $fields).")";
		$query = "SELECT name, html, btype, placeholder, published FROM #__acesef_{$this->_table} {$this->_where}";
		
		return array($query, $filename, $fields, $line);
	}
	
	function restoreSefUrls($line) {
		$preg = '/^INSERT INTO `?(\w)+acesef_urls`?/';
	
		// 1.3 compatibility
		if (strpos($line, 'metatitle')) {
			$qq = explode(") VALUES ('", $line);
			$line = trim(str_replace("');", "", $qq[1]));
			
			// Get fields
			$fields = explode("', '", $line);
			
			$vars = array();
			$vars['url_sef'] = $fields[0];
			$vars['url_real'] = $fields[2];
			$vars['mdate'] = $fields[16];
			$vars['used'] = $fields[10];
			$vars['source'] = $fields[17];
			$vars['published'] = $fields[9];
			$vars['locked'] = $fields[11];
			$vars['blocked'] = $fields[12];
			$vars['notes'] = $fields[13];
			
			$line = self::_getAcesefLine($vars);
		}
		else {
			$qq = explode(") VALUES ('", $line);
			$line = trim(str_replace("');", "", $qq[1]));
			
			// Get fields
			$fields = explode("', '", $line);
			
			if (!empty($fields[7])) {
				$params = '';
				$prms = explode(' ', $fields[7]);
				
				if (is_array($prms)) {
					foreach ($prms as $prm) {
						$params .= "{$prm}\n";
					}
				}
			}
			
			$f = 'url_sef, url_real, used, cdate, mdate, hits, source, params';
			$v = "'{$fields[0]}', '{$fields[1]}', '{$fields[2]}', '{$fields[3]}', '{$fields[4]}', '{$fields[5]}', '{$fields[6]}', '{$params}'";
			$line = "INSERT INTO {$this->_dbprefix}acesef_urls ({$f}) VALUES ({$v});";
		}
		
		return array($preg, $line);
	}
	
	function restoreMovedUrls($line) {
		$preg = '/^INSERT INTO `?(\w)+acesef_urls_moved`?/';
		
		return array($preg, $line);
	}
	
	function restoreMetadata($line) {
		$preg = '/^INSERT INTO `?(\w)+acesef_metadata`?/';
		
		// 1.3 compatibility
		if (strpos($line, ',b,')) {
			$fields = explode(',b,', $line);
			
			$line = "";
			
			$url_sef = AceDatabase::query("SELECT url_sef FROM #__acesef_urls WHERE params LIKE '%notfound=0%' AND url_real = '{$fields[0]}'");
			
			if ($url_sef) {
				$f = "url_sef, title, description, keywords, lang, robots, googlebot, canonical";
				$v = "'{$url_sef}', '{$fields[1]}', '{$fields[2]}', '{$fields[3]}', '{$fields[4]}', '{$fields[5]}', '{$fields[6]}', '{$fields[7]}'";
				$line = "INSERT INTO {$this->_dbprefix}acesef_metadata ({$f}) VALUES ({$v});";
			}
		}
		
		return array($preg, $line);
	}
	
	function restoreSitemap($line) {
		$preg = '/^INSERT INTO `?(\w)+acesef_sitemap`?/';
		
		// 1.3 compatibility
		if (strpos($line, ',b,')) {
			$fields = explode(',b,', $line);
			
			$line = "";
			
			$url_sef = AceDatabase::query("SELECT url_sef FROM #__acesef_urls WHERE params LIKE '%notfound=0%' AND url_real = '{$fields[0]}'");
			
			if ($url_sef) {
				$f = "url_sef, published, sdate, frequency, priority, sparent, sorder";
				$v = "'{$url_sef}', '{$fields[1]}', '{$fields[2]}', '{$fields[3]}', '{$fields[4]}', '0', '1000'";
				$line = "INSERT INTO {$this->_dbprefix}acesef_sitemap ({$f}) VALUES ({$v});";
			}
		}
		
		return array($preg, $line);
	}
	
	function restoreTags($line) {
		$preg = '/^INSERT INTO `?(\w)+acesef_tags`?/';
		
		return array($preg, $line);
	}
	
	function restoreIlinks($line) {
		$preg = '/^INSERT INTO `?(\w)+acesef_ilinks`?/';
		
		return array($preg, $line);
	}
	
	function restoreBookmarks($line) {
		$preg = '/^INSERT INTO `?(\w)+acesef_bookmarks`?/';
		
		return array($preg, $line);
	}
	
	function restoreJoomsef($line) {
		$preg = '/^INSERT INTO `?(\w)+acesef_urls`?/';		
		
		$qq = explode(") VALUES ('", $line);
		$line = trim(str_replace("');", "", $qq[1]));
		
		if (empty($line)) {
			return array($preg, '');
		}
		
		// Get fields
		$fields = explode("', '", $line);
		$this->_cleanFields($field);
		
		$real_url = $fields[2];
		
		// Sort JoomSEF URL structure according to AceSEF
		if(!empty($real_url) && !empty($fields[3])){
			$urlArray = explode("&", $real_url); // explode url to insert itemid after option
			$real_url = $urlArray[0]."&Itemid=".$fields[3]; // join itemid
			if(!empty($urlArray[1])) $real_url .= '&'.$urlArray[1];
			if(!empty($urlArray[2])) $real_url .= '&'.$urlArray[2];
			if(!empty($urlArray[3])) $real_url .= '&'.$urlArray[3];
			if(!empty($urlArray[4])) $real_url .= '&'.$urlArray[4];
			if(!empty($urlArray[5])) $real_url .= '&'.$urlArray[5];
			if(!empty($urlArray[6])) $real_url .= '&'.$urlArray[6];
			if(!empty($urlArray[7])) $real_url .= '&'.$urlArray[7];
			if(!empty($urlArray[8])) $real_url .= '&'.$urlArray[8];
		}		
		
		$vars = array();
		$vars['url_sef'] = $fields[1];
		$vars['url_real'] = $real_url;
		$vars['mdate'] = $fields[11];
		$vars['used'] = 1;
		$vars['source'] = '';
		$vars['published'] = 1;
		$vars['locked'] = 0;
		$vars['blocked'] = 0;
		$vars['notes'] = '';			
		$line = self::_getAcesefLine($vars);

		// Metadata
		$no_meta = (empty($fields[6]) && empty($fields[4]) && empty($fields[5]));
		if (!$no_meta && !is_object(AceDatabase::loadObject("SELECT url_sef FROM #__acesef_metadata WHERE url_sef = '{$fields[1]}'"))) {
			$f = "url_sef, title, description, keywords, lang, robots, googlebot, canonical";
			$v = "'{$fields[1]}', '{$fields[6]}', '{$fields[4]}', '{$fields[5]}', '{$fields[7]}', '{$fields[8]}', '{$fields[9]}', '{$fields[10]}'";
			AceDatabase::query("INSERT INTO #__acesef_metadata ({$f}) VALUES ({$v})");
		}
		
		return array($preg, $line);
	}
	
	function restoreShUrl($line) {
		$preg = '/^INSERT INTO `?(\w)+acesef_urls`?/';

		$delete = '"id","Count","Rank","SEF URL","non-SEF URL","Date added"\n';
		$line = str_replace($delete, " ", $line);
		$line = trim(str_replace("\n", " ", $line));
		
		if (empty($line)) {
			return array($preg, '');
		}
		
		$fields = explode('","', $line);
		$this->_cleanFields($fields);
		$this->_shUnEmpty($fields);
		
		// Remove lang string if JoomFish not installed :   index.php?option=com_banners&bid=3&lang=en&task=click
		if (!AcesefUtility::JoomFishInstalled() && !empty($fields[4])) {
			$pos = strpos($fields[4], 'lang=');
			$lang = substr($fields[4], $pos, 8);
			$fields[4] = str_replace($lang, "", $fields[4]);
		}
		
		$vars = array();
		$vars['url_sef'] = $fields[3];
		$vars['url_real'] = $fields[4];
		$vars['mdate'] = $fields[5];
		$vars['used'] = 1;
		$vars['source'] = '';
		$vars['published'] = 1;
		$vars['locked'] = 0;
		$vars['blocked'] = 0;
		$vars['notes'] = '';			
		$line = self::_getAcesefLine($vars);
		
		return array($preg, $line);
	}
	
	function restoreShMetadata($line) {
		$preg = '/^INSERT INTO `?(\w)+acesef_metadata`?/';
		
		$line = trim(str_replace(";;", "", $line));
		$delete = '"id","newurl","metadesc","metakey","metatitle","metalang","metarobots"';
		$line = str_replace($delete, " ", $line);
		$line = trim(str_replace("\n", " ", $line));
		
		if (empty($line)) {
			return array($preg, '');
		}
		
		$field = explode('","', $line);
		$this->_cleanFields($field);
		$this->_shUnEmpty($field);
		
		// Remove lang string if JoomFish not installed :   index.php?option=com_banners&bid=3&lang=en&task=click
		$path = JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_joomfish'.DS.'joomfish.php';
		if (!AcesefUtility::JoomFishInstalled() && !empty($field[1])) {
			$pos = strpos($field[1], 'lang=');
			$lang = substr($field[1], $pos, 8);
			$field[1] = str_replace($lang, "", $field[1]);
		}
		
		$line = "";
		
		$url_sef = AceDatabase::query("SELECT url_sef FROM #__acesef_urls WHERE params LIKE '%notfound=0%' AND url_real = '{$fields[1]}'");
		if ($url_sef && !is_object(AceDatabase::loadObject("SELECT url_sef FROM #__acesef_metadata WHERE url_sef = '{$url_sef}'"))) {
			$f = "url_sef, title, description, keywords, lang, robots";
			$v = "'{$url_sef}', '{$fields[4]}', '{$fields[2]}', '{$fields[3]}', '{$fields[5]}', '{$fields[6]}'";
			$line = "INSERT INTO {$this->_dbprefix}acesef_metadata ({$f}) VALUES ({$v});";
		}
		
		return array($preg, $line);
	}
	
	function restoreSh2($line) {
		$preg = '/^INSERT INTO `?(\w)+acesef_urls`?/';

		$delete = '"Nbr","Sef url","Non sef url","Hits","Rank","Date added","Page title","Page description","Page keywords","Page language","Robots tag"\n';
		$line = str_replace($delete, " ", $line);
		$line = trim(str_replace("\n", " ", $line));
		
		if (empty($line)) {
			return array($preg, '');
		}
		
		$fields = explode('","', $line);
		$this->_cleanFields($fields);
		$this->_shUnEmpty($fields);
		
		$real_url = $fields[2];
		
		// Remove lang string if JoomFish not installed :   index.php?option=com_banners&bid=3&lang=en&task=click
		if (!AcesefUtility::JoomFishInstalled() && !empty($real_url)) {
			$pos = strpos($real_url, 'lang=');
			$lang = substr($real_url, $pos, 8);
			$real_url = str_replace($lang, "", $real_url);
		}
		
		$vars = array();
		$vars['url_sef'] = $fields[1];
		$vars['url_real'] = $real_url;
		$vars['mdate'] = $fields[5];
		$vars['used'] = 1;
		$vars['source'] = '';
		$vars['published'] = 1;
		$vars['locked'] = 0;
		$vars['blocked'] = 0;
		$vars['notes'] = '';			
		$line = self::_getAcesefLine($vars);
		
		// Metadata
		$no_meta = (empty($fields[6]) && empty($fields[7]) && empty($fields[8]));
		if (!$no_meta && !is_object(AceDatabase::loadObject("SELECT url_sef FROM #__acesef_metadata WHERE url_sef = '{$fields[1]}'"))) {
			$f = "url_sef, title, description, keywords, lang, robots";
			$v = "'{$fields[1]}', '{$fields[6]}', '{$fields[7]}', '{$fields[8]}', '{$fields[9]}', '{$fields[10]}'";
			AceDatabase::query("INSERT INTO #__acesef_metadata ({$f}) VALUES ({$v})");
		}
		
		return array($preg, $line);
	}
	
	function restoreShAliases($line) {
		$preg = '/^INSERT INTO `?(\w)+acesef_urls_moved`?/';

		$delete = '"Nbr","Alias","Sef url","Non sef url","Type","Hits"';
		$line = str_replace($delete, " ", $line);
		$line = trim(str_replace("\n", " ", $line));
		
		if (empty($line)) {
			return array($preg, '');
		}
		
		$fields = explode('","', $line);
		$this->_cleanFields($fields);
		$this->_shUnEmpty($fields);
		
		$line = '';
		
		if (!is_object(AceDatabase::loadObject("SELECT url_old FROM #__acesef_urls_moved WHERE url_old = '{$fields[1]}'"))) {
			$f = "url_new, url_old, hits";
			$v = "'{$fields[2]}', '{$fields[1]}', '{$fields[5]}'";
			$line = "INSERT INTO {$this->_dbprefix}acesef_urls_moved ({$f}) VALUES ({$v});";
		}
		
		return array($preg, $line);
	}
	
	function _getAcesefLine($vars) {		
		$f = "url_sef, url_real, mdate, used, source, params";
		
		$url_real = $vars['url_real'];
		$notfound = 0;
		if ($url_real == "") {
			$url_real = $vars['url_sef'];
			$notfound = 1;
		}
		
		$found = AceDatabase::loadObject("SELECT url_sef FROM #__acesef_urls WHERE url_real = '{$url_real}'");
		if (is_object($found)) {
			return "";
		}
		
		$custom = 0;
		if ($vars['mdate'] != '0000-00-00') {
			$custom = 1;
		}
		
		switch($vars['used']) {
			case 10:
				$used = 2;
				break;
			case 0:
				$used = 1;
				break;
			case 5:
				$used = 0;
				break;
			default:
				$used = 0;
				break;
		}
		
		$trashed = 0;
		if (isset($vars['trashed'])) {
			$trashed = $vars['trashed'];
		}
		
		$params = "custom={$custom}";
		$params .= "\npublished={$vars['published']}";
		$params .= "\nlocked={$vars['locked']}";
		$params .= "\nblocked={$vars['blocked']}";
		$params .= "\ntrashed={$trashed}";
		$params .= "\nnotfound={$notfound}";
		$params .= "\ntags=0";
		$params .= "\nilinks=0";
		$params .= "\nbookmarks=0";
		$params .= "\nvisited=0";
		$params .= "\nnotes={$vars['notes']}";
		
		$v = "'{$vars['url_sef']}', '{$url_real}', '{$vars['mdate']} 00:00:00', '{$used}', '{$vars['source']}', '{$params}'";
		$line = "INSERT INTO {$this->_dbprefix}acesef_urls ({$f}) VALUES ({$v});";
		
		return $line;
	}
	
	function _cleanFields(&$fields) {
		for ($i = 0, $n = count($fields); $i < $n; $i++) {
			$replace = array('\"', "\'", '', '');
			$fields[$i] = str_replace(array('"', "'", '#', '`'), $replace, $fields[$i]);
		}
    }

    function _shUnEmpty(&$fields) {
		for ($i = 0, $n = count($fields); $i < $n; $i++) {
			if ($fields[$i] == '&nbsp') {
				$fields[$i] = '';
			}
		}
    }
}
?>