<?php
/**
* @version		1.5.0
* @package		AceSEF
* @subpackage	AceSEF
* @copyright	2009-2010 JoomAce LLC, www.joomace.net
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// No Permission
defined('_JEXEC') or die('Restricted access');

?>
<form action="index.php?option=com_acesef&amp;controller=purgeupdate&amp;task=view&amp;tmpl=component" name="adminForm" method="post">
	<table class="noshow">
		<tr>
			<td width="50%">
				<fieldset>
					<legend><?php echo JText::_('ACESEF_COMMON_URLS'); ?></legend>
					<table>
						<tr>
							<td width="5%" height="20">
								&nbsp;
							</td>
							<td width="75%" height="20">
								<label for="name">
									<b><?php echo JText::_('Type'); ?></b>
								</label>
							</td>
							<td width="20%" height="20">
								<label for="name">
									<b><?php echo JText::_('ACESEF_COMMON_RECORDS'); ?></b>
								</label>
							</td>
						</tr>
						<tr>
							<td width="5%" height="20">
								<input type="checkbox" name="urls_sef" id="urls_sef" value="1" />
							</td>
							<td width="75%" height="20">
								<label for="name">
									<?php echo JText::_('ACESEF_COMMON_URLS_SEF'); ?>
								</label>
							</td>
							<td width="20%" height="20">
								<label for="name">
									<?php echo $this->urls['sef']; ?>
								</label>
							</td>
						</tr>
						<tr>
							<td width="5%" height="20">
								<input type="checkbox" name="urls_custom" id="urls_custom" value="1" />
							</td>
							<td width="75%" height="20">
								<label for="name">
									<?php echo JText::_('ACESEF_COMMON_URLS_CUSTOM'); ?>
								</label>
							</td>
							<td width="20%" height="20">
								<label for="name">
									<?php echo $this->urls['custom']; ?>
								</label>
							</td>
						</tr>
						<tr>
							<td width="5%" height="20">
								<input type="checkbox" name="urls_404" id="urls_404" value="1" />
							</td>
							<td width="75%" height="20">
								<label for="name">
									<?php echo JText::_('ACESEF_COMMON_URLS_404'); ?>
								</label>
							</td>
							<td width="20%" height="20">
								<label for="name">
									<?php echo $this->urls['404']; ?>
								</label>
							</td>
						</tr>
						<tr>
							<td width="5%" height="20">
								<input type="checkbox" name="urls_moved" id="urls_moved" value="1" />
							</td>
							<td width="75%" height="20">
								<label for="name">
									<?php echo JText::_('ACESEF_COMMON_URLS_MOVED'); ?>
								</label>
							</td>
							<td width="20%" height="20">
								<label for="name">
									<?php echo $this->urls['moved']; ?>
								</label>
							</td>
						</tr>
						<tr>
							<td width="5%" height="20">
								<input type="checkbox" name="urls_locked" id="urls_locked" value="1" />
							</td>
							<td width="75%" height="20">
								<label for="name">
									<?php echo JText::_('ACESEF_COMMON_URLS_LOCKED'); ?>
								</label>
							</td>
							<td width="20%" height="20">
								<label for="name">
									<?php echo $this->urls['locked']; ?>
								</label>
							</td>
						</tr>
						<tr>
							<td width="5%" height="20">
								<input type="checkbox" name="urls_trashed" id="urls_trashed" value="1" />
							</td>
							<td width="75%" height="20">
								<label for="name">
									<?php echo JText::_('ACESEF_COMMON_URLS_TRASHED'); ?>
								</label>
							</td>
							<td width="20%" height="30">
								<label for="name">
									<?php echo $this->urls['trashed']; ?>
								</label>
							</td>
						</tr>
					</table>
					<?php if ($this->urls['total'] != 0) { ?>
						<input type="submit" class="button" name="deleteurls" onclick="window.top.setTimeout('SqueezeBox.close();', 1000);" value="<?php echo JText::_('Delete'); ?>" />
						<input type="submit" class="button" name="updateurls" value="<?php echo JText::_('ACESEF_TOOLBAR_UPDATE'); ?>" />
					<?php } ?>
				</fieldset>
			</td>
			<td width="50%">
				<fieldset height="350">
					<legend><?php echo JText::_('ACESEF_PURGE_UPDATE_METADATA'); ?></legend>
					<table>
						<tr>
							<td width="5%" height="20">
								&nbsp;
							</td>
							<td width="75%" height="20">
								<label for="name">
									<b><?php echo JText::_('Type'); ?></b>
								</label>
							</td>
							<td width="20%" height="20">
								<label for="name">
									<b><?php echo JText::_('ACESEF_COMMON_RECORDS'); ?></b>
								</label>
							</td>
						</tr>
						<tr>
							<td width="5%" height="20">
								<input type="checkbox" name="meta_all" id="meta_all" value="1" />
							</td>
							<td width="75%" height="20">
								<label for="name">
									<?php echo JText::_('All fields'); ?>
								</label>
							</td>
							<td width="20%" height="20">
								<label for="name">
									<?php echo $this->meta['all']; ?>
								</label>
							</td>
						</tr>
						<tr>
							<td width="5%" height="20">
								<input type="checkbox" name="meta_title" id="meta_title" value="1" />
							</td>
							<td width="75%">
								<label for="name" height="20">
									<?php echo JText::_('Title'); ?>
								</label>
							</td>
							<td width="20%" height="20">
								<label for="name">
									<?php echo $this->meta['title']; ?>
								</label>
							</td>
						</tr>
						<tr>
							<td width="5%" height="20">
								<input type="checkbox" name="meta_desc" id="meta_desc" value="1" />
							</td>
							<td width="75%" height="20">
								<label for="name">
									<?php echo JText::_('Description'); ?>
								</label>
							</td>
							<td width="20%" height="20">
								<label for="name">
									<?php echo $this->meta['desc']; ?>
								</label>
							</td>
						</tr>
						<tr>
							<td width="5%" height="20">
								<input type="checkbox" name="meta_key" id="meta_key" value="1" />
							</td>
							<td width="75%" height="20">
								<label for="name">
									<?php echo JText::_('Keywords'); ?>
								</label>
							</td>
							<td width="20%" height="20">
								<label for="name">
									<?php echo $this->meta['key']; ?>
								</label>
							</td>
						</tr>
						<tr>
							<td width="5%" height="20">
								&nbsp;
							</td>
							<td width="75%" height="20">
								<label for="name">
									&nbsp;
								</label>
							</td>
							<td width="20%" height="30">
								<label for="name">
									&nbsp;
								</label>
							</td>
						</tr>
					</table>
					<?php if ($this->meta['total'] != 0) { ?>
						<input type="submit" class="button" name="deletemeta" onclick="window.top.setTimeout('SqueezeBox.close();', 1000);" value="<?php echo JText::_('Delete'); ?>" />
						<input type="submit" class="button" name="updatemeta" value="<?php echo JText::_('ACESEF_TOOLBAR_UPDATE'); ?>" />
					<?php } ?>
				</fieldset>
			</td>
		</tr>
	</table>
	<input type="hidden" name="option" value="com_acesef" />
	<input type="hidden" name="controller" value="purgeupdate" />
	<input type="hidden" name="task" value="deleteupdate" />
	<?php echo JHTML::_('form.token'); ?>
</form>
<br/>