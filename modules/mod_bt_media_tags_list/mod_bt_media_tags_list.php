<?php
/**
 * @package 	mod_bt_media_items_gallery - BT Media Items Gallery
 * @version		1.0.0
 * @created		Aug 2013
 * @author		BowThemes
 * @email		support@bowthems.com
 * @website		http://bowthemes.com
 * @support		Forum - http://bowthemes.com/forum/
 * @copyright	Copyright (C) 2012 Bowthemes. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// Include the latest functions only once
require_once dirname(__FILE__).'/helper.php';

$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));
$list = modBtMediaListTagsHelper::getItems($params);

require JModuleHelper::getLayoutPath('mod_bt_media_tags_list', $params->get('layout', 'default'));
