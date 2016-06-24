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

/**
 * HTML View class for Membership Pro component
 *
 * @static
 * @package        Joomla
 * @subpackage     Membership Pro
 */
class OSMembershipViewFieldHtml extends MPFViewItem
{

	protected function prepareView()
	{
		parent::prepareView();

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$item = $this->item;
		$lists = &$this->lists;

		$fieldTypes = array(
			'Text',
			'Textarea',
			'List',
			'Checkboxes',
			'Radio',
			'Date',
			'Heading',
			'Message',
			'File',
			'Countries',
			'State',
			'SQL'
		);		
		$options = array();
		$options[] = JHtml::_('select.option', -1, JText::_('OSM_FIELD_TYPE'));
		foreach ($fieldTypes as $fieldType)
		{
			$options[] = JHtml::_('select.option', $fieldType, $fieldType);
		}
		if ($item->is_core)
		{
			$readOnly = ' readonly="true" ';
		}			
		else
		{
			$readOnly = '';
		}					
		$lists['fieldtype'] = JHtml::_('select.genericlist', $options, 'fieldtype', ' class="inputbox" ' . $readOnly, 'value', 'text',
			$item->fieldtype);

		$query->select('id, title')
			->from('#__osmembership_plans')
			->where('published = 1')
			->order('ordering');

		$db->setQuery($query);
		$options = array();
		$options[] = JHtml::_('select.option', 0, JText::_('OSM_ALL_PLANS'), 'id', 'title');
		$options = array_merge($options, $db->loadObjectList());        
        if ($item->id)
        {
	        $query->clear();
	        $query->select('plan_id')
		        ->from('#__osmembership_field_plan')
		        ->where('field_id = '. $item->id);
	        $db->setQuery($query);
            $planIds = $db->loadColumn();
            if (count($planIds) == 0)
            {
                $planIds = array(0);
            }
        }
        else
        {
	        $planIds = array(0);
        }                          
		$lists['plan_id'] = JHtml::_('select.genericlist', $options, 'plan_id[]', ' class="inputbox" multiple="multiple" ', 'id', 'title', $planIds);
		$options = array();
		$options[] = JHtml::_('select.option', 1, JText::_('Yes'));
		$options[] = JHtml::_('select.option', 2, JText::_('No'));
		$lists['required'] = JHtml::_('select.booleanlist', 'required', ' class="inputbox" ', $item->required);		
		$lists['multiple'] = JHtml::_('select.booleanlist', 'multiple', ' class="inputbox" ', $item->multiple);		
		$options = array();
		$options[] = JHtml::_('select.option', 0, JText::_('None'));
		$options[] = JHtml::_('select.option', 1, JText::_('Integer Number'));
		$options[] = JHtml::_('select.option', 2, JText::_('Number'));
		$options[] = JHtml::_('select.option', 3, JText::_('Email'));
		$options[] = JHtml::_('select.option', 4, JText::_('Url'));
		$options[] = JHtml::_('select.option', 5, JText::_('Phone'));
		$options[] = JHtml::_('select.option', 6, JText::_('Past Date'));
		$options[] = JHtml::_('select.option', 7, JText::_('Ip'));
		$options[] = JHtml::_('select.option', 8, JText::_('Min size'));
		$options[] = JHtml::_('select.option', 9, JText::_('Max size'));
		$options[] = JHtml::_('select.option', 10, JText::_('Min integer'));
		$options[] = JHtml::_('select.option', 11, JText::_('Max integer'));
		$lists['datatype_validation'] = JHtml::_('select.genericlist', $options, 'datatype_validation', 'class="inputbox"', 'value', 'text', 
			$item->datatype_validation);

		// Trigger plugins to get list of fields for mapping
		JPluginHelper::importPlugin( 'osmembership' );
		$dispatcher = JDispatcher::getInstance();
		$results = $dispatcher->trigger( 'onGetFields', array());
		$fields = array();
		if (count($results))
		{
			foreach($results as $res)
			{
				if (is_array($res) && count($res))
				{
					$fields = $res;
					break;
				}
			}
		}

		if (count($fields))
		{
			$options = array();
			$options[] = JHtml::_('select.option', '', JText::_('Select Field'));
			$options = array_merge($options, $fields);
			$lists['field_mapping'] = JHtml::_('select.genericlist', $options, 'field_mapping', ' class="inputbox" ', 'value', 'text',
				$item->field_mapping);
		}

		$lists['fee_field'] = JHtml::_('select.booleanlist', 'fee_field', ' class="inputbox" ', $item->fee_field);
		$lists['show_on_members_list'] = JHtml::_('select.booleanlist', 'show_on_members_list', ' class="inputbox" ', $item->show_on_members_list);
		$lists['hide_on_membership_renewal'] = JHtml::_('select.booleanlist', 'hide_on_membership_renewal', ' class="inputbox" ', $item->hide_on_membership_renewal);
		if (JPluginHelper::isEnabled('osmembership', 'userprofile'))
		{
			$options = array();
			$options[] = JHtml::_('select.option', '', JText::_('Select Field'));
			
			$fields = array('address1', 'address2', 'city', 'region', 'country', 'postal_code', 'phone', 'website', 'favoritebook', 'aboutme', 'dob');
			
			foreach ($fields as $field)
			{
				$options[] = JHtml::_('select.option', $field);
			}
			$lists['profile_field_mapping'] = JHtml::_('select.genericlist', $options, 'profile_field_mapping', ' class="inputbox" ', 'value', 'text', 
				$item->profile_field_mapping);
		}
		// Custom fields dependency
		$query = $db->getQuery(true);
		$query->select('id, title')
			->from('#__osmembership_fields')
			->where('fieldtype IN ("List", "Radio", "Checkboxes")')
			->where('published=1');
		$db->setQuery($query);
		$options = array();
		$options[] = JHtml::_('select.option', 0, JText::_('Select'), 'id', 'title');
		$options = array_merge($options, $db->loadObjectList());
		$lists['depend_on_field_id'] = JHtml::_('select.genericlist', $options, 'depend_on_field_id',
			'class="inputbox" onchange="updateDependOnOptions();"', 'id', 'title', $item->depend_on_field_id);

		if ($item->depend_on_field_id)
		{
			//Get the selected options
			$this->dependOnOptions = explode(",", $item->depend_on_options);
			$query->clear();
			$query->select('`values`')
				->from('#__osmembership_fields')
				->where('id=' . $item->depend_on_field_id);
			$db->setQuery($query);
			$this->dependOptions = explode("\r\n", $db->loadResult());
		}

	}
}