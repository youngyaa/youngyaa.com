<?php
/**
 * @package     com_bt_media - BT Media
 * @version	1.0.0
 * @created	Oct 2012
 * @author	BowThemes
 * @email	support@bowthems.com
 * @website	http://bowthemes.com
 * @support	Forum - http://bowthemes.com/forum/
 * @copyright   Copyright (C) 2012 Bowthemes. All rights reserved.
 * @license	http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
// no direct access
defined('_JEXEC') or die;

if (Bt_mediaLegacyHelper::isLegacy()) {
    JHtml::_('behavior.tooltip');
} else {
    JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
    JHtml::_('bootstrap.tooltip');
    JHtml::_('formbehavior.chosen', 'select');
}
JHtml::_('behavior.multiselect');
// Import CSS

$document = JFactory::getDocument();
$document->addStyleSheet('components/com_bt_media/assets/css/bt_media.css');
?>

<?php
//Joomla Component Creator code to allow adding non select list filters
if (!empty($this->extra_sidebar)) {
    $this->sidebar .= $this->extra_sidebar;
}
?>

<form action="<?php echo JRoute::_('index.php?option=com_bt_media&view=controlpanel'); ?>" method="post" name="adminForm" id="adminForm" style="display: block;">
    <?php if (!empty($this->sidebar)): ?>
        <div id="j-sidebar-container" class="span2">
            <?php echo $this->sidebar; ?>
        </div>
        <div id="j-main-container" class="span10 com-cpanel">
        <?php else : ?>
            <div id="j-main-container">
            <?php endif; ?>
            <div class="clr"> </div>
            <div class="content-left" style="width: 45%;">
                <a href="<?php echo JRoute::_('index.php?option=com_bt_media&view=categories'); ?>"><div class="c-item c-category"><span><?php echo JText::_("COM_BT_MEDIA_CPANEL_CATEGORY"); ?></span></div></a>
                <a href="<?php echo JRoute::_('index.php?option=com_bt_media&view=list'); ?>"><div class="c-item c-media"><span><?php echo JText::_("COM_BT_MEDIA_CPANEL_MEDIA"); ?></span></div></a>
                <a href="<?php echo JRoute::_('index.php?option=com_bt_media&view=detail&layout=edit'); ?>"><div class="c-item c-media-add"><span><?php echo JText::_("COM_BT_MEDIA_CPANEL_MEDIA_ADD"); ?></span></div></a>
                <a href="<?php echo JRoute::_('index.php?option=com_bt_media&view=tags'); ?>"><div class="c-item c-tag"><span><?php echo JText::_("COM_BT_MEDIA_CPANEL_TAG"); ?></span></div></a>
            </div>
            <div class="content-right" style="float: right; border: solid 2px #C2C2C2; width: 50%; background: #ffffff;">
                <div class="com-description">
                    <h3><?php echo JText::_("COM_BT_MEDIA"); ?></h3>
                    <a href="http://bowthemes.com" target="_blank"><img src="components/com_bt_media/assets/icon/bt_media.png"></a>
                    <p><?php echo JText::_("COM_BT_MEDIA_COMPONENT_DESC"); ?></p>
                    <br clear="both" />
                    <h3><?php echo JText::_("COM_BT_MEDIA_FEATURE_LABEL"); ?></h3>
                    <?php echo JText::_("COM_BT_MEDIA_FEATURE"); ?>
                    <div class="com-version-contact">
                        <div class="version-alert"><?php echo sprintf(JText::_("COM_BT_MEDIA_VERSION_ALERT"), COM_BT_MEDIA_VERSION);?></div>
                        <div class="com-contact"><?php echo JText::_("COM_BT_MEDIA_CONTACT");?></div>
                    </div>

                </div>
            </div>

        </div>
</form>
<style type="text/css">
    .pull-right .chzn-container{
        width: auto !important;
    }
</style>