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

$robots_array = array('', 'index, follow', 'noindex, follow', 'index, nofollow', 'noindex, nofollow');
?>
<div class="modal hide fade" id="collapseModal">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">&#215;</button>
		<h3><?php echo JText::_('COM_EASYFRONTENDSEO_BATCH_OPTIONS'); ?></h3>
	</div>
	<div class="modal-body modal-batch">
		<p><?php echo JText::_('COM_EASYFRONTENDSEO_BATCH_TIP'); ?></p>
		<div class="row-fluid">
            <div class="control-group">
                <div class="control-label">
                    <label for="batch_generator">
                        <strong><?php echo JText::_('COM_EASYFRONTENDSEO_GENERATOR'); ?>:</strong>
                    </label>
                </div>
                <div class="controls form-inline">
                    <input class="text_area" type="text" name="batch[generator][value]" id="batch_generator" size="80"
                           placeholder="<?php echo JText::_('COM_EASYFRONTENDSEO_GENERATOR'); ?>"
                           maxlength="255" value=""/>
                        <label>
                            <input type="checkbox" name="batch[generator][activated]">
                            <?php echo JText::_('COM_EASYFRONTENDSEO_BATCH_ACTIVATED'); ?>
                        </label>
                </div>
            </div>
            <div class="control-group">
                <div class="control-label">
                    <label for="batch_robots">
                        <strong><?php echo JText::_('COM_EASYFRONTENDSEO_ROBOTS'); ?>:</strong>
                    </label>
                </div>
                <div class="controls form-inline">
                    <select class="" id="batch_robots" name="batch[robots][value]">
                        <?php foreach($robots_array as $robots_value) : ?>
                            <option value="<?php echo $robots_value; ?>"><?php echo $robots_value; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label>
                        <input type="checkbox" name="batch[robots][activated]">
                        <?php echo JText::_('COM_EASYFRONTENDSEO_BATCH_ACTIVATED'); ?>
                    </label>
                </div>
            </div>
		</div>
	<div class="modal-footer">
		<button class="btn" type="button" data-dismiss="modal">
			<?php echo JText::_('JCANCEL'); ?>
		</button>
		<button class="btn btn-primary" type="submit" onclick="Joomla.submitbutton('batch');">
			<?php echo JText::_('JGLOBAL_BATCH_PROCESS'); ?>
		</button>
	</div>
</div>
