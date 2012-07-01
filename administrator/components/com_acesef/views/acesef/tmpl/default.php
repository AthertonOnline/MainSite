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
	function upgrade() {
		document.adminForm.controller.value = 'upgrade';
		document.adminForm.submit();
	}

	function sefStatus(type, value) {
		document.adminForm.sefStatusType.value = type;
		document.adminForm.sefStatusValue.value = value;
		submitbutton('sefStatus');
	}
</script>

<form action="index.php?option=com_acesef" method="post" name="adminForm">
	<table cellspacing="0" cellpadding="0" border="0" width="100%">
		<tr>
			<th>		
				<?php
					$status = AcesefUtility::getSefStatus();
					
					if (!$status['sef']) {
						JError::raiseNotice('100', JText::sprintf('ACESEF_CPANEL_STATUS_NOTE_SEF', '<a href="index.php?option=com_config">', '</a>'));
					}
					
					if ($status['sef'] && !$status['mod_rewrite']) {
						JError::raiseWarning('100', JText::sprintf('ACESEF_CPANEL_STATUS_NOTE_MOD', '<a href="index.php?option=com_config">', '</a>'));
					}
					
					if ($status['sef'] && !$status['htaccess']) {
						JError::raiseWarning('100', JText::sprintf('ACESEF_CPANEL_STATUS_HTA_NO'));
					}
					
					if (!$status['htaccess'] || !$status['mod_rewrite'] || !$status['sef']) {
						JError::raiseNotice('100', JText::sprintf('ACESEF_CPANEL_STATUS_NOTE_REQUIREMENTS', '<a href="http://www.joomace.net/support/docs/acesef/installation-upgrading/requirements" target="_blank">', '</a>'));
					}
					
					if (AcesefUtility::JoomFishInstalled()) {
						$jf_id = AceDatabase::loadResult("SELECT extension_id FROM #__extensions WHERE element = 'jfrouter' AND folder = 'system' AND enabled = '1'");
						if (!is_null($jf_id)) {
							JError::raiseNotice('100', JText::sprintf('ACESEF_CPANEL_STATUS_NOTE_JFROUTER', '<a href="index.php?option=com_plugins&view=plugin&client=site&task=edit&cid[]='.$jf_id.'">', '</a>'));
						}
					}

					if ($this->AcesefConfig->multilang == 1) {
						$lf_id = AceDatabase::loadResult("SELECT extension_id FROM #__extensions WHERE element = 'languagefilter' AND folder = 'system' AND enabled = '1'");
						if (!is_null($lf_id)) {
							JError::raiseNotice('100', JText::sprintf('ACESEF_CPANEL_STATUS_NOTE_LANGUAGEFILTER', '<a href="index.php?option=com_plugins&task=plugin.edit&extension_id='.$lf_id.'">', '</a>'));
						}
					}
					
					if(strlen($this->AcesefConfig->download_id) != 32 && (ACESEF_PACK == 'plus' || ACESEF_PACK == 'pro')){
						JError::raiseWarning('100', JText::sprintf('ACESEF_CPANEL_STATUS_NOTE_DOWNLOAD_ID', '<a href="index.php?option=com_acesef&controller=config&task=edit">', '</a>'));
					}
				?>	
			</th>
		</tr>
		<tr>
			<td valign="top" width="58%">
				<table>
					<tr>
						<td>
							<div id="cpanel" width="50%">
							<?php
							$option = JRequest::getString('option');
							
							if ($this->AcesefConfig->ui_cpanel == 1) {
								$link = 'index.php?option='.$option.'&amp;controller=config&amp;task=edit';
								$this->acesefButton($link, 'icon-48-config.png', JText::_('ACESEF_COMMON_CONFIGURATION'));

								$link = 'index.php?option='.$option.'&amp;controller=extensions&amp;task=view';
								$this->acesefButton($link, 'icon-48-extensions.png', JText::_('ACESEF_COMMON_EXTENSIONS'));
								
								$link = 'index.php?option='.$option.'&amp;controller=purgeupdate&amp;task=view';
								$this->acesefButton($link, 'icon-48-purgeupdate.png', JText::_('ACESEF_COMMON_PURGEUPDATE'), true, 470, 320);
								
								$link = 'index.php?option='.$option.'&amp;controller=restoremigrate&amp;task=view';
                                $this->acesefButton($link, 'icon-48-restoremigrate.png', JText::_('ACESEF_COMMON_RESTOREMIGRATE'));
                                
                                $link = 'index.php?option='.$option.'&amp;controller=upgrade&amp;task=view';
                                $this->acesefButton($link, 'icon-48-upgrade.png', JText::_('ACESEF_COMMON_UPGRADE'));
								?>
								<br /><br /><br /><br /><br /><br /><br /><br /><br />
								<?php
								$link = 'index.php?option='.$option.'&amp;controller=sefurls&amp;task=view&amp;type=sef';
                                $this->acesefButton($link, 'icon-48-urlssef.png', JText::_('ACESEF_COMMON_URLS_SEF'));
                                
                                $link = 'index.php?option='.$option.'&amp;controller=sefurls&amp;task=view&amp;type=locked';
                                $this->acesefButton($link, 'icon-48-urlslocked.png', JText::_('ACESEF_COMMON_URLS_LOCKED'));
                                 
                                $link = 'index.php?option='.$option.'&amp;controller=sefurls&amp;task=view&amp;type=custom';
                                $this->acesefButton($link, 'icon-48-urlscustom.png', JText::_('ACESEF_COMMON_URLS_CUSTOM'));
                                
                                $link = 'index.php?option='.$option.'&amp;controller=sefurls&amp;task=view&amp;type=notfound';
                                $this->acesefButton($link, 'icon-48-urls404.png', JText::_('ACESEF_COMMON_URLS_404'));
                                
                                $link = 'index.php?option='.$option.'&amp;controller=movedurls&amp;task=view';
                                $this->acesefButton($link, 'icon-48-urlsmoved.png', JText::_('ACESEF_COMMON_URLS_MOVED'));
								?>
								<br /><br /><br /><br /><br /><br /><br /><br /><br />
								<?php
								$link = 'index.php?option='.$option.'&amp;controller=metadata&amp;task=view';
								$this->acesefButton($link, 'icon-48-metadata.png', JText::_('ACESEF_COMMON_METADATA'));
								
								$link = 'index.php?option='.$option.'&amp;controller=sitemap&amp;task=view';
								$this->acesefButton($link, 'icon-48-sitemap.png', JText::_('ACESEF_COMMON_SITEMAP'));
								
								$link = 'index.php?option='.$option.'&amp;controller=tags&amp;task=view';
								$this->acesefButton($link, 'icon-48-tags.png', JText::_('ACESEF_COMMON_TAGS'));
								
								$link = 'index.php?option='.$option.'&amp;controller=ilinks&amp;task=view';
								$this->acesefButton($link, 'icon-48-ilinks.png', JText::_('ACESEF_COMMON_ILINKS'));
								
								$link = 'index.php?option='.$option.'&amp;controller=bookmarks&amp;task=view';
								$this->acesefButton($link, 'icon-48-bookmarks.png', JText::_('ACESEF_COMMON_BOOKMARKS'));
								?>
								<br /><br /><br /><br /><br /><br /><br /><br /><br />
								<?php
								$link = 'index.php?option='.$option.'&amp;controller=support&amp;task=support';
								$this->acesefButton($link, 'icon-48-support.png', JText::_('ACESEF_COMMON_SUPPORT'), true, 650, 420);
								
								$link = 'index.php?option='.$option.'&amp;controller=support&amp;task=translators';
								$this->acesefButton($link, 'icon-48-translators.png', JText::_('ACESEF_CPANEL_TRANSLATORS'), true);
								
								$link = 'http://www.joomace.net/joomla-extensions/acesef/changelog?tmpl=component';
								$this->acesefButton($link, 'icon-48-changelog.png', JText::_('ACESEF_CPANEL_CHANGELOG'), true);
							} else {
								$link = 'index.php?option='.$option.'&amp;controller=config&amp;task=edit';
								$this->acesefButton($link, 'icon-48-config.png', JText::_('ACESEF_COMMON_CONFIGURATION'));

								$link = 'index.php?option='.$option.'&amp;controller=extensions&amp;task=view';
								$this->acesefButton($link, 'icon-48-extensions.png', JText::_('ACESEF_COMMON_EXTENSIONS'));
								
								$link = 'index.php?option='.$option.'&amp;controller=purgeupdate&amp;task=view';
								$this->acesefButton($link, 'icon-48-purgeupdate.png', JText::_('ACESEF_COMMON_PURGEUPDATE'), true, 470, 320);
								
								$link = 'index.php?option='.$option.'&amp;controller=restoremigrate&amp;task=view';
								$this->acesefButton($link, 'icon-48-restoremigrate.png', JText::_('ACESEF_COMMON_RESTOREMIGRATE'));
							
								$link = 'index.php?option='.$option.'&amp;controller=upgrade&amp;task=view';
								$this->acesefButton($link, 'icon-48-upgrade.png', JText::_('ACESEF_COMMON_UPGRADE'));
								?>
								<br /><br /><br /><br /><br /><br /><br /><br /><br />
								<?php
								$link = 'index.php?option='.$option.'&amp;controller=sefurls&amp;task=view';
								$this->acesefButton($link, 'icon-48-urls.png', JText::_('ACESEF_COMMON_URLS'));
								
								$link = 'index.php?option='.$option.'&amp;controller=metadata&amp;task=view';
								$this->acesefButton($link, 'icon-48-metadata.png', JText::_('ACESEF_COMMON_METADATA'));
								
								$link = 'index.php?option='.$option.'&amp;controller=sitemap&amp;task=view';
								$this->acesefButton($link, 'icon-48-sitemap.png', JText::_('ACESEF_COMMON_SITEMAP'));
								
								$link = 'index.php?option='.$option.'&amp;controller=tags&amp;task=view';
								$this->acesefButton($link, 'icon-48-tags.png', JText::_('ACESEF_COMMON_TAGS'));
								
								$link = 'index.php?option='.$option.'&amp;controller=ilinks&amp;task=view';
								$this->acesefButton($link, 'icon-48-ilinks.png', JText::_('ACESEF_COMMON_ILINKS'));
								
								?>
								<br /><br /><br /><br /><br /><br /><br /><br /><br />
								<?php
							
								$link = 'index.php?option='.$option.'&amp;controller=support&amp;task=support';
								$this->acesefButton($link, 'icon-48-support.png', JText::_('ACESEF_COMMON_SUPPORT'), true, 650, 420);
								
								$link = 'index.php?option='.$option.'&amp;controller=support&amp;task=translators';
								$this->acesefButton($link, 'icon-48-translators.png', JText::_('ACESEF_CPANEL_TRANSLATORS'), true);
								
								$link = 'http://www.joomace.net/joomla-extensions/acesef/changelog?tmpl=component';
								$this->acesefButton($link, 'icon-48-changelog.png', JText::_('ACESEF_CPANEL_CHANGELOG'), true);
							}
							?>
							</div>
						</td>
					</tr>
				</table>
			</td>
			<td valign="top" width="42%" style="padding: 7px 0 0 5px">
				<?php
				echo $this->pane->startPane('stat-pane');
				echo $this->pane->startPanel(JText::_('ACESEF_CPANEL_WELLCOME'), 'wellcome');
				?>
				<table class="adminlist">
					<tr>
						<td valign="top" width="50%" align="center">
							<table class="adminlist">
								<?php
									$rowspan = 5;
									$download_id = ((ACESEF_PACK == 'plus' || ACESEF_PACK == 'pro') && strlen($this->AcesefConfig->download_id) != 32); 
									if ($download_id) {
										$rowspan = 6;
									}
								?>
								<tr height="70">
									<td width="%25">
										<?php
											if ($this->info['version_enabled'] == 0) {
												echo JHTML::_('image', 'administrator/templates/bluestork/images/header/icon-48-info.png', null);
											} elseif ($this->info['version_status'] == 0) {
												echo JHTML::_('image', 'administrator/templates/bluestork/images/header/icon-48-checkin.png', null);
											} elseif($this->info['version_status'] == -1) {
												echo JHTML::_('image', 'administrator/templates/bluestork/images/header/icon-48-help_header.png', null);
											} else {
												echo JHTML::_('image', 'administrator/templates/bluestork/images/header/icon-48-help_header.png', null);
											}
										?>
									</td>
									<td width="%35">
										<?php
											if ($this->info['version_enabled'] == 0) {
												echo '<b>'.JText::_('ACESEF_CPANEL_VERSION_CHECKER_DISABLED').'</b>';
											} elseif ($this->info['version_status'] == 0) {
												echo '<b><font color="green">'.JText::_('ACESEF_CPANEL_LATEST_VERSION_INSTALLED').'</font></b>';
											} elseif($this->info['version_status'] == -1) {
												echo '<b><font color="red">'.JText::_('ACESEF_CPANEL_OLD_VERSION').'</font></b>';
											} else {
												echo '<b><font color="orange">'.JText::_('ACESEF_CPANEL_NEWER_VERSION').'</font></b>';
											}
										?>
									</td>
									<td align="center" rowspan="<?php echo $rowspan; ?>">
										<a href="http://www.joomace.net/joomla-extensions/acesef">
										<img src="components/com_acesef/assets/images/logo.png" width="149" height="222" alt="AceSEF" title="AceSEF" align="middle" border="0">
										</a>
									</td>
								</tr>
								<?php if ($download_id) { ?>
								<tr height="40">
									<td>
										<?php echo '<b><font color="red">'.JText::_('ACESEF_CONFIG_MAIN_UPGRADE_ID').'</font></b>';?>
									</td>
									<td>
										<input type="text" name="download_id" id="download_id" class="inputbox" size="18" />
										&nbsp;
										<input type="button" onclick="javascript: submitbutton('saveDownloadID')" value="<?php echo JText::_('Save'); ?>" />
									</td>
								</tr>
								<?php } ?>
								<tr height="40">
									<td>
										<?php
											if ($this->info['version_status'] == 0 || $this->info['version_enabled'] == 0) {
												echo JText::_('ACESEF_CPANEL_LATEST_VERSION');
											} elseif ($this->info['version_status'] == -1) {
												echo '<b><font color="red">'.JText::_('ACESEF_CPANEL_LATEST_VERSION').'</font></b>';
											} else {
												echo '<b><font color="orange">'.JText::_('ACESEF_CPANEL_LATEST_VERSION').'</font></b>';
											}
										?>
									</td>
									<td>
										<?php
											if ($this->info['version_enabled'] == 0) {
												echo JText::_('Disabled');
											} elseif ($this->info['version_status'] == 0) {
												echo $this->info['version_latest'];
											} elseif ($this->info['version_status'] == -1) {
                                                echo '<b><font color="red">'.$this->info['version_latest'].'</font></b>&nbsp;';
                                                echo '<input type="button" class="button hasTip" value="'.JText::_('ACESEF_COMMON_UPGRADE').'" onclick="upgrade();" />';
											} else {
												echo '<b><font color="orange">'.$this->info['version_latest'].'</font></b>';
											}
											$this->showStatus('version_checker');
										?>
									</td>
								</tr>
								<tr height="40">
									<td>
										<?php echo JText::_('ACESEF_CPANEL_INSTALLED_VERSION'); ?>
									</td>
									<td>
										<?php 
											if ($this->info['version_enabled'] == 0) {
												echo JText::_('Disabled');
											} else {
												echo $this->info['version_installed'];
											}
										?>
									</td>
								</tr>
								<tr height="40">
									<td>
										<?php echo JText::_('ACESEF_CPANEL_COPYRIGHT'); ?>
									</td>
									<td>
										<a href="http://www.joomace.net" target="_blank"><?php echo AcesefUtility::getXmlText(JPATH_ACESEF_ADMIN.'/acesef.xml', 'copyright'); ?></a>
									</td>
								</tr>
								<tr height="40">
									<td>
										<?php echo JText::_('ACESEF_CPANEL_LICENSE'); ?>
									</td>
									<td>
										<?php 
											$license = AcesefUtility::getXmlText(JPATH_ACESEF_ADMIN . '/acesef.xml', 'license');
											list($l_name, $l_link) = explode(', ', $license);
										?>
										<a href="http://www.joomace.net/company/license-policy" target="_blank"><?php echo $l_name; ?></a>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
				<?php
				echo $this->pane->endPanel();
				echo $this->pane->startPanel(JText::_('ACESEF_CPANEL_STATUS'), 'status');
				?>
				<table class="adminlist">
					<thead>
						<tr>
							<td class="title">
								<strong><?php echo JText::_('ACESEF_CPANEL_STATUS_SERVER'); ?></strong>
							</td>
						</tr>
					</thead>
					<tr>
						<td width="30%">
							<?php echo JText::_('ACESEF_CPANEL_STATUS_PHP'); ?>
						</td>
						<td width="70%">
							<?php $this->showStatus('php'); ?>
						</td>
					</tr>
					<?php if ($status['s_mod_rewrite'] != '') { ?>
					<tr>
						<td>
							<?php echo JText::_('ACESEF_CPANEL_STATUS_SERVER_MOD'); ?>
						</td>
						<td>
							<?php $this->showStatus('s_mod_rewrite'); ?>
						</td>
					</tr>
					<?php } ?>
					<tr>
						<td>
							<?php echo JText::_('ACESEF_CPANEL_STATUS_HTA'); ?>
						</td>
						<td>
							<?php $this->showStatus('htaccess'); ?>
						</td>
					</tr>
					<thead>
						<tr>
							<td class="title">
								<strong><?php echo JText::_('AceSEF'); ?></strong>
							</td>
						</tr>
					</thead>
					<tr>
						<td>
							<?php echo JText::_('AceSEF'); ?>
						</td>
						<td>
							<?php $this->showStatus('acesef'); ?>
						</td>
					</tr>
					<tr>
						<td>
							<?php echo JText::_('ACESEF_CPANEL_STATUS_PLUGIN'); ?>
						</td>
						<td>
							<?php $this->showStatus('plugin'); ?>
						</td>
					</tr>
					<tr>
						<td>
							<?php echo JText::_('ACESEF_CPANEL_STATUS_GENERATE_SEF'); ?>
						</td>
						<td>
							<?php $this->showStatus('generate_sef'); ?>
						</td>
					</tr>
					<thead>
						<tr>
							<td class="title">
								<strong><?php echo JText::_('Joomla!'); ?></strong>
							</td>
						</tr>
					</thead>
					<tr>
						<td>
							<?php echo JText::_('ACESEF_CPANEL_STATUS_SEF'); ?>
						</td>
						<td>
							<?php $this->showStatus('sef'); ?>
						</td>
					</tr>
					<tr>
						<td>
							<?php echo JText::_('ACESEF_CPANEL_STATUS_MOD_REWRITE'); ?>
						</td>
						<td>
							<?php $this->showStatus('mod_rewrite'); ?>
						</td>
					</tr>
					<?php if (AcesefUtility::JoomFishInstalled()) {	?>
					<tr>
						<td>
							<?php echo JText::_('ACESEF_CPANEL_STATUS_JOOMFISH'); ?>
						</td>
						<td>
							<?php $this->showStatus('jfrouter'); ?>
						</td>
					</tr>
					<?php }	?>
					<?php if ($this->AcesefConfig->multilang == 1) {	?>
					<tr>
						<td>
							<?php echo JText::_('ACESEF_CPANEL_STATUS_LANGUAGEFILTER'); ?>
						</td>
						<td>
							<?php $this->showStatus('languagefilter'); ?>
						</td>
					</tr>
					<?php }	?>
					<tr>
						<td>
							<?php echo JText::_('ACESEF_CPANEL_STATUS_LIVE_SITE'); ?>
						</td>
						<td>
							<?php $this->showStatus('live_site'); ?>
						</td>
					</tr>
				</table>
				<?php
				echo $this->pane->endPanel();
				echo $this->pane->startPanel(JText::_('ACESEF_COMMON_EXTENSIONS'), 'extensions');
				?>
				<table class="adminlist">
					<thead>
						<tr>
							<th>
								<?php echo JText::_('ACESEF_EXTENSIONS_VIEW_COMPONENT'); ?>
							</th>
							<th align="center">
								<?php echo JText::_('ACESEF_EXTENSIONS_VIEW_ROUTER'); ?>
							</th>
							<th align="center">
								<?php echo JText::_('ACESEF_EXTENSIONS_VIEW_PREFIX'); ?>
							</th>
							<th align="center">
								<?php echo JText::_('ACESEF_EXTENSIONS_VIEW_SKIP'); ?>
							</th>
						</tr>
					</thead>
					<tbody>
					<?php
					$k = 0;
					for ($i=0, $n=count($this->extensions); $i < $n; $i++) {
						$row = &$this->extensions[$i];
						
						$params = new JParameter($row->params);
						
						$router = JText::_('ACESEF_EXTENSIONS_VIEW_SELECT_DISABLE');
						switch ($params->get('router')) {
							case '1':
								$router = JText::_('ACESEF_EXTENSIONS_VIEW_SELECT_ACESEF');
								break;
							case '2':
								$router = JText::_('ACESEF_EXTENSIONS_VIEW_SELECT_15_ROUTER');
								break;
							case '3':
								$router = JText::_('ACESEF_EXTENSIONS_VIEW_SELECT_EXTENSION');
								break;
						}
						
						$skip_menu = JText::_('No');
						if ($params->get('skip_menu') == 1) {
							$skip_menu = JText::_('Yes');
						}

						?>
						<tr class="<?php echo "row$k"; ?>">
							<td>
								<?php
								$link = JRoute::_('index.php?option=com_acesef&controller=extensions&task=edit&cid[]='.$row->id.'&amp;tmpl=component');
								?>
								<a href="<?php echo $link; ?>" style="cursor:pointer" class="modal" rel="{handler: 'iframe', size: {x: 650, y: 500}}">
									<?php echo $row->name; ?>
								</a>
							</td>
							<td align="center">
								<?php echo $router; ?>
							</td>
							<td align="center">
								<?php echo $params->get('prefix'); ?>
							</td>
							<td align="center">
								<?php echo $skip_menu; ?>
							</td>
						</tr>
						<?php
						$k = 1 - $k;
					}
					?>
					</tbody>
				</table>
				<?php
				echo $this->pane->endPanel();
				echo $this->pane->startPanel(JText::_('ACESEF_CPANEL_URLS_STATS'), 'stats');
				?>
				<table class="adminlist">
					<tr>
						<td width="25%">
							<a href="index.php?option=com_acesef&controller=sefurls&task=view" ><?php echo JText::_('ACESEF_COMMON_URLS_SEF'); ?></a>
						</td>
						<td width="75%">
							<b><?php echo $this->info['urls_sef']; ?></b>
						</td>
					</tr>
					<tr>
						<td>
							<a href="index.php?option=com_acesef&controller=movedurls&task=view" ><?php echo JText::_('ACESEF_COMMON_URLS_MOVED'); ?></a>
						</td>
						<td>
							<b><?php echo $this->info['urls_moved']; ?></b>
						</td>
					</tr>
					<tr>
						<td>
							<a href="index.php?option=com_acesef&controller=metadata&task=view" ><?php echo JText::_('ACESEF_COMMON_METADATA'); ?></a>
						</td>
						<td>
							<b><?php echo $this->info['metadata']; ?></b>
						</td>
					</tr>
					<tr>
						<td>
							<a href="index.php?option=com_acesef&controller=sitemap&task=view" ><?php echo JText::_('ACESEF_COMMON_SITEMAP'); ?></a>
						</td>
						<td>
							<b><?php echo $this->info['sitemap']; ?></b>
						</td>
					</tr>
					<tr>
						<td>
							<a href="index.php?option=com_acesef&controller=tags&task=view" ><?php echo JText::_('ACESEF_COMMON_TAGS'); ?></a>
						</td>
						<td>
							<b><?php echo $this->info['tags']; ?></b>
						</td>
					</tr>
					<tr>
						<td>
							<a href="index.php?option=com_acesef&controller=ilinks&task=view" ><?php echo JText::_('ACESEF_COMMON_ILINKS'); ?></a>
						</td>
						<td>
							<b><?php echo $this->info['ilinks']; ?></b>
						</td>
					</tr>
					<tr>
						<td>
							<a href="index.php?option=com_acesef&controller=bookmarks&task=view" ><?php echo JText::_('ACESEF_COMMON_BOOKMARKS'); ?></a>
						</td>
						<td>
							<b><?php echo $this->info['bookmarks']; ?></b>
						</td>
					</tr>
				</table>
				<?php
				echo $this->pane->endPanel();
				echo $this->pane->endPane();
				?>
			</td>
		</tr>
	</table>
	
	<input type="hidden" name="option" value="com_acesef" />
	<input type="hidden" name="controller" value="acesef" />
	<input type="hidden" name="task" value="view" />
	<input type="hidden" name="sefStatusType" value="" />
	<input type="hidden" name="sefStatusValue" value="" />
	<?php echo JHTML::_('form.token'); ?>
</form>