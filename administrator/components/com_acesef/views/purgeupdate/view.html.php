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

// View Class
class AcesefViewPurgeUpdate extends AcesefView {

	// Display purge
	function view($tpl = null) {
		// Get data from the model
		$this->assignRef('urls', $this->get('CountUrls'));
		$this->assignRef('meta', $this->get('CountMeta'));

		parent::display($tpl);
	}
}
