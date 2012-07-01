<?php
/**
* @version		1.5.0
* @package		AceSEF
* @subpackage	AceSEF
* @copyright	2009-2010 JoomAce LLC, www.joomace.net
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
?>

<fieldset class="adminform">
	<legend><?php echo JText::_('ACESEF_UPGRADE_VERSION_INFO'); ?></legend>
	<table class="adminform">
		<tr>
			<th>
				<?php echo JText::_('ACESEF_CPANEL_INSTALLED_VERSION'); ?> : <?php echo $this->versions['installed'];?>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<?php echo JText::_('ACESEF_CPANEL_LATEST_VERSION'); ?> : <?php echo $this->versions['latest'];?>
			</th>
		</tr>
	</table>
</fieldset>
<table class="noshow">
	<tr>
		<td width="50%">
			<fieldset class="adminform">
				<legend><?php echo JText::_('ACESEF_UPGRADE_FROM_SERVER'); ?></legend>
				<?php 
					if ((ACESEF_PACK == 'plus' || ACESEF_PACK == 'pro') && strlen($this->AcesefConfig->download_id) != 32){
						echo '<b><font color="red">'.JText::sprintf('ACESEF_UPGRADE_DOWNLOAD_ID', '<a href="index.php?option=com_acesef&controller=config">', '</a>').'</font></b>';
					} else {
				?>
				<form enctype="multipart/form-data" action="index.php" method="post" name="upgradeFromServer">
					<table class="adminform">
						<tr>
							<th>
								<br/>
								<button><?php echo JText::_('ACESEF_UPGRADE_FROM_SERVER_BTN'); ?></button>
								<br/><br/><br/>
							</th>
						</tr>
					</table>
					<input type="hidden" name="option" value="com_acesef" />
					<input type="hidden" name="controller" value="upgrade" />
					<input type="hidden" name="task" value="upgrade" />
					<input type="hidden" name="type" value="server" />
					<?php echo JHTML::_('form.token'); ?>
				</form>
				<?php } ?>
			</fieldset>
		</td>
		<td width="%50">
			<fieldset class="adminform">
				<legend><?php echo JText::_('ACESEF_UPGRADE_FROM_FILE'); ?></legend>
				<form enctype="multipart/form-data" action="index.php" method="post" name="upgradeFromUpload">
					<table class="adminform">
						<tr>
							<th colspan="2"><?php echo JText::_('ACESEF_UPGRADE_PACKAGE'); ?></th>
						</tr>
						<tr>
							<td width="100">
								<label for="install_package"><?php echo JText::_('ACESEF_COMMON_SELECT_FILE'); ?>:</label>
							</td>
							<td>
								<input class="input_box" id="install_package" name="install_package" type="file" size="40" />
								<input class="button" type="submit" value="<?php echo JText::_('ACESEF_UPGRADE_UPLOAD_UPGRADE'); ?>" />
							</td>
						</tr>
					</table>
					<input type="hidden" name="option" value="com_acesef" />
					<input type="hidden" name="controller" value="upgrade" />
					<input type="hidden" name="task" value="upgrade" />
					<input type="hidden" name="type" value="upload" />
					<?php echo JHTML::_('form.token'); ?>
				</form>
			</fieldset>
		</td>
	</tr>
</table>