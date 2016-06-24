<?php
/**
 * @version        2.2.1
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2016 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

/**
 * HTML View class for Membership Pro component
 *
 * @static
 * @package        Joomla
 * @subpackage     Membership Pro
 */
class OSMembershipViewConfigurationHtml extends MPFViewHtml
{

	public function display()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$config = OSMembershipHelper::getConfig();
		$options = array();
		$options[] = JHtml::_('select.option', 0, JText::_('OSM_NO_INTEGRATION'));
		$options[] = JHtml::_('select.option', 1, JText::_('OSM_COMMUNITY_BUILDER'));
		$options[] = JHtml::_('select.option', 2, JText::_('OSM_JOMSOCIAL'));
		$lists['registration_integration'] = JHtml::_('select.booleanlist', 'registration_integration', ' class="inputbox" ', $config->registration_integration);
		$lists['auto_login'] = JHtml::_('select.booleanlist', 'auto_login', ' class="inputbox" ', $config->auto_login);
		$lists['auto_reload_user'] = JHtml::_('select.booleanlist', 'auto_reload_user', ' class="inputbox" ', $config->auto_reload_user);
		$lists['use_https'] = JHtml::_('select.booleanlist', 'use_https', '', $config->use_https);
		$lists['enable_captcha'] = JHtml::_('select.booleanlist', 'enable_captcha', '', $config->enable_captcha);
		$lists['enable_coupon'] = JHtml::_('select.booleanlist', 'enable_coupon', '', $config->enable_coupon);
		$lists['debug']         = JHtml::_('select.booleanlist', 'debug', '', $config->debug);
		$lists['show_login_box_on_subscribe_page'] = JHtml::_('select.booleanlist', 'show_login_box_on_subscribe_page', '', $config->show_login_box_on_subscribe_page);
		$lists['auto_generate_membership_id'] = JHtml::_('select.booleanlist', 'auto_generate_membership_id', '', $config->auto_generate_membership_id);
		$lists['load_twitter_bootstrap_in_frontend'] = JHtml::_('select.booleanlist', 'load_twitter_bootstrap_in_frontend', '',
			isset($config->load_twitter_bootstrap_in_frontend) ? $config->load_twitter_bootstrap_in_frontend : 1);


		$options = array();
		$options[] = JHtml::_('select.option', 2, JText::_('OSM_VERSION_2'));
		$options[] = JHtml::_('select.option', 3, JText::_('OSM_VERSION_3'));
		$lists['twitter_bootstrap_version'] = JHtml::_('select.genericlist', $options, 'twitter_bootstrap_version', '', 'value', 'text', $config->twitter_bootstrap_version ? $config->twitter_bootstrap_version : 2);

		$lists['load_jquery'] = JHtml::_('select.booleanlist', 'load_jquery', '',
			isset($config->load_jquery) ? $config->load_jquery : 1);
		$lists['show_price_including_tax'] = JHtml::_('select.booleanlist', 'show_price_including_tax', '', $config->show_price_including_tax);

		$query->select('id, title')
			->from('#__content')
			->order('title');
		$db->setQuery($query);
		$options = array();
		$options[] = JHtml::_('select.option', 0, JText::_('OSM_SELECT_ARTICLE'), 'id', 'title');
		$options = array_merge($options, $db->loadObjectList());
		$lists['article_id'] = JHtml::_('select.genericlist', $options, 'article_id', ' class="inputbox" ', 'id', 'title', $config->article_id);
		$lists['fix_terms_and_conditions_popup'] = JHtml::_('select.booleanlist', 'fix_terms_and_conditions_popup', '', $config->fix_terms_and_conditions_popup);
		$lists['send_attachments_to_admin'] = JHtml::_('select.booleanlist', 'send_attachments_to_admin', '', $config->send_attachments_to_admin);

		$currencies = require_once JPATH_ROOT . '/components/com_osmembership/helper/currencies.php';
		$options    = array();
		$options[]  = JHtml::_('select.option', '', JText::_('OSM_SELECT_CURRENCY'));
		foreach ($currencies as $code => $title)
		{
			$options[] = JHtml::_('select.option', $code, $title);
		}
		$lists['currency_code'] = JHtml::_('select.genericlist', $options, 'currency_code', '', 'value', 'text', isset($config->currency_code) ? $config->currency_code : 'USD');

		$options = array();
		$options[] = JHtml::_('select.option', '', JText::_('OSM_SELECT_POSITION'));
		$options[] = JHtml::_('select.option', 0, JText::_('OSM_BEFORE_AMOUNT'));
		$options[] = JHtml::_('select.option', 1, JText::_('OSM_AFTER_AMOUNT'));

		$lists['currency_position'] = JHtml::_('select.genericlist', $options, 'currency_position', ' class="inputbox"', 'value', 'text', $config->currency_position);

		// EU VAT Number field selection
		$query->clear();
		$query->select('name, title')
			->from('#__osmembership_fields')
			->where('published = 1')
			->order('ordering');
		$db->setQuery($query);

		$options = array();
		$options[] = JHtml::_('select.option', '', JText::_('OSM_SELECT'), 'name', 'title');
		$options = array_merge($options, $db->loadObjectList());
		$lists['eu_vat_number_field'] = JHtml::_('select.genericlist', $options, 'eu_vat_number_field', ' class="inputbox"', 'name', 'title', $config->eu_vat_number_field);

		//Get list of country
		$query->clear();
		$query->select('name AS value, name AS text')
			->from('#__osmembership_countries')
			->where('published = 1')
			->order('name');
		$db->setQuery($query);

		$options = array();
		$options[] = JHtml::_('select.option', '', JText::_('OSM_SELECT_DEFAULT_COUNTRY'));
		$options = array_merge($options, $db->loadObjectList());
		$lists['country_list'] = JHtml::_('select.genericlist', $options, 'default_country', '', 'value', 'text', $config->default_country);
		$lists['activate_invoice_feature'] = JHtml::_('select.booleanlist', 'activate_invoice_feature', '', $config->activate_invoice_feature);
		$lists['reset_invoice_number'] = JHtml::_('select.booleanlist', 'reset_invoice_number', '', $config->reset_invoice_number);
		$lists['reset_membership_id'] = JHtml::_('select.booleanlist', 'reset_membership_id', '', $config->reset_membership_id);
		$lists['synchronize_data'] = JHtml::_('select.booleanlist', 'synchronize_data', '', isset($config->synchronize_data) ? $config->synchronize_data : 1);
		$lists['send_invoice_to_customer'] = JHtml::_('select.booleanlist', 'send_invoice_to_customer', '', $config->send_invoice_to_customer);
		$lists['send_invoice_to_admin'] = JHtml::_('select.booleanlist', 'send_invoice_to_admin', '', $config->send_invoice_to_admin);
		$lists['send_activation_email'] = JHtml::_('select.booleanlist', 'send_activation_email', '', $config->send_activation_email);
		$lists['create_account_when_membership_active'] = JHtml::_('select.booleanlist', 'create_account_when_membership_active', '', $config->create_account_when_membership_active);
		$lists['disable_notification_to_admin'] = JHtml::_('select.booleanlist', 'disable_notification_to_admin', '', $config->disable_notification_to_admin);
		$lists['hide_details_button'] = JHtml::_('select.booleanlist', 'hide_details_button', '', $config->hide_details_button);
		$lists['use_email_as_username'] = JHtml::_('select.booleanlist', 'use_email_as_username', '', $config->use_email_as_username);

		$options = array();
		$options[] = JHtml::_('select.option', '', JText::_('OSM_SELECT_FORMAT'));
		$options[] = JHtml::_('select.option', '%Y-%m-%d', 'Y-m-d');
		$options[] = JHtml::_('select.option', '%Y/%m/%d', 'Y/m/d');
		$options[] = JHtml::_('select.option', '%Y.%m.%d', 'Y.m.d');
		$options[] = JHtml::_('select.option', '%m-%d-%Y', 'm-d-Y');
		$options[] = JHtml::_('select.option', '%m/%d/%Y', 'm/d/Y');
		$options[] = JHtml::_('select.option', '%m.%d.%Y', 'm.d.Y');
		$options[] = JHtml::_('select.option', '%d-%m-%Y', 'd-m-Y');
		$options[] = JHtml::_('select.option', '%d/%m/%Y', 'd/m/Y');
		$options[] = JHtml::_('select.option', '%d.%m.%Y', 'd.m.Y');
		$lists['date_field_format'] = JHtml::_('select.genericlist', $options, 'date_field_format', '', 'value', 'text', isset($config->date_field_format) ? $config->date_field_format : 'Y-m-d');

		$this->lists = $lists;
		$this->config = $config;

		parent::display();
	}
}