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
 * Membership Pro Component Language Model
 *
 * @package        Joomla
 * @subpackage     Membership Pro
 */
class OSMembershipModelLanguage extends MPFModel
{
	/**
	 * Instantiate the model.
	 *
	 * @param   array $config The configuration data for the model
	 *
	 */
	public function __construct($config)
	{
		parent::__construct($config);
		$this->state->insert('filter_search', 'string', '')
			->insert('filter_item', 'string', 'com_osmembership')
			->insert('filter_language', 'string', 'en-GB');
	}

	/**
	 * Get language items and store them in an array
	 *
	 */
	function getTrans($lang, $item)
	{
		$registry = new JRegistry();
		$languages = array();
		if (strpos($item, 'admin.') !== false)
		{
			$isAdmin = true;
			$item = substr($item, 6);
		}
		else
		{
			$isAdmin = false;
		}
		if ($isAdmin)
		{
			$path = JPATH_ROOT . '/administrator/language/en-GB/en-GB.' . $item . '.ini';
		}
		else
		{
			$path = JPATH_ROOT . '/language/en-GB/en-GB.' . $item . '.ini';
		}
		$registry->loadFile($path, 'INI');
		$languages['en-GB'][$item] = $registry->toArray();
		
		if ($isAdmin)
		{
			$path = JPATH_ROOT . '/administrator/language/' . $lang . '/' . $lang . '.' . $item . '.ini';
		}
		else
		{
			$path = JPATH_ROOT . '/language/' . $lang . '/' . $lang . '.' . $item . '.ini';
		}

		if (JFile::exists($path))
		{
			$registry->loadFile($path, 'INI');
			$languages[$lang][$item] = $registry->toArray();
		}
		else
		{
			$languages[$lang][$item] = array();
		}
		
		return $languages;
	}

	/**
	 * Get list of languages using on the site
	 *
	 * @return array
	 */
	function getSiteLanguages()
	{
		jimport('joomla.filesystem.folder');
		$path = JPATH_ROOT . '/language';
		$folders = JFolder::folders($path);
		$rets = array();
		foreach ($folders as $folder)
		{
			if ($folder != 'pdf_fonts')
			{
				$rets[] = $folder;
			}				
		}
			
		return $rets;
	}

	/**
	 * Save translation data
	 *
	 * @param array $data
	 */
	function save($data)
	{		
		$lang = $this->state->filter_language;
		$item = $this->state->filter_item;
		if (strpos($item, 'admin.') !== false)
		{
			$item = substr($item, 6);
			$filePath = JPATH_ROOT . '/administrator/language/' . $lang . '/' . $lang . '.' . $item . '.ini';
		}
		else
		{
			$filePath = JPATH_ROOT . '/language/' . $lang . '/' . $lang . '.' . $item . '.ini';
		}
		$keys = $data['keys'];
		$content = "";
		foreach ($keys as $key)
		{
			$value = $data[$key];
			$content .= "$key=\"$value\"\n";
		}
		if (isset($data['extra_keys']))
		{
			$keys = $data['extra_keys'];
			$values = $data['extra_values'];
			for ($i = 0, $n = count($keys); $i < $n; $i++)
			{
				$key = $keys[$i];
				$value = $values[$i];
				$content .= "$key=\"$value\"\n";
			}
		}
		JFile::write($filePath, $content);
	}
}