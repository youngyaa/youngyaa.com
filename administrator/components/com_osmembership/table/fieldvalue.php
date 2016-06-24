<?php
/**
 * Custom Fields value Table Class
 *
 */
class OSMembershipTableFieldvalue extends JTable
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
