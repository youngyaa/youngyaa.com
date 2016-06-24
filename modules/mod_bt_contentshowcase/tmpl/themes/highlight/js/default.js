/*
** Author: hungtx@vsmarttech.com
** Website: bowthemes.com 
** Version: 1.0
** License: http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
** Base on slidesjs.com */

(function($){
	$.fn.btHighlightOption = {
		start:1,
		autoPlay:true,
		hoverPause: true,
		mouseEvent:'hover',
		easing: 'easeInBack',
		slideSpeed: 500,
		interval:3000,
		effect:'fade' // slide or fade
	};
	$.fn.btHighlight = function( option ) {
		option = $.extend( {}, $.fn.btHighlightOption, option);
		return this.each(function(){
			var wrapper = $(this);
			var Highlight = $('.bt-window',wrapper);
			var navigation = wrapper.find('.bt-nav');
			var navPipe =  $('.bt-navpipe',wrapper);
			var img = new Image();
			var start = option.start-1, next = 0,preview = 0, current = 0,total = Highlight.children().size(),direction,move,playing = false,intervalId,navTimeoutId;
			$(img).load(function(){
				startSlider();
			}).error(function () {
				alert('invalid image');
			}).attr('src',Highlight.find('img:first').attr('src'));

			function startSlider(){
				// set height
				current = start;
				Highlight.height(Highlight.find('.bt-slide:first').outerHeight());
				if(option.effect == 'fade'){
					Highlight.children().css({
						display:'none'
					})
				}
				Highlight.children(':eq(' + start + ')').fadeIn(option.slideSpeed, option.easing, function(){
				$(this).css({
					zIndex: 5
					});
				});
		
				
				$(navigation.get(current)).addClass('active');
				if(option.mouseEvent=='click'){
					navigation.click(function(){
						if (option.autoPlay) { pause();}
						animate($(this).index());
					})
				}else{
					navigation.hover(function(){
						if (option.autoPlay) { pause();}
						animate($(this).index());
					});
				}
				$(window).resize(function(){
					Highlight.height(Highlight.find('.bt-slide:first').outerHeight());
				});
				if (option.hoverPause && option.autoPlay) {
					wrapper.bind('mouseover',function(){
						stop();
					});
					wrapper.bind('mouseleave',function(){
						pause();
					});
				}
				if (option.autoPlay){
					intervalId = setInterval(function(){
					animate('next');
					}, option.interval);
					Highlight.data('intervalId',intervalId);
				}
						

			}
			function animate(direction){
				if(playing || direction == current){
					return false;	
				}
				playing = true;
				position = '0%';								
				move = '0%';
				
				switch(direction){
				case 'next':
					prev = current;
					next = current + 1;
					next = total === next ? 0 : next;
					position = '66.66%';
					move = '-200%';
					current = next;
					break;
				case 'prev':
					prev = current;
					next = current - 1;
					next = next === -1 ? total-1 : next;									
					current = next;
					break;
				default:
					next = direction
					prev = current;
					current = next;
					if (next > prev){
						position = '66.66%';
						move = '-200%';
					}
					break;
				}
				
				if(current<0) current= 0; 

					if(option.effect == 'scroll'){
						Highlight.children(':eq('+ next +')').css({
								left: position,
								display: 'block'
						});
						Highlight.animate({
							left: move,
							height: Highlight.children(':eq('+ next +')').outerHeight()
						},option.slideSpeed, option.easing, function(){
							// after animation reset control position
							Highlight.css({
								left: '-100%'
							});
							// reset and show next
							Highlight.children(':eq('+ next +')').css({
								left: '33.33%',
								zIndex: 5
							});
							// reset previous slide
							Highlight.children(':eq('+ prev +')').css({
								left: '33.33%',
								display: 'none',
								zIndex: 0
							});
							
							playing=false;
						});
					}
					else
					{
						Highlight.children(':eq('+ next +')').css({
							zIndex: 5
						}).fadeIn(option.slideSpeed, option.easing, function(){
							Highlight.css({
							height: Highlight.children(':eq('+ next +')').outerHeight()
						})
						Highlight.children(':eq('+ prev +')').css({
								display: 'none',
								zIndex: 0
							});								
						Highlight.children(':eq('+ next +')').css({
							zIndex: 0
						});									
						playing=false;
						});
					}
				changeNavigation();
			
			}
			function changeNavigation(){
				navigation.removeClass('active');
				$(navigation.get(current)).addClass('active');				
			}
			function stop() {
				clearInterval(Highlight.data('intervalId'));
			}
			function pause() {
				if (option.hoverPause) {
					clearInterval(Highlight.data('intervalId'));
					intervalId = setInterval(function(){
						animate("next");
					},option.interval);
					Highlight.data('intervalId',intervalId);
				}else {
					stop();
				}
			}
			
		});
	};	
})(jQuery);

$B=jQuery.noConflict();
