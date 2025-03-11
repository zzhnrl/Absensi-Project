
const select = $('select')
select.select2();

function $select2ListData ({html, rowDataFormat, fetchData}) {
    html.empty()
    $listDatafetch(fetchData).then((res) => {
        var data = []
        data[0] = {
            id: '',
            text: '-- Choose Data --'
        }

        res.data.forEach((dt,index) => {
            data[index+1] = rowDataFormat(dt)
        });
        html.each((_i, e) => {
            var $e = $(e);
            $e.select2({
                tags: false,
                dropdownParent: $e.parent(),
                data: data,
            });
        })
    }).catch((err) => {
        console.log(err)
    })

}

// $ajaxSelect2ListData({html: site_category_filter, url : '/site-category/get'},
// function (dt) {
//     return {
//         text: dt.name,
//         id: dt.uuid
//     }
// })

function $ajaxSelect2ListData ({html, url, minimumInputLength = 2}, returnDataCallback) {
    html.select2({
        minimumInputLength: minimumInputLength,
        tags: false,
        ajax: {
            url: url,
            dataType: 'json',
            type: "GET",
            quietMillis: 1000,
            data: function (search_param) {
                return {
                    search_param: search_param
                };
            },
            processResults: function (data) {
                return {
                    results: $.map(data.data, returnDataCallback)
                };
            }
        }
    });
}


