<?php
/**
 * @version        2.2.1
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2016 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

/**
 * Membership Pro Component Plugin Model
 *
 * @package        Joomla
 * @subpackage     Membership Pro
 */
class OSMembershipModelPlugin extends MPFModelAdmin
{
	/**
	 * Pre-process data, store plugins param in JSON format
	 *
	 * @param      $row
	 * @param      $input
	 * @param bool $isNew
	 */
	protected function beforeStore($row, $input, $isNew)
	{
		$params = $input->get('params', array(), 'array');
		if (is_array($params))
		{
			$params = json_encode($params);
		}
		else
		{
			$params = null;
		}
		$input->set('params', $params);
	}

	/**
	 * Method to install a payment plugin
	 *
	 * @param MPFInput $input
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function install($input)
	{
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.archive');
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		if (version_compare(JVERSION, '3.4.0', 'ge'))
		{
			$plugin = $input->files->get('plugin_package', null, 'raw');
		}
		else
		{
			$plugin = $input->files->get('plugin_package', null, 'none');
		}

		if ($plugin['error'] || $plugin['size'] < 1)
		{
			throw new Exception(JText::_('Upload plugin package error'));
		}
		$dest     = JFactory::getConfig()->get('tmp_path') . '/' . $plugin['name'];
		if (version_compare(JVERSION, '3.4.0', 'ge'))
		{
			$uploaded = JFile::upload($plugin['tmp_name'], $dest, false, true);
		}
		else 
		{
			$uploaded       = JFile::upload($plugin['tmp_name'], $dest);
		}		
		if (!$uploaded)
		{
			throw new Exception(JText::_('OSM_PLUGIN_UPLOAD_FAILED'));
		}
		// Temporary folder to extract the archive into
		$tmpDir     = uniqid('install_');
		$extractDir = JPath::clean(dirname($dest) . '/' . $tmpDir);
		$result     = JArchive::extract($dest, $extractDir);
		if (!$result)
		{
			throw new Exception(JText::_('OSM_EXTRACT_PLUGIN_ERROR'));
		}
		$dirList = array_merge(JFolder::files($extractDir, ''), JFolder::folders($extractDir, ''));
		if (count($dirList) == 1)
		{
			if (JFolder::exists($extractDir . '/' . $dirList[0]))
			{
				$extractDir = JPath::clean($extractDir . '/' . $dirList[0]);
			}
		}
		//Now, search for xml file
		$xmlFiles = JFolder::files($extractDir, '.xml$', 1, true);
		if (empty($xmlFiles))
		{
			throw new Exception(JText::_('OSM_COULD_NOT_FIND_XML_FILE'));
		}
		$file       = $xmlFiles[0];
		$root       = JFactory::getXML($file, true);
		$pluginType = $root->attributes()->type;
		if ($root->getName() !== 'install')
		{
			throw new Exception(JText::_('OSM_INVALID_OSM_PLUGIN'));
		}

		if ($pluginType != 'osmplugin')
		{
			throw new Exception(JText::_('OSM_INVALID_OSM_PLUGIN'));
		}
		$name         = (string) $root->name;
		$title        = (string) $root->title;
		$author       = (string) $root->author;
		$creationDate = (string) $root->creationDate;
		$copyright    = (string) $root->copyright;
		$license      = (string) $root->license;
		$authorEmail  = (string) $root->authorEmail;
		$authorUrl    = (string) $root->authorUrl;
		$version      = (string) $root->version;
		$description  = (string) $root->description;
		$row          = $this->getTable();
		$query->select('id')
			->from('#__osmembership_plugins')
			->where('name=' . $db->quote($name));
		$db->setQuery($query);
		$pluginId = (int) $db->loadResult();
		if ($pluginId)
		{
			$row->load($pluginId);
			$row->name          = $name;
			$row->title         = $title;
			$row->author        = $author;
			$row->creation_date = $creationDate;
			$row->copyright     = $copyright;
			$row->license       = $license;
			$row->author_email  = $authorEmail;
			$row->author_url    = $authorUrl;
			$row->version       = $version;
			$row->description   = $description;
		}
		else
		{
			$row->name          = $name;
			$row->title         = $title;
			$row->author        = $author;
			$row->creation_date = $creationDate;
			$row->copyright     = $copyright;
			$row->license       = $license;
			$row->author_email  = $authorEmail;
			$row->author_url    = $authorUrl;
			$row->version       = $version;
			$row->description   = $description;
			$row->published     = 0;
			$row->ordering      = $row->getNextOrder('published=1');
		}
		$row->store();

		// Update plugins which support recurring payments
		$recurringPlugins = array(
			'os_paypal',
			'os_authnet',
			'os_paypal_pro',
			'os_stripe'
		);

		if (in_array($row->name, $recurringPlugins))
		{
			$query->clear();
			$query->update('#__osmembership_plugins')
				->set('support_recurring_subscription = 1')
				->where('name IN ("' . implode('","', $recurringPlugins) . '")');
			$db->setQuery($query);
			$db->execute();
		}

		$pluginDir = JPATH_ROOT . '/components/com_osmembership/plugins';
		JFile::move($file, $pluginDir . '/' . basename($file));
		$files = $root->files->children();
		for ($i = 0, $n = count($files); $i < $n; $i++)
		{
			$file = $files[$i];
			if ($file->getName() == 'filename')
			{
				$fileName = $file;
				JFile::copy($extractDir . '/' . $fileName, $pluginDir . '/' . $fileName);
			}
			elseif ($file->getName() == 'folder')
			{
				$folderName = $file;
				if (JFolder::exists($extractDir . '/' . $folderName))
				{
					JFolder::move($extractDir . '/' . $folderName, $pluginDir . '/' . $folderName);
				}
			}
		}

		JFolder::delete($extractDir);
	}

	/**
	 * Uninstall a payment plugin
	 *
	 * @param int $id
	 *
	 * @return boolean
	 */
	public function uninstall($id)
	{
		$row = $this->getTable();
		$row->load($id);
		$name         = $row->name;
		$pluginFolder = JPATH_ROOT . '/components/com_osmembership/plugins';
		$file         = $pluginFolder . '/' . $name . '.xml';
		if (!JFile::exists($file))
		{
			$row->delete();

			return true;
		}
		$root      = JFactory::getXML($file);
		$files     = $root->files->children();
		$pluginDir = JPATH_ROOT . '/components/com_pmform/payments';
		for ($i = 0, $n = count($files); $i < $n; $i++)
		{
			$file = $files[$i];
			if ($file->getName() == 'filename')
			{
				$fileName = $file;
				if (JFile::exists($pluginDir . '/' . $fileName))
				{
					JFile::delete($pluginDir . '/' . $fileName);
				}
			}
			elseif ($file->getName() == 'folder')
			{
				$folderName = $file;
				if ($folderName)
				{
					if (JFolder::exists($pluginDir . '/' . $folderName))
					{
						JFolder::delete($pluginDir . '/' . $folderName);
					}
				}
			}
		}
		$files          = $root->languages->children();
		$languageFolder = JPATH_ROOT . '/language';
		for ($i = 0, $n = count($files); $i < $n; $i++)
		{
			$fileName          = $files[$i];
			$pos               = strpos($fileName, '.');
			$languageSubFolder = substr($fileName, 0, $pos);
			if (JFile::exists($languageFolder . '/' . $languageSubFolder . '/' . $fileName))
			{
				JFile::delete($languageFolder . '/' . $languageSubFolder . '/' . $fileName);
			}
		}

		JFile::delete($pluginFolder . '/' . $name . '.xml');
		$row->delete();

		return true;
	}
}