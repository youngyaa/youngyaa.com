$B=jQuery.noConflict();
$B(document).ready(function () {
	$B('img.hovereffect').hover(function () {
		$B(this).stop(true).animate({
			opacity : 0.5
		}, 300);
	}, function () {
		$B(this).animate({
			opacity : 1
		}, 300)
	});
});
