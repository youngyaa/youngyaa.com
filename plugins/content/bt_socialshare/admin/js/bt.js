jQuery.noConflict();
window.addEvent("domready",function(){
	$$("#jform_params_asset-lbl").getParent().destroy();
	
	jQuery('li > .btn-group').each(function(){
		if(jQuery(this).find('input').length != 2 ) return;
		
		el = jQuery(this).find('input:checked').val();
		if( el != '0' && el != '1' && el != 'false' && el != 'true' && el != 'no' && el != 'yes' ){
			return;
		}
		
		jQuery(this).hide();
		var group = this;
		

		var el = jQuery(group).find('input:checked');	
		var switchClass ='';

		if(el.val()=='' || el.val()=='0' || el.val()=='no' || el.val()=='false'){
			switchClass = 'no';
		}else{
			switchClass = 'yes';
		}
		var switcher = new Element('div',{'class' : 'switcher-'+switchClass});
		switcher.inject(group, 'after');
		switcher.addEvent("click", function(){
			var el = jQuery(group).find('input:checked');	
			if(el.val()=='' || el.val()=='0' || el.val()=='no' || el.val()=='false'){
				switcher.setProperty('class','switcher-yes');
			}else {
				switcher.setProperty('class','switcher-no');
			}
			jQuery(group).find('input:not(:checked)').attr('checked',true);
		});
	})

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
		jQuery('#jform_params_google_plus_asynchronous').parent().show();
	}else{
		jQuery('#jform_params_google_plus_asynchronous').parent().hide();
	}
	jQuery('input[name="jform[params][google_plus_parse_tags]"]').click(function(){
		if(jQuery('input[name="jform[params][google_plus_parse_tags]"]:checked').val()=='onload'){
			jQuery('#jform_params_google_plus_asynchronous').parent().show();
		}else{
			jQuery('#jform_params_google_plus_asynchronous').parent().hide();
		}
	});
	
	jQuery('#jform_params_google_plus_annotation').change();
	jQuery('#jform_params_google_plus_annotation').change(function(){
		if(jQuery(this).val()=='inline'){
			jQuery('#jform_params_google_plus_width').parent().show();
		}else{
			jQuery('#jform_params_google_plus_width').parent().hide();
		}
		if(jQuery(this).val()=='vertical-bubble'){
			jQuery('#jform_params_google_plus_type').parent().hide();
		}else{
			jQuery('#jform_params_google_plus_type').parent().show();
		}
	});
	jQuery('#jform_params_google_plus_annotation').change();
})

