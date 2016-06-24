DROP TABLE IF EXISTS `#__osmembership_menus`;
CREATE TABLE `#__osmembership_menus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `menu_name` varchar(255) DEFAULT NULL,
  `menu_parent_id` int(11) DEFAULT NULL,
  `menu_link` varchar(255) DEFAULT NULL,
  `published` tinyint(1) unsigned DEFAULT NULL,
  `ordering` int(11) DEFAULT NULL,
  `menu_class` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `#__eb_menus`
--

INSERT INTO `#__osmembership_menus` (`id`, `menu_name`, `menu_parent_id`, `menu_link`, `published`, `ordering`, `menu_class`) VALUES
(1, 'OSM_DASHBOARD', 0, 'index.php?option=com_osmembership&view=dashboard', 1, 1, 'home'),

(2, 'OSM_SETUP', 0, NULL, 1, 2, 'list-view'),
(3, 'OSM_PLAN_CATEGORIES', 2, 'index.php?option=com_osmembership&view=categories', 1, 1, 'folder-open'),
(4, 'OSM_PLANS', 2, 'index.php?option=com_osmembership&view=plans', 1, 2, 'folder-close'),
(5, 'OSM_CUSTOM_FIELDS', 2, 'index.php?option=com_osmembership&view=fields', 1, 3, 'list'),
(6, 'OSM_TAX_RULES', 2, 'index.php?option=com_osmembership&view=taxes', 1, 4, 'location'),
(7, 'OSM_EMAIL_MESSAGES', 2, 'index.php?option=com_osmembership&view=message', 1, 5, 'envelope'),
(8, 'OSM_COUNTRIES', 2, 'index.php?option=com_osmembership&view=countries', 1, 5, 'flag'),
(9, 'OSM_STATES', 2, 'index.php?option=com_osmembership&view=states', 1, 6, 'book'),


(10, 'OSM_SUBSCRIPTIONS', 0, NULL, 1, 3, 'user'),
(11, 'OSM_SUBSCRIPTIONS', 10, 'index.php?option=com_osmembership&view=subscriptions', 1, 1, 'folder-open'),
(12, 'OSM_SUBSCRIBERS', 10, 'index.php?option=com_osmembership&view=subscribers', 1, 2, 'user'),
(13, 'OSM_GROUPMEMBERS', 10, 'index.php?option=com_osmembership&view=groupmembers', 1, 3, 'user'),
(14, 'OSM_IMPORT', 10, 'index.php?option=com_osmembership&view=import', 1, 4, 'upload'),
(15, 'OSM_EXPORT', 10, 'index.php?option=com_osmembership&task=subscription.export', 1, 5, 'download'),
(16, 'OSM_CSV_IMPORT_TEMPLATE', 10, 'index.php?option=com_osmembership&task=subscription.csv_import_template', 1, 6, 'list'),

(17, 'OSM_COUPONS', 0, NULL, 1, 4, 'tags'),
(18, 'OSM_COUPONS', 17, 'index.php?option=com_osmembership&view=coupons', 1, 1, 'tags'),
(19, 'OSM_IMPORT', 17, 'index.php?option=com_osmembership&view=coupon&layout=import', 1, 2, 'upload'),
(20, 'OSM_EXPORT', 17, 'index.php?option=com_osmembership&task=coupon.export', 1, 3, 'download'),
(21, 'OSM_BATCH_COUPONS', 17, 'index.php?option=com_osmembership&view=coupon&layout=batch', 1, 4, 'list'),

(22, 'OSM_PAYMENT_PLUGINS', 0, 'index.php?option=com_osmembership&view=plugins', 1, 5, 'wrench'),

(23, 'OSM_TRANSLATION', 0, 'index.php?option=com_osmembership&view=language', 1, 6, 'flag'),
(24, 'OSM_CONFIGURATION', 0, 'index.php?option=com_osmembership&view=configuration', 1, 7, 'cog'),

(25, 'OSM_TOOLS', 0, NULL, 1, 8, 'tools'),
(26, 'OSM_PURGE_URLS', 25, 'index.php?option=com_osmembership&task=reset_urls', 1, 1, 'refresh'),
(27, 'OSM_FIX_DATABASE', 25, 'index.php?option=com_osmembership&task=upgrade', 1, 2, 'ok'),
(28, 'OSM_SHARE_TRANSLATION', 25, 'index.php?option=com_osmembership&task=share_translation', 1, 3, 'heart'),
(29, 'OSM_BUILD_EU_TAX_RULES', 25, 'javascript:confirmBuildTaxRules();', 1, 4, 'location');