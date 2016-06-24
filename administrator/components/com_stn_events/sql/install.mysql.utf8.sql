CREATE TABLE IF NOT EXISTS `#__stn_events` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `asset_id` int(10) unsigned NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL,
  `state` tinyint(1) NOT NULL,
  `checked_out` int(11) NOT NULL,
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL,
  `modified_by` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `desription` text NOT NULL,
  `eventimage` text NOT NULL,
  `startdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `enddate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `grepinterval` varchar(255) NOT NULL,
  `prize` varchar(255) NOT NULL,
  `prizeimage` text NOT NULL,
  `prizedescription` text NOT NULL,
  `prizeprovider` varchar(255) NOT NULL,
  `eventrules` text NOT NULL,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__stn_events_dates` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` date NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__stn_events_grabers` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `timesloat_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__stn_events_timeslotes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `date_id` int(11) NOT NULL,
  `starttime` time NOT NULL,
  `endtime` time NOT NULL,
  `prize` varchar(250) NOT NULL,
  `prizeprovider` text NOT NULL,
  `prizedescription` text NOT NULL,
  `prizeimage` text NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `checked_out` int(11) NOT NULL DEFAULT '0',
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__stn_event_setting` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(250) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `rules` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `image` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `startdate` date NOT NULL,
  `enddate` date NOT NULL,
  `creator` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8_general_ci;


INSERT INTO `#__content_types` (`type_title`, `type_alias`, `table`, `content_history_options`)
SELECT * FROM ( SELECT 'Event','com_stn_events.event','{"special":{"dbtable":"#__stn_events","key":"id","type":"Event","prefix":"Joomla EventsTable"}}', '{"formFile":"administrator\/components\/com_stn_events\/models\/forms\/event.xml", "hideFields":["checked_out","checked_out_time","params","language" ,"eventrules"], "ignoreChanges":["modified_by", "modified", "checked_out", "checked_out_time"], "convertToInt":["publish_up", "publish_down"], "displayLookup":[{"sourceColumn":"catid","targetTable":"#__categories","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"group_id","targetTable":"#__usergroups","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"created_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},{"sourceColumn":"access","targetTable":"#__viewlevels","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"modified_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"}]}') AS tmp
WHERE NOT EXISTS (
	SELECT type_alias FROM `#__content_types` WHERE (`type_alias` = 'com_stn_events.event')
) LIMIT 1;
