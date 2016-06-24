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
		// do nothing if is not frontend
		if (!JFactory::getApplication()->isSite())
		{
			return false;
		}

		// return true if module is empty (this will empty the content)
		if ($this->isEmpty($module))
		{
			return true;
		}


		return false;
	}

	public function isEmpty(&$module)
	{
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
			// return true will prevent the module from outputting html
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
		$s    = '#<(' . $tags . ')([^a-z0-9>][^>]*)?>#si';
		if (@preg_match($s . 'u', $content))
		{
			$s .= 'u';
		}
		if (preg_match($s, $content))
		{
			$content = preg_replace($s, '', $content);
		}

		// return false if module is not empty
		if (trim($content) != '')
		{
			return false;
		}

		// return true will prevent the module from outputting html
		return true;
	}


	public function onCreateModuleQuery(&$query)
	{
		// return if is not frontend
		if (!JFactory::getApplication()->isSite())
		{
			return;
		}

		foreach ($query as $type => $strings)
		{
			foreach ($strings as $i => $string)
			{
				if ($type == 'select')
				{
					$query->{$type}[$i] = str_replace(', mm.menuid', '', $string);
				}
				else if (strpos($string, 'mm.') !== false || strpos($string, 'm.publish_') !== false)
				{
					unset($query->{$type}[$i]);
				}
			}
		}
		$query->select[] = 'am.params as advancedparams, 0 as menuid, m.publish_up, m.publish_down';
		$query->join[]   = '#__advancedmodules as am ON am.moduleid = m.id';
		$query->order    = array('m.ordering, m.id');
	}

	public function onPrepareModuleList(&$modules)
	{
		// return if is not frontend
		if (!JFactory::getApplication()->isSite() || empty($modules))
		{
			return;
		}

		jimport('joomla.filesystem.file');

		require_once JPATH_LIBRARIES . '/regularlabs/helpers/parameters.php';
		$parameters = RLParameters::getInstance();

		require_once JPATH_LIBRARIES . '/regularlabs/helpers/assignments.php';
		$assignments = new RLAssignmentsHelper;

		$xmlfile_assignments = JPATH_ADMINISTRATOR . '/components/com_advancedmodules/assignments.xml';

		// set params for all loaded modules first
		// and make it an associated array (array id = module id)
		$new_modules = array();
		require_once JPATH_ADMINISTRATOR . '/components/com_advancedmodules/models/module.php';
		$model = new AdvancedModulesModelModule;
		foreach ($modules as $id => $module)
		{
			if (!isset($module->advancedparams))
			{
				$module->advancedparams = $this->getAdvancedParamsById($id);
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

			$module->advancedparams   = $parameters->getParams($module->advancedparams, $xmlfile_assignments);
			$new_modules[$module->id] = $module;
		}
		$modules = $new_modules;
		unset($new_modules);

		foreach ($modules as $id => $module)
		{
			if ($module->advancedparams === 0)
			{
				continue;
			}

			$module->reverse = 0;

			if (!isset($module->published))
			{
				$module->published = 0;
			}

			if ($module->published)
			{
				$this->setMirrorParams($module, $xmlfile_assignments);
				$this->removeDisabledAssignments($module->advancedparams);

				$ass  = $assignments->getAssignmentsFromParams($module->advancedparams);
				$pass = $assignments->passAll($ass, $module->advancedparams->match_method);

				if (!$pass)
				{
					$module->published = 0;
				}

				if ($module->reverse)
				{
					$module->published = $module->published ? 0 : 1;
				}
			}

			$modules[$id] = $module;
		}
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

		$module->reverse = $mirror_id < 0;

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

	/**
	 * Function that gets the config settings
	 *
	 * @return    Object
	 */
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
