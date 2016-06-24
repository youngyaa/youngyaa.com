<?php
/**
 * @version        2.2.1
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2016 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
// no direct access
defined('_JEXEC') or die;
?>
<div id="osm-categories-list" class="osm-container">
	<?php
		if ($this->category)
		{
			$pageHeading = $this->params->get('page_heading') ? $this->params->get('page_heading') : $this->category->title;
		}
		else
		{
			$pageHeading = $this->params->get('page_heading') ? $this->params->get('page_heading') : JText::_('OSM_CATEGORIES');
		}
		?>

		<h1 class="osm-page-title"><?php echo $pageHeading;?></h1>

		<?php
		if(!empty($this->category->description))
		{
		?>
			<div class="osm-description clearfix"><?php echo $this->category->description;?></div>
		<?php
		}

		echo OSMembershipHelperHtml::loadCommonLayout('common/categories.php', array('items' => $this->items, 'categoryId' => $this->categoryId, 'config' => $this->config, 'Itemid' => $this->Itemid));

		if ($this->pagination->total > $this->pagination->limit)
		{
		?>
			<div class="pagination">
				<?php echo $this->pagination->getPagesLinks(); ?>
			</div>
		<?php
		}
	?>
</div>
