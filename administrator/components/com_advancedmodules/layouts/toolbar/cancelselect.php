<?php
/**
 * @package         Advanced Module Manager
 * @version         6.0.1
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            http://www.regularlabs.com
 * @copyright       Copyright © 2016 Regular Labs All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

/**
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$text = JText::_('JTOOLBAR_CANCEL');
?>
<button onclick="location.href='index.php?option=com_advancedmodules'" class="btn" title="<?php echo $text; ?>">
	<span class="icon-remove"></span> <?php echo $text; ?>
</button>
