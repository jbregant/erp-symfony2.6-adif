var dt_cc =  null;
var dt_cc_column_index = {
    id:                 0, //oculta
    tipo_comprobante:   1,
    numero:             2,
    fecha_comprobante:  3,
    fecha_alta:         4,
    importe:            5,
    id_contrato:        6 //oculta
};

$(document).ready(function(){
    dt_cc = dt_datatable($('#table-cuenta-corriente'), {
        columnDefs: [
            { data: "id", targets: dt_cc_column_index.id},
            {   
                data: 'tipo', 
                targets: dt_cc_column_index.tipo_comprobante,
                width: "100px",
                className: "nowrap"
            },
            { 
                data: "numero", 
                targets: dt_cc_column_index.numero,
                render : function (data, type, full, meta) {
                    return full.punto_venta + '-' + full.numero;
                },
                className: 'text-center nowrap' 
            },
            { 
                data: "fecha_comprobante", 
                targets: dt_cc_column_index.fecha_comprobante,
                className: 'text-center' 
            },
            { data: "fecha_alta", targets: dt_cc_column_index.fecha_alta, className: 'text-center' },
            { data: "importe", targets: dt_cc_column_index.importe, className: 'text-right' },            
            { data: "id_contrato", targets: dt_cc_column_index.id_contrato, visible: false}
        ]
    });

    // Como no se oculta la columna ID la oculto a mano
    dt_cc.DataTable().column(dt_cc_column_index.id).visible(false);
    
    $('#table-cuenta-corriente').off('click', 'tbody tr.tr-grouped');
    $('#table-cuenta-corriente').on('click', 'tbody tr.tr-grouped', function(e) {
        e.preventDefault();
        console.log(this);
    });
});