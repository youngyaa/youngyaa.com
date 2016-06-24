<?php
/**
 * ------------------------------------------------------------------------
 * JA Events II template
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2011 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - Copyrighted Commercial Software
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites:  http://www.joomlart.com -  http://www.joomlancers.com
 * This file may not be redistributed in whole or significant part.
 * ------------------------------------------------------------------------
 */

defined('_JEXEC') or die;

JHtml::_('behavior.framework', true);
JHtml::_('behavior.core');
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::stylesheet('com_finder/finder.css', false, true, false);
?>

<div class="finder<?php echo $this->pageclass_sfx; ?>">
	<div class="search-box-border">
		<?php if ($this->params->get('show_page_heading')) : ?>
			<h1>
				<?php if ($this->escape($this->params->get('page_heading'))) : ?>
					<?php echo $this->escape($this->params->get('page_heading')); ?>
				<?php else : ?>
					<?php echo $this->escape($this->params->get('page_title')); ?>
				<?php endif; ?>
			</h1>
		<?php endif; ?>

		<?php if ($this->params->get('show_search_form', 1)) : ?>
			<div id="search-form">
				<?php echo $this->loadTemplate('form'); ?>
			</div>
		<?php endif; ?>

	</div>
	<?php
	// Load the search results layout if we are performing a search.
	if ($this->query->search === true):
	?>
		<div id="search-results">
			<?php echo $this->loadTemplate('results'); ?>
		</div>
	<?php endif; ?>
</div>

<script>
(function($) {
	$(document).ready(function() {
		$('#finder-search').on('keyup', function(e) {
			var btn = $('#smartsearch-btn', '#finder-search');
			if ($('#q', '#finder-search').val().length >= 3) {
				btn.removeClass('disabled');
			} else {
				btn.addClass('disabled');
			}
		})
	})
} (jQuery));
</script>