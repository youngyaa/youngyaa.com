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

defined('JPATH_BASE') or die;

JFormHelper::loadFieldClass('list');

require_once __DIR__ . '/../../helpers/modules.php';

class JFormFieldModulesMenuId extends JFormFieldList
{
	protected $type = 'ModulesMenuId';

	public function getOptions()
	{
		$clientId = JFactory::getApplication()->input->get('client_id', 0, 'int');
		$options  = ModulesHelper::getMenuItemAssignmentOptions($clientId);

		return array_merge(parent::getOptions(), $options);
	}
}
