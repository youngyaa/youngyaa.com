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
class JFormFieldYoutubeGetBy extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'youtubegetby';

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
                
                $html[] = '<select id="' . $this->id . '" name="' . $this->name . '">';
                $html[] = '<option value="playlist_video">'.JText::_('Playlist or video').'</option>';
                $html[] = '<option value="username">'.JText::_('Username').'</option>';
                $html[] = '</select>';
                $html[] = '
                    <script type="text/javascript">
                        $BM(document).ready(function() {
                            $BM("#'.$this->id.'").change(function(){
                                var getby = $BM("#'.$this->id.'").val();
                                if(getby == "username"){
                                    var uname = $BM("#jform_youtube_username").val();
                                    var pl = $BM("#jform_youtube_playlists").val();
                                    if(uname != "" && pl == ""){
                                        getAlbums("get_video", "youtube", "getplaylists", uname);
                                        comMediaGetItemCount("youtube", "user|"+uname);
                                    }else if(uname != "" && pl != ""){
                                        comMediaAlbumChangeData("youtube");
                                    }else{
                                        btDisable("youtube");
                                    }
                                    $BM("#yt-url").fadeOut(1000,function(){
                                        $BM("#yt-username").fadeIn(1000);
                                        $BM("#yt-playlists").fadeIn(1000);
                                    });
                                }
                                if(getby == "playlist_video"){
                                    var url_data = $BM("#jform_youtube_url").val();
                                    if(url_data != ""){
                                        getvideoFromURL("youtube", url_data);
                                    }else{
                                        btDisable("youtube");
                                    }
                                    $BM("#yt-url").fadeIn(1000, function(){
                                        $BM("#yt-username").fadeOut(1000);
                                        $BM("#yt-playlists").fadeOut(1000);
                                    });
                                }
                            });
                            $BM("#'.$this->id.'").trigger("change");
                        });
                    </script>
                    ';

		return implode($html);
	}
}