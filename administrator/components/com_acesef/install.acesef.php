<?php
/**
* @version		1.5.0
* @package		AceSEF
* @subpackage	Installer
* @copyright	2009-2010 JoomAce LLC, www.joomace.net
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
* 
* This is the special installer addon based on the one created by Andrew Eddie and the team of JXtended.
* We thank for this cool idea of extending the installation process easily
*/

// No Permission
defined('_JEXEC') or die('Restricted Access');

// Import Libraries
jimport('joomla.filesystem.file');
jimport('joomla.installer.installer');

$db =& JFactory::getDBO();

$status = new JObject();
$status->adapters = array();
$status->extensions = array();
$status->modules = array();
$status->plugins = array();

/***********************************************************************************************
* ---------------------------------------------------------------------------------------------
* ADAPTER INSTALLATION SECTION
* ---------------------------------------------------------------------------------------------
***********************************************************************************************/
$adp_src = JPATH_ADMINISTRATOR.'/components/com_acesef/adapters/acesef_ext.php';
$adp_dst = JPATH_LIBRARIES.'/joomla/installer/adapters/acesef_ext.php';
if (is_writable(dirname($adp_dst))) {
	JFile::copy($adp_src, $adp_dst);
	$status->adapters[] = 1;
}

/***********************************************************************************************
* ---------------------------------------------------------------------------------------------
* EXTENSION INSTALLATION SECTION
* ---------------------------------------------------------------------------------------------
***********************************************************************************************/
$extensions = array('com_acesef', 'com_banners', 'com_contact', 'com_content', 'com_mailto', 'com_newsfeeds', 'com_search', 'com_users', 'com_weblinks', 'com_wrapper');
foreach ($extensions as $extension) {
	$file = $this->parent->getPath('source').'/admin/extensions/'.$extension.'.xml';
	
	if (!file_exists($file)) {
		continue;
	}
	
	$manifest = JFactory::getXML($file);
	
	if (!$manifest) {
		continue;
	}
	
	$ename = (string)$manifest->name;

	$prm = array();
	$prm['router'] = 'router=3';
	
	$element = $manifest->install->defaultParams;
	if ($element && count($element->children())) {
		$defaultParams = $element->children();
		if (count($defaultParams) != 0) {
			foreach ($defaultParams as $param) {
				$name = (string)$param->attributes()->name;
				$value = (string)$param->attributes()->value;
				
				$prm[$name] = $name.'='.$value;
			}
		}
	}
	
	if (!isset($prm['skip_menu'])) {
		$prm['skip_menu'] = 'skip_menu=0';
	}
	
	if (!isset($prm['prefix'])) {
		$prm['prefix'] = 'prefix=';
	}
	
	$params = implode("\n", $prm);
	
	$db->setQuery("INSERT IGNORE INTO #__acesef_extensions (name, extension, params) VALUES ('{$ename}', '{$extension}', '{$params}')");
	$db->query();

	$status->extensions[] = array('name' => $ename);
}

/***********************************************************************************************
* ---------------------------------------------------------------------------------------------
* MODULE INSTALLATION SECTION
* ---------------------------------------------------------------------------------------------
***********************************************************************************************/
$installer = new JInstaller();
$installer->install($this->parent->getPath('source').'/modules/mod_acesef_quickicons');

$db->setQuery("UPDATE #__modules SET position = 'icon', ordering = '0', published = '1' WHERE module = 'mod_acesef_quickicons'");
$db->query();

$db->setQuery("SELECT `id` FROM `#__modules` WHERE `module` = 'mod_acesef_quickicons'");
$mod_id = $db->loadResult();

$db->setQuery("REPLACE INTO `#__modules_menu` (`moduleid`, `menuid`) VALUES ({$mod_id}, 0)");
$db->query();

$status->modules[] = array('name' => 'AceSEF - Quick Icons', 'client' => 'Administrator');


/***********************************************************************************************
* ---------------------------------------------------------------------------------------------
* PLUGIN INSTALLATION SECTION
* ---------------------------------------------------------------------------------------------
***********************************************************************************************/
$installer = new JInstaller();
$installer->install($this->parent->getPath('source').'/plugins/acesef');
$db->setQuery("UPDATE #__extensions SET enabled = 1 WHERE type = 'plugin' AND element = 'acesef' AND folder = 'system'");
$db->query();

$status->plugins[] = array('name' => 'AceSEF', 'group' => 'System');

$installer = new JInstaller();
$installer->install($this->parent->getPath('source').'/plugins/acesefmetacontent');
$db->setQuery("UPDATE #__extensions SET enabled = 1 WHERE type = 'plugin' AND element = 'acesefmetacontent' AND folder = 'system'");
$db->query();

$status->plugins[] = array('name' => 'AceSEF Metadata (Content)', 'group' => 'System');

/***********************************************************************************************
* ---------------------------------------------------------------------------------------------
* SITEMAP INSTALLATION SECTION
* ---------------------------------------------------------------------------------------------
***********************************************************************************************/
$sitemap = JPATH_ROOT.DS.'sitemap.xml';
if (!JFile::exists($sitemap)) {
	$content = '';
	JFile::write($sitemap, $content);
}

/***********************************************************************************************
* ---------------------------------------------------------------------------------------------
* OUTPUT TO SCREEN
* ---------------------------------------------------------------------------------------------
***********************************************************************************************/
$rows = 0;
?>
<img src="components/com_acesef/assets/images/logo.png" alt="Joomla! SEO Suite" width="60" height="89" align="left" />

<h2>AceSEF Installation</h2>
<h2><a href="index.php?option=com_acesef">Go to AceSEF Control Panel</a></h2>
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
			<td><strong><?php echo JText::_('Installed'); ?></strong></td>
		</tr>
	<?php
if (count($status->adapters)) : ?>
		<tr class="row1">
			<td class="key" colspan="2"><?php echo 'AceSEF Adapter'; ?></td>
			<td><strong><?php echo JText::_('Installed'); ?></strong></td>
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
			<td><strong><?php echo JText::_('Installed'); ?></strong></td>
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
			<td><strong><?php echo JText::_('Installed'); ?></strong></td>
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
			<td><strong><?php echo JText::_('Installed'); ?></strong></td>
		</tr>
	<?php endforeach;
endif;
 ?>

	</tbody>
</table>