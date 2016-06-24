<?php
/**
 * @package         Regular Labs Library
 * @version         16.5.10919
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            http://www.regularlabs.com
 * @copyright       Copyright © 2016 Regular Labs All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

require_once dirname(__DIR__) . '/assignment.php';

class RLAssignmentsAkeebaSubs extends RLAssignment
{
	function init()
	{
		if (!$this->request->id && $this->request->view == 'level')
		{
			$slug = JFactory::getApplication()->input->getString('slug', '');
			if ($slug)
			{
				$query = $this->db->getQuery(true)
					->select('l.akeebasubs_level_id')
					->from('#__akeebasubs_levels AS l')
					->where('l.slug = ' . $this->db->quote($slug));
				$this->db->setQuery($query);
				$this->request->id = $this->db->loadResult();
			}
		}
	}

	function passPageTypes()
	{
		return $this->passByPageTypes('com_akeebasubs', $this->selection, $this->assignment);
	}

	function passLevels()
	{
		if (!$this->request->id || $this->request->option != 'com_akeebasubs' || $this->request->view != 'level')
		{
			return $this->pass(false);
		}

		return $this->passSimple($this->request->id);
	}
}
