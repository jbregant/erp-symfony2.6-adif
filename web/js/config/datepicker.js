var __month_names = ["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"]

$.fn.datepicker.defaults.language = "es";
$.fn.datepicker.defaults.format = "dd/mm/yy";
var month = function(monthNumber){
    return __month_names[monthNumber];
};

