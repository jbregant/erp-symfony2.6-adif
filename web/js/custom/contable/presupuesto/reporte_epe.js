
/**
 * 
 * @returns {undefined}
 */
function initReporteTitle() {

    $('.reporte_title').hide();
}

/**
 * 
 * @returns {undefined}
 */
function initFiltroButton() {

    $('#filtrar_epe').on('click', function (e) {

        filtrarEPE();
    });
}

/**
 * 
 * @returns {undefined}
 */
function initDatepickerInputs() {

    var meses = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
        "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];

    var mesActual = new Date().getMonth();

    var nombreMesActual = meses[mesActual];

    var fechaInicioEjercicioUsuarioDate = getDateFromString('01/01/' + ejercicioContableSesion);

    var fechaFinEjercicioUsuarioDate = getDateFromString('31/12/' + ejercicioContableSesion);

    // Ejercicio
    initDatepicker($('#adif_contablebundle_filtro_ejercicio'), {
        format: "yyyy",
        viewMode: "years",
        minViewMode: "years"
    });

    $('#adif_contablebundle_filtro_ejercicio')
            .datepicker("update", getCurrentYear().toString());

    $('#adif_contablebundle_filtro_ejercicio')
            .datepicker('setStartDate', fechaInicioEjercicioUsuarioDate);

    $('#adif_contablebundle_filtro_ejercicio')
            .datepicker('setEndDate', fechaFinEjercicioUsuarioDate);

    var calendarOptions = {
        format: "MM",
        viewMode: "months",
        minViewMode: "months"
    };


    // Mes Inicio
    initDatepicker($('#adif_contablebundle_filtro_fechaInicio'), calendarOptions);

    $('#adif_contablebundle_filtro_fechaInicio')
            .on('show', function () {

                $('.datepicker-months').find('thead th').css('visibility', 'hidden');

                $(this).datepicker('setEndDate', $('#adif_contablebundle_filtro_fechaFin').val());
            })
            .on('changeDate', function () {

                var mes = $('#adif_contablebundle_filtro_fechaInicio').datepicker("getDate").getMonth() + 1;

                $('#adif_contablebundle_filtro_fechaInicio').data('mes', mes);
            });


    $('#adif_contablebundle_filtro_fechaInicio').data('mes', 1);

    $('#adif_contablebundle_filtro_fechaInicio')
            .datepicker("update", meses[0]).keyup();

    $('#adif_contablebundle_filtro_fechaInicio')
            .datepicker('setStartDate', fechaInicioEjercicioUsuarioDate);

    $('#adif_contablebundle_filtro_fechaInicio')
            .datepicker('setEndDate', fechaFinEjercicioUsuarioDate);

    // Mes Fin
    initDatepicker($('#adif_contablebundle_filtro_fechaFin'), calendarOptions);

    $('#adif_contablebundle_filtro_fechaFin')
            .on('show', function () {
                $('.datepicker-months').find('thead th').css('visibility', 'hidden');

                $(this).datepicker('setStartDate', $('#adif_contablebundle_filtro_fechaInicio').val());
            })
            .on('changeDate', function () {

                var mes = $('#adif_contablebundle_filtro_fechaFin').datepicker("getDate").getMonth() + 1;

                $('#adif_contablebundle_filtro_fechaFin').data('mes', mes);
            });

    $('#adif_contablebundle_filtro_fechaFin').data('mes', mesActual + 1);

    $('#adif_contablebundle_filtro_fechaFin')
            .datepicker("update", nombreMesActual);


    $('#adif_contablebundle_filtro_fechaFin')
            .datepicker('setStartDate', fechaInicioEjercicioUsuarioDate);

    $('#adif_contablebundle_filtro_fechaFin')
            .datepicker('setEndDate', fechaFinEjercicioUsuarioDate);
}

/**
 * 
 * @returns {undefined}
 */
function filtrarEPE() {

    var ejercicio = $('#adif_contablebundle_filtro_ejercicio').val();

    var mesInicio = $('#adif_contablebundle_filtro_fechaInicio').data('mes');

    var mesFin = $('#adif_contablebundle_filtro_fechaFin').data('mes');


    if (ejercicio && mesInicio && mesFin) {

        var data = {
            ejercicio: ejercicio,
            fechaInicio: mesInicio,
            fechaFin: mesFin
        };

        $.ajax({
            type: "POST",
            data: data,
            url: urlEPEAccion
        }).done(function (renglones) {

            actualizarTabla(renglones);

            setMasks();

            updateCaptionTitle();
        });
    }
}

/**
 * 
 * @returns {undefined}
 */
function updateCaptionTitle() {

    var $fechaInicio = $('#adif_contablebundle_filtro_fechaInicio').val();
    var $fechaFin = $('#adif_contablebundle_filtro_fechaFin').val();

    var $ejercicio = $('#adif_contablebundle_filtro_ejercicio').val();

    $('.caption-fecha-desde').text($fechaInicio);
    $('.caption-fecha-hasta').text($fechaFin);
    $('.caption-ejercicio').text($ejercicio);

    $('.reporte_title').show();
}

/**
 * 
 * @returns {undefined}
 */
function setMasks() {

    $('.money-format').each(function () {
        $(this).autoNumeric('init', {vMin: '-999999999999.99', aSign: '$ ', aSep: '.', aDec: ','});
    });

    $('.money-format').each(function () {
        $(this).autoNumeric('update', {vMin: '-999999999999.99', aSign: '$ ', aSep: '.', aDec: ','});
    });
}