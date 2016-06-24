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
defined('_JEXEC') or die();

class EventbookingModelLanguage extends RADModel
{

	/**
	 * List total
	 *
	 * @var integer
	 */
	protected $total;

	/**
	 * Pagination
	 *
	 * @var JPagination
	 */
	protected $pagination;

	/**
	 * Model list data
	 *
	 * @var Array
	 */
	protected $data;

	/**
	 * Instantiate the model.
	 *
	 * @param   array $config The configuration data for the model
	 *
	 */
	public function __construct($config)
	{
		parent::__construct($config);

		$this->state->insert('filter_search', 'string')
			->insert('filter_item', 'string', 'com_eventbooking')
			->insert('filter_language', 'string', 'en-GB')
			->insert('limit', 'int', 100)
			->insert('limitstart', 'int', 0);
	}

	/**
	 * Get language items and store them in an array
	 *
	 */
	public function getData()
	{
		$registry     = new JRegistry();
		$search       = $this->state->filter_search;
		$language     = $this->state->filter_language;
		$languageFile = $this->state->filter_item;
		if (strpos($languageFile, 'admin') !== false)
		{
			$languageFolder = JPATH_ROOT . '/administrator/language/';
			$languageFile   = substr($languageFile, 6);
		}
		else
		{
			$languageFolder = JPATH_ROOT . '/language/';
		}
		$path = $languageFolder . 'en-GB/en-GB.' . $languageFile . '.ini';
		$registry->loadFile($path, 'INI');
		$enGbItems = $registry->toArray();
		if ($language != 'en-GB')
		{
			$translatedRegistry = new JRegistry();
			$translatedPath     = $languageFolder . $language . '/' . $language . '.' . $languageFile . '.ini';
			if (JFile::exists($translatedPath))
			{
				$translatedRegistry->loadFile($translatedPath);
				$translatedLanguageItems = $translatedRegistry->toArray();
				//Remove unused language items
				$enGbKeys = array_keys($enGbItems);
				$changed  = false;
				foreach ($translatedLanguageItems as $key => $value)
				{
					if (!in_array($key, $enGbKeys))
					{
						unset($translatedLanguageItems[$key]);
						$changed = true;
					}
				}
				if ($changed)
				{
					$translatedRegistry = new JRegistry();
					$translatedRegistry->loadArray($translatedLanguageItems);
				}
			}
			else
			{
				$translatedLanguageItems = array();
			}
			$translatedLanguageKeys = array_keys($translatedLanguageItems);
			foreach ($enGbItems as $key => $value)
			{
				if (!in_array($key, $translatedLanguageKeys))
				{
					$translatedRegistry->set($key, $value);
				}
			}
			JFile::write($translatedPath, $translatedRegistry->toString('INI'));
		}
		if ($search)
		{
			$search = strtolower($search);
			foreach ($enGbItems as $key => $value)
			{
				if (strpos(strtolower($key), $search) === false && strpos(strtolower($value), $search) === false)
				{
					unset($enGbItems[$key]);
				}
			}
		}
		$this->total                  = count($enGbItems);
		$data['en-GB'][$languageFile] = array_slice($enGbItems, $this->state->limitstart, $this->state->limit);
		if ($language != 'en-GB')
		{
			$path = $languageFolder . $language . '/' . $language . '.' . $languageFile . '.ini';
			if (JFile::exists($path))
			{
				$registry->loadFile($path);
				$languageItems   = $registry->toArray();
				$translatedItems = array();
				foreach ($data['en-GB'][$languageFile] as $key => $value)
				{
					$translatedItems[$key] = isset($languageItems[$key]) ? $languageItems[$key] : '';
				}
				$data[$language][$languageFile] = $translatedItems;
			}
			else
			{
				$data[$language][$languageFile] = array();
			}
		}

		return $data;
	}

	/**
	 * Get site languages
	 *
	 * @return array
	 */
	public function getSiteLanguages()
	{
		jimport('joomla.filesystem.folder');
		$path    = JPATH_ROOT . '/language';
		$folders = JFolder::folders($path);
		$result  = array();
		foreach ($folders as $folder)
		{
			if ($folder != 'pdf_fonts' && $folder != 'overrides')
			{
				$result[] = $folder;
			}
		}

		return $result;
	}

	/**
	 * Get total number of language items
	 *
	 * @return int
	 */
	public function getTotal()
	{
		return $this->total;
	}

	/**
	 * Get pagination object
	 *
	 * @return JPagination
	 */
	public function getPagination()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->pagination))
		{
			jimport('joomla.html.pagination');
			$this->pagination = new JPagination($this->getTotal(), abs($this->state->limitstart), $this->state->limit);
		}

		return $this->pagination;
	}

	/**
	 * Save translation data
	 *
	 * @param $data
	 *
	 * @return bool
	 */
	public function save($data)
	{
		$language     = $this->state->filter_language;
		$languageFile = $this->state->filter_item;
		if (strpos($languageFile, 'admin') !== false)
		{
			$languageFolder = JPATH_ROOT . '/administrator/language/';
			$languageFile   = substr($languageFile, 6);
		}
		else
		{
			$languageFolder = JPATH_ROOT . '/language/';
		}
		$registry = new JRegistry();
		$filePath = $languageFolder . $language . '/' . $language . '.' . $languageFile . '.ini';

		if (JFile::exists($filePath))
		{
			$registry->loadFile($filePath, 'INI');
		}
		else
		{
			$registry->loadFile($languageFolder . 'en-GB/en-GB.' . $languageFile . '.ini', 'INI');
		}
		//Get the current language file and store it to array				
		$keys = $data['keys'];
		foreach ($keys as $key)
		{
			$key   = trim($key);
			$value = ltrim($data[$key]);
			$registry->set($key, $value);
		}
		if (isset($data['extra_keys']))
		{
			$keys   = $data['extra_keys'];
			$values = $data['extra_values'];
			for ($i = 0, $n = count($keys); $i < $n; $i++)
			{
				$key   = trim($keys[$i]);
				$value = ltrim($values[$i]);
				$registry->set($key, $value);
			}
		}
		JFile::write($filePath, $registry->toString('INI'));

		return true;
	}
}