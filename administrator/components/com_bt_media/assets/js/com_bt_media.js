/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
jQuery(document).ready(function() {
    jQuery("#jform_image_process").change(function() {
        imgProcess();
    });
    jQuery("#jform_image_process_type").change(function() {
        processTypeChange();
    });
    jQuery("#jform_image_process_crop_position").change(function() {
        cropPosChange();
    });
    jQuery("#jform_image_process").trigger("change");
    jQuery("#jform_image_process_type").trigger("change");

    jQuery("#jform_youtube_from").change(function(){
        jQuery("#btnGetVideosFromYoutube .custom-button").removeClass("bt-disable").addClass("bt-enable");
    });
    jQuery("#jform_youtube_to").change(function(){
        jQuery("#btnGetVideosFromYoutube .custom-button").removeClass("bt-disable").addClass("bt-enable");
    });
    
    jQuery("#jform_vimeo_from").change(function(){
        jQuery("#btnGetVideosFromVimeo .custom-button").removeClass("bt-disable").addClass("bt-enable");
    });
    jQuery("#jform_vimeo_to").change(function(){
        jQuery("#btnGetVideosFromVimeo .custom-button").removeClass("bt-disable").addClass("bt-enable");
    });
    
    jQuery("#jform_picasa_from").change(function(){
        jQuery("#btnGetImagesFromPicasa .custom-button").removeClass("bt-disable").addClass("bt-enable");
    });
    jQuery("#jform_picasa_to").change(function(){
        jQuery("#btnGetImagesFromPicasa .custom-button").removeClass("bt-disable").addClass("bt-enable");
    });
    
    jQuery("#jform_flickr_from").change(function(){
        jQuery("#btnGetImagesFromFlickr .custom-button").removeClass("bt-disable").addClass("bt-enable");
    });
    jQuery("#jform_flickr_to").change(function(){
        jQuery("#btnGetImagesFromFlickr .custom-button").removeClass("bt-disable").addClass("bt-enable");
    });
    //js for btn upload from folder
    jQuery('#btnUploadFromFolder').click(function() {
        var elem = jQuery(this).parent().find('ul');
        var inputs = jQuery(elem).find('input');
        var data = getListFolderSelect(inputs);
        if (jQuery('#chkSubFolder').is(':checked')) {
            data[data.length] = 1;
        } else {
            data[data.length] = 0;
        }
        jQuery(this).addClass('bt-disable').removeClass('bt-enable');
        jQuery.ajax({
            url: btMediaCfg.siteURL + 'index.php',
            data: "option=com_bt_media&task=detail.getImage&from=jfolder&data=" + data,
            type: "post",
            beforeSend: function() {
                jQuery("#folderget .content-right .ajax-loading").removeClass("load-complete").addClass("loader").html("").fadeIn(500);
            },
            success: function(response) {
                var a = jQuery.parseJSON(response);
                if (a.success) {
                    comMediaGetItem('jfolder', a.data, 0, a.data.length - 1, false);
                }
            }
        });
    });

});

function getListFolderSelect(value) {
    var folders = new Array();
    var j = 0;
    for (var i = 0; i < value.length; i++) {
        var folder = jQuery(value[i]).val();
        if (folder !== "") {
            folders[j] = folder;
            j++;
        }
    }
    return folders;
}

function select(e, value) {
    var fselect = jQuery(e).parent().find("input").val();
    if (fselect == "") {
        fselect = value;
        //jQuery(e).parent().find("input").val(fselect);
        jQuery(e).attr("onclick", "unSelect(this, \'" + value + "\')");
    } else {
        fselect = fselect + "|" + value;
        //jQuery(e).parent().find("input").val(fselect);
        jQuery(e).attr("onclick", "unSelect(this, \'" + value + "\')");
    }
    jQuery(e).find('input').val(value);
    jQuery(e).css({
        "background": "#84C5E5"
    });
    btUploadFromFolderControl(e);
}
function unSelect(e, value) {
    jQuery(e).attr("onclick", "select(this, \'" + value + "\')");
    jQuery(e).find('input').val('');
    jQuery(e).css({
        "background": "#FFFFFF"
    });
    btUploadFromFolderControl(e);
}

function loadSubFolder(el, fdirect) {
    jQuery.ajax({
        url: btMediaCfg.siteURL + 'index.php',
        data: "option=com_bt_media&task=detail.loadSubFolder&folder=" + fdirect,
        type: "post",
        beforeSend: function() {
            jQuery(".ajax-loading", el).show();
        },
        success: function(data) {
            var a = jQuery.parseJSON(data);
            if (a.success) {
                jQuery(el).parent().html(a.data);
            } else {
                jQuery(".ajax-loading", el).hide();
            }
        }
    });
}

function loadParentFolder(el, fdirect) {
    jQuery.ajax({
        url: btMediaCfg.siteURL + 'index.php',
        data: "option=com_bt_media&task=detail.loadParentFolder&folder=" + fdirect,
        type: "post",
        beforeSend: function() {
            jQuery(".ajax-loading", el).show();
        },
        success: function(data) {
            var a = jQuery.parseJSON(data);
            if (a.success) {
                jQuery(el).parent().html(a.data);
            } else {
                jQuery(".ajax-loading", el).hide();
            }
        }
    });
}

function btUploadFromFolderControl(e) {
    var inputs = jQuery(e).parent().find('input');
    var folders = getListFolderSelect(inputs);
    if (folders.length > 0) {
        jQuery('#btnUploadFromFolder').removeClass('bt-disable').addClass('bt-enable');
    } else {
        jQuery('#btnUploadFromFolder').removeClass('bt-enable').addClass('bt-disable');
    }
}


function btDisable(from) {
    if (from == "youtube") {
        jQuery("#youtube li.message-display").removeClass("error").html("").fadeOut(1000);
        jQuery("#i_yt_show_count").html("").fadeOut(1000);
        jQuery("#i_yt_from_to").fadeOut(1000);
        jQuery("#jform_youtube_from").html("");
        jQuery("#jform_youtube_to").html("");
        jQuery("#btnGetVideosFromYoutube .custom-button").addClass("bt-disable").removeClass("bt-enable");
    }
    if (from == "vimeo") {
        jQuery("#vimeo li.message-display").removeClass("error").html("").fadeOut(1000);
        jQuery("#i_vm_show_count").html("").fadeOut(1000);
        jQuery("#i_vm_from_to").fadeOut(1000);
        jQuery("#jform_vimeo_from").html("");
        jQuery("#jform_vimeo_to").html("");
        jQuery("#btnGetVideosFromVimeo .custom-button").addClass("bt-disable").removeClass("bt-enable");
    }
    if (from == "picasa") {
        jQuery("#btnGetImagesFromPicasa .custom-button").addClass("bt-disable").removeClass("bt-enable");
        jQuery("#i_p_from_to").fadeOut(1000);
        jQuery("#i_p_show_count").html("").fadeOut(1000);
        jQuery("#jform_picasa_from").html("");
        jQuery("#jform_picasa_to").html("");
    }
    if (from == "flickr") {
        jQuery("#btnGetImagesFromFlickr .custom-button").addClass("bt-disable").removeClass("bt-enable");
        jQuery("#i_f_from_to").fadeOut(1000);
        jQuery("#i_f_show_count").html("").fadeOut(1000);
        jQuery("#jform_flickr_from").html("");
        jQuery("#jform_flickr_to").html("");
    }
}


function getAlbums(type, from, method, username) {
    if (type == "get_image") {
        if (from == "picasa") {
            jQuery.ajax({
                url: btMediaCfg.siteURL + 'index.php',
                data: "option=com_bt_media&task=detail.getImage&from=picasa&act=" + method + "&username=" + username,
                type: "post",
                beforeSend: function() {
                    jQuery("#picasa li.message-display").removeClass("error").fadeOut(400, function() {
                        jQuery("#picasa li.message-display").addClass("ajax-display").html("Loading...").fadeIn(400);
                    });
                },
                success: function(data) {
                    var a = jQuery.parseJSON(data);
                    if (a.success) {
                        jQuery("#jform_picasa_albumid").html(a.data);
                        jQuery("#jform_picasa_albumid").trigger('liszt:updated');
                        jQuery("#picasa li.message-display").removeClass("ajax-display error").html("").fadeOut(1000);
                    } else {
                        jQuery("#picasa li.message-display").removeClass("ajax-display").html("").fadeOut(1000, function() {
                            jQuery("#picasa li.message-display").addClass("error").html(a.message).fadeIn("slow");
                        });
                    }
                }
            });
        }

        if (from == "flickr") {
            jQuery.ajax({
                url: btMediaCfg.siteURL + 'index.php',
                data: "option=com_bt_media&task=detail.getImage&from=flickr&act=" + method + "&username=" + username,
                type: "post",
                beforeSend: function() {
                    jQuery("#flickr li.message-display").removeClass("error").fadeOut(400, function() {
                        jQuery("#flickr li.message-display").addClass("ajax-display").html("Loading...").fadeIn(400);
                    });
                },
                success: function(data) {
                    var a = jQuery.parseJSON(data);
                    if (a.success) {
                        jQuery("#jform_flickr_albumid").html(a.data);
                        jQuery("#jform_flickr_albumid").trigger('liszt:updated');
                        jQuery("#flickr li.message-display").removeClass("ajax-display error").html("").fadeOut(1000);
                    } else {
                        jQuery("#flickr li.message-display").removeClass("ajax-display").html("").fadeOut(1000, function() {
                            jQuery("#flickr li.message-display").addClass("error").html(a.message).fadeIn("slow");
                        });
                    }
                }
            });
        }
    }
    if (type == "get_video") {
        if (from == "youtube") {
            jQuery("#youtube-fields .ajax-display").html("Loadding...").fadeIn();
            btDisable(from);
            jQuery.ajax({
                url: btMediaCfg.siteURL + 'index.php',
                data: "option=com_bt_media&task=detail.getVideo&from=youtube&method=" + method + "&username=" + username,
                type: "post",
                beforeSend: function() {
                    jQuery("#youtube-fields .ajax-display").html("Loadding...").fadeIn();
                },
                success: function(response) {
                    var data = jQuery.parseJSON(response);
                    if (data.success) {
                        jQuery("#jform_youtube_playlists").html(data.data);
                        jQuery("#jform_youtube_playlists").trigger('liszt:updated');
                        jQuery("#youtube-fields .ajax-display").html("Success").fadeOut();
                    } else {
                        jQuery("#youtube-fields .ajax-display").html("Fail!").fadeOut();
                    }
                }
            });
        }

        if (from == "vimeo") {
            jQuery("#vimeo-fields .ajax-display").html("Loadding...").fadeIn();
            btDisable(from);
            jQuery.ajax({
                url: btMediaCfg.siteURL + 'index.php',
                data: "option=com_bt_media&task=detail.getVideo&from=vimeo&method=" + method + "&username=" + username,
                type: "post",
                beforeSend: function() {
                    jQuery("#vimeo-fields .ajax-display").html("Loadding...").fadeIn();
                },
                success: function(response) {
                    var a = jQuery.parseJSON(response);
                    if (a.success) {
                        jQuery("#jform_vimeo_playlists").html(a.data);
                        jQuery("#jform_vimeo_playlists").trigger('liszt:updated');
                        jQuery("#vimeo-fields .ajax-display").html("Success").fadeOut();
                    } else {
                        jQuery("#vimeo-fields .ajax-display").html("Fail").fadeOut();
                    }
                }
            });
        }
    }
}


function comMediaGetItem(source, data, from, to, complete) {
    if (source == "jfolder") {
        if (complete) {
            jQuery("#folderget .custom-button").removeClass("bt-disable").addClass("bt-enable");
            jQuery("#folderget .content-right .ajax-loading").removeClass("loader").addClass("load-complete").html("<h2>Complete!</h2>");
        } else {
            jQuery.ajax({
                url: btMediaCfg.siteURL + 'index.php',
                data: "option=com_bt_media&task=detail.getImage&from=jfolder&act=getphoto&photoid=" + data[from],
                type: "post",
                success: function(response) {
                    var a = jQuery.parseJSON(response);
                    if (a.success) {
                        jQuery("#display_upload_image_from_jfolder .img-list").append(a.data);
                        jQuery("#display_upload_image_from_jfolder .img-list li:last-child").fadeIn(1000);
                    }
                    from++;
                    if (from <= to) {
                        comMediaGetItem(source, data, from, to, false);
                    } else {
                        comMediaGetItem(source, data, from, to, true);
                    }
                }
            });
        }
    }

    if (source == "picasa") {
        if (complete) {
            jQuery("#picasa .custom-button").removeClass("bt-disable").addClass("bt-enable");
            jQuery("#picasa .ajax-loading").removeClass("loader").addClass("load-complete").html("<h2>Complete!</h2>");
        } else {
            jQuery.ajax({
                url: btMediaCfg.siteURL + 'index.php',
                data: "option=com_bt_media&task=detail.getImage&from=picasa&act=getphoto&photoid[0]=" + data[from]['url'] + "&photoid[1]=" + data[from]['title'],
                type: "post",
                success: function(response) {
                    var a = jQuery.parseJSON(response);
                    if (a.success) {
                        jQuery("#display_picasa_image .img-list").append(a.data);
                        jQuery("#display_picasa_image .img-list li:last-child").fadeIn(1000);
                    }
                    from++;
                    if (from <= to) {
                        comMediaGetItem(source, data, from, to, false);
                    } else {
                        comMediaGetItem(source, data, from, to, true);
                    }
                }
            });
        }
    }

    if (source == "flickr") {
        if (complete) {
            jQuery("#flickr .custom-button").removeClass("bt-disable").addClass("bt-enable");
            jQuery("#flickr .ajax-loading").removeClass("loader").addClass("load-complete").html("<h2>Complete!</h2>").fadeIn(500);
        } else {
            jQuery.ajax({
                url: btMediaCfg.siteURL + 'index.php',
                data: "option=com_bt_media&task=detail.getImage&from=flickr&act=getphoto&photoid=" + data[from],
                type: "post",
                success: function(response) {
                    var a = jQuery.parseJSON(response);
                    if (a.success) {
                        jQuery("#display_flickr_image .img-list").append(a.data);
                        jQuery("#display_flickr_image .img-list li:last-child").fadeIn(1000);
                    }
                    from++;
                    if (from <= to) {
                        comMediaGetItem(source, data, from, to, false);
                    } else {
                        comMediaGetItem(source, data, from, to, true);
                    }
                }
            });
        }
    }

    if (source == "youtube") {
        if (complete) {
            jQuery("#btnGetVideosFromYoutube .custom-button").removeClass("bt-disable").addClass("bt-enable");
            jQuery("#youtube .ajax-loading").removeClass("loader").addClass("load-complete").html("<h2>Complete!</h2>");
        } else {
            jQuery.ajax({
                url: btMediaCfg.siteURL + 'index.php',
                data: "option=com_bt_media&task=detail.getVideo&from=youtube&method=getvideo&videoid=" + data[from],
                type: "post",
                success: function(response) {
                    var a = jQuery.parseJSON(response);
                    if (a.success) {
                        jQuery("#display_youtube_video .img-list").append(a.data);
                        jQuery("#display_youtube_video .img-list li:last-child").fadeIn(1000);
                    }
                    from++;
                    if (from <= to) {
                        comMediaGetItem(source, data, from, to, false);
                    } else {
                        comMediaGetItem(source, data, from, to, true);
                    }
                }
            });
        }
    }

    if (source == "vimeo") {
        if (complete) {
            jQuery("#vimeo .custom-button").removeClass("bt-disable").addClass("bt-enable");
            jQuery("#vimeo .ajax-loading").removeClass("loader").addClass("load-complete").html("<h2>Complete!</h2>");
        } else {
            jQuery.ajax({
                url: btMediaCfg.siteURL + 'index.php',
                data: "option=com_bt_media&task=detail.getVideo&from=vimeo&method=getvideo&videoid=" + data[from],
                type: "post",
                success: function(response) {
                    var a = jQuery.parseJSON(response);
                    if (a.success) {
                        jQuery("#display_vimeo_video .img-list").append(a.data);
                        jQuery("#display_vimeo_video .img-list li:last-child").fadeIn(1000);
                    }
                    from++;
                    if (from <= to) {
                        comMediaGetItem(source, data, from, to, false);
                    } else {
                        comMediaGetItem(source, data, from, to, true);
                    }
                }
            });
        }
    }
}

function comMediaAlbumChangeData(source) {
    var data = null;
    if (source == "youtube") {

        jQuery("#youtube-fields .ajax-display").html("Loadding...").fadeIn();
        btDisable(source);
        data = jQuery("#jform_youtube_playlists").val();
        if (data == "") {
            /**
            var uname = jQuery("#jform_youtube_username").val();
            if (uname !== "") {
                comMediaGetItemCount("youtube", "user|" + uname);
            }
             */
        } else {
            comMediaGetItemCount(source, data);
        }
    }
    if (source == "vimeo") {
        jQuery("#vimeo-fields .ajax-display").html("Loadding...").fadeIn();
        btDisable(source);
        data = jQuery("#jform_vimeo_playlists").val();
        comMediaGetItemCount(source, data);
    }
    if (source == "flickr") {
        data = jQuery("#jform_flickr_albumid").val();
        btDisable(source);
        comMediaGetItemCount(source, data);
    }
    if (source == "picasa") {
        data = new Array(jQuery("#jform_picasa_username").val(), jQuery("#jform_picasa_albumid").val());
        btDisable(source);
        comMediaGetItemCount(source, data);
    }
}

function comMediaGetItemCount(source, data) {

    if (source == "youtube") {
        jQuery.ajax({
            url: btMediaCfg.siteURL + 'index.php',
            data: "option=com_bt_media&task=detail.getVideo&from=youtube&method=getvideos&data=" + data,
            type: "post",
            beforeSend: function() {
                jQuery("#youtube-fields .ajax-display").html("Loadding...").fadeIn();
            },
            success: function(response) {
                var a = jQuery.parseJSON(response);
                if (a.success) {
                    if (typeof(a.data) == "object" && (a.data instanceof Array)) {
                        jQuery("#i_yt_show_count").html(a.data.length + " items found").fadeIn(1000);
                        for (var i = 0; i < a.data.length; i++) {
                            jQuery("#jform_youtube_from").append("<option value=" + i + ">" + (i + 1) + "</option>");
                        }
                        jQuery("#jform_youtube_from").trigger('liszt:updated');
                        for (var i = 0; i < a.data.length; i++) {
                            if (i == a.data.length - 1) {
                                jQuery("#jform_youtube_to").append("<option value=" + i + " selected=\"selected\">" + (i + 1) + "</option>");
                            } else {
                                jQuery("#jform_youtube_to").append("<option value=" + i + ">" + (i + 1) + "</option>");
                            }
                        }
                        jQuery("#jform_youtube_to").trigger('liszt:updated');
                        jQuery("#i_yt_from_to").fadeIn(1000);
                        jQuery("#btnGetVideosFromYoutube .custom-button").addClass("bt-enable").removeClass("bt-disable");
                    } else {
                        jQuery("#i_yt_show_count").html("").fadeOut(1000);
                        jQuery("#i_yt_from_to").fadeOut(1000);
                        jQuery("#jform_youtube_from").html("<option value=0>1</option>");
                        jQuery("#jform_youtube_to").html("<option value=0>1</option>");
                        jQuery("#btnGetVideosFromYoutube .custom-button").addClass("bt-enable").removeClass("bt-disable");
                    }
                    jQuery("#youtube-fields .ajax-display").html("Success").fadeOut();
                } else {
                    jQuery("#youtube-fields .ajax-display").html("Fail!").fadeOut();
                    jQuery("#youtube li.message-display").addClass("error").html(a.message).fadeIn(1000);
                }
            }
        });
    }

    if (source == "vimeo") {
        jQuery.ajax({
            url: btMediaCfg.siteURL + 'index.php',
            data: "option=com_bt_media&task=detail.getVideo&from=vimeo&method=getvideos&data=" + data,
            type: "post",
            beforeSend: function() {
                jQuery("#vimeo-fields .ajax-display").html("Loadding...").fadeIn();
            },
            success: function(response) {
                var a = jQuery.parseJSON(response);
                if (a.success) {
                    if (typeof(a.data) == "object" && (a.data instanceof Array)) {
                        jQuery("#i_vm_show_count").html(a.data.length + " items found").fadeIn(1000);
                        for (var i = 0; i < a.data.length; i++) {
                            jQuery("#jform_vimeo_from").append("<option value=" + i + ">" + (i + 1) + "</option>");
                        }
                        jQuery("#jform_vimeo_from").trigger('liszt:updated');
                        for (var i = 0; i < a.data.length; i++) {
                            if (i == a.data.length - 1) {
                                jQuery("#jform_vimeo_to").append("<option value=" + i + " selected=\"selected\">" + (i + 1) + "</option>");
                            } else {
                                jQuery("#jform_vimeo_to").append("<option value=" + i + ">" + (i + 1) + "</option>");
                            }
                        }
                        jQuery("#jform_vimeo_to").trigger('liszt:updated');
                        jQuery("#i_vm_from_to").fadeIn(1000);
                        jQuery("#btnGetVideosFromVimeo .custom-button").addClass("bt-enable").removeClass("bt-disable");
                    } else {
                        jQuery("#i_vm_show_count").html("").fadeOut(1000);
                        jQuery("#i_vm_from_to").fadeOut(1000);
                        jQuery("#jform_vimeo_from").html("<option value=0>1</option>");
                        jQuery("#jform_vimeo_to").html("<option value=0>1</option>");
                        jQuery("#btnGetVideosFromVimeo .custom-button").addClass("bt-enable").removeClass("bt-disable");
                    }
                    jQuery("#vimeo-fields .ajax-display").html("Success").fadeOut();
                } else {
                    jQuery("#vimeo-fields .ajax-display").html("Fail!").fadeOut();
                    jQuery("#vimeo li.message-display").addClass("error").html(a.message).fadeIn(1000);
                }
            }
        });
    }
    if (source == "flickr") {
        jQuery.ajax({
            url: btMediaCfg.siteURL + 'index.php',
            data: "option=com_bt_media&task=detail.getImage&from=flickr&act=getphotos&album=" + data,
            type: "post",
            beforeSend: function() {
                jQuery("#flickr li.message-display").removeClass("error").html("").fadeOut(1000, function() {
                    jQuery("#flickr li.message-display").addClass("ajax-display").html("Loading...").fadeIn("slow");
                });
            },
            success: function(data) {
                var a = jQuery.parseJSON(data);
                if (a.success) {
                    jQuery("#i_f_show_count").html(a.data.length + " items found").fadeIn(1000);
                    for (var i = 0; i < a.data.length; i++) {
                        jQuery("#jform_flickr_from").append("<option value=" + i + ">" + (i + 1) + "</option>");
                    }
                    jQuery("#jform_flickr_from").trigger('liszt:updated');
                    for (var i = 0; i < a.data.length; i++) {
                        if (i == a.data.length - 1) {
                            jQuery("#jform_flickr_to").append("<option value=" + i + " selected=\"selected\">" + (i + 1) + "</option>");
                        } else {
                            jQuery("#jform_flickr_to").append("<option value=" + i + ">" + (i + 1) + "</option>");
                        }
                    }
                    jQuery("#jform_flickr_to").trigger('liszt:updated');
                    jQuery("#i_f_from_to").fadeIn(1000);
                    jQuery("#btnGetImagesFromFlickr .custom-button").addClass("bt-enable").removeClass("bt-disable");
                    jQuery("#flickr li.message-display").removeClass("error ajax-display").html("").fadeOut(1000);
                } else {
                    jQuery("#flickr li.message-display").removeClass("ajax-display").html("").fadeOut(1000, function() {
                        jQuery("#flickr li.message-display").addClass("error").html(a.message).fadeIn("slow");
                    });
                }
            }
        });
    }
    if (source == "picasa") {
        jQuery.ajax({
            url: btMediaCfg.siteURL + 'index.php',
            data: "option=com_bt_media&task=detail.getImage&from=picasa&act=getphotos&username=" + data[0] + "&album=" + data[1],
            type: "post",
            success: function(data) {
                var a = jQuery.parseJSON(data);
                if (a.success) {
                    jQuery("#i_p_show_count").html(a.data.length + " items found").fadeIn(1000);
                    for (var i = 0; i < a.data.length; i++) {
                        jQuery("#jform_picasa_from").append("<option value=" + i + ">" + (i + 1) + "</option>");
                    }
                    jQuery("#jform_picasa_from").trigger('liszt:updated');
                    for (var i = 0; i < a.data.length; i++) {
                        if (i == a.data.length - 1) {
                            jQuery("#jform_picasa_to").append("<option value=" + i + " selected=\"selected\">" + (i + 1) + "</option>");
                        } else {
                            jQuery("#jform_picasa_to").append("<option value=" + i + ">" + (i + 1) + "</option>");
                        }
                    }
                    jQuery("#jform_picasa_to").trigger('liszt:updated');
                    jQuery("#i_p_from_to").fadeIn(1000);
                    jQuery("#btnGetImagesFromPicasa .custom-button").addClass("bt-enable").removeClass("bt-disable");
                    jQuery("#picasa li.message-display").removeClass("error ajax-display").html("").fadeOut(1000);
                } else {
                    jQuery("#picasa li.message-display").removeClass("ajax-display").html("").fadeOut(1000, function() {
                        jQuery("#picasa li.message-display").addClass("error").html(a.message).fadeIn("slow");
                    });
                }
            }
        });
    }
}

function getvideoFromURL(from, video_url) {
    btDisable(from);
    if (from == "youtube") {
        var video_data = encodeURIComponent(video_url);
        jQuery.ajax({
            url: btMediaCfg.siteURL + 'index.php',
            data: "option=com_bt_media&task=detail.getVideo&from=youtube&method=playlist_video&data=" + video_data,
            type: "post",
            beforeSend: function() {
                jQuery("#youtube-fields .ajax-display").html("Loadding...").fadeIn();
            },
            success: function(response) {
                var data = jQuery.parseJSON(response);
                if (data.success) {
                    jQuery("#jform_youtube_data").val(data.data);
                    comMediaGetItemCount(from, data.data);
                } else {
                    jQuery("#youtube-fields .ajax-display").html("Fail!").fadeOut();
                }
            }
        });
    }

    if (from == "vimeo") {
        jQuery.ajax({
            url: btMediaCfg.siteURL + 'index.php',
            data: "option=com_bt_media&task=detail.getVideo&from=vimeo&method=playlist_video&data=" + video_url,
            type: "post",
            beforeSend: function() {
                jQuery("#vimeo-fields .ajax-display").html("Loadding...").fadeIn();
            },
            success: function(response) {
                var data = jQuery.parseJSON(response);
                if (data.success) {
                    jQuery("#jform_vimeo_data").val(data.data);
                    comMediaGetItemCount(from, data.data);
                } else {
                    jQuery("#vimeo-fields .ajax-display").html("Fail").fadeOut();
                }
            }
        });
    }
}

function addFile(el) {
    jQuery(el).parent().append('<a href="#" class="button" onclick="return removeFile(this)">x</a>');
    jQuery(el).parent().parent().append('<li><label>Select file</label><input type="file" name="fileuploads[]"><a href="#" class="button" onclick="return addFile(this)">+</a></li>');
    jQuery(el).remove();
    return false;
}

function removeFile(el) {
    jQuery(el).parent().remove();
    return false;
}

