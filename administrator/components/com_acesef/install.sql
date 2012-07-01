# -----------------------
# AceSEF SQL Installation
# -----------------------
DROP TABLE IF EXISTS `#__acesef_urls`;
CREATE TABLE IF NOT EXISTS `#__acesef_urls` (
  `id` INT(12) UNSIGNED NOT NULL AUTO_INCREMENT,
  `url_sef` VARCHAR(255) NOT NULL DEFAULT '',
  `url_real` VARCHAR(255) NOT NULL DEFAULT '',
  `cdate` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `mdate` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `used` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
  `hits` INT(12) UNSIGNED NOT NULL DEFAULT '0',
  `source` TEXT NOT NULL,
  `params` TEXT NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `url_real` (`url_real`),
  KEY `url_sef` (`url_sef`),
  KEY `used` (`used`)
) DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__acesef_urls_moved`;
CREATE TABLE IF NOT EXISTS `#__acesef_urls_moved` (
  `id` INT(12) UNSIGNED NOT NULL AUTO_INCREMENT,
  `url_new` VARCHAR(255) NOT NULL DEFAULT '',
  `url_old` VARCHAR(255) NOT NULL DEFAULT '',
  `published` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
  `hits` INT(12) UNSIGNED NOT NULL DEFAULT '0',
  `last_hit` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `url_old` (`url_old`),
  KEY `url_new` (`url_new`)
) DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__acesef_metadata`;
CREATE TABLE IF NOT EXISTS `#__acesef_metadata` (
  `id` INT(12) UNSIGNED NOT NULL AUTO_INCREMENT,
  `url_sef` VARCHAR(255) NOT NULL DEFAULT '',
  `published` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
  `title` VARCHAR(255) NOT NULL DEFAULT '',
  `description` VARCHAR(255) NOT NULL DEFAULT '',
  `keywords` VARCHAR(255) NOT NULL DEFAULT '',
  `lang` VARCHAR(30) NOT NULL DEFAULT '',
  `robots` VARCHAR(30) NOT NULL DEFAULT '',
  `googlebot` VARCHAR(30) NOT NULL DEFAULT '',
  `canonical` VARCHAR(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `url_sef` (`url_sef`)
) DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__acesef_sitemap`;
CREATE TABLE IF NOT EXISTS `#__acesef_sitemap` (
  `id` INT(12) UNSIGNED NOT NULL AUTO_INCREMENT,
  `url_sef` VARCHAR(255) NOT NULL DEFAULT '',
  `title` VARCHAR(255) NOT NULL DEFAULT '',
  `published` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
  `sdate` DATE NOT NULL DEFAULT '0000-00-00',
  `frequency` VARCHAR(30) NOT NULL DEFAULT '',
  `priority` VARCHAR(10) NOT NULL DEFAULT '',
  `sparent` INT(12) UNSIGNED NOT NULL DEFAULT '0',
  `sorder` INT(5) UNSIGNED NOT NULL DEFAULT '1000',
  PRIMARY KEY (`id`),
  UNIQUE KEY `url_sef` (`url_sef`)
) DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__acesef_tags`;
CREATE TABLE IF NOT EXISTS `#__acesef_tags` (
  `id` INT(12) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(150) NOT NULL DEFAULT '',
  `alias` VARCHAR(150) NOT NULL DEFAULT '',
  `description` VARCHAR(150) NOT NULL DEFAULT '',
  `published` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
  `ordering` INT(12) NOT NULL DEFAULT '0',
  `hits` INT(12) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `title` (`title`)
) DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__acesef_tags_map`;
CREATE TABLE IF NOT EXISTS `#__acesef_tags_map` (
  `url_sef` VARCHAR(255) NOT NULL DEFAULT '',
  `tag` VARCHAR(150) NOT NULL DEFAULT ''
) DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__acesef_ilinks`;
CREATE TABLE IF NOT EXISTS `#__acesef_ilinks` (
  `id` INT(12) UNSIGNED NOT NULL AUTO_INCREMENT,
  `word` VARCHAR(255) NOT NULL DEFAULT '',
  `link` VARCHAR(255) NOT NULL DEFAULT '',
  `published` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
  `nofollow` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
  `iblank` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
  `ilimit` VARCHAR(30) NOT NULL DEFAULT '10',
  PRIMARY KEY (`id`),
  UNIQUE KEY `word` (`word`)
) DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__acesef_extensions`;
CREATE TABLE IF NOT EXISTS `#__acesef_extensions` (
  `id` INT(12) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL DEFAULT '',
  `extension` VARCHAR(45) NOT NULL DEFAULT '',
  `params` TEXT NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `extension` (`extension`)
) DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__acesef_bookmarks`;
CREATE TABLE IF NOT EXISTS `#__acesef_bookmarks` (
  `id` INT(12) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL DEFAULT '',
  `html` TEXT NOT NULL DEFAULT '',
  `btype` VARCHAR(20) NOT NULL DEFAULT '',
  `placeholder` VARCHAR(150) NOT NULL DEFAULT '',
  `published` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;

INSERT INTO `#__acesef_bookmarks` (`id`, `name`, `html`, `btype`, `placeholder`) VALUES
(1,'Digg.com','<a rel="nofollow" href="http://digg.com/" title="Digg!" target="_blank" onclick="window.open(''http://digg.com/submit?url=*acesef*url*&title=*acesef*title*&bodytext=*acesef*description*''); return false;"><img height="18px" width="18px" src="*acesef*imageDirectory*/digg.png" alt="Digg!" title="Digg!" /></a>','icon','{acesef icon}'),
(2,'Digg.com - Normal','<script type="text/javascript">digg_url=''*acesef*url*''; digg_title=''*acesef*title*''; digg_bodytext=''*acesef*description*''; digg_bgcolor=''*acesef*bgcolor*''; digg_window=''new'';</script><script src="http://digg.com/tools/diggthis.js" type="text/javascript"></script>','badge','{acesef Digg1}'),
(3,'Digg.com - Compact','<script type="text/javascript">digg_url=''*acesef*url*''; digg_title=''*acesef*title*''; digg_bodytext=''*acesef*description*''; digg_bgcolor=''*acesef*bgcolor*''; digg_skin=''compact''; digg_window = ''new'';</script><script src="http://digg.com/tools/diggthis.js" type="text/javascript"></script>','badge','{acesef Digg2}'),
(4,'Reddit.com','<a rel="nofollow" onclick="window.open(''http://reddit.com/submit?url=*acesef*url_encoded*&title=*acesef*title_encoded*''); return false;" href="http://reddit.com" title="Reddit!" target="_blank"><img height="18px" width="18px" src="*acesef*imageDirectory*/reddit.png" alt="Reddit!" title="Reddit!" /></a>','icon','{acesef icon}'),
(5,'Reddit.com - Style 1','<script>reddit_url=''*acesef*url*''</script><script>reddit_title=''*acesef*title*''</script><script type="text/javascript" src="http://reddit.com/button.js?t=1"></script>','badge','{acesef Reddit1}'),
(6,'Reddit.com - Style 2','<script>reddit_url=''*acesef*url*''</script><script>reddit_title=''*acesef*title*''</script><script type="text/javascript" src="http://reddit.com/button.js?t=2"></script>','badge','{acesef Reddit2}'),
(7,'Reddit.com - Style 3','<script>reddit_url=''*acesef*url*''</script><script>reddit_title=''*acesef*title*''</script><script type="text/javascript" src="http://reddit.com/button.js?t=3"></script>','badge','{acesef Reddit3}'),
(8,'Del.icio.us','<a rel="nofollow" href="http://del.icio.us/" title="Del.icio.us!" target="_blank" onclick="window.open(''http://del.icio.us/post?v=4&noui&jump=close&url=*acesef*url_encoded*&title=*acesef*title_encoded*''); return false;"><img height="18px" width="18px" src="*acesef*imageDirectory*/delicious.png" alt="Del.icio.us!" title="Del.icio.us!" /></a>','icon','{acesef icon}'),
(9,'Del.icio.us - Tall','<script src="http://images.del.icio.us/static/js/blogbadge.js"></script>','badge','{acesef Delicious2}'),
(10,'Del.icio.us - One Line','<script type="text/javascript">if (typeof window.Delicious == "undefined") window.Delicious = {}; Delicious.BLOGBADGE_DEFAULT_CLASS = ''delicious-blogbadge-line'';</script><script src="http://images.del.icio.us/static/js/blogbadge.js"></script>','badge','{acesef Delicious2}'),
(11,'Mixx','<a rel="nofollow" onclick="window.open(''http://www.mixx.com/submit?page_url=*acesef*url_encoded*&title=*acesef*title_encoded*''); return false;" href="http://www.mixx.com/" title="Mixx!" target="_blank"><img height="18px" width="18px" src="*acesef*imageDirectory*/mixx.png" alt="Mixx!" title="Mixx!" /></a>','icon','{acesef icon}'),
(12,'EntirelyOpenSource.com','<a onclick="window.open(''http://www.entirelyopensource.com/submit.php?url=*acesef*url_encoded*''); return false;" href="http://www.entirelyopensource.com/" title="Free and Open Source Software News" target="_blank"><img height="18px" width="18px" src="*acesef*imageDirectory*/entirelyopensource.png" alt="Free and Open Source Software News" title="Free and Open Source Software News" /></a>','icon','{acesef icon}'),
(13,'Google Bookmarks','<a rel="nofollow" onclick="window.open(''http://www.google.com/bookmarks/mark?op=edit&bkmk=*acesef*url_encoded*&title=*acesef*title_encoded*''); return false;" href="http://www.google.com/bookmarks/" title="Google!" target="_blank"><img height="18px" width="18px" src="*acesef*imageDirectory*/google.png" alt="Google!" title="Google!" /></a>','icon','{acesef icon}'),
(14,'Live.com','<a rel="nofollow" onclick="window.open(''https://favorites.live.com/quickadd.aspx?url=*acesef*url_encoded*&title=*acesef*title_encoded*''); return false;" href="https://favorites.live.com/" title="Live!" target="_blank"><img height="18px" width="18px" src="*acesef*imageDirectory*/live.png" alt="Live!" title="Live!" /></a>','icon','{acesef icon}'),
(15,'Facebook.com','<a rel="nofollow" onclick="window.open(''http://www.facebook.com/sharer.php?u=*acesef*url_encoded*&t=*acesef*title_encoded*''); return false;" href="https://www.facebook.com/" title="Facebook!" target="_blank"><img height="18px" width="18px" src="*acesef*imageDirectory*/facebook.png" alt="Facebook!" title="Facebook!" /></a>','icon','{acesef icon}'),
(16,'Slashdot.org','<a rel="nofollow" onclick="window.open('' http://slashdot.org/bookmark.pl?url=*acesef*url_encoded*&title=*acesef*title_encoded*''); return false;" href="http://slashdot.org/" title="Slashdot!" target="_blank"><img height="18px" width="18px" src="*acesef*imageDirectory*/slashdot.png" alt="Slashdot!" title="Slashdot!" /></a>','icon','{acesef icon}'),
(17,'Netscape.com','<a rel="nofollow" onclick="window.open(http://www.netscape.com/submit/?U=*acesef*url_encoded*&T=*acesef*title_encoded*''); return false;" href="http://www.netscape.com/" title="Netscape!" target="_blank"><img height="18px" width="18px" src="*acesef*imageDirectory*/netscape.png" alt="Netscape!" title="Netscape!" /></a>','icon','{acesef icon}'),
(18,'Technorati.com','<a rel="nofollow" onclick="window.open(''http://www.technorati.com/faves?add=*acesef*url_encoded*''); return false;" href="http://www.technorati.com/" title="Technorati!" target="_blank"><img src="*acesef*imageDirectory*/technorati.png" alt="Technorati!" title="Technorati!" /></a>','icon','{acesef icon}'),
(19,'StumbleUpon.com','<a rel="nofollow" onclick="window.open(''http://www.stumbleupon.com/submit?url=*acesef*url_encoded*&title=*acesef*title_encoded*''); return false;" href="http://www.stumbleupon.com/" title="StumbleUpon!" target="_blank"><img src="*acesef*imageDirectory*/stumbleupon.png" alt="StumbleUpon!" title="StumbleUpon!" /></a>','icon','{acesef icon}'),
(20,'MySpace.com','<a rel="nofollow" href="http://www.myspace.com/" title="MySpace!" target="_blank" onclick="window.open(''http://www.myspace.com/Modules/PostTo/Pages/?t=*acesef*title*&u=*acesef*url*''); return false;"><img height="18px" width="18px" src="*acesef*imageDirectory*/myspace.png" alt="MySpace!" title="MySpace!" /></a>','icon','{acesef icon}'),
(21,'Spurl.net','<a rel="nofollow" onclick="window.open(''http://www.spurl.net/spurl.php?url=*acesef*url_encoded*&title=*acesef*title_encoded*''); return false;" href="http://www.spurl.net/" title="Spurl!" target="_blank"><img src="*acesef*imageDirectory*/spurl.png" alt="Spurl!" title="Spurl!" /></a>','icon','{acesef icon}'),
(22,'Wists.com','<a rel="nofollow" onclick="window.open(''http://wists.com/r.php?c=&r=*acesef*url_encoded*&tot;e=*acesef*title_encoded*''); return false;" href="http://wists.com/" title="Wists!" target="_blank"><img src="*acesef*imageDirectory*/wists.png" alt="Wists!" title="Wists!" /></a>','icon','{acesef icon}'),
(23,'Simpy.com','<a rel="nofollow" onclick="window.open(''http://www.simpy.com/simpy/LinkAdd.do?href=*acesef*url_encoded*&title=*acesef*title_encoded*''); return false;" href="http://www.simpy.com/" title="Simpy!" target="_blank"><img src="*acesef*imageDirectory*/simpy.png" alt="Simpy!" title="Simpy!" /></a>','icon','{acesef icon}'),
(24,'Newsvine.com','<a rel="nofollow" onclick="window.open('' http://www.newsvine.com/_wine/save?u=*acesef*url_encoded*&h=*acesef*title_encoded*''); return false;" href="http://www.newsvine.com/" title="Newsvine!" target="_blank"><img src="*acesef*imageDirectory*/newsvine.png" alt="Newsvine!" title="Newsvine!" /></a>','icon','{acesef icon}'),
(25,'BlinkList.com','<a rel="nofollow" onclick="window.open('' http://blinklist.com/index.php?Action=Blink/addblink.php&Url=*acesef*url_encoded*&Title=*acesef*title_encoded*''); return false;" href="http://www.blinklist.com/" title="Blinklist!" target="_blank"><img src="*acesef*imageDirectory*/blinklist.png" alt="Blinklist!" title="Blinklist!" /></a>','icon','{acesef icon}'),
(26,'Furl.net','<a rel="nofollow" onclick="window.open(''http://furl.net/storeIt.jsp?u=*acesef*url_encoded*&t=*acesef*title_encoded*''); return false;" href="http://www.furl.net/" title="Furl!" target="_blank"><img src="*acesef*imageDirectory*/furl.png" alt="Furl!" title="Furl!" /></a>','icon','{acesef icon}'),
(27,'Fark.com','<a rel="nofollow" onclick="window.open(''http://cgi.fark.com/cgi/fark/submit.pl?new_url=*acesef*url_encoded*&new_comment=*acesef*title_encoded*&linktype='');return false;" href="http://fark.com" title="Fark!" target="_blank"><img src="*acesef*imageDirectory*/fark.png" alt="Fark!" title="Fark!" /></a>','icon','{acesef icon}'),
(28,'BlogMarks.net','<a rel="nofollow" onclick="window.open(''http://blogmarks.net/my/new.php?mini=1&simple=1&url=*acesef*url_encoded*&title=*acesef*title_encoded*''); return false;" href="http://blogmarks.net/" title="Blogmarks!" target="_blank"><img src="*acesef*imageDirectory*/blogmarks.png" alt="Blogmarks!" title="Blogmarks!" /></a>','icon','{acesef icon}'),
(29,'Yahoo! Buzz','<a rel="nofollow" onclick="window.open(''http://myweb2.search.yahoo.com/myresults/bookmarklet?u=*acesef*url_encoded*&t=*acesef*title_encoded*''); return false;" href="http://myweb2.search.yahoo.com/" title="Yahoo!" target="_blank"><img src="*acesef*imageDirectory*/yahoo.png" alt="Yahoo!" title="Yahoo!" /></a>','icon','{acesef icon}'),
(30,'Smarking.com','<a rel="nofollow" onclick="window.open(''http://smarking.com/editbookmark/?url=*acesef*url_encoded*''); return false;" href="http://smarking.com/" title="Smarking!" target="_blank"><img src="*acesef*imageDirectory*/smarking.png" alt="Smarking!" title="Smarking!" /></a>','icon','{acesef icon}'),
(31,'Netvouz.com','<a rel="nofollow" onclick="window.open(''http://www.netvouz.com/action/submitBookmark?url=*acesef*url_encoded*&title=*acesef*title_encoded*&popup=no''); return false;" href="http://www.netvouz.com/" title="Smarking!" target="_blank"><img src="*acesef*imageDirectory*/netvouz.png" alt="Netvouz!" title="Netvouz!" /></a>','icon','{acesef icon}'),
(32,'Mister-Wong.com','<a rel="nofollow" onclick="window.open(''http://www.mister-wong.com/index.php?action=addurl&bm_url=*acesef*url_encoded*&bm_description=*acesef*title_encoded*''); return false;" href="http://www.mister-wong.com/" title="Mister-Wong!" target="_blank"><img src="*acesef*imageDirectory*/mister-wong.png" alt="Mister-Wong!" title="Mister-Wong!" /></a>','icon','{acesef icon}'),
(33,'RawSugar.com','<a rel="nofollow" onclick="window.open(''http://www.rawsugar.com/tagger/?turl=*acesef*url_encoded*&tttl=*acesef*title_encoded*&editorInitialized=1''); return false;" href="http://www.rawsugar.com/" title="RawSugar!" target="_blank"><img src="*acesef*imageDirectory*/rawsugar.png" alt="RawSugar!" title="RawSugar!" /></a>','icon','{acesef icon}'),
(34,'Ma.gnolia.com','<a rel="nofollow" onclick="window.open(''http://ma.gnolia.com/bookmarklet/add?url=*acesef*url_encoded*&title=*acesef*title_encoded*''); return false;" href="http://ma.gnolia.com/" title="Ma.gnolia!" target="_blank"><img src="*acesef*imageDirectory*/magnolia.png" alt="Ma.gnolia!" title="Ma.gnolia!" /></a>','icon','{acesef icon}'),
(35,'Squidoo.com','<a rel="nofollow" onclick="window.open(''http://www.squidoo.com/lensmaster/bookmark?*url_encoded*&title=*acesef*title_encoded*''); return false;" href="http://www.squidoo.com/" title="Squidoo!" target="_blank"><img src="*acesef*imageDirectory*/squidoo.png" alt="Squidoo!" title="Squidoo!" /></a>','icon','{acesef icon}'),
(36,'FeedMeLinks.com','<a rel="nofollow" onclick="window.open(''http://feedmelinks.com/categorize?from=toolbar&op=submit&url=*acesef*url_encoded*&name=*acesef*title_encoded*''); return false;" href="http://feedmelinks.com/" title="FeedMeLinks!" target="_blank"><img src="*acesef*imageDirectory*/feedmelinks.png" alt="FeedMeLinks!" title="FeedMeLinks!" /></a>','icon','{acesef icon}'),
(37,'BlinkBits.com','<a rel="nofollow" onclick="window.open(''http://www.blinkbits.com/bookmarklets/save.php?v=1&source_url=*acesef*url_encoded*&title=*acesef*title_encoded*''); return false;" href="http://www.blinkbits.com/" title="BlinkBits!" target="_blank"><img src="*acesef*imageDirectory*/blinkbits.png" alt="BlinkBits!" title="BlinkBits!" /></a>','icon','{acesef icon}'),
(38,'TailRank.com','<a rel="nofollow" onclick="window.open(''http://tailrank.com/share/?link_href=*acesef*url_encoded*&title=*acesef*title_encoded*''); return false;" href="http://tailrank.com/" title="Tailrank!" target="_blank"><img src="*acesef*imageDirectory*/tailrank.png" alt="Tailrank!" title="Tailrank!" /></a>','icon','{acesef icon}'),
(39,'linkaGoGo.com','<a rel="nofollow" onclick="window.open(''http://www.linkagogo.com/go/AddNoPopup?url=*acesef*url_encoded*&title=*acesef*title_encoded*''); return false;" href="http://www.linkagogo.com/" title="linkaGoGo!" target="_blank"><img src="*acesef*imageDirectory*/linkagogo.png" alt="linkaGoGo!" title="linkaGoGo!" /></a>','icon','{acesef icon}'),
(40,'Cannotea.org','<a rel="nofollow" onclick="window.open(''http://www.connotea.org/addpopup?continue=confirm&uri=*acesef*url_encoded*&title=*acesef*title_encoded*''); return false;" href="http://www.cannotea.org/" title="Cannotea!" target="_blank"><img src="*acesef*imageDirectory*/cannotea.png" alt="Cannotea!" title="Cannotea!" /></a>','icon','{acesef icon}'),
(41,'Diigo.com','<a rel="nofollow" onclick="window.open(''http://www.diigo.com/post?url=*acesef*url_encoded*&title=*acesef*title_encoded*''); return false;" href="http://www.diigo.com/" title="Diigo!" target="_blank"><img src="*acesef*imageDirectory*/diigo.png" alt="Diigo!" title="Diigo!" /></a>','icon','{acesef icon}'),
(42,'Faves.com','<a rel="nofollow" onclick="window.open(''http://faves.com/Authoring.aspx?u=*acesef*url_encoded*&t=*acesef*title_encoded*''); return false;" href="http://faves.com/" title="Faves!" target="_blank"><img src="*acesef*imageDirectory*/faves.png" alt="Faves!" title="Faves!" /></a>','icon','{acesef icon}'),
(43,'Ask.com','<a rel="nofollow" onclick="window.open(''http://myjeeves.ask.com/mysearch/BookmarkIt?v=1.2&t=webpages&url=*acesef*url_encoded*&title=*acesef*title_encoded*''); return false;" href="http://faves.com/" title="Ask!" target="_blank"><img src="*acesef*imageDirectory*/ask.png" alt="Ask!" title="Ask!" /></a>','icon','{acesef icon}'),
(44,'DZone.com','<a rel="nofollow" onclick="window.open(''http://www.dzone.com/links/add.html?url=*acesef*url_encoded*&title=*acesef*title_encoded*''); return false;" href="http://www.dzone.com/" title="DZone!" target="_blank"><img src="*acesef*imageDirectory*/dzone.png" alt="DZone!" title="DZone!" /></a>','icon','{acesef icon}'),
(45,'DZone.com - Tall','<script type="text/javascript">var dzone_url = ''*acesef*url*'';</script><script type="text/javascript">var dzone_title = ''*acesef*title*'';</script><script type="text/javascript">var dzone_blurb=''*acesef*description*'';</script><script type="text/javascript">var dzone_style = ''1'';</script><script language="javascript" src="http://widgets.dzone.com/widgets/zoneit.js"></script>','badge','{acesef DZone1}'),
(46,'DZone.com - Wide','<script type="text/javascript">var dzone_url = ''*acesef*url*'';</script><script type="text/javascript">var dzone_title = ''*acesef*title*'';</script><script type="text/javascript">var dzone_blurb=''*acesef*description*'';</script><script type="text/javascript">var dzone_style = ''2'';</script><script language="javascript" src="http://widgets.dzone.com/widgets/zoneit.js"></script>','badge','{acesef DZone2}'),
(47,'Swik.net','<a rel="nofollow" onclick="window.open(''http://stories.swik.net/?submitUrl&url=*acesef*url_encoded*''); return false;" href="http://stories.swik.net/" title="Swik!" target="_blank"><img src="*acesef*imageDirectory*/swik.png" alt="Swik!" title="Swik!" /></a>','icon','{acesef icon}'),
(48,'Shoutwire.com','<a rel="nofollow" onclick="window.open(''http://www.shoutwire.com/?p=submit&link=*acesef*url_encoded*''); return false;" href="http://wwww.shoutwire.net/" title="ShoutWire!" target="_blank"><img src="*acesef*imageDirectory*/shoutwire.png" alt="ShoutWire!" title="ShoutWire!" /></a>','icon','{acesef icon}'),
(49,'MyLinkVault.com','<a rel="nofollow" onclick="window.open(''http://www.mylinkvault.com/link-quick.php?u=*acesef*url_decoded*&n=*acesef*title_encoded*''); return false;" href="http://wwww.mylinkvault.net/" title="MyLinkVault!" target="_blank"><img src="*acesef*imageDirectory*/mylinkvault.png" alt="MyLinkVault!" title="MyLinkVault!" /></a>','icon','{acesef icon}'),
(50,'Maple.nu','<a rel="nofollow" onclick="window.open(''http://www.maple.nu/submit.php?url=*acesef*url_encoded*&title=*acesef*title_encoded*''); return false;" href="http://maple.nu/" title="Maple!" target="_blank"><img src="*acesef*imageDirectory*/maple.png" alt="Maple!" title="Maple!" /></a>','icon','{acesef icon}'),
(51,'BlogRolling.com','<a rel="nofollow" onclick="window.open(''http://www.blogrolling.com/add_links_pop.phtml?u=*acesef*url_encoded*&t=*acesef*title_encoded*''); return false;" href="http://www.blogrolling.com/" title="BlogRolling!" target="_blank"><img src="*acesef*imageDirectory*/blogrolling.png" alt="BlogRolling!" title="BlogRolling!" /></a>','icon','{acesef icon}'),
(52,'AddThis.com - Drop Down','<!-- AddThis Bookmark Button BEGIN --><script type="text/javascript">addthis_url=''*acesef*url*''; addthis_title=''*acesef*title*''; addthis_pub=''*acesef*addThisPubId*'';</script><script type="text/javascript" src="http://s7.addthis.com/js/addthis_widget.php?v=12" ></script><!-- AddThis Bookmark Button END -->','iconset','{acesef AddThis1}'),
(53,'AddThis.com - Style 1','<!-- AddThis Bookmark Button BEGIN --><a href="http://www.addthis.com/bookmark.php" onclick="addthis_url=''*acesef*url*''; addthis_title=''*acesef*title*''; return addthis_click(this);" target="_blank"><img src="http://s9.addthis.com/button0-bm.gif" width="83" height="16" border="0" alt="AddThis Social Bookmark Button" /></a> <script type="text/javascript">var addthis_pub=''*acesef*addThisPubId*'';</script><script type="text/javascript" src="http://s9.addthis.com/js/widget.php?v=10"></script>  <!-- AddThis Bookmark Button END -->','iconset','{acesef AddThis2}'),
(54,'AddThis.com - Style 2','<!-- AddThis Bookmark Button BEGIN --><a href="http://www.addthis.com/bookmark.php" onclick="addthis_url=''*acesef*url*''; addthis_title=''*acesef*title*''; return addthis_click(this);" target="_blank"><img src="http://s9.addthis.com/button1-bm.gif" width="125" height="16" border="0" alt="AddThis Social Bookmark Button" /></a> <script type="text/javascript">var addthis_pub=''*acesef*addThisPubId*'';</script><script type="text/javascript" src="http://s9.addthis.com/js/widget.php?v=10"></script>  <!-- AddThis Bookmark Button END -->','iconset','{acesef AddThis3}'),
(55,'AddThis.com - Style 3','<!-- AddThis Bookmark Button BEGIN --><a href="http://www.addthis.com/bookmark.php" onclick="addthis_url=''*acesef*url*''; addthis_title=''*acesef*title*''; return addthis_click(this);" target="_blank"><img src="http://s9.addthis.com/button1-share.gif" width="125" height="16" border="0" alt="AddThis Social Bookmark Button" /></a> <script type="text/javascript">var addthis_pub=''*acesef*addThisPubId*'';</script><script type="text/javascript" src="http://s9.addthis.com/js/widget.php?v=10"></script>  <!-- AddThis Bookmark Button END -->','iconset','{acesef AddThis4}'),
(56,'AddThis.com - Style 4','<!-- AddThis Bookmark Button BEGIN --><a href="http://www.addthis.com/bookmark.php" onclick="addthis_url=''*acesef*url*''; addthis_title=''*acesef*title*''; return addthis_click(this);" target="_blank"><img src="http://s9.addthis.com/button1-addthis.gif" width="125" height="16" border="0" alt="AddThis Social Bookmark Button" /></a> <script type="text/javascript">var addthis_pub=''*acesef*addThisPubId*'';</script><script type="text/javascript" src="http://s9.addthis.com/js/widget.php?v=10"></script><!-- AddThis Bookmark Button END -->','iconset','{acesef AddThis5}'),
(57,'AddThis.com - Style 5','<!-- AddThis Bookmark Button BEGIN --><a href="http://www.addthis.com/bookmark.php" onclick="addthis_url=''*acesef*url*''; addthis_title=''*acesef*title*''; return addthis_click(this);" target="_blank"><img src="http://s9.addthis.com/button2-bm.png" width="160" height="24" border="0" alt="AddThis Social Bookmark Button" /></a> <script type="text/javascript">var addthis_pub=''*acesef*addThisPubId*'';</script><script type="text/javascript" src="http://s9.addthis.com/js/widget.php?v=10"></script><!-- AddThis Bookmark Button END -->','iconset','{acesef AddThis6}'),
(58,'GodSurfer.com','<a rel="nofollow" href="http://www.godsurfer.com/" title="GodSurfer!" target="_blank"\r\nonclick="window.open(''http://www.godsurfer.com/addStory.php?url=*acesef*url*''); return false;">\r\n<img height="18px" width="18px" src="*acesef*imageDirectory*/godsurfer.png" alt="GodSurfer!" title="GodSurfer!" /></a>','icon','{acesef icon}'),
(59,'GodSurfer.com - Large','<script type="text/javascript">GODSurfer_url = "*acesef*url*";</script><script src="http://www.godsurfer.com/tools/GODSurfer.js" type="text/javascript"></script>','badge','{acesef GodSurfer1}'),
(60,'GodSurfer.com - Compact','<script type="text/javascript">GODSurfer_url = "*acesef*url*"; GODSurfer_skin = "compact";</script><script src="http://www.godsurfer.com/tools/GODSurfer.js" type="text/javascript"></script>','badge','{acesef GodSurfer2}'),
(61,'Tell-a-Friend','<script src="http://cdn.socialtwist.com/*acesef*TellAFriendId*/script.js"></script><img style="border:0;padding:0;margin:0;" src="http://images.socialtwist.com/*acesef*TellAFriendId*/button.png" onmouseout="hideHoverMap(this)" onmouseover="showHoverMap(this, ''*acesef*TellAFriendId*'', window.location, document.title)" onclick="cw(this, {id: ''*acesef*TellAFriendId*'',link: window.location, title: document.title })"/>','iconset','{acesef TellAFriend}'),
(62,'Google Buzz','<a rel="nofollow" onclick="window.open(''http://www.google.com/reader/link?url=*acesef*url_encoded*&title=*acesef*title_encoded*&srcUrl=*acesef*domain*&srcTitle=*acesef*sitename*&snippet=*acesef*description*''); return false;" href="http://www.google.com/" title="Buzz" target="_blank"><img src="*acesef*imageDirectory*/googlebuzz.png" alt="Buzz" title="Buzz" /></a>','icon','{acesef icon}'),
(63,'Twitter','<script type="text/javascript">tweetmeme_url=''*acesef*url_encoded*'';tweetme_window=''new'';tweetme_bgcolor=''*acesef*bgcolor*'';tweetmeme_source=''*acesef*twitterAccount*'';tweetmeme_service=''bit.ly'';tweetme_title=''*acesef*title*'';tweetmeme_hashtags='''';</script><script type="text/javascript" src="http://tweetmeme.com/i/scripts/button.js"></script>','badge','{acesef Twitter}'),
(64,'Google Buzz','<a title="Post on Google Buzz" class="google-buzz-button" href="http://www.google.com/buzz/post" data-button-style="normal-count" ></a><script type="text/javascript" src="http://www.google.com/buzz/api/button.js"></script>','badge','{acesef GoogleBuzz}'),
(65,'Yahoo! Buzz','<script type="text/javascript">yahooBuzzArticleId = window.location.href;</script><script type="text/javascript" src="http://d.yimg.com/ds/badge2.js" badgetype="square"></script>','badge','{acesef YahooBuzz}'),
(66,'MySpace','<a href="javascript:void(window.open(\'http://www.myspace.com/Modules/PostTo/Pages/?u=\'+encodeURIComponent(document.location.toString()),\'ptm\',\'height=450,width=440\').focus())"><img src="http://cms.myspacecdn.com/cms/ShareOnMySpace/LargeSquare.png" border="0" alt="Share on MySpace" /></a>','badge','{acesef MySpace}'),
(67,'Stumbleupon','<script src="http://www.stumbleupon.com/hostedbadge.php?s=5"></script>','badge','{acesef Stumbleupon}'),
(68,'Google Buzz (with counter)','<a title="Buzz" class="google-buzz-button" href="http://www.google.com/buzz/post" data-button-style="small-count"></a><script type="text/javascript" src="http://www.google.com/buzz/api/button.js"></script>','badge','{acesef GoogleBuzz2}'),
(69,'Twitter (with counter)','<a href="http://twitter.com/share?url=*acesef*url_encoded*" class="twitter-share-button" data-text="*acesef*title*:" data-count="horizontal" data-via="*acesef*twitterAccount*">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>','badge','{acesef Twitter2}'),
(70,'Facebook (with counter)','<iframe src="http://www.facebook.com/plugins/like.php?href=*acesef*url_encoded*&amp;layout=button_count&amp;width=90&amp;height=20&amp;show_faces=false&amp;action=like&amp;colorscheme=light" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:90px; height:20px;" allowTransparency="true"></iframe>','badge','{acesef Facebook2}'),
(71,'Facebook Share','<script>var fbShare = {url: "*acesef*url_encoded*"}</script><script src="http://widgets.fbshare.me/files/fbshare.js"></script>','badge','{acesef Facebook}');