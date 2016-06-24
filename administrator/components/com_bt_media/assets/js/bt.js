jQuery.noConflict();
window.addEvent("domready",function(){
    $$("#jform_params_asset-lbl").getParent().destroy();
    $$("#jform_params_ajax-lbl").getParent().destroy();
    
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
			
            var switcher = new Element('div',{
                'class' : 'switcher-'+switchClass
            });

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

    if(jQuery('#jform_params_responsive').val() == 0){
        jQuery(".responsive").parent().hide();
        jQuery(".none-responsive").parent().show();
    }else{
        jQuery(".responsive").parent().show();
        jQuery(".none-responsive").parent().hide();
    }
    
    jQuery("#jform_params_responsive").next().click(function(){
        if(!jQuery(this).hasClass('switcher-yes')){
            jQuery(".responsive").parent().hide();
            jQuery(".none-responsive").parent().show();
        }else{
            jQuery(".responsive").parent().show();
            jQuery(".none-responsive").parent().hide();
        }
    });
    jQuery(".pane-sliders select").each(function(){
	
        if(jQuery(this).is(":visible")) {
            jQuery(this).chosen()
        };
    })	
    jQuery(".chzn-container").click(function(){
        jQuery(".panel .pane-slider,.panel .panelform").css("overflow","visible");	
    })
    jQuery(".panel .title").click(function(){
        jQuery(".panel .pane-slider,.panel .panelform").css("overflow","hidden");		
    })	
})

