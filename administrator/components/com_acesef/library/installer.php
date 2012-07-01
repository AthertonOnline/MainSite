<?php
/**
* @version		1.7.0
* @package		AceSEF Library
* @subpackage	Installer
* @copyright	2009-2011 JoomAce LLC, www.joomace.net
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// No Permission
defined('_JEXEC') or die('Restricted access');

// Imports
jimport('joomla.installer.helper');

class AcesefInstaller {
	
	function __construct() 	{
		parent::__construct();
		
		// Get config object
		$this->AcesefConfig = AcesefFactory::getConfig();
	}
	
	function getPackageFromUpload($userfile) {
		// Make sure that file uploads are enabled in php
		if (!(bool) ini_get('file_uploads')) {
			JError::raiseWarning(100, JText::_('WARNINSTALLFILE'));
			return false;
		}

		// Make sure that zlib is loaded so that the package can be unpacked
		if (!extension_loaded('zlib')) {
			JError::raiseWarning(100, JText::_('WARNINSTALLZLIB'));
			return false;
		}

		// If there is no uploaded file, we have a problem...
		if (!is_array($userfile) ) {
			JError::raiseWarning(100, JText::_('No file selected'));
			return false;
		}

		// Check if there was a problem uploading the file.
		if ( $userfile['error'] || $userfile['size'] < 1 ) {
			JError::raiseWarning(100, JText::_('WARNINSTALLUPLOADERROR'));
			return false;
		}

		// Build the appropriate paths
		$JoomlaConfig =& JFactory::getConfig();
		$tmp_dest = $JoomlaConfig->get('tmp_path').DS.$userfile['name'];
		$tmp_src  = $userfile['tmp_name'];

		// Move uploaded file
		jimport('joomla.filesystem.file');
		$uploaded = JFile::upload($tmp_src, $tmp_dest);
		
		if (!$uploaded) {
			JError::raiseWarning('SOME_ERROR_CODE', '<br /><br />' . JText::_('File not uploaded, please, make sure that your AceSEF=>Configuraiton=>Download-ID and/or the "Global Configuration=>Server=>Path to Temp-folder" field has a valid value.') . '<br /><br /><br />');
			return false;
		}

		// Unpack the downloaded package file
		$package = JInstallerHelper::unpack($tmp_dest);

		// Delete the package file
		JFile::delete($tmp_dest);

		return $package;
    }
	
	function getPackageFromServer($url) {
		// Make sure that file uploads are enabled in php
		if (!(bool) ini_get('file_uploads')) {
			JError::raiseWarning('1001', JText::_('ACESEF_EXTENSIONS_VIEW_INSTALL_PHP_SETTINGS'));
			return false;
		}

		// Make sure that zlib is loaded so that the package can be unpacked
		if (!extension_loaded('zlib')) {
			JError::raiseWarning('1001', JText::_('ACESEF_EXTENSIONS_VIEW_INSTALL_PHP_ZLIB'));
			return false;
		}
		
		// Get temp path
		$JoomlaConfig =& JFactory::getConfig();
		$tmp_dest = $JoomlaConfig->get('tmp_path');

		$url = str_replace('http://www.joomace.net/', '', $url);
		$url = str_replace('https://www.joomace.net/', '', $url);
		$url = 'http://www.joomace.net/'.$url;
		
		// Grab the package
		$data = AcesefUtility::getRemoteData($url);
		
		$target = $tmp_dest.DS.'acesef_upgrade.zip';
		
		// Write buffer to file
		$written = JFile::write($target, $data);
		
		if (!$written) {
			JError::raiseWarning('SOME_ERROR_CODE', '<br /><br />' . JText::_('File not uploaded, please, make sure that your "AceSEF=>Configuration=>Download-ID" and/or the "Global Configuration=>Server=>Path to Temp-folder" field has a valid value.') . '<br /><br /><br />');
			return false;
		}
		
		$p_file = basename($target);
		
		// Was the package downloaded?
		if (!$p_file) {
			JError::raiseWarning('SOME_ERROR_CODE', JText::_('Invalid Download-ID'));
			return false;
		}

		// Unpack the downloaded package file
		$package = JInstallerHelper::unpack($tmp_dest.DS.$p_file);
		
		if (!$package) {
			JError::raiseWarning('SOME_ERROR_CODE', JText::_('An error occured, please, make sure that your "AceSEF=>Configuration=>Download-ID" and/or the "Global Configuration=>Server=>Path to Temp-folder" field has a valid value.'));
			return false;
		}
		
		// Delete the package file
		JFile::delete($tmp_dest.DS.$p_file);
		
		return $package;
	}
}