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
	function generateURLs() {
		document.location.href = 'index.php?option=com_acesef&controller=sefurls&task=generateurls';
	}
</script>
 
<div>
	<center>
	<br /><br /><br />
	<h1><?php echo JText::_('ACESEF_URL_SEF_GENERATING_URLS'); ?></h1>
	<br />
	<img onLoad="javascript: generateURLs();" src="components/com_acesef/assets/images/loading.gif" />
	<br /><br /><br />
	<?php echo JText::_('ACESEF_URL_SEF_GENERATING_URLS_MSG'); ?>
	<br />
	</center>
</div>