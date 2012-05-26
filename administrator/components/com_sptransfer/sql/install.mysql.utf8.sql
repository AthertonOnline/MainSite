DROP TABLE IF EXISTS `#__sptransfer_tables`;
CREATE TABLE IF NOT EXISTS `#__sptransfer_tables` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `extension_name` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL COMMENT 'Name of the extension''s table',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

INSERT INTO `#__sptransfer_tables` (`id`, `extension_name`, `name`) VALUES
(1, 'com_users', 'usergroups'),
(2, 'com_users', 'viewlevels'),
(3, 'com_users', 'users'),
(4, 'com_content', 'categories'),
(5, 'com_content', 'content'),
(6, 'com_contact', 'categories'),
(7, 'com_contact', 'contact_details'),
(8, 'com_weblinks', 'categories'),
(9, 'com_weblinks', 'weblinks'),
(10, 'com_newsfeeds', 'categories'),
(11, 'com_newsfeeds', 'newsfeeds'),
(12, 'com_banners', 'categories'),
(13, 'com_banners', 'banner_clients'),
(14, 'com_banners', 'banners'),
(15, 'com_menus', 'menu_types'),
(16, 'com_menus', 'menu'),
(17, 'com_modules', 'modules');

DROP TABLE IF EXISTS `#__sptransfer_log`;
CREATE TABLE IF NOT EXISTS `#__sptransfer_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tables_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'FK to the #__sptransfer_tables',
  `note` mediumtext NOT NULL,
  `state` tinyint(3) NOT NULL DEFAULT '0',
  `source_id` int(10) unsigned NOT NULL DEFAULT '0',
  `destination_id` int(10) unsigned NOT NULL DEFAULT '0',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;