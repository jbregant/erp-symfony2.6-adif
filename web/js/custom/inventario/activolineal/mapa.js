var mapa;
var wmsLayer;
var activoLinealId;
var activoLinealPunto;
$(function(){

	$("#modal-mapa").on('shown.bs.modal', function(ev){
					if(mapa === undefined){
						mapa = L.map('leafletMap').setView([-34, -64], 5);
						L.tileLayer.bing({'bingMapsKey': BING_KEY, 'imagerySet': 'AerialWithLabels'}).addTo(mapa);
						wmsLayerLinea = L.tileLayer.wms(MAP_SERVICE_PATH, 
												{layers: LAYERNAME_LINEA, transparent: true, format: 'image/png', cql_filter:'id=-1'}).addTo(mapa);
						wmsLayerPunto = L.tileLayer.wms(MAP_SERVICE_PATH, 
												{layers: LAYERNAME_PUNTO, transparent: true, format: 'image/png', cql_filter:'id=-1'}).addTo(mapa);
					}


		/*Necesario para saber el bbox del elemento*/
		$.ajax({
				url: MAP_SERVICE_PATH,
				data: {	service:'WFS',
						version:'1.1.0',
						request:'GetFeature',
						typeName: activoLinealPunto ? LAYERNAME_PUNTO : LAYERNAME_LINEA,
						maxFeatures:'50',
						outputFormat:'application/json',
						cql_filter:'id='+activoLinealId},
				type: 'get',
				dataType: 'json',
				success: function(response){
						if(response.hasOwnProperty('bbox')){
							mapa.fitBounds([[response.bbox[0],response.bbox[1]],[response.bbox[2],response.bbox[3]]]);
							wmsLayerLinea.setParams({cql_filter:'id='+activoLinealId});
							wmsLayerPunto.setParams({cql_filter:'id='+activoLinealId});
						}else{
							alert("No se pudo mapear el elemento");
							$("#modal-mapa").modal('hide');
						}
				}
		});

	});

	$("#modal-mapa").on('hidden.bs.modal', function(ev){
		mapa.setView([-34, -64], 5);
		wmsLayerLinea.setParams({cql_filter:'id=-1'});
		wmsLayerPunto.setParams({cql_filter:'id=-1'});
		
		
	});
});

function showMap(id,punto){
	activoLinealId = id;
	activoLinealPunto = punto;
	$("#modal-mapa").modal('show');

}
