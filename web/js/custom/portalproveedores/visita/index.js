
let index = 0;

let dt_visita_column_index = {
    id: index++,
    multiselect: index++,
    cantidad: index++,
    fecha: index++,
    acciones: index
};

/**
 * initializes filter inputs and vars
 */
function initFiltro() {

    let today = new Date();
    // let yesterday = new Date();
    // yesterday.setDate(today.getDate() -1);

    let fDesde = $("#adif_portalproveedoresbundle_desde_hasta_form_fechaDesde");
    let fHasta = $("#adif_portalproveedoresbundle_desde_hasta_form_fechaHasta");

    fDesde.datepicker("setDate", today);
    fHasta.datepicker("setDate", today);
}

/**
 * @param {objetc|array} range dates
 * initializes datatable
 */
function initDataTable(jsonData) {

    if (validarRangoFechas(jsonData.fDesde, jsonData.fHasta)) {
        dt_visita = dt_datatable($('#table-visita'), {
            ajax: {
                url: __AJAX_PATH__ + 'visitas/index_table/',
                type: 'POST',
                data: jsonData,
            },
            columnDefs: [
                {
                    "targets": dt_visita_column_index.multiselect,
                    "data": "ch_multiselect",
                    "render": function (data, type, full, meta) {
                        return '<input type="checkbox" class="checkboxes" value="' + data + '" />';
                    }
                },
                {
                    "targets": dt_visita_column_index.acciones,
                    "data": "actions",
                    "render": function () {
                        return '';
                    }
                },
                {
                    className: "text-center",
                    targets: [
                        dt_visita_column_index.multiselect
                    ]
                },
                {
                    className: "ctn_acciones text-center nowrap",
                    targets: dt_visita_column_index.acciones
                }
            ]
        });
    }
}

/**
 * initializes filter button
 */
function initFiltroButton() {

    $('#adif_portalproveedoresbundle_desde_hasta_form_submit').on('click', function (e) {

        e.preventDefault();
        let data = {
            fDesde : $("#adif_portalproveedoresbundle_desde_hasta_form_fechaDesde").val().trim(),
            fHasta : $("#adif_portalproveedoresbundle_desde_hasta_form_fechaHasta").val().trim(),
            chkStrictBtn : $("#adif_portalproveedoresbundle_desde_hasta_form_chkStrict").closest('.has-switch').hasClass('switch-on')
        };

        if (validarRangoFechas(data.fDesde, data.fHasta)) {
            $(".tooltips, .dt_export_ctn").remove();
            dt_visita.fnDestroy();
            dt_visita.fnAddData();
            initDataTable(data);

            if(data.fDesde === data.fHasta)
                initChartByDay(data);
            else
                initChartByRangeDate(data);
        }
    });
    $('#adif_portalproveedoresbundle_desde_hasta_form_submit').click();
}

/**
 * @param {objetc|array} range dates
 * initializes the chart by range date
 */

function initChartByRangeDate(jsonData){
    $.ajax({
        url: __AJAX_PATH__ + 'visitas/index_table/',
        type: 'POST',
        data: jsonData
    }).done(function(data) {

        var json = jQuery.parseJSON( data );
        var fechasVisitas = [];

        $.each(json.data, function(index, value){
            let fechaSplit = value[2].split('/');
            fechasVisitas.push([Date.UTC(fechaSplit[2], fechaSplit[1]-1, fechaSplit[0]),parseInt(value[3])]);
        });

        id = "grafic-container";
        title = "Visitas por Día";
        subtitle = 'Rango seleccionado: '+jsonData.fDesde+' - '+jsonData.fHasta;
        lineTitle = "Nro. Visitas";
        xTitle =  "Fechas";

        createChartByRangeDate(id, title, subtitle, lineTitle, xTitle, fechasVisitas);
    });
}

/**
 * @param {objetc|array} day
 *
 * initializes the chart by hour per day
 */
function initChartByDay(jsonData){

    $.ajax({
        url: __AJAX_PATH__ + 'visitas/index_chart/',
        type: 'POST',
        data: jsonData
    }).done(function(data) {

        var json = jQuery.parseJSON( data );
        var horasVisitas = [];

        $.each(json.data, function(index, value){
            let fechaSplit = value[2].split('/');
            let horaSplit = value[4].split(':');
            horasVisitas.push([Date.UTC(fechaSplit[2], fechaSplit[1]-1, fechaSplit[0],horaSplit[0]),parseInt(value[3])]);
        });
        id       = "grafic-container";
        title = "Visitas por Horas";
        subtitle = 'Rango seleccionado: '+jsonData.fDesde+' - '+jsonData.fHasta;
        lineTitle= "Nro. Visitas";
        xTitle =  "Fechas";

        createChartByDay(id, title, subtitle, lineTitle, xTitle, horasVisitas);
    });
}

/**
 * @param {object|array}  rangeDate
 * choose which graph to initialize based on the selected range date
 */
function chartChooser(rangeDate){

    let desdeMillisecs = Date.parse(dateFormatConverter(rangeDate.fDesde));
    let hastaMillisecs = Date.parse(dateFormatConverter(rangeDate.fHasta));
    let unDiaMillisecs = 86400000; //1 dia

    if((hastaMillisecs - desdeMillisecs) > unDiaMillisecs)
        return true;

    return false;
}

/**
 * @param {string} date
 * @return {string} date
 */
function dateFormatConverter(date) {
    dateSplit = date.split('/');
    return dateSplit[2]+'/'+dateSplit[1]+'/'+dateSplit[0];
}

$(document).ready(function () {

    initFiltro();

    let rangeDates = {
        fDesde : $("#adif_portalproveedoresbundle_desde_hasta_form_fechaDesde").val().trim(),
        fHasta : $("#adif_portalproveedoresbundle_desde_hasta_form_fechaHasta").val().trim()
    };


    initDataTable(rangeDates);

    initFiltroButton();

    // $("#chartButton").click(function(e) {
    //     e.preventDefault();
    //     $("#grafic-container").removeClass("hidden");
    //     $('html, body').animate({
    //         scrollTop: $("#grafic-container").offset().top
    //     }, 2000);
    // });
});

