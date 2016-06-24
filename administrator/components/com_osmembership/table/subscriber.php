<?php
/**
 * Subscribers Table Class
 *
 */
class OSMembershipTableSubscriber extends JTable
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
