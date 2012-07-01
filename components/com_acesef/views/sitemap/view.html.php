<?php
/**
* @version		1.5.0
* @package		AceSEF
* @subpackage	AceSEF
* @copyright	2009-2010 JoomAce LLC, www.joomace.net
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

//No Permision
defined('_JEXEC') or die('Restricted access');

// Imports
jimport('joomla.application.component.view');

class AcesefViewSitemap extends JView {

	function display($tpl = null) {
		$mainframe =& JFactory::getApplication();
		$document =& JFactory::getDocument();
		$params = $mainframe->getParams();
		
		// Add page number to title
		$limit = $mainframe->getUserStateFromRequest('limit', 'limit', $params->get('display_num', $mainframe->getCfg('list_limit')), 'int');
		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
		if (!empty($limit) && !empty($limitstart)) {			
			$number = $limitstart / $limit; 
			$number++;

			$document->setTitle($params->get('page_title', '') . ' - ' . JText::_('PAGE') . ' ' . $number);
		}
		
		$this->assignRef('items', 		$this->get('Items'));
		$this->assignRef('params', 		$params);
		$this->assignRef('pagination',	$this->get('Pagination'));
		
		parent::display($tpl);
	}
}