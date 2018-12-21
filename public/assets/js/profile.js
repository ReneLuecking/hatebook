jQuery(document).ready(function () {

    jQuery('.edit-profile').click(function (e) {
        e.preventDefault();

        jQuery(this).parent().addClass('edit');
    });

    jQuery('.cancel-profile').click(function (e) {
        e.preventDefault();

        jQuery(this).parent().removeClass('edit');

        let input = jQuery(this).siblings('input');
        input.val(input.data('value'));
    });

    jQuery('.save-profile').click(function (e) {
        e.preventDefault();

        let input = jQuery(this).siblings('input');
        let td = jQuery(this).parent();
        let value = input.val();

        jQuery.ajax({
            method: 'POST',
            url: '/profile/save',
            data: {
                userId: jQuery(this).closest('.user').data('id'),
                field: input.prop('name'),
                value: value
            },
            success: function () {
                if (input.prop('type') === 'date') {
                    let d = new Date(value);
                    value = d.toLocaleDateString();
                }

                input.data('value', value);
                input.siblings('span').text(value);

                td.removeClass('edit');
            }
        });
    });

    jQuery('.edit-picture').click(function (e) {
        e.preventDefault();

        jQuery('input[name="profile-picture"]').click();
    });

    jQuery('input[name="profile-picture"]').change(function(){
        let formData = new FormData(document.getElementById('profile-picture-form'));

        jQuery.ajax({
            method: 'POST',
            url: '/profile/save',
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                jQuery('#profile-picture').find('> img').prop('src', res);
            }
        });
    });

    jQuery( '#toggle_enemy').click(function(){
        var url = $(this).attr('data-url');
        jQuery.ajax({
            method: 'POST',
            url: url,
            success: function () {
                location.reload()
            }
        });
    });

    if ( $( '.enemy_requests').length )
    {
        jQuery.ajax({
            url: '/profile/enemy_requests',
            success: function ( html ) {
                if ( html.length > 0 ) {
                    $( '.enemy_requests' ).append( html );
                    $( '.enemy_requests' ).removeClass('d-none');
                }
            }
        });

        $( '.enemy_requests' ).on( 'click', '.enemy_button',  function(){
            var enemy = jQuery(this).parent();
            var url = $(this).data('url');
            var action = $(this).data('action');
            jQuery.ajax({
                url: url,
                success: function () {
                    if ( action === 'accept'){
                        enemy.appendTo('.enemies');
                        enemy.find('btn-success').remove();
                        jQuery('.enemies').removeClass('d-none');
                    }else{
                        enemy.remove();
                    }
                }
            });
        })
    }

    if ($('.enemies').length) {
        jQuery.ajax({
            url: '/profile/enemies',
            success: function (html) {
                if (html.length > 0) {
                    $('.enemies').append(html);
                    $('.enemies').removeClass('d-none');
                    if (!SELF) {
                        $('.enemies .enemy_button').remove();
                    }
                }
            }
        });

        $('.enemies').on('click', '.enemy_button', function () {
            var enemy = jQuery(this).parent();
            var url = $(this).data('url');
            jQuery.ajax({
                url: url,
                success: function () {
                    enemy.remove();
                }
            });
        });
    }
});


