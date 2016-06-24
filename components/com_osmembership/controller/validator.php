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

class OSMembershipControllerValidator extends MPFController
{
	/**
	 * Validate username, make sure it is allowed. In Joomla, username must be unique for each user
	 */
	public function validate_username()
	{
		$db         = JFactory::getDbo();
		$query      = $db->getQuery(true);
		$username   = $this->input->getString('fieldValue');
		$validateId = $this->input->getString('fieldId');
		$query->select('COUNT(*)')
			->from('#__users')
			->where('username=' . $db->quote($username));
		$db->setQuery($query);
		$total        = $db->loadResult();
		$arrayToJs    = array();
		$arrayToJs[0] = $validateId;
		if ($total)
		{
			$arrayToJs[1] = false;
		}
		else
		{
			$arrayToJs[1] = true;
		}
		echo json_encode($arrayToJs);
		$this->app->close();
	}

	/**
	 * Validate email, make sure it is valid before continue processing subscription
	 * In Joomla, each user must have an unique email address for account registration
	 *
	 */
	public function validate_email()
	{
		$user         = JFactory::getUser();
		$config       = OSMembershipHelper::getConfig();
		$email        = $this->input->get('fieldValue', '', 'string');
		$validateId   = $this->input->getString('fieldId');
		$arrayToJs    = array();
		$arrayToJs[0] = $validateId;
		$arrayToJs[1] = true;
		if (JFactory::getApplication()->isSite() && $config->registration_integration && !$user->id)
		{
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('COUNT(*)')
				->from('#__users')
				->where('email=' . $db->quote($email));
			$db->setQuery($query);
			$total = $db->loadResult();
			if ($total)
			{
				$arrayToJs[1] = false;
			}
		}
		echo json_encode($arrayToJs);
		$this->app->close();
	}

	/**
	 * Validate email, make sure it is valid before continue processing subscription
	 * In Joomla, each user must have an unique email address for account registration
	 *
	 */
	public function validate_group_member_email()
	{
		$db         = JFactory::getDbo();
		$query      = $db->getQuery(true);
		$email      = $this->input->get('fieldValue', '', 'string');
		$validateId = $this->input->get('fieldId', '', 'string');
		$query->select('COUNT(*)')
			->from('#__users')
			->where('email="' . $email . '"');
		$db->setQuery($query);
		$total        = $db->loadResult();
		$arrayToJs    = array();
		$arrayToJs[0] = $validateId;
		if (!$total)
		{
			$arrayToJs[1] = true;
		}
		else
		{
			$arrayToJs[1] = false;
		}
		echo json_encode($arrayToJs);
		$this->app->close();
	}

	/**
	 * Validate password to ensure that password is trong
	 */
	public function validate_password()
	{
		//Load language from user component
		$lang = JFactory::getLanguage();
		$tag  = $lang->getTag();
		if (!$tag)
		{
			$tag = 'en-GB';
		}
		$lang->load('com_users', JPATH_ROOT, $tag);
		$value            = $this->input->get('fieldValue', '', 'none');
		$validateId       = $this->input->get('fieldId', '', 'none');
		$params           = JComponentHelper::getParams('com_users');
		$minimumIntegers  = $params->get('minimum_integers');
		$minimumSymbols   = $params->get('minimum_symbols');
		$minimumUppercase = $params->get('minimum_uppercase');
		$validPassword    = true;
		$errorMessage     = '';
		if (!empty($minimumIntegers))
		{
			$nInts = preg_match_all('/[0-9]/', $value, $imatch);

			if ($nInts < $minimumIntegers)
			{
				$errorMessage  = JText::plural('COM_USERS_MSG_NOT_ENOUGH_INTEGERS_N', $minimumIntegers);
				$validPassword = false;
			}
		}
		if ($validPassword && !empty($minimumSymbols))
		{
			$nsymbols = preg_match_all('[\W]', $value, $smatch);

			if ($nsymbols < $minimumSymbols)
			{
				$errorMessage  = JText::plural('COM_USERS_MSG_NOT_ENOUGH_SYMBOLS_N', $minimumSymbols);
				$validPassword = false;
			}
		}
		if ($validPassword && !empty($minimumUppercase))
		{
			$nUppercase = preg_match_all("/[A-Z]/", $value, $umatch);
			if ($nUppercase < $minimumUppercase)
			{
				$errorMessage  = JText::plural('COM_USERS_MSG_NOT_ENOUGH_UPPERCASE_LETTERS_N', $minimumUppercase);
				$validPassword = false;
			}
		}
		$arrayToJs    = array();
		$arrayToJs[0] = $validateId;
		if (!$validPassword)
		{
			$arrayToJs[1] = false;
			$arrayToJs[2] = $errorMessage;
		}
		else
		{
			$arrayToJs[1] = true;
		}
		echo json_encode($arrayToJs);
		$this->app->close();
	}
}