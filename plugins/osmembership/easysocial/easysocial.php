<?php
/**
 * @version        2.2.1
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2016 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die;

class plgOSMembershipEasySocial extends JPlugin
{
	/**
	 * Constructor
	 *
	 * @param   object &$subject   The object to observe
	 * @param   array  $config     An optional associative array of configuration settings.
	 *                             Recognized key values include 'name', 'group', 'params', 'language'
	 *                             (this list is not meant to be comprehensive).
	 */
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);

		JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_osmembership/table');
		$this->canRun = file_exists(JPATH_ROOT . '/components/com_easysocial/easysocial.php');
	}

	/**
	 * Method to get list of custom fields in Jomsocial used to map with fields in Membership Pro
	 *
	 * Method is called on custom field add / edit page from backend of Membership Pro
	 *
	 * @return mixed
	 */
	public function onGetFields()
	{
		if ($this->canRun)
		{
			$db  = JFactory::getDbo();
			$sql = 'SELECT title AS `value`, title AS `text` FROM #__social_fields WHERE state=1 AND title != ""';
			$db->setQuery($sql);

			return $db->loadObjectList();
		}
	}

	/**
	 * Method to get data stored in Jomsocial profile of the given user
	 *
	 * @param int   $userId
	 * @param array $mappings
	 *
	 * @return array
	 */
	public function onGetProfileData($userId, $mappings = array())
	{
		if ($this->canRun)
		{
			$synchronizer = new MPFSynchronizerEasysocial();

			return $synchronizer->getData($userId, $mappings);
		}
	}

	/**
	 * Render settings form allows admin to choose what Jomsocial groups subscribers will be assigned to when they sign up for this plan
	 *
	 * Method is called on plan add/edit page
	 *
	 * @param OSMembershipPlan $row The plan record
	 *
	 * @return array
	 */
	function onEditSubscriptionPlan($row)
	{
		ob_start();
		$this->_drawSettingForm($row);
		$form = ob_get_contents();
		ob_end_clean();

		return array('title' => JText::_('PLG_OSMEMBERSHIP_EASYSOCIAL_SETTINGS'),
		             'form'  => $form
		);
	}

	/**
	 * Method to store settings into database
	 *
	 * @param OSMembershipPlan $row   The plan record
	 * @param array            $data  The form post data
	 * @param bool             $isNew True if new plan is created, false if updating the plan
	 */
	function onAfterSaveSubscriptionPlan($context, $row, $data, $isNew)
	{
		if (!$this->canRun)
		{
			return;
		}

		$params = new JRegistry($row->params);
		$params->set('easysocial_group_ids', implode(',', $data['easysocial_group_ids']));
		$params->set('easysocial_expried_group_ids', implode(',', $data['easysocial_expried_group_ids']));
		$row->params = $params->toString();

		$row->store();
	}

	/**
	 * Method to create Jomsocial account for subscriber and assign him to selected Jomsocial groups when subscription is active
	 *
	 * @param $row
	 *
	 * @return bool
	 */
	function onMembershipActive($row)
	{
		if (!$this->canRun)
		{
			return;
		}
		if ($row->user_id)
		{
			$db  = JFactory::getDbo();
			$sql = 'SELECT COUNT(*) FROM #__social_users WHERE user_id=' . $row->user_id;
			$db->setQuery($sql);
			$count = $db->loadResult();
			if (!$count)
			{
				$sql = 'INSERT INTO #__social_users(user_id) VALUES(' . $row->user_id . ')';
				$db->setQuery($sql);
				$db->query();
			}

			$sql = 'SELECT id, title FROM #__social_fields WHERE state=1 AND title != ""';
			$db->setQuery($sql);
			$rowFields = $db->loadObjectList();
			$fieldList = array();
			foreach ($rowFields as $rowField)
			{
				$fieldList[$rowField->fieldcode] = $rowField->id;
			}


			$sql = 'SELECT name, field_mapping FROM #__osmembership_fields WHERE field_mapping != "" AND field_mapping IS NOT NULL AND is_core = 1';
			$db->setQuery($sql);
			$fields      = $db->loadObjectList();
			$fieldValues = array();

			if (count($fields))
			{
				foreach ($fields as $field)
				{
					$fieldName = $field->field_mapping;
					if ($fieldName)
					{
						$fieldValues[$fieldName] = $row->{$field->name};
					}
				}
			}

			$sql = 'SELECT a.field_mapping, b.field_value FROM #__osmembership_fields AS a '
				. ' INNER JOIN #__osmembership_field_value AS b '
				. ' ON a.id = b.field_id '
				. ' WHERE b.subscriber_id=' . $row->id;
			$db->setQuery($sql);
			$fields = $db->loadObjectList();
			if (count($fields))
			{
				foreach ($fields as $field)
				{
					if ($field->field_mapping)
					{
						$fieldValues[$field->field_mapping] = $field->field_value;
					}
				}
			}

			if (count($fieldValues))
			{
				foreach ($fieldValues as $fieldCode => $fieldValue)
				{
					if (isset($fieldList[$fieldCode]))
					{
						$fieldId = $fieldList[$fieldCode];
						if ($fieldId)
						{
							$fieldValue = $db->quote($fieldValue);
							$sql        = "INSERT INTO #__social_fields_data(uid, field_id, `data`) VALUES($row->user_id, $fieldId, $fieldValue)";
							$db->setQuery($sql);
							$db->query();
							echo $db->getQuery();
						}
					}
				}
			}

			$plan = JTable::getInstance('Osmembership', 'Plan');
			$plan->load($row->plan_id);
			$params = new JRegistry($plan->params);
			$groups = explode(',', $params->get('easysocial_group_ids'));
			if (count($groups))
			{
				$sql = 'REPLACE INTO `#__social_clusters_nodes` (`cluster_id`,`uid`,`type`,`created`,`state`,`owner`,`admin`,`invited_by`) VALUES ';
				
				$values = array();
				foreach ($groups as $group)
				{
					$values[] = '(' . $db->Quote($group) . ', ' . $db->Quote($row->user_id) . ','. $db->Quote('user') .','. $db->Quote(JFactory::getDate()) .', 1, 1, 0, 0)';
				}
				
				$sql .= implode(', ', $values);

				$db->setQuery($sql);
				$db->execute();
			}
		}

		return true;
	}


	/**
	 * Run when a membership activated
	 *
	 * @param PlanOsMembership $row
	 */
	function onProfileUpdate($row)
	{
		if (!$this->canRun)
		{
			return;
		}
		if ($row->user_id)
		{
			$db  = JFactory::getDbo();
			$sql = 'SELECT COUNT(*) FROM #__social_users WHERE user_id=' . $row->user_id;
			$db->setQuery($sql);
			$count = $db->loadResult();

			if (!$count)
			{
				$sql = 'INSERT INTO #__social_users(user_id) VALUES(' . $row->user_id . ')';
				$db->setQuery($sql);
				$db->query();
			}

			$sql = 'SELECT id, title FROM #__social_fields WHERE state=1 AND title != ""';
			$db->setQuery($sql);
			$rowFields = $db->loadObjectList();
			$fieldList = array();
			foreach ($rowFields as $rowField)
			{
				$fieldList[$rowField->title] = $rowField->id;
			}

			$sql = 'SELECT name, field_mapping FROM #__osmembership_fields WHERE field_mapping != "" AND field_mapping IS NOT NULL AND is_core = 1';
			$db->setQuery($sql);
			$fields      = $db->loadObjectList();
			$fieldValues = array();

			if (count($fields))
			{
				foreach ($fields as $field)
				{
					$fieldName = $field->field_mapping;
					if ($fieldName)
					{
						$fieldValues[$fieldName] = $row->{$field->name};
					}
				}
			}

			$sql = 'SELECT a.field_mapping, b.field_value FROM #__osmembership_fields AS a '
				. ' INNER JOIN #__osmembership_field_value AS b '
				. ' ON a.id = b.field_id '
				. ' WHERE b.subscriber_id=' . $row->id;
			$db->setQuery($sql);
			$fields = $db->loadObjectList();
			if (count($fields))
			{
				foreach ($fields as $field)
				{
					if ($field->field_mapping)
					{
						$fieldValues[$field->field_mapping] = $field->field_value;
					}
				}
			}

			if (count($fieldValues))
			{
				foreach ($fieldValues as $fieldCode => $fieldValue)
				{
					if (isset($fieldList[$fieldCode]))
					{
						$fieldId = $fieldList[$fieldCode];
						if ($fieldId)
						{
							$fieldValue = $db->quote($fieldValue);
							$sql        = "REPLACE INTO #__social_fields_data(field_id, uid, `data`) VALUES($fieldId, $row->user_id, $fieldValue)";
							$db->setQuery($sql);
							$db->query();
							echo $db->getQuery();
						}
					}
				}
			}

			$plan =  & JTable::getInstance('Osmembership', 'Plan');
			$plan->load($row->plan_id);
			$params = new JRegistry($plan->params);
			$groups = explode(',', $params->get('easysocial_group_ids'));
			if (count($groups))
			{
				$sql = 'REPLACE INTO `#__social_clusters_nodes` (`cluster_id`,`uid`,`type`,`created`,`state`,`owner`,`admin`,`invited_by`) VALUES ';

				$values = array();
				foreach ($groups as $group)
				{
					$values[] = '(' . $db->Quote($group) . ', ' . $db->Quote($row->user_id) . ','. $db->Quote('user') .','. $db->Quote(JFactory::getDate()) .', 1, 1, 0, 0)';
				}

				$sql .= implode(', ', $values);

				$db->setQuery($sql);
				$db->query();
			}
		}

		return true;
	}

	/**
	 * Run when a membership expiried die
	 *
	 * @param PlanOsMembership $row
	 */
	function onMembershipExpire($row)
	{
		if (!$this->canRun)
		{
			return;
		}
		if ($row->user_id)
		{
			$plan =  & JTable::getInstance('Osmembership', 'Plan');
			$plan->load($row->plan_id);
			$params = new JRegistry($plan->params);
			$groups = explode(',', $params->get('easysocial_expried_group_ids'));
			if (count($groups))
			{
				$db = JFactory::getDbo();
				foreach ($groups as $group)
				{
					$group = (int) $group;
					if ($group)
					{
						$sql = 'DELETE FROM #__social_clusters_nodes WHERE id=' . $group . ' AND uid=' . $row->user_id;
						$db->setQuery($sql);
						$db->query();
					}
				}
			}
		}
	}

	/**
	 * Display form allows users to change setting for this subscription plan
	 *
	 * @param object $row
	 *
	 */
	function _drawSettingForm($row)
	{
		if (!$this->canRun)
		{
			return;
		}
		$params                      = new JRegistry($row->params);
		$easysocial_group_ids         = explode(',', $params->get('easysocial_group_ids', ''));
		$easysocial_expried_group_ids = explode(',', $params->get('easysocial_expried_group_ids', ''));

		$db  = JFactory::getDbo();
		$sql = 'SELECT id, title AS name FROM #__social_clusters WHERE state = 1 ORDER BY title ';
		$db->setQuery($sql);
		$options   = array();
		$options[] = JHTML::_('select.option', 0, JText::_('Choose Groups'), 'id', 'name');
		$options   = array_merge($options, $db->loadObjectList());
		?>
		<table class="admintable adminform" style="width: 90%;">
			<tr>
				<td width="220" class="key">
					<?php echo JText::_('PLG_OSMEMBERSHIP_EASYSOCIAL_ASSIGN_TO_GROUPS'); ?>
				</td>
				<td>
					<?php
					echo JHTML::_('select.genericlist', $options, 'easysocial_group_ids[]', ' multiple="multiple" size="6" ', 'id', 'name', $easysocial_group_ids);
					?>
				</td>
				<td>
					<?php echo JText::_('PLG_OSMEMBERSHIP_EASYSOCIAL_ASSIGN_TO_GROUPS_EXPLAIN'); ?>
				</td>
			</tr>
			<tr>
				<td width="220" class="key">
					<?php echo JText::_('PLG_OSMEMBERSHIP_EASYSOCIAL_REMOVE_FROM_GROUPS'); ?>
				</td>
				<td>
					<?php
					echo JHTML::_('select.genericlist', $options, 'easysocial_expried_group_ids[]', ' multiple="multiple" size="6" ', 'id', 'name', $easysocial_expried_group_ids);
					?>
				</td>
				<td>
					<?php echo JText::_('PLG_OSMEMBERSHIP_EASYSOCIAL_REMOVE_FROM_GROUPS_EXPLAIN'); ?>
				</td>
			</tr>
		</table>
	<?php
	}
}	