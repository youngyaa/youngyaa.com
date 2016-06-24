<?php
/**
 * @version            2.0.4
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2015 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */
// no direct access
defined('_JEXEC') or die;
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class com_eventbookingInstallerScript
{

	public static $languageFiles = array('en-GB.com_eventbooking.ini');

	protected $installType;

	/**
	 * Method to run before installing the component
	 */
	function preflight($type, $parent)
	{
		//Backup the old language files
		foreach (self::$languageFiles as $languageFile)
		{
			if (JFile::exists(JPATH_ROOT . '/language/en-GB/' . $languageFile))
			{
				JFile::copy(JPATH_ROOT . '/language/en-GB/' . $languageFile, JPATH_ROOT . '/language/en-GB/bak.' . $languageFile);
			}
		}

		//Delete the css files which are now moved to themes folder
		$files = array('default.css', 'fire.css', 'leaf.css', 'ocean.css', 'sky.css', 'tree.css');
		$path  = JPATH_ROOT . '/components/com_eventbooking/assets/css/';
		foreach ($files as $file)
		{
			$filePath = $path . $file;
			if (JFile::exists($filePath))
			{
				JFile::delete($filePath);
			}
		}

		//Backup files which need to be keep 
		if (JFile::exists(JPATH_ROOT . '/components/com_eventbooking/fields.xml'))
		{
			JFile::copy(JPATH_ROOT . '/components/com_eventbooking/fields.xml', JPATH_ROOT . '/components/com_eventbooking/bak.fields.xml');
		}

		if (JFolder::exists(JPATH_ROOT . '/components/com_eventbooking/views'))
		{
			JFolder::delete(JPATH_ROOT . '/components/com_eventbooking/views');
		}

		if (JFolder::exists(JPATH_ROOT . '/administrator/components/com_eventbooking/controller'))
		{
			JFolder::delete(JPATH_ROOT . '/administrator/components/com_eventbooking/controller');
		}

		// Backup css file
		if (JFile::exists(JPATH_ROOT . '/components/com_eventbooking/assets/css/custom.css'))
		{
			JFile::copy(JPATH_ROOT . '/components/com_eventbooking/assets/css/custom.css', JPATH_ROOT . '/components/com_eventbooking/custom.css');
		}
	}

	/**
	 * method to install the component
	 *
	 * @return void
	 */
	function install($parent)
	{
		$this->installType = 'install';
	}

	function update($parent)
	{
		$this->installType = 'update';
	}

	/**
	 * Method to run after installing the component
	 */
	function postflight($type, $parent)
	{
		//Restore the modified language strings by merging to language files
		$registry = new JRegistry();
		foreach (self::$languageFiles as $languageFile)
		{
			$backupFile  = JPATH_ROOT . '/language/en-GB/bak.' . $languageFile;
			$currentFile = JPATH_ROOT . '/language/en-GB/' . $languageFile;
			if (JFile::exists($currentFile) && JFile::exists($backupFile))
			{
				$registry->loadFile($currentFile, 'INI');
				$currentItems = $registry->toArray();
				$registry->loadFile($backupFile, 'INI');
				$backupItems = $registry->toArray();
				$items       = array_merge($currentItems, $backupItems);
				$content     = "";
				foreach ($items as $key => $value)
				{
					$content .= "$key=\"$value\"\n";
				}
				JFile::write($currentFile, $content);
				//Delete the backup file
				JFile::delete($backupFile);
			}
		}
		//Restore the renamed files
		if (JFile::exists(JPATH_ROOT . '/components/com_eventbooking/bak.fields.xml'))
		{
			JFile::copy(JPATH_ROOT . '/components/com_eventbooking/bak.fields.xml', JPATH_ROOT . '/components/com_eventbooking/fields.xml');
			JFile::delete(JPATH_ROOT . '/components/com_eventbooking/bak.fields.xml');
		}

		if (JFile::exists(JPATH_ROOT . '/components/com_eventbooking/custom.css'))
		{
			JFile::move(JPATH_ROOT . '/components/com_eventbooking/custom.css', JPATH_ROOT . '/media/com_eventbooking/assets/css/custom.css');
		}

		$customCss = JPATH_ROOT . '/media/com_eventbooking/assets/css/custom.css';
		if (!file_exists($customCss))
		{
			$fp = fopen($customCss, 'w');
			fclose($fp);
			@chmod($customCss, 0777);
		}

		if ($this->installType == 'install')
		{
			$db  = JFactory::getDbo();
			$sql = 'SELECT COUNT(*) FROM #__eb_messages';
			$db->setQuery($sql);
			$total = $db->loadResult();
			if (!$total)
			{
				$configSql = JPATH_ADMINISTRATOR . '/components/com_eventbooking/sql/messages.eventbooking.sql';
				$sql       = JFile::read($configSql);
				$queries   = $db->splitSql($sql);
				if (count($queries))
				{
					foreach ($queries as $query)
					{
						$query = trim($query);
						if ($query != '' && $query{0} != '#')
						{
							$db->setQuery($query);
							$db->execute();
						}
					}
				}
			}
		}

		JFactory::getApplication()->redirect(
			JRoute::_('index.php?option=com_eventbooking&task=update_db_schema&install_type=' . $this->installType, false));
	}
}