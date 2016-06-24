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

class OSMembershipModelSubscription extends MPFModel
{

	/**
	 * Instantiate the model.
	 *
	 * @param array $config configuration data for the model
	 *
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		$this->state->insert('id', 'int', 0);
	}

	/**
	 * Load subscription record from database
	 *
	 * @return mixed
	 */
	public function getData()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('tbl.*')
			->select('b.lifetime_membership, b.title AS plan_title, b.enable_renewal, b.recurring_subscription, DATEDIFF(NOW(), from_date) AS presence')
			->from('#__osmembership_subscribers AS tbl')
			->leftJoin('#__osmembership_plans AS b ON tbl.plan_id = b.id')
			->where('tbl.id = '. $this->state->id);
		$db->setQuery($query);

		return $db->loadObject();
	}
}