
$(document).ready( function(){
    // content always fits to header
    $('#content').css( 'margin-top', $( '.navbar' ).outerHeight() );
    $( window ).resize( function(){
        $('#content').css( 'margin-top', $( '.navbar' ).outerHeight() );
    });
});
