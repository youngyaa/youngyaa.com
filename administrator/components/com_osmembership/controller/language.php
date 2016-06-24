<?php
/**
 * @version        2.2.1
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2016 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
// no direct access
defined('_JEXEC') or die();

/**
 * Membership Pro controller
 *
 * @package        Joomla
 * @subpackage     Membership Pro
 */
class OSMembershipControllerLanguage extends OSMembershipController
{

	public function save()
	{
		$model = $this->getModel('language');
		$data  = $this->input->getData();
		$model->save($data);
		$task = $this->getTask();
		$msg  = JText::_('Traslation saved');
		if ($task == 'apply')
		{
			$lang = $data['filter_language'];
			$item = $data['filter_item'];
			$this->setRedirect('index.php?option=com_osmembership&view=language&filter_language=' . $lang . '&filter_item=' . $item, $msg);
		}
		else
		{
			$this->setRedirect('index.php?option=com_osmembership&view=' . $this->config['default_view'], $msg);
		}
	}

	/**
	 * Redirect back to default view afters users cancel an action
	 */
	public function cancel()
	{
		$this->setRedirect('index.php?option=com_osmembership&view=' . $this->config['default_view']);
	}
}