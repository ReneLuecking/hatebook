jQuery(document).ready(function () {
    let sidebar = jQuery('.sidebar');
    let conn = new WebSocket('ws://' + window.location.hostname + ':8080/?id=' + jQuery('#chats').data('sender'));

    conn.onmessage = function(msg){
        let chat = null;
        msg = JSON.parse(msg.data);

        switch (msg.action) {
            case 'sent':
                chat = jQuery('#single-chat-' + msg.to);
                chat.find('.messages').append(
                    '<div class="clearfix">' +
                    '<p id="message-' + msg.id + '" data-datetime="' + msg.datetime.date + '" class="message right">' + msg.text + '</p>' +
                    '</div>'
                );
                chat.scrollTop(chat[0].scrollHeight);
                break;
            case 'new':
                chat = jQuery('#single-chat-' + msg.from);
                if (chat.length){
                    chat.find('.messages').append(
                        '<div class="clearfix">' +
                        '<p id="message-' + msg.id + '" data-datetime="' + msg.datetime.date + '" class="message left">' + msg.text + '</p>' +
                        '</div>'
                    );

                    if (!chat.hasClass('d-none')){
                        chat.scrollTop(chat[0].scrollHeight);
                    }
                }
                jQuery('#chat-enemy-' + msg.from).children('svg').removeClass('d-none');
                break;
        }
    };

    jQuery('.open-chat').click(function (e) {
        e.preventDefault();

        jQuery(this).siblings('svg').addClass('d-none');

        let chats = jQuery('#chats');
        let sender = chats.data('sender');
        let recipient = jQuery(this).data('id');
        let chat = jQuery('#single-chat-' + recipient);

        jQuery('.single-chat:not(.d-none)').addClass('d-none');

        if (chat.length) {
            chat.removeClass('d-none');
            chat.scrollTop(chat[0].scrollHeight);
            return;
        }

        chat = jQuery('<div id="single-chat-' + recipient + '" class="single-chat">' +
            '<div class="messages"></div>' +
            '<form class="new-message">' +
            '<input name="new-message" placeholder="Verfasse eine Nachricht">' +
            '</form>' +
            '</div>');

        chat.data('recipient', recipient);

        chats.parent().append(chat);

        jQuery.ajax({
            method: 'POST',
            url: '/chat/getmessages',
            data: {
                senderId: sender,
                recipientId: recipient
            },
            success: function (res) {
                let messages = JSON.parse(res);
                let messageContainer = chat.find('.messages');

                messages.forEach(function (el) {
                    let align = el.sender.id === sender ? 'right' : 'left';
                    messageContainer.prepend(
                        '<div class="clearfix"><p id="message-' + el.id + '" data-datetime="' + el.datetime.date + '" class="message ' + align + '">' + el.text + '</p></div>'
                    );
                });

                chat.scrollTop(chat[0].scrollHeight);
            }
        });

        if (!chats.hasClass('h-50')) {
            chats.addClass('h-50');
        }
    });

    sidebar.on('submit', '.new-message', function(e){
        e.preventDefault();

        let chat = jQuery(this).parent();
        let sender = jQuery('#chats').data('sender');
        let recipient = chat.data('recipient');
        let input = chat.find('input[name="new-message"]');
        let text = input.val();

        if (text === '') {
            return;
        }

        let message = {
            action: 'send',
            to: recipient,
            text: text
        };

        conn.send(JSON.stringify(message));

        input.val('');
    });

    sidebar.on('scroll', '.single-chat', function () {
        let chat = jQuery(this);
        if (chat.scrollTop() === 0) {
            let sender = jQuery('#chats').data('sender');
            let recipient = chat.data('recipient');
            let messageContainer = chat.find('.messages');
            let first = messageContainer.find('div:first-child');

            jQuery.ajax({
                method: 'POST',
                url: '/chat/getmessages',
                data: {
                    senderId: sender,
                    recipientId: recipient,
                    offset: messageContainer.find('> div').length
                },
                success: function (res) {
                    let messages = JSON.parse(res);

                    messages.forEach(function (el) {
                        let align = el.sender.id === sender ? 'right' : 'left';
                        messageContainer.prepend(
                            '<div class="clearfix"><p id="message-' + el.id + '" data-datetime="' + el.datetime.date + '" class="message ' + align + '">' + el.text + '</p></div>'
                        );
                    });

                    first[0].scrollIntoView();
                }
            });


        }
    });

    sidebar.on('focus', '.single-chat', function () {
        let recipient = jQuery(this).data('recipient');
        jQuery('#chat-enemy-' + recipient).children('svg').addClass('d-none');
    });
});
