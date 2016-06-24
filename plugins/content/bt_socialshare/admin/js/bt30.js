jQuery.noConflict();
window.addEvent("domready",function(){
	$$("#jform_params_asset-lbl").getParent().destroy();
	
	$$('.bt_switch').each(function(el)
	{
			
			var options = el.getElements('option');		
			if(options.length==2){
			
				el.setStyle('display','none');
				var value = new Array();
				value[0] = options[0].value;
				value[1] = options[1].value;
				
				var text = new Array();
				text[0] = options[0].text.replace(" ","-").toLowerCase().trim();
				text[1] = options[1].text.replace(" ","-").toLowerCase().trim();
				
				var switchClass = (el.value == value[0]) ? text[0] : text[1];
			
				var switcher = new Element('div',{'class' : 'switcher-'+switchClass});

				switcher.inject(el, 'after');
				switcher.addEvent("click", function(){
					if(el.value == value[1]){
						switcher.setProperty('class','switcher-'+text[0]);
						el.value = value[0];
					} else {
						switcher.setProperty('class','switcher-'+text[1]);
						el.value = value[1];
					}
				});
		}
	});

	jQuery('.bt_color').ColorPicker({
		color: '#0000ff',
		onShow: function (colpkr) {
			jQuery(colpkr).fadeIn(500);
			return false;
		},
		onHide: function (colpkr) {
			jQuery(colpkr).fadeOut(500);
			return false;
		},
		onSubmit: function(hsb, hex, rgb, el) {
			jQuery(el).val("#"+hex);
			//jQuery(el).css('background',jQuery(el).val())
			jQuery(el).ColorPickerHide();
		},
		onBeforeShow: function () {
			jQuery(this).ColorPickerSetColor(this.value);
		}
	})
	.bind('keyup', function(){
		jQuery(this).ColorPickerSetColor(this.value);
	});
	jQuery(".pane-sliders select").each(function(){
	
		if(jQuery(this).is(":visible")) {
		jQuery(this).css("width",parseInt(jQuery(this).width())+50);
		jQuery(this).chosen()
		};
	})	
	jQuery(".chzn-container").click(function(){
		jQuery(".panel .pane-slider,.panel .panelform").css("overflow","visible");	
	})
	jQuery(".panel .title").click(function(){
		jQuery(".panel .pane-slider,.panel .panelform").css("overflow","hidden");		
	})	
	
	// show/hide asynchronous option when change option parse tags
	if(jQuery('input[name="jform[params][google_plus_parse_tags]"]:checked').val()=='onload'){
		jQuery('#jform_params_google_plus_asynchronous').parent().parent().show();
	}else{
		jQuery('#jform_params_google_plus_asynchronous').parent().parent().hide();
	}
	jQuery('input[name="jform[params][google_plus_parse_tags]"]').click(function(){
		if(jQuery('input[name="jform[params][google_plus_parse_tags]"]:checked').val()=='onload'){
			jQuery('#jform_params_google_plus_asynchronous').parent().parent().show();
		}else{
			jQuery('#jform_params_google_plus_asynchronous').parent().parent().hide();
		}
	});
	
	// show/hide width option when change anotation value
	jQuery('#jform_params_google_plus_annotation').change();
	jQuery('#jform_params_google_plus_annotation').change(function(){
		if(jQuery(this).val()=='inline'){
			jQuery('#jform_params_google_plus_width').parent().parent().show();
		}else{
			jQuery('#jform_params_google_plus_width').parent().parent().hide();
		}
		if(jQuery(this).val()=='vertical-bubble'){
			jQuery('#jform_params_google_plus_type').parent().parent().hide();
		}else{
			jQuery('#jform_params_google_plus_type').parent().parent().show();
		}
	});
	jQuery('#jform_params_google_plus_annotation').change();
})

