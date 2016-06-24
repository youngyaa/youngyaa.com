<?php
/**
 * Rules Table Class
 *
 */
class OSMembershipTableRule extends JTable
{

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function __construct(& $db)
	{
		parent::__construct('#__osmembership_upgraderules', 'id', $db);
	}
}
