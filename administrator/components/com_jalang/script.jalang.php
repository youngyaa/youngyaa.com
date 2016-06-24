<?php
/**
 * ------------------------------------------------------------------------
 * JA Multilingual Component for Joomla 2.5 & 3.4
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2011 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites: http://www.joomlart.com - http://www.joomlancers.com
 * ------------------------------------------------------------------------
 */

defined('_JEXEC') or die;

// Try extending time, as unziping/ftping took already quite some... :
@set_time_limit( 0 );


class Com_jalangInstallerScript
{
	function postflight($type, $parent) {
		$messages = array();

		// Import required modules
		jimport('joomla.installer.installer');
		jimport('joomla.installer.helper');
		jimport('joomla.filesystem.file');
		
	}

	public function install($parent)
	{
		//enable Language Filter plugin
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->update('#__extensions')->set('`enabled` = 1')
			->where(array('`type`='.$db->quote('plugin'), '`element`='.$db->quote('languagefilter'), '`folder`='.$db->quote('system')));
		$db->setQuery($query);
		$db->execute();
	}
	
	public function uninstall($parent){
		jimport('joomla.installer.installer');
		jimport('joomla.installer.helper');
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');	
	}
}