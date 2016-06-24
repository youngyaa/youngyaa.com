<?php
/**
 * Custom Field Value Table Class
 *
 */
class PlanOsMembership extends JTable
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

/**
 * Subscriber Table Class
 *
 */
class SubscriberOSMembership extends JTable
{
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function __construct(& $db)
	{
		parent::__construct('#__osmembership_subscribers', 'id', $db);
	}
}

/**
 * Custom Field Value Table Class
 *
 */
class FieldValueOsMembership extends JTable
{
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function __construct(& $db)
	{
		parent::__construct('#__osmembership_field_value', 'id', $db);
	}
}
