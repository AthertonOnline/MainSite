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
?>
<META HTTP-EQUIV="REFRESH" CONTENT="15">
<p><?php echo JText::_("COM_SPTRANSFER_REFRESH"); ?></p>
<?php
$log = @file_get_contents(JPATH_BASE."/components/com_sptransfer/log.htm");
echo $log;
?>

