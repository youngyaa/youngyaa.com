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

abstract class OSMembershipHelperJquery
{
	/**
	 * validate form
	 */
	public static function validateForm()
	{
		$document    = JFactory::getDocument();
		$config      = OSMembershipHelper::getConfig();
		$dateFormat  = $config->date_field_format ? $config->date_field_format : '%Y-%m-%d';
		$dateFormat  = str_replace('%', '', $dateFormat);
		$humanFormat = str_replace('Y', 'YYYY', $dateFormat);
		$humanFormat = str_replace('m', 'MM', $humanFormat);
		$humanFormat = str_replace('d', 'DD', $humanFormat);

		if (strpos($dateFormat, '.') !== false)
		{
			$dateParts = explode('.', $dateFormat);
			$separator = '.';
		}

		if (strpos($dateFormat, '-') !== false)
		{
			$dateParts = explode('-', $dateFormat);
			$separator = '-';
		}

		if (strpos($dateFormat, '/') !== false)
		{
			$dateParts = explode('/', $dateFormat);
			$separator = '/';
		}

		$yearIndex  = array_search('Y', $dateParts);
		$monthIndex = array_search('m', $dateParts);
		$dayIndex   = array_search('d', $dateParts);
		
		$regex 	 = $dateFormat;
		$regex   = str_replace($separator, '[\\' . $separator . ']', $regex);
		$regex   = str_replace('d', '(0?[1-9]|[12][0-9]|3[01])', $regex);
		$regex   = str_replace('Y', '(\d{4})', $regex);
		$regex   = str_replace('m', '(0?[1-9]|1[012])', $regex);
		$regex   = 'var pattern = new RegExp(/^' . $regex . '$/);';		
				
		$siteUrl = OSMembershipHelper::getSiteUrl();
		$document->addStyleSheet(JUri::root(true) . '/components/com_osmembership/assets/js/validate/css/validationEngine.jquery.css');
		$document->addScriptDeclaration("
			var yearPartIndex = $yearIndex;
			var monthPartIndex = $monthIndex;
			var dayPartIndex = $dayIndex;
			var customDateFormat = '$dateFormat';
		");
		$document->addScriptDeclaration('
			OSM.jQuery(function($){
			    $.fn.validationEngineLanguage = function(){
			    };
			    $.validationEngineLanguage = {
			        newLang: function(){
			            $.validationEngineLanguage.allRules = {
			                "required": { // Add your regex rules here, you can take telephone as an example
			                    "regex": "none",
			                    "alertText": "' . JText::_('OSM_FIELD_REQUIRED') . '",
			                    "alertTextCheckboxMultiple": "' . JText::_('OSM_PLEASE_SELECT_AN_OPTION') . '",
			                    "alertTextCheckboxe": "' . JText::_('OSM_CHECKBOX_REQUIRED') . '",
			                    "alertTextDateRange": "' . JText::_('OSM_BOTH_DATE_RANGE_FIELD_REQUIRED') . '"
			                },
			                "requiredInFunction": {
			                    "func": function(field, rules, i, options){
			                        return (field.val() == "test") ? true : false;
			                    },
			                    "alertText": "' . JText::_('OSM_FIELD_MUST_EQUAL_TEST') . '"
			                },
			                "dateRange": {
			                    "regex": "none",
			                    "alertText": "' . JText::_('OSM_INVALID') . '",
			                    "alertText2": "Date Range"
			                },
			                "dateTimeRange": {
			                    "regex": "none",
			                    "alertText": "' . JText::_('OSM_INVALID') . '",
			                    "alertText2": "' . JText::_('OSM_DATE_TIME_RANGE') . '"
			                },
			                "minSize": {
			                    "regex": "none",
			                    "alertText": "' . JText::_('OSM_MINIMUM') . ' ",
			                    "alertText2": " '. JText::_('OSM_CHARACTERS_REQUIRED') . ' "
			                },
			                "maxSize": {
			                    "regex": "none",
			                    "alertText": "' . JText::_('OSM_MAXIMUM') . '",
			                    "alertText2": "' . JText::_('OSM_CHACTERS_ALLOWED') . '"
			                },
							"groupRequired": {
			                    "regex": "none",
			                    "alertText": "' . JText::_('OSM_GROUP_REQUIRED') . '"
			                },
			                "min": {
			                    "regex": "none",
			                    "alertText": "' . JText::_('OSM_MIN') . '"
			                },
			                "max": {
			                    "regex": "none",
			                    "alertText": "' . JText::_('OSM_MAX') . '"
			                },
			                "past": {
			                    "regex": "none",
			                    "alertText": "' . JText::_('OSM_DATE_PRIOR_TO') . '"
			                },
			                "future": {
			                    "regex": "none",
			                    "alertText": "' . JText::_('OSM_DATE_PAST') . '"
			                },
			                "maxCheckbox": {
			                    "regex": "none",
			                    "alertText": "' . JText::_('OSM_MAXIMUM') . '",
			                    "alertText2": "' . JText::_('OSM_OPTION_ALLOW') . '"
			                },
			                "minCheckbox": {
			                    "regex": "none",
			                    "alertText": "' . JText::_('OSM_PLEASE_SELECT') . '",
			                    "alertText2": " options"
			                },
			                "equals": {
			                    "regex": "none",
			                    "alertText": "' . JText::_('OSM_FIELDS_DO_NOT_MATCH') . '"
			                },
			                "creditCard": {
			                    "regex": "none",
			                    "alertText": "' . JText::_('OSM_INVALID_CREDIT_CARD_NUMBER') . '"
			                },
			                "phone": {
			                    // credit: jquery.h5validate.js / orefalo
			                    "regex": /^([\+][0-9]{1,3}[\ \.\-])?([\(]{1}[0-9]{2,6}[\)])?([0-9\ \.\-\/]{3,20})((x|ext|extension)[\ ]?[0-9]{1,4})?$/,
			                    "alertText": "' . JText::_('OSM_INVALID_PHONE_NUMBER') . '"
			                },
			                "email": {
			                    // HTML5 compatible email regex ( http://www.whatwg.org/specs/web-apps/current-work/multipage/states-of-the-type-attribute.html#    e-mail-state-%28type=email%29 )
			                    "regex": /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/,
			                    "alertText": "' . JText::_('OSM_INVALID_EMAIL_ADDRESS') . '"
			                },
			                "integer": {
			                    "regex": /^[\-\+]?\d+$/,
			                    "alertText": "' . JText::_('OSM_NOT_A_VALID_INTEGER') . '"
			                },
			                "number": {
			                    // Number, including positive, negative, and floating decimal. credit: orefalo
			                    "regex": /^[\-\+]?((([0-9]{1,3})([,][0-9]{3})*)|([0-9]+))?([\.]([0-9]+))?$/,
			                    "alertText": "' . JText::_('OSM_INVALID_FLOATING_DECIMAL_NUMBER') . '"
			                },
			                "date": {
			                    //	Check if date is valid by leap year
							"func": function (field) {
								' . $regex . '
								var match = pattern.exec(field.val());
								if (match == null)
								   return false;
	
								var year = match[yearPartIndex + 1];
								var month = match[monthPartIndex + 1]*1;
								var day = match[dayPartIndex + 1]*1;
								var date = new Date(year, month - 1, day); // because months starts from 0.
	
								return (date.getFullYear() == year && date.getMonth() == (month - 1) && date.getDate() == day);
							},
						 	"alertText": "' . str_replace('YYYY-MM-DD', $humanFormat, JText::_('OSM_INVALID_DATE')) . '"
			                },
			                "ipv4": {
			                    "regex": /^((([01]?[0-9]{1,2})|(2[0-4][0-9])|(25[0-5]))[.]){3}(([0-1]?[0-9]{1,2})|(2[0-4][0-9])|(25[0-5]))$/,
			                    "alertText": "' . JText::_('OSM_INVALID_IP_ADDRESS') . '"
			                },
			                "url": {
			                    "regex": /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/,
			                    "alertText": "' . JText::_('OSM_INVALID_URL') . '"
			                },
			                "onlyNumberSp": {
			                    "regex": /^[0-9\ ]+$/,
			                    "alertText": "' . JText::_('OSM_NUMBER_ONLY') . '"
			                },
			                "onlyLetterSp": {
			                    "regex": /^[a-zA-Z\ \']+$/,
			                    "alertText": "' . JText::_('OSM_LETTERS_ONLY') . '"
			                },
			                "onlyLetterNumber": {
			                    "regex": /^[0-9a-zA-Z]+$/,
			                    "alertText": "' . JText::_('OSM_NO_SPECIAL_CHACTERS_ALLOWED') . '"
			                },
			                // --- CUSTOM RULES -- Those are specific to the demos, they can be removed or changed to your likings
			                "ajaxUserCall": {
			                	"url": "' . $siteUrl . 'index.php?option=com_osmembership&task=validator.validate_username",
			                    // you may want to pass extra data on the ajax call
			                    "extraData": "name=eric",
			                    "alertText": "' . JText::_('OSM_INVALID_USERNAME') . '",
			                },
			                "ajaxEmailCall": {
			                	"url": "' . $siteUrl . 'index.php?option=com_osmembership&task=validator.validate_email",
			                    // you may want to pass extra data on the ajax call
			                    "extraData": "name=eric",
			                    "alertText": "' . JText::_('OSM_INVALID_EMAIL') . '",
			                },
			                "ajaxValidateGroupMemberEmail": {
			                	"url": "' . $siteUrl . 'index.php?option=com_osmembership&task=validator.validate_group_member_email",
			                    // you may want to pass extra data on the ajax call
			                    "extraData": "email=eric",
			                    "alertText": "' . JText::_('OSM_INVALID_EMAIL') . '",
			                },
							"ajaxValidatePassword": {
			                	"url": "' . $siteUrl . 'index.php?option=com_osmembership&task=validator.validate_password",
			                    "alertText": "' . JText::_('OSM_INVALID_PASSWORD') . '",
			                },
				            //tls warning:homegrown not fielded
			                "dateFormat":{
			                    "regex": /^\d{4}[\/\-](0?[1-9]|1[012])[\/\-](0?[1-9]|[12][0-9]|3[01])$|^(?:(?:(?:0?[13578]|1[02])(\/|-)31)|(?:(?:0?[1,3-9]|1[0-2])(\/|-)(?:29|30)))(\/|-)(?:[1-9]\d\d\d|\d[1-9]\d\d|\d\d[1-9]\d|\d\d\d[1-9])$|^(?:(?:0?[1-9]|1[0-2])(\/|-)(?:0?[1-9]|1\d|2[0-8]))(\/|-)(?:[1-9]\d\d\d|\d[1-9]\d\d|\d\d[1-9]\d|\d\d\d[1-9])$|^(0?2(\/|-)29)(\/|-)(?:(?:0[48]00|[13579][26]00|[2468][048]00)|(?:\d\d)?(?:0[48]|[2468][048]|[13579][26]))$/,
			                    "alertText": "' . JText::_('OSM_INVALID_DATE') . '"
			                },
			                //tls warning:homegrown not fielded
							"dateTimeFormat": {
				                "regex": /^\d{4}[\/\-](0?[1-9]|1[012])[\/\-](0?[1-9]|[12][0-9]|3[01])\s+(1[012]|0?[1-9]){1}:(0?[1-5]|[0-6][0-9]){1}:(0?[0-6]|[0-6][0-9]){1}\s+(am|pm|AM|PM){1}$|^(?:(?:(?:0?[13578]|1[02])(\/|-)31)|(?:(?:0?[1,3-9]|1[0-2])(\/|-)(?:29|30)))(\/|-)(?:[1-9]\d\d\d|\d[1-9]\d\d|\d\d[1-9]\d|\d\d\d[1-9])$|^((1[012]|0?[1-9]){1}\/(0?[1-9]|[12][0-9]|3[01]){1}\/\d{2,4}\s+(1[012]|0?[1-9]){1}:(0?[1-5]|[0-6][0-9]){1}:(0?[0-6]|[0-6][0-9]){1}\s+(am|pm|AM|PM){1})$/,
			                    "alertText": "* Invalid Date or Date Format",
			                    "alertText2": "' . JText::_('OSM_EXPECTED_FORMAT') . '",
			                    "alertText3": "mm/dd/yyyy hh:mm:ss AM|PM or ",
			                    "alertText4": "yyyy-mm-dd hh:mm:ss AM|PM"
				            }
			            };
			     
			        }
			    };
			    $.validationEngineLanguage.newLang();
			});
		');
		self::addScript(JUri::root(true) . '/components/com_osmembership/assets/js/validate/js/jquery.validationEngine.js');
	}

	public static function addScript($sources)
	{
		$script = '<script type="text/javascript" src="' . $sources . '"></script>';
		echo $script;
	}

	/**
	 * Equal Heights Plugin
	 * Equalize the heights of elements. Great for columns or any elements
	 * that need to be the same size (floats, etc).
	 *
	 * Version 1.0
	 * Updated 12/10/2008
	 *
	 * Copyright (c) 2008 Rob Glazebrook (cssnewbie.com)
	 *
	 * Usage: $(object).equalHeights([minHeight], [maxHeight]);
	 *
	 * Example 1: $(".cols").equalHeights(); Sets all columns to the same height.
	 * Example 2: $(".cols").equalHeights(400); Sets all cols to at least 400px tall.
	 * Example 3: $(".cols").equalHeights(100,300); Cols are at least 100 but no more
	 * than 300 pixels tall. Elements with too much content will gain a scrollbar.
	 *
	 */
	public static function equalHeights()
	{
		static $loaded = false;
		if (!$loaded)
		{
			$script = 'OSM.jQuery(function($) { $.fn.equalHeights = function(minHeight, maxHeight) { tallest = (minHeight) ? minHeight : 0;this.each(function() {if($(this).height() > tallest) {tallest = $(this).height();}});if((maxHeight) && tallest > maxHeight) tallest = maxHeight;return this.each(function() {$(this).height(tallest).css("overflow","auto");});}});';
			JFactory::getDocument()->addScriptDeclaration($script);
		}
		$loaded = true;
	}
}