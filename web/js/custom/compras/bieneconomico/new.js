jQuery(document).ready(function() {
    $('form[name=adif_comprasbundle_bieneconomico]').validate();
    
    if (edit) {
        $('#adif_comprasbundle_bieneconomico_regimenRetencionSUSS').val(regimenes[0]).select2();
        $('#adif_comprasbundle_bieneconomico_regimenRetencionIVA').val(regimenes[1]).select2();
        $('#adif_comprasbundle_bieneconomico_regimenRetencionIIBB').val(regimenes[2]).select2();
        $('#adif_comprasbundle_bieneconomico_regimenRetencionGanancias').val(regimenes[3]).select2();
    }
    
});