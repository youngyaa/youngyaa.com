<?php
/**
 * @package     com_bt_media - BT Media
 * @version	1.0.0
 * @created	Oct 2012
 * @author	BowThemes
 * @email	support@bowthems.com
 * @website	http://bowthemes.com
 * @support	Forum - http://bowthemes.com/forum/
 * @copyright   Copyright (C) 2012 Bowthemes. All rights reserved.
 * @license	http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');

/**
 * Supports an HTML select list of categories
 */
class JFormFieldTimecreated extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'timecreated';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput()
	{
		// Initialize variables.
		$html = array();
        
		$time_created = $this->value;
		if (!$time_created) {
			$time_created = date("Y-m-d H:i:s");
			$html[] = '<input type="hidden" name="'.$this->name.'" value="'.$time_created.'" />';
		}
		$jdate = new JDate($time_created);
		$pretty_date = $jdate->format(JText::_('DATE_FORMAT_LC2'));
		$html[] = "<div>".$pretty_date."</div>";
        
		return implode($html);
	}
}