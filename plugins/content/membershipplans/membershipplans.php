<?php
/**
 * @version        2.2.1
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2016 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die();
error_reporting(0);

class plgContentMembershipPlans extends JPlugin
{

	function onContentPrepare($context, &$article, &$params, $limitstart)
	{
		if (file_exists(JPATH_ROOT . '/components/com_osmembership/osmembership.php'))
		{
			$app = JFactory::getApplication();
			if ($app->getName() != 'site')
			{
				return;
			}
			if (strpos($article->text, 'membershipplans') === false)
			{
				return true;
			}
			$regex         = '#{membershipplans ids="(.*?)"}#s';
			$article->text = preg_replace_callback($regex, array(&$this, 'displayPlans'), $article->text);
		}

		return true;
	}

	/**
	 * Replace callback function
	 *
	 * @param $matches
	 *
	 * @return string
	 * @throws Exception
	 */
	function displayPlans($matches)
	{
		$planIds     = $matches[1];
		$layout  = $this->params->get('layout_type', 'default');
		require_once JPATH_ADMINISTRATOR . '/components/com_osmembership/loader.php';
		OSMembershipHelper::loadLanguage();
		$request = array('option' => 'com_osmembership', 'view' => 'plans', 'layout' => $layout, 'filter_plan_ids' => $planIds, 'limit' => 0, 'hmvc_call' => 1, 'Itemid' => OSMembershipHelper::getItemid());
		$input   = new MPFInput($request);
		$config  = array(
			'default_controller_class' => 'OSMembershipController',
			'default_view'             => 'plans',
			'class_prefix'             => 'OSMembership',
			'language_prefix'          => 'OSM',
			'remember_states'			=> false,
			'ignore_request'			=> false
		);

		ob_start();

		//Initialize the controller, execute the task
		MPFController::getInstance('com_osmembership', $input, $config)
			->execute();

		return ob_get_clean();
	}
}