<?php
/**
 * @package 	mod_bt_contentshowcase - BT ContentShowcase Module
 * @version		1.0
 * @created		June 2012
 * @author		BowThemes
 * @email		support@bowthems.com
 * @website		http://bowthemes.com
 * @support		Forum - http://bowthemes.com/forum/
 * @copyright	Copyright (C) 2012 Bowthemes. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

/**
 * Form Field to display a list of the layouts for module display from the module or template overrides.
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @since       11.1
 */
class JFormFieldLayout extends JFormField {

    /**
     * The form field type.
     *
     * @var    string
     * @since  11.1
     */
    protected $type = 'layout';
    public function getInput() {
		$mainframe = JFactory::getApplication('site');
		$template = $mainframe->getTemplate();
		$themePath = JPATH_ROOT . '/modules/mod_bt_contentshowcase/tmpl/themes';
		$themes = JFolder::folders($themePath);
		
		$options = array();
		
		if($themes){
			
			foreach($themes as $theme){
				if(JFolder::exists($themePath. '/'. $theme) && JFile::exists($themePath. '/'. $theme . '/'. $theme . '.php') && $theme != 'responsive')
					$options[$theme] = (object) array('value' => $theme, 'text' => ucfirst(str_replace('_',' ', $theme)));
			}
		}	
		
		$db = JFactory::getDbo();
		$db->setQuery( 'SELECT id,template FROM #__template_styles WHERE client_id=0 AND home=1');
		$rs = $db->loadObject();
		
		if($rs){
			$templatePath = JPATH_ROOT . '/templates/' . $rs->template . '/html/mod_bt_contentshowcase/themes';
			if(is_dir($templatePath)){
				$themes = JFolder::folders($templatePath);
				if($themes){
					foreach($themes as $theme){
						if(JFolder::exists($templatePath. '/'. $theme) && JFile::exists($templatePath. '/'. $theme . '/'. $theme . '.php') && $theme != 'responsive'){
							if(!array_key_exists($theme, $options)){
								$options[$theme] = (object) array('value' => $theme, 'text' => ucfirst(str_replace('_',' ', $theme)));
							}
						}
					}	
				}
			}
		}
		if(count($options)){

			$attr = $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';
			// Prepare HTML code
			// Add a grouped list
			$html = JHtml::_(
							'select.genericlist', $options, $this->name, $attr, 'value', 'text', $this->value, $this->id
			);

			//$html = implode($html);
			$screenshotURI = 'http://bowthemes.com/images/screenshots/bt_contentshowcase';
			$html .= '
				<div id="layout-demo">
					<a href="" rel="lightbox">Demo layout</a>
				</div>
				<script type="text/javascript">
					jQuery(document).ready(function(){
						
						jQuery("#jform_params_layout_chzn").css("float","left");
						var layout = jQuery("#jform_params_layout").val();
						jQuery("#layout-demo a").attr("href", "' . $screenshotURI . '/' . '" + layout + ".jpg");
						//hide field not belong to layout
						jQuery(".layout-config").each(function(){
							if(!jQuery(this).hasClass(layout)){
							
								jQuery(this).parents("li:first").hide();
							}
						});
						
						//hide field not belong to layout when change layout
						jQuery("#jform_params_layout").change(function(){
							layout = jQuery(this).val();
							jQuery("#layout-demo a").attr("href", "' . $screenshotURI . '/' . '" + layout + ".jpg");
						});
						jQuery("#layout-demo a").lightBox();
					});
				</script>
				<style type="text/css">
				#layout-demo{
					width: 88px; height: 18px;
					background: url(' . JURI::root() . 'modules/mod_bt_contentshowcase/admin/images/demo.png'.') no-repeat;
					float: left;
					margin: 5px 10px;
					text-align: center;
				}
				#layout-demo a{
					color: #ffffff;
					font-family: arial; font-size: 11px;
					line-height: 17px;
				}
				</style>
				';
			return $html;
		}else{
			return '';
		}
    }

}

?>