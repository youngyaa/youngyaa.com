<?php 
defined('_JEXEC') or die('Restricted access');

$data = $this->data;

$Itemid = JEVHelper::getItemid();

?>
<div class="ja-events-list row equal-height equal-height-child">
    <?php
    $num_events = count($data['rows']);
    $chdate ="";
    if( $num_events > 0 ){

        for( $r = 0; $r < $num_events; $r++ ){
            $row = $data['rows'][$r];

            $event_day_month_year   = $row->dup() . $row->mup() . $row->yup();
            // Ensure we reflect multiday setting
            if (!$row->eventOnDate(JevDate::mktime(0,0,0,$row->mup(),$row->dup(),$row->yup()))) continue;

            if(( $event_day_month_year <> $chdate ) && $chdate <> '' ){
            }

            $datenow = JEVHelper::getNow();
            $class =  $datenow->toFormat('%Y-%m-%d')>$row->startDate() ? "pastevent":"";
            if( $event_day_month_year <> $chdate ){
                $date =JEventsHTML::getDateFormat( $row->yup(), $row->mup(), $row->dup(), 1 );
            }

            if( $event_day_month_year <> $chdate ){

            }

            $link = $row->viewDetailLink($row->yup(), $row->mup(), $row->dup(), $Itemid);

            $listyle = 'style="background-color:'.$row->bgcolor().';"';
            echo "<div class='col-sm-6 col-md-6 col-lg-4 col ".$class."'><div class='inner'>";
     
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
                <?php 
            }

            ?>

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

            $chdate = $event_day_month_year;
        }
    } else {
        echo '<div class="col-sm-12"><p>' . "\n";

            echo JText::_('JEV_NO_EVENTS') . '</p></div>';
    }

    ?>
</div>
</fieldset>

<?php
    // Create the pagination object
    if ($data["total"]>$data["limit"]){
    	$this->paginationForm($data["total"], $data["limitstart"], $data["limit"]);
    }
?>