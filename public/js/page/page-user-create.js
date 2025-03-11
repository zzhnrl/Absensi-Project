const preview_image = $('#preview-image')
const upload_image = $('#upload-image')
const preview_images = $('#preview-images')
const upload_images = $('#upload-images')

upload_image.on('change', function (e) {
    readURL(this)
});

upload_images.on('change', function (e) {
    readURL1(this)
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

function readURL1(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            preview_images.attr('src', e.target.result);
        }

        reader.readAsDataURL(input.files[0]);
    }
}