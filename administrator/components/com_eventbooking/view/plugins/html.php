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
defined('_JEXEC') or die;

class EventbookingViewPluginsHtml extends RADViewList
{
	/**
	 * Override add toolbar method to add custom toolbar
	 */
	protected function addToolbar()
	{
		JToolBarHelper::title(JText::_('EB_PAYMENT_PLUGIN_MANAGEMENT'), 'generic.png');
		JToolBarHelper::publishList('publish');
		JToolBarHelper::unpublishList('unpublish');
		JToolBarHelper::deleteList(JText::_('Do you really want to uninstall the selected payment plugin?'), 'uninstall', 'Uninstall');
	}
}