import { datatableHandleFetchData } from "/js/helper/datatable.js";

$(function(){
  const table = $('#datatable');

  function loadTable(){
    const month = $('#month-filter').val();
    const year  = $('#year-filter').val();
    const params = new URLSearchParams({ month, year }).toString();

    datatableHandleFetchData({
      html: table,
      url: window.REKAP_ABSEN_DATA_URL + '?' + params,
      column: [
        { title:"No",       data:'DT_RowIndex',    orderable:false, searchable:false },
        { title:"Karyawan", data:'nama_karyawan'                   },
        { title:"WFO",      data:'WFO'                            },
        { title:"WFH",      data:'WFH'                            },
      ],
      searching: false
    }, false, false);
  }

  $('#month-filter, #year-filter').on('change', loadTable);

  loadTable(); // initial load
});
