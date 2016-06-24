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

class plgContentSysplgaup_reader2author extends JPlugin
{

	public function onContentAfterDisplay($context, &$article, &$params, $limitstart=0)
	{
		$app = JFactory::getApplication();
		
		$user =  JFactory::getUser();	
		
		if(isset($article->created_by)){
			$authorid  = $article->created_by;
		} else {$authorid = null;}		
	 
		if(isset($article->id)){
			$articleid  = $article->id;
		} else {$articleid = null;}		

		if ($app->isAdmin() || $user->id==$authorid || !$articleid || !$authorid ) return;		
		
		$option = JFactory::getApplication()->input->get('option', '');
		$view   = JFactory::getApplication()->input->get('view',   '');		
		
    $lang = JFactory::getLanguage();
    $lang->load( 'com_alphauserpoints', JPATH_SITE);

		switch ( $view ) {
			case 'article' :
				if ( $option=='com_content' && $limitstart==0 ) {
					
					require_once (JPATH_SITE.DS.'components'.DS.'com_alphauserpoints'.DS.'helper.php');
					
					// Rule reader to author (guest and registered)
					$authorarticle = ($article->created_by_alias) ? $article->created_by_alias : $article->author;
					
					$uri = JURI::getInstance();
					
					$uri->delVar('invitekey');      // remove var used by alpharecommend pro -> no need in the url in data reference
					$uri->delVar('referreruser');   // remove var used by alphauserpoints    -> no need in the url in data reference
					$uri->delVar('keyreference');   // remove var used by alphauserpoints    -> no need in the url in data reference
					$uri->delVar('datareference');  // remove var used by alphauserpoints    -> no need in the url in data reference
					
					$url = $uri->toString();
					
					$this->reader2author($authorid, $authorarticle, $articleid, $article->title, $url);					
				}
				break;
			default:					
		}	
	}
	
	public function reader2author ( $authorid=0, $author='', $articleid=0, $title='', $url='' )
	{	
		$app = JFactory::getApplication();
		
		require_once (JPATH_SITE.DS.'components'.DS.'com_alphauserpoints'.DS.'helper.php');
		
		if ( !$authorid || !$articleid ) return;
		
		// get referrerid of author
		$referrerUserAuthor = AlphaUserPointsHelper::getAnyUserReferreID( $authorid );
		if ( !AlphaUserPointsHelper::checkExcludeUsers( $referrerUserAuthor ) ) return ;
		
		$ip 		= getenv('REMOTE_ADDR');				
		$db	        = JFactory::getDBO();
		
		$keyreference = $articleid . "|" . $ip;		
		$keyreference = AlphaUserPointsHelper::buildKeyreference('sysplgaup_reader2author', $keyreference);
		
		// check if not already view by active user
		$query = "SELECT `id` FROM #__alpha_userpoints_details WHERE `keyreference`='" . $keyreference . "' AND enabled='1'";
		$db->setQuery( $query );
		$alreadyView = $db->loadResult();
		if ( !$alreadyView )
		{	
			$user 		=  JFactory::getUser();			
			$jnow		=  JFactory::getDate();
			$now		= $jnow->toSql();		
			$authorizedLevels = JAccess::getAuthorisedViewLevels($user->id);			
			
			$query = "SELECT * FROM #__alpha_userpoints_rules WHERE `plugin_function`='sysplgaup_reader2author' AND `published`='1' AND `access` IN (" . implode ( ",", $authorizedLevels ) . ") AND (`rule_expire`>'$now' OR `rule_expire`='0000-00-00 00:00:00')";
			$db->setQuery( $query );
			$result  = $db->loadObjectList();
			if ( $result && $referrerUserAuthor )
			{
				$datareference = '<a href="' . $url . '">' . $title . '</a> ('.$author.')' ;
				AlphaUserPointsHelper::insertUserPoints( $referrerUserAuthor, $result[0], 0, $keyreference, $datareference );
			}
		}		
	}

}
?>