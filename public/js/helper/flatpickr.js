

$(".flatpickr-date").flatpickr({
    altInput: true,
    altFormat: "F j, Y",
    dateFormat: "Y-m-d",
});


$(".flatpickr-daterange").flatpickr({
    altInput: true,
    altFormat: "F j, Y",
    dateFormat: "Y-m-d",
    mode: "range"
});

$(".flatpickr-datetime").flatpickr({
    altInput: true,
    altFormat: "F j, Y H:i",
    dateFormat: "Y-m-d H:i:s",
    enableTime: true,
})
