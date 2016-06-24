(function($){

	$.fn.bstyle = function(option){
	
		  option = $.extend({}, $.fn.bstyle.option, option);
	
		return this.each(function(){
			var el = $(this).parent()
			var control = $('ul',this);
			var total = $('li',control).size();
			var next = 0, prev = 0, number = 0, current = 0, loaded, active, clicked, position, direction, imageParent, pauseTimeout, playInterval;
			
			// set style for element
			$(this).parent().css({'height': $(this).find('li:first').outerHeight()+'px'});
			$(this).css({'overflow':'hidden'});
			$(this).css({zIndex:5});
			if(option.vertical){
				control.addClass('vertical');
				$(this).css({'margin-left':'120px'});
				//$('li', this).css({'padding':'13px'});
				if(option.generateNextPrev){
					$('.next', el).addClass('next-vertical');
					$('.prev', el).addClass('prev-vertical');
				}
				$('li', this).css({ 'position': 'absolute',display: 'none', width:'auto', height: 'auto'});
			}else{
				control.addClass('horizontal');
				if(option.generateNextPrev){
					$('.next', el).addClass('next-horizontal');
					$('.prev', el).addClass('prev-horizontal');
				}
				$('li',this).css({'float':'left'});
				$(this).css({'margin-left':'120px'});
				$(this).css({'height':'100%','width':'auto'});
				$('li', this).css({'width':(option.width - 140) + 'px', 'position': 'absolute',display: 'none'});
			}
			if(total<2){
				$('.next',control).hide();
				$('.prev',control).hide();
				$('li', this).css({'display': 'block'});
				return false;
			}
			
		//function 
		function animate(direction,vertical){
			if(!active){
				active= true;
				switch(direction) {
					case 'next':
						// change current slide to previous
						prev = current;
						// get next from current + 1
						next = current + 1;
						// if last slide, set next to first slide
						next = total === next ? 0 : next;
						// set position of next slide to right of previous
						position = '66.66%';
						// distance to slide based on width of slides
						direction = '-200%';
						// store new current slide
						current = next;
					break;
					case 'prev':
						// change current slide to previous
						prev = current;
						// get next from current - 1
						next = current - 1;
						// if first slide, set next to last slide
						next = next === -1 ? total-1 : next;								
						// set position of next slide to left of previous
						position = '0%';								
						// distance to slide based on width of slides
						direction = '0%';		
						// store new current slide
						current = next;
					break;
				}
				
				// animate ul
				if(vertical){
					// move next slide to right of previous
					control.children(':eq('+ next +')').css({
						top: position,
						display: 'block'
					});
					control.animate({
						top: direction
					},
					option.slideSpeed,
					function(){
						control.css({
							top: '-100%'
						});
						control.children(':eq('+ next +')').css({
							top: '33.3%',
							zIndex: 5
						});
						// reset previous slide
						control.children(':eq('+ prev +')').css({
							top: '0%',
							display: 'none',
							zIndex: 0
						});
						// end of animation
						active = false;
					}
				);
				}else{
					// move next slide to right of previous
					control.children(':eq('+ next +')').css({
						left: position,
						display: 'block'
					});
					control.animate({
							left: direction
							//height: control.children(':eq('+ next +')').outerHeight()
						},
						option.slideSpeed,
						function(){
							control.css({
								left: '-100%'
							});
							control.children(':eq('+ next +')').css({
								left: '33.3%',
								zIndex: 5
							});
							// reset previous slide
							control.children(':eq('+ prev +')').css({
								left: 0,
								display: 'none',
								zIndex: 0
							});
							// end of animation
							active = false;
						}
					);
				}
			}
			
		}
		function stop() {
			// clear interval from stored id
			clearInterval(el.data('interval'));
		}
		// pause function 
		function pause(){
			if (option.pause) {
				// clear timeout and interval
				clearTimeout(el.data('pause'));
				clearInterval(el.data('interval'));
				// pause slide show for option.pause amount
				pauseTimeout = setTimeout(function() {
					// clear pause timeout
					clearTimeout(el.data('pause'));
					// start play interval after pause
					playInterval = setInterval(	function(){
						animate("next", option.vertical);
					},option.interval);
					// store play interval
					el.data('interval',playInterval);
				},option.pause);
				// store pause interval
				el.data('pause',pauseTimeout);
			} else {
				// if no pause, just stop
				stop();
			}
		}
		
		// pause on mouseover
		if (option.hoverPause && option.autoPlay) {
			control.bind('mouseover',function(){
				// on mouse over stop
				stop();
			});
			control.bind('mouseleave',function(){
				// on mouse leave start pause timeout
				pause();
			});
		}
		// next button
		$('.' + option.next ,el).click(function(e){
			e.preventDefault();
			if (option.autoPlay) {
				pause();
			}
			animate('next',option.vertical );
		});
		
		// previous button
		$('.' + option.prev, el).click(function(e){
			e.preventDefault();
			if (option.autoPlay) {
				 pause();
			}
			animate('prev', option.vertical);
		});
		if(option.start){ 
			current = option.start;
		}
		if(option.vertical){
			control.children(':eq('+option.start+')').css({display:'block',top: '33.33%'});
		}else{
			control.children(':eq('+option.start+')').css({display:'block',left: '33.33%'});
		}
		if (option.autoPlay) {
			// set interval
			playInterval = setInterval(function() {
				animate('next', option.vertical);
			}, option.interval);
			// store interval id
			el.data('interval',playInterval);
		}
	
		});
	};
	 $.fn.bstyle.option = {
				width: 'auto',
				vertical : false,// direction animate
				generateNextPrev: false, // boolean, Auto generate next/prev buttons
				next: 'next', // string, Class name for next button
				prev: 'prev', // string, Class name for previous button
				currentClass: 'current', // string, Class name for current class
				slideSpeed: 350, // number, Set the speed of the sliding animation in milliseconds
				hoverPause: false, // boolean, Set to true and hovering over slideshow will pause it
				autoPlay: false, // boolean, set to true and slide auto play when starting
				interval: 5000, //interval time to next , using miliseconds
				start : 1 , // number, Set the speed of the sliding animation in milliseconds
				pause: 0 ,// number, Pause slideshow on click of next/prev or pagination. A positive number will set to true and be the time of pause in milliseconds
			};
})(jQuery);