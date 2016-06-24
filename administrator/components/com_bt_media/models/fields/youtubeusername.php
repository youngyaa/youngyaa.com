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

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');

/**
 * Supports an HTML select list of categories
 */
class JFormFieldYoutubeUsername extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'youtubeusername';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput()
	{
		// Initialize variables.
		$html = array();

                $html[] = '<input type="text" id="'.$this->id.'" name="'.$this->name.'" />';
                $html[] = '
                    <script type="text/javascript">
                        $BM(document).ready(function() {
                            $BM("#'.$this->id.'").blur(function(){
                                var uname = $BM("#'.$this->id.'").val();
                                if(uname != ""){
                                    getAlbums("get_video", "youtube", "getplaylists", uname);
                                    //comMediaGetItemCount("youtube", "user|"+uname);
                                }else{
                                    $BM("#tabs-7 li.message-display").addClass("error").html("Please input Youtube user").fadeIn(1000);
                                }
                            });
                        });
                    </script>
                    ';
		return implode($html);
	}
}