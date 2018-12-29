
$(document).ready(function () {

    var $targets = $(
            'h1, h2, h3, h4, table, th, p, label, legend, input, select, \n\
            .body-container a,  .body-container span, .body-container button, \n\
            .caption, .rezisable'
            );

    $targets.jfontsize({
        btnMinusMaxHits: 5, // How many times the size can be decreased
        btnPlusMaxHits: 5, // How many times the size can be increased
        sizeChange: 1 // Defines the range of change in pixels
    });

});