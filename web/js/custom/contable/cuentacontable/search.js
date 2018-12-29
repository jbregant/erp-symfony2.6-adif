
var cuenta_contable_search_form;

var cuenta_contable_seleccionada;

$(document).ready(function () {

    cuenta_contable_search_form = $('.cuenta_contable_search_form_content').html();

    $('.cuenta_contable_search_form_content').remove();

    initCuentaContableTree();

    initBuscarCuentaContableLink();
});


/**
 * 
 * @returns {undefined}
 */
function initCuentaContableTree() {

    $(".cuenta_contable_search_form")
            .on('changed.jstree', function (e, data) {

                var i, j = [];

                for (i = 0, j = data.selected.length; i < j; i++) {
                    cuenta_contable_seleccionada = {
                        id: data.instance.get_node(data.selected[i]).id,
                        text: data.instance.get_node(data.selected[i]).text
                    };
                }
            })
            .jstree({
                "plugins": ["unique", "search", "types", "wholerow"],
                "core": {
                    "animation": 100,
                    "check_callback": true,
                    "multiple": false,
                    "themes": {
                        "theme": "classic",
                        "stripes": true,
                        "dots": true,
                        "responsive": true,
                        "icons": false
                    },
                    'data': {
                        'url': function (node) {
                            return __AJAX_PATH__ + 'cuentacontable/tree/';
                        },
                        'data': function (node) {
                            return {'id': node.id};
                        },
                        "type": "post"
                    }
                },
                "types": {
                    "default": {
                        "icon": "fa fa-folder icon-warning icon-lg"
                    },
                    "file": {
                        "icon": "fa fa-file icon-warning icon-lg"
                    }
                },
                "search": {
                    'fuzzy': false,
                    "case_insensitive": false,
                    "show_only_matches": true,
                    "search_callback": false,
                    "close_opened_onclear": true,
                    'ajax': {
                        'url': __AJAX_PATH__ + "cuentacontable/search/",
                        'data': function (str) {
                            return {"search_str": str};
                        }
                    }
                }
            });
}

/**
 * 
 * @returns {undefined}
 */
function initBuscarCuentaContableLink() {

    $('.btn-search-cuenta-contable').off().on('click', function (e) {

        e.preventDefault();

        cuenta_contable_seleccionada = null;

        show_dialog({
            titulo: 'Seleccionar cuenta contable',
            contenido: cuenta_contable_search_form,
            callbackCancel: function () {
            },
            callbackSuccess: function () {

                // Si se seleccionó una Cuenta Contable
                if (cuenta_contable_seleccionada) {

                    $('select[id $= cuentaContable]')
                            .val(cuenta_contable_seleccionada['id']).select2().change();

                    $('select[id $= cuentaContablePadre]')
                            .val(cuenta_contable_seleccionada['id']).select2().change();
                } //.   
                else {
                    return false;
                }
            }
        });

        initCuentaContableTree();

        initBuscador();

        $('.bootbox').removeAttr('tabindex');

        $('.modal-dialog').css('width', '80%');
    });
}

/**
 * 
 * @returns {undefined}
 */
function initBuscador() {

    var to = false;

    $('#search_field').keyup(function () {

        if (to) {
            clearTimeout(to);
        }

        to = setTimeout(function () {
            $(".cuenta_contable_search_form").jstree(true)
                    .search($('#search_field').val());
        }, 250);
    });

    $("#search_tree").click(function () {

        $(".cuenta_contable_search_form").jstree(true)
                .search($('#search_field').val());
    });

}