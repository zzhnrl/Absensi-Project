function $ajaxStore ({
    url,
    data,
    callBack = null,
    errorCallBack = null,
}) {
    $.ajax({
        headers: {
            'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
        },
        url: url,
        data: data,
        dataType:'json',
        async:true,
        type:'post',
        success: callBack,
        statusCode: {
            500: function(data) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: data.responseJSON.message,
                })
            },
            400: function(data) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: data.responseJSON.message,
                })
            },
            401: function() {
                location.reload();
            },
            422: errorCallBack,
            419: function() {
                location.reload();
            },
            403: function(){
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Access Denied!',
                })
            }
        },
    });
}

function $ajaxJsonGet ({
    url,
    data,
    callBack = null,
    errorCallBack = null,
}) {
    $.ajax({
        headers: {
            'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
        },
        url: url,
        data: data,
        dataType:'json',
        async:true,
        type:'get',
        success: callBack,
        statusCode: {
            500: function(data) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: data.responseJSON.message,
                })
            },
            400: function(data) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: data.responseJSON.message,
                })
            },
            401: function() {
                location.reload();
            },
            422: errorCallBack,
            419: function() {
                location.reload();
            },
            403: function(){
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Access Denied!',
                })
            }
        },
    });

}

function $ajaxJsonUpdate ({
    url,
    data,
    callback = null
}) {
    $.ajax({
        headers: {
            'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
        },
        url: url,
        data: JSON.stringify(data),
        dataType:'json',
        async:true,
        type:'put',
        processData: false,
        contentType: false,
        success: callback,
        statusCode: {
            500: function(data) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: data.responseJSON.errors,
                })
            },
            400: function(data) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: data.responseJSON.errors,
                })
            },
            401: function() {
                location.reload();
            },
            419: function() {
                location.reload();
            },
            403: function(){
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Access Denied!',
                })
            }
        },
    });
}



function ajaxJsonDelete () {

}
