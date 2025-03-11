
function displayInputErrorMessage (errors, param_name)
{

    $('#'+param_name+'-error').remove()
    $('#'+param_name).removeClass('is-invalid')

    if ( errors.message && errors.message[param_name] != null ) {
        $('#'+param_name).addClass('is-invalid')

        $( '#'+param_name ).after(function() {
            return '<span id="'+param_name+'-error" class="text-danger">'
            +errors.message[param_name]+
            '</span>';
        });

    }
}

function validateForm (errors, document_ids) {
    for (let index in document_ids) {
        displayInputErrorMessage (errors, document_ids[index])
    }
}

$('#submit-data').on('click', function (e) {
    e.preventDefault()
    $(this).text("");
    $(this).append('<i class="fas fa-circle-notch fa-spin"></i> Loading...')
    $(this).attr('disabled', 'disabled');
    setTimeout(function () {
        $('form').submit()
    }, 500)
});
