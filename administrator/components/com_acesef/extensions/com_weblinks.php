<?php
/*
* @package		AceSEF
* @subpackage	Web Links
* @copyright	2009-2010 JoomAce LLC, www.joomace.net
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// No Permission
defined('_JEXEC') or die('Restricted access');

class AceSEF_com_weblinks extends AcesefExtension {
	
	function catParam($vars, $real_url) {
        extract($vars);
		
		if (isset($view)) {
            switch($view) {
                case 'category':					
					if (!empty($id)) {
						parent::categoryParams($id, 1, $real_url);
					}
                    break;
				case 'weblink':
					if (!empty($catid)) {
						parent::categoryParams($catid, 0, $real_url);
					}
                    break;
            }
        }
	}

	function build(&$vars, &$segments, &$do_sef, &$metadata, &$item_limitstart) {
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
				case 'weblink':
					if (!empty($catid) && $this->params->get('category_inc', '2') == '2') {
						$segments[] = self::_getCategory(intval($catid));
						unset($vars['catid']);
					}
					
					if (!empty($id)) {
						$segments[] = self::_getLink($id);
						unset($vars['id']);
					} else {
						$segments[] = JText::_('Submit');
						unset($vars['layout']);
					}
					break;
				default:
					$segments[] = $view;
					break;
			}
			unset($vars['view']);
		}
		
		if (isset($task)) {
			$segments[] = $task;
			unset($vars['task']);
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
	
	function _getLink($id) {
		static $cache = array();
		
		if (!isset($cache[$id])) {
			$joomfish = $this->AcesefConfig->joomfish_trans_url ? ', id' : '';
			
			$row = AceDatabase::loadRow("SELECT title, alias{$joomfish} FROM #__weblinks WHERE id = ".$id);
			
			$name = (($this->params->get('linkid_inc', '1') != '1') ? $id.' ' : '');
			if (parent::urlPart($this->params->get('link_part', 'global')) == 'title') {
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

    function getCategoryList($query) {
        if (self::_is16()) {
            $field = 'extension';
        }
        else{
            $field = 'section';
        }

         $rows = AceDatabase::loadObjectList("SELECT id, title AS name FROM #__categories WHERE {$field} = 'com_weblinks' ORDER BY title");

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