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

// Model Class
class AcesefModelRestoreMigrate extends AcesefModel {
	
	// Main constructer
	function __construct() {
		parent::__construct('restoremigrate');
	}
	
	function backup() {
		list($query, $filename, $fields, $line) = self::_backupGetVars();
		
		$ret = AcesefUtility::backupDB($query, $filename, $fields, $line);

		return $ret;
    }
	
	function _backupGetVars() {
		AcesefUtility::import('library.backuprestore');
		
		$items = array('sefurls', 'movedurls', 'metadata', 'sitemap', 'tags', 'ilinks', 'bookmarks');
		
		foreach ($items as $item) {
			if (JRequest::getVar('backup_'.$item, 0, 'post')) {
				$_table = $item;
				
				if ($item == 'sefurls') {
					$_table = 'urls';
				}
				
				if ($item == 'movedurls') {
					$_table = 'urls_moved';
				}
				
				$class = new AcesefBackupRestore(array('_table' => $_table, '_where' => ''));
				$function = 'backup' . ucfirst($item);
				
				list($query, $filename, $fields, $line) = $class->$function();
				
				return array($query, $filename, $fields, $line);
			}
		}
	}
	
    function restore() {		
		// Get the uploaded file
		if (!$file = self::_getUploadedFile()) {
			return false;
		}

		// Load SQL
		$lines = file($file);

		$result = true;
		for ($i = 0, $n = count($lines); $i < $n; $i++) {
			// Trim line
			$line = trim($lines[$i]);
			
			list($preg, $line) = self::_restoreGetPregLine($line);
			
			// Ignore empty lines
			if (strlen($line) == 0 || empty($line) || $line == '') {
				continue;
			}

			// If the query continues at the next line.
			while (substr($line, -1) != ';' && $i + 1 < count($lines)) {
				$i++;
				$newLine = trim($lines[$i]);
				
				if (strlen($newLine) == 0) {
					continue;
				}
				
				$line .= ' '.$lines[$i];
			}

			if (preg_match($preg, $line) > 0) {
				$this->_db->setQuery($line);
				if (!$this->_db->query()) {
					JError::raiseWarning( 100, JText::_('Error importing line').': '.$line.'<br />'.$this->_db->getErrorMsg());
					$result = false;
				}
			} else {
				JError::raiseWarning(100, JText::_('Ignoring line').': '.$line);
			}
		}

		JFile::delete($file);
		
		return $result;
    }
	
	function _restoreGetPregLine($line) {
		AcesefUtility::import('library.backuprestore');
		
		$items = array('sefurls', 'movedurls', 'metadata', 'sitemap', 'tags', 'ilinks', 'bookmarks', 'joomsef', 'shUrl', 'shMetadata', 'sh2', 'shAliases');
		
		foreach ($items as $item) {
			if (JRequest::getVar('restore_'.$item, 0, 'post')) {
				$class = new AcesefBackupRestore();
				$function = 'restore' . ucfirst($item);
				
				list($preg, $line) = $class->$function($line);
				
				return array($preg, $line);
			}
		}
	}
	
	function _getUploadedFile () {
		$userfile = JRequest::getVar('file_restore', null, 'files', 'array');

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
		$config =& JFactory::getConfig();
		$tmp_dest = $config->getValue('config.tmp_path').DS.$userfile['name'];
		$tmp_src  = $userfile['tmp_name'];

		// Move uploaded file
		jimport('joomla.filesystem.file');
		$uploaded = JFile::upload($tmp_src, $tmp_dest);
		
		if (!$uploaded) {
			JError::raiseWarning('SOME_ERROR_CODE', '<br /><br />' . JText::_('File not uploaded, please, make sure the "Global Configuration=>Server=>Path to Temp-folder" is valid.') . '<br /><br /><br />');
			return false;
		}
		
		return $tmp_dest;
	}
}
?>