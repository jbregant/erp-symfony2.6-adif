
var index = 0;

var dt_usuario_column_index = {
    id: index++,
    multiselect: index++, 

    email: index++,

    cuit: index++,

    fecha_alta: index++,

    last_login: index++,

    estado: index++,

    acciones: index
};

dt_usuario = dt_datatable($('#table-portal-usuario'), {
    ajax: { 
        url: __AJAX_PATH__ + 'portal/usuarios/index_table/',
        dataFilter: function(data){
            var json = jQuery.parseJSON( data );
            var estados = [];
            estados["Registrado"] = 0;
            estados["Pendiente"] = 0;
            estados["Inderesado"] = 0;

            var total = json.data.lenght;
            $.each(json.data, function(index, value){
                estados[value[6]]++;
            });
            console.log(estados);
            chartData = [{
                name: "Registrado",
                y: estados["Registrado"]
            },{
                name: "Pendiente",
                y: estados["Pendiente"]
            },{
                name: "Interesado",
                y: estados["Inderesado"]
            }];
            id       = "grafic-container";
            title    = "Gráfico";
            subtitle = "";
            lineTitle= "Distribución de Usuarios"
            createPieChart(id, title, subtitle, lineTitle, chartData);

            return JSON.stringify( json ); // return JSON string
        }
    },
    columnDefs: [
    {
        "targets": dt_usuario_column_index.multiselect,
        "data": "ch_multiselect",
        "render": function (data, type, full, meta) {
            return '<input type="checkbox" class="checkboxes" value="' + data + '" />';
        }
    },
    {
        "targets": dt_usuario_column_index.acciones,
        "data": "actions",
        "render": function (data, type, full, meta) {
            var full_data = full[dt_usuario_column_index.acciones];
            return '<a href="' + full_data.show + '" class="btn btn-xs blue tooltips" data-original-title="Ver detalle">\n\
            <i class="fa fa-search"></i>\n\
            </a>' +
            (full_data.edit !== undefined ?
                '<a href="' + full_data.edit + '" class="btn btn-xs green tooltips" data-original-title="Editar">\n\
                <i class="fa fa-pencil"></i>\n\
                </a>' : '');
        }
    },
    {
        className: "text-center",
        targets: [
        dt_usuario_column_index.multiselect
        ]
    },
    {
        className: "ctn_acciones text-center nowrap",
        targets: dt_usuario_column_index.acciones
    }
    ]
});

$("#chartButton").click(function(e) { 
    e.preventDefault(); 
    $("#grafic-container").removeClass("hidden"); 
    $('html, body').animate({ 
        scrollTop: $("#grafic-container").offset().top 
    }, 2000); 
});