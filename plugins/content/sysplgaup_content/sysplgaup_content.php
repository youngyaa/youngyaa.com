<?php
/*
 * @component AlphaUserPoints
 * @copyright Copyright (C) 2008-2015 Bernard Gilly
 * @license : GNU/GPL
 * @Website : http://www.alphaplug.com
 */

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

if(!defined('DS')) {
   define('DS', DIRECTORY_SEPARATOR);
}


jimport( 'joomla.plugin.plugin' );

/**
 * AlphaUserPoints Content Plugin
 *
 * @package		Joomla
 * @subpackage	AlphaUserPoints
 * @since 		1.6
 */

class plgContentsysplgaup_content extends JPlugin
{

	public function onContentPrepare($context, &$article, &$params, $limitstart)
	{
		$app = JFactory::getApplication();
		
		$user 	=  JFactory::getUser();
		
		if(isset($article->id))
		{
			$articleid  = $article->id;
		} 
		else 
		{
			$articleid = null;
		}		
		
		$option = JFactory::getApplication()->input->get('option', '');
		$view   = JFactory::getApplication()->input->get('view',   '');
		$print	= JFactory::getApplication()->input->get('print', '');

		
		if ($app->isAdmin()) return;
		
		if ( !$user->id || !$articleid ) {
			$article->text = preg_replace( " |{AUP::SHOWPOINTS}| ", "", $article->text );
			return;
		}
		
    $lang = JFactory::getLanguage();
    $lang->load( 'com_alphauserpoints', JPATH_SITE);
		
		require_once (JPATH_SITE.DS.'components'.DS.'com_alphauserpoints'.DS.'helper.php');		
		
		// *******************************************
		// * show current points of the current user *
		// *******************************************
		if ( preg_match('#{AUP::SHOWPOINTS}#Uis', $article->text, $m) )
		{
			$show = $m[0];
			if ( $show && @$_SESSION['referrerid'] ) {
				$currentpoints = AlphaUserPointsHelper::getCurrentTotalPoints( @$_SESSION['referrerid'] );
				$currentpoints = AlphaUserPointsHelper::getFPoints($currentpoints);
				if ( !$article->title || $article->title==NULL ) $article->title = $option;					
				$article->text = preg_replace( " |{AUP::SHOWPOINTS}| ", $currentpoints, $article->text );
			} else $article->text = preg_replace( " |{AUP::SHOWPOINTS}| ", "", $article->text );
		} 
		
	}
}
?>