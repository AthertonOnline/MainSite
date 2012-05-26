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
defined('_JEXEC') or die('Restricted Access');
//JHtml::_('behavior.modal');
?>
<?php foreach($this->items as $i => $item): ?>
	<tr class="row<?php echo $i % 2; ?>">
		<td>
			<?php echo $item->id; ?>
		</td>
		<td>
			<?php echo JHtml::_('grid.id', $i, $item->id); ?>
		</td>	
                <td class="left">
                    <?php echo JText::_($item->extension_name); ?>
                    <?php echo ' -> ';?>
                    <?php echo JText::_($item->extension_name.'_'.$item->name); ?>
		</td>
                <td class="left">
                    <?php echo JText::_($item->extension_name.'_'.$item->name.'_desc'); ?>
		</td>
                <td class="center">
                    <input type="text" name="input_ids[]" id="input_ids" value="" class="inputbox" size="45" aria-invalid="false">
                </td>
	</tr>
<?php endforeach; ?>

