<?php
/**
 * @package         Regular Labs Installer
 * @version         16.4.23089
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            http://www.regularlabs.com
 * @copyright       Copyright © 2016 Regular Labs All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

class PlgSystemRegularLabsInstallerInstallerScript
{
	private $min_joomla_version = '3.4.1';
	private $min_php_version    = '5.3.13';

	public function preflight($route, JAdapterInstance $adapter)
	{
		JFactory::getLanguage()->load('plg_system_regularlabsinstaller', __DIR__);

		if (!$this->passMinimumJoomlaVersion())
		{
			$this->uninstallInstaller();

			return false;
		}

		if (!$this->passMinimumPHPVersion())
		{
			$this->uninstallInstaller();

			return false;
		}
	}

	public function postflight($route, JAdapterInstance $adapter)
	{
		if (!in_array($route, array('install', 'update')))
		{
			return;
		}

		// First install the Regular Labs Library
		$this->installLibrary();

		// Then install the rest of the packages
		if (!$this->installPackages())
		{
			// Uninstall this installer
			$this->uninstallInstaller();

			return false;
		}

		$this->removeDuplicateUpdateSites();

		JFactory::getApplication()->enqueueMessage(JText::_('RLI_PLEASE_CLEAR_YOUR_BROWSERS_CACHE'), 'notice');

		// Uninstall this old NoNumber Framework (if not needed anymore)
		$this->uninstallOldFramework();

		// Uninstall this installer
		$this->uninstallInstaller();
	}

	private function removeDuplicateUpdateSites()
	{
		$db = JFactory::getDbo();

		$query = $db->getQuery(true)
			->select('a.update_site_id')
			->from($db->quoteName('#__update_sites', 'a'))
			->join('LEFT', $db->quoteName('#__update_sites_extensions', 'b') . ' ON ' . $db->quoteName('b.update_site_id') . ' = ' . $db->quoteName('a.update_site_id'))
			->join('LEFT', $db->quoteName('#__update_sites_extensions', 'c')
				. ' ON ' . $db->quoteName('c.extension_id') . ' = ' . $db->quoteName('b.extension_id')
				. 'AND ' . $db->quoteName('c.update_site_id') . ' != ' . $db->quoteName('b.update_site_id')
			)
			->join('LEFT', $db->quoteName('#__update_sites', 'd') . ' ON ' . $db->quoteName('d.update_site_id') . ' = ' . $db->quoteName('c.update_site_id'))
			->where($db->quoteName('a.name') . ' LIKE ' . $db->quote('%NoNumber%'))
			->where($db->quoteName('d.name') . ' LIKE ' . $db->quote('%Regular Labs%'));
		$db->setQuery($query);
		$ids = $db->loadColumn();

		if (empty($ids))
		{
			return;
		}

		$query->clear()
			->delete('#__update_sites')
			->where($db->quoteName('update_site_id') . ' IN (' . implode(',', $ids) . ')');
		$db->setQuery($query);
		$db->execute();

		$query->clear()
			->delete('#__update_sites_extensions')
			->where($db->quoteName('update_site_id') . ' IN (' . implode(',', $ids) . ')');
		$db->setQuery($query);
		$db->execute();
	}

	// Check if Joomla version passes minimum requirement
	private function passMinimumJoomlaVersion()
	{
		if (version_compare(JVERSION, $this->min_joomla_version, '<'))
		{
			JFactory::getApplication()->enqueueMessage(
				JText::sprintf('RLI_NOT_COMPATIBLE_UPDATE', JVERSION, $this->min_joomla_version),
				'error'
			);

			return false;
		}

		return true;
	}

	// Check if PHP version passes minimum requirement
	private function passMinimumPHPVersion()
	{

		if (version_compare(PHP_VERSION, $this->min_php_version, 'l'))
		{
			JFactory::getApplication()->enqueueMessage(
				JText::sprintf('RLI_NOT_COMPATIBLE_PHP', PHP_VERSION, $this->min_php_version),
				'error'
			);

			return false;
		}

		return true;
	}

	private function installPackages()
	{
		$packages = JFolder::folders(__DIR__ . '/packages');

		$packages = array_diff($packages, array('library_regularlabs', 'plg_system_regularlabs'));

		foreach ($packages as $package)
		{
			if (!$this->installPackage($package))
			{
				return false;
			}
		}

		return true;
	}

	private function installPackage($package)
	{
		$tmpInstaller = new RLInstaller;

		return $tmpInstaller->install(__DIR__ . '/packages/' . $package);
	}

	private function installLibrary()
	{
		return
			$this->installPackage('library_regularlabs')
			&& $this->installPackage('plg_system_regularlabs');

		JFactory::getCache()->clean('_system');
	}

	private function uninstallInstaller()
	{
		if (!JFolder::exists(JPATH_SITE . '/plugins/system/regularlabsinstaller'))
		{
			return;
		}

		$this->deleteFolders(
			array(
				JPATH_SITE . '/plugins/system/regularlabsinstaller/language',
				JPATH_SITE . '/plugins/system/regularlabsinstaller',
			)
		);

		$db = JFactory::getDbo();

		$query = $db->getQuery(true)
			->delete('#__extensions')
			->where($db->quoteName('element') . ' = ' . $db->quote('regularlabsinstaller'))
			->where($db->quoteName('folder') . ' = ' . $db->quote('system'))
			->where($db->quoteName('type') . ' = ' . $db->quote('plugin'));
		$db->setQuery($query);
		$db->execute();

		JFactory::getCache()->clean('_system');
	}

	public function deleteFolders($folders = array())
	{
		foreach ($folders as $folder)
		{
			if (!is_dir($folder))
			{
				continue;
			}

			JFolder::delete($folder);
		}
	}

	public function uninstallOldFramework()
	{
		// Old NoNumber Framework is not installed
		if (!JFolder::exists(JPATH_SITE . '/plugins/system/nnframework'))
		{
			return;
		}

		if ($this->requiresOldFramework())
		{
			JFactory::getApplication()->enqueueMessage(JText::_('RLI_NONUMBER_TO_REGULAR_LABS'), 'notice');
			return;
		}

		//$this->removeOldFramework();
		JFactory::getApplication()->enqueueMessage(JText::_('RLI_NONUMBER_TO_REGULAR_LABS') . '<br>' . JText::_('RLI_UNINSTALL_NONUMBER_FRAMEWORK'), 'notice');
	}

	public function requiresOldFramework()
	{
		// list of xml files and version numbers to check
		// The version number is the first version that no longer needs the NoNumber Framework
		$extensions = array(
			array('6', '/administrator/components/com_advancedmodules/advancedmodules.xml'),
			array('2', '/administrator/components/com_advancedtemplates/advancedtemplates.xml'),
			array('6', '/administrator/components/com_contenttemplater/contenttemplater.xml'),
			array('5', '/administrator/components/com_dbreplacer/dbreplacer.xml'),
			array('6', '/administrator/components/com_nonumbermanager/nonumbermanager.xml'),
			array('7', '/administrator/components/com_rereplacer/rereplacer.xml'),
			array('5', '/administrator/components/com_snippets/snippets.xml'),

			array('5', '/administrator/modules/mod_addtomenu/mod_addtomenu.xml'),

			array('5', '/plugins/system/articlesanywhere/articlesanywhere.xml'),
			array('5', '/plugins/system/betterpreview/betterpreview.xml'),
			array('5', '/plugins/system/cachecleaner/cachecleaner.xml'),
			array('5', '/plugins/system/cdnforjoomla/cdnforjoomla.xml'),
			array('5', '/plugins/system/componentsanywhere/componentsanywhere.xml'),
			array('3', '/plugins/system/dummycontent/dummycontent.xml'),
			array('3', '/plugins/system/emailprotector/emailprotector.xml'),
			array('1', '/plugins/system/geoip/geoip.xml'),
			array('3', '/plugins/system/iplogin/iplogin.xml'),
			array('7', '/plugins/system/modals/modals.xml'),
			array('5', '/plugins/system/modulesanywhere/modulesanywhere.xml'),
			array('6', '/plugins/system/sliders/sliders.xml'),
			array('6', '/plugins/system/sourcerer/sourcerer.xml'),
			array('6', '/plugins/system/tabs/tabs.xml'),
			array('5', '/plugins/system/tooltips/tooltips.xml'),
			array('11', '/plugins/system/whatnothing/whatnothing.xml'),
		);

		foreach ($extensions as $extension)
		{
			list($version, $xml) = $extension;

			if ($this->extensionIsOld($version, $xml))
			{
				return true;
			}
		}

		return false;
	}

	public function extensionIsOld($version, $xml)
	{
		$file = JPATH_SITE . $xml;

		if (!is_file($file))
		{
			return false;
		}

		$xml = JApplicationHelper::parseXMLInstallFile($file);

		if (!$xml || empty($xml['version']))
		{
			return false;
		}

		$installed_version = $xml['version'];

		return version_compare($installed_version, $version, '<');
	}

	public function removeOldFramework()
	{
		$this->deleteFolders(
			array(
				JPATH_SITE . '/plugins/system/nnframework/language',
				JPATH_SITE . '/plugins/system/nnframework',
				JPATH_SITE . '/media/nnframework',
			)
		);

		$db = JFactory::getDbo();

		$query = $db->getQuery(true)
			->delete('#__extensions')
			->where($db->quoteName('element') . ' = ' . $db->quote('nnframework'))
			->where($db->quoteName('folder') . ' = ' . $db->quote('system'))
			->where($db->quoteName('type') . ' = ' . $db->quote('plugin'));
		$db->setQuery($query);
		$db->execute();

		JFactory::getCache()->clean('_system');
	}
}

/*
 * Override core Library Installer to prevent it from uninstalling the library before upgrade
 * We need the files to check for the version to decide whether to install or not.
 */

class RLInstaller extends JInstaller
{
	public function getAdapter($name, $options = array())
	{
		if ($name == 'library')
		{
			return new RLInstallerAdapterLibrary($this, $this->getDbo(), $options);
		}

		//parent::getAdapter($name, $options = array());
		$adapter = $this->loadAdapter($name, $options);

		if (!array_key_exists($name, $this->_adapters))
		{
			if (!$this->setAdapter($name, $adapter))
			{
				return false;
			}
		}

		return $adapter;
	}
}

JLoader::import('joomla.installer.adapter.library');

class RLInstallerAdapterLibrary extends JInstallerAdapterLibrary
{
	protected function checkExtensionInFilesystem()
	{
		if (!$this->currentExtensionId)
		{
			return;
		}

		// Already installed, can we upgrade?
		if (!$this->parent->isOverwrite() && !$this->parent->isUpgrade())
		{
			// Abort the install, no upgrade possible
			throw new RuntimeException(JText::_('JLIB_INSTALLER_ABORT_LIB_INSTALL_ALREADY_INSTALLED'));
		}

		// From this point we'll consider this an update
		$this->setRoute('update');
	}

	protected function storeExtension()
	{
		$db    = $this->parent->getDbo();
		$query = $db->getQuery(true)
			->delete($db->quoteName('#__extensions'))
			->where($db->quoteName('type') . ' = ' . $db->quote('library'))
			->where($db->quoteName('element') . ' = ' . $db->quote($this->element));
		$db->setQuery($query);

		$db->execute();

		parent::storeExtension();

		JFactory::getCache()->clean('_system');
	}
}
