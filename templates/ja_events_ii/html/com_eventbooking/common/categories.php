<?php
/**
 * @version        	2.0.0
 * @package        	Joomla
 * @subpackage		Event Booking
 * @author  		Tuan Pham Ngoc
 * @copyright    	Copyright (C) 2010 - 2015 Ossolution Team
 * @license        	GNU/GPL, see LICENSE.php
 */
// no direct access
defined( '_JEXEC' ) or die;
if (count($categories))
{
    if ($categoryId)
    {
    ?>
        <h2 class="eb-heading"><?php echo JText::_('EB_SUB_CATEGORIES'); ?></h2>
    <?php
    }
    ?>
    <div id="eb-categories">
    <div class="owl-carousel">
        <?php
        for ($i = 0 , $n = count($categories) ; $i < $n ; $i++)
        {
            $category = $categories[$i];
            if (!$config->show_empty_cat && !$category->total_events)
            {
                continue ;
            }
            ?>
            <div class="eb-category">
                <div class="eb-box-heading">
                    <h3 class="eb-category-title">
                        <a href="<?php echo JRoute::_(EventbookingHelperRoute::getCategoryRoute($category->id, $Itemid)); ?>" class="eb-category-title-link">
                            <?php
                                echo $category->name;
                                if ($config->show_number_events) {
                                ?>
                                    <small>( <?php echo $category->total_events ;?> <?php echo $category->total_events > 1 ? JText::_('EB_EVENTS') :  JText::_('EB_EVENT') ; ?> )</small>
                                <?php
                                }
                            ?>
                        </a>
                    </h3>
                </div>
                <?php
                    if($category->description)
                    {
                    ?>
                        <div class="eb-description clearfix"><?php echo $category->description;?></div>
                    <?php
                    }
                ?>
            </div>
        <?php
        }
        ?>
        </div>
    </div>
<?php
}