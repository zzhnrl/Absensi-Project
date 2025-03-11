

function previewImage ({html_upload_image, html_preview_image}) {
    html_upload_image.on('change', function (e) {
        readURL(this, html_preview_image)
    });
}


function readURL({input, preview_image}) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            preview_image.attr('src', e.target.result);
        }

        reader.readAsDataURL(input.files[0]);
    }
}

