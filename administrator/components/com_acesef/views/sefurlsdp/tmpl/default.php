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
		var selection = document.getElementById('sefurlsdp_selection').value;
		var action = document.getElementById('sefurlsdp_action').value;
		
		if (action == 'sep') {
			return;
		}
		
		if (selection == 'selected' && document.adminForm.boxchecked.value == 0) {
			alert('<?php echo JText::_('Please make a selection from the list'); ?>');
			return;
		}
		
		// If delete, show warning
		if (action == 'delete') {
			if (!confirm('<?php echo JText::_('ACESEF_TOOLBAR_CONFIRM_DELETE'); ?>')) {
				return;
			}
		}
		
		// Call the action
		document.adminForm.selection.value = selection;
		submitbutton(action);
	}
</script>

<form action="index.php?option=com_acesef&amp;controller=sefurlsdp&amp;task=view&amp;id=<?php echo JRequest::getVar('id');?>&amp;tmpl=component" method="post" name="adminForm" id="adminForm">
	<table class="adminlist">
		<tbody>
			<tr>
				<td align="left">
					<h3><?php echo substr($this->sef, 0, 173); ?></h3>
				</td>
				<td width="220px" align="right">
					<?php echo $this->toolbar->action; ?>
					<?php echo $this->toolbar->selection; ?>
					<?php echo $this->toolbar->button; ?>
				</td>
			</tr>
		</tbody>
	</table>
	<table class="adminlist" cellspacing="1">
		<thead>
			<tr>
				<th width="13">
					<?php echo JText::_('ACESEF_COMMON_NUM'); ?>
				</th>
				<th width="20">
					<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>);" />
				</th>
				<th nowrap="nowrap">
					<?php echo JHTML::_('grid.sort', JText::_('ACESEF_URL_SEF_COMMON_URL_REAL'), 'url_real', $this->lists['order_dir'], $this->lists['order']); ?>
				</th>
				<th width="50" nowrap="nowrap">
					<?php echo JText::_('ACESEF_URL_SEF_COMMON_USED'); ?>
				</th>
				<th width="30" nowrap="nowrap">
					<?php echo JHTML::_('grid.sort', JText::_('Hits'), 'hits', $this->lists['order_dir'], $this->lists['order']); ?>
				</th>
			</tr>
			<tr>
				<th nowrap="nowrap" colspan="2">
					<?php echo $this->lists['reset_filters']; ?>
				</th>
				<th nowrap="nowrap">
					<?php echo $this->lists['search_real']; ?>
					<?php echo $this->lists['component_list']; ?>
					<?php 
						if (AcesefUtility::JoomFishInstalled() && $this->AcesefConfig->ui_language == 1) {
							echo $this->lists['lang_list'];
						}
					?>
				</th>
				<th nowrap="nowrap">
					<?php echo $this->lists['used_list']; ?>
				</th>
				<th nowrap="nowrap">
					<?php echo $this->lists['search_hits']; ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="7">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php
		$k = 0;
		for ($i=0, $n=count($this->items); $i < $n; $i++) {
			$row = &$this->items[$i];
			
			$checked = JHTML::_('grid.id', $i, $row->id);
			
			// Load parameters
			$params = new JParameter($row->params);
			
			// Images folder
			$images		= 'components/com_acesef/assets/images/';
			
			// Used icon
			if ($row->used == 2) {
				$task_used = '';
			} else {
				$task_used = 'used';
			}
			if ($row->used == 2) {
				$img_used = 'icon-16-used-on.png';
			} elseif ($row->used == 1) {
				$img_used = 'icon-16-used-on2.png';
			} else {
				$img_used = 'icon-16-used-off.png';
			}
			
			if ($row->used != 2) {
				$used_icon = $this->getIcon($i, $task_used, $img_used);
			} else {
				$used_icon = '<img src="components/com_acesef/assets/images/'.$img_used.'" border="0" />';
			}
			
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<?php echo $this->pagination->getRowOffset($i); ?>
				</td>
				<td>
					<?php echo $checked; ?>
				</td>
				<td>
					<?php echo htmlentities(substr($row->url_real, 0, 173)); ?>
				</td>
				<td align="center">
					<?php echo $used_icon;?>
				</td>
				<td>
					<?php echo $row->hits; ?>
				</td>
			</tr>
			<?php
			$k = 1 - $k;
		}
		?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="com_acesef" />
	<input type="hidden" name="controller" value="sefurlsdp" />
	<input type="hidden" name="task" value="view" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_dir']; ?>" />
	<input type="hidden" name="selection" value="selected" />
	<?php echo JHTML::_('form.token'); ?>
</form>