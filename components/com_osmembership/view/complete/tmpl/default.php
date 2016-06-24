<?php
/**
 * @version        2.2.1
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2016 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
// no direct access
defined( '_JEXEC' ) or die ;
?>
<div id="osm-subscription-complete" class="osm-container">
	<h1 class="osm-page-title"><?php echo JText::_('OSM_SUBSCRIPTION_COMPLETE'); ?></h1>
	<p class="osm-message"><?php echo $this->message; ?></p>
</div>
<?php
	echo $this->conversionTrackingCode;
?>
