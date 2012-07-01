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
class AcesefModelPurgeUpdate extends AcesefModel {
	
	// Main constructer
	function __construct() {
        parent::__construct('purgeupdate');
    }
    
	// Delete URLs
    function deleteURLs($ext_id = "", $action = "") {
		$rt = false;
		
		// Get selections
		$urls_sef		= JRequest::getInt('urls_sef', 0, 'post');
		$urls_custom	= JRequest::getInt('urls_custom', 0, 'post');
		$urls_404		= JRequest::getInt('urls_404', 0, 'post');
		$urls_moved 	= JRequest::getInt('urls_moved', 0, 'post');
		$urls_locked	= JRequest::getInt('urls_locked', 0, 'post');
		$urls_trashed	= JRequest::getInt('urls_trashed', 0, 'post');
		
		if (!empty($ext_id)) {
			$component = AceDatabase::loadResult("SELECT extension FROM #__acesef_extensions WHERE id = {$ext_id}");
			$where = " WHERE url_real LIKE '%option={$component}%' AND params LIKE '%notfound=0%' AND params LIKE '%custom=0%' AND params LIKE '%locked=0\nb%' AND params LIKE '%trashed=0%'";
			AceDatabase::query("DELETE FROM #__acesef_urls{$where}");
			return true;
		}
		
		if ($urls_sef) {
			$where = " WHERE params LIKE '%notfound=0%' AND params LIKE '%custom=0%' AND params LIKE '%locked=0\nb%' AND params LIKE '%trashed=0%'";
			AceDatabase::query("DELETE FROM #__acesef_urls{$where}");
			$rt = true;
		}
		
		if ($urls_custom) {
			$where = " WHERE params LIKE '%notfound=0%' AND params LIKE '%custom=1%' AND params LIKE '%locked=0\nb%' AND params LIKE '%trashed=0%'";
			AceDatabase::query("DELETE FROM #__acesef_urls{$where}");
			$rt = true;
		}
		
		if ($urls_404) {
			$where = " WHERE params LIKE '%notfound=1%'";
			AceDatabase::query("DELETE FROM #__acesef_urls{$where}");
			$rt = true;
		}
		
		if ($urls_moved) {
			AceDatabase::query("DELETE FROM #__acesef_urls_moved");
			$rt = true;
		}
		
		if ($urls_locked) {
			$where = " WHERE params LIKE '%locked=1\nb%' AND params LIKE '%trashed=0%'";
			AceDatabase::query("DELETE FROM #__acesef_urls{$where}");
			$rt = true;
		}
		
		if ($urls_trashed) {
			$where = " WHERE params LIKE '%notfound=0%' AND params LIKE '%trashed=1%'";
			AceDatabase::query("DELETE FROM #__acesef_urls{$where}");
			$rt = true;
		}
		
		return $rt;
    }
    
	// Update URLs
    function updateURLs($ext_id = "", $action = "") {
		$rows = "";
		$where = "";
		$sef = 0;
		$locked = 0;
		$custom = 0;
		
		// Get selections
		$urls_sef		= JRequest::getInt('urls_sef', 0, 'post');
		$urls_custom	= JRequest::getInt('urls_custom', 0, 'post');
		$urls_locked	= JRequest::getInt('urls_locked', 0, 'post');
		
		if (!empty($ext_id)) {
			$component = AceDatabase::loadResult("SELECT extension FROM #__acesef_extensions WHERE id = ".$ext_id);
			$where = " WHERE url_real LIKE '%option={$component}%' AND params LIKE '%notfound=0%' AND params LIKE '%custom=0%' AND params LIKE '%locked=0\nb%' AND params LIKE '%trashed=0%'";
			$rows = AceDatabase::loadObjectList("SELECT url_sef, url_real FROM #__acesef_urls{$where}");
			return AcesefURI::updateURLs($rows, $where);
		}
		
		if ($urls_sef) {
			$where = " WHERE params LIKE '%notfound=0%' AND params LIKE '%custom=0%' AND params LIKE '%locked=0\nb%' AND params LIKE '%trashed=0%'";
			$rows = AceDatabase::loadObjectList("SELECT url_sef, url_real FROM #__acesef_urls{$where}");
			$sef = AcesefURI::updateURLs($rows, $where);
		}
		
		if ($urls_custom) {
			$where = " WHERE params LIKE '%notfound=0%' AND  params LIKE '%custom=1%' AND params LIKE '%locked=0\nb%' AND params LIKE '%trashed=0%'";
			$rows = AceDatabase::loadObjectList("SELECT url_sef, url_real FROM #__acesef_urls{$where}");
			$custom = AcesefURI::updateURLs($rows, $where);
		}
		
		if ($urls_locked) {
			$where = " WHERE params LIKE '%locked=1\nb%' AND params LIKE '%trashed=0%'";
			$rows = AceDatabase::loadObjectList("SELECT url_sef, url_real FROM #__acesef_urls{$where}");
			$locked = AcesefURI::updateURLs($rows, $where);
		}
		
		$rt = $sef + $custom + $locked;
		
		return $rt;
    }
	
	function deleteUpdateMeta($action) {
		// Get selections
		$meta_all       = JRequest::getInt('meta_all', 0, 'post');
		$meta_title 	= JRequest::getInt('meta_title', 0, 'post');
		$meta_desc      = JRequest::getInt('meta_desc', 0, 'post');
		$meta_key       = JRequest::getInt('meta_key', 0, 'post');

		$fields = array();
		$where = "";
		
		if ($meta_all) {
			$fields[] = "title";
			$fields[] = "description";
			$fields[] = "keywords";
		} else {
			if ($meta_title) {
				$fields[] = "title";
			}
			if ($meta_desc) {
				$fields[] = "description";
			}
			if ($meta_key) {
				$fields[] = "keywords";
			}
		}
		
		return AcesefMetadata::$action($where, $fields);
	}
	
	// Clean Cache
    function cleanCache() {
		$cache = AcesefFactory::getCache();
		$rt = false;
		
		// Get selections
		$cache_versions		= JRequest::getInt('cache_versions', 0, 'post');
		$cache_extensions	= JRequest::getInt('cache_extensions', 0, 'post');
		$cache_urls			= JRequest::getInt('cache_urls', 0, 'post');
		$cache_urls_moved	= JRequest::getInt('cache_urls_moved', 0, 'post');
		$cache_metadata		= JRequest::getInt('cache_metadata', 0, 'post');
		$cache_sitemap		= JRequest::getInt('cache_sitemap', 0, 'post');
		$cache_tags 		= JRequest::getInt('cache_tags', 0, 'post');
		$cache_ilinks 		= JRequest::getInt('cache_ilinks', 0, 'post');
		
		if ($cache_versions) {
			$cache->remove('versions');
			$rt = true;
		}
		
		if ($cache_extensions) {
			$cache->remove('extensions');
			$rt = true;
		}
		
		if ($cache_urls) {
			$cache->remove('urls');
			$rt = true;
		}
		
		if ($cache_urls_moved) {
			$cache->remove('urls_moved');
			$rt = true;
		}
		
		if ($cache_metadata) {
			$cache->remove('metadata');
			$rt = true;
		}
		
		if ($cache_sitemap) {
			$cache->remove('sitemap');
			$rt = true;
		}
		
		if ($cache_tags) {
			$cache->remove('tags');
			$rt = true;
		}
		
		if ($cache_ilinks) {
			$cache->remove('ilinks');
			$rt = true;
		}
		
		return $rt;
    }
    
	// Count URLs
    function getCountURLs() {
		$count = array();
		$count['sef'] = AceDatabase::loadResult("SELECT COUNT(id) FROM #__acesef_urls WHERE params LIKE '%custom=0%' AND params LIKE '%notfound=0%'");
		$count['custom'] = AceDatabase::loadResult("SELECT COUNT(id) FROM #__acesef_urls WHERE params LIKE '%custom=1%' AND params LIKE '%notfound=0%'");
		$count['404'] = AceDatabase::loadResult("SELECT COUNT(id) FROM #__acesef_urls WHERE params LIKE '%notfound=1%'");
		$count['moved'] = AceDatabase::loadResult("SELECT COUNT(id) FROM #__acesef_urls_moved");
		$count['locked'] = AceDatabase::loadResult("SELECT COUNT(id) FROM #__acesef_urls WHERE params LIKE '%locked=1\nb%'");
		$count['trashed'] = AceDatabase::loadResult("SELECT COUNT(id) FROM #__acesef_urls WHERE params LIKE '%trashed=1%'");
		$count['total'] = array_sum($count);
		
		return $count;
    }
	
	// Count URLs
    function getCountMeta() {
		$count = array();
		$count['all'] = AceDatabase::loadResult("SELECT COUNT(id) FROM #__acesef_metadata WHERE title != '' AND description != '' AND keywords != ''");
		$count['title'] = AceDatabase::loadResult("SELECT COUNT(id) FROM #__acesef_metadata WHERE title != ''");
		$count['desc'] = AceDatabase::loadResult("SELECT COUNT(id) FROM #__acesef_metadata WHERE description != ''");
		$count['key'] = AceDatabase::loadResult("SELECT COUNT(id) FROM #__acesef_metadata WHERE keywords != ''");
		$count['total'] = array_sum($count);
		
		return $count;
    }
    
	// Count cache
    function getCountCache() {
		$cache = AcesefFactory::getCache();
		
		$count = array();
		$items = array('versions', 'extensions', 'urls', 'urls_moved', 'metadata', 'sitemap', 'tags', 'ilinks');
		
		foreach ($items as $item) {
			$contents = $cache->load($item);
			if (!empty($contents)) {
				$count[$item] = count($contents);
			} else {
				$count[$item] = 0;
			}
		}
		
		return $count;
    }
}
?>