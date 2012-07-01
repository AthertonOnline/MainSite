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

// Get metadata
$mainframe =& JFactory::getApplication();
$row = $mainframe->getUserState('com_acesef.metadata');

?>

<script type="text/javascript">
	window.addEvent('load', function(){
		$$('.pane-sliders').each(
			function(slider) {
				$$('#acesefpanel-container').getFirst().inject(slider);
				$$('#acesefpanel-container').erase();
			}
		);
		
		/*var sliders = new Element('div', {class: 'pane-sliders'});
		var acesefpanel = new Element('div', {id: 'acesefpanel-container'});
		
		acesefpanel.getFirst().inject(sliders);*/
	});
</script>

<div id="acesefpanel-container">
	<div class="panel">
		<h3 class="pane-toggler title" id="acesef-pane">
			<a href="javascript:void(0);">
				<span><?php echo JText::_('AceSEF') . ' ' . JText::_('ACESEF_COMMON_METADATA'); ?></span>
			</a>
		</h3>

		<div class="pane-slider content" style="padding-top: 0px; border-top: medium none; padding-bottom: 0px; border-bottom: medium none; overflow: hidden; height: auto;">
			<table class="admintable">
				<tr>
					<td width="25%" class="key">
						<label for="name">
							<?php echo JText::_('ACESEF_COMMON_TITLE'); ?>
						</label>
					</td>
					<td width="75%">
						<input class="inputbox" type="text" name="acesef_title" size="45" value="<?php echo AcesefUtility::replaceSpecialChars(htmlspecialchars($row->title), true); ?>" />
					</td>
				</tr>
				<tr>
					<td width="25%" class="key">
						<label for="name">
							<?php echo JText::_('ACESEF_COMMON_DESCRIPTION'); ?>
						</label>
					</td>
					<td width="75%">
						<textarea name="acesef_desc" rows="6" cols="30" class="text_area"><?php echo AcesefUtility::replaceSpecialChars(htmlspecialchars($row->description), true); ?></textarea>
					</td>
				</tr>
				<tr>
					<td width="25%" class="key">
						<label for="name">
							<?php echo JText::_('ACESEF_COMMON_KEYWORDS'); ?>
						</label>
					</td>
					<td width="75%">
						<textarea name="acesef_key" rows="5" cols="30" class="text_area"><?php echo AcesefUtility::replaceSpecialChars(htmlspecialchars($row->keywords), true); ?></textarea>
						</td>
				</tr>
				<tr>
					<td width="25%" class="key">
						<label for="name">
							<?php echo JText::_('ACESEF_URL_EDIT_METALANG'); ?>
						</label>
					</td>
					<td width="75%">
						<input class="inputbox" type="text" name="acesef_lang" size="20" value="<?php echo AcesefUtility::replaceSpecialChars(htmlspecialchars($row->lang), true); ?>" />
					</td>
				</tr>
				<tr>
					<td width="25%" class="key">
						<label for="name">
							<?php echo JText::_('ACESEF_URL_EDIT_METAROBOTS'); ?>
						</label>
					</td>
					<td width="75%">
						<input class="inputbox" type="text" name="acesef_robots" size="20" value="<?php echo AcesefUtility::replaceSpecialChars(htmlspecialchars($row->robots), true); ?>" />
					</td>
				</tr>
				<tr>
					<td width="25%" class="key">
						<label for="name">
							<?php echo JText::_('ACESEF_URL_EDIT_METAGOOGLEBOT'); ?>
						</label>
					</td>
					<td width="75%">
						<input class="inputbox" type="text" name="acesef_googlebot" size="20" value="<?php echo AcesefUtility::replaceSpecialChars(htmlspecialchars($row->googlebot), true); ?>" />
					</td>
				</tr>
			</table>
			<input type="hidden" name="acesef_id" value="<?php echo $row->id; ?>">
			<input type="hidden" name="acesef_url_sef" value="<?php echo $row->url_sef; ?>">
		</div>
	</div>
</div>