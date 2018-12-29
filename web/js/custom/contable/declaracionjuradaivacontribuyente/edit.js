$(document).ready(function () {
    initListeners();
    initCerrarButton();
    
    $('#boton_detalle_credito_fiscal').on('click', function(){
        show_dialog({
            titulo: 'Detalle del cr&eacute;dito fiscal',
            contenido: $('#detalle_credito_fiscal'),
            labelCancel: 'Cancelar',
            closeButton: false,
            callbackSuccess: function () {
                $('#contenido_detalle_credito_fiscal').html($('#detalle_credito_fiscal'));
                desbloquear();
                return;
            }
        });
        $('.bootbox-close-button').remove();
        $('.cancel').remove();
    });
    
    $('#boton_detalle_percepciones_iva').on('click' , function (e) {
        e.preventDefault();
        bloquear();
        
        var ajaxDialog = $.ajax({
            type: 'POST',
            data: {
                id: $('#id_ddjj').val()
            },
            url: __AJAX_PATH__ + 'declaracionesjuradasivacontribuyente/detalle_percepciones_iva/'
        });

        $.when(ajaxDialog).done(function (dataDialog) {
            var formulario = dataDialog;
            show_dialog({
                titulo: 'Detalle percepciones de IVA',
                contenido: formulario,
                labelCancel: 'Aceptar',
                callbackCancel: function () {
                    desbloquear();
                    return;
                },
                callbackSuccess: function () {
                    desbloquear();
                    return;                    
                }
            });
            
            $('.modal-footer').find('.success').remove();            
            $('.modal-dialog').css('width', '80%');

            dt_init($('.table-percepciones-iva'));
        });
    });
    
    calcular_coeficiente_prorrateo();
    calcular_total_iva();
    calcular_saldo_final();
});

function initListeners(){
    $('#adif_contablebundle_declaracionjuradaivacontribuyente_montoTotalFacturado,#adif_contablebundle_declaracionjuradaivacontribuyente_montoGravadoFacturado').on('change', function(){
        calcular_coeficiente_prorrateo();
    });
    
    $('#adif_contablebundle_declaracionjuradaivacontribuyente_montoIva105,#adif_contablebundle_declaracionjuradaivacontribuyente_montoIva21,#adif_contablebundle_declaracionjuradaivacontribuyente_montoIva27').on('change', function(){
        calcular_total_iva();
    });
    
    $('#adif_contablebundle_declaracionjuradaivacontribuyente_montoRetencionesIva,#adif_contablebundle_declaracionjuradaivacontribuyente_montoPercepcionesIva').on('change', function(){
        calcular_total_retenciones();
    });
    
    $('#adif_contablebundle_declaracionjuradaivacontribuyente_montoDebitoFiscal,#adif_contablebundle_declaracionjuradaivacontribuyente_montoCreditoFiscal').on('change', function(){
        calcular_saldo_ddjj();
    });
    
    $('#saldo_ddjj,#total_retenciones_percepciones').on('change', function(){
        calcular_saldo_final();
    });
}

/**
 * 
 * @returns {undefined}
 */
function initCerrarButton() {
    // Handler para el boton "Cerrar"
    $('#adif_contablebundle_declaracionjuradaivacontribuyente_ceerrar').on('click', function (e) {
        if ($('form[name="adif_contablebundle_declaracionjuradaivacontribuyente"]').valid()) {
            e.preventDefault();

            show_confirm({
                msg: '¿Desea cerrar la declaraci&oacute;n jurada?',
                callbackOK: function () {
                    var data = {
                        cerrar: true
                    };
                    
                    $('form[name="adif_contablebundle_declaracionjuradaivacontribuyente"]').addHiddenInputData(data).submit();
                }
            });

            e.stopPropagation();
            return false;
        }
        return false;
    });
}

function calcular_coeficiente_prorrateo(){
    var total_facturado = parseFloat($('#adif_contablebundle_declaracionjuradaivacontribuyente_montoTotalFacturado').val());
    var total_gravado = parseFloat($('#adif_contablebundle_declaracionjuradaivacontribuyente_montoGravadoFacturado').val());
    var coeficiente_prorrateo = (total_gravado / (total_facturado == 0 ? 1 : total_facturado)).toFixed(2).toString().replace('.', ',');
    $('#coeficiente_prorrateo').val(coeficiente_prorrateo);
    calcular_iva_cf();
}

function calcular_total_iva(){
    var iva105 = parseFloat($('#adif_contablebundle_declaracionjuradaivacontribuyente_montoIva105').val());
    var iva21 = parseFloat($('#adif_contablebundle_declaracionjuradaivacontribuyente_montoIva21').val());
    var iva27 = parseFloat($('#adif_contablebundle_declaracionjuradaivacontribuyente_montoIva27').val());
    var total_iva = (iva105 + iva21 + iva27).toFixed(2).toString().replace('.', ',');
    $('#total_iva').val(total_iva);
    calcular_iva_cf();
}

function calcular_total_retenciones(){
    var retenciones = parseFloat($('#adif_contablebundle_declaracionjuradaivacontribuyente_montoRetencionesIva').val());
    var percepciones = parseFloat($('#adif_contablebundle_declaracionjuradaivacontribuyente_montoPercepcionesIva').val());
    
    var total_retenciones = (retenciones + percepciones);
    $('#total_retenciones_percepciones').val(total_retenciones.toFixed(2).toString().replace('.', ',')).trigger('change');
}

function calcular_iva_cf(){
    var coeficiente_prorrateo = parseFloat($('#coeficiente_prorrateo').val().replace(',', '.'));
    var total_iva = parseFloat($('#total_iva').val().replace(',', '.'));
    var iva_cf_computable = total_iva * coeficiente_prorrateo;
    var iva_cf_no_computable = total_iva - iva_cf_computable;
            
    $('#adif_contablebundle_declaracionjuradaivacontribuyente_montoCreditoFiscal').val(iva_cf_computable.toFixed(2).toString().replace('.', ',')).trigger('change');
    $('#iva_cf_computable').val(iva_cf_computable.toFixed(2).toString().replace('.', ','));
    $('#iva_cf_no_computable').val(iva_cf_no_computable.toFixed(2).toString().replace('.', ','));
}

function calcular_saldo_ddjj(){
    var debito_fiscal = parseFloat($('#adif_contablebundle_declaracionjuradaivacontribuyente_montoDebitoFiscal').val().replace(',', '.'));
    var credito_fiscal = parseFloat($('#adif_contablebundle_declaracionjuradaivacontribuyente_montoCreditoFiscal').val().replace(',', '.'));
    
    var saldo_ddjj = debito_fiscal - credito_fiscal;
            
    $('#saldo_ddjj').val(saldo_ddjj.toFixed(2).toString().replace('.', ',')).trigger('change');
}

function calcular_saldo_final(){
    var saldo_mes_anterior = parseFloat($('#saldo_mes_anterior').val().replace(',', '.'));
    var saldo_ddjj = parseFloat($('#saldo_ddjj').val().replace(',', '.'));
    var total_retenciones = parseFloat($('#total_retenciones_percepciones').val().replace(',', '.'));
    
    var saldo_final = saldo_ddjj - total_retenciones - saldo_mes_anterior;
    
    var $label = $('#adif_contablebundle_declaracionjuradaivacontribuyente_saldo').parents('.form-group').find('label');
    
    if(saldo_final > 0){
        $label.text('Saldo a favor de AFIP');
    } else {
        $label.text('Saldo a favor de ADIF');
    }
    $('#adif_contablebundle_declaracionjuradaivacontribuyente_saldoMostrar').val(Math.abs(saldo_final).toFixed(2).toString().replace('.', ','));
    $('#adif_contablebundle_declaracionjuradaivacontribuyente_saldo').val(saldo_final.toFixed(2).toString().replace('.', ','));
            
}