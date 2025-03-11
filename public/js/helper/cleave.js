
function timeFormatCleave(html) {
    return new Cleave(html, {
        time: true,
        timePattern: ['h', 'm']
    });
}

function percentageFormatCleave(html) {
    return new Cleave(html, {
        numeral: true,
    });
}

function nominalFormatCleave(html) {
    return new Cleave(html, {
        numeral: true,
        numeralThousandsGroupStyle: 'thousand'
    });
}



