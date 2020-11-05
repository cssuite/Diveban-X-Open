SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
CREATE TABLE IF NOT EXISTS `suite_servers` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `ip` varchar(255) collate utf8_bin NOT NULL,
  `port` int(5) NOT NULL,
  `mode` varchar(255) collate utf8_bin NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `diveban_admins` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) collate utf8_bin NOT NULL,
  `nick` varchar(255) collate utf8_bin NOT NULL,
  `icq` varchar(255) collate utf8_bin NOT NULL,
  `skype` varchar(255) collate utf8_bin NOT NULL,
  `vk` varchar(255) collate utf8_bin NOT NULL,
  `steam` varchar(255) collate utf8_bin NOT NULL,
  `steamid` varchar(255) collate utf8_bin NOT NULL,
  `passwd`  varchar(255) collate utf8_bin NOT NULL,
  `flags` varchar(255) collate utf8_bin NOT NULL,
  `access` varchar(255) collate utf8_bin NOT NULL,
  `timedo` varchar(100) NOT NULL default '',
  `timelast` varchar(100) NOT NULL default '',
  `hide` INT NOT NULL DEFAULT '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `suite_users` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) collate utf8_bin NOT NULL,
  `password` varchar(255) collate utf8_bin NOT NULL,
  `group` varchar(255) collate utf8_bin NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;