<?php 
defined('_JEXEC') or die('Restricted access');

$cfg	 = JEVConfig::getInstance();
$Itemid = JEVHelper::getItemid();

// Note that using a $limit value of -1 the limit is ignored in the query
$this->data = $data = $this->datamodel->getYearData($this->year,$this->limit, $this->limitstart);

echo "<div id='cal_title'>". JText::_('JEV_EVENTSFOR') .": ".$data["year"]."</div>\n";
//echo '<fieldset id="ev_fieldset"><legend class="ev_fieldset">' . JText::_('JEV_ARCHIVE') . '</legend><br />' . "\n";
?>
<div class="ja-events-list row equal-height equal-height-child">
<?php
if ($data["total"] <= 0 && $cfg->get('year_show_noev_found', 0)) {
        
        echo '<div class="col-sm-12 no_events_found"><p>'.JText::_('JEV_NO_EVENTS_FOUND').'</p></div>';
    
} else {
        for($month = 1; $month <= 12; $month++) {
                $num_events = count($data["months"][$month]["rows"]);
                if ($num_events>0){
                        for ($r = 0; $r < $num_events; $r++) {
                                if (!isset($data["months"][$month]["rows"][$r])) continue;
                                $row =& $data["months"][$month]["rows"][$r];
                                $link = $row->viewDetailLink($row->yup(), $row->mup(), $row->dup(), $Itemid);
                                $listyle = 'style="background-color:'.$row->bgcolor().';"';
                                echo "<div class='col-sm-6 col-md-6 col-lg-4 col'><div class='inner'>\n";
                                if ($row->get('imageimg1')) {
                                ?>
                                <div class="item-image">
                                    <div class="img-intro-left">
                                        <a href="<?php echo $link; ?>" title="<?php echo $row->title(); ?>" class="item-link">                                     
                                            <?php 
                                            if ($row->customfields["event_span"]["value"] >= 2) {
                                                echo $row->get("imageimg1");
                                            } else {
                                                echo $row->get("thumbimg1"); 
                                            }?>
                                        </a>
                                    </div>
                                </div>
                                <?php } ?>

                                  <p <?php echo $listyle; ?> class="category" <?php $category_url = JRoute::_("index.php?option=com_jevents&view=range&layout=listevents&Itemid=".$Itemid."&catids=".$row->catid.""); ?>                 
                                      <a href="<?php echo $category_url; ?> "> <?php echo $row->catname(); ?></a>
                                  </p>

                                  <div class="item-main clearfix">
                                      <!-- Item header -->
                                      <div class="header item-header clearfix">

                                          <?php
                                              echo '<h2><a href="' . $row->viewDetailLink($row->yup(), $row->mup(), $row->dup(), $Itemid) . '">' . $row->title() . ' </a></h2>';
                                              ?>
                                              <dl class="article-info">
                                                  <dd>
                                                      <span class="icon-calendar"></span>
                                                      <?php 
                                                      echo $row->startDate();
                                                      ?>
                                                  </dd>
                                                  <dd>
                                                      <span class="icon-time"></span>
                                                      <?php 
                                                      echo JEVHelper::getTime($row->getUnixStartTime(), $row->hup(), $row->minup()) . ' - ';
                                                      echo JEVHelper::getTime($row->getUnixEndTime(), $row->hdn(), $row->mindn());
                                                      ?>
                                                  </dd>
                                                  <dd style="float:none;">
                                                      <span class="icon-globe"></span> <?php echo $row->location(); ?>
                                                  </dd>

                                              </dl>

                                              <?php
                                          
                                          ?>
                                      </div>

                                  </div>
                            <?php  
                                echo "</div></div>\n";
                        }
                }

        }
}
echo '</div>' . "\n";

$this->paginationForm($data["total"], $data["limitstart"], $data["limit"]);