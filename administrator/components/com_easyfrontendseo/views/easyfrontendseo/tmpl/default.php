<?php
/**
 * EFSEO - Easy Frontend SEO for Joomal! 3.x
 * License: GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * Author: Viktor Vogel
 * Project page: https://joomla-extensions.kubik-rubik.de/efseo-easy-frontend-seo
 *
 * @license GNU/GPL
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
?>
<form action="<?php echo JRoute::_('index.php?option=com_easyfrontendseo'); ?>" method="post" name="adminForm"
      id="adminForm">
    <div id="j-main-container">
        <div id="filter-bar" class="btn-toolbar">
            <div class="filter-search btn-group pull-left">
                <label for="filter_search"
                       class="element-invisible"><?php echo JText::_('JSEARCH_FILTER'); ?></label>
                <input type="text" name="filter_search" id="filter_search"
                       placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>"
                       value="<?php echo $this->escape($this->state->get('filter.search')); ?>" class="hasTooltip"
                       title="<?php echo JText::_('JSEARCH_FILTER'); ?>"/>
            </div>
            <div class="btn-group pull-left hidden-phone">
                <button type="submit" class="btn hasTooltip"
                        title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i>
                </button>
                <button type="button" class="btn hasTooltip" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>"
                        onclick="document.id('filter_search').value = '';
                    this.form.submit();"><i class="icon-remove"></i></button>
            </div>
            <div class="btn-group pull-right hidden-phone">
                <label for="limit"
                       class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?></label>
                <?php echo $this->pagination->getLimitBox(); ?>
            </div>
        </div>
        <div class="clearfix"></div>
        <table id="articleList" class="table table-striped">
            <thead>
            <tr>
                <th width="20">
                    <input type="checkbox" name="checkall-toggle" value=""
                           title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)"/>
                </th>
                <th width="24%">
                    <?php echo JText::_('COM_EASYFRONTENDSEO_URL'); ?>
                </th>
                <th width="12%">
                    <?php echo JText::_('COM_EASYFRONTENDSEO_TITLE'); ?>
                </th>
                <th>
                    <?php echo JText::_('COM_EASYFRONTENDSEO_DESCRIPTION'); ?>
                </th>
                <th width="10%">
                    <?php echo JText::_('COM_EASYFRONTENDSEO_KEYWORDS'); ?>
                </th>
                <th width="8%">
                    <?php echo JText::_('COM_EASYFRONTENDSEO_GENERATOR'); ?>
                </th>
                <th width="5%">
                    <?php echo JText::_('COM_EASYFRONTENDSEO_ROBOTS'); ?>
                </th>
                <th width="2%">
                </th>
            </tr>
            </thead>
            <tbody>
            <?php
            $k = 0;
            $n = count($this->items);

            for($i = 0; $i < $n; $i++)
            {
                $row = $this->items[$i];
                $checked = JHTML::_('grid.id', $i, $row->id, false, 'id');
                $link = JRoute::_('index.php?option=com_easyfrontendseo&controller=entry&task=edit&id[]='.$row->id);
                ?>
                <tr class="<?php echo "row$k"; ?>">
                    <td>
                        <?php echo $checked; ?>
                    </td>
                    <td>
                    <span class="hasTooltip" title="<?php echo $row->url; ?>">
                        <?php
                        if(strlen($row->url) > 120) :
                            $url = substr($row->url, 0, 120)."...";
                        else :
                            $url = $row->url;
                        endif;
                        ?>
                        <a href="<?php echo $link ?>"><?php echo $url; ?></a>
                        <?php
                        if(strpos($row->url, JURI::root()) === false) :
                            $row->url = JURI::root().$row->url;
                        endif;
                        ?>
                        <a href="<?php echo $row->url; ?>" target="_blank"><img
                                src="components/com_easyfrontendseo/images/external.png"/></a>
                    </span>
                    </td>
                    <td>
                    <span class="hasTooltip" title="<?php echo htmlspecialchars($row->title); ?>">
                        <?php
                        if(strlen($row->title) > 65) :
                            echo substr($row->title, 0, 65)."...";
                        else :
                            echo $row->title;
                        endif;
                        ?>
                    </span>
                    </td>
                    <td>
                    <span class="hasTooltip" title="<?php echo htmlspecialchars($row->description); ?>">
                        <?php
                        if(strlen($row->description) > 165) :
                            echo mb_substr($row->description, 0, 165)."...";
                        else :
                            echo $row->description;
                        endif;
                        ?>
                    </span>
                    </td>
                    <td>
                    <span class="hasTooltip" title="<?php echo htmlspecialchars($row->keywords); ?>">
                        <?php
                        if(strlen($row->keywords) > 30) :
                            echo substr($row->keywords, 0, 30)."...";
                        else :
                            echo $row->keywords;
                        endif;
                        ?>
                    </span>
                    </td>
                    <td>
                    <span class="hasTooltip" title="<?php echo htmlspecialchars($row->generator); ?>">
                        <?php
                        if(strlen($row->generator) > 30) :
                            echo substr($row->generator, 0, 30)."...";
                        else :
                            echo $row->generator;
                        endif;
                        ?>
                    </span>
                    </td>
                    <td>
                    <span class="hasTooltip" title="<?php echo htmlspecialchars($row->robots); ?>">
                        <?php
                        if(strlen($row->robots) > 30) :
                            echo substr($row->robots, 0, 30)."...";
                        else :
                            echo $row->robots;
                        endif;
                        ?>
                    </span>
                    </td>
                    <td>
                        <?php if(!empty($row->complete)) : ?>
                            <img src="<?php echo JUri::base(); ?>components/com_easyfrontendseo/images/check.png"
                                 alt="<?php echo JText::_('COM_EASYFRONTENDSEO_ALLDATA'); ?>"
                                 title="<?php echo JText::_('COM_EASYFRONTENDSEO_ALLDATA'); ?>"/>
                        <?php else : ?>
                            <img src="<?php echo JUri::base(); ?>components/com_easyfrontendseo/images/cross.png"
                                 alt="<?php echo JText::_('COM_EASYFRONTENDSEO_NOTALLDATA'); ?>"
                                 title="<?php echo JText::_('COM_EASYFRONTENDSEO_NOTALLDATA'); ?>"/>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php
                $k = 1 - $k;
            }
            ?>
            </tbody>
            <tfoot>
            <tr>
                <td class="center" colspan="8">
                    <?php echo $this->pagination->getListFooter(); ?>
                    <?php if(isset($this->plugin_state['enabled']) AND isset($this->plugin_state['url_settings'])) : ?>
                        <p class="footer-tip">
                            <?php if($this->plugin_state['enabled'] == true) : ?>
                                <span class="text-success"><span
                                        class="icon-easyfrontendseo-success"></span> <?php echo JText::sprintf('COM_EASYFRONTENDSEO_PLUGIN_ENABLED', $this->plugin_state['url_settings']); ?></span>
                            <?php else : ?>
                                <span class="text-error"><span
                                        class="icon-easyfrontendseo-error"></span> <?php echo JText::sprintf('COM_EASYFRONTENDSEO_PLUGIN_DISABLED', $this->plugin_state['url_settings']); ?></span>
                            <?php endif; ?>
                        </p>
                    <?php endif; ?>
                </td>
            </tr>
            </tfoot>
        </table>

        <?php echo $this->loadTemplate('batch'); ?>

        <input type="hidden" name="option" value="com_easyfrontendseo"/>
        <input type="hidden" name="task" value=""/>
        <input type="hidden" name="boxchecked" value="0"/>
        <input type="hidden" name="controller" value="entry"/>
        <?php echo JHTML::_('form.token'); ?>
    </div>
</form>
<div style="text-align: center; margin-top: 10px;">
    <p><?php echo JText::sprintf('COMEASYFRONTENDSEO_VERSION', EASYFRONTENDSEO_VERSION) ?></p>
</div>
<?php echo $this->donation_code_message; ?>