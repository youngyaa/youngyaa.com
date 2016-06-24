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
	<h1 class="osm-page-title"><?php echo JText::_('OSM_SUBSCRIPTION_FAILURE'); ?></h1>
	<form class="form form-horizontal">
		<div class="control-group osm-message">
			<?php echo JText::_('OSM_FAILURE_MESSAGE'); ?>
		</div>
		<div class="control-group">
			<label class="control-label">
				<?php echo  JText::_('OSM_REASON') ?>
			</label>
			<div class="controls">
				<p class="osm-message"><?php echo $this->reason; ?></p>
			</div>
		</div>
		<div class="form-actions">
			<input type="button" class="button btn btn-primary" value="<?php echo JText::_('OSM_BACK'); ?>" onclick="window.history.go(-1);" />
		</div>
	</form>
</div>