<?php
/**
* @version		1.5.0
* @package		AceSEF Library
* @subpackage	Sitemap
* @copyright	2009-2010 JoomAce LLC, www.joomace.net
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// No Permission
defined('_JEXEC') or die('Restricted Access');

// Sitemap class
class AcesefSitemap {

	function getPriorityList() {		
		static $list;
		
		if(!isset($list)) {
			$list[] = JHTML::_('select.option', '0.0', '0.0');
			$list[] = JHTML::_('select.option', '0.1', '0.1');
			$list[] = JHTML::_('select.option', '0.2', '0.2');
			$list[] = JHTML::_('select.option', '0.3', '0.3');
			$list[] = JHTML::_('select.option', '0.4', '0.4');
			$list[] = JHTML::_('select.option', '0.5', '0.5');
			$list[] = JHTML::_('select.option', '0.6', '0.6');
			$list[] = JHTML::_('select.option', '0.7', '0.7');
			$list[] = JHTML::_('select.option', '0.8', '0.8');
			$list[] = JHTML::_('select.option', '0.9', '0.9');
			$list[] = JHTML::_('select.option', '1.0', '1.0');
		}
		
		return $list;
	}
	
	function getFrequencyList() {		
		static $list;
		
		if(!isset($list)) {
			$list[] = JHTML::_('select.option', 'always', JText::_('ACESEF_SITEMAP_SELECT_ALWAYS'));
			$list[] = JHTML::_('select.option', 'hourly', JText::_('ACESEF_SITEMAP_SELECT_HOURLY'));
			$list[] = JHTML::_('select.option', 'daily', JText::_('ACESEF_SITEMAP_SELECT_DAILY'));
			$list[] = JHTML::_('select.option', 'weekly', JText::_('ACESEF_SITEMAP_SELECT_WEEKLY'));
			$list[] = JHTML::_('select.option', 'monthly', JText::_('ACESEF_SITEMAP_SELECT_MONTHLY'));
			$list[] = JHTML::_('select.option', 'yearly', JText::_('ACESEF_SITEMAP_SELECT_YEARLY'));
			$list[] = JHTML::_('select.option', 'never', JText::_('ACESEF_SITEMAP_SELECT_NEVER'));
		}
		
		return $list;
	}
}