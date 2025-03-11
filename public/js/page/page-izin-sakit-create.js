const preview_image = $('#preview-image')
const upload_image = $('#upload-image')

upload_image.on('change', function (e) {
    readURL(this)
});

function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            preview_image.attr('src', e.target.result);
        }

        reader.readAsDataURL(input.files[0]);
    }
}
