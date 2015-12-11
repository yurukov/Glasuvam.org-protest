SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `opendata_glasuvame`
--

-- --------------------------------------------------------

--
-- Table structure for table `protest`
--

CREATE TABLE IF NOT EXISTS `protest` (
  `ip` char(8) NOT NULL,
  `country` char(2) NOT NULL,
  `lastvisit` timestamp NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `protest_stats`
--

CREATE TABLE IF NOT EXISTS `protest_stats` (
  `timest` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `num` mediumint(8) unsigned NOT NULL,
  `numall` mediumint(8) unsigned NOT NULL,
  UNIQUE KEY `timest` (`timest`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure for view `protest_show`
--

CREATE VIEW `protest_show` AS select `protest`.`country` AS `country`,count(`protest`.`ip`) AS `count(ip)` from `protest` where (`protest`.`lastvisit` > (now() - interval 10 minute)) group by `protest`.`country` order by `protest`.`country`;

