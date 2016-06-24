<?php die("Access Denied"); ?>#x#a:2:{s:6:"output";a:3:{s:4:"body";s:0:"";s:4:"head";a:2:{s:11:"styleSheets";a:2:{s:60:"https://www.youngyaa.com//templates/ja_events_ii/css/acm.css";a:3:{s:4:"mime";s:8:"text/css";s:5:"media";N;s:7:"attribs";a:0:{}}s:53:"/templates/ja_events_ii/local/acm/video/css/style.css";a:3:{s:4:"mime";s:8:"text/css";s:5:"media";N;s:7:"attribs";a:0:{}}}s:7:"scripts";a:1:{s:46:"/templates/ja_events_ii/acm/video/js/script.js";a:3:{s:4:"mime";s:15:"text/javascript";s:5:"defer";b:0;s:5:"async";b:0;}}}s:13:"mime_encoding";s:9:"text/html";}s:6:"result";s:2548:"<div class="wrap t3-section  bg-transparent  " id="Mod95"  ><div class="section-inner">
<div id="acm-video-95" class="acm-video style-1 style-dark  show-intro">
  <div class="col-sm-7 video-content">
        <div class="video-heading">
      The football match orgnized by us    </div>
        
        <div class="video-intro">
      For 3 days in November, 2014, a community of smart, talented people come together to share ideas, tel stories, and drink a few beers.    </div>
    
          <a class="btn btn-light-trans" href="https://www.youtube.com/results?search_query=%E6%BE%B3%E5%9B%A2%E6%9D%AF" >more <i class="fa fa-angle-right"></i></a>
      </div>
	<div class="col-sm-5 video-player"><div class="video-wrapper">
					<div id="videoplayer">
								<img alt=" " src="https://img.youtube.com/vi/ZVTFaDO5lQ8/maxresdefault.jpg" />
								</div>
			<a  onclick="javideoPlay();"  title="Play" class="btn btn-border btn-border-inverse btn-rounded btn-play"><span class="sr-only">Watch the video</span><i class="fa fa-play"></i></a>
		  </div></div>
</div>
<script>
(function($){
	
  $(document).ready(function(){
    var videoHeight = $('#acm-video-95 .video-player').height();
    $('#acm-video-95 .video-content').height(videoHeight);
	$('#acm-video-95 #videoplayer').height(videoHeight);
    $(window).resize(function(){
      var videoHeight = $('#acm-video-95 .video-player').height();
      $('#acm-video-95 .video-content').height(videoHeight);
      $('#acm-video-95 #videoplayer').height(videoHeight);
    });
    // for vimeo js api player. need jquery so we set inside.
      });

})(jQuery);
		  var tag = document.createElement('script');

		  tag.src = "https://www.youtube.com/iframe_api";
		  var firstScriptTag = document.getElementsByTagName('script')[0];
		  firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
		  var player;
		  function onYouTubeIframeAPIReady() {
		  	
		  }
		  function onPlayerReady(event) {
		  	jQuery('iframe#videoplayer').css('width', '100%').css('height', '100%');
			event.target.playVideo();
		  }

		  var done = false;
		  function onPlayerStateChange(event) {}
		  function stopVideo() {}

		  function javideoPlay() {
				jQuery('div.video-wrapper').addClass('playing');
			 	player = new YT.Player('videoplayer', {
			  height: '390',
			  width: '640',
			  videoId: 'ZVTFaDO5lQ8',
			  events: {
				'onReady': onPlayerReady,
				'onStateChange': onPlayerStateChange
			  }
			});
		  }
		</script></div></div>";}