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

class plgEventBookingMailchimp extends JPlugin
{
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		JFactory::getLanguage()->load('plg_eventbooking_mailchimp', JPATH_ADMINISTRATOR);
		JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_eventbooking/table');
	}

	/**
	 * Render settings form
	 *
	 * @param $row
	 *
	 * @return array
	 */
	public function onEditEvent($row)
	{
		ob_start();
		$this->drawSettingForm($row);
		$form = ob_get_contents();
		ob_end_clean();

		return array('title' => JText::_('PLG_EB_MAILCHIMP_SETTINGS'),
		             'form'  => $form
		);
	}

	/**
	 * Store setting into database, in this case, use params field of plans table
	 *
	 * @param event   $row
	 * @param Boolean $isNew true if create new plan, false if edit
	 */
	public function onAfterSaveEvent($row, $data, $isNew)
	{
		// $row of table EB_plans
		$params = new JRegistry($row->params);
		$params->set('mailchimp_list_ids', implode(',', $data['mailchimp_list_ids']));
		$row->params = $params->toString();

		$row->store();
	}

	/**
	 * Run when registration record stored to database
	 *
	 * @param JTable $row
	 */
	public function onAfterStoreRegistrant($row)
	{
		$event = JTable::getInstance('EventBooking', 'Event');
		$event->load($row->event_id);
		$params  = new JRegistry($event->params);
		$listIds = $params->get('mailchimp_list_ids', '');
		if ($listIds != '')
		{
			$listIds = explode(',', $listIds);
			if (count($listIds))
			{
				require_once dirname(__FILE__) . '/api/MailChimp.php';
				$mailchimp = new MailChimp($this->params->get('api_key'));
				foreach ($listIds as $listId)
				{
					if ($listId)
					{
						$mailchimp->call('lists/subscribe', array(
							'id'                => $listId,
							'email'             => array('email' => $row->email),
							'merge_vars'        => array('FNAME' => $row->first_name, 'LNAME' => $row->last_name),
							'double_optin'      => false,
							'update_existing'   => true,
							'replace_interests' => false,
							'send_welcome'      => false,
						));
					}
				}
			}
		}
	}

	/**
	 * Display form allows users to change settings on event add/edit screen
	 *
	 * @param object $row
	 */
	private function drawSettingForm($row)
	{
		require_once dirname(__FILE__) . '/api/MailChimp.php';
		$mailchimp = new MailChimp($this->params->get('api_key'));
		$lists     = $mailchimp->call('lists/list');
		if ($lists === false)
		{

		}
		else
		{
			$params  = new JRegistry($row->params);
			$listIds = explode(',', $params->get('mailchimp_list_ids', ''));
			$options = array();
			$lists   = $lists['data'];
			if (count($lists))
			{
				foreach ($lists as $list)
				{
					$options[] = JHtml::_('select.option', $list['id'], $list['name']);
				}
			}
			?>
			<table class="admintable adminform" style="width: 90%;">
				<tr>
					<td width="220" class="key">
						<?php echo JText::_('PLG_EB_MAILCHIMP_ASSIGN_TO_LISTS'); ?>
					</td>
					<td>
						<?php echo JHtml::_('select.genericlist', $options, 'mailchimp_list_ids[]', 'class="inputbox" multiple="multiple" size="10"', 'value', 'text', $listIds)?>
					</td>
					<td>
						<?php echo JText::_('PLG_EB_ACYMAILING_ASSIGN_TO_LISTS_EXPLAIN'); ?>
					</td>
				</tr>
			</table>
		<?php
		}
	}
}