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

// Imports
jimport('joomla.html.pane');
AcesefUtility::import('library.sitemap');

// Tabs instance
$startOffset = JRequest::getInt('startOffset', 0);
$pane =& JPane::getInstance('Tabs', array('startOffset' => $startOffset));

// Get task and tmpl vars
$tmpl = JRequest::getVar('tmpl');
$task = JRequest::getVar('task');

?>

<script language="javascript">
	function submitbutton(pressbutton){
		var form = document.adminForm;
		
		// Check if is modal view
		<?php if ($tmpl == 'component') { ?>
			form.modal.value = '1';
		<?php } ?>
		
		if (pressbutton == 'editCancel') {
			submitform(pressbutton);
			return;
		} else {
			<?php if ($task == 'add') { ?>
				form.url_cdate.value = "<?php echo date('Y-m-d H:i:s'); ?>";
			<?php } else { ?>
				form.url_mdate.value = "<?php echo date('Y-m-d H:i:s'); ?>";
			<?php }?>
		}
		
		// Real URL must begin with index.php
		if (form.url_real.value.match(/^index.php/)) {
			submitform(pressbutton);
		} else {
			alert("<?php echo JText::_('The Real URL must begin with index.php'); ?>");
		}
	}
</script>
  
<form action="index.php?option=com_acesef&amp;controller=sefurls&amp;task=edit&cid[]=<?php echo $this->row->id; ?>&amp;tmpl=component" method="post" name="adminForm" id="adminForm">
	<?php if ($tmpl == 'component') { ?>
	<div>
		<fieldset class="adminform">
			<table class="toolbar1">
				<tr>
					<td class="desc" width="550px">
						<?php
							$text = JText::_('ACESEF_URL_EDIT_NEW');
							if ($task != 'add') {
								$text = '<u>'.JText::_('ACESEF_URL_EDIT_TITLE').'</u> '.$this->row->url_sef; 
							}
							
							echo '<h3>'.$text.'</h3>';
						?>
					</td>
					<td>
						<a href="#" onclick="javascript: submitbutton('editSave'); window.top.setTimeout('SqueezeBox.close();', 1000);" class="toolbar1"><span class="icon-32-save1" title="<?php echo JText::_('Save'); ?>"></span><?php echo JText::_('Save'); ?></a>
					</td>
					<?php if ($task != 'add') {	?>
					<td>
						<a href="#" onclick="javascript: submitbutton('editApply');" class="toolbar1"><span class="icon-32-apply1" title="<?php echo JText::_('Apply'); ?>"></span><?php echo JText::_('Apply'); ?></a>
					</td>
					<?php }	?>
					<td>
						<a href="#" onclick="javascript: submitbutton('editCancel'); window.top.setTimeout('SqueezeBox.close();', 1000);" class="toolbar1"><span class="icon-32-cancel1" title="<?php echo JText::_('Cancel'); ?>"></span><?php echo JText::_('Cancel'); ?></a>
					</td>
					<?php if ($task != 'add') {	?>
					<td class="divider"></td>
					<td>
						<a href="#" onclick="javascript: submitbutton('editSaveMoved'); submitbutton(); window.top.setTimeout('SqueezeBox.close();', 1000);" class="toolbar"><span class="icon-32-save1" title="<?php echo JText::_('Save & Moved'); ?>"></span><?php echo JText::_('Save & Moved'); ?></a>
					</td>
					<?php }	?>
				</tr>
			</table>
		</fieldset>
	</div>
	<div>
	<fieldset class="adminform" style="background: #ffffff;">
	<?php }
	echo $pane->startPane('pane');
	echo $pane->startPanel(JText::_('ACESEF_COMMON_URL'), 'url');
	?>
		<table class="admintable">
			<tr>
				<td width="23%" class="key2">
					<label for="name">
						<?php echo JText::_('ACESEF_URL_SEF_COMMON_URL_SEF'); ?>
					</label>
				</td>
				<td width="77%">
					<input class="inputbox" type="text" name="url_sef" size="80" value="<?php echo $this->row->url_sef; ?>" />
				</td>
			</tr>
			<tr>
				<td width="23%" class="key2">
					<label for="name">
						<?php echo JText::_('ACESEF_URL_SEF_COMMON_URL_REAL'); ?>
					</label>
				</td>
				<td width="77%">
					<input class="inputbox" type="text" id="url_real" name="url_real" size="80" value="<?php echo $this->row->url_real; ?>" />
				</td>
			</tr>
			<tr>
				<td width="23%" class="key2">
					<label for="name">
						<?php echo JText::_('State'); ?>
					</label>
				</td>
				<td width="77%">
					<br/>
                    <table class="noshow">
                        <tr>
                            <td>
                                <input type="checkbox" name="url_custom" value="1" <?php echo $this->url['custom'] ? 'checked="checked" ' : ''; ?> />
                                &nbsp;<?php echo JText::_('ACESEF_URL_EDIT_CUSTOM'); ?>
                            </td>
                            <td>
                                <input type="checkbox" name="url_published" value="1" <?php echo $this->url['published'] ? 'checked="checked" ' : ''; ?> />
                                &nbsp;<?php echo JText::_('Published'); ?>
                            </td>
                            <td>
                                <input type="checkbox" name="url_locked" value="1" <?php echo $this->url['locked'] ? 'checked="checked" ' : ''; ?> />
                                &nbsp;<?php echo JText::_('ACESEF_URL_SEF_COMMON_LOCKED'); ?>
                            </td>
                            <td>
                                <input type="checkbox" name="url_blocked" value="1" <?php echo $this->url['blocked'] ? 'checked="checked" ' : ''; ?> />
                                &nbsp;<?php echo JText::_('ACESEF_URL_SEF_COMMON_BLOCKED'); ?>
                            </td>
                            <td>
                                <input type="checkbox" name="url_cached" value="1" <?php echo $this->url['cached'] ? 'checked="checked" ' : ''; ?> />
                                &nbsp;<?php echo JText::_('ACESEF_COMMON_CACHED'); ?>
                            </td>
                        </tr>
                    </table>
					<br/>
				</td>
			</tr>
			<tr>
				<td width="23%" class="key2">
					<label for="name">
						<?php echo JText::_('ACESEF_URL_EDIT_ALIAS'); ?>
					</label>
				</td>
				<td width="77%">
					<textarea name="url_alias" rows="8" cols="57" class="text_area"><?php echo $this->url['alias']; ?></textarea>
				</td>
			</tr>
			<tr>
				<td width="23%" class="key2">
					<label for="name">
						<?php echo JText::_('ACESEF_URL_EDIT_NOTES'); ?>
					</label>
				</td>
				<td width="77%">
					<textarea name="url_notes" rows="3" cols="57" class="text_area"><?php echo AcesefUtility::replaceSpecialChars($this->url['notes'], true); ?></textarea>
				</td>
			</tr>
		</table>
		<?php
		echo $pane->endPanel();
		echo $pane->startPanel(JText::_('ACESEF_COMMON_METADATA'), 'meta');
		?>
		<table class="admintable">
			<tr>
				<td width="23%" class="key2">
					<label for="name">
						<?php echo JText::_('Published'); ?>
					</label>
				</td>
				<td width="77%">
					<input type="checkbox" name="meta_published" value="1" <?php echo $this->metadata->published ? 'checked="checked" ' : ''; ?> />
				</td>
			</tr>
			<tr>
				<td width="23%" class="key2">
					<label for="name">
						<?php echo JText::_('ACESEF_COMMON_TITLE'); ?>
					</label>
				</td>
				<td width="77%">
					<input class="inputbox" type="text" name="meta_title" size="80" value="<?php echo AcesefUtility::replaceSpecialChars(htmlspecialchars($this->metadata->title), true); ?>" />
					<input type="hidden" name="meta_id" value="<?php echo $this->metadata->id; ?>">
				</td>
			</tr>
			<tr>
				<td width="23%" class="key2">
					<label for="name">
						<?php echo JText::_('ACESEF_COMMON_META') . ' ' . JText::_('ACESEF_COMMON_DESCRIPTION'); ?>
					</label>
				</td>
				<td width="77%">
					<textarea name="meta_desc" rows="5" cols="57" class="text_area"><?php echo AcesefUtility::replaceSpecialChars(htmlspecialchars($this->metadata->description), true); ?></textarea>
				</td>
			</tr>
			<tr>
				<td width="23%" class="key2">
					<label for="name">
						<?php echo JText::_('ACESEF_COMMON_META') . ' ' . JText::_('ACESEF_COMMON_KEYWORDS'); ?>
					</label>
				</td>
				<td width="77%">
					<textarea name="meta_key" rows="3" cols="57" class="text_area"><?php echo AcesefUtility::replaceSpecialChars(htmlspecialchars($this->metadata->keywords), true); ?></textarea>
				</td>
			</tr>
			<tr>
				<td width="23%" class="key2">
					<label for="name">
						<?php echo JText::_('ACESEF_COMMON_META') . ' ' . JText::_('ACESEF_URL_EDIT_METALANG'); ?>
					</label>
				</td>
				<td width="77%">
					<input class="inputbox" type="text" name="meta_lang" size="30" value="<?php echo AcesefUtility::replaceSpecialChars(htmlspecialchars($this->metadata->lang), true); ?>" />
				</td>
			</tr>
			<tr>
				<td width="23%" class="key2">
					<label for="name">
						<?php echo JText::_('ACESEF_COMMON_META') . ' ' . JText::_('ACESEF_URL_EDIT_METAROBOTS'); ?>
					</label>
				</td>
				<td width="77%">
					<input class="inputbox" type="text" name="meta_robots" size="30" value="<?php echo AcesefUtility::replaceSpecialChars(htmlspecialchars($this->metadata->robots), true); ?>" />
				</td>
			</tr>
			<tr>
				<td width="23%" class="key2">
					<label for="name">
						<?php echo JText::_('ACESEF_COMMON_META') . ' ' . JText::_('ACESEF_URL_EDIT_METAGOOGLEBOT'); ?>
					</label>
				</td>
				<td width="77%">
					<input class="inputbox" type="text" name="meta_googlebot" size="30" value="<?php echo AcesefUtility::replaceSpecialChars(htmlspecialchars($this->metadata->googlebot), true); ?>" />
				</td>
			</tr>
			<tr>
				<td width="23%" class="key2">
					<label for="name">
						<?php echo JText::_('ACESEF_COMMON_META') . ' ' . JText::_('ACESEF_URL_EDIT_METACANONICAL'); ?>
					</label>
				</td>
				<td width="77%">
					<input class="inputbox" type="text" name="meta_canonical" size="80" value="<?php echo AcesefUtility::replaceSpecialChars(htmlspecialchars($this->metadata->canonical), true); ?>" />
				</td>
			</tr>
		</table>
		<?php
		echo $pane->endPanel();
		echo $pane->startPanel(JText::_('ACESEF_COMMON_SITEMAP'), 'sitemap');
		?>
		<table class="admintable">
			<tr>
				<td width="23%" class="key2">
					<label for="name">
						<?php echo JText::_('Published'); ?>
					</label>
				</td>
				<td width="77%">
					<input type="checkbox" name="sm_published" value="1" <?php echo $this->sitemap->published ? 'checked="checked" ' : ''; ?> />
				</td>
			</tr>
			<tr>
				<td width="23%" class="key2">
					<label for="name">
						<?php echo JText::_('ACESEF_SITEMAP_DATE'); ?>
					</label>
				</td>
				<td width="77%">
					<?php echo JHTML::calendar($this->sitemap->sdate, 'sm_date', 'sm_date', '%Y-%m-%d', 'size="13"'); ?>
					<input type="hidden" name="sm_id" value="<?php echo $this->sitemap->id; ?>">
				</td>
			</tr>
			<tr>
				<td width="23%" class="key2">
					<label for="name">
						<?php echo JText::_('ACESEF_SITEMAP_FREQUENCY'); ?>
					</label>
				</td>
				<td width="77%">
					<?php echo JHTML::_('select.genericlist', AcesefSitemap::getFrequencyList(), 'sm_freq', 'class="inputbox" size="1 "','value', 'text', $this->sitemap->frequency); ?>
				</td>
			</tr>
			<tr>
				<td width="23%" class="key2">
					<label for="name">
						<?php echo JText::_('ACESEF_SITEMAP_PRIORITY'); ?>
					</label>
				</td>
				<td width="77%">
					<?php echo JHTML::_('select.genericlist', AcesefSitemap::getPriorityList(), 'sm_priority', 'class="inputbox" size="1 "','value', 'text', $this->sitemap->priority); ?>
				</td>
			</tr>
		</table>
		<?php
		echo $pane->endPanel();
		echo $pane->startPanel(JText::_('ACESEF_COMMON_TAGS'), 'tags');
		?>
		<table class="admintable">
			<tr>
				<td width="23%" class="key2">
					<label for="name">
						<?php echo JText::_('Enable'); ?>
					</label>
				</td>
				<td width="77%">
					<input type="checkbox" name="url_tags" value="1" <?php echo $this->url['tags'] ? 'checked="checked" ' : ''; ?> />
				</td>
			</tr>
		</table>
		<?php
		echo $pane->endPanel();
		echo $pane->startPanel(JText::_('ACESEF_COMMON_ILINKS'), 'ilinks');
		?>
		<table class="admintable">
			<tr>
				<td width="23%" class="key2">
					<label for="name">
						<?php echo JText::_('Enable'); ?>
					</label>
				</td>
				<td width="77%">
					<input type="checkbox" name="url_ilinks" value="1" <?php echo $this->url['ilinks'] ? 'checked="checked" ' : ''; ?> />
				</td>
			</tr>
		</table>
		<?php
		echo $pane->endPanel();
		echo $pane->startPanel(JText::_('ACESEF_COMMON_BOOKMARKS'), 'bookmarks');
		?>
		<table class="admintable">
			<tr>
				<td width="23%" class="key2">
					<label for="name">
						<?php echo JText::_('Enable'); ?>
					</label>
				</td>
				<td width="77%">
					<input type="checkbox" name="url_bookmarks" value="1" <?php echo $this->url['bookmarks'] ? 'checked="checked" ' : ''; ?> />
				</td>
			</tr>
		</table>
		<?php
		echo $pane->endPanel();
		echo $pane->startPanel(JText::_('ACESEF_URL_EDIT_TABS_SOURCE'), 'source');
		
		if ($this->row->source) {
			$source = explode('--b1--', AcesefUtility::replaceSpecialChars($this->row->source, true));
			
			for ($i=0; $i < 3; $i++) {
				$no = $i+1;
				$line = explode('--b2--', $source[$i]);
				?>
				<fieldset class="adminform">
				<legend><?php echo JText::_('ACESEF_URL_EDIT_LEGEND_SOURCE').' '.$no; ?></legend>
					<table class="admintable">
						<tr>
							<td width="23%" class="key2">
								<label for="name">
									<?php echo JText::_('ACESEF_URL_EDIT_SOURCE_FUNCTION'); ?>
								</label>
							</td>
							<td width="77%">
								<?php echo $line[0]; ?>
							</td>
						</tr>
						<tr>
							<td width="23%" class="key2">
								<label for="name">
									<?php echo JText::_('ACESEF_URL_EDIT_SOURCE_FILE'); ?>
								</label>
							</td>
							<td width="77%">
								<?php echo $line[1]; ?>
							</td>
						</tr>
						<tr>
							<td width="23%" class="key2">
								<label for="name">
									<?php echo JText::_('ACESEF_URL_EDIT_SOURCE_LINE'); ?>
								</label>
							</td>
							<td width="77%">
								<?php echo $line[2]; ?>
							</td>
						</tr>
					</table>
				</fieldset>
			<?php
			}
		}
		echo $pane->endPanel();
		echo $pane->endPane();
	
	if ($tmpl == 'component') { ?>
	</fieldset>
	</div>
	<?php } ?>
	<input type="hidden" name="option" value="com_acesef" />
	<input type="hidden" name="controller" value="sefurls" />
	<input type="hidden" name="task" value="edit" />
	<input type="hidden" name="modal" value="0" />
	<input type="hidden" name="url_old" value="<?php echo $this->row->url_sef; ?>" />
	<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="url_cdate" value="<?php echo $this->row->cdate; ?>" />
	<input type="hidden" name="url_mdate" value="<?php echo $this->row->mdate; ?>" />
	<input type="hidden" name="url_trashed" value="<?php echo $this->url['trashed']; ?>" />
	<input type="hidden" name="url_notfound" value="<?php echo $this->url['notfound']; ?>" />
	<?php echo JHTML::_('form.token'); ?>
</form>