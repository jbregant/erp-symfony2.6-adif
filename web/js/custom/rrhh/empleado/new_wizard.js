var _empleado_wizard = $('#empleado_form_wizard');
var _empleado_tab_prefix = 'empleado_tab_';
var _empleado_form = $('#empleado_submit_form');
var _submit_clicked = false;

var _tab_alta_temprana_index = 1;
var _tab_datos_personales_index = 2;
var _tab_estudios_index = 3;
var _tab_puesto_index = 4;
var _tab_cuenta_index = 5;
var _tab_familiares_index = 6;
var _tab_contactos_emergencia_index = 7;

var _no_validate_tabs = [_tab_estudios_index, _tab_familiares_index, _tab_contactos_emergencia_index];
var EmpleadoFormWizard = function() {

    return {
        //main function to initiate the module
        init: function() {
            if (!$().bootstrapWizard) {
                return;
            }

            var form = $('#empleado_submit_form');
            var error = $('.alert-danger', form);
            var success = $('.alert-success', form);

            var handleTitle = function(tab, navigation, index) {
                var total = navigation.find('li').length;
                var current = index + 1;
                // set wizard title
                $('.step-title', _empleado_wizard).text('Paso ' + (index + 1) + ' de ' + total);

                // set done steps
                // $('li', _empleado_wizard).removeClass("done");
                // var li_list = navigation.find('li');
                // for (var i = 0; i < index; i++) {
                //     $(li_list[i]).addClass("done");
                // }

                if (current == 1) {
                    _empleado_wizard.find('.button-previous').hide();
                } else {
                    _empleado_wizard.find('.button-previous').show();
                }

                if (current >= total) {
                    _empleado_wizard.find('.button-next').hide();
                } else {
                    _empleado_wizard.find('.button-next').show();
                }

                App.scrollTo($('.page-title'));
            };

            // default form wizard
            _empleado_wizard.bootstrapWizard({
                'nextSelector': '.button-next',
                'previousSelector': '.button-previous',
                onTabClick: function(tab, navigation, index, clickedIndex) {
                    if (index == clickedIndex) {
                        return;
                    }

                    success.hide();
                    error.hide();

                    if (clickedIndex > index) {
                        // if (!currentTabValid(index + 1)) {
                        //     return false;
                        // }
                    }

                    handleTitle(tab, navigation, clickedIndex);
                },
                onNext: function(tab, navigation, index) {
                    success.hide();
                    error.hide();

                    // if (!currentTabValid(index)) {
                    //     return false;
                    // }
                    handleTitle(tab, navigation, index);
                },
                onPrevious: function(tab, navigation, index) {
                    success.hide();
                    error.hide();
                    handleTitle(tab, navigation, index);
                },
                onTabShow: function(tab, navigation, index) {
//                    getEmpleadoValidator().resetForm();
//                    _empleado_wizard.find('.has-error').removeClass('has-error');

                    if (!_submit_clicked) {
                        getEmpleadoValidator().resetForm();
                        _empleado_wizard.find('.has-error').removeClass('has-error');
                    }

                    var total = navigation.find('li').length;
                    var current = index + 1;
                    var $percent = (current / total) * 100;
                    _empleado_wizard.find('.progress-bar').css({
                        width: $percent + '%'
                    });
                }
            });

            $('.nav-pills > li').width((100 / $('.nav-pills > li').length) + '%');

            _empleado_wizard.find('.button-previous').hide();

            _empleado_wizard.find('.button-submit').on('click', function(e) {
                _submit_clicked = true;
                if (!_empleado_form.valid() && getEmpleadoValidator().invalidElements().length > 0) {
                    success.hide();
                        var tab_error_index = $(getEmpleadoValidator().invalidElements()).first().closest('.tab-pane').attr('id').split('_')[2];
                        $('li a.step[href="#empleado_tab_' + tab_error_index + '"]').trigger('click');
                        error.show();
                        _submit_clicked = false;
                    return false;
                } else {
                    return true;
                }
            });
        }
    };
}();

function currentTabValid(current_tab) {
    if (_.contains(_no_validate_tabs, current_tab)) {
        return true;
    }

    var errors = [];
    $('#' + _empleado_tab_prefix + (current_tab)).find(getEmpleadoValidator().elements()).each(function() {
        if (!getEmpleadoValidator().element($(this))) {
            errors.push(getEmpleadoValidator().element($(this)));
        }
    });

    return !(errors.length > 0);
}