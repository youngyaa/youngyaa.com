<?php 
/**
 * ------------------------------------------------------------------------
 * JA Events II template
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2011 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - Copyrighted Commercial Software
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites:  http://www.joomlart.com -  http://www.joomlancers.com
 * This file may not be redistributed in whole or significant part.
 * ------------------------------------------------------------------------
 */
?>

<?php 
	$fullWidth 					= $helper->get('full-width');
	$contacInfoMap 			= $helper->get('contact-info-googlemap');
	$contacInfoImage 		= $helper->get('contact-info-image');
	$contacInfoPosition = $helper->get('contact-info-position');
?>

  <div id="uber-contact-<?php echo $module->id; ?>" class="uber-contact-info style-1 <?php if(!($contacInfoImage || $contacInfoMap)): ?> no-background <?php endif; ?> <?php if($fullWidth): ?>full-width <?php endif; ?>">
    <?php if($contacInfoImage || $contacInfoMap): ?>
    <div class="info-bg">
    	<?php echo $contacInfoMap; ?>
    	<?php if($contacInfoImage): ?><img src="<?php echo $contacInfoImage; ?>" alt="" /><?php endif; ?>
    </div>
    <?php endif; ?>
  	<div class="info <?php echo $helper->get('contact-info-position'); ?>">
  		<dl class="info-list">
  		  <?php $count= $helper->getRows('contact-info-item.contact-info-name'); ?>
  		  
  		  <?php for ($i=0; $i<$count; $i++) : ?>
  		  
  			<dt>
  				<?php if($helper->get ('contact-info-item.contact-info-icon', $i)): ?><i class="fa <?php echo $helper->get ('contact-info-item.contact-info-icon', $i); ?>"></i><?php endif; ?>
  				<?php echo $helper->get ('contact-info-item.contact-info-name', $i) ?>
  			</dt>
  		
  	  	<?php if ($helper->get ('contact-info-item.contact-info-value', $i)) : ?>
  	    <dd><?php echo $helper->get ('contact-info-item.contact-info-value', $i) ?></dd>
  	  	<?php endif; ?>
  	  	
  			<?php endfor; ?>
  			
  		</dl>
  	</div>
  </div>
<script>
(function ($) {
	$(document).ready(function(){
	
		if($('#uber-contact-<?php echo $module->id ?>').length > 0) {
			var heightContact = $('#uber-contact-<?php echo $module->id ?> .info-bg').outerHeight(),
					infoContact   = $('#uber-contact-<?php echo $module->id ?> .info').outerHeight();
					
			if(infoContact > (heightContact - 48)) {
				$('#uber-contact-<?php echo $module->id ?> .info.top-left').css({ 
					'top': '20px',
					'left': '20px'
				});
				
				$('#uber-contact-<?php echo $module->id ?> .info.bottom-left').css({ 
					'bottom': '20px',
					'left': '20px'
				});
				
				$('#uber-contact-<?php echo $module->id ?> .info.top-right').css({ 
					'top': '20px',
					'right': '20px'
				});
				
				$('#uber-contact-<?php echo $module->id ?> .info.bottom-right').css({ 
					'bottom': '20px',
					'right': '20px'
				});
			} else {
				$('#uber-contact-<?php echo $module->id ?> .info.top-left').css({ 
					'top': '48px',
					'left': '220px'
				});
				
				$('#uber-contact-<?php echo $module->id ?> .info.bottom-left').css({ 
					'bottom': '48px',
					'left': '220px'
				});
				
				$('#uber-contact-<?php echo $module->id ?> .info.top-right').css({ 
					'top': '48px',
					'right': '220px'
				});
				
				$('#uber-contact-<?php echo $module->id ?> .info.bottom-right').css({ 
					'bottom': '48px',
					'right': '220px'
				});
			}
			
			$(window).resize(function(){
				var heightContact = $('#uber-contact-<?php echo $module->id ?> .info-bg').outerHeight(),
						infoContact   = $('#uber-contact-<?php echo $module->id ?> .info').outerHeight();
						
				if(infoContact > (heightContact - 48)) {
          $('#uber-contact-<?php echo $module->id ?> .info.top-left').css({ 
            'top': '20px',
            'left': '20px'
          });
          
          $('#uber-contact-<?php echo $module->id ?> .info.bottom-left').css({ 
            'bottom': '20px',
            'left': '20px'
          });
          
          $('#uber-contact-<?php echo $module->id ?> .info.top-right').css({ 
            'top': '20px',
            'right': '20px'
          });
          
          $('#uber-contact-<?php echo $module->id ?> .info.bottom-right').css({ 
            'bottom': '20px',
            'right': '20px'
          });
        } else {
          $('#uber-contact-<?php echo $module->id ?> .info.top-left').css({ 
            'top': '48px',
            'left': '220px'
          });
          
          $('#uber-contact-<?php echo $module->id ?> .info.bottom-left').css({ 
            'bottom': '48px',
            'left': '220px'
          });
          
          $('#uber-contact-<?php echo $module->id ?> .info.top-right').css({ 
            'top': '48px',
            'right': '220px'
          });
          
          $('#uber-contact-<?php echo $module->id ?> .info.bottom-right').css({ 
            'bottom': '48px',
            'right': '220px'
          });
        }
			});
		}
			
	});
})(jQuery);
</script>