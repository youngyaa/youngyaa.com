<?php
/**
 * Countries Table Class
 *
 */
class OSMembershipTableCountry extends JTable
{

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function __construct(& $db)
	{
		parent::__construct('#__osmembership_countries', 'id', $db);
	}
}
