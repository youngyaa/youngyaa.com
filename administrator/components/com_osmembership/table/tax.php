<?php
/**
 * Tax rate Table Class
 *
 */
class OSMembershipTableTax extends JTable
{

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function __construct(& $db)
	{
		parent::__construct('#__osmembership_taxes', 'id', $db);
	}
}
