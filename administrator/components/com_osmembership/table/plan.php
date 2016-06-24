<?php
/**
 * Subscription Plan Table Class
 *
 */
class OSMembershipTablePlan extends JTable
{

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function __construct(& $db)
	{
		parent::__construct('#__osmembership_plans', 'id', $db);
	}
}
