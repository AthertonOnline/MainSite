<?php
/**
* @version		1.5.0
* @package		AceSEF
* @subpackage	Uninstaller
* @copyright	2009-2010 JoomAce LLC, www.joomace.net
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * 
 * This is the special installer addon based on the one created by Andrew Eddie and the team of JXtended.
 * We thank for this cool idea of extending the installation process easily
 */

// No Permission
defined('_JEXEC') or die('Restricted Access');

// Import Libraries
jimport('joomla.application.helper');
jimport('joomla.filesystem.file');
jimport('joomla.installer.installer');

$db = &JFactory::getDBO();

$status = new JObject();
$status->adapters = array();
$status->extensions = array();
$status->modules = array();
$status->plugins = array();

/***********************************************************************************************
 * ---------------------------------------------------------------------------------------------
 * DATABASE BACKUP SECTION
 * ---------------------------------------------------------------------------------------------
 ***********************************************************************************************/
/*$db->setQuery("DELETE FROM `#__menu` WHERE link LIKE '%com_acesef%'");
$db->query();*/

$db->setQuery("DROP TABLE IF EXISTS `#__acesef_bookmarks_backup`");
$db->query();
$db->setQuery("RENAME TABLE `#__acesef_bookmarks` TO `#__acesef_bookmarks_backup`");
$db->query();

$db->setQuery("DROP TABLE IF EXISTS `#__acesef_ilinks_backup`");
$db->query();
$db->setQuery("RENAME TABLE `#__acesef_ilinks` TO `#__acesef_ilinks_backup`");
$db->query();

$db->setQuery("DROP TABLE IF EXISTS `#__acesef_metadata_backup`");
$db->query();
$db->setQuery("RENAME TABLE `#__acesef_metadata` TO `#__acesef_metadata_backup`");
$db->query();

$db->setQuery("DROP TABLE IF EXISTS `#__acesef_sitemap_backup`");
$db->query();
$db->setQuery("RENAME TABLE `#__acesef_sitemap` TO `#__acesef_sitemap_backup`");
$db->query();

$db->setQuery("DROP TABLE IF EXISTS `#__acesef_tags_backup`");
$db->query();
$db->setQuery("RENAME TABLE `#__acesef_tags` TO `#__acesef_tags_backup`");
$db->query();

$db->setQuery("DROP TABLE IF EXISTS `#__acesef_tags_map_backup`");
$db->query();
$db->setQuery("RENAME TABLE `#__acesef_tags_map` TO `#__acesef_tags_map_backup`");
$db->query();

$db->setQuery("DROP TABLE IF EXISTS `#__acesef_urls_backup`");
$db->query();
$db->setQuery("RENAME TABLE `#__acesef_urls` TO `#__acesef_urls_backup`");
$db->query();

$db->setQuery("DROP TABLE IF EXISTS `#__acesef_urls_moved_backup`");
$db->query();
$db->setQuery("RENAME TABLE `#__acesef_urls_moved` TO `#__acesef_urls_moved_backup`");
$db->query();

$db->setQuery("DROP TABLE IF EXISTS `#__acesef_extensions_backup`");
$db->query();
$db->setQuery("RENAME TABLE `#__acesef_extensions` TO `#__acesef_extensions_backup`");
$db->query();
 
/***********************************************************************************************
* ---------------------------------------------------------------------------------------------
* ADAPTER REMOVAL SECTION
* ---------------------------------------------------------------------------------------------
***********************************************************************************************/
$adapter = JPATH_LIBRARIES.DS.'joomla'.DS.'installer'.DS.'adapters'.DS.'acesef_ext.php';
if (JFile::exists($adapter)) {
	JFile::delete($adapter);
	$status->adapters[] = 1;
}

/***********************************************************************************************
* ---------------------------------------------------------------------------------------------
* EXTENSION REMOVAL SECTION
* ---------------------------------------------------------------------------------------------
***********************************************************************************************/
$db =& JFactory::getDBO();
$db->setQuery("SELECT name FROM #__acesef_extensions_backup WHERE name != ''");
$extensions = $db->loadResultArray();

if (!empty($extensions)) {
	foreach ($extensions as $extension) {
		$status->extensions[] = array('name' => $extension);
	}
}

/***********************************************************************************************
 * ---------------------------------------------------------------------------------------------
 * MODULE REMOVAL SECTION
 * ---------------------------------------------------------------------------------------------
 ***********************************************************************************************/
$db->setQuery("SELECT extension_id FROM #__extensions WHERE type = 'module' AND element = 'mod_acesef_quickicons' LIMIT 1");
$id = $db->loadResult();
if ($id) {
	$installer = new JInstaller();
	$installer->uninstall('module', $id);
	$status->modules[] = array('name' => 'AceSEF - Quick Icons', 'client' => 'Administrator');
}

/***********************************************************************************************
 * ---------------------------------------------------------------------------------------------
 * PLUGIN REMOVAL SECTION
 * ---------------------------------------------------------------------------------------------
 ***********************************************************************************************/
$db->setQuery("SELECT extension_id FROM #__extensions WHERE type = 'plugin' AND element = 'acesef' LIMIT 1");
$id = $db->loadResult();
if ($id) {
	$installer = new JInstaller();
	$installer->uninstall('plugin', $id);
	$status->plugins[] = array('name' => 'AceSEF', 'group' => 'System');
}

$db->setQuery("SELECT extension_id FROM #__extensions WHERE type = 'plugin' AND element = 'acesefmetacontent' LIMIT 1");
$id = $db->loadResult();
if ($id) {
	$installer = new JInstaller();
	$installer->uninstall('plugin', $id);
	$status->plugins[] = array('name' => 'AceSEF Metadata (Content)', 'group' => 'System');
}

/***********************************************************************************************
 * ---------------------------------------------------------------------------------------------
 * OUTPUT TO SCREEN
 * ---------------------------------------------------------------------------------------------
 ***********************************************************************************************/

$rows = 0;
?>

<h2>AceSEF Removal</h2>
<table class="adminlist">
	<thead>
		<tr>
			<th class="title" colspan="2"><?php echo JText::_('Extension'); ?></th>
			<th width="30%"><?php echo JText::_('Status'); ?></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="3"></td>
		</tr>
	</tfoot>
	<tbody>
		<tr>
			<th colspan="3"><?php echo JText::_('Core'); ?></th>
		</tr>
		<tr class="row0">
			<td class="key" colspan="2"><?php echo 'AceSEF '.JText::_('Component'); ?></td>
			<td><strong><?php echo JText::_('Removed'); ?></strong></td>
		</tr>
	<?php
if (count($status->adapters)) : ?>
		<tr class="row1">
			<td class="key" colspan="2"><?php echo 'AceSEF Adapter'; ?></td>
			<td><strong><?php echo JText::_('Removed'); ?></strong></td>
		</tr>
	<?php
endif;
if (count($status->extensions)) : ?>
		<tr>
			<th colspan="3"><?php echo JText::_('AceSEF Extension'); ?></th>
		</tr>
	<?php foreach ($status->extensions as $extension) : ?>
		<tr class="row<?php echo (++ $rows % 2); ?>">
			<td class="key" colspan="2"><?php echo $extension['name']; ?></td>
			<td><strong><?php echo JText::_('Removed'); ?></strong></td>
		</tr>
	<?php endforeach;
endif;
if (count($status->modules)) : ?>
		<tr>
			<th><?php echo JText::_('Module'); ?></th>
			<th colspan="2"><?php echo JText::_('Client'); ?></th>
		</tr>
	<?php foreach ($status->modules as $module) : ?>
		<tr class="row<?php echo (++ $rows % 2); ?>">
			<td class="key"><?php echo $module['name']; ?></td>
			<td class="key"><?php echo ucfirst($module['client']); ?></td>
			<td><strong><?php echo JText::_('Removed'); ?></strong></td>
		</tr>
	<?php endforeach;
endif;
if (count($status->plugins)) : ?>
		<tr>
			<th><?php echo JText::_('Plugin'); ?></th>
			<th colspan="2"><?php echo JText::_('Group'); ?></th>
		</tr>
	<?php foreach ($status->plugins as $plugin) : ?>
		<tr class="row<?php echo (++ $rows % 2); ?>">
			<td class="key"><?php echo ucfirst($plugin['name']); ?></td>
			<td class="key"><?php echo ucfirst($plugin['group']); ?></td>
			<td><strong><?php echo JText::_('Removed'); ?></strong></td>
		</tr>
	<?php endforeach;
endif;
?>
	</tbody>
</table>