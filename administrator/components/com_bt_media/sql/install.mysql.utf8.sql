CREATE TABLE IF NOT EXISTS `#__bt_media_categories` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`asset_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
`name` VARCHAR(255)  NOT NULL ,
`alias` VARCHAR(255)  NOT NULL ,
`parent_id` VARCHAR(255)  NOT NULL ,
`level` INT(11)  NOT NULL DEFAULT '1',
`description` TEXT NOT NULL ,
`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`created_by` INT(11)  NOT NULL ,
`language` VARCHAR(255)  NOT NULL ,
`access` INT(11)  NOT NULL ,
`hits` INT(11)  NOT NULL DEFAULT '0',
`created_date` DATETIME NOT NULL ,
`params` TEXT,
`category_image` VARCHAR(255) NOT NULL,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8_general_ci;



CREATE TABLE IF NOT EXISTS `#__bt_media_items` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`asset_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
`name` VARCHAR(255)  NOT NULL ,
`image_path` VARCHAR(255)  NOT NULL ,
`video_path` VARCHAR(255)  NOT NULL ,
`cate_id` VARCHAR(255)  NOT NULL ,
`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`created_by` INT(11)  NOT NULL ,
`source_of_media` VARCHAR(255)  NOT NULL ,
`media_type` VARCHAR(255)  NOT NULL ,
`alias` VARCHAR(255)  NOT NULL ,
`description` TEXT NOT NULL ,
`hits` INT(11)  NOT NULL DEFAULT '0',
`vote_sum` INT(11)  NOT NULL DEFAULT '0',
`vote_count` INT(11)  NOT NULL DEFAULT '0',
`created_date` DATETIME NOT NULL,
`language` VARCHAR(255)  NOT NULL ,
`access` INT(11)  NOT NULL ,
`featured` TINYINT(1)  NOT NULL ,
`params` TEXT,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__bt_media_tags` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`name` VARCHAR(255)  NOT NULL ,
`alias` VARCHAR(255)  NOT NULL ,
`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '0',
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`created_by` INT(11)  NOT NULL ,
`description` TEXT NOT NULL ,
`hits` INT(11)  NOT NULL DEFAULT '0',
`created_date` DATETIME NOT NULL,
`language` VARCHAR(255)  NOT NULL ,
`access` INT(11)  NOT NULL ,
`params` TEXT,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__bt_media_tags_xref` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`tag_id` INT(11)  NOT NULL ,
`item_id` INT(11)  NOT NULL ,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__bt_media_vote` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`user_id` INT(11)  NOT NULL ,
`item_id` INT(11)  NOT NULL ,
`vote` INT(2)  NOT NULL ,
`ip` VARCHAR(255)  NOT NULL ,
`created_date` DATETIME  NOT NULL DEFAULT '0000-00-00 00:00:00',
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8_general_ci;


