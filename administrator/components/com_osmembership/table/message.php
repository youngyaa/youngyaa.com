<?php
/**
 * Messages Table Class
 *
 */
class OSMembershipTableMessage extends JTable
{

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function __construct(& $db)
	{
		parent::__construct('#__osmembership_messages', 'id', $db);
	}
}
