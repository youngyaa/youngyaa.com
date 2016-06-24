(function($){
	$.fn.metroslide=function(settings){
		//intial global variable
		
		var ms = $(this);
			
			ms._items = ms.find('.metro-item');
			ms._inProgress = 0,
			ms._flag = false,
			ms._numberSlide = 0,
			ms._numberItem = 0,
			ms._currentItem = 0,
			ms._currentSlide = 0,
			ms._width = 0, ms._height = 0;
			
		//inital and extend settings
		settings = $.extend({
			mode: 'slide', //mode of metro animation: slide, flip
			direction: 'ltr', //direction of animation: ltr, rtl, btt, ttb
			autoplay: true, // animate metro item automatic
			interval : 3000, //time between each animation
			speed: 300, //speed of animation
			pauseOnHover: true, //pause all animatation of slide when mouse over slide
			animateOnHover: true, // slide or flip metro item when mouse over it
			preloadImages: true, // preload images
			visible: 0, //number of item per slide which you can be see
			itemWidth: 0, //specify width of item
			itemHeight: 0, //specify height of item
			animateComplete: function(){}
		}, settings);
		
		/*
		 * Prepare and call all function neccesary
		 */
		function _initial(){
			
			//checked ie9 
			if(/MSIE/i.test(navigator.userAgent)) settings.mode = 'slide';
			//calculate size of slide if they are not defined

			//calculate size of item if they are not defined
			settings.originalWidth = settings.itemWidth*0.75;
			settings.originalHeight = settings.itemHeight*0.75;
			settings.ratio = settings.itemHeight/settings.itemWidth;

			var wrapperWidth = ms.width();
			var itemPerRow= Math.floor(wrapperWidth/settings.originalWidth);
			if(itemPerRow==0) itemPerRow = 1;
			if(itemPerRow > ms._items.size()) itemPerRow = ms._items.size();

			var itemPerWidth=Math.floor(wrapperWidth/itemPerRow);
			settings.itemWidth = itemPerWidth - parseInt(ms.find('.metro-item:first').css('margin-left')) - parseInt(ms.find('.metro-item:first').css('margin-right'));
			settings.itemHeight = Math.floor(settings.itemWidth*settings.ratio);
			ms.find('.metro-item').css({width:settings.itemWidth,height:settings.itemHeight});
			if(settings.visible){
				var itemPerCol = Math.ceil(settings.visible/itemPerRow);
				var marginBottom = parseInt(ms.find('.metro-item:first').css('margin-bottom')) + parseInt(ms.find('.metro-item:first').css('margin-top'))
				ms.css({height:(settings.itemHeight+marginBottom)*itemPerCol-marginBottom});
			}
			var timeOut = 0;
			ms._width = ms.width();
			ms._height = ms.height();
			$(window).resize(function(){
				clearTimeout(timeOut)
				timeOut = setTimeout(function(){
				var wrapperWidth = ms.width();
				var itemPerRow= Math.floor(wrapperWidth/settings.originalWidth);
				if(itemPerRow==0) itemPerRow = 1;
				if(itemPerRow > ms._items.size()) itemPerRow = ms._items.size();
				var itemPerWidth=Math.floor(wrapperWidth/itemPerRow);
				settings.itemWidth = itemPerWidth - parseInt(ms.find('.metro-item:first').css('margin-left')) - parseInt(ms.find('.metro-item:first').css('margin-right'));
				settings.itemHeight = Math.floor(settings.itemWidth*settings.ratio);
				ms.find('.metro-item').css({width:settings.itemWidth,height:settings.itemHeight});
				if(settings.visible){
					var itemPerCol = Math.ceil(settings.visible/itemPerRow);
					var marginBottom = parseInt(ms.find('.metro-item:first').css('margin-bottom')) + parseInt(ms.find('.metro-item:first').css('margin-top'))
					ms.css({height:(settings.itemHeight+marginBottom)*itemPerCol-marginBottom});
				}
				if(settings.mode == 'slide'){ 
					var top = 0,
					left = 0,
					toTop = 0,
					toLeft = 0;
					
					switch(settings.direction){
						case 'ltr': 
							left = -1 * settings.itemWidth;
							toLeft = settings.itemWidth;
							break;
						case 'rtl':
							left = settings.itemWidth;
							toLeft = -1 * settings.itemWidth;
							break;
						case 'ttb': 
							top = -1 * settings.itemHeight;
							toTop = settings.itemHeight;
							break;
						case 'btt':
							top = settings.itemHeight;
							toTop = -1 * settings.itemHeight;
							break;
						default: 
					}
					ms.find('.mi-back').css({top: top, left: left});
				}
				ms._width = ms.width();
				ms._height = ms.height();
			},100);
			})
			
			//calculate total number of metro-item
			ms._numberItem = ms._items.length;
			
			//render metro-slide
			var tmpSlideIndex = 1;
			var metroSlide = null;
			for(var i = 0; i < ms._numberItem; i++){
				if(metroSlide != null) {
					ms.append(metroSlide);
					
				}
				ms._items.eq(i).attr('id', 'metro-item-' + (i+1));				
				if(i % settings.visible == 0){
					metroSlide = $('<div>').addClass('metro-slide').attr('id', 'metro-slide-' + tmpSlideIndex++);
				}
				if(metroSlide != null) {
					metroSlide.append(ms._items.eq(i));
				}
			}
			//check for last slide which hasn't appended yet
			if(ms.find(metroSlide).length == 0) {
				ms.append(metroSlide);
			}
			//calculate total number of metro slide
			ms._numberSlide = ms.find('.metro-slide').length;

			
			
			if(settings.mode == 'flip'){
				ms.find('.metro-item').addClass('flip');
			}
			//if allowing animate item on mouser over
			if(settings.animateOnHover){
				ms.find('.metro-item').hover(function(){
					$('.mi-back', this).stop();			
					_animate($(this).attr('id').replace('metro-item-', ''));					
				},function(){
					$('.mi-back', this).stop();
					_animate($(this).attr('id').replace('metro-item-', ''));
				});
			}
			
			
			//auto play
			if(settings.autoplay){
				var time = 3 * settings.speed + settings.interval;
				
				var interval = setInterval(function(){_play()}, time);
				
				if(settings.pauseOnHover){
					ms.hover(function(){
						clearInterval(interval);
					}, function(){
						interval = setInterval(function(){_play()}, time);
					});
					
				}
			}
			
			//init css properties for all items
			if(ms.find('.metro-slide').length > 0 ){
				ms.find('.metro-slide').css({display: 'none'}).eq(0).css({display: ''});
			}
		}
		function _play(){
			if(((settings.visible != 0 && ms._currentItem % settings.visible == 0 && ms._currentItem != 0) || ms._currentItem == ms._numberItem) && ms._flag  ){
				ms._currentSlide = (ms._currentSlide + 1) > ms._numberSlide ? 1 : ms._currentSlide + 1;
				_next(ms._currentSlide, 'slide');
				ms._flag = false;
				
			}else{
				ms._currentItem = (ms._currentItem + 1) > ms._numberItem ? 1 : ms._currentItem + 1;
				_next(ms._currentItem, 'item');
				ms._flag = true;
			}
		}

		function _next(current, type){
			if(type == 'item'){
				
				_animate(current);
				setTimeout(function(){_animate(current)}, settings.interval);
				
			}else{
				_animateSlide(current);
			}
			
		};
		function _animate(index){
			ms._inProgress = 1;

			if(settings.mode == 'slide'){
				_slide(index);
			}else{
				_flip(index);
			}
		};
		function _stopAnimate(index){
			ms.find('#metro-item-' + index).delay(setting.speed);
			
		};
		function _slide(index){
			var item = ms.find('#metro-item-' + index),
				//front = item.find('.mi-s-front').length > 0 ? item.find('.mi-s-front') : item.find('.mi-front'),
				back = item.find('.mi-back'),
				top = 0,
				left = 0,
				toTop = 0,
				toLeft = 0;
				
				switch(settings.direction){
					case 'ltr': 
						left = -1 * settings.itemWidth;
						toLeft = settings.itemWidth;
						break;
					case 'rtl':
						left = settings.itemWidth;
						toLeft = -1 * settings.itemWidth;
						break;
					case 'ttb': 
						top = -1 * settings.itemHeight;
						toTop = settings.itemHeight;
						break;
					case 'btt':
						top = settings.itemHeight;
						toTop = -1 * settings.itemHeight;
						break;
					default: 
						throw Error("metroSlide: No direction is setted.");
					
				}
				if(back.hasClass('mi-s-front')){
					back.animate({top: top, left: left}, settings.speed).removeClass('mi-s-front');
					
				}else{
					back.css({position: 'absolute', top: top, left: left, 'z-index': 30});
					back.addClass('mi-s-front').animate({top: '0px', left: '0px'}, settings.speed);
					
				}
		};
		function _flip(index){
			var item = ms.find('#metro-item-' + index);
			
			item.find('.mi-front').css({
				'transition': 'all ' + settings.speed / 1000 + 's ease-in-out'
				
			});
			item.find('.mi-back').css({
				'transition': 'all ' + settings.speed / 1000 + 's ease-in-out'
			});
			if(item.hasClass('flipped')){
				setTimeout(function(){ item.removeClass('flipped')}, settings.speed);
			}else{
				item.addClass('flipped');
			}
			
			
		};
		function _animateSlide(index){
			var currentSlide = ms.find('#metro-slide-' + index), nextSlide = null;
			
			if(index == ms.find('.metro-slide').length){
				nextSlide = ms.find('#metro-slide-1');
			}else{
				nextSlide = ms.find('#metro-slide-' + (index + 1));
			}
			var  top = 0,
				left = 0,
				toTop = 0,
				toLeft = 0;
				
			switch(settings.direction){
				case 'ltr': 
					left = -1 * ms._width;
					toLeft = ms._width;
					break;
				case 'rtl':
					left = ms._width;
					toLeft = -1 * ms._width;
					break;
				case 'ttb': 
					top = -1 * ms._height;
					toTop = ms._height;
					break;
				case 'btt':
					top = ms._height;
					toTop = -1 * ms._height;
					break;
				default: 
					throw Error("metroSlide: No direction is setted.");
				
			}
			nextSlide.show().css({top: top, left: left});
			currentSlide.animate({top: toTop, left: toLeft}, settings.speed);
			nextSlide.animate({top: 0, left: 0}, settings.speed, function(){
				
			});
				
		};
		return _initial();
	}
})(jQuery);