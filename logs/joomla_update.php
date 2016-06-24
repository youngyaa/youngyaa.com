#
#<?php die('Forbidden.'); ?>
#Date: 2016-01-27 11:01:02 UTC
#Software: Joomla Platform 13.1.0 Stable [ Curiosity ] 24-Apr-2013 00:00 GMT

#Fields: datetime	priority clientip	category	message
2016-01-27T11:01:02+00:00	INFO 60.242.81.146	update	用户Super User（42）开始升级工作。旧版本为 3.4.5 。
2016-01-27T11:01:03+00:00	INFO 60.242.81.146	update	从 https://github.com/joomla/joomla-cms/releases/download/3.4.8/Joomla_3.4.x_to_3.4.8-Stable-Patch_Package.zip 下载升级文件
2016-01-27T11:01:03+00:00	INFO 60.242.81.146	update	已成功下载文件 Joomla_3.4.x_to_3.4.8-Stable-Patch_Package.zip 。
2016-01-27T11:01:04+00:00	INFO 60.242.81.146	update	开始安装新版本。
2016-01-27T11:01:08+00:00	INFO 60.242.81.146	update	Finalising installation.
2016-01-27T11:01:08+00:00	INFO 60.242.81.146	update	Deleting removed files and folders.
2016-01-27T11:01:15+00:00	INFO 60.242.81.146	update	安装后清理现场
2016-01-27T11:01:15+00:00	INFO 60.242.81.146	update	已完成到版本 3.4.8 的升级。
2016-03-21T23:31:26+00:00	INFO 60.242.81.146	update	Update started by user Super User (42). Old version is 3.4.8.
2016-03-21T23:31:26+00:00	INFO 60.242.81.146	update	Downloading update file from https://github.com/joomla/joomla-cms/releases/download/3.5.0/Joomla_3.5.0-Stable-Update_Package.zip.
2016-03-21T23:31:37+00:00	INFO 60.242.81.146	update	File Joomla_3.5.0-Stable-Update_Package.zip successfully downloaded.
2016-03-21T23:32:16+00:00	INFO 60.242.81.146	update	Starting installation of new version.
2016-03-21T23:32:21+00:00	INFO 60.242.81.146	update	Finalising installation.
2016-03-21T23:32:21+00:00	INFO 60.242.81.146	update	Ran query from file 3.5.0-2015-07-01. Query text: ALTER TABLE `#__session` MODIFY `session_id` varchar(191) NOT NULL DEFAULT '';.
2016-03-21T23:32:21+00:00	INFO 60.242.81.146	update	Ran query from file 3.5.0-2015-07-01. Query text: ALTER TABLE `#__user_keys` MODIFY `series` varchar(191) NOT NULL;.
2016-03-21T23:32:21+00:00	INFO 60.242.81.146	update	Ran query from file 3.5.0-2015-10-13. Query text: INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`.
2016-03-21T23:32:21+00:00	INFO 60.242.81.146	update	Ran query from file 3.5.0-2015-10-26. Query text: ALTER TABLE `#__contentitem_tag_map` DROP INDEX `idx_tag`;.
2016-03-21T23:32:21+00:00	INFO 60.242.81.146	update	Ran query from file 3.5.0-2015-10-26. Query text: ALTER TABLE `#__contentitem_tag_map` DROP INDEX `idx_type`;.
2016-03-21T23:32:21+00:00	INFO 60.242.81.146	update	Ran query from file 3.5.0-2015-10-30. Query text: UPDATE `#__menu` SET `title` = 'com_contact_contacts' WHERE `id` = 8;.
2016-03-21T23:32:21+00:00	INFO 60.242.81.146	update	Ran query from file 3.5.0-2015-11-04. Query text: DELETE FROM `#__menu` WHERE `title` = 'com_messages_read' AND `client_id` = 1;.
2016-03-21T23:32:21+00:00	INFO 60.242.81.146	update	Ran query from file 3.5.0-2015-11-04. Query text: INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`.
2016-03-21T23:32:21+00:00	INFO 60.242.81.146	update	Ran query from file 3.5.0-2015-11-05. Query text: INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`.
2016-03-21T23:32:21+00:00	INFO 60.242.81.146	update	Ran query from file 3.5.0-2015-11-05. Query text: INSERT INTO `#__postinstall_messages` (`extension_id`, `title_key`, `description.
2016-03-21T23:32:21+00:00	INFO 60.242.81.146	update	Ran query from file 3.5.0-2016-02-26. Query text: CREATE TABLE IF NOT EXISTS `#__utf8_conversion` (   `converted` tinyint(4) NOT N.
2016-03-21T23:32:21+00:00	INFO 60.242.81.146	update	Ran query from file 3.5.0-2016-02-26. Query text: INSERT INTO `#__utf8_conversion` (`converted`) VALUES (0);.
2016-03-21T23:32:21+00:00	INFO 60.242.81.146	update	Ran query from file 3.5.0-2016-03-01. Query text: ALTER TABLE `#__redirect_links` DROP INDEX `idx_link_old`;.
2016-03-21T23:32:21+00:00	INFO 60.242.81.146	update	Ran query from file 3.5.0-2016-03-01. Query text: ALTER TABLE `#__redirect_links` MODIFY `old_url` VARCHAR(2048) NOT NULL;.
2016-03-21T23:32:22+00:00	INFO 60.242.81.146	update	Ran query from file 3.5.0-2016-03-01. Query text: ALTER TABLE `#__redirect_links` MODIFY `new_url` VARCHAR(2048) NOT NULL;.
2016-03-21T23:32:22+00:00	INFO 60.242.81.146	update	Ran query from file 3.5.0-2016-03-01. Query text: ALTER TABLE `#__redirect_links` MODIFY `referer` VARCHAR(2048) NOT NULL;.
2016-03-21T23:32:23+00:00	INFO 60.242.81.146	update	Ran query from file 3.5.0-2016-03-01. Query text: ALTER TABLE `#__redirect_links` ADD INDEX `idx_old_url` (`old_url`(100));.
2016-03-21T23:32:23+00:00	INFO 60.242.81.146	update	Deleting removed files and folders.
2016-03-21T23:32:31+00:00	INFO 60.242.81.146	update	Cleaning up after installation.
2016-03-21T23:32:31+00:00	INFO 60.242.81.146	update	Update to version 3.5.0 is complete.
2016-04-06T01:42:58+00:00	INFO 60.242.81.146	update	Update started by user Super User (42). Old version is 3.5.0.
2016-04-06T01:42:58+00:00	INFO 60.242.81.146	update	Downloading update file from https://github.com/joomla/joomla-cms/releases/download/3.5.1/Joomla_3.5.x_to_3.5.1-Stable-Patch_Package.zip.
2016-04-06T01:42:59+00:00	INFO 60.242.81.146	update	File Joomla_3.5.x_to_3.5.1-Stable-Patch_Package.zip successfully downloaded.
2016-04-06T01:46:09+00:00	INFO 60.242.81.146	update	Update started by user Super User (42). Old version is 3.5.0.
2016-04-06T01:46:09+00:00	INFO 60.242.81.146	update	File Joomla_3.5.x_to_3.5.1-Stable-Patch_Package.zip successfully downloaded.
2016-04-06T01:52:25+00:00	INFO 60.242.81.146	update	Update started by user Super User (42). Old version is 3.5.0.
2016-04-06T01:52:25+00:00	INFO 60.242.81.146	update	File Joomla_3.5.x_to_3.5.1-Stable-Patch_Package.zip successfully downloaded.
2016-04-06T01:58:08+00:00	INFO 60.242.81.146	update	Update started by user Super User (42). Old version is 3.5.0.
2016-04-06T01:58:08+00:00	INFO 60.242.81.146	update	File Joomla_3.5.x_to_3.5.1-Stable-Patch_Package.zip successfully downloaded.
2016-04-06T02:04:20+00:00	INFO 60.242.81.146	update	Update started by user Super User (42). Old version is 3.5.0.
2016-04-06T02:04:20+00:00	INFO 60.242.81.146	update	File Joomla_3.5.x_to_3.5.1-Stable-Patch_Package.zip successfully downloaded.
2016-04-06T02:10:51+00:00	INFO 60.242.81.146	update	Update started by user Super User (42). Old version is 3.5.0.
2016-04-06T02:10:51+00:00	INFO 60.242.81.146	update	File Joomla_3.5.x_to_3.5.1-Stable-Patch_Package.zip successfully downloaded.
2016-04-06T02:20:54+00:00	INFO 60.242.81.146	update	Update started by user Super User (42). Old version is 3.5.0.
2016-04-06T02:20:54+00:00	INFO 60.242.81.146	update	File Joomla_3.5.x_to_3.5.1-Stable-Patch_Package.zip successfully downloaded.
2016-04-06T02:21:22+00:00	INFO 60.242.81.146	update	Starting installation of new version.
2016-04-06T02:21:30+00:00	INFO 60.242.81.146	update	Finalising installation.
2016-04-06T02:21:30+00:00	INFO 60.242.81.146	update	Ran query from file 3.5.1-2016-03-25. Query text: ALTER TABLE `#__user_keys` MODIFY `user_id` varchar(150) NOT NULL;.
2016-04-06T02:21:30+00:00	INFO 60.242.81.146	update	Ran query from file 3.5.1-2016-03-29. Query text: UPDATE `#__utf8_conversion` SET `converted` = 0  WHERE (SELECT COUNT(*) FROM `#_.
2016-04-06T02:21:30+00:00	INFO 60.242.81.146	update	Deleting removed files and folders.
2016-04-06T02:21:37+00:00	INFO 60.242.81.146	update	Cleaning up after installation.
2016-04-06T02:21:37+00:00	INFO 60.242.81.146	update	Update to version 3.5.1 is complete.
