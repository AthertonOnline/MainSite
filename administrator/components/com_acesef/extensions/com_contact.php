<?php
/*
* @package		AceSEF
* @subpackage	Contact
* @copyright	2009-2010 JoomAce LLC, www.joomace.net
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// No Permission
defined('_JEXEC') or die('Restricted access');

class AceSEF_com_contact extends AcesefExtension {
	
	function beforeBuild(&$uri) {
	
		if (is_null($uri->getVar('view')) && (!is_null($uri->getVar('id')) || !is_null($uri->getVar('catid')))) {
            $uri->setVar('view', 'category');
        }
		
		/*if (!is_null($uri->getVar('view')) && $uri->getVar('view') == 'category' && !is_null($uri->getVar('id'))) {
            $uri->setVar('catid', $uri->getVar('id'));
            $uri->delVar('id');
        }*/
	}
	
	function catParam($vars, $real_url) {
        extract($vars);
		
		if (isset($view)) {
            switch($view) {
                case 'category':					
					if (!empty($catid)) {
						parent::categoryParams($catid, 1, $real_url);
					}
                    break;
				case 'contact':
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
				$rows = AceDatabase::loadRowList("SELECT id, catid FROM #__contact_details");
				foreach ($rows as $row) {
					$cache[$row[0]] = $row[1];
				}
			} else {
				$cache[$id] = AceDatabase::loadResult("SELECT catid FROM #__contact_details WHERE id = {$id}");
			}
		}
		
		if (!isset($cache[$id])) {
			$cache[$id] = "";
		}
		
		return $cache[$id];
    }
	
	function build(&$vars, &$segments, &$do_sef, &$metadata, &$item_limitstart) {
        extract($vars);
		
		if (isset($view)) {
            switch($view) {
				case 'category':
				case 'categories':
                    if (isset($catid)) {
						$segments = array_merge($segments, self::_getCategory(intval($catid)));
						unset($vars['catid']);
					}
                    elseif (isset($id)) {
						$segments = array_merge($segments, self::_getCategory(intval($id)));
						unset($vars['id']);
					}
                    break;
                case 'contact':
					if (isset($id)) {
						$segments = array_merge($segments, self::_getContact(intval($id)));
						unset($vars['id']);
						unset($vars['catid']);
					}
                    break;
				default:
					$segments[] = $view;
					break;
            }
			unset($vars['view']);
        }
		
		$metadata = parent::getMetaData($vars, $item_limitstart);
		
		unset($vars['limit']);
		unset($vars['limitstart']);
	}
	
	function _getCategory($id) {
        if (self::_is16()) {
            return self::_getCategory16($id);
        }

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
			
			$cache[$id]['name'] = array($name);
			$cache[$id]['meta_title'] = $row[0];
			$cache[$id]['meta_desc'] = $row[2];
		}
		
		$this->meta_title[] = $cache[$id]['meta_title'];
		$this->meta_desc = $cache[$id]['meta_desc'];
		
		return $cache[$id]['name'];
    }

	function _getCategory16($id) {
		$cats = $this->params->get('category_inc', '1');

        if ($cats == '1') {
            return array();
        }
		
		static $cache = array();
		
		if (!isset($cache[$id])) {
			$joomfish = $this->AcesefConfig->joomfish_trans_url ? ', id' : '';
			$categories = array();
			$cat_title = array();
			$cat_desc = array();

			while ($id > 1 && $id != 4 && $id != 11) {
				$row = AceDatabase::loadObject("SELECT title, alias, parent_id, description{$joomfish} FROM #__categories WHERE id = '{$id}' AND extension = 'com_contact'");
				
				if (!is_object($row)) {
					break;
				}
				
				$name = (($this->params->get('categoryid_inc', '1') != '1') ? $id.' ' : '');
				if (parent::urlPart($this->params->get('category_part', 'global')) == 'title'){
					$name .= $row->title;
				} else {
					$name .= $row->alias;
				}
				
				array_unshift($categories, $name);
				$cat_title[] = $row->title;
				$cat_desc[] = $row->description;
				
				$id = $row->parent_id;
				if ($cats == '2'){
					break; //  Only last cat
				}
			}
			
			$cache[$id]['name'] = $categories;
			$cache[$id]['meta_title'] = $cat_title;
			$cache[$id]['meta_desc'] = $cat_desc;
		}
		
		$this->meta_title = $cache[$id]['meta_title'];
		if (!empty($cache[$id]['meta_desc'])) {
			$this->meta_desc = $cache[$id]['meta_desc'][0];
		}
		
		return $cache[$id]['name'];
    }

    function _getContact($id) {
		static $cache = array();
		
		if (!isset($cache[$id])) {
			$joomfish = $this->AcesefConfig->joomfish_trans_url ? ', id' : '';
			
			$row = AceDatabase::loadRow("SELECT name, alias, catid{$joomfish} FROM #__contact_details WHERE id = ".$id);

			$name = (($this->params->get('contactid_inc', '1') != '1') ? $id.' ' : '');
			if (parent::urlPart($this->params->get('contact_part', 'global')) == 'title') {
				$name .= $row[0];
			} else {
				$name .= $row[1];
			}
			
			if ($this->params->get('category_inc', '1') == '1'){
				$cache[$id]['name'] = array($name);
			}
			else {
				$category = self::_getCategory($row[2]);
				array_push($category, $name);
				$cache[$id]['name'] = $category;
			}
			
			$cache[$id]['meta_title'] = $row[0];
		}
		
		array_unshift($this->meta_title, $cache[$id]['meta_title']);
		
		return $cache[$id]['name'];
    }

    function getCategoryList($query) {
        if (self::_is16()) {
            $field = 'extension';
            $value = 'com_contact';
        }
        else{
            $field = 'section';
            $value = 'com_contact_details';
        }

        $rows = AceDatabase::loadObjectList("SELECT id, title AS name FROM #__categories WHERE {$field} = '{$value}' ORDER BY title");

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