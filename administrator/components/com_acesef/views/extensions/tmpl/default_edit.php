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

// Tmpl var
$tmpl = JRequest::getVar('tmpl');
?>

<script language="javascript">
	function submitbutton(pressbutton){
		// Check if is modal ivew
		<?php if ($tmpl == 'component') { ?>
		document.adminForm.modal.value = '1';
		<?php } ?>
		
		submitform(pressbutton);
	}
</script>

<form action="index.php?option=com_acesef&amp;controller=extensions&amp;task=edit&amp;cid[]=<?php echo $this->row->id; ?>&amp;tmpl=component" method="post" name="adminForm">
	<div>
		<fieldset class="adminform">
			<table class="toolbar1">
				<tr>
					<td class="desc" width="550px">
						<?php echo '<h3>'.$this->row->description.'</h3>'; ?>
					</td>
					<td>
						<a href="#" onclick="javascript: submitbutton('editSave'); window.top.setTimeout('SqueezeBox.close();', 1000);" class="toolbar1"><span class="icon-32-save1" title="<?php echo JText::_('Save'); ?>"></span><?php echo JText::_('Save'); ?></a>
					</td>
					<td>
						<a href="#" onclick="javascript: submitbutton('editApply'); " class="toolbar1"><span class="icon-32-apply1" title="<?php echo JText::_('Apply'); ?>"></span><?php echo JText::_('Apply'); ?></a>
					</td>
					<td>
						<a href="#" onclick="javascript: submitbutton('editCancel'); window.top.setTimeout('SqueezeBox.close();', 1000);" class="toolbar1"><span class="icon-32-cancel1" title="<?php echo JText::_('Cancel'); ?>"></span><?php echo JText::_('Cancel'); ?></a>
					</td>
				</tr>
			</table>
		</fieldset>
	</div>
	<div>
		<?php
			if ($params = $this->row->params->render('params', 'download_id')) {
		?>
			<fieldset class="adminform">
				<legend><?php echo JText::_('ACESEF_CONFIG_MAIN_UPGRADE_ID'); ?></legend>
				<?php echo $params;	?>
			</fieldset>
		<?php
			}
		?>
		<fieldset class="adminform">
			<legend><?php echo JText::_('Parameters'); ?></legend>

			<?php
				echo $this->tabs->startPane("extension");

				// URL tab
				echo $this->tabs->startPanel(JText::_('ACESEF_COMMON_URL'), "url");
				echo $this->sliders->startPanel(JText::_('ACESEF_PARAMS_URL_EXTENSION'), "extension");
				if ($params = $this->row->params->render('params', 'url')) {
					echo $params;
				}
				echo $this->sliders->endPanel();
				echo $this->sliders->startPanel(JText::_('ACESEF_PARAMS_URL_COMMON'), "common");
				if ($params = $this->row->params->render('params', 'default_url')) {
					echo $params;
				}
				echo $this->sliders->endPanel();
				echo $this->tabs->endPanel();

				// Meta tab
				echo $this->tabs->startPanel(JText::_('ACESEF_COMMON_METADATA'), "meta");
				if ($params = $this->row->params->render('params', 'default_meta')) {
					echo $params;
				}
				if ($params = $this->row->params->render('params', 'meta')) {
					echo $params;
				}
				echo $this->tabs->endPanel();

				// Sitemap tab
				echo $this->tabs->startPanel(JText::_('ACESEF_COMMON_SITEMAP'), "sitemap");
				if ($params = $this->row->params->render('params', 'default_sitemap')) {
					echo $params;
				}
				if ($params = $this->row->params->render('params', 'sitemap')) {
					echo $params;
				}
				if ($params = $this->row->params->render('params', 'default_sitemap_auto_header')) {
					echo $params;
				}
				if ($this->row->params->render('params', 'categorylist')) {
					echo $this->row->params->render('params', 'default_sitemap_auto_cats');
				}
				if ($params = $this->row->params->render('params', 'default_sitemap_auto')) {
					echo $params;
				}
				echo $this->tabs->endPanel();

				// Tags tab
				echo $this->tabs->startPanel(JText::_('ACESEF_COMMON_TAGS'), "tags");
				if ($params = $this->row->params->render('params', 'default_tags')) {
					echo $params;
				}
				if ($this->row->params->render('params', 'categorylist')) {
					echo $this->row->params->render('params', 'default_tags_cats');
				}
				if ($params = $this->row->params->render('params', 'tags')) {
					echo $params;
				}
				echo $this->tabs->endPanel();

				// Internal Links tab
				echo $this->tabs->startPanel(JText::_('ACESEF_COMMON_ILINKS'), "ilinks");
				if ($params = $this->row->params->render('params', 'default_ilinks')) {
					echo $params;
				}
				if ($this->row->params->render('params', 'categorylist')) {
					echo $this->row->params->render('params', 'default_ilinks_cats');
				}
				if ($params = $this->row->params->render('params', 'ilinks')) {
					echo $params;
				}
				echo $this->tabs->endPanel();

				// Social Bookmarks tab
				echo $this->tabs->startPanel(JText::_('ACESEF_COMMON_BOOKMARKS'), "bookmarks");
				if ($params = $this->row->params->render('params', 'default_bookmarks')) {
					echo $params;
				}
				if ($this->row->params->render('params', 'categorylist')) {
					echo $this->row->params->render('params', 'default_bookmarks_cats');
				}
				if ($params = $this->row->params->render('params', 'bookmarks')) {
					echo $params;
				}
				echo $this->tabs->endPanel();
				echo $this->tabs->endPane();
			?>
		</fieldset>
	</div>
	<div class="clr">
	</div>
	<input type="hidden" name="option" value="com_acesef" />
	<input type="hidden" name="controller" value="extensions" />
	<input type="hidden" name="task" value="edit" />
	<input type="hidden" name="modal" value="0" />
	<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
	<?php echo JHTML::_('form.token'); ?>
</form>