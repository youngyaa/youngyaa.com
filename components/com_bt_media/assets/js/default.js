/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$BM = jQuery.noConflict();


function rate(mediaId, rating) {
    jQuery.ajax({
        url: btMediaCfg.siteURL + 'index.php',
        data: "option=com_bt_media&format=raw&task=detail.rate&id=" + mediaId + '&rating=' + rating,
        type: "post",
        success: function(responseJSON) {
            var a = jQuery.parseJSON(responseJSON);
            if (a.success) {
                $BM('.btp-rating-container-' + mediaId).each(function() {
                    $BM(this).find('.btp-rating-current').css({
                        width: a.rating_width+"px"
                    });
                    $BM('.btp-rating-container-' + mediaId + ' .btp-rating-notice').text(a.rating_text);
                });
            }
            else {
                alert(a.message);
            }
        }, error: function() {
            alert('Unknow Error!!!');
        }
    });
}

$BM(document).ready(function() {
    $BM(window).resize(function() {
        $BM('.image-wrap').css({'width': 'auto', 'height': 'auto'});
    });
});

