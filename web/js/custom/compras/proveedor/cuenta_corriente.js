var id_proveedor = 5;
var dt_cc =  null;
var dt_cc_column_index = {
    id:                 0, //oculta
    tipo_comprobante:   1,
    numero:             2,
    fecha_comprobante:  3,
    fecha_alta:         4,
    importe:            5,
    oc_id:              6 //oculta
};

var tchild_template = [
    '<div class="table-responsive">',
        '<table id="table-child" class="table table-bordered table-striped table-condensed flip-content">',
            '<thead><tr>',
                '<th width="40%">Tipo</th>',
                '<th width="40%">N&uacute;mero</th>',
                '<th width="20%">Fecha creaci&oacute;n</th>',
            '</tr></thead>',
            '<tbody>',
            '_____BODY____',
            '</tbody>',
        '</table>',
    '</div>'
].join();

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
            { data: "oc.id", targets: dt_cc_column_index.oc_id, visible: false}
        ],
        // drawCallback: function ( settings ) {
        //     var api = this.api();
        //     var rows = api.rows( {page:'current'} ).nodes();
        //     var last = null;
        //     var subtotal_oc_saldo = 0;

        //     api.column(dt_cc_column_index.oc_id, {page:'current'} ).data().each( function ( group, i ) {
                
        //         var row_data = api.row(i).data()

        //         // Sumar si tiene OP
        //         subtotal_oc_saldo +=  parseFloat(row_data.op.id == null ? row_data.importe.replace('.', '').replace(',', '.') : 0)
                
        //         // console.log(last, group, row_data)

        //         if ( last !== group ) {
        //             $(rows).eq( i ).before([
        //                 '<tr role="row" class="tr-grouped" id_oc="'+row_data.oc.id+'">',
        //                     '<td colspan="5">',
        //                     // '<td colspan="'+api.column(':visible').eq(0).length+'">',
        //                         'Orden de compra: <b>'+row_data.oc.numero+'</b> - Fecha: <b>'+row_data.oc.fecha+'</b>',
        //                     '</td>',
        //                     '<td class="text-right">'+row_data.oc.total+'</td>',
        //                     '<td colspan="6"></td>',
        //                 '</tr>'
        //                 ].join('')
        //             )
        //             last = group
        //         } else {
        //              $(rows).eq( i ).after([
        //                 '<tr role="row" class="tr-grouped" id_oc="'+row_data.oc.id+'">',
        //                     '<td colspan="5" class="text-right">',
        //                         'Saldo',
        //                     '</td>',
        //                     '<td class="text-right">'+$('<h>'+subtotal_oc_saldo.toFixed(2)+'</h>').priceFormat({ prefix: '', centsSeparator: ',',thousandsSeparator: '.'}).text()+'</td>',
        //                     '<td colspan="6"></td>',
        //                 '</tr>'
        //                 ].join('')
        //             )
        //         }
        //     })
        // }
    });

    // Como no se oculta la columna ID la oculto a mano
    dt_cc.DataTable().column(dt_cc_column_index.id).visible(false);
    // dt_cc.DataTable().column(dt_cc_column_index.ver_op).visible(false)
    // dt_cc.DataTable().column(dt_cc_column_index.importe).visible(false)

    $('#table-cuenta-corriente').off('click', 'tbody tr.tr-grouped');
    $('#table-cuenta-corriente').on('click', 'tbody tr.tr-grouped', function(e) {
        e.preventDefault();
        console.log(this);
    });
});