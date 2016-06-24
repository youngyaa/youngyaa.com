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

class AdvancedModulesController extends JControllerLegacy
{
	protected $default_view = 'edit';

	public function display($cachable = false, $urlparams = false)
	{
		return parent::display();
	}

	public function __construct($config = array())
	{
		$this->input = JFactory::getApplication()->input;

		// Modules frontpage Editor Module proxying:
		if ($this->input->get('view') === 'modules' && $this->input->get('layout') === 'modal')
		{
			JHtml::_('stylesheet', 'system/adminlist.css', array(), true);
			$config['base_path'] = JPATH_COMPONENT_ADMINISTRATOR;
		}

		parent::__construct($config);
	}
}
