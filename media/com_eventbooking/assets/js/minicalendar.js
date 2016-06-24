		Eb.jQuery(document).ready(function($){
			//pre month
		   	$('#prev_month').bind('click', function() {
		   		var itemId = $('[name^=itemId]').val();
			   	var month = $('.month_ajax').val();
			   	var year  = $('.year_ajax').val();
		   		if(month == 1){
		   			 month = 13;
		   			 year --;
		   		}
			   	month --;
				$.ajax({
					url : 'index.php?option=com_eventbooking&view=calendar&layout=mini&format=raw&month='+ month +'&year='+ year + '&Itemid=' + itemId + langLinkForAjax,
					dataType: 'html',
					success: function(html) {
						$('#calendar_result').html(html);
						$('.month_ajax').val(month);
						$('.year_ajax').val(year);
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				})
			});

			//next month
		   	$('#next_month').bind('click', function() {
		   		var itemId = $('[name^=itemId]').val();
		   		var month = $('.month_ajax').val();
		   		var year  = $('.year_ajax').val();
		   		if(month == 12){
		   			 month = 0;
		   			 year ++;
		   		}
		   		month ++;
				$.ajax({
					url : 'index.php?option=com_eventbooking&view=calendar&layout=mini&format=raw&month='+ month +'&year='+ year + '&Itemid=' + itemId + langLinkForAjax,
					dataType: 'html',
					success: function(html) {
						$('#calendar_result').html(html);
						$('.month_ajax').val(month);
						$('.year_ajax').val(year);
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				})
			});

			//next year
		   	$('#next_year').bind('click', function() {
		   		var itemId = $('[name^=itemId]').val();
		   		var month = $('.month_ajax').val();
		   		var year  = $('.year_ajax').val();
		   		year ++;
				$.ajax({
					url : 'index.php?option=com_eventbooking&view=calendar&layout=mini&format=raw&month='+ month +'&year='+ year + '&Itemid=' + itemId + langLinkForAjax,
					dataType: 'html',
					success: function(html) {
						$('#calendar_result').html(html);
						$('.month_ajax').val(month);
						$('.year_ajax').val(year);
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				})
			});

			//pre month
		   	$('#prev_year').bind('click', function() {
		   		var itemId = $('[name^=itemId]').val();
			   	var month = $('.month_ajax').val();
			   	var year  = $('.year_ajax').val();
			   	year --;
				$.ajax({
					url : 'index.php?option=com_eventbooking&view=calendar&layout=mini&format=raw&month='+ month +'&year='+ year + '&Itemid=' + itemId,
					dataType: 'html',
					success: function(html) {
						
						$('#calendar_result').html(html);
						$('.month_ajax').val(month);
						$('.year_ajax').val(year);
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				})
			});
			
		});
