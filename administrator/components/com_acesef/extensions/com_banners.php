<?php
/*
* @package		AceSEF
* @subpackage	Banners
* @copyright	2009-2010 JoomAce LLC, www.joomace.net
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// No Permission
defined('_JEXEC') or die ('Restricted access');

class AceSEF_com_banners extends AcesefExtension {

	function catParam($vars, $real_url) {
        extract($vars);
		
		if (isset($view)) {
            switch($view) {
				case 'click':
					if (!empty($id)) {
						$catid = self::_getItemCatId(intval($id));
						if (!empty($catid)) {
							parent::categoryParams($catid, 0, $real_url);
						}
					}
                    break;
            }
        }
	}
	
	function _getItemCatId($id) {
		static $cache = array();
		
		if (!isset($cache[$id])) {
			if ($this->AcesefConfig->cache_instant == 1) {
				$rows = AceDatabase::loadRowList("SELECT id, cid FROM #__banner");
				foreach ($rows as $row) {
					$cache[$row[0]] = $row[1];
				}
			} else {
				$cache[$id] = AceDatabase::loadResult("SELECT cid FROM #__banner WHERE id = {$id}");
			}
		}
		
		if (!isset($cache[$id])) {
			$cache[$id] = "";
		}
		
		return $cache[$id];
    }
	
	function build(&$vars, &$segments, &$do_sef, &$metadata, &$item_limitstart) {
        extract($vars);

		if (isset($task)) {
            switch($task) {
				case 'click':
                    if (isset($bid)) {
                        $segments[] = self::_getBanner(intval($bid));
						unset($vars['bid']);
					}
                    elseif (isset($id)) {
                        $segments[] = self::_getBanner(intval($id));
						unset($vars['id']);
					}
                    break;
				default:
					$segments[] = $task;
					break;
            }
			unset($vars['task']);
        }
		
		$metadata = parent::getMetaData($vars, $item_limitstart);
		
		unset($vars['limit']);
		unset($vars['limitstart']);
	}
	
	function _getBanner($id) {
		static $cache = array();
		
		if (!isset($cache[$id])) {
			if (self::_is16()) {
				$joomfish = $this->AcesefConfig->joomfish_trans_url ? ', id' : '';
				$row = AceDatabase::loadRow("SELECT name, alias{$joomfish} FROM #__banners WHERE id = ".$id);
			}
			else {
				$joomfish = $this->AcesefConfig->joomfish_trans_url ? ', bid' : '';
				$row = AceDatabase::loadRow("SELECT name, alias{$joomfish} FROM #__banner WHERE bid = ".$id);
			}
			
			$name = (($this->params->get('bannerid_inc', '1') != '1') ? $id.' ' : '');
			if (parent::urlPart($this->params->get('banner_part', 'title')) == 'title') {
				$name .= $row[0];
			} else {
				$name .= $row[1];
			}
			
			$cache[$id]['name'] = $name;
			$cache[$id]['meta_title'] = $row[0];
		}
		
		$this->meta_title[] = $cache[$id]['meta_title'];
		
		return $cache[$id]['name'];
    }

    function getCategoryList($query) {
        if (self::_is16()) {
            $field = 'extension';
        }
        else{
            $field = 'section';
        }

        $rows = AceDatabase::loadObjectList("SELECT id, title AS name FROM #__categories WHERE {$field} = 'com_banners' ORDER BY title");

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