import {datatableHandleFetchData} from "/js/helper/datatable.js"
import {Ajax} from "/js/helper/ajax-json.js"
$(function () {
    'use strict';
    //init variable
    const datatable = $('#datatable')

    fetchData()

    function fetchData () {
        datatableHandleFetchData({
            html: datatable,
            url: '/notifikasi/grid',
            column : [
                { title: "Judul", data: 'judul' },
                { title: "Teks", data: 'teks' },
                ]
        })
    }

    datatable.on( 'click', 'tbody .mark-as-read', function (e) {
        Ajax.put({
            url : '/notifikasi/read/' + this.value ,
            data : {},
        })
        fetchData ()
    })


})
