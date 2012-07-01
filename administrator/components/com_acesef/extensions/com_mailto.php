<?php
/**
* @package		AceSEF
* @subpackage	Mailto
* @copyright	2009 JoomAce LLC, www.joomce.net
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// No Permission
defined('_JEXEC') or die('Restricted access');

jimport('joomla.version');

class AceSEF_com_mailto extends AcesefExtension {
	
	protected $_helperExists = false;
	
	function __construct($params = null) {
		parent::__construct($params);
		
		$version = new JVersion();
		if (($version->RELEASE == '1.5' && $version->DEV_LEVEL >= 23)
			|| ($version->RELEASE == '1.6' && $version->DEV_LEVEL >= 1))
		{
			require_once JPATH_SITE . DS . 'components' . DS . 'com_mailto' . DS . 'helpers' . DS . 'mailto.php';
			$this->_helperExists = true;
		}
	}

	function build(&$vars, &$segments, &$do_sef, &$metadata, &$item_limitstart) {
		// Extract variables
        extract($vars);

		if (isset($link)) {
			if ($this->_helperExists) {
				$link = MailtoHelper::validateHash($link);
			} else {
				$link = base64_decode($link);
			}
			$link = str_replace(JURI::root(), '', $link);
			
			// Remove URL Suffix
			if ($this->params->get('remove_url_suffix', 0) && $this->AcesefConfig->url_suffix != '') {
				$urlSuffix = $this->AcesefConfig->url_suffix;
				if ($urlSuffix == substr($link, -strlen($urlSuffix))) {
					$link = substr($link, 0, -strlen($urlSuffix));
				}
			}
			
			if (substr($link, 0, 1) == '/') {
				$link = substr($link, 1, strlen($link) -1);
			}
			
			$segments[] = rtrim($link, '/');
			unset($vars['link']);
		}
		
		$metadata = parent::getMetaData($vars, $item_limitstart);
		
		unset($vars['limit']);
		unset($vars['limitstart']);
	}
}
?>