ALTER TABLE `#__bt_media_categories`  ADD `asset_id` INT(10) NOT NULL DEFAULT '0' AFTER `id`;
ALTER TABLE `#__bt_media_items`  ADD `asset_id` INT(10) NOT NULL DEFAULT '0' AFTER `id`;