// public/js/page/page-history-point.js
import { datatableHandleFetchData } from "/js/helper/datatable.js";

$(function () {
    const datatable = $('#datatable');

    datatableHandleFetchData({
        html: datatable,
        url: window.HISTORY_POINT_GRID_URL,
        column: [
            { title: "No",             data: 'DT_RowIndex',    orderable: false, searchable: false },
            { title: "Perubahan Poin",  data: 'perubahan_point' },
            { title: "Total Poin",      data: 'jumlah_point' },
            { title: "Tanggal",         data: 'tanggal'        },
        ],
        searching: false
    }, false, false);
});
