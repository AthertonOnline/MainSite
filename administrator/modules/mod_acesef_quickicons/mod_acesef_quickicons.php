<?php
/**
* @version		1.6.0
* @package		AceSEF
* @subpackage	Quick Icons
* @copyright	2009-2011 JoomAce LLC, www.joomace.net
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// No Permission
defined('_JEXEC') or die('Restricted access');

function acesefIcon($link, $image, $text) {
	$mainframe =& JFactory::getApplication();
	$img_path 	= '/components/com_acesef/assets/images/';
	$lang		=& JFactory::getLanguage();
	?>
	<div class="icon-wrapper" style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
		<div class="icon">
			<a href="<?php echo $link; ?>">
				<?php echo JHtml::_('image.site', $image, $img_path, NULL, NULL, $text); ?>
				<span><?php echo $text; ?></span>
			</a>
		</div>
	</div>
	<?php
}

function getVersionState() {
	$factory_file = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_acesef'.DS.'library'.DS.'factory.php';
	$utility_file = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_acesef'.DS.'library'.DS.'utility.php';
	
	if (!file_exists($factory_file) || !file_exists($utility_file)) {
		return 0;
	}
	
	require_once($factory_file);
	require_once($utility_file);
	$utility = new AcesefUtility();
	
	$installed = $utility->getXmlText(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_acesef'.DS.'acesef.xml', 'version');
	$version_info = $utility->getRemoteInfo();
	$latest = $version_info['acesef'];
	
	$version = version_compare($installed, $latest);
	
	return $version;
}

?>

<div id="cpanel">
	<?php
	// AceSEF icons
	if ($params->get('acesef_version', '1') == 1) {
		$link = 'index.php?option=com_acesef&amp;controller=upgrade&amp;task=view';
		$version = getVersionState();
		if ($version != 0) {
			acesefIcon($link, 'icon-48-version-up.png', JText::_('Upgrade Available'));
		} else {
			acesefIcon($link, 'icon-48-version-ok.png', JText::_('Up-to-date'));
		}
	}

	if ($params->get('acesef_configuration', '0') == 1) {
		$link = 'index.php?option=com_acesef&amp;controller=config&amp;task=edit';
		acesefIcon($link, 'icon-48-config.png', JText::_('Configuration'));
	}

	if ($params->get('acesef_extensions', '0') == 1) {
		$link = 'index.php?option=com_acesef&amp;controller=extensions&amp;task=view';
		acesefIcon($link, 'icon-48-extensions.png', JText::_('Extensions'));
	}

	if ($params->get('acesef_urls', '1') == 1) {
		$link = 'index.php?option=com_acesef&amp;controller=sefurls&amp;task=view';
		acesefIcon($link, 'icon-48-urls.png', JText::_('URLs'));
	}

	if ($params->get('acesef_metadata', '1') == 1) {
		$link = 'index.php?option=com_acesef&amp;controller=metadata&amp;task=view';
		acesefIcon($link, 'icon-48-metadata.png', JText::_('Metadata'));
	}

	if ($params->get('acesef_sitemap', '1') == 1) {
		$link = 'index.php?option=com_acesef&amp;controller=sitemap&amp;task=view';
		acesefIcon($link, 'icon-48-sitemap.png', JText::_('Sitemap'));
	}

	if ($params->get('acesef_tags', '1') == 1) {
		$link = 'index.php?option=com_acesef&amp;controller=tags&amp;task=view';
		acesefIcon($link, 'icon-48-tags.png', JText::_('Tags'));
	}

	if ($params->get('acesef_ilinks', '0') == 1) {
		$link = 'index.php?option=com_acesef&amp;controller=ilinks&amp;task=view';
		acesefIcon($link, 'icon-48-ilinks.png', JText::_('Internal Links'));
	}

	if ($params->get('acesef_bookmarks', '0') == 1) {
		$link = 'index.php?option=com_acesef&amp;controller=bookmarks&amp;task=view';
		acesefIcon($link, 'icon-48-bookmarks.png', JText::_('Social Bookmarks'));
	}
	?>
</div>