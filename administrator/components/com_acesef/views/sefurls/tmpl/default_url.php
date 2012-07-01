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

<form action="index.php?option=com_acesef&amp;controller=sefurls&amp;task=url&amp;tmpl=component&amp;id=id" method="post" name="adminForm" id="adminForm">
	<table class="adminlist" cellspacing="1">
		<thead>
			<tr>
				<th width="13">
					<?php echo JText::_('ACESEF_COMMON_NUM'); ?>
				</th>
				<th nowrap="nowrap">
					<?php echo JHTML::_('grid.sort', JText::_('ACESEF_URL_SEF_COMMON_URL_SEF'), 'url_sef', $this->lists['order_dir'], $this->lists['order']); ?>
				</th>
				<th nowrap="nowrap">
					<?php echo JHTML::_('grid.sort', JText::_('ACESEF_URL_SEF_COMMON_URL_REAL'), 'url_real', $this->lists['order_dir'], $this->lists['order']); ?>
				</th>
				<?php if ($this->AcesefConfig->ui_sef_published == 1) { ?>
				<th width="50" nowrap="nowrap">
					<?php echo JText::_('Published'); ?>
				</th>
				<?php }	?>
				<?php if ($this->AcesefConfig->ui_sef_used == 1) { ?>
				<th width="50" nowrap="nowrap">
					<?php echo JHTML::_('grid.sort', JText::_('ACESEF_URL_SEF_COMMON_USED'), 'used', $this->lists['order_dir'], $this->lists['order']); ?>
				</th>
				<?php }	?>
			</tr>
			<tr>
				<th nowrap="nowrap">
					&nbsp;
				</th>
				<th nowrap="nowrap">
					<?php echo $this->lists['search_sef']; ?>
				</th>
				<th nowrap="nowrap">
					<?php echo $this->lists['search_real']; ?>
					<?php echo $this->lists['component_list']; ?>
					<?php 
						if (AcesefUtility::JoomFishInstalled() && $this->AcesefConfig->ui_sef_language == 1) {
							echo $this->lists['lang_list'];
						}
					?>
				</th>
				<?php if ($this->AcesefConfig->ui_sef_published == 1) { ?>
				<th nowrap="nowrap">
					<?php echo $this->lists['published_list']; ?>
				</th>
				<?php }	?>
				<?php if ($this->AcesefConfig->ui_sef_used == 1) { ?>
				<th nowrap="nowrap">
					<?php echo $this->lists['used_list']; ?>
				</th>
				<?php }	?>
			</tr>
		</thead>
		<tbody>
		<?php
		$k = 0;
		for ($i=0, $n=count($this->items); $i < $n; $i++) {
			$row = &$this->items[$i];
			
			$url_sef = $row->url_sef;
			if(strlen($url_sef) >= 50) {
				$url_sef = substr($url_sef, 0, 50) . '...';
			}
			
			$url_real = $row->url_real;
			if(strlen($url_real) >= 70) {
				$url_real = substr($url_real, 0, 70) . '...';
			}
			
			// Load parameters
			$params = new JParameter($row->params);
			
			// Published icon
			if ($this->AcesefConfig->ui_sef_published == 1) {
				if ($this->type == 'trashed') {
					$img_published = $params->get('published', '0') ? 'icon-16-published-on.png' : 'icon-16-published-off.png';
					$published_icon = '<img src="components/com_acesef/assets/images/'.$img_published.'" border="0" />';
				} else {
					$published_icon = $this->getIcon($i, $params->get('published', '0') ? 'unpublish' : 'publish', $params->get('published', '0') ? 'icon-16-published-on.png' : 'icon-16-published-off.png');
				}
			}
			
			// Used icon
			if ($this->AcesefConfig->ui_sef_used == 1) {
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
				
				if ($this->type == 'trashed') {
					$used_icon = '<img src="components/com_acesef/assets/images/'.$img_used.'" border="0" />';
				} else {
					if ($row->used != 2) {
						$used_icon = $this->getIcon($i, $task_used, $img_used);
					} else {
						$used_icon = '<img src="components/com_acesef/assets/images/'.$img_used.'" border="0" />';
					}
				}
			}
			
			// Locked icon
			if ($this->AcesefConfig->ui_sef_locked == 1) {
				if ($this->type == 'trashed') {
					$img_locked = $params->get('locked', '0') ? 'icon-16-lock-on.png' : 'icon-16-lock-off.png';
					$locked_icon = '<img src="components/com_acesef/assets/images/'.$img_locked.'" border="0" />';
				} else {
					$locked_icon = $this->getIcon($i, $params->get('locked', '0') ? 'unlock' : 'lock', $params->get('locked', '0') ? 'icon-16-lock-on.png' : 'icon-16-lock-off.png');
				}
			}
			
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<?php echo $this->pagination->getRowOffset($i); ?>
				</td>
				<td>
					<a style="cursor: pointer;" onclick="window.parent.selectURL('<?php echo $row->id; ?>', '<?php echo str_replace(array("'", "\""), array("\\'", ""), $row->url_sef); ?>', '<?php echo JRequest::getVar('id'); ?>');">
						<?php echo htmlspecialchars($url_sef, ENT_QUOTES, 'UTF-8'); ?>
					</a>
				</td>
				<td>
					<a style="cursor: pointer;" onclick="window.parent.selectURL('<?php echo $row->id; ?>', '<?php echo str_replace(array("'", "\""), array("\\'", ""), $row->url_sef); ?>', '<?php echo JRequest::getVar('id'); ?>');">
						<?php echo htmlspecialchars($url_real, ENT_QUOTES, 'UTF-8'); ?>
					</a>
				</td>
				<?php if ($this->AcesefConfig->ui_sef_published == 1) { ?>
				<td align="center">
					<?php echo $published_icon;?>
				</td>
				<?php }	?>
				<?php if ($this->AcesefConfig->ui_sef_used == 1) { ?>
				<td align="center">
					<?php echo $used_icon;?>
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

	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_dir']; ?>" />
</form>