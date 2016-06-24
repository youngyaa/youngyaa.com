<?php
defined('_JEXEC') or die('Restricted access');

$cfg = JEVConfig::getInstance();

$this->data = $data = $this->datamodel->getDayData($this->year, $this->month, $this->day);
$this->Redirectdetail();

$cfg = JEVConfig::getInstance();
$Itemid = JEVHelper::getItemid();
$hasevents = false;

	echo '<fieldset><legend class="ev_fieldset">' . JText::_('JEV_EVENTSFORTHE') . ': '.JEventsHTML::getDateFormat($this->year, $this->month, $this->day, 0).'</legend><br />' . "\n";
	echo '<div class="ja-events-list row equal-height equal-height-child">' . "\n";
	
	// Timeless Events First
	if (count($data['hours']['timeless']['events']) > 0)
	{
		$start_time = JText::_('TIMELESS');
		$hasevents = true;

		foreach ($data['hours']['timeless']['events'] as $row)
		{ 
      $class =  $datenow->toFormat('%Y-%m-%d')>$row->startDate() ? "pastevent":"";
      $link = $row->viewDetailLink($row->yup(), $row->mup(), $row->dup(), $Itemid);
      $listyle = 'style="background-color:'.$row->bgcolor().';"';
			echo "<div class='col-sm-6 col-md-6 col-lg-4 col ".$class."'><div class='inner'>\n";

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

	for ($h = 0; $h < 24; $h++)
	{
		if (count($data['hours'][$h]['events']) > 0)
		{
			$hasevents = true;
			$start_time = JEVHelper::getTime($data['hours'][$h]['hour_start']);
			$hasevents = true;

			foreach ($data['hours'][$h]['events'] as $row)
			{
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
			<?php echo "</div></div>\n";
			}
		}
	}
	if (!$hasevents)
	{
		echo "<div class='col-sm-12 col'><p>\n";
		echo JText::_('JEV_NO_EVENTS');
		echo "</p></div>\n";
	}
	echo '</div>' . "\n";
	echo '</fieldset>' . "\n";