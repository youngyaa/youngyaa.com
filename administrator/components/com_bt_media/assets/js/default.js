/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$B = $BM = jQuery.noConflict();

BTM = new Class({
    initialize: function(liveSite) {
        this.liveSite = liveSite;
    }
});
BTM.Showcase = new Class({
    initialize: function(liveSite) {
        this.liveSite = liveSite;
    },
    rate: function(mediaId, rating) {
        var jsonRequest = new Request.JSON({
            url: this.liveSite + 'index.php?option=com_bt_media&format=raw&task=detail.rate&id=' + mediaId + '&rating=' + rating,
            onSuccess: function(responseJSON, responseText) {
                if (responseJSON.success) {
                    $$('.btp-rating-container-' + mediaId).each(function(el) {
                        new Fx.Morph(el.getElement('.btp-rating-current'), {
                            duration: 'long',
                            transition: Fx.Transitions.Sine.easeOut
                        }).start({
                            width: responseJSON.rating_width + 'px'
                        });
                        $$('.btp-rating-container-' + mediaId + ' .btp-rating-notice').set('text', responseJSON.rating_text);
                    });
                }
                else {
                    alert(responseJSON.message);
                }
            },
            onFailure: function(xhr) {
                alert('Unknow Error!!!');
            }
        }).get();
    }
});

