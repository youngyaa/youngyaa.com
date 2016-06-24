/**
 * ------------------------------------------------------------------------
 * JA Login module for J25 & J3x
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2011 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites: http://www.joomlart.com - http://www.joomlancers.com
 * ------------------------------------------------------------------------
 */
var jaboxes = [];
var jaboxoverlay = null;
showBox = function (box,focusobj, caller, e) {
	//Add overlay
	if (!jaboxoverlay) {
		jaboxoverlay = new Element ('div', {id:"jabox-overlay"}).inject ($(box),'before');
		jaboxoverlay.setStyle ('opacity', 0.01);
		jaboxoverlay.addEvent ('click', function (e) {
			jaboxes.each(function(box){
				if (box.status=='show') {
					box.status = 'hide';
					box.setStyle('visibility','hidden');
					var fx = new Fx.Tween (box);
					fx.pause();
					fx.start ('opacity',box.getStyle('opacity'), 0);
					if (box._caller) box._caller.removeClass ('show');
					
					if($('system-message')) {
						$('system-message').removeClass('alert');
						$('system-message').removeClass('alert-error');
						$('system-message').innerHTML = '';
					}
					if($('system-message-container')) $('system-message-container').setStyle('display', 'block');
				}
			},this);
			jaboxoverlay.setStyle ('display', 'none');
		});
	} else {
		console.log (box);
		jaboxoverlay.inject ($(box),'before');
	}

	caller.blur();
	//new Event(e).stop ();
	box=$(box);
	if (!box) return;
	if ($(caller)) box._caller = $(caller);
	if (!jaboxes.contains (box)) {
		jaboxes.include (box);
		//box.addEvent ('click', function (e) {/*new Event(e).stop ();*/});
	}
	
	if (box.getStyle('display') == 'none') {
		box.setStyles({
			display: 'block',
			opacity: 0
		});
	}
	if (box.status == 'show') {
		//hide
		box.status = 'hide';
		box.setStyle('visibility','hidden');
		var fx = new Fx.Tween (box);
		fx.pause();
		fx.start ('opacity',box.getStyle('opacity'), 0);
		if (box._caller) box._caller.removeClass ('show');
		jaboxoverlay.setStyle ('display', 'none');
	} else {
		jaboxes.each(function(box1){
			if (box1!=box && box1.status=='show') {
				box1.status = 'hide';
				box1.setStyle('visibility','hidden');
				var fx = new Fx.Tween (box1);
				fx.pause();
				fx.start ('opacity',box1.getStyle('opacity'), 0);
				if (box1._caller) box1._caller.removeClass ('show');
			}
		},this);
		box.status = 'show';
		box.setStyle('visibility','visible');
		var fx = new Fx.Tween (box,{onComplete:function(){if($(focusobj))$(focusobj).focus();}});
		fx.pause();
		fx.start ('opacity',box.getStyle('opacity'), 1);
		
		if (box._caller) box._caller.addClass ('show');
		jaboxoverlay.setStyle ('display', 'block');
		if($('system-message-container')) $('system-message-container').setStyle('display', 'none');
	}
}