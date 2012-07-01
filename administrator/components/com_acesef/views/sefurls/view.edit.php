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
class AcesefViewSefUrls extends AcesefView {

	// Edit URL
	function edit($tpl = null) {
		// Get data from model
		$model =& $this->getModel();
		$row = $model->getEditData('AcesefSefUrls');
		
		// Toolbar
		JToolBarHelper::title(JText::_('ACESEF_URL_EDIT_TITLE').' '.$row->url_sef, 'acesef');
		JToolBarHelper::custom('editSave', 'save1.png', 'save1.png', JTEXT::_('Save'), false);
		JToolBarHelper::custom('editApply', 'apply1.png', 'apply1.png', JTEXT::_('Apply'), false);
		JToolBarHelper::custom('editCancel', 'cancel1.png', 'cancel1.png', JTEXT::_('Cancel'), false);
		JToolBarHelper::divider();
		JToolBarHelper::custom('editSaveMoved', 'save1.png', 'save1.png', JTEXT::_('ACESEF_TOOLBAR_SEF_SAVEMOVED'), false);
		JToolBarHelper::divider();
		$this->toolbar->appendButton('Popup', 'help1', JText::_('Help'), 'http://www.joomace.net/support/docs/acesef/user-manual/urls?tmpl=component', 650, 500);
		
		//
		// Params
		//
		$params = new JParameter($row->params);
   	   	$url['custom'] = $params->get('custom', '0');
   	   	$url['published'] = $params->get('published', '0');
   	   	$url['locked'] = $params->get('locked', '0');
   	   	$url['blocked'] = $params->get('blocked', '0');
   	   	$url['trashed'] = $params->get('trashed', '0');
   	   	$url['notfound'] = $params->get('notfound', '0');
   	   	$url['tags'] = $params->get('tags', '0');
   	   	$url['ilinks'] = $params->get('ilinks', '0');
   	   	$url['bookmarks'] = $params->get('bookmarks', '0');
		$url['notes'] = $params->get('notes', '');
		
		$cache = $this->get('Cache');
		$url['cached'] = '0';
		if (isset($cache[$row->url_real])) {
			$url['cached'] = '1';
		}
		
		// Get alias
		$url['alias'] = self::getAliases($row->url_sef);
		
		// Assign values
		$this->assignRef('row', 		$row);
		$this->assignRef('url', 		$url);
		$this->assignRef('metadata', 	self::getMetadata($row->url_sef));
		$this->assignRef('sitemap', 	self::getSitemap($row->url_sef));
		
		parent::display($tpl);
	}
	
	function getAliases($url) {
		$aliases = "";
		$urls = AceDatabase::loadObjectList("SELECT url_old FROM #__acesef_urls_moved WHERE url_new = '{$url}' ORDER BY url_old");
		
		if (!is_null($urls)) {
			foreach ($urls as $u) {
				$aliases .= $u->url_old."\n";
			}
		}
		
		return $aliases;
	}
	
	function getMetadata($url) {
		$empty = new stdClass();
		$empty->id = "";
		$empty->published = 0;
		$empty->title = "";
		$empty->description = "";
		$empty->keywords = "";
		$empty->lang = "";
		$empty->robots = "";
		$empty->googlebot = "";
		$empty->canonical = "";
		
		$task = JRequest::getWord('task');
		if ($task == 'add') {
			return $empty;
		}
		
		$metadata = AceDatabase::loadObject("SELECT * FROM #__acesef_metadata WHERE url_sef = '{$url}'");
		if (!is_object($metadata)) {
			return $empty;
		}
		
		return $metadata;
	}
	
	function getSitemap($url) {
		$empty = new stdClass();
		$empty->id = "";
		$empty->published = "0";
		$empty->sdate = date('Y-m-d');
		$empty->frequency = $this->AcesefConfig->sm_freq;
		$empty->priority = $this->AcesefConfig->sm_priority;
		$empty->sparent = "";
		$empty->sorder = "";
		
		$task = JRequest::getWord('task');
		if ($task == 'add') {
			return $empty;
		}
		
		$sitemap = AceDatabase::loadObject("SELECT * FROM #__acesef_sitemap WHERE url_sef = '{$url}'");
		if (!is_object($sitemap)) {
			return $empty;
		}
		
		return $sitemap;
	}
}
?>