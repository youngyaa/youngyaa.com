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

defined('_JEXEC') or die;

jimport('joomla.filesystem.file');

/*
 * ModuleHelper methods
 */

class PlgSystemAdvancedModuleHelper
{
	var $advanced_params = array();
	var $mirror_ids      = array();

	public function onRenderModule(&$module)
	{
		// Module already nulled
		if (is_null($module))
		{
			return;
		}

		// Do nothing if is not frontend
		if (!JFactory::getApplication()->isSite())
		{
			return;
		}

		// return true if module is empty (this will empty the content)
		if ($this->isEmpty($module))
		{
			$module = null;

			return;
		}

	}

	public function isEmpty(&$module)
	{
		if (!isset($module->content))
		{
			return true;
		}

		$params = isset($module->adv_param) ? $module->adv_param : (isset($module->advancedparams) ? $module->advancedparams : null);

		// return false if hideempty is off in module params
		if (empty($params) || !isset($params->hideempty) || !$params->hideempty)
		{
			return false;
		}

		$config = $this->getConfig();

		// return false if show_hideempty is off in main config
		if (!$config->show_hideempty)
		{
			return false;
		}

		$content = trim($module->content);

		// return true if module is empty
		if ($content == '')
		{
			return true;
		}

		// remove html and hidden whitespace
		$content = str_replace(chr(194) . chr(160), ' ', $content);
		$content = str_replace(array('&nbsp;', '&#160;'), ' ', $content);
		// remove comment tags
		$content = preg_replace('#<\!--.*?-->#si', '', $content);
		// remove all closing tags
		$content = preg_replace('#</[^>]+>#si', '', $content);
		// remove tags to be ignored
		$tags = 'p|div|span|strong|b|em|i|ul|font|br|h[0-9]|fieldset|label|ul|ol|li|table|thead|tbody|tfoot|tr|th|td|form';
		$s    = '#<(?:' . $tags . ')(?:\s*[^>]*)?>#si';

		if (@preg_match($s . 'u', $content))
		{
			$s .= 'u';
		}

		if (preg_match($s, $content))
		{
			$content = preg_replace($s, '', $content);
		}

		// return whether content is empty
		return (trim($content) == '');
	}


	public function onPrepareModuleList(&$modules)
	{
		// return if is not frontend
		if (!JFactory::getApplication()->isSite())
		{
			return;
		}

		jimport('joomla.filesystem.file');

		require_once JPATH_LIBRARIES . '/regularlabs/helpers/parameters.php';
		$parameters = RLParameters::getInstance();

		require_once JPATH_LIBRARIES . '/regularlabs/helpers/assignments.php';
		$assignments_helper = new RLAssignmentsHelper;

		require_once JPATH_ADMINISTRATOR . '/components/com_advancedmodules/models/module.php';
		$model = new AdvancedModulesModelModule;

		$xmlfile_assignments = JPATH_ADMINISTRATOR . '/components/com_advancedmodules/assignments.xml';

		$modules = is_null($modules) ? $this->getModuleList() : $modules;

		if (is_array($modules) && empty($modules))
		{
			return;
		}

		$filtered_modules = array();

		foreach ($modules as $module)
		{
			$module->name = substr($module->module, 4);

			if (JFactory::getApplication()->input->get('option') == 'com_ajax')
			{
				$filtered_modules[] = $module;
				continue;
			}

			if (!isset($module->advancedparams))
			{
				$module->advancedparams = $this->getAdvancedParamsById($module->id);
			}

			$module->advancedparams = json_decode($module->advancedparams);
			if (is_null($module->advancedparams))
			{
				$module->advancedparams = new stdClass;
			}

			if (
				!isset($module->advancedparams->assignto_menuitems)
				|| isset($module->advancedparams->assignto_urls_selection_sef)
				|| (
					!is_array($module->advancedparams->assignto_menuitems)
					&& strpos($module->advancedparams->assignto_menuitems, '|') !== false
				)
			)
			{
				$module->advancedparams = (object) $model->initAssignments($module->id, $module);
			}

			$module->advancedparams = $parameters->getParams($module->advancedparams, $xmlfile_assignments);

			if ($module->advancedparams === 0)
			{
				if (isset($module->published) && !$module->published)
				{
					continue;
				}

				$filtered_modules[] = $module;

				continue;
			}


			$module->reverse = 0;

			$this->setMirrorParams($module, $xmlfile_assignments);

			$this->removeDisabledAssignments($module->advancedparams);

			AdvancedModules::setCurrentModule($module);

			$assignments       = $assignments_helper->getAssignmentsFromParams($module->advancedparams);
			$module->published = $assignments_helper->passAll(
				$assignments,
				$module->advancedparams->match_method
			);

			if ($module->reverse)
			{
				$module->published = !$module->published;
			}

			if (isset($module->published) && !$module->published)
			{
				continue;
			}

			$filtered_modules[] = $module;
		}

		$modules = array_values($filtered_modules);
		unset($filtered_modules);
	}

	private function setMirrorParams(&$module, $xmlfile_assignments)
	{
		$module->mirror_id = $this->getMirrorModuleId($module);

		if (empty($module->mirror_id))
		{
			return;
		}

		$parameters = RLParameters::getInstance();

		$mirror_id = $module->mirror_id < 0 ? $module->mirror_id * -1 : $module->mirror_id;

		$count = 0;
		while ($count++ < 10)
		{
			if (!$test_mirrorid = $this->getMirrorModuleIdById($mirror_id))
			{
				break;
			}

			$mirror_id = $test_mirrorid;
		}

		if (empty($mirror_id))
		{
			return;
		}

		$module->reverse = $module->mirror_id < 0;

		if ($mirror_id == $module->id)
		{
			$empty         = new stdClass;
			$mirror_params = $parameters->getParams($empty, $xmlfile_assignments);
		}
		else
		{
			if (isset($modules[$mirror_id]))
			{
				if (!isset($modules[$mirror_id]->advancedparams))
				{
					$modules[$mirror_id]->advancedparams = $this->getAdvancedParamsById($mirror_id);
					$modules[$mirror_id]->advancedparams = $parameters->getParams($modules[$mirror_id]->adv_param, $xmlfile_assignments);
				}
				$mirror_params = $modules[$mirror_id]->advancedparams;
			}
			else
			{
				$mirror_params = $this->getAdvancedParamsById($mirror_id);
				$mirror_params = $parameters->getParams($mirror_params, $xmlfile_assignments);
			}
		}

		// Keep the advanced settings that shouldn't be mirrored
		$settings_to_keep = array(
			'hideempty', 'color',
		);

		foreach ($settings_to_keep as $key)
		{
			if (!isset($module->advancedparams->{$key}))
			{
				continue;
			}

			$mirror_params->{$key} = $module->advancedparams->{$key};
		}

		$module->advancedparams = $mirror_params;
	}

	private function removeDisabledAssignments(&$params)
	{
		$config = $this->getConfig();

		if (!$config->show_assignto_homepage)
		{
			$params->assignto_homepage = 0;
		}
		if (!$config->show_assignto_usergrouplevels)
		{
			$params->assignto_usergrouplevels = 0;
		}
		if (!$config->show_assignto_date)
		{
			$params->assignto_date = 0;
		}
		if (!$config->show_assignto_languages)
		{
			$params->assignto_languages = 0;
		}
		if (!$config->show_assignto_templates)
		{
			$params->assignto_templates = 0;
		}
		if (!$config->show_assignto_urls)
		{
			$params->assignto_urls = 0;
		}
		if (!$config->show_assignto_os)
		{
			$params->assignto_os = 0;
		}
		if (!$config->show_assignto_browsers)
		{
			$params->assignto_browsers = 0;
		}
		if (!$config->show_assignto_components)
		{
			$params->assignto_components = 0;
		}
		if (!$config->show_assignto_tags)
		{
			$params->show_assignto_tags = 0;
		}
		if (!$config->show_assignto_content)
		{
			$params->assignto_contentpagetypes = 0;
			$params->assignto_cats             = 0;
			$params->assignto_articles         = 0;
		}
	}


	private function getModuleList()
	{
		$app      = JFactory::getApplication();
		$groups   = implode(',', JFactory::getUser()->getAuthorisedViewLevels());
		$lang     = JFactory::getLanguage()->getTag();
		$clientId = (int) $app->getClientId();

		$db = JFactory::getDbo();

		$query = $db->getQuery(true)
			->select('m.id, m.title, m.module, m.position, m.content, m.showtitle, m.params')
			->select('am.mirror_id, am.params as advancedparams, 0 as menuid, m.publish_up, m.publish_down')
			->from('#__modules AS m')
			->join('LEFT', '#__extensions AS e ON e.element = m.module AND e.client_id = m.client_id')
			->join('LEFT', '#__advancedmodules as am ON am.moduleid = m.id')
			->where('m.published = 1')
			->where('e.enabled = 1')
			->where('m.access IN (' . $groups . ')')
			->where('m.client_id = ' . $clientId);

		// Filter by language
		if ($app->isSite() && $app->getLanguageFilter())
		{
			$query->where('m.language IN (' . $db->quote($lang) . ',' . $db->quote('*') . ')');
		}

		$query->order('m.position, m.ordering');

		// Set the query
		$db->setQuery($query);

		try
		{
			$modules = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			JLog::add(JText::sprintf('JLIB_APPLICATION_ERROR_MODULE_LOAD', $e->getMessage()), JLog::WARNING, 'jerror');

			return array();
		}

		return array_values($modules);
	}

	private function getConfig()
	{
		static $instance;

		if (is_object($instance))
		{
			return $instance;
		}

		require_once JPATH_LIBRARIES . '/regularlabs/helpers/parameters.php';
		$parameters = RLParameters::getInstance();
		$instance   = $parameters->getComponentParams('advancedmodules');

		return $instance;
	}

	private function getMirrorModuleId($module)
	{
		if (isset($module->mirror_id))
		{
			return $module->mirror_id;
		}

		if (empty($module->advancedparams->mirror_module) || empty($module->advancedparams->mirror_id))
		{
			return 0;
		}

		return $this->getMirrorModuleIdById($module->id);
	}

	private function getMirrorModuleIdById($id)
	{

		if (isset($this->mirror_ids[$id]))
		{
			return $this->mirror_ids[$id];
		}

		$db    = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('a.mirror_id')
			->from('#__advancedmodules AS a')
			->where('a.moduleid = ' . (int) $id);
		$db->setQuery($query);

		$this->mirror_ids[$id] = $db->loadResult();

		return $this->mirror_ids[$id];
	}

	private function getAdvancedParamsById($id = 0)
	{
		if (isset($this->advanced_params[$id]))
		{
			return $this->advanced_params[$id];
		}

		$db    = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('a.params')
			->from('#__advancedmodules AS a')
			->where('a.moduleid = ' . (int) $id);
		$db->setQuery($query);

		$params = $db->loadResult();
		if (empty($params))
		{
			$params = '{}';
		}

		$this->advanced_params[$id] = $params;

		return $this->advanced_params[$id];
	}
}

class AdvancedModules
{
	static $current_module = null;

	public static function getCurrentModule()
	{
		return self::$current_module;
	}

	public static function setCurrentModule($module)
	{
		self::$current_module = $module;
	}
}
