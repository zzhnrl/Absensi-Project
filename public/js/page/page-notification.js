import {datatableHandleEvent} from "/js/helper/datatable.js"
$(function () {
    'use strict';
    //init variable
    const notification_count = $('#notification-count')
    const notification_item = $('#notification-item')
    const user_notification_id = $('#user-notification-id')
    const notification_button = $('#notification-button')


    var notification_text_template = ''
    notification_text_template += '<li>'
    notification_text_template +=   '<div class="row p-1 bg-light">'
    notification_text_template +=       '<div class="col-12 box p-none ">'
    notification_text_template +=           '<div class="float-right">'
    notification_text_template +=               '<i class="fas fa-star"></i>'
    notification_text_template +=           '</div>'
    notification_text_template +=           '<div class="float-left">'
    notification_text_template +=               '<b><a href="[url]" >[title]</a> </b><br>'
    notification_text_template +=               '<b><small>[notification_time]</small></b>'
    notification_text_template +=               '<p style="font-size: 12px">[text]</p>'
    notification_text_template +=           '</div>'
    notification_text_template +=           '<div class="float-right">'
    notification_text_template +=               '<br>'
    notification_text_template +=               '<button type="button" id="mark-as-read-[uuid]" class="btn btn-xs btn-default" value="[uuid]">mark as read</button>'
    notification_text_template +=           '</div>'
    notification_text_template +=       '</div>'
    notification_text_template +=   '</div>'
    notification_text_template += '<hr>'
    notification_text_template += '</li>'

    setTimeout(function () {
        getListNotification()
    },2000)


    var pusher = new Pusher("f361600fab8aa30ba750",
    {
        cluster: "ap1",
    })
    var channel = pusher.subscribe('notification-channel');
    channel.bind('get-notification-'+user_notification_id.val(), function(data) {
        notification_button.addClass('bg-white')
        getListNotification ()
        setTimeout(() => {
            notification_button.removeClass('bg-white')
        }, 1000);
    });

    function getListNotification () {
        fetch('/notifikasi/list', {
            headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
            },
            method: 'get'
        }).then((response)  => {
            return response.json()

        }).then((data)  => {
            notification_item.empty()
            notification_count.text(data.data.count)
            for (let item of data.data.notification) {
                let notification_text = notification_text_template.replace('[title]',item.judul )
                notification_text = notification_text.replace('[notification_time]',timeDifference(new Date(item.waktu_notifikasi*1000)))
                notification_text = notification_text.replace('[text]',item.teks)
                notification_text = notification_text.replace('[uuid]',item.uuid)
                notification_text = notification_text.replace('[uuid]',item.uuid)
                notification_text = notification_text.replace('[url]',item.url)
                notification_item.append(notification_text)

                datatableHandleEvent({
                    html:  $('#mark-as-read-'+item.uuid),
                    onEvent: 'click',
                    triggerEvent: function (e) {
                        notification_button.addClass('bg-white')
                        $ajaxJsonUpdate({
                            url : '/notifikasi/read/' + item.uuid ,
                            data : {},
                        })
                        $('#mark-as-read-'+item.uuid).parent('div').parent('div').parent('div').parent('li').remove()
                        notification_count.text(notification_count.text()-1)
                        setTimeout(() => {

                            notification_button.removeClass('bg-white')
                        }, 1000);
                        return true;
                    }
                })
            }

        })
        .catch(err => console.log(err))
    }
})
