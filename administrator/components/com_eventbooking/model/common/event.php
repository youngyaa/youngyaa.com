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
defined('_JEXEC') or die();

class EventbookingModelCommonEvent extends RADModelAdmin
{

	/**
	 * Method to store an event
	 *
	 * @param RADInput $input
	 * @param array    $ignore
	 *
	 * @throws Exception
	 *
	 * @return void
	 */
	public function store($input, $ignore = array())
	{
		$db  = $this->getDbo();
		$app = JFactory::getApplication();
		if ($app->isAdmin())
		{
			$data = $input->getData(RAD_INPUT_ALLOWRAW);
		}
		else
		{
			$data = $input->getData();
		}
		$config     = EventbookingHelper::getConfig();
		$thumbImage = $input->files->get('thumb_image');
		if ($thumbImage['name'])
		{
			$fileExt        = JString::strtoupper(JFile::getExt($thumbImage['name']));
			$supportedTypes = array('JPG', 'PNG', 'GIF');
			if (in_array($fileExt, $supportedTypes))
			{
				if (JFile::exists(JPATH_ROOT . '/media/com_eventbooking/images/' . JString::strtolower($thumbImage['name'])))
				{
					$fileName = time() . '_' . JString::strtolower($thumbImage['name']);
				}
				else
				{
					$fileName = JString::strtolower($thumbImage['name']);
				}
				$imagePath = JPATH_ROOT . '/media/com_eventbooking/images/' . $fileName;
				$thumbPath = JPATH_ROOT . '/media/com_eventbooking/images/thumbs/' . $fileName;
				JFile::upload($_FILES['thumb_image']['tmp_name'], $imagePath);
				if (!$config->thumb_width)
				{
					$config->thumb_width = 120;
				}
				if (!$config->thumb_height)
				{
					$config->thumb_height = 120;
				}
				EventbookingHelper::resizeImage($imagePath, $thumbPath, $config->thumb_width, $config->thumb_height, 95);
				$data['thumb'] = $fileName;
			}
		}

		if (isset($data['discount_groups']))
		{
			$data['discount_groups'] = implode(',', $data['discount_groups']);
		}

		//Process attachment
		$attachment = $thumbImage = $input->files->get('attachment');
		if ($attachment['name'])
		{
			$pathUpload        = JPATH_ROOT . '/media/com_eventbooking';
			$allowedExtensions = EventbookingHelper::getConfigValue('allowed_file_types');
			if (!$allowedExtensions)
			{
				$allowedExtensions = 'doc, docx, ppt, pptx, pdf, zip, rar, jpg, jepg, png, zipx';
			}
			$allowedExtensions = explode(',', $allowedExtensions);
			$allowedExtensions = array_map('trim', $allowedExtensions);
			$allowedExtensions = array_map('strtolower', $allowedExtensions);
			$fileName          = $attachment['name'];
			$fileExt           = JFile::getExt($fileName);
			if (in_array(strtolower($fileExt), $allowedExtensions))
			{
				$fileName = JFile::makeSafe($fileName);
				JFile::upload($attachment['tmp_name'], $pathUpload . '/' . $fileName);
				$data['attachment'] = $fileName;
			}
			else
			{
				// Throw notice, but still allow saving the event
				$data['attachment'] = '';
			}
		}

		//Init default data
		if (!isset($data['weekdays']))
		{
			$data['weekdays'] = array();
		}
		if (!isset($data['monthdays']))
		{
			$data['monthdays'] = '';
		}
		if (!$data['number_days'])
		{
			$data['number_days'] = 1;
		}
		if (!$data['number_weeks'])
		{
			$data['number_week'] = 1;
		}
		if (!$data['recurring_occurrencies'])
		{
			$data['recurring_occurrencies'] = 0;
		}
		if (!$data['recurring_end_date'])
		{
			$data['recurring_end_date'] = $db->getNullDate();
		}

		if (!$data['weekly_number_months'])
		{
			$data['weekly_number_months'] = 1;
		}

		if (isset($data['payment_methods']))
		{
			$data['payment_methods'] = implode(',', $data['payment_methods']);
		}

		if (empty($data['event_date_hour'] ))
		{
			$data['event_date_hour'] = '00';
		}

		if (empty($data['event_date_minute'] ))
		{
			$data['event_date_minute'] = '00';
		}

		if (empty($data['cut_off_hour'] ))
		{
			$data['cut_off_hour'] = '00';
		}

		if (empty($data['cut_off_minute'] ))
		{
			$data['cut_off_minute'] = '00';
		}

		if (isset($data['recurring_type']) && $data['recurring_type'])
		{
			$this->_storeRecurringEvent($data);
			$input->set('id', $data['id']);
		}
		else
		{
			//Normal events
			$row = $this->getTable();
			if ($this->state->id)
			{
				$isNew = false;
				$row->load($this->state->id);
				if (isset($data['del_thumb']) && $row->thumb)
				{
					if (JFile::exists(JPATH_ROOT . '/media/com_eventbooking/images/' . $row->thumb))
					{
						JFile::delete(JPATH_ROOT . '/media/com_eventbooking/images/' . $row->thumb);
					}
					if (JFile::exists(JPATH_ROOT . '/media/com_eventbooking/images/thumbs/' . $row->thumb))
					{
						JFile::delete(JPATH_ROOT . '/media/com_eventbooking/images/thumbs/' . $row->thumb);
					}
					$row->thumb = '';
				}

				if (isset($data['del_attachment']) && $row->attachment)
				{
					if (JFile::exists(JPATH_ROOT . '/media/com_eventbooking/' . $row->attachment))
					{
						JFile::delete(JPATH_ROOT . '/media/com_eventbooking/' . $row->attachment);
					}
					$row->attachment = '';
				}
			}
			else
			{
				$isNew = true;
			}
			if (!$row->bind($data, array('category_id', 'main_category_id')))
			{
				throw new Exception($db->getErrorMsg());
			}
			if (!$row->created_by)
			{
				$user            = JFactory::getUser();
				$row->created_by = $user->get('id');
			}
			$eventDateHour = $data['event_date_hour'];
			$row->event_date .= ' ' . $eventDateHour . ':' . $data['event_date_minute'] . ':00';
			$eventDateHour = $data['event_end_date_hour'];
			$row->event_end_date .= ' ' . $eventDateHour . ':' . $data['event_end_date_minute'] . ':00';

			$row->registration_start_date .= ' ' . $data['registration_start_hour'] . ':' . $data['registration_start_minute'] . ':00';
			$row->cut_off_date .= ' ' . $data['cut_off_hour'] . ':' . $data['cut_off_minute'] . ':00';


			$eventCustomField = EventbookingHelper::getConfigValue('event_custom_field');
			if ($eventCustomField)
			{
				if (is_array($data['params']))
				{
					$row->custom_fields = json_encode($data['params']);
				}
			}
			//Check ordering of the fieds
			if (!$row->id)
			{
				$row->ordering = $row->getNextOrder();
			}
			if (!$row->alias)
			{
				$row->alias = JApplication::stringURLSafe($row->title);
			}

			// Build alias for other languages
			if (JLanguageMultilang::isEnabled())
			{
				$languages = EventbookingHelper::getLanguages();
				if (count($languages))
				{
					foreach ($languages as $language)
					{
						$sef = $language->sef;
						if (!$row->{'alias_' . $sef})
						{
							$row->{'alias_' . $sef} = JApplication::stringURLSafe($row->{'title_' . $sef});
						}
						else
						{
							$row->{'alias_' . $sef} = JApplication::stringURLSafe($row->{'alias_' . $sef});
						}
					}
				}
			}

			if (!$row->store())
			{
				throw new Exception($db->getErrorMsg());
			}
			//Adjust alias if needed
			$query = $db->getQuery(true);
			$query->select('COUNT(*)')
				->from('#__eb_events')
				->where('alias=' . $db->quote($row->alias))
				->where('id !=' . $row->id);
			$db->setQuery($query);
			$total = $db->loadResult();
			if ($total)
			{
				$alias = $row->id . '-' . $row->alias;
				$query->clear();
				$query->update('#__eb_events')
					->set('alias=' . $db->quote($alias))
					->where('id=' . $row->id);
				$db->setQuery($query);
				$db->execute();
			}
			$query->clear();
			$query->delete('#__eb_event_group_prices')->where('event_id=' . $row->id);
			$db->setQuery($query);
			$db->execute();
			$prices            = $data['price'];
			$registrantNumbers = $data['registrant_number'];
			for ($i = 0, $n = count($prices); $i < $n; $i++)
			{
				$price            = $prices[$i];
				$registrantNumber = $registrantNumbers[$i];
				if (($registrantNumber > 0) && ($price > 0))
				{
					$query->clear();
					$query->insert('#__eb_event_group_prices')
						->columns('event_id, registrant_number, price')
						->values("$row->id, $registrantNumber, $price");
					$db->setQuery($query);
					$db->execute();
				}
			}
			$query->clear();
			$query->delete('#__eb_event_categories')->where('event_id=' . $row->id);
			$db->setQuery($query);
			$db->execute();
			$mainCategoryId = (int) $data['main_category_id'];
			if ($mainCategoryId)
			{
				$query->clear();
				$query->insert('#__eb_event_categories')
					->columns('event_id, category_id, main_category')
					->values("$row->id, $mainCategoryId, 1");
				$db->setQuery($query);
				$db->execute();
			}
			$categories = isset($data['category_id']) ? $data['category_id'] : array();
			for ($i = 0, $n = count($categories); $i < $n; $i++)
			{
				$categoryId = (int) $categories[$i];
				if ($categoryId && ($categoryId != $mainCategoryId))
				{
					$query->clear();
					$query->insert('#__eb_event_categories')
						->columns('event_id, category_id, main_category')
						->values("$row->id, $categoryId, 0");
					$db->setQuery($query);
					$db->execute();
				}
			}
			$input->set('id', $row->id);
			//Trigger event which allows plugins to save it own data
			JPluginHelper::importPlugin('eventbooking');
			$dispatcher = JDispatcher::getInstance();
			//Trigger plugins
			$dispatcher->trigger('onAfterSaveEvent', array($row, $data, $isNew));
		}
	}

	/**
	 * Store the event in case recurring feature activated
	 *
	 * @param $data
	 *
	 * @throws Exception
	 */
	function _storeRecurringEvent(&$data)
	{
		$db       = $this->getDbo();
		$config   = EventbookingHelper::getConfig();
		$row      = $this->getTable();
		$nullDate = $db->getNullDate();
		if ($this->state->id)
		{
			$row->load($this->state->id);
			$isNew = false;
		}
		else
		{
			$isNew = true;
		}

		if (!$data['alias'])
		{
			$data['alias'] = JApplication::stringURLSafe($data['title']);
		}

		if (!$row->bind($data, array('category_id', 'params')))
		{
			throw new Exception($db->getErrorMsg());
		}


		// Build alias for other languages
		if (JLanguageMultilang::isEnabled())
		{
			$languages = EventbookingHelper::getLanguages();
			if (count($languages))
			{
				foreach ($languages as $language)
				{
					$sef = $language->sef;
					if (!$row->{'alias_' . $sef})
					{
						$row->{'alias_' . $sef} = JApplication::stringURLSafe($row->{'title_' . $sef});
					}
					else
					{
						$row->{'alias_' . $sef} = JApplication::stringURLSafe($row->{'alias_' . $sef});
					}
				}
			}
		}


		if (!$row->created_by)
		{
			$user            = JFactory::getUser();
			$row->created_by = $user->get('id');
		}

		$row->event_type = 1;
		$eventDateHour   = $data['event_date_hour'];
		$row->event_date .= ' ' . $eventDateHour . ':' . $data['event_date_minute'] . ':00';
		$eventDateHour = $data['event_end_date_hour'];
		$row->weekdays = implode(',', $data['weekdays']);
		$row->event_end_date .= ' ' . $eventDateHour . ':' . $data['event_end_date_minute'] . ':00';
		$row->registration_start_date .= ' ' . $data['registration_start_hour'] . ':' . $data['registration_start_minute'] . ':00';
		$row->cut_off_date .= ' ' . $data['cut_off_hour'] . ':' . $data['cut_off_minute'] . ':00';
		//Adjust event start date and event end date
		if ($data['recurring_type'] == 1)
		{
			$eventDates               = EventbookingHelper::getDailyRecurringEventDates($row->event_date, $data['recurring_end_date'], (int) $data['number_days'],
				(int) $data['recurring_occurrencies']);
			$row->recurring_frequency = $data['number_days'];
		}
		elseif ($data['recurring_type'] == 2)
		{
			$eventDates               = EventbookingHelper::getWeeklyRecurringEventDates($row->event_date, $data['recurring_end_date'], (int) $data['number_weeks'],
				(int) $data['recurring_occurrencies'], $data['weekdays']);
			$row->recurring_frequency = $data['number_weeks'];
		}
		elseif ($data['recurring_type'] == 3)
		{
			//Monthly recurring
			$eventDates               = EventbookingHelper::getMonthlyRecurringEventDates($row->event_date, $data['recurring_end_date'],
				(int) $data['number_months'], (int) $data['recurring_occurrencies'], $data['monthdays']);
			$row->recurring_frequency = $data['number_months'];
		}
		else
		{
			// Monthly recurring at a specific date in the week
			$eventDates               = EventbookingHelper::getMonthlyRecurringAtDayInWeekEventDates($row->event_date, $data['recurring_end_date'], (int) $data['weekly_number_months'], (int) $data['recurring_occurrencies'], $data['week_in_month'], $data['day_of_week']);
			$row->recurring_frequency = $data['weekly_number_months'];

			$params = new JRegistry($row->params);
			$params->set('weekly_number_months', $data['weekly_number_months']);
			$params->set('week_in_month', $data['week_in_month']);
			$params->set('day_of_week', $data['day_of_week']);
			$row->params = $params->toString();
		}

		if (strlen(trim($data['event_end_date'])) && $row->event_end_date != $nullDate)
		{
			$eventDuration = abs(strtotime($row->event_end_date) - strtotime($row->event_date));
		}
		else
		{
			$eventDuration = 0;
		}
		if (strlen(trim($data['cut_off_date'])) && $row->cut_off_date != $nullDate)
		{
			$cutOffDuration = abs(strtotime($row->cut_off_date) - strtotime($row->event_date));
		}
		else
		{
			$cutOffDuration = 0;
		}
		if (strlen(trim($row->cancel_before_date)) && $row->cancel_before_date != $nullDate)
		{
			$cancelDuration = abs(strtotime($row->cancel_before_date) - strtotime($row->event_date));
		}
		else
		{
			$cancelDuration = 0;
		}
		if (strlen(trim($row->early_bird_discount_date)) && $row->early_bird_discount_date != $nullDate)
		{
			$earlyBirdDuration = abs(strtotime($row->early_bird_discount_date) - strtotime($row->event_date));
		}
		else
		{
			$earlyBirdDuration = 0;
		}

		if (strlen(trim($data['registration_start_date'])) && $row->registration_start_date != $nullDate)
		{
			$registrationStartDuration = abs(strtotime($row->registration_start_date) - strtotime($row->event_date));
		}
		else
		{
			$registrationStartDuration = 0;
		}

		if (count($eventDates) == 0)
		{
			JFactory::getApplication()->redirect('index.php?option=com_eventbooking&view=events', JText::_('Invalid recurring setting'));
		}
		else
		{
			$row->event_date = $eventDates[0];
			if ($eventDuration)
			{
				$row->event_end_date = strftime('%Y-%m-%d %H:%M:%S', strtotime($row->event_date) + $eventDuration);
			}
			else
			{
				$row->event_end_date = '';
			}

		}
		$eventCustomField = EventbookingHelper::getConfigValue('event_custom_field');
		if ($eventCustomField)
		{
			$params = $data['params'];
			if (is_array($params))
			{
				$row->custom_fields = json_encode($params);
			}
		}
		//Check ordering of the fieds
		if (!$row->id)
		{
			$row->ordering = $row->getNextOrder();
		}

		if (!$row->store())
		{
			throw new Exception($db->getErrorMsg());
		}

		$query = $db->getQuery(true);
		$query->select('COUNT(*)')
			->from('#__eb_events')
			->where('alias=' . $db->quote($row->alias))
			->where('id !=' . $row->id);
		$db->setQuery($query);
		$total = $db->loadResult();
		if ($total)
		{
			$alias = $row->id . '-' . $row->alias;
			$query->clear();
			$query->update('#__eb_events')
				->set('alias=' . $db->quote($alias))
				->where('id=' . $row->id);
			$db->setQuery($query);
			$db->execute();
		}

		$data['id'] = $row->id;
		$query->clear();
		$query->delete('#__eb_event_group_prices')->where('event_id=' . $row->id);
		$db->setQuery($query);
		$db->execute();

		$prices            = $data['price'];
		$registrantNumbers = $data['registrant_number'];
		for ($i = 0, $n = count($prices); $i < $n; $i++)
		{
			$price            = $prices[$i];
			$registrantNumber = $registrantNumbers[$i];
			if (($registrantNumber > 0) && ($price > 0))
			{
				$query->clear();
				$query->insert('#__eb_event_group_prices')
					->columns('event_id, registrant_number, price')
					->values("$row->id, $registrantNumber, $price");
				$db->setQuery($query);
				$db->execute();
			}
		}
		$query->clear();
		$query->delete('#__eb_event_categories')->where('event_id=' . $row->id);
		$db->setQuery($query);
		$db->execute();
		$mainCategoryId = (int) $data['main_category_id'];
		if ($mainCategoryId)
		{
			$query->clear();
			$query->insert('#__eb_event_categories')
				->columns('event_id, category_id, main_category')
				->values("$row->id, $mainCategoryId, 1");
			$db->setQuery($query);
			$db->execute();
		}
		$categories = isset($data['category_id']) ? $data['category_id'] : array();
		for ($i = 0, $n = count($categories); $i < $n; $i++)
		{
			$categoryId = (int) $categories[$i];
			if ($categoryId && ($categoryId != $mainCategoryId))
			{
				$query->clear();
				$query->insert('#__eb_event_categories')
					->columns('event_id, category_id, main_category')
					->values("$row->id, $categoryId, 0");
				$db->setQuery($query);
				$db->execute();
			}
		}
		/**
		 * In case creating new event, we will create children events
		 */
		if (!$this->state->id)
		{
			for ($i = 1, $n = count($eventDates); $i < $n; $i++)
			{
				$rowChildEvent             = clone ($row);
				$rowChildEvent->id         = 0;
				$rowChildEvent->event_date = $eventDates[$i];
				if ($eventDuration)
				{
					$rowChildEvent->event_end_date = strftime('%Y-%m-%d %H:%M:%S', strtotime($eventDates[$i]) + $eventDuration);
				}
				else
				{
					$rowChildEvent->event_end_date = '';
				}

				if ($cutOffDuration)
				{
					$rowChildEvent->cut_off_date = strftime('%Y-%m-%d %H:%M:%S', strtotime($rowChildEvent->event_date) - $cutOffDuration);
				}
				if ($cancelDuration)
				{
					$rowChildEvent->cancel_before_date = strftime('%Y-%m-%d %H:%M:%S', strtotime($rowChildEvent->event_date) - $cancelDuration);
				}
				if ($earlyBirdDuration)
				{
					$rowChildEvent->early_bird_discount_date = strftime('%Y-%m-%d %H:%M:%S', strtotime($rowChildEvent->event_date) - $earlyBirdDuration);
				}
				if ($registrationStartDuration)
				{
					$rowChildEvent->registration_start_date = strftime('%Y-%m-%d %H:%M:%S', strtotime($rowChildEvent->event_date) - $registrationStartDuration);
				}
				$rowChildEvent->event_type             = 2;
				$rowChildEvent->parent_id              = $row->id;
				$rowChildEvent->recurring_type         = 0;
				$rowChildEvent->recurring_frequency    = 0;
				$rowChildEvent->weekdays               = '';
				$rowChildEvent->monthdays              = '';
				$rowChildEvent->recurring_end_date     = $db->getNullDate();
				$rowChildEvent->recurring_occurrencies = 0;
				$rowChildEvent->created_by             = $row->created_by;
				$rowChildEvent->alias                  = JApplication::stringURLSafe(
					$rowChildEvent->title . '-' . JHtml::_('date', $rowChildEvent->event_date, $config->date_format, null));

				// Build alias for other languages
				if (JLanguageMultilang::isEnabled())
				{
					if (count($languages))
					{
						foreach ($languages as $language)
						{
							$sef                              = $language->sef;
							$rowChildEvent->{'alias_' . $sef} = JApplication::stringURLSafe(
								$rowChildEvent->{'alias_' . $sef} . '-' . JHtml::_('date', $rowChildEvent->event_date, $config->date_format, null));
						}
					}
				}

				$rowChildEvent->store();
				//Generate alias
				$query->clear();
				$query->select('COUNT(*)')
					->from('#__eb_events')
					->where('alias=' . $db->quote($rowChildEvent->alias))
					->where('id !=' . $rowChildEvent->id);
				$db->setQuery($query);
				$total = $db->loadResult();
				if ($total)
				{
					$alias = $rowChildEvent->id . '-' . $rowChildEvent->alias;
					$query->clear();
					$query->update('#__eb_events')
						->set('alias=' . $db->quote($alias))
						->where('id=' . $rowChildEvent->id);
					$db->setQuery($query);
					$db->execute();
				}

				//Event Price
				for ($j = 0, $m = count($prices); $j < $m; $j++)
				{
					$price            = $prices[$j];
					$registrantNumber = $registrantNumbers[$j];
					if (($registrantNumber > 0) && ($price > 0))
					{
						$query->clear();
						$query->insert('#__eb_event_group_prices')
							->columns('event_id, registrant_number, price')
							->values("$rowChildEvent->id, $registrantNumber, $price");
						$db->setQuery($query);
						$db->execute();
					}
				}
				$sql = 'INSERT INTO #__eb_event_categories(event_id, category_id, main_category) '
					. "SELECT $rowChildEvent->id, category_id, main_category FROM #__eb_event_categories WHERE event_id=$row->id";
				$db->setQuery($sql);
				$db->execute();
			}
		}
		elseif (isset($data['update_children_event']))
		{
			$deleteEventIds = array();
			$eventDatesDate = array();

			foreach($eventDates as $eventDate)
			{
				$eventDatesDate[] = JHtml::_('date', $eventDate, 'Y-m-d', null);
			}

			// The parent event
			$childEventDate = JHtml::_('date', $row->event_date, 'Y-m-d', null);
			$index          = array_search($childEventDate, $eventDatesDate);
			if ($index !== false)
			{
				unset($eventDatesDate[$index]);
			}

			$query->clear();
			$query->select('id')
				->from('#__eb_events')
				->where('parent_id=' . $row->id)
				->order('event_date');

			$db->setQuery($query);
			$children = $db->loadColumn();
			if (count($children))
			{
				$fieldsToUpdate = array(
					'category_id',
					'thumb',
					'location_id',
					'tax_rate',
					'registration_type',
					'title',
					'short_description',
					'description',
					'access',
					'registration_access',
					'individual_price',
					'event_capacity',
					'registration_type',
					'max_group_number',
					'discount_type',
					'discount',
					'early_bird_discount_amount',
					'early_bird_discount_type',
					'paypal_email',
					'notification_emails',
					'user_email_body',
					'user_email_body_offline',
					'thanks_message',
					'thanks_message_offline',
					'params',
					'currency_code',
					'currency_symbol',
					'published');
				$rowChildEvent = $this->getTable();
				foreach ($children as $childId)
				{
					$rowChildEvent->load($childId);
					$childEventDate = JHtml::_('date', $rowChildEvent->event_date, 'Y-m-d', null);
					$index          = array_search($childEventDate, $eventDatesDate);
					if ($index !== false)
					{
						unset($eventDatesDate[$index]);
					}
					else
					{
						$deleteEventIds[] = $rowChildEvent->id;
						continue;
					}

					foreach ($fieldsToUpdate as $field)
					{
						$rowChildEvent->$field = $row->$field;
					}

					// Allow children event to update hour and minute secure
					$rowChildEvent->event_date = JHtml::_('date', $rowChildEvent->event_date, 'Y-m-d', null) . ' ' . JHtml::_('date', $row->event_date, 'H:i:s', null);

					if ($eventDuration)
					{
						$rowChildEvent->event_end_date = strftime('%Y-%m-%d %H:%M:%S', strtotime($rowChildEvent->event_date) + $eventDuration);
					}
					else
					{
						$rowChildEvent->event_end_date = '';
					}

					if ($cutOffDuration)
					{
						$rowChildEvent->cut_off_date = strftime('%Y-%m-%d %H:%M:%S', strtotime($rowChildEvent->event_date) - $cutOffDuration);
					}
					else
					{
						$rowChildEvent->cut_off_date = '';
					}

					if ($cancelDuration)
					{
						$rowChildEvent->cancel_before_date = strftime('%Y-%m-%d %H:%M:%S', strtotime($rowChildEvent->event_date) - $cancelDuration);
					}
					else
					{
						$rowChildEvent->cancel_before_date = '';
					}
					if ($earlyBirdDuration)
					{
						$rowChildEvent->early_bird_discount_date = strftime('%Y-%m-%d %H:%M:%S', strtotime($rowChildEvent->event_date) - $earlyBirdDuration);
					}
					else
					{
						$rowChildEvent->early_bird_discount_date = '';
					}

					if ($registrationStartDuration)
					{
						$rowChildEvent->registration_start_date = strftime('%Y-%m-%d %H:%M:%S', strtotime($rowChildEvent->event_date) - $registrationStartDuration);
					}
					else
					{
						$rowChildEvent->registration_start_date = '';
					}

					$rowChildEvent->store();
					$query->clear();
					$query->delete('#__eb_event_group_prices')->where('event_id=' . $rowChildEvent->id);
					$db->setQuery($query);
					$db->execute();

					for ($i = 0, $n = count($prices); $i < $n; $i++)
					{
						$price            = $prices[$i];
						$registrantNumber = $registrantNumbers[$i];
						if (($registrantNumber > 0) && ($price > 0))
						{
							$query->clear();
							$query->insert('#__eb_event_group_prices')
								->columns('event_id, registrant_number, price')
								->values("$rowChildEvent->id, $registrantNumber, $price");
							$db->setQuery($query);
							$db->execute();
						}
					}
					$query->clear();
					$query->delete('#__eb_event_categories')->where('event_id=' . $rowChildEvent->id);
					$db->setQuery($query);
					$db->execute();
					$sql = 'INSERT INTO #__eb_event_categories(event_id, category_id, main_category) '
						. "SELECT $rowChildEvent->id, category_id, main_category FROM #__eb_event_categories WHERE event_id=$row->id";
					$db->setQuery($sql);
					$db->execute();
				}
			}

			// Insert new events
			if (count($eventDatesDate))
			{
				foreach($eventDatesDate as $newEventDate)
				{
					$rowChildEvent             = clone ($row);
					$rowChildEvent->id         = 0;
					$rowChildEvent->event_date = $newEventDate;
					$rowChildEvent->event_date = JHtml::_('date', $rowChildEvent->event_date, 'Y-m-d', null) . ' ' . JHtml::_('date', $row->event_date, 'H:i:s', null);
					if ($eventDuration)
					{
						$rowChildEvent->event_end_date = strftime('%Y-%m-%d %H:%M:%S', strtotime($rowChildEvent->event_date) + $eventDuration);
					}
					else
					{
						$rowChildEvent->event_end_date = '';
					}

					if ($cutOffDuration)
					{
						$rowChildEvent->cut_off_date = strftime('%Y-%m-%d %H:%M:%S', strtotime($rowChildEvent->event_date) - $cutOffDuration);
					}
					if ($cancelDuration)
					{
						$rowChildEvent->cancel_before_date = strftime('%Y-%m-%d %H:%M:%S', strtotime($rowChildEvent->event_date) - $cancelDuration);
					}
					if ($earlyBirdDuration)
					{
						$rowChildEvent->early_bird_discount_date = strftime('%Y-%m-%d %H:%M:%S', strtotime($rowChildEvent->event_date) - $earlyBirdDuration);
					}
					if ($registrationStartDuration)
					{
						$rowChildEvent->registration_start_date = strftime('%Y-%m-%d %H:%M:%S', strtotime($rowChildEvent->event_date) - $registrationStartDuration);
					}
					$rowChildEvent->event_type             = 2;
					$rowChildEvent->parent_id              = $row->id;
					$rowChildEvent->recurring_type         = 0;
					$rowChildEvent->recurring_frequency    = 0;
					$rowChildEvent->weekdays               = '';
					$rowChildEvent->monthdays              = '';
					$rowChildEvent->recurring_end_date     = $db->getNullDate();
					$rowChildEvent->recurring_occurrencies = 0;
					$rowChildEvent->created_by             = $row->created_by;
					$rowChildEvent->alias                  = JApplication::stringURLSafe(
						$rowChildEvent->title . '-' . JHtml::_('date', $rowChildEvent->event_date, $config->date_format, null));

					// Build alias for other languages
					if (JLanguageMultilang::isEnabled())
					{
						if (count($languages))
						{
							foreach ($languages as $language)
							{
								$sef                              = $language->sef;
								$rowChildEvent->{'alias_' . $sef} = JApplication::stringURLSafe(
									$rowChildEvent->{'alias_' . $sef} . '-' . JHtml::_('date', $rowChildEvent->event_date, $config->date_format, null));
							}
						}
					}

					$rowChildEvent->store();
					//Generate alias
					$query->clear();
					$query->select('COUNT(*)')
						->from('#__eb_events')
						->where('alias=' . $db->quote($rowChildEvent->alias))
						->where('id !=' . $rowChildEvent->id);
					$db->setQuery($query);
					$total = $db->loadResult();
					if ($total)
					{
						$alias = $rowChildEvent->id . '-' . $rowChildEvent->alias;
						$query->clear();
						$query->update('#__eb_events')
							->set('alias=' . $db->quote($alias))
							->where('id=' . $rowChildEvent->id);
						$db->setQuery($query);
						$db->execute();
					}

					//Event Price
					for ($j = 0, $m = count($prices); $j < $m; $j++)
					{
						$price            = $prices[$j];
						$registrantNumber = $registrantNumbers[$j];
						if (($registrantNumber > 0) && ($price > 0))
						{
							$query->clear();
							$query->insert('#__eb_event_group_prices')
								->columns('event_id, registrant_number, price')
								->values("$rowChildEvent->id, $registrantNumber, $price");
							$db->setQuery($query);
							$db->execute();
						}
					}
					$sql = 'INSERT INTO #__eb_event_categories(event_id, category_id, main_category) '
						. "SELECT $rowChildEvent->id, category_id, main_category FROM #__eb_event_categories WHERE event_id=$row->id";
					$db->setQuery($sql);
					$db->execute();
				}
			}
			if (count($deleteEventIds))
			{
				foreach($deleteEventIds as $i => $deleteEventId)
				{
					// Check to see if this event has registrants, if it doesn't have registrants, it is safe to delete
					$query->clear();
					$query->select('COUNT(*)')
						->from('#__eb_registrants')
						->where('event_id = '. $deleteEventId)
						->where('(published >= 1 OR payment_method LIKE "os_offline%")');
					$db->setQuery($query);
					$total = $db->loadResult();
					if ($total)
					{
						unset($deleteEventIds[$i]);
					}
				}

				if (count($deleteEventIds))
				{
					$this->delete($deleteEventIds);
				}
			}
		}

		JPluginHelper::importPlugin('eventbooking');
		$dispatcher = JDispatcher::getInstance();
		//Trigger plugins
		$dispatcher->trigger('onAfterSaveEvent', array($row, $data, $isNew));
	}

	/**
	 * Init event data
	 *
	 */
	function initData()
	{
		parent::initData();
		$db                                   = $this->getDbo();
		$config                               = EventbookingHelper::getConfig();
		$this->data->event_date               = $db->getNullDate();
		$this->data->event_end_date           = $db->getNullDate();
		$this->data->registration_start_date  = $db->getNullDate();
		$this->data->late_fee_date  		  = $db->getNullDate();
		$this->data->cut_off_date             = $db->getNullDate();
		$this->data->registration_type        = isset($config->registration_type) ? $config->registration_type : 0;
		$this->data->access                   = isset($config->access) ? $config->access : 1;
		$this->data->registration_access      = isset($config->registration_access) ? $config->registration_access : 1;
		$this->data->cancel_before_date       = $db->getNullDate();
		$this->data->early_bird_discount_date = $db->getNullDate();
		$this->data->article_id               = $config->article_id;
		$this->data->recurring_type           = 0;
		$this->data->number_days              = '';
		$this->data->number_weeks             = '';
		$this->data->number_months            = '';
		$this->data->recurring_frequency      = 0;
		$this->data->recurring_end_date       = $db->getNullDate();
		$this->data->ordering                 = 0;
		$this->data->published                = isset($config->default_event_status) ? $config->default_event_status : 0;
	}

	/**
	 * Load event date from database
	 *
	 * @see RADModelAdmin::loadData()
	 */
	function loadData()
	{
		parent::loadData();
		$activateRecurringEvent = EventbookingHelper::getConfigValue('activate_recurring_event');
		if ($activateRecurringEvent)
		{
			if ($this->data->recurring_type == 1)
			{
				$this->data->number_days   = $this->data->recurring_frequency;
				$this->data->number_weeks  = 0;
				$this->data->number_months = 0;
			}
			elseif ($this->data->recurring_type == 2)
			{
				$this->data->number_weeks  = $this->data->recurring_frequency;
				$this->data->number_days   = 0;
				$this->data->number_months = 0;
			}
			elseif ($this->data->recurring_type == 3)
			{
				$this->data->number_months = $this->data->recurring_frequency;
				$this->data->number_days   = 0;
				$this->data->number_weeks  = 0;
			}
		}


		if ($this->data->recurring_type)
		{
			if ($this->data->number_days == 0)
			{
				$this->data->number_days = '';
			}
			if ($this->data->number_weeks == 0)
			{
				$this->data->number_weeks = '';
			}
			if ($this->data->number_months == 0)
			{
				$this->data->number_months = '';
			}

			if ($this->data->recurring_occurrencies == 0)
			{
				$this->data->recurring_occurrencies = '';
			}
		}
	}

	/**
	 * Method to remove events
	 *
	 * @param array $cid Array contains IDs of the events which you want to delete
	 *
	 * @return    boolean    True on success
	 */
	public function delete($cid = array())
	{
		$db    = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('id')
			->from('#__eb_events')
			->where('parent_id IN (' . implode(',', $cid) . ')');
		$db->setQuery($query);
		$cid  = array_merge($cid, $db->loadColumn());
		$cids = implode(',', $cid);
		//Delete price setting for events
		$query->clear();
		$query->delete('#__eb_event_group_prices')->where('event_id IN (' . $cids . ')');
		$db->setQuery($query);
		$db->execute();
		//Delete categories for the event
		$query->clear();
		$query->delete('#__eb_event_categories')->where('event_id IN (' . $cids . ')');
		$db->setQuery($query);
		$db->execute();
		//Delete events themself
		$query->clear();
		$query->delete('#__eb_events')->where('id IN (' . $cids . ')');
		$db->setQuery($query);
		$db->execute();

		return true;
	}

	/**
	 * Get group registration rates for the event
	 *
	 * @return array|mixed
	 */
	public function getPrices()
	{
		$prices = array();

		if ($this->state->id)
		{
			$db    = $this->getDbo();
			$query = $db->getQuery(true);
			$query->select('*')
				->from('#__eb_event_group_prices')
				->where('event_id=' . $this->state->id)
				->order('id');
			$db->setQuery($query);
			$prices = $db->loadObjectList();
		}

		return $prices;
	}
}