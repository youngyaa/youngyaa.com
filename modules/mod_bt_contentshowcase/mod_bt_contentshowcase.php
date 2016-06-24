<?php
/**
 * @package 	mod_bt_contentshowcase - BT ContentShowcase Module
 * @version		2.1
 * @created		Oct 2011

 * @author		BowThemes
 * @email		support@bowthems.com
 * @website		http://bowthemes.com
 * @support		Forum - http://bowthemes.com/forum/
 * @copyright	Copyright (C) 2012 Bowthemes. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once 'helpers/helper.php';
//Get list content
$list = modBtContentShowcaseHelper::getList( $params, $module );


$moduleclass_sfx = $params->get('moduleclass_sfx');
$imgClass = $params->get('hovereffect',1)? 'class= "hovereffect"':'';
$modal = $params->get('modalbox');

$tmp = $params->get( 'module_height', 'auto' );
$moduleHeight   =  ( $tmp=='auto' ) ? 'auto' : ((int)$tmp).'px';
$tmp = $params->get( 'module_width', 'auto' );
$moduleWidth    =  ( $tmp=='auto') ? 'auto': ((int)$tmp).'px';
$moduleWidthWrapper = ( $tmp=='auto') ? '100%': (int)$tmp.'px';

//Get Open target
$openTarget 	= $params->get( 'open_target', '_parent' );

//auto_start


//butlet and next back
$next_back 		= $params->get( 'next_back', 1 );
$butlet 		= $params->get( 'butlet', 1 );

//option for slider
//effect 
$vertical = $params->get('slide_direction') != 'vertical' ? false : true;
$effect = $params->get('slide_effect', 'scroll');
$auto_start 	= $params->get('auto_start',1);
$slide_item_per_time = $params->get('slide_item_per_time', 1);
//buttons
$nextBackPosition = $params->get('next_back_position');
$navigationType = $params->get('navigation_type');
$navigationPosition = $params->get('navigation_position');

//Option for content
$showReadmore = $params->get( 'show_readmore', '1' );
$showTitle = $params->get( 'show_title', '1' );

$show_category_name = $params->get( 'show_category_name', 0 );
$show_category_name_as_link = $params->get( 'show_category_name_as_link', 0 );

$showDate = $params->get( 'show_date', '0' );
$showAuthor = $params->get( 'show_author', '0' );
$show_intro = $params->get( 'show_intro', '0' );

//Option for image
$thumbWidth    = (int)$params->get( 'thumbnail_width', 200 );
$thumbHeight   = (int)$params->get( 'thumbnail_height', 150 );

$image_crop = $params->get( 'image_crop', '0' );
$show_image = $params->get( 'show_image', '0' );

modBtContentShowcaseHelper::fetchHead( $params );

//Get tmpl
$align_image = strtolower($params->get( 'image_align', "center" ));

$layout = $params->get('layout', 'default');

$document = JFactory::getDocument();
$params->set('rtl',$document->direction=='rtl');
require JModuleHelper::getLayoutPath('mod_bt_contentshowcase','/themes/'.$layout.'/'.$layout);


?>

