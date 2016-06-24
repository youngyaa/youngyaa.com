<?php

/**
 * @package     Joomla.Plugin
 * @subpackage  Editors-xtd.article
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

/**
 * Editor Article buton
 *
 * @package     Joomla.Plugin
 * @subpackage  Editors-xtd.article
 * @since       1.5
 */
class plgButtonBt_Media_Button extends JPlugin {

    /**
     * Constructor
     *
     * @access      protected
     * @param       object  $subject The object to observe
     * @param       array   $config  An array that holds the plugin configuration
     * @since       1.5
     */
    public function __construct(& $subject, $config) {
        parent::__construct($subject, $config);
        $this->loadLanguage();
    }

    /**
     * Display the button
     *
     * @return array A four element array of (article_id, article_title, category_id, object)
     */
    public function onDisplay($name, $asset, $author) {
        /*
         * Javascript to insert the link
         * View element calls jSelectArticle when an article is clicked
         * jSelectArticle creates the link tag, sends it to the editor,
         * and closes the select frame.
         */
        JHtml::_('behavior.modal');
        $app = JFactory::getApplication();
        $user = JFactory::getUser();
        $extension = $app->input->get('option');
        $js = "
		function jSelectGallery(text)
		{
			jInsertEditorText(text, '" . $name . "');
			SqueezeBox.close();
		}";

        $doc = JFactory::getDocument();
        $doc->addScriptDeclaration($js);
        $doc->addStyleDeclaration('.btn-toolbar .icon-image:before {content: "0";}');


        if ($asset == '') {
            $asset = $extension;
        }

        /*
         * Use the built-in element view to select the article.
         * Currently uses blank class.
         */
        if ($user->authorise('core.edit', $asset) || $user->authorise('core.create', $asset) || (count($user->getAuthorisedCategories($asset, 'core.create')) > 0) || ($user->authorise('core.edit.own', $asset) && $author == $user->id) || (count($user->getAuthorisedCategories($extension, 'core.edit')) > 0) || (count($user->getAuthorisedCategories($extension, 'core.edit.own')) > 0 && $author == $user->id)
        ) {
            $link = 'index.php?option=com_bt_media&amp;view=list&amp;layout=modal&amp;tmpl=component&amp;' . JSession::getFormToken() . '=1';

            $button = new JObject;
            $button->modal = true;
            $button->link = $link;
            $button->text = JText::_('PLG_EDITORS-XTD_BT_MEDIA_BUTTON_TITLE');
            $button->name = 'image';
            $button->class = 'btn';
            $button->options = "{handler: 'iframe', size: {x: 805, y: 500}}";

            return $button;
        } else {
            return FALSE;
        }
    }

}
