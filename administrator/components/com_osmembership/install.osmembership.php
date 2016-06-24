<?php
/**
 * @version        2.2.1
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2016 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
// no direct access
defined('_JEXEC') or die();
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class com_osmembershipInstallerScript
{

	public static $languageFiles = array('en-GB.com_osmembership.ini');

	private $installType = null;

	/**
	 * Method to run before installing the component. Using to backup language file in this case
	 */
	function preflight($type, $parent)
	{
		//Backup the old language file
		foreach (self::$languageFiles as $languageFile)
		{
			if (JFile::exists(JPATH_ROOT . '/language/en-GB/' . $languageFile))
			{
				JFile::copy(JPATH_ROOT . '/language/en-GB/' . $languageFile, JPATH_ROOT . '/language/en-GB/bak.' . $languageFile);
			}
		}

		if (JFile::exists(JPATH_ROOT . '/components/com_osmembership/assets/css/custom.css'))
		{
			JFile::copy(JPATH_ROOT . '/components/com_osmembership/assets/css/custom.css',
				JPATH_ROOT . '/components/com_osmembership/assets/css/bak.custom.css');
		}

		$deleteFolders = array(
			JPATH_ROOT . '/components/com_osmembership/assets/validate',
			JPATH_ROOT . '/components/com_osmembership/assets/models',
			JPATH_ROOT . '/components/com_osmembership/assets/views',
			JPATH_ROOT . '/components/com_osmembership/assets/libraries',
			JPATH_ADMINISTRATOR . '/components/com_osmembership/controllers',
			JPATH_ADMINISTRATOR . '/components/com_osmembership/models',
			JPATH_ADMINISTRATOR . '/components/com_osmembership/views',
			JPATH_ADMINISTRATOR . '/components/com_osmembership/tables',
			JPATH_ADMINISTRATOR . '/components/com_osmembership/libraries'
		);

		$deleteFiles = array(
			JPATH_ROOT . '/components/com_osmembership/helper/fields.php',
			JPATH_ROOT . '/components/com_osmembership/ipn_logs.txt',
			JPATH_ROOT . '/components/com_osmembership/views/complete/metadata.xml',
			JPATH_ROOT . '/components/com_osmembership/controller.php',
			JPATH_ADMINISTRATOR . '/components/com_osmembership/controller.php'

		);

		foreach($deleteFolders as $folder)
		{
			if (JFolder::exists($folder))
			{
				JFolder::delete($folder);
			}
		}

		foreach($deleteFiles as $file)
		{
			if (JFile::exists($file))
			{
				JFile::delete($file);
			}
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
		$this->installType = 'upgrade';

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
			}
		}

		// Restore custom modified css file
		if (JFile::exists(JPATH_ROOT . '/components/com_osmembership/assets/css/bak.custom.css'))
		{
			JFile::copy(JPATH_ROOT . '/components/com_osmembership/assets/css/bak.custom.css',
				JPATH_ROOT . '/components/com_osmembership/assets/css/custom.css');
			JFile::delete(JPATH_ROOT . '/components/com_osmembership/assets/css/bak.custom.css');
		}

		JFactory::getApplication()->redirect(
			JRoute::_('index.php?option=com_osmembership&task=upgrade&install_type=' . $this->installType, false));
	}
}