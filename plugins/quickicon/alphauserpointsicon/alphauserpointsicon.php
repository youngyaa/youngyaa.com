<?php defined('_JEXEC') or die('Restricted Access');

/*
 * @component AlphaUserPoints
 * @copyright Copyright (C) 2008-2015 Bernard Gilly
 * @license : GNU/GPL
 * @Website : http://www.alphaplug.com
 */

class plgQuickiconalphauserpointsicon extends JPlugin
{
	public function onGetIcons($context)
	{
	
		$app = JFactory::getApplication();
		if (!$app->isAdmin()) return;	
		
		if (
			$context == $this->params->get('context', 'mod_quickicon')
			&& JFactory::getUser()->authorise('core.manage', 'com_alphauserpoints')
		)
		{		

			$db = JFactory::getDBO();
		
      $lang = JFactory::getLanguage();
      $lang->load( 'com_alphauserpoints', JPATH_ADMINISTRATOR);
      
			$label = JText::_('AUP_USERS_POINTS');
			$label2 = "";
		
			// check if unapproved item
			$query = "SELECT COUNT(*) FROM #__alpha_userpoints_details"
				   . " WHERE approved='0' AND status='0' AND enabled='1'"
				   ;
			$db->setQuery( $query );
			$result = $db->loadResult();
			
			if ( $result ) $label2 = '<span class="small"> - <font color="red">' . JText::_('AUP_PENDING_APPROVAL') . ' ('.$result.')</font></span>';
			$icon = ($result)? "warning" : "star-2";
		
			return array(array(
				'link' => 'index.php?option=com_alphauserpoints',
				'image' => $icon,
				'text' => $label.$label2,
				'id' => 'plg_quickicon_alphauserpointsicon'
			));
		} else return;

	}
}
