<?php
/**
 * @package         Advanced Module Manager
 * @version         6.0.1
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            http://www.regularlabs.com
 * @copyright       Copyright © 2016 Regular Labs All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

/**
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Modules Component Module Model
 */
class AdvancedModulesModelModules extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param   array $config An optional associative array of configuration settings.
	 *
	 * @see     JController
	 * @since   1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'color', 'a.color',
				'title', 'a.title',
				'checked_out', 'a.checked_out',
				'checked_out_time', 'a.checked_out_time',
				'published', 'a.published', 'state',
				'access', 'a.access', 'access_level',
				'ordering', 'a.ordering',
				'module', 'a.module',
				'language', 'a.language', 'language_title',
				'publish_up', 'a.publish_up',
				'publish_down', 'a.publish_down',
				'client_id', 'a.client_id',
				'position', 'a.position',
				'menuid',
				'name', 'e.name',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string $ordering  An optional ordering field.
	 * @param   string $direction An optional direction (asc|desc).
	 *
	 * @return  void
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication('administrator');

		// Load the filter state.
		$search = trim($this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search'));
		$this->setState('filter.search', $search);

		$access = $this->getUserStateFromRequest($this->context . '.filter.access', 'filter_access');
		$this->setState('filter.access', $access);

		$state = $this->getUserStateFromRequest($this->context . '.filter.state', 'filter_state', '', 'string');
		$this->setState('filter.state', $state);

		$position = $this->getUserStateFromRequest($this->context . '.filter.position', 'filter_position', '', 'string');
		$this->setState('filter.position', $position);

		$module = $this->getUserStateFromRequest($this->context . '.filter.module', 'filter_module', '', 'string');
		$this->setState('filter.module', $module);

		// Special handling for filter client_id.

		// Try to get current Client selection from $_POST.
		$clientId = $app->input->getString('client_id', null);

		// Client Site(0) or Administrator(1) selected?
		if (in_array($clientId, array('0', '1')))
		{
			// Not the same client like saved previous one?
			if ($clientId != $app->getUserState($this->context . '.client_id'))
			{
				// Save current selection as new previous value in session.
				$app->setUserState($this->context . '.client_id', $clientId);

				// Reset pagination.
				$app->input->set('limitstart', 0);
			}
		}

		// No Client selected?
		else
		{
			// Try to get previous one from session.
			$clientId = (string) $app->getUserState($this->context . '.client_id');

			// Client not Site(0) and not Administrator(1)? So, set to Site(0).
			if (!in_array($clientId, array('0', '1')))
			{
				$clientId = '0';
			}
		}

		// Modal view should return only front end modules
		if (JFactory::getApplication()->input->get('layout') == 'modal')
		{
			$clientId = 0;
		}

		$this->setState('filter.client_id', $clientId);

		$language = $this->getUserStateFromRequest($this->context . '.filter.language', 'filter_language', '');
		$this->setState('filter.language', $language);

		// Load the parameters.
		$params = JComponentHelper::getParams('com_advancedmodules');
		$this->setState('params', $params);

		// List state information.
		$this->getConfig();
		list($default_ordering, $default_direction) = explode(' ', $this->config->default_ordering, 2);

		$this->setState('list.fullordering', $this->config->default_ordering);
		parent::populateState($default_ordering, $default_direction);
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return    mixed    The data for the form.
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState($this->context, new stdClass);

		// Pre-fill the list options
		if (!property_exists($data, 'list'))
		{
			$this->getConfig();
			list($default_ordering, $default_direction) = explode(' ', $this->config->default_ordering, 2);

			$data->list = array(
				'direction'    => $default_direction,
				'limit'        => $this->state->{'list.limit'},
				'ordering'     => $default_ordering,
				'fullordering' => $this->config->default_ordering,
				'start'        => $this->state->{'list.start'},
			);
		}

		return $data;
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string $id A prefix for the store id.
	 *
	 * @return  string    A store id.
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . trim($this->getState('filter.search'));
		$id .= ':' . $this->getState('filter.access');
		$id .= ':' . $this->getState('filter.state');
		$id .= ':' . $this->getState('filter.position');
		$id .= ':' . $this->getState('filter.module');
		$id .= ':' . $this->getState('filter.menuid');
		$id .= ':' . $this->getState('filter.client_id');
		$id .= ':' . $this->getState('filter.language');

		return parent::getStoreId($id);
	}

	/**
	 * Returns an object list
	 *
	 * @param   string $query      The query
	 * @param   int    $limitstart Offset
	 * @param   int    $limit      The number of records
	 *
	 * @return  array
	 */
	protected function _getList($query, $limitstart = 0, $limit = 0)
	{
		$ordering  = strtolower($this->getState('list.ordering', 'ordering'));
		$orderDirn = strtoupper($this->getState('list.direction', 'ASC'));

		if (in_array($ordering, array('menuid', 'name')))
		{
			$this->_db->setQuery($query);
			$result = $this->_db->loadObjectList();
			$this->translate($result);
			JArrayHelper::sortObjects($result, $ordering, $orderDirn == 'DESC' ? -1 : 1, true, true);
			$total                                      = count($result);
			$this->cache[$this->getStoreId('getTotal')] = $total;

			if ($total < $limitstart)
			{
				$limitstart = 0;
				$this->setState('list.start', 0);
			}

			return array_slice($result, $limitstart, $limit ? $limit : null);
		}

		if ($ordering != 'color')
		{
			if ($ordering == 'ordering')
			{
				$query->order('a.position ASC');
				$ordering = 'a.ordering';
			}

			if ($ordering == 'language_title')
			{
				$ordering = 'l.manifest_cache';
			}

			$query->order($this->_db->quoteName($ordering) . ' ' . $orderDirn);

			if ($ordering == 'position')
			{
				$query->order('a.ordering ASC');
			}

			$result = parent::_getList($query, $limitstart, $limit);
			$this->translate($result);

			return $result;
		}

		$this->_db->setQuery($query);
		$result = $this->_db->loadObjectList();
		$this->translate($result);
		$newresult = array();

		foreach ($result as $i => $row)
		{
			$params = json_decode($row->advancedparams);
			if (is_null($params))
			{
				$params = new stdClass;
			}

			$color                                              = isset($params->color) ? str_replace('#', '', $params->color) : 'none';
			$color                                              = empty($color) ? 'none' : $color;
			$newresult['_' . $color . '_' . (($i + 1) / 10000)] = $row;
		}

		if ($orderDirn == 'DESC')
		{
			krsort($newresult);
		}
		else
		{
			ksort($newresult);
		}

		$newresult                                  = array_values($newresult);
		$total                                      = count($newresult);
		$this->cache[$this->getStoreId('getTotal')] = $total;

		if ($total < $limitstart)
		{
			$limitstart = 0;
			$this->setState('list.start', 0);
		}

		return array_slice($newresult, $limitstart, $limit ? $limit : null);
	}

	/**
	 * Translate a list of objects
	 *
	 * @param   array &$items The array of objects
	 *
	 * @return  array The array of translated objects
	 */
	protected function translate(&$items)
	{
		$lang   = JFactory::getLanguage();
		$client = $this->getState('filter.client_id') ? 'administrator' : 'site';

		foreach ($items as $item)
		{
			$extension = $item->module;
			$source    = constant('JPATH_' . strtoupper($client)) . "/modules/$extension";
			$lang->load("$extension.sys", constant('JPATH_' . strtoupper($client)), null, false, true)
			|| $lang->load("$extension.sys", $source, null, false, true);
			$item->name = JText::_($item->name);

			if ($item->mirror_id > 0)
			{
				$item->menuid = JText::sprintf(
					'AMM_MIRRORING_MODULE',
					'[<a href="' . JRoute::_('index.php?option=com_advancedmodules&task=module.edit&id=' . (int) $item->mirror_id) . '">'
					. $item->mirror_id . '</a>]'
				);
				continue;
			}

			if ($item->mirror_id < 0)
			{
				$item->menuid = JText::sprintf(
					'AMM_MIRRORING_MODULE_OPPOSITE',
					'[<a href="' . JRoute::_('index.php?option=com_advancedmodules&task=module.edit&id=' . (int) ($item->mirror_id * -1)) . '">'
					. ($item->mirror_id * -1) . '</a>]'
				);
				continue;
			}

			if (is_null($item->menuid))
			{
				$item->menuid = JText::_('JNONE');
				continue;
			}

			if ($item->menuid < 0)
			{
				$item->menuid = JText::_('COM_MODULES_ASSIGNED_VARIES_EXCEPT');
				continue;
			}

			if ($item->menuid > 0)
			{
				$item->menuid = JText::_('COM_MODULES_ASSIGNED_VARIES_ONLY');
				continue;
			}

			$item->menuid = JText::_('JALL');
		}
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  JDatabaseQuery
	 */
	protected function getListQuery()
	{
		$db = $this->getDbo();

		$query = $db->getQuery(true)
			// Select the required fields from the table.
			->select('a.id')
			->from('#__modules AS a')
			// Join over the module menus
			->join('LEFT', '#__modules_menu AS mm ON mm.moduleid = a.id')
			// Join over the extensions
			->join('LEFT', '#__extensions AS e ON e.element = a.module');

		// Filter by module
		$module = $this->getState('filter.module');
		if ($module)
		{
			$query->where('a.module = ' . $db->quote($module));
		}

		$wheres = array();

		// Filter by menuid
		$menuid = $this->getState('filter.menuid');

		switch ($menuid)
		{
			case '':
				break;
			case '0':
				$wheres[] = 'mm.menuid = 0';
				break;
			case '-':
				$wheres[] = 'mm.menuid IS NULL';
				break;
			case '-1':
				$wheres[] = 'mm.menuid LIKE \'-%\'';
				break;
			case '-2':
				$wheres[] = 'mm.menuid NOT LIKE \'-%\' AND mm.menuid != 0';
				break;
			default:
				$wheres[] = '(mm.menuid IN (0, ' . (int) $menuid . ') OR (mm.menuid LIKE \'-%\' AND mm.menuid != ' . $db->quote('-' . (int) $menuid) . '))';
				break;
		}

		// Filter by position
		if ($position = $this->getState('filter.position'))
		{
			$wheres[] = 'a.position = ' . $db->quote($position != 'none' ? $position : '');
		}

		// Filter by access level.
		if ($access = $this->getState('filter.access'))
		{
			$wheres[] = 'a.access = ' . (int) $access;
		}

		// Filter on the language.
		if ($language = $this->getState('filter.language'))
		{
			$wheres[] = 'a.language = ' . $db->quote($language);
		}

		// Filter by published state
		$published = $this->getState('filter.state');

		// Modal view should return only front end modules
		if (JFactory::getApplication()->input->get('layout') == 'modal')
		{
			$published = 1;
		}

		if (is_numeric($published))
		{
			$wheres[] = 'a.published = ' . (int) $published;
		}
		elseif ($published == '')
		{
			$wheres[] = '(a.published IN (0, 1))';
		}

		// Filter by client.
		$clientId = $this->getState('filter.client_id');

		// Modal view should return only front end modules
		if (JFactory::getApplication()->input->get('layout') == 'modal')
		{
			$clientId = 0;
		}

		if (is_numeric($clientId))
		{
			$wheres[] = 'a.client_id = ' . (int) $clientId . ' AND e.client_id =' . (int) $clientId;
		}

		// Modal view should return only specific language and ALL
		if (JFactory::getApplication()->input->get('layout') == 'modal')
		{
			if (JFactory::getApplication()->isSite() && JLanguageMultilang::isEnabled())
			{
				$query->where('a.language in (' . $db->quote(JFactory::getLanguage()->getTag()) . ',' . $db->quote('*') . ')');
			}
		}
		elseif ($language = $this->getState('filter.language'))
		{
			// Filter on the language.
			$query->where('a.language = ' . $db->quote($language));
		}

		// Set wheres
		foreach ($wheres as $where)
		{
			$query->where($where);
		}

		// Filter by search in title
		$search = trim($this->getState('filter.search'));

		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
				$query->where('(' . 'a.title LIKE ' . $search . ' OR a.note LIKE ' . $search . ')');
			}
		}

		$db->setQuery($query);
		$ids = $db->loadColumn();

		foreach ($ids as $key => $id)
		{
			if (JFactory::getUser()->authorise('core.edit', 'com_modules.module.' . $id))
			{
				continue;
			}

			unset($ids[$key]);
		}

		if (!empty($ids))
		{
			$mirror_wheres = $wheres;
			array_unshift(
				$mirror_wheres,
				'aa.mirror_id IN (' . implode(',', $ids) . ',-' . implode(',-', $ids) . ')'
			);

			$query->clear('where');

			// Join advanced params
			$query->join('LEFT', '#__advancedmodules AS aa ON aa.moduleid = a.id');

			// Set wheres
			foreach ($mirror_wheres as $where)
			{
				$query->where($where);
			}

			$db->setQuery($query);
			$mirror_ids = $db->loadColumn();

			$mirror_ids = array_unique($mirror_ids);

			foreach ($mirror_ids as $key => $id)
			{
				if (JFactory::getUser()->authorise('core.edit', 'com_modules.module.' . $id))
				{
					continue;
				}

				unset($mirror_ids[$key]);
			}

			$ids = array_merge($ids, $mirror_ids);
		}

		$query = $db->getQuery(true)
			// Select the required fields from the table.
			->select(
				$this->getState(
					'list.select',
					'a.id, a.title, a.note, a.position, a.module, a.language,' .
					'a.checked_out, a.checked_out_time, a.published as published, e.enabled as enabled, a.access, a.ordering, a.publish_up, a.publish_down'
				)
			)
			->from('#__modules AS a')
			// Join over the language
			->select('l.manifest_cache AS language_params')
			->join('LEFT', '#__extensions AS l ON l.element = a.language')
			// Join over the users for the checked out user.
			->select('uc.name AS editor')
			->join('LEFT', '#__users AS uc ON uc.id = a.checked_out')
			// Join over the asset groups.
			->select('ag.title AS access_level')
			->join('LEFT', '#__viewlevels AS ag ON ag.id = a.access')
			// Join over the module menus
			->select('MIN(mm.menuid) AS menuid')
			->join('LEFT', '#__modules_menu AS mm ON mm.moduleid = a.id')
			// Join over the extensions
			->select('e.name AS name')
			->join('LEFT', '#__extensions AS e ON e.element = a.module')
			// Join over the advanced params
			->select('aa.params AS advancedparams')
			->select('aa.mirror_id AS mirror_id')
			->join('LEFT', '#__advancedmodules AS aa ON aa.moduleid = a.id')
			// Group
			->group(
				'a.id, a.title, a.note, a.position, a.module, a.language, a.checked_out,' .
				'a.checked_out_time, a.published, a.access, a.ordering, uc.name, ag.title, e.name,' .
				'uc.id, ag.id, mm.moduleid, e.element, a.publish_up, a.publish_down, e.enabled'
			);

		if (empty($ids))
		{
			$query->where('1 = 0');

			return $query;
		}

		$ids = array_unique($ids);

		$query->where('a.id IN (' . implode(',', $ids) . ')');

		return $query;
	}

	/**
	 * Function that gets the config settings
	 *
	 * @return    Object
	 */
	protected function getConfig()
	{
		if (isset($this->config))
		{
			return $this->config;
		}

		require_once JPATH_LIBRARIES . '/regularlabs/helpers/parameters.php';
		$parameters   = RLParameters::getInstance();
		$this->config = $parameters->getComponentParams('advancedmodules');

		return $this->config;
	}
}
