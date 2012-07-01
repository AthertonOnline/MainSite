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

<script language="javascript">
	function apply() {
		var selection = document.getElementById('movedurls_selection').value;
		var action = document.getElementById('movedurls_action').value;
		
		if (action == 'sep') {
			return;
		}
		
		if (selection == 'selected' && document.adminForm.boxchecked.value == 0) {
			alert('Please make a selection from the list');
			return;
		}
		
		// If delete, show warning
		if (action == 'delete') {
			if (!confirm('<?php echo JText::_("ACESEF_TOOLBAR_CONFIRM_DELETE"); ?>')) {
				return;
			}
		}
		
		// Call the action
		document.adminForm.selection.value = selection;
		submitbutton(action);
	}
	
	function resetFilters() {
		document.adminForm.search_new.value = '';
		document.adminForm.search_old.value = '';
		document.adminForm.filter_published.value = '-1';
		document.adminForm.filter_hit_val.value = '0';
		document.adminForm.search_hit.value = '';
		document.adminForm.filter_date.value = '';
		document.adminForm.search_id.value = '';
		
		document.adminForm.submit();
	}

	function changeType(type) {
		window.location = 'index.php?option=com_acesef&controller=sefurls&task=view&type='+type;
	}
</script>

<form action="index.php?option=com_acesef&amp;controller=movedurls&amp;task=view" method="post" name="adminForm" id="adminForm">
	<dl class="tabs">
	    <dt onclick="javascript: changeType('sef');" class="<?php echo ($this->type == 'sef') ? 'open' : 'closed'; ?>" style="cursor: pointer"><b><?php echo JText::_('ACESEF_COMMON_URLS_SEF'); ?></b></dt>
	    <dt onclick="javascript: changeType('custom');" class="<?php echo ($this->type == 'custom') ? 'open' : 'closed'; ?>" style="cursor: pointer"><b><?php echo JText::_('ACESEF_COMMON_URLS_CUSTOM'); ?></b></dt>
	    <dt onclick="javascript: changeType('notfound');" class="<?php echo ($this->type == 'notfound') ? 'open' : 'closed'; ?>" style="cursor: pointer"><b><?php echo JText::_('ACESEF_COMMON_URLS_404'); ?></b></dt>
	    <dt onclick="javascript: changeType('moved');" class="<?php echo ($this->type == 'moved') ? 'open' : 'closed'; ?>" style="cursor: pointer"><b><?php echo JText::_('ACESEF_COMMON_URLS_MOVED'); ?></b></dt>
	    <dt onclick="javascript: changeType('locked');" class="<?php echo ($this->type == 'locked') ? 'open' : 'closed'; ?>" style="cursor: pointer"><b><?php echo JText::_('ACESEF_COMMON_URLS_LOCKED'); ?></b></dt>
	    <dt onclick="javascript: changeType('blocked');" class="<?php echo ($this->type == 'blocked') ? 'open' : 'closed'; ?>" style="cursor: pointer"><b><?php echo JText::_('ACESEF_COMMON_URLS_BLOCKED'); ?></b></dt>
	    <dt onclick="javascript: changeType('duplicated');" class="<?php echo ($this->type == 'duplicated') ? 'open' : 'closed'; ?>" style="cursor: pointer"><b><?php echo JText::_('ACESEF_COMMON_URLS_DUPLICATED'); ?></b></dt>
	    <dt onclick="javascript: changeType('red');" class="<?php echo ($this->type == 'red') ? 'open' : 'closed'; ?>" style="cursor: pointer"><b><?php echo JText::_('ACESEF_COMMON_URLS_RED'); ?></b></dt>
		<dt>|</dt>
	    <dt onclick="javascript: changeType('quickedit');" class="<?php echo ($this->type == 'quickedit') ? 'open' : 'closed'; ?>" style="cursor: pointer"><font color="green"><b><?php echo JText::_('ACESEF_URL_SEF_QUICK_EDIT'); ?></b></font></dt>
	    <dt onclick="javascript: changeType('trashed');" class="<?php echo ($this->type == 'trashed') ? 'open' : 'closed'; ?>" style="cursor: pointer"><font color="red"><b><?php echo JText::_('ACESEF_URL_SEF_TRASH'); ?></b></font></dt>
	</dl>
	<div class="current" style="background-color:#ffffff;">
		<table class="adminlist">
			<thead>
				<tr>
					<th width="13px">
						<?php echo JText::_('ACESEF_COMMON_NUM'); ?>
					</th>
					<th width="20px">
						<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>);" />
					</th>
					<th nowrap="nowrap">
						<?php echo JHTML::_('grid.sort', JText::_('ACESEF_URL_MOVED_NEW'), 'url_new', $this->lists['order_dir'], $this->lists['order']); ?>
					</th>
					<th nowrap="nowrap">
						<?php echo JHTML::_('grid.sort', JText::_('ACESEF_URL_MOVED_OLD'), 'url_old', $this->lists['order_dir'], $this->lists['order']); ?>
					</th>
					<?php if ($this->AcesefConfig->ui_moved_published == 1) { ?>
					<th width="50px" nowrap="nowrap">
						<?php echo JHTML::_('grid.sort', JText::_('Published'), 'published', $this->lists['order_dir'], $this->lists['order']); ?>
					</th>
					<?php }	?>
					<?php if ($this->AcesefConfig->ui_moved_hits == 1) { ?>
					<th width="80px" nowrap="nowrap">
						<?php echo JHTML::_('grid.sort', JText::_('Hits'), 'hits', $this->lists['order_dir'], $this->lists['order']); ?>
					</th>
					<?php }	?>
					<?php if ($this->AcesefConfig->ui_moved_clicked == 1) { ?>
					<th width="120px" nowrap="nowrap">
						<?php echo JHTML::_('grid.sort', JText::_('ACESEF_URL_MOVED_LAST_HIT'), 'last_hit', $this->lists['order_dir'], $this->lists['order']); ?>
					</th>
					<?php }	?>
					<?php if ($this->AcesefConfig->ui_moved_cached == 1) { ?>
					<th width="50" nowrap="nowrap">
						<?php echo JText::_('ACESEF_COMMON_CACHED'); ?>
					</th>
					<?php }	?>
					<?php if ($this->AcesefConfig->ui_moved_id == 1) { ?>
					<th width="30px" nowrap="nowrap">
						<?php echo JHTML::_('grid.sort', 'ID', 'id', $this->lists['order_dir'], $this->lists['order']); ?>
					</th>
					<?php }	?>
				</tr>
				<tr>
					<th nowrap="nowrap" colspan="2">
						<?php echo $this->lists['reset_filters']; ?>
					</th>
					<th nowrap="nowrap">
						<?php echo $this->lists['search_new']; ?>
					</th>
					<th nowrap="nowrap">
						<?php echo $this->lists['search_old']; ?>
					</th>
					<?php if ($this->AcesefConfig->ui_moved_published == 1) { ?>
					<th nowrap="nowrap">
						<?php echo $this->lists['published_list']; ?>
					</th>
					<?php }	?>
					<?php if ($this->AcesefConfig->ui_moved_hits == 1) { ?>
					<th nowrap="nowrap">
						<?php echo $this->lists['hit_val']; ?>
						<?php echo $this->lists['search_hit']; ?>
					</th>
					<?php }	?>
					<?php if ($this->AcesefConfig->ui_moved_clicked == 1) { ?>
					<th nowrap="nowrap">
						<?php echo $this->lists['filter_date']; ?>
					</th>
					<?php }	?>
					<?php if ($this->AcesefConfig->ui_moved_cached == 1) { ?>
					<th nowrap="nowrap">
						&nbsp;
					</th>
					<?php }	?>
					<?php if ($this->AcesefConfig->ui_moved_id == 1) { ?>
					<th nowrap="nowrap">
						<?php echo $this->lists['search_id']; ?>
					</th>
					<?php }	?>
				</tr>
			</thead>
			<tbody>
			<?php
			$k = 0;
			for ($i=0, $n=count($this->items); $i < $n; $i++) {
				$row = &$this->items[$i];
				$checked = JHTML::_('grid.id', $i, $row->id);
				
				// Published icon
				$published_icon = $this->getIcon($i, $row->published ? 'unpublish' : 'publish', $row->published ? 'icon-16-published-on.png' : 'icon-16-published-off.png');
	
				if (preg_match("/^(https?|ftps?|itpc|telnet|gopher):\/\//i", $row->url_new)) {
					$preview_link = $row->url_new;
				} else {
					$preview_link = '../'.$row->url_new;
				}
				
				// Cache icon
				if ($this->AcesefConfig->ui_moved_cached == 1) {
					$cached = false;
					if (isset($this->cache[$row->url_old])) {
						$cached = true;
					}
					$cached_icon = $this->getIcon($i, $cached ? 'uncache' : 'cache', $cached ? 'icon-16-cache-on.png' : 'icon-16-cache-off.png');
				}
				
				$edit_link = JRoute::_('index.php?option=com_acesef&controller=movedurls&task=edit&cid[]='.$row->id.'&amp;tmpl=component');
				
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td>
						<?php echo $this->pagination->getRowOffset($i); ?>
					</td>
					<td>
						<?php echo $checked; ?>
					</td>
					<td>
						<a href="<?php echo $preview_link; ?>" title="<?php echo JText::_('ACESEF_URL_SEF_TOOLTIP_SEF_URL'); ?>" target="_blank">
						<?php echo substr($row->url_new, 0, 173); ?></a>
					</td>
					<td>
						<a href="<?php echo $edit_link; ?>" style="cursor:pointer" class="modal" rel="{handler: 'iframe', size: {x: 600, y: 350}}">
							<?php echo htmlentities(substr($row->url_old, 0, 173)); ?>
						</a>
					</td>
					<?php if ($this->AcesefConfig->ui_moved_published == 1) { ?>
					<td align="center">
						<?php echo $published_icon;?>
					</td>
					<?php }	?>
					<?php if ($this->AcesefConfig->ui_moved_hits == 1) { ?>
					<td align="center">
						<?php echo $row->hits;?>
					</td>
					<?php }	?>
					<?php if ($this->AcesefConfig->ui_moved_clicked == 1) { ?>
					<td align="center">
						<?php echo (substr($row->last_hit, 0, 10) == '0000-00-00' ? JText::_('Never') : $row->last_hit); ?>
					</td>
					<?php }	?>
					<?php if ($this->AcesefConfig->ui_moved_cached == 1) { ?>
					<td align="center">
						<?php echo $cached_icon;?>
					</td>
					<?php }	?>
					<?php if ($this->AcesefConfig->ui_moved_id == 1) { ?>
					<td align="center">
						<?php echo $row->id;?>
					</td>
					<?php }	?>
				</tr>
				<?php
				$k = 1 - $k;
			}
			?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="<?php echo $this->colspan;?>">
						<?php echo $this->pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
		</table>
	</div>

	<input type="hidden" name="option" value="com_acesef" />
	<input type="hidden" name="controller" value="movedurls" />
	<input type="hidden" name="task" value="view" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_dir']; ?>" />
	<input type="hidden" name="selection" value="selected" />
	<?php echo JHTML::_('form.token'); ?>
</form>