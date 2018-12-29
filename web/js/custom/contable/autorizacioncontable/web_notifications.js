var displayTime = 5000; //5 segundos
var timeOut = 60000; // 1 minutos
var frequency = 300000; // 5 minutos
       
var coin = new Audio(__AJAX_PATH__ + 'coin_sound.mp3');
var megaphone = new Audio(__AJAX_PATH__ + 'megaphone_sound.mp3'); 
/* Deshabilitado a pedido de setchetto - @jaicardo
$(document).ready(function () {
    
    askForWebNotificationPermissions();
    
    controlarAutorizacionesPendientes();
    
    setInterval(controlarAutorizacionesPendientes, frequency);

});
*/
function controlarAutorizacionesPendientes() {
    
    var notif = getNotificationSingleton();
    if (notif.permission == 'granted') { 

        $.ajax({
            type: "POST",
            data: {fechaInicio: $('#adif_contablebundle_filtro_fechaInicio').val(), fechaFin: $('#adif_contablebundle_filtro_fechaFin').val()},
            url: __AJAX_PATH__ + 'autorizacioncontable/autorizaciones_pendientes_sin_ver/'
        }).done(function (result) {
            var autorizaciones = JSON.parse(result).data;
            var ids = [];
            if (autorizaciones.length > 0) {

                megaphone.play();

                $(autorizaciones).each(function (index, value) {
                    ids.push(this[1]);
                    setTimeout(sendNotification, displayTime*(index+1), this);

                });

                $('#table-autorizacioncontable').DataTable().ajax.reload();

            }

        }); 
    }    

}

sendNotification = function(orden_pago){

    var options = { 
        body: orden_pago[4], 
        icon: __AJAX_PATH__ + 'images/logo_adif_sm_transparent.png',
        tag: orden_pago[12]
    };

    coin.play();

    var notif = new Notification('OP ' + parseInt(orden_pago[3]) + ' por ' + orden_pago[8].replace("$ ", "$"), options);
    
    setTimeout(notif.close.bind(notif), timeOut);
    
    notif.addEventListener("click", clickNotification);

    return notif;        
    
}

//Returns current browser's notification object. Base of all notifications. It may change, so let's encapsulate it here.
function getNotificationSingleton() {
	return window.Notification;
}

//Returns true if the web notifications API is supported
function getWebNotificationsSupported() {
	return (!!getNotificationSingleton());
}

//Ask the user for permission to show notifications if needed
//It will only work if it's called by an user's action (eg: it will not work in the onload event)
function askForWebNotificationPermissions()
{
	if (getWebNotificationsSupported()) {
		var notif = getNotificationSingleton();
		notif.requestPermission();
	}
}

function clickNotification(event) {
       
    //A reference to the Notification object
    var notif = event.currentTarget;
    $.ajax({
        type: "POST",
        data: {id: notif.tag},
        url: __AJAX_PATH__ + 'autorizacioncontable/autorizaciones_vistas/'
    }).done(function (response) {
//        if (response.status === 'OK') {
//            $('#table-autorizacioncontable').DataTable().ajax.reload();
//        }
    });      
}
