/**
 * @package         Better Preview
 * @version         5.0.1
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            http://www.regularlabs.com
 * @copyright       Copyright © 2016 Regular Labs All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

(function($) {
	"use strict";

	$(document).ready(function() {
		$('div.betterpreview_message, div.betterpreview_error').click(function(e) {
			$(this).fadeOut();
			e.stopPropagation();
		});
		$('html').click(function() {
			$('div.betterpreview_message, div.betterpreview_error').fadeOut();
		});
	});
})(jQuery);
