<?php
/**
* @version		1.5.0
* @package		AceSEF Library
* @subpackage	Extension
* @copyright	2009-2010 JoomAce LLC, www.joomace.net
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// No Permission
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.filesystem.file');
require_once(JPATH_ACESEF_ADMIN.DS.'tables'.DS.'acesefextensions.php');

// Extension class
class AcesefExtension {
	
	protected $params = null;
	protected $meta_title = array();
	protected $meta_desc = null;
	
	function __construct($params = null) {
		// Get config object
		$this->AcesefConfig = AcesefFactory::getConfig();
		
		// Meta
		$this->meta_title = array();
		$this->meta_desc = null;
		
		$this->params = $params;
		
		// Skip menu
		self::skipMenu(false);
	}

    function is16() {
		static $status;
		
		if (!isset($status)) {
			if (version_compare(JVERSION,'1.6.0','ge')) {
				$status = true;
			} else {
				$status = false;
			}
		}
		
		return $status;
	}
	
	function resetMetadata() {
		$this->meta_title = array();
		$this->meta_desc = null;
	}
	
	function skipMenu($status, $get = false) {
		static $skip_menu = false;
		
		if ($get) {
			return $skip_menu;
		}
		
		$skip_menu = $status;
    }
	
	function beforeBuild(&$uri) {
    }
	
	function catParam($vars) {
    }
	
	function build(&$vars, &$segments, &$do_sef, &$metadata, &$item_limitstart) {
    }
	
	function afterBuild(&$uri) {
    }
	
	// Define title or alias
	function urlPart($param) {
        if (($param == 'title') || ($param == 'global' && $this->AcesefConfig->title_alias == 'title')) {
            return 'title';
        }
        return 'alias';
    }
	
	function categoryParam($area, $action = 2, $id = 0, $is_cat = 0, $real_url = "") {
		self::categoryParams($id, $is_cat, $real_url);
	}
	
	function categoryParams($id = 0, $is_cat = 0, $real_url = "") {
		$vars = array();
		$areas = array('sm_auto_cats', 'tags_cats', 'ilinks_cats', 'bookmarks_cats');
		
		foreach ($areas as $a) {
			if (!isset($vars[$a.'_status'])) {
				$vars[$a.'_status'] = 0;
			}
			if (!isset($vars[$a.'_flag'])) {
				$vars['_flag'] = 0;
			}
			if (!isset($vars['_is_cat'])) {
				$vars['_is_cat'] = $is_cat;
			}
			if (!isset($vars['_real_url'])) {
				$vars['_real_url'] = $real_url;
			}
		}
		
		foreach ($areas as $a) {
			$categories = $this->params->get($a, '-11');
			if ($categories == 'all') {
				$vars[$a.'_status'] = 1;
			}
			elseif (is_array($categories) && in_array($id, $categories)) {
				$vars[$a.'_status'] = 1;
			}
			elseif ($categories == $id) {
				$vars[$a.'_status'] = 1;
			}
		}
		$vars['_flag'] = 1;
		
		AcesefUtility::set('category.param', $vars);
	}
	
	function getMetaData($vars, $item_limitstart = false) {		
		$auto_title			= self::_autoTitle($this->params->get('meta_title', 'global'));
		$auto_desc			= self::_autoDesc($this->params->get('meta_desc', 'global'));
		$auto_key			= self::_autoKey($this->params->get('meta_key', 'global'));
		$separator			= $this->params->get('separator', '-');
		$sitename			= JFactory::getConfig()->getValue('sitename');
		$custom_sitename	= $this->params->get('custom_sitename', '');
		$use_sitename		= $this->params->get('use_sitename', '2');
		$title_prefix		= $this->params->get('title_prefix', '');
		$title_suffix		= $this->params->get('title_suffix', '');
		
		$acesef_title = $acesef_desc = $acesef_key = "";
		$title = $desc = $key = "";
		
		// Prepare meta title
		if (!empty($this->meta_title)) {
			$acesef_title = AcesefUtility::cleanText(implode(" ".$separator." ", $this->meta_title));
		}
		
		$b_title = $acesef_title;
		
		$page_number = "";
		if ($this->params->get('page_number', '2') == '2' && !empty($vars) && !empty($vars['limitstart'])) {
			$number	= AcesefURI::getPageNumber($vars, $this->params, $item_limitstart);
			$page_number = JText::_('PAGE').' '.$number;
		}
		
		if (!empty($acesef_title)) {
			if (!empty($page_number)) {
				$acesef_title = $acesef_title." ".$separator." ".$page_number;
			}
			
			if (!empty($custom_sitename)) {
				$sitename = $custom_sitename;
			}
			
			if ($use_sitename == 1) {
				$acesef_title = $sitename." ".$separator." ".$acesef_title;
			} elseif ($use_sitename == 2) {
				$acesef_title = $acesef_title." ".$separator." ".$sitename;
			}
			
			if (!empty($title_prefix)) {
				$acesef_title = $title_prefix." ".$separator." ".$acesef_title;
			}
			
			if (!empty($title_suffix)) {
				$acesef_title = $acesef_title." ".$separator." ".$title_suffix;
			}
		}
		
		if ($this->params->get('desc_inc_title', '2') == '2' && !empty($b_title)) {
			$this->meta_desc = $b_title.' '.$this->meta_desc;
		}
		
        $clean_desc = AcesefUtility::cleanText($this->meta_desc);
		
		$acesef_title	= AcesefUtility::cleanText($acesef_title);
		$acesef_desc	= self::_clipDesc($clean_desc);
		$acesef_key		= self::_generateKeywords($clean_desc);
		
		// Set extension metadata
		$mainframe =& JFactory::getApplication();
		$mainframe->set('acesef.meta.autodesc', $acesef_desc);
		$mainframe->set('acesef.meta.autokey', $acesef_key);
		
		// Meta title
		if ($auto_title) {
			$title = $acesef_title;
		}
		
		// Meta description
		if ($auto_desc) {
			$desc = $acesef_desc;
		}
		
		// Meta keywords
		if ($auto_key) {
			$key = $acesef_key;
		}
		
		// Set metadata
		$meta = array();
		$meta['title']			= $title;
		$meta['description']	= $desc;
		$meta['keywords']		= $key;
		
		return $meta;
	}
	
	// Define meta title generation
	function _autoTitle($param) {
        if (($param == 'no') || ($param == 'global' && $this->AcesefConfig->meta_title == '0')) {
            return false;
        }
        return true;
    }
	
	// Define meta desc generation
	function _autoDesc($param) {
        if (($param == 'no') || ($param == 'global' && ($this->AcesefConfig->meta_desc == '2' || $this->AcesefConfig->meta_desc == '3'))) {
            return false;
        }
        return true;
    }
	
	// Define meta title generation
	function _autoKey($param) {
        if (($param == 'no') || ($param == 'global' && ($this->AcesefConfig->meta_key == '2' || $this->AcesefConfig->meta_key == '3'))) {
            return false;
        }
        return true;
    }
	
	// Clip text to use as meta description
	function _clipDesc($text) {		
		// Get params
		$desc_clip		= $this->params->get('desc_clip', '1');
		$desc_clip_s	= $this->params->get('desc_clip_s', '2');
		$desc_clip_w	= $this->params->get('desc_clip_w', '20');
		$desc_clip_c	= $this->params->get('desc_clip_c', '250');
		$description	= "";
		
		// Sentence clip
		if ($desc_clip == '1') {
			$description = "";
			$pattern = '/\b(.+?[\.|\!|\?])/u';
			
			for ($i=0; $i < $desc_clip_s; $i++) {
				$offset = "";
				if (preg_match($pattern, $text, $matches)) {
					$match = $matches[1];
				} else {
					break;
				}
				
				$description .= " ".$match;
				
				$offset = strpos($text, $match);
				$offset += strlen($match);
				$text = substr($text, $offset);
			}
		} 
		
		// Word clip
		if ($desc_clip == '2') {
			$explode = explode(' ',trim($text));
	    	$string = '';
	    
	    	for ($i=0; $i < $desc_clip_w; $i++) {
	        	if (isset($explode[$i])) {
		        	$string .= $explode[$i]." ";
	    		} else {
	    			break;
	    		}
	    	} 
	        $description = trim($string);
		}
		
		// Char clip
		if ($description == '' || $desc_clip == '3') {
            $text = substr($text, 0, $desc_clip_c);
            $pos = strrpos($text, ' ');
            if ($pos !== false) {
                $text = substr($text, 0, $pos - 1);
            }
            $description = trim($text);
        }
		
		return $description;
	}
	
	// Generate keywords
	function _generateKeywords($text) {		
		$keywords_word	= $this->params->get('keywords_word', '3');
		$keywords_count	= $this->params->get('keywords_count', '15');
		$blacklist		= self::_getKeywordsList($this->params->get('keywords_backlist', 'global'), 'blacklist');
		$whitelist		= self::_getKeywordsList($this->params->get('keywords_whitelist', 'global'), 'whitelist');
		
		// Firstly, cleanup text
		$text = AcesefUtility::cleanText($text);
		
		// Remove any email addresses
		$regex = '/(([_A-Za-z0-9-]+)(\\.[_A-Za-z0-9-]+)*@([A-Za-z0-9-]+)(\\.[A-Za-z0-9-]+)*)/iex';
		$text = preg_replace($regex, '', $text);
		
		// Some unwanted replaces
		$text = str_replace(array('?', '!'), '', $text);
		
		// Lowercase the strings		
		$text = JString::strtolower($text);
        
		// Sort words from up to down
		$keys_array = explode(" ", $text);
		$keys_array = array_count_values($keys_array);
		
		$new_keys_array = array();
		
		if (!empty($whitelist)) {
			$white_array = explode(",", $whitelist);
			foreach ($white_array as $white_word) {
				$white_word = JString::strtolower($white_word);;
				if (isset($keys_array[trim($white_word)])) {
					$new_keys_array[] = trim($white_word);
					unset($keys_array[trim($white_word)]);
				}
			}
		}
		
		if (!empty($blacklist)) {
			$black_array = explode(",", $blacklist);
			foreach ($black_array as $black_word) {
				if (isset($keys_array[trim($black_word)])) {
					unset($keys_array[trim($black_word)]);
				}
			}
		}
		
		arsort($keys_array);
		foreach ($keys_array as $word => $instances) {
			$new_keys_array[] = trim($word);
		}
		
		$i = 1;
		$keywords = "";
		foreach ($new_keys_array as $index => $word) {
			if ($i > $keywords_count) {
				break;
			}
			if (strlen(trim($word)) >= $keywords_word) {
				$keywords .= $word.", ";
				$i++;
			}
		}
		
		$keywords = rtrim($keywords, ", ");
		$keywords = trim($keywords, ".");
		$keywords = str_replace(',,', ',', $keywords);
		$keywords = str_replace('.,', ',', $keywords);
		
		return $keywords;
    }
	
	// Define blacklist
	function _getKeywordsList($param, $list) {
		$ext_list = $this->params->get($list, '');
		$config = 'meta_key_'.$list;
		$glb_list = $this->AcesefConfig->$config;
		
        if ($param == 'combine') {
			$combined = "";
			if (!empty($glb_list)) {
				$global = explode(',', trim($glb_list));
			} else {
				return $ext_list;
			}
			
			if (!empty($ext_blacklist)) {
				$extension = explode(',', trim($ext_list));
			} else {
				return $glb_list;
			}
			
			$combined = array_unique(array_merge($global, $extension));
			$combined = implode(', ', $combined);
			return $combined;
		}
		elseif ($param == 'following') {
			return $ext_list;
		}
		else {
			return $glb_list;
		}
    }
	
	function getMenuParams($id) {
		static $params = array();
		
		if (!isset($params[$id])) {
			$params[$id] = AcesefUtility::getMenu()->getParams($id);
		}
		
		return $params[$id];
	}
	
	function fixVar($var) {
        if (!is_null($var)) {
            $pos = strpos($var, ':');
            if ($pos !== false) {
                $var = substr($var, 0, $pos);
			}
        }
		return $var;
    }
}
?>