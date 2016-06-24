<?php
/**
 * @version        2.2.1
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012-2014 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
if (isset($config->number_columns))
{
	$numberColumns = $config->number_columns ;
}
else
{
	$numberColumns = 3 ;
}

$numberColumns = min($numberColumns, 4);
if (!isset($categoryId))
{
	$categoryId = 0;
}
$span = intval(12 / $numberColumns);
$btnClass = $bootstrapHelper->getClassMapping('btn');
$imgClass = $bootstrapHelper->getClassMapping('img-polaroid');
$spanClass = $bootstrapHelper->getClassMapping('span' . $span);
$i = 0;
$numberPlans = count($items);
$recommendedPlanId = (int) JFactory::getApplication()->getParams()->get('recommended_campaign_id');
$defaultItemId = $Itemid;
foreach ($items as $item)
{	
	$Itemid = OSMembershipHelperRoute::getPlanMenuId($item->id, $item->category_id, $defaultItemId);
	if ($item->thumb)
	{
		$imgSrc = JUri::base().'media/com_osmembership/'.$item->thumb ;
	}
	$url = JRoute::_('index.php?option=com_osmembership&view=plan&catid='.$item->category_id.'&id='.$item->id.'&Itemid='.$Itemid);
	if ($config->use_https)
	{
		$signUpUrl = JRoute::_(OSMembershipHelperRoute::getSignupRoute($item->id, $Itemid), false, 1);
	}
	else
	{
		$signUpUrl = JRoute::_(OSMembershipHelperRoute::getSignupRoute($item->id, $Itemid));
	}
	if (!$item->short_description)
	{
		$item->short_description = $item->description;
	}
	if ($item->id == $recommendedPlanId)
	{
		$recommended = true;
	}
	else
	{
		$recommended = false;
	}
	if ($i % $numberColumns == 0)
	{
	?>
		<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?> osm-pricing-table clearfix">
	<?php
	}
	?>
	<div class="<?php echo $spanClass; ?>">
		<div class="osm-plan<?php if ($recommended) echo ' osm-plan-recommended'; ?>">
			<?php
				if ($recommended)
				{
				?>
					<p class="plan-recommended"><?php echo JText::_('OSM_RECOMMENDED'); ?></p>
				<?php
				}
			?>
			<div class="osm-plan-header">
				<h2 class="osm-plan-title">
					<?php echo $item->title; ?>
				</h2>
			</div>
			<div class="osm-plan-price">
				<h2>
					<p class="price">
						<span>
						<?php
							if ($item->price > 0)
							{
								$symbol = $item->currency_symbol ? $item->currency_symbol : $item->currency;
								echo OSMembershipHelper::formatCurrency($item->price, $config, $symbol);
							}
							else
							{
								echo JText::_('OSM_FREE');
							}
							?>
						</span>
					</p>
				</h2>
			</div>
			<div class="osm-plan-short-description">
				<?php echo $item->short_description;?>
			</div>
			<?php
			if (OSMembershipHelper::canSubscribe($item))
			{
			?>
				 <ul class="osm-signup-container">
					<li>
						<a href="<?php echo $signUpUrl; ?>" class="<?php echo $btnClass; ?> btn-primary btn-singup">
							<?php echo JText::_('OSM_SIGNUP'); ?>
						</a>
					</li>
				</ul>
			<?php
			}
			?>
		</div>
	</div>
<?php	
	if (($i + 1) % $numberColumns == 0)
	{
	?>
		</div>
	<?php
	}
	$i++;
}

if ($i % $numberColumns != 0)
{
	echo "</div>" ;
}
?>