<?php
/**
 * @version        2.2.1
 * @package        Joomla
 * @subpackage     OSMembership
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2016 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
// no direct access
defined('_JEXEC') or die();

class OSMembershipControllerProfile extends OSMembershipController
{
	/**
	 * Update user profile data
	 */
	public function update()
	{
		$Itemid     = $this->input->getInt('Itemid', 0);
		$data       = $this->input->getData();
		$model      = $this->getModel();
		$data['id'] = (int) $data['cid'][0];
		$model->updateProfile($data);

		//Redirect to the profile page
		$this->setRedirect(JRoute::_('index.php?option=com_osmembership&view=profile&Itemid=' . $Itemid), JText::_('OSM_YOUR_PROFILE_UPDATED'));
	}
}