<?php
/*
* @package		AceSEF
* @subpackage	Content
* @copyright	2009-2010 JoomAce LLC, www.joomace.net
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// No Permission
defined('_JEXEC') or die ('Restricted access');

class AceSEF_com_content extends AcesefExtension {
	
	function beforeBuild(&$uri) {
		AcesefURI::fixUriVar($uri, 'id');
        AcesefURI::fixUriVar($uri, 'catid');
		
		// Change task=view to view=article for old urls
        if (!is_null($uri->getVar('task')) && ($uri->getVar('task') == 'view')) {
            $uri->delVar('task');
            $uri->setVar('view', 'article');
        }
		
		if (is_null($uri->getVar('limitstart')) && !is_null($uri->getVar('start'))) {
            $uri->setVar('limitstart', $uri->getVar('start'));
			$uri->delVar('start');
        }
        
		// Remove the limitstart and limit variables if they point to the first page
		if (is_null($uri->getVar('limitstart')) || $uri->getVar('limitstart') == '0') {
            $uri->delVar('limitstart');
            $uri->delVar('limit');
        }
        
		if (!is_null($uri->getVar('view')) && ($uri->getVar('view') == 'article') && (is_null($uri->getVar('catid')) || $uri->getVar('catid') == 0) && !is_null($uri->getVar('id'))) {
			$catid = self::_getItemCatId(intval($uri->getVar('id')));
			
			if (!empty($catid)) {
				$uri->setVar('catid', $catid);
			}
		}
		
		// Add the view variable if it's not set
		if (!self::_is16()) {
			if (is_null($uri->getVar('view'))) {
				if (is_null($uri->getVar('id'))) {
					$uri->setVar('view', 'frontpage');
				} else {
					$uri->setVar('view', 'article');
				}
			}
		}
		else {
			if (is_null($uri->getVar('view'))) {
				if (is_null($uri->getVar('limitstart'))) {
					$uri->setVar('view', 'categories');
				}
				elseif (is_null($uri->getVar('id'))) {
					$uri->setVar('view', 'featured');
				}
				else {
					$uri->setVar('view', 'category');
				}
			}
		}
        
		// Fix for AlphaContent
		if (!is_null($uri->getVar('directory'))) {
            $uri->delVar('directory');
		}
		
		if (($this->params->get('smart_itemid', '1') == '2') && !is_null($uri->getVar('view'))) {
			if (!class_exists('ContentRoute')) {
				return;
			}
			
			if (!method_exists('ContentRoute', '_findItemExt')) {
				return;
			}
            
            if (!self::_is16() && $uri->getVar('view') == 'section' && !is_null($uri->getVar('id'))) {
				$id = $uri->getVar('id');
				$needles = array('section' => (int) $id);

				$item = ContentRoute::_findItemExt($needles);
				if (!empty($item)) {
					$uri->setVar('Itemid', $item->id);
				}
			}
			
			if ($uri->getVar('view') == 'category' && !is_null($uri->getVar('id'))) {
				$id = $uri->getVar('id');
				
				if (self::_is16()) {
					$needles = array('category' => array((int) $id));
				}
				else {
					$needles = array('category' => (int) $id);
				}
		
				$item = ContentRoute::_findItemExt($needles);
				if (!empty($item)) {
					if (self::_is16()) {
						$uri->setVar('Itemid', $item);
					}
					else {
						$uri->setVar('Itemid', $item->id);
					}
				}
			}
			
			if ($uri->getVar('view') == 'article' && !is_null($uri->getVar('id'))) {
				$id = $uri->getVar('id');
				
				$catid = 0;
				if (!is_null($uri->getVar('catid'))) {
					$catid = $uri->getVar('catid');
				}

                if (self::_is16()) {
                    $needles = array('article' => array((int) $id), 'category' => array((int) $catid));
                }
                else {
                    $needles = array('article' => (int) $id, 'category' => (int) $catid, 'section' => 0);
                }
		
				$item = ContentRoute::_findItemExt($needles);
				if (!empty($item)) {
					if (self::_is16()) {
						$uri->setVar('Itemid', $item);
					}
					else {
						$uri->setVar('Itemid', $item->id);
					}
				}
			}
		}
    }
	
	function _getItemCatId($id) {
		static $cache = array();
		
		if (!isset($cache[$id])) {
			if ($this->AcesefConfig->cache_instant == 1) {
				$rows = AceDatabase::loadRowList("SELECT id, catid FROM #__content");
				foreach ($rows as $row) {
					$cache[$row[0]] = $row[1];
				}
			} else {
				$cache[$id] = AceDatabase::loadResult("SELECT catid FROM #__content WHERE id = {$id}");
			}
		}
		
		if (!isset($cache[$id])) {
			$cache[$id] = "";
		}
		
		return $cache[$id];
    }
	
	function catParam($vars, $real_url) {
        extract($vars);
		
		if (isset($view)) {
            switch($view) {
                case 'category':
					if (!empty($id)) {
						parent::categoryParams($id, 1, $real_url);
					}
                    break;
				case 'article':
					if (!empty($catid)) {
						parent::categoryParams($catid, 0, $real_url);
					}
                    break;
            }
        }
	}
	
	function build(&$vars, &$segments, &$do_sef, &$metadata, &$item_limitstart) {
        extract($vars);
		
		$cat_suffix 	= $this->params->get('cat_suffix', '1');
		$default_index	= $this->params->get('default_index', '');
		$layout_prefix	= $this->params->get('layout_prefix', '2');
		$list_prefix	= $this->params->get('list_prefix', '');
		$blog_prefix	= $this->params->get('blog_prefix', '');
		
		$meta_vars = $vars;
	
		if (isset($view)) {
            switch($view) {
				case 'archive':
					$segments[] = JText::_('ARCHIVES');
					if (!empty($year)) {
						$segments[] = $year;
						unset($vars['year']);
					}
					
					if (!empty($month)) {
						$segments[] = $month;
						unset($vars['month']);
					}
					break;
				case 'section':
                    if (self::_is16()) {
                        break;
                    }

					if (!empty($layout)) {
						if (!empty($layout) && $layout == 'blog' && !empty($blog_prefix)) {
							$segments[] = $blog_prefix;
						}
						if (!empty($layout) && $layout == 'default' && !empty($list_prefix)) {
							$segments[] = $list_prefix;
						}
                    }
					unset($vars['layout']);

                    if (!empty($id)) {
						$add_sec = (($this->params->get('section_inc', '1') == '2') || ($this->params->get('section_inc', '1') == '1' && $this->params->get('skip_menu', '0') == '1'));
						if ($add_sec) {
							$segments[] = self::_getSection(intval($id));
						}
						unset($vars['id']);
					}

					if ($cat_suffix == '1' && $default_index == '') {
						$segments[] = "/";
					}

					if ($default_index != '') {
						$segments[] = $default_index;
					}
                    break;
				case 'categories':
                case 'category':
					if (!empty($layout) && $layout_prefix != '1') {
						if ($layout == 'blog' && !empty($blog_prefix)) {
							$segments[] = $blog_prefix;
						}
						if ($layout == 'default' && !empty($list_prefix)) {
							$segments[] = $list_prefix;
						}
                    }
					unset($vars['layout']);
					
					if (!empty($id)) {
						$add_cat = (($this->params->get('category_inc', '2') == '2' || $this->params->get('category_inc', '2') == '3') || ($this->params->get('category_inc', '2') == '1' && $this->params->get('skip_menu', '0') == '1'));
						if ($add_cat == true) {
							$segments = array_merge($segments, self::_getCategory(intval($id)));
						}
						unset($vars['id']);
					}
					
					if ($view == 'categories') {
						$segments[] = JText::_('Categories');
					}
					
					if ($cat_suffix == '1' && $default_index == '') {
						$segments[] = "/";
					}
					
					if ($default_index != '') {
						$segments[] = $default_index;
					}
                    break;
				case 'article':
					if (!empty($id)) {
						if ($this->params->get('url_structure', 'joomla') == 'joomla') {
							if (!empty($catid)) {
                                if (self::_is16()) {
								    $segments = array_merge($segments, self::_getCategory16(intval($catid), false, 'article'));
                                }
                                elseif ($this->params->get('category_inc', '2') == '2') {
                                    $segments = array_merge($segments, self::_getCategory(intval($catid), false, 'article'));
                                }
							}
							
							$segments = array_merge($segments, self::_getArticle(intval($id)));
						}
						else {
							$segments = array_merge($segments, self::_getArticleWP(intval($id)));
						}
						
						unset($vars['catid']);
						unset($vars['id']);
					}
					
					// Google News
					if (!empty($id) && ($this->params->get('url_structure', 'joomla') == 'joomla') && $this->params->get('google_news', '1') != '1' && !empty($catid)) {
						$categories = $this->params->get('google_news_cats', 'all');
						
						$apply_gn = false;
						if ($categories == 'all') {
							$apply_gn = true;
						}
						elseif (is_array($categories) && in_array($catid, $categories)) {
							$apply_gn = true;
						}
						elseif ($categories == $catid) {
							$apply_gn = true;
						}
						
						if ($apply_gn) {
							$i = count($segments) - 1;
							$segments[$i] = self::_googleNews($segments[$i], intval($id));
						}
					}
					
					if (!empty($limitstart)) {
						$item_limitstart = true;
					}
					
					if (isset($task)) {
						$segments[] = $task;
						unset($vars['task']);
					}
					
					if (isset($print)) {
						$segments[] = JText::_('PRINT');
						unset($vars['print']);
					}
					
					if (isset($showall) && $showall == 1) {
						$segments[] = JText::_('ALL');
						unset($vars['showall']);
					}
					
					if (isset($layout) && $layout == 'form') {
						$segments[] = JText::_('NEW ITEM');
						unset($vars['layout']);
					}
                    break;
				case 'form':
					if (isset($layout) && $layout == 'edit') {
						$segments[] = JText::_('New Item');
						unset($vars['layout']);
					}
					else {
						$do_sef = false;
					}
					break;
				case 'frontpage':
				case 'featured':
					break;
                case 'archivecategory':
                case 'archivesection':
                case 'archive':
                    $do_sef = false;
                    break;
				default:
					$segments[] = $view;
					break;
            }
			unset($vars['view']);
        }
		
		if (!empty($format)) {
			$segments[] = $format;
			unset($vars['format']);
		}
		
		if (!empty($type)) {
			$segments[] = $type;
			unset($vars['type']);
		}
		
		$metadata = parent::getMetaData($meta_vars, $item_limitstart);
		
		unset($vars['limit']);
		unset($vars['limitstart']);
	}

	function _getSection($id) {
		static $cache = array();

		if (!isset($cache[$id])) {
			$joomfish = $this->AcesefConfig->joomfish_trans_url ? ', id' : '';
			$row = AceDatabase::loadObject("SELECT title, alias, description{$joomfish} FROM #__sections WHERE id = {$id}");
			
			if (!is_object($row)) {
				$cache[$id]['name'] = '';
				$cache[$id]['meta_title'] = '';
				$cache[$id]['meta_desc'] = '';
			
				return $cache[$id]['name'];
			}
			
			$name = (($this->params->get('sectionid_inc', '1') != '1') ? $id.' ' : '');
			if (parent::urlPart($this->params->get('section_part', 'global')) == 'title') {
				$name .= $row->title;
			} else {
				$name .= $row->alias;
			}

			$cache[$id]['name'] = $name;
			$cache[$id]['meta_title'] = $row->title;
			$cache[$id]['meta_desc'] = $row->description;
		}

		$this->meta_title[] = $cache[$id]['meta_title'];
		$this->meta_desc = $cache[$id]['meta_desc'];

		return $cache[$id]['name'];
    }

    function _getCategory($id, $is_wp = false, $view = '') {
        if (self::_is16()) {
            return self::_getCategory16($id, $is_wp, $view);
        }

		static $cache = array();

		if (!isset($cache[$id])) {
			$joomfish = $this->AcesefConfig->joomfish_trans_url ? ', id' : '';
			$row = AceDatabase::loadObject("SELECT title, alias, section, description{$joomfish} FROM #__categories WHERE id = {$id}");
			
			if (!is_object($row)) {
				$cache[$id]['name'] = '';
				$cache[$id]['section'] = 0;
				$cache[$id]['meta_title'] = '';
				$cache[$id]['meta_desc'] = '';
			
				return array($cache[$id]['name']);
			}
			
			$name = (($this->params->get('categoryid_inc', '1') != '1') ? $id.' ' : '');
			if (parent::urlPart($this->params->get('category_part', 'global')) == 'title') {
				$name .= $row->title;
			} else {
				$name .= $row->alias;
			}

			$cache[$id]['name'] = $name;
			$cache[$id]['section'] = $row->section;
			$cache[$id]['meta_title'] = $row->title;
			$cache[$id]['meta_desc'] = $row->description;
		}

		$this->meta_title[] = $cache[$id]['meta_title'];
		$this->meta_desc = $cache[$id]['meta_desc'];

		if ($is_wp) {
			return $cache[$id]['name'];
		}

		if ($this->params->get('section_inc', '1') == '1') {
			return array($cache[$id]['name']);
		}

		return array(self::_getSection($cache[$id]['section']), $cache[$id]['name']);
    }

	function _getCategory16($id, $is_wp = false, $view = '') {
		$cats = $this->params->get('category_inc', '2');
		
		if (($is_wp == true) || (($cats == '1') && ($this->params->get('skip_menu', '0') == '1') && ($view != 'article'))) {
			$cats = '2';
		}
		
        if ($cats == '1') {
            return array();
        }
		
		static $cache = array();
		
		if (!isset($cache[$id])) {
			$joomfish = $this->AcesefConfig->joomfish_trans_url ? ', id' : '';
			$categories = array();
			$cat_title = array();
			$cat_desc = array();

			while ($id > 2 && $id != 9) {
				$row = AceDatabase::loadObject("SELECT title, alias, parent_id, description{$joomfish} FROM #__categories WHERE id = '{$id}' AND extension = 'com_content'");
				
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

    function _getArticle($id) {
		static $cache = array();

		if (!isset($cache[$id])) {
			$joomfish = $this->AcesefConfig->joomfish_trans_url ? ', id' : '';
			$row = AceDatabase::loadObject("SELECT title, alias, introtext, metadesc, sectionid{$joomfish} FROM #__content WHERE id = {$id}");

			if (!empty($row) && is_object($row)) {
				$name = (($this->params->get('articleid_inc', '1') != '1') ? $id.' ' : '');
				if (parent::urlPart($this->params->get('article_part', 'global')) == 'title') {
					$name .= $row->title;
				} else {
					$name .= $row->alias;
				}

				if (!empty($row->sectionid) && ($this->params->get('section_inc', '1') == '2') && ($this->params->get('category_inc', '2') == '1')) {
					$cache[$id]['name'] = array(self::_getSection(intval($row->sectionid)), $name);
				} else {
					$cache[$id]['name'] = array($name);
				}
				
				$cache[$id]['meta_title'] = $row->title;

				if ($this->params->get('item_desc', '1') == '1') {
					$cache[$id]['meta_desc'] = $row->introtext;
				} else {
					$cache[$id]['meta_desc'] = $row->metadesc;
				}
			}
			else {
				$cache[$id]['name'] = array();
				$cache[$id]['meta_title'] = $cache[$id]['meta_desc'] = "";
			}
		}

		array_unshift($this->meta_title, $cache[$id]['meta_title']);
		$this->meta_desc = $cache[$id]['meta_desc'];

		return $cache[$id]['name'];
    }

    function _getArticleWP($id) {
		static $cache = array();
		
		if (!isset($cache[$id])) {
			$joomfish = $this->AcesefConfig->joomfish_trans_url ? ', id' : '';
			$row = AceDatabase::loadObject("SELECT title, alias, catid, introtext, metadesc, sectionid, publish_up, created_by{$joomfish} FROM #__content WHERE id = {$id}");
			
			if (!empty($row) && is_object($row)) {
				$name = array();
				$structure = $this->params->get('url_structure', 'daytitle');
				
				$date = explode('-', JFactory::getDate($row->publish_up)->toFormat('%Y-%m-%d'));
				$year = $date[0];
				$month = $date[1];
				$day = $date[2];
				
				switch($structure) {
					case 'daytitle':
						$name[] = $year;
						$name[] = $month;
						$name[] = $day;
						$name[] = (($this->params->get('articleid_inc', '1') != '1') ? $id.' ' : '') . $row->title;
						
						$meta_title = $row->title;
						break;
					case 'monthtitle':
						$name[] = $year;
						$name[] = $month;
						$name[] = (($this->params->get('articleid_inc', '1') != '1') ? $id.' ' : '') . $row->title;
						
						$meta_title = $row->title;
						break;
					case 'numeric':
						$name[] = 'archives';
						$name[] = $id;
						
						$meta_title = $row->title;
						break;
					case 'custom':
						$custom_structure = $this->params->get('custom_structure', 'category/article');
						$array_structure = explode('/', $custom_structure);
						
						$category = $author = '';
						$article = (($this->params->get('articleid_inc', '1') != '1') ? $id.' ' : '') . $row->title;
						
						if (in_array('{category}', $array_structure) && !empty($row->catid)) {
							$category = self::_getCategory(intval($row->catid), true, 'article');
                            if (self::_is16()) {
                                $category = $category[0];
                            }
						}
						
						if (in_array('{author}', $array_structure) && !empty($row->created_by)) {
							$author = self::_getUser(intval($row->created_by));
						}
						
						$search = array('{category}', '{article}', '{author}', '{year}', '{month}', '{day}');
						$replace = array($category, $article, $author, $year, $month, $day);
						$name[] = str_replace($search, $replace, $custom_structure);
						
						$meta_title = $row->title;
						break;
				}
				
				$cache[$id]['name'] = $name;
				$cache[$id]['meta_title'] = $meta_title;
				
				if ($this->params->get('item_desc', '1') == '1') {
					$cache[$id]['meta_desc'] = $row->introtext;
				} else {
					$cache[$id]['meta_desc'] = $row->metadesc;
				}
			}
			else {
				$cache[$id]['name'] = array();
				$cache[$id]['meta_title'] = $cache[$id]['meta_desc'] = "";
			}
		}
		
		array_unshift($this->meta_title, $cache[$id]['meta_title']);
		$this->meta_desc = $cache[$id]['meta_desc'];
		
		return $cache[$id]['name'];
    }

	function _getUser($id) {
		static $cache = array();
		
		if (!isset($cache[$id])) {
			$user = AceDatabase::loadResult("SELECT name FROM #__users WHERE id = {$id}");

			if (!empty($user)) {
				$cache[$id]['name'] = $user;
			} else {
				$cache[$id]['name'] = $id;
			}
		}
		
		return $cache[$id]['name'];
    }
	
	// Google New numbering
	function _googleNews($title, $id) {
        $num = '';
        $add = $this->params->get('google_news', '1');

		// ID
		$digits = trim($this->params->get('google_news_digits', '3'));
		if (!is_numeric($digits)) {
			$digits = '3';
		}
		$articleid = sprintf('%0'.$digits.'d', $id);
        
		// Date
		if ($add == '3' || $add == '4') {
			$time = AceDatabase::loadResult("SELECT publish_up FROM #__content WHERE id = {$id}");

			$time = strtotime($time);

			$date = $this->params->get('google_news_dateformat', 'ddmm');

			$search = array('dd', 'd', 'mm', 'm', 'yyyy', 'yy');
			$replace = array(date('d', $time), date('j', $time), date('m', $time), date('n', $time), date('Y', $time), date('y', $time));
			$articledate = str_replace($search, $replace, $date);
		}
		
		if ($add == '2') {
			$num = $articleid;
		} elseif ($add == '3') {
			$num = $articledate;
		} elseif ($add == '4') {
			$num = $articledate.$articleid;
		}

        if (!empty($num)) {
            $sep = $this->AcesefConfig->replacement_character;
            $where = $this->params->get('google_news_pos', '2');

            if ($where == '2') {
                $title = $title.$sep.$num;
            } else {
                $title = $num.$sep.$title;
            }
        }

        return $title;
    }

	function getCategoryList($query) {
        if (self::_is16()) {
            $rows = AceDatabase::loadObjectList("SELECT id, title AS name, parent_id AS parent FROM #__categories WHERE parent_id > 0 AND published = 1 AND extension = 'com_content' ORDER BY parent_id, lft");
        }
        else{
            $rows = AceDatabase::loadObjectList("SELECT c.id, CONCAT_WS(' / ', s.title, c.title) AS name FROM #__categories AS c, #__sections AS s WHERE s.scope = 'content' AND c.section = s.id ORDER BY s.title, c.title");
        }
        
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

require_once(JPATH_ROOT.'/components/com_content/helpers/route.php');
class ContentRoute extends ContentHelperRoute {
	
	public static function _findItemExt($needles = null) {
		return parent::_findItem($needles);
	}
}