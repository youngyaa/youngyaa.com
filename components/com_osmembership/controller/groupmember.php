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

class OSMembershipControllerGroupmember extends OSMembershipController
{
	/**
	 * Method to allow adding new member to a group
	 */
	public function save()
	{
		$this->csrfProtection();
		$post      = $this->input->post->getData();
		$memberId  = (int) $post['cid'][0];
		$canManage = OSMembershipHelper::getManageGroupMemberPermission();
		if (($memberId && $canManage >= 1) || ($canManage == 2))
		{
			$model = $this->getModel('groupmember');

			if ($post['user_id'] > 0)
			{
				// Try to check if this user is a member of the plan
				$db    = JFactory::getDbo();
				$query = $db->getQuery(true);
				$query->select('COUNT(*)')
					->from('#__osmembership_subscribers')
					->where('user_id = ' . (int) $post['user_id'])
					->where('plan_id = ' . (int) $post['plan_id']);
				$db->setQuery($query);
				$total = $db->loadResult();
				if ($total > 0)
				{
					// This user is a group member of the plan already
					$this->setRedirect(JRoute::_('index.php?option=com_osmembership&view=groupmember&Itemid = ' . $this->input->getInt('Itemid', 0), JText::_('OSM_USER_IS_GROUP_MEMBER_ALREADY'), 'warning'));

					return;
				}
			}
			$post['id']       = (int) $post['cid'][0];
			$post['password'] = $post['password1'];
			$model->store($post);
			$Itemid = OSMembershipHelperRoute::findView('groupmembers', $this->input->getInt('Itemid', 0));
			$this->setRedirect(JRoute::_('index.php?option=com_osmembership&view=groupmembers&Itemid=' . $Itemid), JText::_('OSM_GROUP_MEMBER_WAS_SUCCESSFULL_CREATED'));
		}
		else
		{
			$this->setRedirect('index.php', JText::_('OSM_NOT_ALLOW_TO_MANAGE_GROUP_MEMBERS'));
		}
	}

	/**
	 * Delete a member from group
	 */
	public function delete()
	{
		$this->csrfProtection();
		$canManage = OSMembershipHelper::getManageGroupMemberPermission();
		if ($canManage >= 1)
		{
			$id     = $this->input->getInt('member_id', 0);
			$Itemid = $this->input->getInt('Itemid', 0);
			$model  = $this->getModel('groupmember');
			$model->deleteMember($id);
			$this->setRedirect(JRoute::_('index.php?option=com_osmembership&view=groupmembers&Itemid=' . $Itemid), JText::_('OSM_GROUP_MEMBER_WAS_SUCCESSFULL_DELETED'));
		}
		else
		{
			$this->setRedirect('index.php', JText::_('OSM_NOT_ALLOW_TO_MANAGE_GROUP_MEMBERS'));
		}
	}

	/**
	 * Get profile data of the subscriber, using for json format
	 *
	 */
	function get_member_data()
	{
		// Check permission
		$canManage = OSMembershipHelper::getManageGroupMemberPermission();
		if ($canManage >= 1)
		{
			$input  = $this->input;
			$userId = $input->getInt('user_id', 0);
			$planId = $input->getInt('plan_id');
			$data   = array();
			if ($userId)
			{
				$rowFields = OSMembershipHelper::getProfileFields($planId, true);
				$db        = JFactory::getDbo();
				$query     = $db->getQuery(true);
				$query->clear();
				$query->select('*')
					->from('#__osmembership_subscribers')
					->where('user_id=' . $userId);
				$db->setQuery($query);
				$rowProfile = $db->loadObject();
				$data       = array();
				if ($rowProfile)
				{
					$data = OSMembershipHelper::getProfileData($rowProfile, $planId, $rowFields);
				}
				else
				{
					// Trigger plugin to get data
					$mappings = array();
					foreach ($rowFields as $rowField)
					{
						if ($rowField->field_mapping)
						{
							$mappings[$rowField->name] = $rowField->field_mapping;
						}
					}
					JPluginHelper::importPlugin('osmembership');
					$dispatcher = JDispatcher::getInstance();
					$results    = $dispatcher->trigger('onGetProfileData', array($userId, $mappings));
					if (count($results))
					{
						foreach ($results as $res)
						{
							if (is_array($res) && count($res))
							{
								$data = $res;
								break;
							}
						}
					}
				}
				if (!count($data) && JPluginHelper::isEnabled('user', 'profile'))
				{
					$synchronizer = new MPFSynchronizerJoomla();
					$mappings     = array();
					foreach ($rowFields as $rowField)
					{
						if ($rowField->profile_field_mapping)
						{
							$mappings[$rowField->name] = $rowField->profile_field_mapping;
						}
					}
					$data = $synchronizer->getData($userId, $mappings);
				}
			}

			if ($userId && !isset($data['first_name']))
			{
				//Load the name from Joomla default name
				$user = JFactory::getUser($userId);
				$name = $user->name;
				if ($name)
				{
					$pos = strpos($name, ' ');
					if ($pos !== false)
					{
						$data['first_name'] = substr($name, 0, $pos);
						$data['last_name']  = substr($name, $pos + 1);
					}
					else
					{
						$data['first_name'] = $name;
						$data['last_name']  = '';
					}
				}
			}
			if ($userId && !isset($data['email']))
			{
				$user          = JFactory::getUser($userId);
				$data['email'] = $user->email;
			}
			echo json_encode($data);
			$this->app->close();
		}
	}
}