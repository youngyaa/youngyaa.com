<?php

/**
 * copyright (C) 2011 GWE Systems Ltd - All rights reserved
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC' ) or die();
$document = JFactory::getDocument();

$script = '
    function showHide(elementid) {
var el = document.getElementById(elementid);
	el.style.display = (el.style.display != \'none\' ? \'none\' : \'\' );
};';
$document-> addScriptDeclaration ($script);

$featuredCount = $params->get('featuredcount',3);
$count = $params->get('count',3);
//$catid = $params->get('catid',0);
$Itemid = $params->get('target_itemid',0);
//$catid = (is_array($catid)) ? $catid[0] : 0;
$mediabase = JURI::root().$params->get('image_path', 'images/stories');
$folder = "jevents/jevlocations";

$modparams = new JRegistry($module->params);
?>
<?php if(count($list)) : ?>

<?php if($module->showtitle || $modparams->get('module-intro') || $count > 3): ?>
<div class="section-header text-left">
  <?php if($module->showtitle): ?>
  <h3 class="section-title ">  <span><?php echo $module->title ?></span> </h3>
  <?php endif; ?>

  <?php if($modparams->get('module-intro')): ?>
  <div class="module-intro">
    <?php echo $modparams->get('module-intro') ?>
  </div>
  <?php endif; ?>
</div>
<?php endif; ?>

<div id="jevlocations-<?php echo $module->id; ?>" class="jevlocations <?php echo $moduleclass_sfx; ?>">
	<?php foreach ($list as $index => $item) :?>
		<div class="mod-jevloc-item">
                <div class="mod-jevloc-image">
                  <?php
      						$nonSEFLink =  'index.php?option=com_jevlocations&task=locations.detail&loc_id='. $item->loc_id ."&se=1"."&title=".JApplication::stringURLSafe($item->title);
      						$Itemid = $params->get('target_itemid',0);

      						if($Itemid!=0)
      						{
      							$nonSEFLink .="&Itemid=".$Itemid;
      						}

      						$link 	= JRoute::_($nonSEFLink);
      						$mediabase = JUri::root().$params->get('image_path', 'images');
      						$folder = "jevents/jevlocations";
      						$thimg = '<img class="jevloc-bloglayout-image" width="280" alt=" " src="'.$mediabase.'/'.$folder.'/thumbnails/thumb_'.$item->image.'" />' ;
                  ?>
					       <a href="<?php echo $link; ?>" title="<?php echo $item->title; ?>"><?php echo $thimg; ?></a>
                </div>

                <div class="mod-jevloc-info">
                  <div class="mod-jevloc-title">
                    <h4 class="mod-jevloc-loc-title"><a href="<?php echo $link ?>" ><?php echo $item->title; ?></a></h4>
                  </div>
                  <?php if($item->city!='') : ?>
                  <div class="mod-jevloc-city">
                          <strong><?php echo JText::_('MOD_JEVLOCATIONS_CITY');?>:</strong> <?php echo $item->city; ?>

                  </div>
                  <?php endif ?>
                  <?php if($item->state!='') : ?>
                  <div class="mod-jevloc-state">
                          <strong><?php echo JText::_('MOD_JEVLOCATIONS_STATE');?>:</strong> <?php echo $item->state; ?>

                  </div>
                  <?php endif ?>
  				        <?php if($item->country!='') : ?>
                  <div class="mod-jevloc-country">
                          <strong><?php echo JText::_('MOD_JEVLOCATIONS_COUNTRY');?>:</strong> <?php echo $item->country; ?>

                  </div>
                  <?php endif ?>
     
                </div>
        </div>
	<?php endforeach ?>
</div>
<?php endif ?>

<script>
(function ($) {
  $(document).ready(function(){ 
    $("#jevlocations-<?php echo $module->id; ?>.jevlocations").owlCarousel({
      navigation : true,
      pagination: false,
      items: 3,
      loop: false,
      scrollPerPage : true,
      autoHeight: false,
      navigationText : ["<i class='fa fa-angle-left'></i>", "<i class='fa fa-angle-right'></i>"],
      itemsDesktop : [1199, 3],
      itemsDesktopSmall : [979, 2],
      itemsTablet : [768, 2],
      itemsTabletSmall : [600, 2],
      itemsMobile : [479, 1]
    });
  });
})(jQuery);
</script>