function  videoPause(el, view) {
    var id = $BM(el).attr('id');
    if ($BM('#' + view + '-video-js-' + id).size() > 0) {
        _V_(view + '-video-js-' + id).pause();
    }
}

$BM(document).ready(function() {
    prepareItem();
    imagesLoaded($BM('#bt-media-container').find('img'), function() {
        prepareItem();
    });
    $BM(window).resize(function() {
        prepareItem();
    });
});


function prepareItem() {
    var container = $BM('#bt-media-container');
    var document_w = $BM('#bt-media-container').width();
    var padding = $BM('.image-information .items_list div.item').outerWidth(true) - $BM('.image-information .items_list div.item').width();
    var ratio = Math.floor(document_w / (btMediaCfg.thumbWidth * 0.85 + padding));
    var el_width = Math.floor((document_w - padding * ratio) / ratio);

    $BM('.item', container).css({'width': el_width});
    var height = $BM('div.img-thumb .image:first', container).height();
    if ($BM('.cat-item .item-image', container).length > 0) {
        if (height > 0) {
            $BM('.cat-item .item-image', container).css({'height': height});
        } else {
            $BM('.cat-item .item-image', container).css({'height': 200});
        }
    }
    container.masonry({
        itemSelector: '.item'
    });
}

function loadMoreItem() {
    if (scrollPages > 1) {
        var dataurl = 'limitstart=' + scrollStart + '&tmpl=component';
        if (filterBar) {
            dataurl += '&filter_search=' + keySearch + '&filter_type=' + filter_type + '&filter_ordering=' + filter_ordering;
        }
        if (!scrollLoad) {
            $BM.ajax({
                url: location.href,
                data: dataurl,
                type: "GET",
                beforeSend: function() {
                    $BM('.load-more-item').hide();
                    $BM('.ajax-load-more').show();
                },
                success: function(response) {
                    var container = $BM('#bt-media-container');
                    var doc = $BM(response);
                    var listMedia = doc.find('.media-item');
                    //var listMediaImg = listMedia.find('img');
                    var count = 0;
                    listMedia.each(function() {
                        var el = this;
                        imagesLoaded($BM(el).find('img'), function() {
                            count++;
                            container.append(el).masonry('appended', el);
                            if (count === listMedia.length) {
                                if (typeof FB !== "undefined") {
                                    FB.XFBML.parse(document.getElementById("#bt-media-container"));
                                }
                                $BM('.ajax-load-more').hide();
                                if (scrollPages > 1) {
                                    $BM('.load-more-item').show();
                                }
                                setTimeout(function() {
                                    scrollLoad = false;
                                }, 200);
                            }
                        });
                        prepareItem();
                    });
                }
            });
            scrollStart = scrollStart + scrollLimit;
            scrollPages--;
            scrollLoad = true;
        }
    }
}
function imagesLoaded(elem, callback) {
    if (elem.length > 0) {
        var loaded = 0;
        elem.each(function() {
            var img = new Image();
            $BM(img).load(function() {
                loaded++;
                if (loaded === elem.length) {
                    callback.call();
                }
            }).error(function() {
                loaded++;
                if (loaded === elem.length) {
                    callback.call();
                }
            }).attr('src', $BM(this).attr('src'));
        });
    } else {
        callback.call();
    }
}

function deleteItem(item_id) {
    if (confirm(Joomla.JText._('COM_BT_MEDIA_DELETE_MESSAGE'))) {
        document.getElementById('bt-media-delete-' + item_id).submit();
    }
}