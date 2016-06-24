<?php

/**
 * @package 	bt_socialshare - BT Social Share Plugin
 * @version		2.0
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

define('_JEXEC', 1 );
define('JPATH_BASE', dirname(dirname(dirname(dirname(__FILE__)))));

if(!$_REQUEST['link'] || !$_REQUEST['title']) return;

require_once ( JPATH_BASE .'/includes/defines.php' );
require_once ( JPATH_BASE .'/includes/framework.php' );

$mainframe = JFactory::getApplication('site');
$mainframe->initialise();

$plugin = JPluginHelper::getPlugin('content', 'bt_socialshare');
$pluginParams = new JRegistry();
$pluginParams->loadString($plugin->params);
$config = JFactory::getConfig();

$mail_to=$pluginParams->get('mail_to','');
$message=$pluginParams->get('mail_message','');
$message = str_replace('[LINK]',$_REQUEST['link'],$message);
$message = str_replace('[TITLE]',$_REQUEST['title'],$message);
$mail_to = explode(',', $mail_to);
$mail_subject=$pluginParams->get('mail_subject','New comment at [[TITLE]]');
$mail_subject = str_replace('[LINK]',$_REQUEST['link'],$mail_subject);
$mail_subject = str_replace('[TITLE]',$_REQUEST['title'],$mail_subject);
foreach($mail_to as $mail){
	$mailer = JFactory::getMailer();
	$mailer->sendMail($config->get( 'fromname' ),$config->get( 'mailfrom' ),$mail, $mail_subject, $message);
}
?>
