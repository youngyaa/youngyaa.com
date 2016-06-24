btscJQ=jQuery.noConflict();
btscJQ(document).ready(function () {
	jQuery('img.hovereffect').hover(function () {
		jQuery(this).stop(true).animate({
			opacity : 0.5
		}, 300);
	}, function () {
		jQuery(this).animate({
			opacity : 1
		}, 300)
	});
});
