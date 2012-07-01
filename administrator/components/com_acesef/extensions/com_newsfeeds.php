<?php
/*
* @package		AceSEF
* @subpackage	News Feeds
* @copyright	2009-2010 JoomAce LLC, www.joomace.net
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// No Permission
defined('_JEXEC') or die('Restricted access');

class AceSEF_com_newsfeeds extends AcesefExtension {
	
	function beforeBuild(&$uri) {
		if (!is_null($uri->getVar('view')) && ($uri->getVar('view') == 'newsfeed') && !is_null($uri->getVar('id'))) {
            $id	= $this->_fixFeedId($uri->getVar('id'));
            $uri->setVar('id', $id);
        }
		return;
	}
	
	function catParam($vars, $real_url) {
		// Extract variables
        extract($vars);
		
		if (isset($view)) {
            switch($view) {
                case 'category':					
					if (!empty($id)) {
						parent::categoryParams($id, 1, $real_url);
					}
                    break;
				case 'newsfeed':
					if (!empty($catid)) {
						parent::categoryParams($catid, 0, $real_url);
					}
                    break;
            }
        }
	}
	
	function build(&$vars, &$segments, &$do_sef, &$metadata, &$item_limitstart) {
		// Extract variables
        extract($vars);

		if (isset($view)) {
			switch ($view) {
				case 'categories':
					$segments[] = JText::_('Categories');
					break;
				case 'category':
					if (!empty($id)) {
						$segments[] = self::_getCategory(intval($id));
						unset($vars['id']);
					}
					break;
				case 'newsfeed':
					if (!empty($catid) && $this->params->get('category_inc', '2') == '2') {
						$segments[] = self::_getCategory(intval($catid));
						unset($vars['catid']);
					}
					if (!empty($id)) {
						$segments[] = self::_getFeed(intval($id));
						unset($vars['feedid']);
						unset($vars['id']);
					}
					break;
			}
			unset($vars['view']);
		}
		
		$metadata = parent::getMetaData($vars, $item_limitstart);
		
		unset($vars['limit']);
		unset($vars['limitstart']);
	}
	
	function _getCategory($id) {
		static $cache = array();
		
		if (!isset($cache[$id])) {
			$joomfish = $this->AcesefConfig->joomfish_trans_url ? ', id' : '';
			
			$row = AceDatabase::loadRow("SELECT title, alias, description{$joomfish} FROM #__categories WHERE id = ".$id);
			
			$name = (($this->params->get('categoryid_inc', '1') != '1') ? $id.' ' : '');
			if (parent::urlPart($this->params->get('category_part', 'global')) == 'title') {
				$name .= $row[0];
			} else {
				$name .= $row[1];
			}
			
			$cache[$id]['name'] = $name;
			$cache[$id]['meta_title'] = $row[0];
			$cache[$id]['meta_desc'] = $row[2];
		}
		
		$this->meta_title[] = $cache[$id]['meta_title'];
		$this->meta_desc = $cache[$id]['meta_desc'];
		
		return $cache[$id]['name'];
    }
	
	function _getFeed($id) {
		static $cache = array();
		
		if (!isset($cache[$id])) {
			$joomfish = $this->AcesefConfig->joomfish_trans_url ? ', id' : '';
			
			$row = AceDatabase::loadRow("SELECT name, alias{$joomfish} FROM #__newsfeeds WHERE id = ".$id);
			
			$name = (($this->params->get('feedid_inc', '1') != '1') ? $id.' ' : '');
			if (parent::urlPart($this->params->get('feed_part', 'global')) == 'title') {
				$name .= $row[0];
			} else {
				$name .= $row[1];
			}
			
			$cache[$id]['name'] = $name;
			$cache[$id]['meta_title'] = $row[0];
		}
		
		array_unshift($this->meta_title, $cache[$id]['meta_title']);
		
		return $cache[$id]['name'];
    }
	
	// Remove : part from News Feeds id
	function _fixFeedId($var) {
        if (!is_null($var)) {
			$idArray = explode('-', $var);
			$var = $idArray[0];
		}
		return $var;
    }

    function getCategoryList($query) {
        if (self::_is16()) {
            $field = 'extension';
        }
        else{
            $field = 'section';
        }

         $rows = AceDatabase::loadObjectList("SELECT id, title AS name FROM #__categories WHERE {$field} = 'com_newsfeeds' ORDER BY title");

        return $rows;
	}

    function _is16() {
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
}