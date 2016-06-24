<?php
/**
 * @version        2.2.1
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2016 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die;

class plgOSMembershipArticles extends JPlugin
{

	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);

		JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_osmembership/table');
	}

	/**
	 * Render settings from
	 *
	 * @param PlanOSMembership $row
	 */
	function onEditSubscriptionPlan($row)
	{
		ob_start();
		$this->_drawSettingForm($row);
		$form = ob_get_contents();
		ob_end_clean();

		return array('title' => JText::_('PLG_OSMEMBERSHIP_ARTICLES_RESTRICTION_SETTINGS'),
		             'form'  => $form
		);
	}

	/**
	 * Store setting into database
	 *
	 * @param PlanOsMembership $row
	 * @param Boolean          $isNew true if create new plan, false if edit
	 */
	function onAfterSaveSubscriptionPlan($context, $row, $data, $isNew)
	{
		$db         = JFactory::getDbo();
		$query      = $db->getQuery(true);
		$planId     = $row->id;
		$articleIds = $data['article_id'];
		if (!$isNew)
		{
			$query->delete('#__osmembership_articles')->where('plan_id=' . (int) $planId);
			$db->setQuery($query);
			$db->execute();
		}
		if (count($articleIds))
		{
			for ($i = 0; $i < count($articleIds); $i++)
			{
				$articleId = $articleIds[$i];
				$query->clear();
				$query->insert('#__osmembership_articles')
					->columns('plan_id,article_id')
					->values("$row->id,$articleId");
				$db->setQuery($query);
				$db->execute();
			}
		}
	}

	/**
	 * Display form allows users to change setting for this subscription plan
	 *
	 * @param object $row
	 *
	 */
	function _drawSettingForm($row)
	{
		//Get categories
		$categoryIds = $this->params->get('category_ids');
		$db          = JFactory::getDbo();
		$query       = $db->getQuery(true);
		$query->select('id, title')
			->from('#__categories')
			->where('extension = "com_content"')
			->where('published = 1');
		if (count($categoryIds) && !in_array(0, $categoryIds))
		{
			$query->where('id IN (' . implode(',', $categoryIds) . ')');
		}
		$db->setQuery($query);
		$categories = $db->loadObjectList('id');
		if (!count($categories))
		{
			return;
		}
		$categoryIds = array_keys($categories);
		$query->clear();
		$query->select('id, title, catid')
			->from('#__content')
			->where('`state` = 1')
			->where('catid IN (' . implode(',', $categoryIds) . ')');
		$db->setQuery($query);
		$rowArticles = $db->loadObjectList();
		if (!count($rowArticles))
		{
			return;
		}
		$articles = array();
		foreach ($rowArticles as $rowArticle)
		{
			$articles[$rowArticle->catid][] = $rowArticle;
		}
		//Get plans articles
		$query->clear();
		$query->select('article_id')
			->from('#__osmembership_articles')
			->where('plan_id=' . (int) $row->id);
		$db->setQuery($query);
		$planArticles = $db->loadColumn();
		?>
		<table class="admintable adminform" style="width: 100%;">
			<tr>
				<td>
					<div class="accordion" id="accordion2">
						<?php
						$count = 0;
						foreach ($categories as $category)
						{
							if (!isset($articles[$category->id]))
							{
								continue;
							}
							?>
							<div class="accordion-group">
								<div class="accordion-heading">
									<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapse<?php echo $category->id; ?>" style="display: inline;">
										<?php echo $category->title; ?>
									</a>
									<label class="checkbox"> <input type="checkbox" value="<?php echo $category->id?>" id="<?php echo $category->id?>" class="checkAll" name=""> <strong>#</strong> </label>
								</div>
								<div id="collapse<?php echo $category->id; ?>" class="accordion-body collapse <?php if ($count == 0) echo ' in'; ?>">
									<div class="accordion-inner">
										<?php
										$categoryArticles = $articles[$category->id];
										foreach ($categoryArticles as $article)
										{
										?>
											<label class="checkbox" style="display: block;">
												<input type="checkbox" <?php if (in_array($article->id, $planArticles)) echo ' checked="checked" '; ?> value="<?php echo $article->id; ?>" id="article_<?php echo $article->id; ?>" name="article_id[]" class="checkall_<?php echo $category->id?>" />
												<strong><?php echo $article->title; ?></strong>
											</label>
										<?php
										}
										?>
									</div>
								</div>
							</div>
							<?php
							$count++;
						}
						?>
					</div>
				</td>
			</tr>
		</table>
		<script type="text/javascript">
			(function($){
				$(document).ready(function(){
					$(".checkAll").click(function () {
						var ID = $(this).attr("id");
						if ($(this).is(':checked'))
						{
							$('.checkall_' + ID).attr("checked", true);
						}
						else
						{
							$('.checkall_' + ID).attr("checked", false);
						}
					});

				})
			})(jQuery)
		</script>
	<?php
	}
}