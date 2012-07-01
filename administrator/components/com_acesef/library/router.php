<?php
/**
* @version		1.5.0
* @package		AceSEF Library
* @subpackage	Main router
* @copyright	2009-2010 JoomAce LLC, www.joomace.net
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// No Permission
defined('_JEXEC') or die('Restricted Access');

define('JROUTER_MODE_DONT_PARSE', 2);

// IIS Patch
if (isset($_SERVER['HTTP_X_REWRITE_URL'])) {
    $_SERVER['REQUEST_URI'] = $_SERVER['HTTP_X_REWRITE_URL'];
}

class ExtURI extends JURI {

	public static function getURL($uri) {
		return $uri->_uri;
	}
}

// Main router class
class JRouterAcesef extends JRouter {

	public $attributes = null;
	protected $parsing = false;

	function __construct($options = array()) {
		parent:: __construct($options);
		
		// URI attributes
		$this->attributes = new stdClass();
		
		// Get config object
		$this->AcesefConfig = AcesefFactory::getConfig();
	}

	// The parse function handles the incomming URL
	function parse(&$siteRouter, &$uri) {
        if ($this->parsing) {
            return array();
        }
        
        $this->parsing = true;

        $uri->setPath(JURI::base(true) . '/' . $uri->getPath());
        
		$mainframe =& JFactory::getApplication();
        $router = $mainframe->get('acesef.global.jrouter');
        $router->setMode(JROUTER_MODE_DONT_PARSE);
		
		// Fix the missing question mark
        if (count($_POST) == 0) {
            $url = $uri->toString();
            $new_url = preg_replace('/^([^?&]*)&([^?]+=[^?]*)$/', '$1?$2', $url);
            // Redirect if question mark fixed
            if ($new_url != $url) {
                $mainframe->redirect($new_url, '', 'message', true);
            }
        }

		// Check if URI is string
		if (is_string($uri)) {
			$uri = JURI::getInstance($uri);
		}

		// Backlink plugin compatibility
        if (JPluginHelper::isEnabled('system', 'backlink') ) {
            $joomla_request = $_SERVER['REQUEST_URI'];
            $real_request = $uri->toString(array('path', 'query'));

            if ($real_request != $joomla_request) {
                $uri = new JURI($joomla_request);
            }
        }

		// We'll use it for Joomla! SEF to AceSEF redirection
		$old_uri = clone($uri);

        // get path
        $path = $uri->getPath();

        // remove basepath
        $path = substr_replace($path, '', 0, strlen(JURI::base(true)));

        // remove slashes
        $path = ltrim($path, '/');
		
		// Check if the URL starts with index2.php
		if (substr($path, 0, 10) == 'index2.php') {
            return $uri->getQuery(true);
        }

        // remove prefix (both index.php and index2.php)
        $path = preg_replace('/^index2?.php/i', '', $path);

        // remove slashes again to be sure there aren't any left
        $path = ltrim($path, '/');

        // replace spaces with our replacement character
        $path = str_replace(' ', $this->AcesefConfig->replacement_character, $path);

        // set the route
        $uri->setPath($path);

		$vars = AcesefURI::parseURI($uri, $old_uri);

        // Parsing done
        $this->parsing = false;

        // Fix the start variable
        if ($start = $uri->getVar('start')) {
            $uri->delVar('start');
            $vars['limitstart'] = $start;
        }

        $menu =& AcesefUtility::getMenu();

        // Handle an empty URL (special case)
        if (empty($vars['Itemid']) && empty($vars['option'])) {
            $item = AcesefURI::getDefaultMenuItem();

            if (!is_object($item)) {
				return $vars;
			}

            // set the information in the request
        	$vars = $item->query;

            // get the itemid
            $vars['Itemid'] = $item->id;

            // set the active menu item
            $menu->setActive($vars['Itemid']);

            // set vars
            $this->setRequestVars($vars);

            return $vars;
        }

        // Get the item id, if it hasn't been set force it to null
        if (empty($vars['Itemid'])) {
            $vars['Itemid'] = JRequest::getInt('Itemid', null);
        }

        // Get the variables from the uri
        $this->setVars($vars);

        // No option? Get the full information from the itemid
        if (empty($vars['option'])) {
            $item = $menu->getItem($this->getVar('Itemid'));
            if (!is_object($item)) return $vars; // No default item set

            $vars = $vars + $item->query;
        }

        // Set the active menu item
        $menu->setActive($this->getVar('Itemid'));

        AcesefLanguage::parseLang($vars);

        // Set vars
        $this->setRequestVars($vars);

        return $vars;
    }

	// Build the SEF URL
	function &build(&$siteRouter, &$uri) {
		$sef_url = "";

        $orig_path = $uri->getPath();
		$url = ExtURI::getURL($uri);
		
		$real_url = str_replace('&amp;', '&', $url);

		// Security - only allow colon in protocol part
		if (strpos($real_url, ':') !== false) {
            $offset = 0;
            if (substr($real_url, 0, 5) == 'http:') {
                $offset = 5;
            } elseif (substr($real_url, 0, 6) == 'https:') {
                $offset = 6;
            }

            $real_url = substr($real_url, 0, $offset) . str_replace(':', '%3A', substr($real_url, $offset));
        }

		// Create URI object
		$uri = $this->_createURI($real_url);
		$org_uri = clone($uri); // For disable router option and dosef_false
		
		if (substr($real_url, 0, 10) != 'index.php?') {
			return $uri;
		}

		$menu =& AcesefUtility::getMenu();

		// Make some fixes on URI
		if ($real_url != 'index.php') {
            // Get the itemid from the URI
            $Itemid = $uri->getVar('Itemid');

            if (is_null($Itemid)) {
                if (($option = $uri->getVar('option'))) {
                    $item = $menu->getItem(intval($this->getVar('Itemid')));
                    if (isset($item) && $item->component == $option) {
                        $uri->setVar('Itemid', $item->id);
                    }
                }
                else {
                    if (($Itemid = intval($this->getVar('Itemid')))) {
                        $uri->setVar('Itemid', $Itemid);
                    }
                }
            }

            // If there is no option specified, try to get the query from menu item
            if (is_null($uri->getVar('option'))) {
            	if (count($vars = $uri->getQuery(true)) == 2 && isset($vars['Itemid']) && isset($vars['limitstart'])) {
            		foreach ($this->getVars() as $name => $value) {
            			if ($name != 'limitstart' && $name != 'start') {
            				$uri->setVar($name, $value);
                        }
                    }

            		if ($uri->getVar('limitstart') == 0) {
            			$uri->delVar('limitstart');
                    }
            	}
                elseif (!is_null($uri->getVar('Itemid'))) {
                    $item = $menu->getItem($uri->getVar('Itemid'));

                    if (is_object($item)) {
                        foreach($item->query as $k => $v) {
                            $uri->setVar($k, $v);
                        }
                    }
                }
                else {
                    // There is no option or Itemid specified, assume frontpage
                    $item = AcesefURI::getDefaultMenuItem();

                    if (is_object($item)) {
                        foreach($item->query as $k => $v) {
                            $uri->setVar($k, $v);
                        }
                        
                        // Set Itemid
                        $uri->setVar('Itemid', $item->id);
                    }
                }
            }
        }

        $mainframe =& JFactory::getApplication();
        $router = $mainframe->getRouter();
        $router->setMode(JROUTER_MODE_SEF);

		$prev_lang = '';
		$vars = $uri->getQuery(true);

		// If site is root, do not do anything else
        if (empty($vars) && (!AcesefUtility::JoomFishInstalled() || $this->AcesefConfig->joomfish_lang_code == '0')) {
            $uri = new JURI(JURI::root());
            return $uri;
        }
		
		$lang = AcesefLanguage::getLang($uri);

		// Add JoomFish lang code
		if (AcesefUtility::JoomFishInstalled()) {
            // If lang not set
            if (empty($lang)) {
				$uri->setVar('lang', AcesefURI::getLangCode());
			}

            // Get the URL's language and set it as global language (for correct translation)
            $lang = $uri->getVar('lang');
            $code = '';
            if (!empty($lang)) {
                $code = AcesefURI::getLangLongCode($lang);
                if (!is_null($code)) {
                    if ($code != AcesefURI::getLangLongCode()) {
                        $language =& JFactory::getLanguage();
                        $prev_lang = $language->setLanguage($code);
                        $language->load();
                    }
                }
            }
        }

		// Set active ItemID if set to
		$Itemid = intval($uri->getVar('Itemid'));
		if (empty($Itemid) && $this->AcesefConfig->insert_active_itemid == 1) {
			$active = $menu->getActive();
			if (is_object($active) && isset($active->id)) {
				$uri->setVar('Itemid', $active->id);
			}
		}

        $vars = $uri->getQuery(true);

		// if there are no variables and only single language is used
        if (empty($vars) && !isset($lang)) {
            $org_uri = AcesefURI::createUri($org_uri);
			AcesefURI::restoreLang($prev_lang);
            $router->setMode(JROUTER_MODE_RAW);

			return $org_uri;
        }

		// Check if we should prepend the lang code to SEF URL
		$lang_code 		= "";
		$lang 			= $uri->getVar('lang');
		$add_code		= $this->AcesefConfig->joomfish_lang_code;
		$main_lang		= $this->AcesefConfig->joomfish_main_lang;
		$main_lang_del	= $this->AcesefConfig->joomfish_main_lang_del;
		//if (AcesefUtility::JoomFishInstalled() && !empty($lang)) {
		if (!empty($lang)) {
			// Add lang code if set to
			if ($add_code != '0' && ($main_lang == '0' || $lang != $main_lang) && !strpos($sef_url, $lang)) {
				$lang_code = $lang;
			}
			
			// Remove main lang variable if set to
			if ($lang == $main_lang && $main_lang_del == 1) {
				$uri->delVar('lang');
			}
		}
		
		// Get option
		$component = $uri->getVar('option');
		
		// Set attributes
		$this->attributes->meta = null;
		$this->attributes->params = new JParameter("");
		$this->attributes->non_sef_part = "";
		$this->attributes->item_limitstart = false;

		// Home Page ?
        if (AcesefURI::_isHomePage($uri)) {
        	// Check if URL exists
			if (AcesefURI::_checkDB($uri, $prev_lang)) {
				return $uri;
			}
			
			// Check if we should create new SEF URLs
			if ($this->AcesefConfig->generate_sef == 0) {
				return $uri;
			}
			
			$real_url = AcesefURI::sortURItoString($uri);
			
			$sef_url = AcesefURI::_finalizeSEF($uri, $sef_url, $real_url, $component, $lang_code);
			
			$uri = AcesefURI::_finalizeURI($uri, $sef_url);
			
            AcesefURI::restoreLang($prev_lang);
            
            return $uri;
        } else {
			$default = $menu->getDefault();
			$checkup = (is_object($default) && !is_null(intval($uri->getVar('Itemid'))));
			if ($checkup && is_null($uri->getVar('limitstart')) && $uri->getVar('Itemid') == $default->id && $this->AcesefConfig->insert_active_itemid != 1) {
				$uri->delVar('Itemid');
			}
        }

		// Lets do this job, start routing
		$routed	= false;

		if (!is_null($component)) {
			// Get params
			$ext_params = AcesefCache::getExtensionParams($component);

			// Component not installed
			if (!$ext_params) {
				$org_uri = AcesefURI::createUri($org_uri);
				AcesefURI::restoreLang($prev_lang);
                $router->setMode(JROUTER_MODE_RAW);

				return $org_uri;
			}
			
			// Get params
			$this->attributes->params = $ext_params;
			
			// Get router type
			$routing = $this->attributes->params->get('router', '0');

			//
			// Start routing...
			//

			// Routing disabled
			if ($routing == 0) {
				$org_uri = AcesefURI::createUri($org_uri);
                AcesefURI::restoreLang($prev_lang);
                $router->setMode(JROUTER_MODE_RAW);
                
                return $org_uri;
			} else {
				// Check if we should return the URI directly
				$dosef = AcesefURI::disableSefVars($uri);
				if ($dosef == false) {
					$org_uri = AcesefURI::createUri($org_uri);
					AcesefURI::restoreLang($prev_lang);
                    $router->setMode(JROUTER_MODE_RAW);

					return $org_uri;
				}

				// Reorder URI (ksort)
				$uri = $this->_createURI(AcesefURI::sortURItoString($uri, true));

				// non-SEF Vars
				$this->attributes->non_sef_part = AcesefURI::nonSefVars($uri);

				// Backup variables to use later
				$backup_vars = $uri->getQuery(true);

				// Remove session IDs, if set to
                if ($this->AcesefConfig->remove_sid == 1 && !is_null($uri->getVar('sid'))) {
                    $uri->delVar('sid');
				}

				// Ensure that the mosmsg are removed
				if (!empty($mosmsg)) {
					$uri->delVar('mosmsg');
				}

				// Delete limistart var if empty
				$_lmtstrt = $uri->getVar('limitstart');
				if (empty($_lmtstrt)) {
					$uri->delVar('limitstart');
				}
				else {
					//AcesefURI::fixPaginationURI($uri);
				}
				
				// Smart Itemid
				$skipped_components = array('com_wrapper');
				if (!in_array($component, $skipped_components) && AcesefUtility::getConfigState($this->attributes->params, 'global_smart_itemid')) {
					$s_vars = $uri->getQuery(true);
					unset($s_vars['Itemid']);
					unset($s_vars['lang']);
					unset($s_vars['limit']);
					unset($s_vars['limitstart']);
					
					$menu_item = AcesefURI::findItemid($s_vars);
					if (is_object($menu_item)) {
						$uri->setVar('Itemid', $menu_item->id);
					}
				}

                $lang =& JFactory::getLanguage();
                $lang->load('com_acesef', JPATH_SITE, 'en-GB', true);
                $lang->load('com_acesef', JPATH_SITE, $lang->getDefault(), true);
                $lang->load('com_acesef', JPATH_SITE, null, true);

				// AceSEF extension
				$extension = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_acesef'.DS.'extensions'.DS.$component.'.php';
				if (!$routed && file_exists($extension) && $routing == 3) {
					$acesef_ext = AcesefFactory::getExtension($component);

					// Fix : for ids
					AcesefURI::fixUriVariables($uri);

					// Override menu item id if set to
					if ($this->attributes->params->get('override', '1') != '1' && $this->attributes->params->get('override_id', '') != '') {
						$uri->setVar('Itemid', $this->attributes->params->get('override_id'));
					}

					// Make changes on URI before building route
					$acesef_ext->beforeBuild($uri);
					
					// Category status
					$real_url = AcesefURI::sortURItoString($uri);
					$acesef_ext->catParam($uri->getQuery(true), $real_url);
					
					// Check if URL exists
					if (AcesefURI::_checkDB($uri, $prev_lang)) {
						return $uri;
					}
					
					// Load language file
					$lang_file =& JFactory::getLanguage();
					$lang_file->load($component, JPATH_SITE);
					
					// Prepare vars
					$vars = $uri->getQuery(true);
					$segments = array();
					$do_sef = true;
					$this->attributes->meta = null;
					$this->attributes->item_limitstart = false;
					
					// Build
					$acesef_ext->build($vars, $segments, $do_sef, $this->attributes->meta, $this->attributes->item_limitstart);

					// Check if do_sef is false
					if ($do_sef == false) {
						$org_uri = AcesefURI::createUri($org_uri);
						AcesefURI::restoreLang($prev_lang);
                        $router->setMode(JROUTER_MODE_RAW);

						return $org_uri; // To obtain original URI, not sorted
					}
					
					// Append as non-SEF all non-proccessed vars
					if ($this->AcesefConfig->append_non_sef == 1) {
						$this->attributes->non_sef_part = AcesefURI::nonSefVars($uri, $vars, $this->attributes->non_sef_part);
					}
					
					if (!empty($segments) && in_array('acesef_url', $segments)){
						$url_found = AcesefCache::checkURL($uri->getVar('id'), false, true);
						if (is_object($url_found)) {
							// Check if it is blocked
							if (AcesefUtility::getParam($url_found->params, 'blocked') == '1') {
								$route = $url_found->url_real;
							} else {
								$route = $url_found->url_sef;
							}
							
							$uri = AcesefURI::_finalizeURI($uri, $route);
							
							AcesefURI::restoreLang($prev_lang);
							
							return $uri;
						} else {
							$segments = array();
						}
					}
					
					// Check if URL exists
					unset($vars['option']);
					unset($vars['Itemid']);
					unset($vars['lang']);
					if (!empty($vars) && $this->AcesefConfig->prevent_dup_error == 1 && AcesefURI::_checkDB($uri, $prev_lang)) {
						return $uri;
					}

					// Load segments as string
					if (!empty($segments)) {
						$sef_url = implode('/', $segments);
					}

					$routed = true;
				}

				// router.php
				$router_file = JPATH_BASE.DS.'components'.DS.$component.DS.'router.php';
				if (!$routed && file_exists($router_file) && $routing == 2) {					
					// Check if URL exists
					if (AcesefURI::_checkDB($uri, $prev_lang)) {
						return $uri;
					}
					
					// Prepare routing
					require_once($router_file);
					$function = substr($component, 4).'buildRoute';

					// Get vars array
					$vars = $uri->getQuery(true);

					// Run BuildRoute function
					$segments = $function($vars);

					// Append as non-SEF the non-proccessed vars
					if ($this->AcesefConfig->append_non_sef == 1) {
						$this->attributes->non_sef_part = AcesefURI::nonSefVars($uri, $vars, $this->attributes->non_sef_part);
					}
					
					// Check if URL exists
					unset($vars['option']);
					unset($vars['Itemid']);
					unset($vars['lang']);
					if (!empty($vars) && $this->AcesefConfig->prevent_dup_error == 1 && AcesefURI::_checkDB($uri, $prev_lang)) {
						return $uri;
					}

					// Prevent metadata of the previous component
					$this->attributes->meta = null;

					// Replace : with -
					$segments = $this->_encodeSegments($segments);

					// Load segments as string
					if (!empty($segments)) {
						if (substr($sef_url, -1) != '/') {
							$sef_url .= '/';
						}
						$sef_url .= implode('/', $segments);
					}

					$routed = true;
				}

				// Basic routing
				if (!$routed && $routing == 1) {
					// Check if URL exists
					if (AcesefURI::_checkDB($uri, $prev_lang)) {
						return $uri;
					}
					
					// Prevent metadata of the previous component
					$this->attributes->meta = null;

					// Get vars array
					$vars = $uri->getQuery(true);

					// Add all values to new url
					$filter = array('option', 'Itemid', 'lang', 'limitstart');
					foreach ($vars as $name => $value) {
						if (in_array($name, $filter)) {
							continue;
						}
						
						if (substr($sef_url, -1) != '/') {
							$sef_url .= '/';
						}
						
						$sef_url .= $value;
					}

					// Replace : with -
					if (!empty($sef_url)) {
						$url_array = explode('/', $sef_url);
						$url_array = $this->_encodeSegments($url_array);
						$sef_url = implode('/', $url_array);
					}

					$routed = true;
				}
			}
		
			// Reconnect Session ID to real url
			if (!empty($backup_vars['sid']) && $this->AcesefConfig->remove_sid == 1) {
				$uri->setVar('sid', $var_sid);
			}

			// Reconnect mosmsg to real url
			if (!empty($backup_vars['mosmsg'])) {
				$uri->setVar('mosmsg', $var_mosmsg);
			}
		}

		// Prevent recording the edit url of 404 page
		if ($sef_url == '404/edit' || $sef_url == '404/edit'.$this->AcesefConfig->url_suffix) {
			$routed = false;
		}

		// Finalize this job
		if ($routed) {
			// Check if we should create new SEF URLs
			if ($this->AcesefConfig->generate_sef == 0) {
				return $uri;
			}
			
			$real_url = AcesefURI::sortURItoString($uri);
			
			$sef_url = AcesefURI::_finalizeSEF($uri, $sef_url, $real_url, $component, $lang_code);
			
			$uri = AcesefURI::_finalizeURI($uri, $sef_url);
		}

		AcesefURI::restoreLang($prev_lang);
        
        // Combine original path with new path
        $path = $uri->getPath();
        if ($path != "") {
            if (substr($orig_path, 0, 10) == 'index.php/') {
                $orig_path = substr($orig_path, 10);
            }

            $path = rtrim($orig_path, '/').$path;

            $uri->setPath($path);
        }
        
		return $uri;
	}

    function getMode() {
        return JROUTER_MODE_SEF;
    }

	// Create a URI based on a full or partial url string
	function &_createURI($url) {
        // Create full URL if we are only appending variables to it
        if (substr($url, 0, 1) == '&') {
            $vars = array();

			if (strpos($url, '&amp;') !== false) {
			   $url = str_replace('&amp;', '&',$url);
			}

            parse_str($url, $vars);
            $vars = array_merge($this->getVars(), $vars);

            foreach ($vars as $key => $var) {
                if ($var == "") {
					unset($vars[$key]);
				}
            }

            $url = 'index.php?'.JURI::buildQuery($vars);
        }

        // Security - only allow one question mark in URL
        $pos = strpos($url, '?');
        if ( $pos !== false ) {
            $url = substr($url, 0, $pos+1) . str_replace('?', '%3F', substr($url, $pos+1));
        }

        // Decompose link into url component parts
        $uri = new JURI($url);

        return $uri;
    }

	// Set request vars
	function setRequestVars(&$vars) {
		if (is_array($vars) && count($vars)) {
			foreach ($vars as $name => $value) {
				// Clean the var
				$GLOBALS['_JREQUEST'][$name] = array();

				// Set the GET array
				$_GET[$name] = $value;
				$GLOBALS['_JREQUEST'][$name]['SET.GET'] = true;

				// Set the REQUEST array if request method is GET
				if ($_SERVER['REQUEST_METHOD'] == 'GET') {
					$_REQUEST[$name] = $value;
					$GLOBALS['_JREQUEST'][$name]['SET.REQUEST'] = true;
				}
			}
		}
    }
}
?>