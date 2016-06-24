<?php
/**
 * State Table Class
 *
 */
class OSMembershipTableState extends JTable
{

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function __construct(& $db)
	{
		parent::__construct('#__osmembership_states', 'id', $db);
	}
}
