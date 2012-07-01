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

// Control Panel View Class
class AcesefViewAcesef extends AcesefView {
	
	// Display cpanel
	function display($tpl = null) {
		// Toolbar
		JToolBarHelper::title(JText::_('ACESEF_COMMON_CONTROLPANEL'), 'acesef');
		$this->toolbar->appendButton('Popup', 'cache', JText::_('ACESEF_CACHE_CLEAN'), 'index.php?option=com_acesef&amp;controller=purgeupdate&amp;task=cache&amp;tmpl=component', 300, 380);
		JToolBarHelper::divider();
		$this->toolbar->appendButton('Popup', 'help1', JText::_('Help'), 'http://www.joomace.net/support/docs/acesef/user-manual/control-panel?tmpl=component', 650, 500);
		
		// Load pane behavior
		jimport('joomla.html.pane');
		$pane =& JPane::getInstance('sliders');
		
		$this->AcesefConfig = AcesefFactory::getConfig();
		
		// Assign vars to the template
		$this->assignRef('pane', 		$pane);
		$this->assignRef('info', 		$this->get('Info'));
		$this->assignRef('extensions', 	$this->get('Extensions'));

		parent::display($tpl);
	}

	// Control Panel Buttons
	function acesefButton($link, $image, $text, $modal = false, $x = 500, $y = 450, $new_window = false) {
		// Initialise variables
		$lang = & JFactory::getLanguage();
		
		$new_window	= ($new_window) ? ' target="_blank"' : '';
  		?>

		<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
			<div class="icon">
				<?php
				if ($modal) {
					JHTML::_('behavior.modal');
					
					if (!strpos($link, 'tmpl=component')) {
						$link .= '&amp;tmpl=component';
					}
				?>
					<a href="<?php echo $link; ?>" style="cursor:pointer" class="modal" rel="{handler: 'iframe', size: {x: <?php echo $x; ?>, y: <?php echo $y; ?>}}"<?php echo $new_window; ?>>
				<?php
				} else {
				?>
					<a href="<?php echo $link; ?>"<?php echo $new_window; ?>>
				<?php
				}
					echo JHTML::_('image', 'administrator/components/com_acesef/assets/images/'.$image, $text );
				?>
					<span><?php echo $text; ?></span>
				</a>
			</div>
		</div>
		<?php
	}

	function showStatus($type) {
		static $status;
		
		if (!isset($status)) {
			$status = AcesefUtility::getSefStatus();
		}

		if (isset($status[$type])) {
			if ($type == "live_site") {
				echo '<input type="text" name="live_site" id="live_site" class="inputbox" size="35" value="'.$status['live_site'].'" />';
				echo '&nbsp;';
				echo '<input type="button" onclick="sefStatus(\''.$type.'\', \'0\');" value="'.JText::_('Save').'" />';
			}
			elseif ($type == "php") {
				if ($status[$type]) {
					echo '<span style="font-weight: bold; color: green;">'. PHP_VERSION .'</span>';
				} else {
					echo '<span style="font-weight: bold; color: red;">'. PHP_VERSION .'</span>';
				}
			}
			elseif ($type == "s_mod_rewrite") {
				if ($status[$type]) {
					echo '<span style="font-weight: bold; color: green;">'.JText::_('OK').'</span>';
				} else {
					echo '<span style="font-weight: bold; color: red;">'.JText::_('ACESEF_CPANEL_STATUS_SERVER_MOD_NO').'</span>';
				}
			}
			elseif ($type == "htaccess") {
				if ($status[$type]) {
					echo '<span style="font-weight: bold; color: green;">'.JText::_('OK').'</span>';
				} else {
					echo '<span style="font-weight: bold; color: red;">'.JText::_('ACESEF_CPANEL_STATUS_HTA_NO').'</span>';
				}
			}
			elseif ($type == "version_checker") {
				if ($status[$type]) {
					echo '&nbsp;&nbsp;&nbsp;';
					echo '<input type="button" onclick="sefStatus(\''.$type.'\', \'0\');" value="'.JText::_('Disable').'" />';
				} else {
					echo '&nbsp;&nbsp;&nbsp;';
					echo '<input type="button" onclick="sefStatus(\''.$type.'\', \'1\');" value="'.JText::_('Enable').'" />';
				}
			} elseif ($type == "jfrouter") {
				if ($status[$type]) {
					echo '<span style="font-weight: bold; color: red;">'.JText::_('Enabled').'</span>';
					echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
					echo '<input type="button" onclick="sefStatus(\''.$type.'\', \'0\');" value="'.JText::_('Disable').'" />';
				} else {
					echo '<span style="font-weight: bold; color: green;">'.JText::_('Disabled').'</span>';
					echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
					echo '<input type="button" onclick="sefStatus(\''.$type.'\', \'1\');" value="'.JText::_('Enable').'" />';
				}
			} elseif ($type == "languagefilter") {
				if ($status[$type]) {
					echo '<span style="font-weight: bold; color: red;">'.JText::_('Enabled').'</span>';
					echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
					echo '<input type="button" onclick="sefStatus(\''.$type.'\', \'0\');" value="'.JText::_('Disable').'" />';
				} else {
					echo '<span style="font-weight: bold; color: green;">'.JText::_('Disabled').'</span>';
					echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
					echo '<input type="button" onclick="sefStatus(\''.$type.'\', \'1\');" value="'.JText::_('Enable').'" />';
				}
			} else {
				if ($status[$type]) {
					echo '<span style="font-weight: bold; color: green;">'.JText::_('Enabled').'</span>';
					echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
					echo '<input type="button" onclick="sefStatus(\''.$type.'\', \'0\');" value="'.JText::_('Disable').'" />';
				} else {
					echo '<span style="font-weight: bold; color: red;">'.JText::_('Disabled').'</span>';
					echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
					echo '<input type="button" onclick="sefStatus(\''.$type.'\', \'1\');" value="'.JText::_('Enable').'" />';
				}
			}
		}
	}
}
?>