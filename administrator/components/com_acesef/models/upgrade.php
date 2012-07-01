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
jimport('joomla.installer.installer');
jimport('joomla.installer.helper');

// Model Class
class AcesefModelUpgrade extends AcesefModel {

	// Main constructer
	function __construct() {
        parent::__construct('upgrade');
    }
    
	// Upgrade
    function upgrade() {
		AcesefUtility::import('library.installer');
		
		// Get package
		$type = JRequest::getVar('type');
		if ($type == 'upload') {
			$userfile = JRequest::getVar('install_package', null, 'files', 'array');
			$package = AcesefInstaller::getPackageFromUpload($userfile);
		} elseif ($type == 'server') {
			$url = self::_getURL();
			$package = AcesefInstaller::getPackageFromServer($url);
		}

		// Was the package unpacked?
		if (!$package) {
			$this->setState('message', 'Unable to find install package.');
			return false;
		}

		// Get current version
		$cur_version = AcesefUtility::getXmlText(JPATH_ACESEF_ADMIN.DS.'acesef.xml', 'version');
		if (empty($cur_version)) {
			$this->setState('message', JText::_('Could not find current version.'));
			JFolder::delete($package['dir']);
			return false;
		}

		// Create an array of upgrade files
		$pack_dir = $package['dir'].DS.'upgrade';
		$pack_files = JFolder::files($pack_dir, '.php$');

		if (empty($pack_files)) {
			$this->setState('message', JText::_('This package does not contain any upgrade informations.'));
			JFolder::delete($package['dir']);
			return false;
		}

		natcasesort($pack_files);

		// prepare vars
		$this->_fileError = false;
		$this->_fileList = array();
		$this->_sqlList = array();
		$this->_scriptList = array();
		
		// Load the file list first
		require_once($pack_dir.DS.'filelist.php');

		// load each upgrade file starting with current version in ascending order
		foreach ($pack_files as $file) {
			if (!preg_match("/^[0-9]+\.[0-9]+\.[0-9]+\.php$/i", $file)) {
				continue;
			}

			if (strnatcasecmp($file, $cur_version.".php") >= 0) {
				require_once($pack_dir.DS.$file);
			}
		}
		
		if ($this->_fileError == false) {
			// set errors variable
			$errors = false;

			// checkup
			foreach ($this->_fileList as $dest => $upgrade) {
				// Destination
				$dest = JPath::clean($dest);
				
				// check if source file is present in upgrade package
				if ($upgrade->action == 'upgrade') {
					$src = JPath::clean($package['dir'].DS.$upgrade->p_path);
					if ($upgrade->folder) {
						if(!JFolder::exists($src)) {
							JError::raiseWarning(100, JText::_('Folder does not exist in upgrade package').': '.$upgrade->p_path);
							$errors = true;
						}
					} else {
						if(!JFile::exists($src)) {
							JError::raiseWarning(100, JText::_('File does not exist in upgrade package').': '.$upgrade->p_path);
							$errors = true;
						}
					}
				}

				if (!$upgrade->folder && (($upgrade->action == 'delete') && (JFile::exists($dest))) || (($upgrade->action == 'upgrade') && (!JFile::exists($dest)))) {
					// if the file is to be deleted or created, the file's directory must be writable
					$dir = dirname($dest);
					if (!JFolder::exists($dir)) {
						// we need to create the directory where the file is to be created
						if(!JFolder::create($dir)) {
							JError::raiseWarning(100, JText::_('Directory could not be created') . ': ' . $dir);
							$errors = true;
						}
					}
				}
			}

			if (!$errors) {
				// SQL queries
				foreach ($this->_sqlList as $sql) {
					if(!AceDatabase::query($sql)) {
						JError::raiseWarning(100, JText::_('Unable to execute SQL query') . ': ' . $sql);
						$errors = true;
					}
				}
				
				// File operations
				foreach ($this->_fileList as $dest => $upgrade) {
					$dest = JPath::clean($dest);
					
					if ($upgrade->action == 'delete') {
						if ($upgrade->folder) {
							if (JFolder::exists($dest)) {
								if (!JFolder::delete($dest)) {
									JError::raiseWarning(100, JText::_('Could not delete folder. Please, check the write permissions on').' '.$dest);
									$errors = true;
								}
							}
						} else {
							if (JFile::exists($dest)) {
								if (!JFile::delete($dest)) {
									JError::raiseWarning(100, JText::_('Could not delete file. Please, check the write permissions on').' '.$dest);
									$errors = true;
								}
							}
						}
					}
					elseif ($upgrade->action == 'upgrade') {
						$src = JPath::clean($package['dir'].DS.$upgrade->p_path);
						
						if ($upgrade->folder) {
							if (!(JFolder::copy($src, $dest, null, true))) {
								JError::raiseWarning(100, JText::sprintf('Failed to copy folder: "' . $src . '" =>>> "' . $dest . '"'));
								$errors = true;
							}
						} else {
							$folder = dirname($dest);

							// create the destination directory if needed
							if (!JFolder::exists($folder)) {
								JFolder::create($folder);
							}

							if (!JFile::copy($src, $dest)) {
								JError::raiseWarning(100, JText::sprintf('Failed to copy file: "' . $src . '" =>>> "' . $dest . '"'));
								$errors = true;
							}
						}
					}
				}

				// Scripts
				foreach ($this->_scriptList as $script) {
					$file = JPath::clean($package['dir'].DS.$script);
					if(!JFile::exists($file)) {
						JError::raiseWarning(100, JText::_('Could not find script file').': '.$script);
						$errors = true;
					} else {
						include($file);
					}
				}
			}

			if (!$errors) {
				JFactory::getApplication()->enqueueMessage(JText::_('ACESEF_UPGRADE_SUCCESS'));
			}
			else {
				JError::raiseWarning(100, JText::_('ACESEF_UPGRADE_UNSUCCESS'));
			}
		}

		JFolder::delete($package['dir']);
		return;
    }
	
	function _getURL() {
		$f = 'downloads/acesef/component/acesef-basic-17/download?method=upgrade';
		$c = 'downloads/download-request?method=upgrade&download_id=';
		$download_id = $this->AcesefConfig->download_id;
		
		if (strlen($download_id) == 32){
			$url = $c.$download_id;
		} else {
			$url = $f;
		}
		
		return $url;
	}
	
	function _addFile($j_path, $p_path, $action = 'upgrade', $folder = false) {
		if (!in_array($action, array('upgrade', 'delete'))) {
			$this->fileError = true;
			JError::raiseWarning(100, JText::_('Invalid upgrade operation') . ': ' . $action);
			return false;
		}

		$upgrade = new stdClass();
		$upgrade->action = $action;
		$upgrade->p_path = $p_path;
		$upgrade->folder = $folder;

		$this->_fileList[$j_path] = $upgrade;
    }
	
	function _addSQL($sql) {
		$this->_sqlList[] = $sql;
    }

    function _addScript($script) {
		$this->_scriptList[] = $script;
    }
}
?>