<?php

defined( '_JEXEC' ) or die( 'Restricted access' );
define('CLOUDFLARE_VERSION', '0.1.5');
require_once("IpRange.php");
require_once("IpRewrite.php");
 
jimport( 'joomla.plugin.plugin' );
 
class PlgSystemCloudFlare extends JPlugin
{
	public function __construct( &$subject, $config )
	{
		parent::__construct( $subject, $config );
	}
 
	function onAfterInitialise()
	{
		global $is_cf;

		$is_cf = CloudFlare\IpRewrite::isCloudFlare();

		// Let people know that the CF plugin is turned on.
		if (!headers_sent()) 
		{
		    header("X-CF-Powered-By: CF-Joomla " . CLOUDFLARE_VERSION);
		}

	}

}
