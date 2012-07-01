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

// Imports
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

// Control Panel Model Class
class AcesefModelAcesef extends AcesefModel {
	
	// Main constructer
	function __construct() {
        parent::__construct('acesef');
    }
    
	function sefStatus() {
        $type = JRequest::getVar('sefStatusType', '', 'post', 'string');
        $value = JRequest::getVar('sefStatusValue', '', 'post', 'string');
        $types = array('version_checker', 'sef', 'mod_rewrite', 'live_site', 'jfrouter', 'acesef', 'plugin', 'languagefilter', 'generate_sef');
        $msg = '';
        
        if (in_array($type, $types)) {
            // Joomla settings
            if ($type == 'sef' || $type == 'mod_rewrite' || $type == 'live_site') {
                $JoomlaConfig =& JFactory::getConfig();
                
                if ($type == 'sef') {
                    $JoomlaConfig->set('sef', $value);
                } 
				elseif ($type == 'mod_rewrite') {
                    $JoomlaConfig->set('sef_rewrite', $value);
                } 
				elseif ($type == 'live_site') {
					$live_site = JRequest::getVar('live_site', '', 'post', 'string');
					
					if (!empty($live_site) && (strpos($live_site, 'http') === false)) {
						$live_site = 'http://'.$live_site;
					}
					
					$JoomlaConfig->set('live_site', trim($live_site));
                }
                
                // Store the configuration
                $file = JPATH_CONFIGURATION.DS.'configuration.php';
        		if (!JFile::write($file, $JoomlaConfig->toString('PHP', array('class' => 'JConfig', 'closingtag' => false))) ) {
        			$msg = JText::_('Error writing Joomla! configuration, make the changes from Joomla Global Configuration page.');
        		}
            }
            elseif ($type == 'acesef' || $type == 'generate_sef' || $type == 'version_checker') {
                // AceSEF settings
				$AcesefConfig = AcesefFactory::getConfig();
                
                if ($type == 'acesef') {
                    $AcesefConfig->mode = $value;
                }
                elseif ($type == 'generate_sef') {
                    $AcesefConfig->generate_sef = $value;
                }
                elseif ($type == 'version_checker') {
                    $AcesefConfig->version_checker = $value;
                }
				
				AcesefUtility::storeConfig($AcesefConfig);
            }
            elseif ($type == 'plugin' || $type == 'jfrouter' || $type == 'languagefilter') {
                if ($type == 'plugin') {
                    $type = 'acesef';
                }
                
                if (!AceDatabase::query("UPDATE `#__extensions` SET `enabled` = '{$value}' WHERE (`element` = '{$type}') AND (`folder` = 'system') LIMIT 1")) {
                    $msg = JText::_('Error writing changing plugin status');
                }
            }
        }
        
        return $msg;
    }
	
	function saveDownloadID() {
		$download_id = trim(JRequest::getVar('download_id', '', 'post', 'string'));
		
		if (strlen($download_id) == 32) {
			$AcesefConfig = AcesefFactory::getConfig();
			$AcesefConfig->download_id = $download_id;
			
			AcesefUtility::storeConfig($AcesefConfig);
		}
	}

	// Check info
	function getInfo() {
		static $info;
		
		if (!isset($info)) {
			$info = array();
			if ($this->AcesefConfig->version_checker == 1){
				$info['version_installed'] = AcesefUtility::getXmlText(JPATH_ACESEF_ADMIN.'/acesef.xml', 'version');
				$version_info = AcesefUtility::getRemoteInfo();
				
				$info['version_latest'] = $version_info['acesef'];
				
				// Set the version status
				$info['version_status'] = version_compare($info['version_installed'], $info['version_latest']);
				$info['version_enabled'] = 1;
			} else {
				$info['version_status'] = 0;
				$info['version_enabled'] = 0;
			}
			
			$info['download_id'] = $this->AcesefConfig->download_id;

			$info['urls_sef'] = AceDatabase::loadResult("SELECT COUNT(*) FROM #__acesef_urls");
			$info['urls_moved'] = AceDatabase::loadResult("SELECT COUNT(*) FROM #__acesef_urls_moved");
			$info['metadata'] = AceDatabase::loadResult("SELECT COUNT(*) FROM #__acesef_metadata");
			$info['sitemap'] = AceDatabase::loadResult("SELECT COUNT(*) FROM #__acesef_sitemap");
			$info['tags'] = AceDatabase::loadResult("SELECT COUNT(*) FROM #__acesef_tags");
			$info['ilinks'] = AceDatabase::loadResult("SELECT COUNT(*) FROM #__acesef_ilinks");
			$info['bookmarks'] = AceDatabase::loadResult("SELECT COUNT(*) FROM #__acesef_bookmarks");
		}
		
		return $info;
	}
	
	// Get extensions list
	function getExtensions() {
		static $extensions;
		
		if(!isset($extensions)) {
			$extensions = AceDatabase::loadObjectList("SELECT * FROM #__acesef_extensions WHERE name != '' ORDER BY name");
		}
		
		return $extensions;
	}
}
?>