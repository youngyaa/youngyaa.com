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

jimport('joomla.plugin.plugin');
error_reporting(0);
class plgContentMPRestriction extends JPlugin
{

	public function onContentPrepare($context, &$row, &$params, $page = 0)
	{
		if (file_exists(JPATH_ROOT . '/components/com_osmembership/osmembership.php'))
		{
			if (is_object($row))
			{
				// Check whether the plugin should process or not
				if (JString::strpos($row->text, 'mprestriction') === false)
				{
					return true;
				}
				// Search for this tag in the content
				$regex = '#{mprestriction ids="(.*?)"}(.*?){/mprestriction}#s';
				$row->text = preg_replace_callback($regex, array(&$this, 'processRestriction'), $row->text);
			}
		}
		
		return true;
	}

	function processRestriction($matches)
	{
		$document = JFactory::getDocument();
		$styleUrl = JURI::base(true) . '/components/com_osmembership/assets/css/style.css';
		$document->addStylesheet($styleUrl, 'text/css', null, null);
		require_once JPATH_ROOT . '/components/com_osmembership/helper/helper.php';
		$message = OSMembershipHelper::getMessages();
		$fieldSuffix = OSMembershipHelper::getFieldSuffix();
		if (strlen($message->{'content_restricted_message' . $fieldSuffix}))
		{
			$restrictedText = $message->{'content_restricted_message' . $fieldSuffix};
		}
		else
		{
			$restrictedText = $message->content_restricted_message;
		}				
		$requiredPlanIds = $matches[1];
		$protectedText = $matches[2];
		$activePlanIds = OSMembershipHelper::getActiveMembershipPlans();
		if (count($activePlanIds) == 1 && $activePlanIds[0] == 0)
		{
			return '<div id="restricted_info">' . $restrictedText . '</div>';
		}
		elseif ($requiredPlanIds == '*')
		{
			return $protectedText;
		}
		else
		{
			$requiredPlanIds = explode(',', $requiredPlanIds);
			if (count(array_intersect($requiredPlanIds, $activePlanIds)))
			{
				return $protectedText;
			}
			else
			{
				return '<div id="restricted_info">' . $restrictedText . '</div>';
			}
		}
	}
}