$(document).ready( function(){
    post_handler.init();
});

post_handler = {

    post_offset : 0,

    init_done: false,

    search_query: '',

    init : function()
    {
        let query = window.location.search.substring(1);
        let vars = query.split('&');

        for (let i = 0; i < vars.length; i++) {
            let pair = vars[i].split("=");
            if (pair[0] === 's') {
                this.search_query = '&' + vars[i];
            }
        }

        if ( !this.init_done )
        {
            this.next_posts();
            this.init_events();
        }

        this.init_done = true;
    },

    next_posts: function()
    {
        $.ajax({
            url: "http://" + HTTP_ROOT + "/next_post_ids?offset=" + post_handler.post_offset + this.search_query,
            success : function( result ){
            //    post_handler.post_offset += 5;
                post_handler.next_post_ids = JSON.parse(result);

                if (result.length === 0) {
                    $('.posts > p.d-none').removeClass('d-none');
                } else {
                    post_handler.load_post();
                }
            },
        });

    },

    load_post: function()
    {
        var post_id = this.next_post_ids.shift();
        if ( post_id == undefined )
        {
            return;
        }
        $.ajax({
            url: "http://" + HTTP_ROOT + "/post/" + post_id,
            success : function( result ){
                $('.posts').append( result );
                post_handler.load_comments( post_id );
            },
        });
    },

    load_comments : function( post_id )
    {
        $.ajax({
            url: "http://" + HTTP_ROOT + "/comments?post_id=" + post_id,
            success : function( result ){
                result = JSON.parse(result);
                $.each( result, function( i, value )
                {
                    $( '.post[data-post-id="' + post_id + '"]' ).find( '.comments' ).append( value );
                });
                post_handler.load_post();
            },
        });
    },

    toggle_post_hate : function( post_id )
    {
        var hated = $( '.post[data-post-id="' + post_id + '"]' ).find( '.bt_hate' ).attr( 'data-hated' );

        if ( hated == "true" )
        {
            $.ajax({
                url: "http://" + HTTP_ROOT + "/delete_post_hate?post_id=" + post_id,
                success: function()
                {
                    var post = $( '.post[data-post-id="' + post_id + '"]' );
                    post.find( '.bt_hate' ).attr( 'data-hated', "false" );
                    post.find( '.bt_hate' ).text( "🖕 Hasse ich" );
                    var hates = post.find( '.hates:first' ).text();
                    hates--;
                    post.find( '.hates:first' ).text( hates );
                }
            });
        }
        else
        {
            $.ajax({
                url: "http://" + HTTP_ROOT + "/create_post_hate?post_id=" + post_id,
                success: function()
                {
                    var post = $( '.post[data-post-id="' + post_id + '"]' );
                    post.find( '.bt_hate' ).attr( 'data-hated', "true" );
                    post.find( '.bt_hate' ).text( "🖕 Hasse ich nicht mehr" );
                    var hates = post.find( '.hates:first' ).text();
                    hates++;
                    post.find( '.hates:first' ).text( hates );

                }
            });
        }
    },

    reload_comments: function( post_id )
    {
        $.ajax({
            url: "http://" + HTTP_ROOT + "/comments?post_id=" + post_id,
            success : function( result ){
                result = JSON.parse(result);
                $( '.post[data-post-id="' + post_id + '"]' ).find( '.comment' ).remove();
                $.each( result, function( i, value )
                {
                    $( '.post[data-post-id="' + post_id + '"]' ).find( '.comments' ).append( value );
                });
            },
        });
    },

    toggle_comment_hate : function( comment_id )
    {

        var hated = $( '.comment[data-comment-id="' + comment_id + '"]' ).find( '.bt_comment_hate' ).attr( 'data-hated' );
        if ( hated == "true" )
        {
            $.ajax({
                url: "http://" + HTTP_ROOT + "/delete_comment_hate?comment_id=" + comment_id,
                success: function()
                {
                    var comment = $( '.comment[data-comment-id="' + comment_id + '"]' );
                    comment.find( '.bt_comment_hate' ).attr( 'data-hated', "false" );
                    comment.find( '.bt_comment_hate' ).text( "🖕 Hasse ich" );
                    var hates = comment.find( '.hates' ).text();
                    hates--;
                    comment.find( '.hates' ).text( hates );
                }
            });
        }
        else
        {
            $.ajax({
                url: "http://" + HTTP_ROOT + "/create_comment_hate?comment_id=" + comment_id,
                success: function()
                {
                    var comment = $( '.comment[data-comment-id="' + comment_id + '"]' );
                    comment.find( '.bt_comment_hate' ).attr( 'data-hated', "true" );
                    comment.find( '.bt_comment_hate' ).text( "🖕 Hasse ich nicht mehr" );
                    var hates = comment.find( '.hates' ).text();
                    hates++;
                    comment.find( '.hates' ).text( hates );

                }
            });
        }
    },

    create_post : function()
    {
        var text = $( '.new_post textarea' ).val();
        if ( !text.match( /^\s*$/ ) )
        {
            $.ajax({
                url: "http://" + HTTP_ROOT + "/create_post?text=" + text,
                success: function()
                {
                    // TODO Den neuen Post über Ajax nachladen
                    location.reload();
                }
            });
        }
    },

    create_comment : function( post_id, text )
    {
        if ( !text.match( /^\s*$/ ) )
        {
            $.ajax({
                url: "http://" + HTTP_ROOT + "/create_comment?text=" + text + "&post_id=" + post_id,
                success: function()
                {
                    post_handler.reload_comments( post_id );
                }
            });
        }
    },

    init_events : function()
    {
        $( '.posts' ).on( 'click',  '.bt_comment', function( event ){
            $( event.target ).closest( '.post' ).find( '.new_comment' ).focus();
        });

        $( '.posts' ).on( 'click', '.bt_hate', function( event ){
            var post_id = $( event.target ).closest( '.post' ).data( 'post-id' );
            post_handler.toggle_post_hate( post_id );
        });

        $( document ).on( 'click', '.bt_comment_hate', function( event ){
            var comment_id = $( event.target ).closest( '.comment' ).data( 'comment-id' );
            post_handler.toggle_comment_hate( comment_id );
        });

        $( '.new_post' ).find( 'button' ).on( 'click', function(){
            post_handler.create_post();
        });

        $( '.posts' ).on( "keypress", '.new_comment', function( event ){
            if ( event.which == 13 ) {
               var post_id =  $( event.target ).closest( '.post' ).attr( 'data-post-id' );
               var text = $( event.target ).val();
                $( event.target ).val("");
             post_handler.create_comment( post_id, text );
            }
        });

        $(window).scroll(function() {

            if ( $(window).scrollTop()  + $(window).height() + 5 >=  $(document).height() )
            {
         //       post_handler.next_posts();
            }
        });

    },

};



