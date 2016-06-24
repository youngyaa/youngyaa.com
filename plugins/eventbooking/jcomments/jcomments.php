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

class plgEventBookingJcomments extends JPlugin
{
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_eventbooking/table');
	}

	public function onEventDisplay($row)
	{
		ob_start();
		$this->displayCommentForm($row);
		$form = ob_get_contents();
		ob_end_clean();

		return array('title' => JText::_('Comment'),
		             'form'  => $form
		);
	}

	/**
	 * Display form allows users to add comments about the event via JComments
	 *
	 * @param object $row
	 */
	private function displayCommentForm($row)
	{
		$comments = JPATH_ROOT . '/components/com_jcomments/jcomments.php';
		if (file_exists($comments))
		{
			require_once($comments);
			echo '<div style="clear:both; padding-top: 10px;"></div>';
			echo JComments::showComments($row->id, 'com_eventbooking', $row->title);
		}
	}
}	