<?php

/**
 * @package		SP Paypal
 * @subpackage	Components
 * @copyright	SP CYEND - All rights reserved.
 * @author		SP CYEND
 * @link		http://www.cyend.com
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * SPTransfers View
 */
class SPTransferViewMonitoring_Log extends JView
{
	/**
	 * SPTransfers view display method
	 * @return void
	 */
	function display($tpl = null) 
	{
		// Set the toolbar
		$this->addToolBar();

		// Display the template
		parent::display($tpl);

		// Set the document
		$this->setDocument();
	}

	/**
	 * Setting the toolbar
	 */
	protected function addToolBar() 
	{
		$canDo = SPTransferHelper::getActions();
		
		JToolBarHelper::title(JText::_('COM_SPTRANSFER_TABLES_TITLE'), 'install.png');
                
		if ($canDo->get('core.admin')) 
		{
			JToolBarHelper::preferences('com_sptransfer');
		}
                $bar=& JToolBar::getInstance( 'toolbar' );
                $bar->appendButton( 'Help', 'help', 'JTOOLBAR_HELP', 'http://cyend.com/extensions/extensions/components/documentation/88-user-guide-sp-transfer', 640, 480 );
	}
	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument() 
	{
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_SPTRANSFER_ADMINISTRATION'));
	}
}
