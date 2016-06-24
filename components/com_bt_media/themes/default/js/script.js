function  videoPause(el, view) {
    var id = $BM(el).attr('id');
    if ($BM('#' + view + '-video-js-' + id).size() > 0) {
        _V_(view + '-video-js-' + id).pause();
    }
}

function deleteItem(item_id) {
    if (confirm(Joomla.JText._('COM_BT_MEDIA_DELETE_MESSAGE'))) {
        document.getElementById('bt-media-delete-' + item_id).submit();
    }
}


$BM(document).ready(function() {
    $BM('.items_list div.item').hover(function() {
        $BM(this).stop(true, true).animate({opacity: 0.5}, 400);
    }, function() {
        $BM(this).stop(true, true).animate({opacity: 1}, 400);
    });
});

