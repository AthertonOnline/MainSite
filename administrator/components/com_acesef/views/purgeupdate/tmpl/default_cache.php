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
<form action="index.php?option=com_acesef&amp;controller=purgeupdate&amp;task=cache&amp;tmpl=component" name="adminForm" method="post">
	<fieldset>
		<legend><?php echo JText::_('ACESEF_COMMON_CACHE'); ?></legend>
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
					<input type="checkbox" name="cache_versions" value="1" />
				</td>
				<td width="75%" height="20">
					<label for="name">
						<?php echo JText::_('ACESEF_CONFIG_MAIN_CACHE_VERSION'); ?>
					</label>
				</td>
				<td width="20%" height="20">
					<label for="name">
						<?php echo $this->count['versions']; ?>
					</label>
				</td>
			</tr>
			<tr>
				<td width="5%" height="20">
					<input type="checkbox" name="cache_extensions" value="1" />
				</td>
				<td width="75%" height="20">
					<label for="name">
						<?php echo JText::_('ACESEF_CONFIG_MAIN_CACHE_PARAMS'); ?>
					</label>
				</td>
				<td width="20%" height="20">
					<label for="name">
						<?php echo $this->count['extensions']; ?>
					</label>
				</td>
			</tr>
			<tr>
				<td width="5%" height="20">
					<input type="checkbox" name="cache_urls" value="1" />
				</td>
				<td width="75%" height="20">
					<label for="name">
						<?php echo JText::_('ACESEF_COMMON_URLS_SEF'); ?>
					</label>
				</td>
				<td width="20%" height="20">
					<label for="name">
						<?php echo $this->count['urls']; ?>
					</label>
				</td>
			</tr>
			<tr>
				<td width="5%" height="20">
					<input type="checkbox" name="cache_urls_moved" value="1" />
				</td>
				<td width="75%" height="20">
					<label for="name">
						<?php echo JText::_('ACESEF_COMMON_URLS_MOVED'); ?>
					</label>
				</td>
				<td width="20%" height="20">
					<label for="name">
						<?php echo $this->count['urls_moved']; ?>
					</label>
				</td>
			</tr>
			<tr>
				<td width="5%" height="20">
					<input type="checkbox" name="cache_metadata" value="1" />
				</td>
				<td width="75%" height="20">
					<label for="name">
						<?php echo JText::_('ACESEF_COMMON_METADATA'); ?>
					</label>
				</td>
				<td width="20%" height="20">
					<label for="name">
						<?php echo $this->count['metadata']; ?>
					</label>
				</td>
			</tr>
			<tr>
				<td width="5%" height="20">
					<input type="checkbox" name="cache_sitemap" value="1" />
				</td>
				<td width="75%" height="20">
					<label for="name">
						<?php echo JText::_('ACESEF_COMMON_SITEMAP'); ?>
					</label>
				</td>
				<td width="20%" height="20">
					<label for="name">
						<?php echo $this->count['sitemap']; ?>
					</label>
				</td>
			</tr>
			<tr>
				<td width="5%" height="20">
					<input type="checkbox" name="cache_tags" value="1" />
				</td>
				<td width="75%" height="20">
					<label for="name">
						<?php echo JText::_('ACESEF_COMMON_TAGS'); ?>
					</label>
				</td>
				<td width="20%" height="20">
					<label for="name">
						<?php echo $this->count['tags']; ?>
					</label>
				</td>
			</tr>
			<tr>
				<td width="5%" height="20">
					<input type="checkbox" name="cache_ilinks" value="1" />
				</td>
				<td width="75%" height="20">
					<label for="name">
						<?php echo JText::_('ACESEF_COMMON_ILINKS'); ?>
					</label>
				</td>
				<td width="20%" height="20">
					<label for="name">
						<?php echo $this->count['ilinks']; ?>
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
				<td width="20%" height="20">
					<label for="name">
						&nbsp;
					</label>
				</td>
			</tr>
		</table>
		<input type="submit" class="button" name="cleancache" onclick="window.top.setTimeout('SqueezeBox.close();', 1500);" value="<?php echo JText::_('ACESEF_CACHE_CLEAN'); ?>" />
	</fieldset>
	<input type="hidden" name="option" value="com_acesef" />
	<input type="hidden" name="controller" value="purgeupdate" />
	<input type="hidden" name="task" value="cleancache" />
	<?php echo JHTML::_('form.token'); ?>
</form>
<br/>