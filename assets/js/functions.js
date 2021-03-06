var url = '/ocsmanager/index.php/';
//var url = 'http://192.168.0.191/ocsmanager/index.php/';

var stations = [], stationsDesc = [], stationsPor = [], stationsTotal = [];
var stationsQty = [], stationsUtil = [], stationColor = [], dataStations = [];
var nameStation = {}, porStation = {};
var gran_total = 0.0;
var gran_qty = 0.0;
var gran_cost = 0.0;
var gran_util = 0.0;
var paramsRequest = [];

var totalProductsExclude = [], totalProductsInclude = [],
	quiantityProductExclude = [], quiantityProductInclude = [],
	dataSumarySale = {};

/**
 * Funcion para acceso al sistema
 */
function login() {
	$('.msg-login').html(loading());
	console.log('username: '+$('#username').val()+', password: '+$('#password').val());
	if(empty($('#username').val())) {
		$('#username').focus();
		$('.msg-login').html(_alert('warning', 'Debe ingresar Usuario'));
		return false;
	}
	if(empty($('#password').val())) {
		$('.msg-login').html(_alert('warning', 'Debe ingresar Contraseña'));
		$('#password').focus();
		return false;
	}

	var params = {
		username: $('#username').val(),
		password: $('#password').val(),
	};
	$.post(url+'secure/postLogin', params, function(data) {
		console.log(data);
		if(data.status == 1) {
			window.location = url;
		} else if(data.status == 2) {
			$('.msg-login').html(_alert('warning', data.message));
			$('#password').focus();
		} else if(data.status == 3) {
			$('.msg-login').html(_alert('warning', data.message));
			$('#username').focus();
		} else if(data.status == 100) {
			$('.msg-login').html(_alert('warning', data.message));
		}
	}, 'json');
}

/**
 * Buscar ventas en estacion(es), combustibles y market
 * typeStation:
 * 0 - origen: ventas/combustibles, mod: TOTALS_SALE_COMB
 * 1 - 
 */
function searchSale() {
	console.log('function searchSale');
	clearStations();
	$('.container-chart-station').addClass('none');
	$('.container-ss-station').addClass('none');
	$('.result-search').html('<br><br>'+loading());
	var valStartDate = checkDate($('#start-date-request').val(),'/');
	var valEndDate = checkDate($('#end-date-request').val(),'/');

	console.log('valStartDate: '+valStartDate);
	console.log('valEndDate: '+valEndDate);

	if(valStartDate == 0) {
		//Error en formato de fecha
		$('.result-search').html(_alert('warning', '<span class="glyphicon glyphicon-alert"></span> <b>Importante</b>, Error en formato de fecha.'));
		$('.btn-search-sale').prop('disabled', false);
		//$('#start-date-request').focus();
		return false;
	} else if(valStartDate == 2) {
		//fecha futura
		$('.result-search').html(_alert('warning', '<span class="glyphicon glyphicon-alert"></span> <b>Importante</b>, No se puede consultar con esta fecha'));
		$('.btn-search-sale').prop('disabled', false);
		//$('#start-date-request').focus();
		return false;
	}

	if(valEndDate == 0) {
		//error en formato de fecha
		$('.result-search').html(_alert('warning', '<span class="glyphicon glyphicon-alert"></span> <b>Importante</b>, Error en formato de fecha.'));
		$('.btn-search-sale').prop('disabled', false);
		//$('#end-date-request').focus();
		return false;
	} else if(valEndDate == 2) {
		//fecha futura
		$('.result-search').html(_alert('warning', '<span class="glyphicon glyphicon-alert"></span> <b>Importante</b>, No se puede consultar con esta fecha'));
		$('.btn-search-sale').prop('disabled', false);
		//$('#end-date-request').focus();
		return false;
	}

	paramsRequest = {
		id: $('#select-station').val(),
		dateBegin: $('#start-date-request').val(),
		dateEnd: $('#end-date-request').val(),
		typeStation: $('#typeStation').val(),
		isMarket: $('#data-ismarket').val(),
		qtySale: $('#qty_sale').val(),
		typeCost: $('#type_cost').val(),
		typeResult: 1,
	}
	console.log('paramsRequest:', paramsRequest);
	console.log('start: '+paramsRequest.dateBegin+', end: '+paramsRequest.dateEnd);

	var sBeing = paramsRequest.dateBegin.split("/");
	var sEnd = paramsRequest.dateEnd.split("/");

	var sBeing = sBeing[1]+'/'+sBeing[2];
	var sEnd = sEnd[1]+'/'+sEnd[2];

	console.log('sBeing: '+sBeing+', sEnd: '+sEnd);

	if(sBeing == sEnd) {
		var charMode = $('#chart-mode').val();
		var count = 0;
		console.log('searchsale!');
		console.log('typeStation: '+paramsRequest.typeStation);
		//return false;
		// $.ajax({
		// 	type: 'post',
		// 	url: url+'requests/getSales',
		// 	data: paramsRequest,
		// 	dataType: 'json'
		// })
		// .done(function(data){
		// 	checkSession(data);
		// 	$('.btn-search-sale').prop('disabled', false);
		// 	console.log('Dentro del callback');
		// 	console.log(data);
		// 	$('.result-search').html(templateStationsSearch(data, data.typeStation, charMode));
		// });
		$.post(url+'requests/getSales', paramsRequest, function(data) {
			checkSession(data);
			$('.btn-search-sale').prop('disabled', false);
			console.log('Dentro del callback');
			console.log(data);
			$('.result-search').html(templateStationsSearch(data, data.typeStation, charMode));
		}, 'json');
	} else {
		$('.result-search').html(_alert('warning', '<span class="glyphicon glyphicon-alert"></span> <b>Importante</b>, Las fechas a consultar deben estar en el mismo mes.'))
		$('.btn-search-sale').prop('disabled', false);
	}
}

/**
 * Plantilla de estaciones buscadas
 * @param obj data, int t(typo estacion), int cm(chart mode)
 * @return string html
 */
function templateStationsSearch(data,t,cm) {
	console.log('data en templateStationsSearch:', data);

	clearStations();
	var html = '<br>';
	var detail = data.stations;
	if (typeof detail == "undefined") {
		return '<div class="alert alert-info">No existe información</div>';
	}
	var count = detail.length;
	gran_total = 0.0;
	gran_qty = 0.0;
	gran_util = 0.0;
	gran_cost = 0.0;
	var num = 1;
	var unit = t == 0 ? 'Gln' : '';

	var color_id, taxid;
	for(var i = 0; i<count; i++) {
		color_id = getRandomColor();
		if(taxid != detail[i].group.taxid) {
			html += (i != 0 ? '<hr>' : '');
			html += '<div class="panel-group-station"><h5 title="RUC: '+detail[i].group.taxid+'">'+detail[i].group.name+'</h5></div>';
			taxid = detail[i].group.taxid;
		}
		if(!detail[i].isConnection) {
			html += '<div class="container-station"><div class="panel panel-danger">'
			+'<div class="panel-heading"><span class="glyphicon glyphicon-exclamation-sign"></span> <strong>Sin conexión.</strong></div>';
		} else {
			html += '<div class="container-station"><div class="panel panel-default">';
		}
		html += '<div class="panel-body detail-station" data-station="'+detail[i].id+'"'
		+'data-begindate="'+data.beginDate+'" data-enddate="'+data.endDate+'" data-typestation="'+data.typeStation+'"'
		+'data-typecost="'+data.typeCost+'" title="Ver detalle de '+detail[i].name+'"'
		+'><span class="glyphicon glyphicon-stop" style="color: '+color_id+'"></span> '+num+'. '+detail[i].name+'</div>'
		+'<div class="panel-footer">'
		+'<div class="row">'
		+'<div class="col-md-6">'
		+'<div class="mid"><b>Venta: S/ '+numeral(detail[i].total_venta).format('0,0')+'</b></div>'
		+' <div class="mid"><b>'+numeral(detail[i].total_cantidad).format('0,0')+' '+unit+'</b></div>'
		+'</div>'
		+'<div class="col-md-6">'
		+' <div class="mid"><b>Costo: S/ '+numeral(detail[i].total_costo).format('0,0')+'</b></div>'
		+' <div class="mid"><b>Margen: S/ '+numeral(detail[i].total_utilidad).format('0,0')+'</b></div>'
		+'</div>'

		+'</div>'
		+'</div></div>';

		console.log('> gran_util: '+gran_util);
		gran_total += detail[i].total_venta != '' ? parseFloat(detail[i].total_venta) : parseFloat(0);
		gran_qty += detail[i].total_cantidad != '' ? parseFloat(detail[i].total_cantidad) : parseFloat(0);
		gran_util += detail[i].total_utilidad != '' ? parseFloat(detail[i].total_utilidad) : parseFloat(0);
		gran_cost += detail[i].total_costo != '' ? parseFloat(detail[i].total_costo) : parseFloat(0);

		/**
		 * Importante: considerar que PUSH esta agregando venta, cantidad y utilidad
		 * solo si el monto es positivo, caso contrario solo se agrega 0(cero)
		 */
		dataStations.push({
			name: detail[i].initials,
			total: detail[i].total_venta > 0 ? parseFloat(detail[i].total_venta) : parseFloat(0),
			qty: detail[i].total_cantidad > 0 ? parseFloat(detail[i].total_cantidad) : parseFloat(0),
			util: detail[i].total_utilidad > 0 ? parseFloat(detail[i].total_utilidad) : parseFloat(0),
			color: color_id,
			data: detail[i].data,
		});
		num++;
	}

	//gran_util
	console.log('> gran_util: '+gran_util);
	html += '<div class="panel panel-primary">'
	+'<div class="panel-heading" title="Ver total de productos"><div class="panel-title">Total General</div></div>'
	+'<div class="panel-body all-result-sales-comb" data-station="'+data.id+'" data-begindate="'
	+data.beginDate+'" data-enddate="'+data.endDate+'" data-typecost="'+data.typeCost+'" '
	+'data-typestation="'+data.typeStation+'">'
	+'<div class="row">'
	+'<div class="col-md-6">'
	+'<div class="mid"><b>Venta: S/ '+numeral(gran_total).format('0,0')+'</b></div>'
	+' <div class="mid"><b>'+numeral(gran_qty).format('0,0')+' '+unit+'</b></div>'
	+'</div>'
	+'<div class="col-md-6">'
	+' <div class="mid"><b>Costo: S/ '+numeral(gran_cost).format('0,0')+'</b></div>'
	+' <div class="mid"><b>Margen: S/ '+numeral(gran_util).format('0,0')+'</b></div>'
	+'</div>'
	+'</div>'
	+'</div>'
	+'</div>';

	storageStations();
	if(count > 1) {
		$('.container-chart-station').removeClass('none');
			
		if(cm == 0) {
			viewChartBarStation();
			viewChartBarStationQty();
			viewChartBarStationUtil();
		} else {
			viewChartStation();
		}
	}
	$('.container-ss-station').removeClass('none');

	setDataResultRequest('.download-comb-sales',data);

	return html;
}

/**
 * Visualizar información estación en Modal
 * @param obj - element t
 * data-typestation: 1 - ventas/market
 */
function viewDetailStation(t) {
    setContendModal('#normal-modal', '.modal-title', 'Cargando...', true);
    setContendModal('#normal-modal', '.modal-body', loading(), true);
	setContendModal('#normal-modal', '.modal-footer', '<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>', true);
	$('#normal-modal').modal();

	var params = {
		id: t.attr('data-station'),
		dateBegin: t.attr('data-begindate'),
		dateEnd: t.attr('data-enddate'),
		typeStation: t.attr('data-typestation'),
		typeCost: t.attr('data-typecost')
	};
	console.log('start: '+params.dateBegin+', end: '+params.dateEnd);
	$.post(url+'requests/getDetailComb', params, function(data) {
		checkSession(data);
		console.log('requests/getDetailComb');
		console.log(data);
		setContendModal('#normal-modal', '.modal-title', 'Detalle en '+data.stations[0].name, true);
		setContendModal('#normal-modal', '.modal-body', templateDetailStation(data, data.typeStation), true);
		setContendModal('#normal-modal', '.modal-footer', '<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>', true);
	}, 'json');
}

/**
 * Cuerpo del modal detalle estación
 * @param obj data(estacion), int type(tipo de estación)
 * @return string
 */
function templateDetailStation(data, type) {
	console.log('start: '+data.dateBegin+', end: '+data.dateEnd);
	var html = '<div class="row"><div class="col-md-6">Fecha:</div><div class="col-md-6">'
	+data.dateBegin+' - '+data.dateEnd+'</div></div><br>';
	console.log('isConnection: '+data.stations[0].isConnection);
	if(!data.stations[0].isConnection) {
		html += '<div class="alert alert-danger" role="alert"><span class="glyphicon glyphicon-exclamation-sign"></span> <strong>Sin conexión.</strong></div>';
	}
	html += '<div class="table-responsive"><table class="table table-bordered"> <thead> <tr> <th>Producto</th> <th align="right">Cantidad</th> <th align="right">Venta</th> <th align="right">Costo</th> <th align="right">Margen</th> </tr> </thead> <tbody>';

	var detail = data.stations[0].data;
	var count = detail.length;

	if(type == 0) {
		var qty_nglp = 0.0
		var total_nglp = 0.0;
		var cost_nglp = 0.0;
		var util_nglp = 0.0;

		var qty_glp = 0.0;
		var total_glp = 0.0;
		var cost_glp = 0.0;
		var util_glp = 0.0;

		var total_qty = 0.0;
		var gran_total = 0.0;
		var cost_total = 0.0;
		var util_total = 0.0;

		var qty = 0.0;
		var qtygal = 0.0;
		var html_ = ''; html__ = '';

		//comb
		for(var i = 0; i<count; i++) {
			console.log('For detail comb/line: '+detail[i].neto_venta);
			if(detail[i].product_id == '11620308') {
				console.log('GNV. detail[i].neto_venta; '+detail[i].neto_venta);

				if(detail[i].neto_venta != '') {
					qty = converterUM({type : 1, co : parseFloat(detail[i].neto_cantidad)});
					qtygal = converterUM({type : 1, co : parseFloat(detail[i].neto_cantidad)});
					qty_nglp += parseFloat(qtygal);

					//alert(''+parseFloat(detail[i].neto_cantidad)+', '+qty_nglp);
					total_nglp += parseFloat(detail[i].neto_venta);
					cost_nglp += parseFloat(detail[i].consumo_galon);
					console.log(': '+detail[i].consumo_galon);
					util_nglp += parseFloat(detail[i].utilidad);

					html += itemTableModal(3, i, detail, qtygal);
				} else {
					qty = parseFloat(0);
					qtygal = parseFloat(0);

					qty_nglp += parseFloat(0);
					total_nglp += parseFloat(0);
					cost_nglp += parseFloat(0);
					util_nglp += parseFloat(0);
				}

			} else if(detail[i].product_id != '11620307') {
				html += itemTableModal(1, i, detail, '');
				qty_nglp += parseFloat(detail[i].neto_cantidad);
				total_nglp += parseFloat(detail[i].neto_venta);
				cost_nglp += parseFloat(detail[i].consumo_galon);
				util_nglp += parseFloat(detail[i].utilidad);
				console.log(detail[i].product_id+': '+detail[i].neto_venta);
			} else {
				qty = converterUM({type : 0, co : parseFloat(detail[i].neto_cantidad)});
				qtygal = converterUM({type : 0, co : parseFloat(detail[i].neto_cantidad)});
				html__ += itemTableModal(2, i, detail, qtygal);
				qty_glp = parseFloat(qtygal);
				total_glp = parseFloat(detail[i].neto_venta);
				cost_glp += parseFloat(detail[i].consumo_galon);
				util_glp += parseFloat(detail[i].utilidad);
				console.log(detail[i].product_id+': '+detail[i].neto_venta);
			}
		}

		if(qty_nglp > 0) {
			html_ = '<tr class="info"><th scope="row"></th>'
			+'<td align="right">'+numeral(qty_nglp).format('0,0')+' Gl</td>'
			+'<td align="right">S/ '+numeral(total_nglp).format('0,0')+'</td>'
			+'<td align="right">S/ '+numeral(cost_nglp).format('0,0')+'</td>'
			+'<td align="right">S/ '+numeral(util_nglp).format('0,0')+'</td>'
			+'</tr>';
		} else {
			html_ = '';
		}

		total_qty = qty_nglp + qty_glp;
		gran_total = total_nglp + total_glp;
		cost_total = cost_nglp + cost_glp;
		util_total = util_nglp + util_glp;

		console.log('qty: '+qty_nglp + ' - '+qty_glp);
		
		return html+html_+html__
		+'<tr class="success"><th scope="row">Total General</th>'
		+'<td align="right">'+numeral(total_qty).format('0,0')+' Gl</td>'
		+'<td align="right">S/ '+numeral(gran_total).format('0,0')+'</td>'
		+'<td align="right">S/ '+numeral(cost_total).format('0,0')+'</td>'
		+'<td align="right">S/ '+numeral(util_total).format('0,0')+'</td>'
		+'</tr></tbody> </table></div>';

	} else if(type == 1 || type == 2) {
		//market
		console.log('detalle de market');
		var qty = 0.0;
		var sale = 0.0;
		var cost = 0.0;
		var util = 0.0;
		for(var i = 0; i<count; i++) {
			html += itemTableModal(4, i, detail, '');
			qty += clearFloat(detail[i].neto_cantidad);
			sale += clearFloat(detail[i].neto_venta);
			cost += clearFloat(detail[i].consumo_galon);
			console.log('pre util: '+util+', sumará: '+detail[i].utilidad);
			util += clearFloat(detail[i].utilidad);
			console.log('last util: '+util+'\n');
		}
		console.log('qty: '+qty+', sale: '+sale+' cost: '+cost+', util: '+util);
		return html+'<tr class="success"><th scope="row">Total General</th>'
		+'<td align="right">'+numeral(qty).format('0,0')+'</td>'
		+'<td align="right">S/ '+numeral(sale).format('0,0')+'</td>'
		+'<td align="right">S/ '+numeral(cost).format('0,0')+'</td>'
		+'<td align="right">S/ '+numeral(util).format('0,0')+'</td>'
		+'</tr></tbody> </table>';
	}
}

/**
 * Retorna filas para la tabla de productos(Modal)
 * @param int type 1,2 y 3 comb; 4: market
 * @return string
 */
function itemTableModal(type, i, detail, qtygal) {
	console.log('itemTableModal: '+detail[i].product);
	if(type == 1) {
		return '<tr><th scope="row">'+detail[i].product+'</th>'
				+'<td align="right">'
				+numeral(detail[i].neto_cantidad).format('0,0')+' Gl</td>'
				+'<td align="right">S/ '+numeral(detail[i].neto_venta).format('0,0')+'</td>'
				+'<td align="right">S/ '+numeral(detail[i].consumo_galon).format('0,0')+'</td>'
				+'<td align="right">S/ '+numeral(detail[i].utilidad).format('0,0')+'</td>'
				+'</tr>';
	} else if(type == 2) {
		//07 GLP
		var neto_venta = parseFloat(detail[i].neto_venta);
		var consumo_galon = parseFloat(detail[i].consumo_galon);
		var utilidad = parseFloat(detail[i].utilidad);
		return '<tr><th scope="row">'+detail[i].product+'</th>'
				+'<td align="right" title="'+detail[i].neto_cantidad+' Litros">'
				+'<a class="show-ltr" data-ltr="'+numeral(detail[i].neto_cantidad).format('0,0')+' L">'+numeral(qtygal).format('0,0')+' Gl</a></td>'
				+'<td align="right">S/ '+numeral(neto_venta).format('0,0')+'</td>'
				+'<td align="right">S/ '+numeral(consumo_galon).format('0,0')+'</td>'
				+'<td align="right">S/ '+numeral(utilidad).format('0,0')+'</td>'
				+'</tr>';
	} else if(type == 3) {
		//08 GNV
		var neto_venta = parseFloat(detail[i].neto_venta);
		var consumo_galon = parseFloat(detail[i].consumo_galon);
		//alert(consumo_galon);
		var utilidad = parseFloat(detail[i].utilidad);
		return '<tr><th scope="row">'+detail[i].product+'</th>'
				+'<td align="right" title="'+detail[i].neto_cantidad+' Metros Cúbicos">'
				//+'<a >'+numeral(detail[i].neto_cantidad).format('0,0')+' M3</a></td>'
				+'<a class="show-ltr" data-ltr="'+numeral(detail[i].neto_cantidad).format('0,0')+' M3">'+numeral(qtygal).format('0,0')+' Gl</a></td>'
				+'<td align="right">S/ '+numeral(neto_venta).format('0,0')+'</td>'
				+'<td align="right">S/ '+numeral(consumo_galon).format('0,0')+'</td>'
				+'<td align="right">S/ '+numeral(utilidad).format('0,0')+'</td>'
				+'</tr>';
	} else if(type == 4) {
		return '<tr><th scope="row">'+detail[i].product+'</th>'
				+'<td align="right" title="Unidad">'
				+'<a >'+numeral(detail[i].neto_cantidad).format('0,0')+'</a></td>'
				+'<td align="right">S/ '+numeral(detail[i].neto_venta).format('0,0')+'</td>'
				+'<td align="right">S/ '+numeral(detail[i].consumo_galon).format('0,0')+'</td>'
				+'<td align="right">S/ '+numeral(detail[i].utilidad).format('0,0')+'</td>'
				+'</tr>';
	}
}

//deprecated
function getAllProductsDetail(data) {
	var product_id = [];
	var station = data.stations;
	var count = station.length;
	for(var i = 0; i < count; i++) {
		console.log('station: '+station[i].name);
		var data_ = station[i].data;
		var count_ = data_.length;
		console.log('data_: '+data_+' count_: '+count_);
		for(var j = 0; j < count_; j++) {
			console.log('product: '+data_[j].product_id);
			product_id['"'+data_[j].product_id+'"'] = [data_[j].product, 0.0, 0.0];//sales
			//product_id['"'+data_[j].product_id+'"'][] = 0.0;//qty
			console.log('product_id["'+data_[j].product_id+'"]: '+product_id['"'+data_[j].product_id+'"']);
		};
	};
	return product_id;
}

//deprecated
//ejemplo de grafico
function viewChart() {
	setContendModal('#normal-modal', '.modal-title', 'Productos vendidos', true);
	setContendModal('#normal-modal', '.modal-body', '<canvas id="myChart"></canvas>', true);
	var ctx = document.getElementById('myChart').getContext('2d');
	var myChart = new Chart(ctx, {
		type: 'pie',
		data: {
			labels: ['Producto A', 'Producto B', 'P.C', 'P.D', 'P.E', 'P.F', 'P.G'],
			datasets: [{
				backgroundColor: [
				"#2ecc71",
				"#3498db",
				"#95a5a6",
				"#9b59b6",
				"#f1c40f",
				"#e74c3c",
				"#34495e"
				],
				data: [12, 19, 3, 17, 28, 24, 7]
			}]
		}
	});
	setContendModal('#normal-modal', '.modal-footer', '<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>', true);
	$('#normal-modal').modal();
}

/**
 * Vista de grafico tipo pie 
 */
function viewChartStation() {
	$('.chartStation').html('<canvas id="chartStation"></canvas>');
	var ctx = document.getElementById('chartStation').getContext('2d');
	var myChart = new Chart(ctx, {
		type: 'pie',
		data: {
			//labels: stations,
			labels : [],
			datasets: [{
				label: '---',
				backgroundColor: stationColor,
				data: stationsTotal
			}]
		}
	});
}

/**
 * Vista de grafico de barras principal (ventas)
 */
function viewChartBarStation() {
	$('.chartStation').html('<canvas id="chartStation"></canvas>');
	var ctx = document.getElementById('chartStation').getContext('2d');
	var myChart = new Chart(ctx, {
		type: 'bar',
		data: {
			labels: stations,
			datasets: [
				{
					label: 'Ventas',
					backgroundColor: stationColor,
					data: stationsTotal,
				}
			]
		}
	});

	//viewChartBarDemo();
	listAllObjects();//solo para comprobar lo que esta añadido en el objeto stations
}

/**
 * Vista de grafico de barras cantidad
 */
function viewChartBarStationQty() {
	$('.chartStationQty').html('<canvas id="chartStationQty"></canvas>');
	var ctx = document.getElementById('chartStationQty').getContext('2d');
	var myChart = new Chart(ctx, {
		type: 'bar',
		data: {
			labels: stations,
			datasets: [
				{
					label: 'Cantidades',
					backgroundColor: stationColor,
					data: stationsQty,
				}
			]
		}
	});
	//viewChartBarDemo();
	listAllObjects();//solo para comprobar lo que esta añadido en el objeto stations
}

/**
 * Vista de grafico de barras utilidad
 */
function viewChartBarStationUtil() {
	$('.chartStationUtil').html('<canvas id="chartStationUtil"></canvas>');
	var ctx = document.getElementById('chartStationUtil').getContext('2d');
	var myChart = new Chart(ctx, {
		type: 'bar',
		data: {
			labels: stations,
			datasets: [
				{
					label: 'Utilidades',
					backgroundColor: stationColor,
					data: stationsUtil,
				}
			]
		}
	});
	$('.chartStationUtil').append('<br><div class="alert alert-info" align="center">Nota: Las estaciones con Utilidad negativo tendrán 0 en este gráfico</div>');

	//viewChartBarDemo();
	listAllObjects();//solo para comprobar lo que esta añadido en el objeto stations
}

/**
 * Ejemplo de grafico de barras (demo)
 */
function viewChartBarDemo() {
	$('.chartStation').append('<canvas id="chartStation2"></canvas>');
	var ctx = document.getElementById('chartStation2').getContext('2d');
	var myChart = new Chart(ctx, {
		type: 'bar',
		data: {
			labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
			datasets: [
				{
					label: 'Ventas',
					backgroundColor: [
					'rgba(255, 99, 132, 0.2)',
					'rgba(54, 162, 235, 0.2)',
					'rgba(255, 206, 86, 0.2)',
					'rgba(75, 192, 192, 0.2)',
					'rgba(153, 102, 255, 0.2)',
					'rgba(255, 159, 64, 0.2)'
					],
					borderColor: [
					'rgba(255,99,132,1)',
					'rgba(54, 162, 235, 1)',
					'rgba(255, 206, 86, 1)',
					'rgba(75, 192, 192, 1)',
					'rgba(153, 102, 255, 1)',
					'rgba(255, 159, 64, 1)'
					],
					data: [65, 59, 80, 81, 56, 55, 40],
				}
			]
		}
	});
}

/**
 * Asignar contenido al modal(secciones)
 * @param string modal(element), string elem(elemento a modificar), string cont(contenido a usar), boolean isVisible
 */
function setContendModal(modal, elem, cont, isVisible) {
	isVisible = isVisible ? 'block' : 'none';
	$( modal ).find( elem ).html( cont ).css( 'display',isVisible );
}

/**
 * Comprobar si un valor es vacio
 * @param string input
 * @return boolean
 */
function empty(input) {
	console.log('input.length: '+input.length);
	if(input == '' || input.length < 1) {//error
		return true;
	} else {
		return false;
	}
}

/**
 * Mensaje de alerta
 * @param string type(doc bootstrap), string text(contenido a mostrar)
 * @return string
 */
function _alert(type, text) {
	return '<div class="alert alert-'+type+'" role="alert">'+text+'</div>';
}

function _alertJS(type, text) {
	return '<div role="alert" class="alert alert-'+type+' alert-dismissible fade in"> <button aria-label="Close" data-dismiss="alert" class="close" type="button"><span aria-hidden="true">×</span></button> '+text+' </div>';
}

/**
 * Validar fecha
 * Ej valida: XXXX-02-30, XXXX-12-32, fechas futuras
 * @param string input, string sep
 * @return 
 */
function checkDate(input, sep) {
	var status = 1;
	console.log('input: '+input+', sep: '+sep);
	input = input.split(sep);
	console.log('split: '+input[2]+'/'+input[1]+'/'+input[0]);
	console.log('input: '+input);
	var month = parseInt(input[1]) -1;
	//var month = input[1];
	var d = new Date(input[2], month, input[0]);

	var rightnow = new Date();
	if (d.getFullYear() == input[2] && d.getMonth() == input[1] && d.getDate() == input[0]) {
		status = 0;
	} else if(rightnow < d) {
		status = 2;
	}
	console.log('---> input[2], month, input[0]: '+input[2]+'-'+month+'-'+input[0]);
	console.log('---> d: '+d);
	console.log('---> rightnow: '+rightnow);

	return status;
}

/**
 * Retorna 0 si el contenido es NaN
 * @param val
 * @return val
 */
function formatNaN(val) {
	if (isNaN(val)) {
		return 0;
	}
	return val;
}

/**
 * Completa caracteres a dos digitos para fechas
 * @param int m
 * @return string m
 */
function completeMonth(m) {
	if(m.length != 2) {
		return '0'+m;
	} else {
		return m;
	}
}


/**
 * Alamacena informacion de estaciones en objetos y arreglos
 *
 */
function storageStations() {
	console.log(stations);
	var count = dataStations.length;
	console.log('station l: '+dataStations.length);
	var name = '';
	for(var i = 0; i < count; i++) {
		console.log('station: '+dataStations[i].name);
		name = dataStations[i].name;
		stations.push(
			name
		);

		stationsTotal.push(
			numeral(dataStations[i].total).format('0.00')
		);

		stationsQty.push(
			numeral(dataStations[i].qty).format('0.00')
		);

		stationsUtil.push(
			numeral(dataStations[i].util).format('0.00')
		);

		var por = (dataStations[i].total / gran_total) * 100;
		por = parseFloat(por);

		por = numeral(por).format('0.00');

		stationsPor.push(
			por
		);

		stationColor.push(
			dataStations[i].color
		);
	};
}

/**
 * Lista informacion de estaciones almacenada en objetos y arreglos
 * Solo para visualizar(comprobar) data
 */
function listAllObjects() {
	var count = stations.length;
	for(var i = 0; i < count; i++) {
		console.log('stations: '+stations[i]);
	}
	console.log('------');
	var count = stationsDesc.length;
	for(var i = 0; i < count; i++) {
		console.log('stationsDesc: '+stationsDesc[i]);
	}
	console.log('------');
	var count = stationsTotal.length;
	for(var i = 0; i < count; i++) {
		console.log('stationsTotal: '+stationsTotal[i]);
	}
	console.log('------');
	var count = stationsQty.length;
	for(var i = 0; i < count; i++) {
		console.log('stationsQty: '+stationsQty[i]);
	}
	console.log('------');
	var count = stationsUtil.length;
	for(var i = 0; i < count; i++) {
		console.log('stationsUtil: '+stationsUtil[i]);
	}
	console.log('------');
	var count = stationsPor.length;
	for(var i = 0; i < count; i++) {
		console.log('stationsPor: '+stationsPor[i]);
	}
	console.log('------');
	var count = stationColor.length;
	for(var i = 0; i < count; i++) {
		console.log('stationColor: '+stationColor[i]);
	}
}

/**
 * Descargar Hoja de Cálculo de Venta de Combustibles
 * @param obj t (atributos del boton de descarga)
 */
function downloadCombSales(t) {
	console.log(t);
	var dateB = t.attr('data-begindate').split("/");
	dateB = dateB[0] + '-' + dateB[1] + '-' + dateB[2];

	var dateE = t.attr('data-enddate').split("/");
	dateE = dateE[0] + '-' + dateE[1] + '-' + dateE[2];

	console.log('dateB: '+dateB+', dateE: '+dateE);

	//validaciones
	var params = {
		id: t.attr('data-station') == '*' ? 'a' : t.attr('data-station'),
		beginDate: dateB,
		endDate: dateE,
		typeStation: t.attr('data-typestation'),
		qtySale: t.attr('data-qtysale'),
		typeCost: t.attr('data-typecost'),
		typeResult: 1,
	};
	console.log('params.beginDate: '+params.beginDate+', params.endDate: '+params.endDate);
	var url_ = url+'reports/resumeSales/'+params.id+'/'+params.beginDate+'/'+params.endDate+'/'+params.typeStation+'/'+params.qtySale+'/'+params.typeCost;
	console.log('url_: '+url_);
	window.location = url_;
}

function downloadSumary(t) {
	console.log(t);
	var dateB = t.attr('data-begindate').split("/");
	dateB = dateB[0] + '-' + dateB[1] + '-' + dateB[2];

	var dateE = t.attr('data-enddate').split("/");
	dateE = dateE[0] + '-' + dateE[1] + '-' + dateE[2];

	console.log('dateB: '+dateB+', dateE: '+dateE);

	//validaciones
	var params = {
		id: t.attr('data-station') == '*' ? 'a' : t.attr('data-station'),
		beginDate: dateB,
		endDate: dateE,
		typeStation: t.attr('data-typestation'),
		qtySale: t.attr('data-qtysale'),
		typeCost: t.attr('data-typecost'),
		typeResult: 1,
		include: t.attr('data-include'),
	};
	console.log('params.beginDate: '+params.beginDate+', params.endDate: '+params.endDate);
	var url_ = url+'reports/generateCaclSumary/'+params.id+'/'+params.beginDate+'/'+params.endDate+'/'+params.typeStation+'/'+params.qtySale+'/'+params.typeCost+'/'+params.include;
	console.log('url_: '+url_);
	window.location = url_;
}

/**
 * Otorgar data obtenida en atributos de solicitud
 * @param string element, obj dfata
 */
function setDataResultRequest(element,data) {
	console.log('SET: '+element);
	console.log('\n\n\n');
	console.log(data);
	console.log('\n\n\n');
	$(element).attr('data-typestation',data.typeStation).attr('data-enddate',data.endDate).attr('data-begindate',data.beginDate).attr('data-station',data.id).attr('data-typecost',data.typeCost).attr('data-qtysale',data.qtySale);
}

/**
 * Detalle de todos los productos - linea en todas las estaciones seleccionadas (Modal)
 * @param obj element click
 */
function detailAllResult(t) {
	console.log('click 666');
	setContendModal('#normal-modal', '.modal-title', 'Cargando...', true);
	setContendModal('#normal-modal', '.modal-body', loading(), true);
	setContendModal('#normal-modal', '.modal-footer', '<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>', true);
	$('#normal-modal').modal();

	var params = {
		id: t.attr('data-station'),
		dateBegin: t.attr('data-begindate'),
		dateEnd: t.attr('data-enddate'),
		typeStation: $('#typeStation').val(),
		typeCost: t.attr('data-typecost')
	};

	var html = '';
	var unit = params.typeStation == 0 ? 'Gl' : '';

	if(params.typeStation == 0) {
		var qty_nglp = 0.0
		var total_nglp = 0.0;
		var cost_nglp = 0.0;
		var util_nglp = 0.0;

		var qty_glp = 0.0;
		var total_glp = 0.0;
		var cost_glp = 0.0;
		var util_glp = 0.0;

		var total_qty = 0.0;
		var gran_total = 0.0;
		var cost_total = 0.0;
		var util_total = 0.0;

		var qty = 0.0;
		var qtygal = 0.0;
		var html_ = ''; html__ = '';
	} else if(params.typeStation == 1 || params.typeStation == 2) {
		var total_qty = 0.0;
		var gran_total = 0.0;
		var cost_total = 0.0;
		var util_total = 0.0;
	}

	$.post(url+'requests/getDetailProducts', params, function(data) {
		checkSession(data);
		console.log(data);
		console.log(params.typeStation);
		html = '<div class="row"><div class="col-md-6">Fecha:</div><div class="col-md-6">'
		+data.dateBegin+' - '+data.dateEnd+'</div></div><br>';
		html += '<div class="table-responsive"><table class="table table-bordered"> <thead> <tr> <th>Producto</th> <th align="right">Cantidad</th> <th align="right">Venta</th> <th align="right">Costo</th> <th align="right">Utilidad</th> </tr> </thead> <tbody>';

		var product = data.dataProducts;
		var sales = 0.0;

		if(params.typeStation == 0) {
			for(var i = 0; i < product.length; i++) {
				console.log('formatDateEnd: '+product[i].code);
				if(product[i].code == '11620308') {
					qty = converterUM({type : 1, co : product[i].neto_cantidad});
					qtygal = converterUM({type : 1, co : product[i].neto_cantidad});

					//qty_nglp += parseFloat(0);
					if(product[i].neto_venta != '') {
						qty_nglp += parseFloat(qtygal);
						total_nglp += parseFloat(product[i].neto_venta);
						cost_nglp += parseFloat(product[i].consumo_galon);
						util_nglp += parseFloat(product[i].utilidad);
					} else {
						qty_nglp += parseFloat(0);
						total_nglp += parseFloat(0);
						cost_nglp += parseFloat(0);
						util_nglp += parseFloat(0);
					}
					html += itemTableModal(3, i, product, qtygal);
					//total_nglp += parseFloat(product[i].neto_venta);
				} else if(product[i].code != '11620307') {
					qty_nglp += parseFloat(product[i].neto_cantidad);
					total_nglp += parseFloat(product[i].neto_venta);
					cost_nglp += parseFloat(product[i].consumo_galon);
					util_nglp += parseFloat(product[i].utilidad);
					html += itemTableModal(1, i, product, '');
				} else {
					qty = converterUM({type : 0, co : product[i].neto_cantidad});
					qtygal = converterUM({type : 0, co : product[i].neto_cantidad});
					html__ += itemTableModal(2, i, product, qtygal);
					qty_glp = parseFloat(qtygal);
					total_glp += parseFloat(product[i].neto_venta);
					cost_glp += parseFloat(product[i].consumo_galon);
					util_glp += parseFloat(product[i].utilidad);
				}
			};

			if(qty_nglp > 0) {
				html_ = '<tr class="info"><th scope="row"></th>'
				+'<td align="right">'+numeral(qty_nglp).format('0,0')+' '+unit+'</td>'
				+'<td align="right">S/ '+numeral(total_nglp).format('0,0')+'</td>'
				+'<td align="right">S/ '+numeral(cost_nglp).format('0,0')+'</td>'
				+'<td align="right">S/ '+numeral(util_nglp).format('0,0')+'</td>'
				+'</tr>';
			} else {
				html_ = '';
			}

			total_qty = qty_nglp + qty_glp;
			gran_total = total_nglp + total_glp;
			cost_total = cost_nglp + cost_glp;
			util_total = util_nglp + util_glp;
			
			var _html = html+html_+html__
			+'<tr class="success"><th scope="row">Total General</th>'
			+'<td align="right">'+numeral(total_qty).format('0,0')+' '+unit+'</td>'
			+'<td align="right">S/ '+numeral(gran_total).format('0,0')+'</td>'
			+'<td align="right">S/ '+numeral(cost_total).format('0,0')+'</td>'
			+'<td align="right">S/ '+numeral(util_total).format('0,0')+'</td>'
			+'</tr></tbody> </table></div>';
		} else if(params.typeStation == 1 || params.typeStation == 2) {
			for(var i = 0; i < product.length; i++) {
				console.log('code: '+product[i].code);
				html += itemTableModal(4, i, product, '');
				total_qty += clearFloat(product[i].neto_cantidad);
				gran_total += clearFloat(product[i].neto_venta);
				cost_total += clearFloat(product[i].consumo_galon);
				util_total += clearFloat(product[i].utilidad);
			}
			var _html = html+'<tr class="success"><th scope="row">Total General</th>'
			+'<td align="right">'+numeral(total_qty).format('0,0')+' '+unit+'</td>'
			+'<td align="right">S/ '+numeral(gran_total).format('0,0')+'</td>'
			+'<td align="right">S/ '+numeral(cost_total).format('0,0')+'</td>'
			+'<td align="right">S/ '+numeral(util_total).format('0,0')+'</td>'
			+'</tr> </tbody> </table></div>';
		}

		setContendModal('#normal-modal', '.modal-title', 'Resumen del total de productos vendidos', true);
		setContendModal('#normal-modal', '.modal-body', _html, true);
		setContendModal('#normal-modal', '.modal-footer', '<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>', true);
	}, 'json');
}

/**
 * Buscar stock de Combustible y Market TP
 */
function searchStock() {
	$('.container-ss-station').addClass('none');
	$('.result-search').html('<br><br>'+loading());
	var valStartDate = checkDate($('#start-date-request').val(),'/');

	if(valStartDate == 0) {
		//formato no valido
		$('.result-search').html(_alert('warning', '<span class="glyphicon glyphicon-alert"></span> <b>Importante</b>, Error en formato de fecha.'));
		$('.btn-search-stock').prop('disabled', false);
	} else if(valStartDate == 2) {
		//fechas futuras
		$('.result-search').html(_alert('warning', '<span class="glyphicon glyphicon-alert"></span> <b>Importante</b>, No se puede consultar con esta fecha'));
		$('.btn-search-stock').prop('disabled', false);
	}

	paramsRequest = {
		id: $('#select-station').val(),
		dateBegin: $('#start-date-request').val(),
		daysProm: $('#days-prom').val(),
		typeStation: $('#typeStation').val(),
		isMarket: $('#data-ismarket').val(),
		qty_sale: $('#qty_sale').val(),
		type_cost: $('#type_cost').val(),
		type_result: 1,
	}
	console.log('start: '+paramsRequest.dateBegin);
	$.post(url+'requests/getStocks', paramsRequest, function(data) {
		checkSession(data);
		$('.btn-search-stock').prop('disabled', false);
		console.log('Dentro del callback');
		console.log(data);
		//$('.result-search').html(templateStationsSearch(data,data.typeStation,charMode));
		$('.result-search').html(templateStock(data,data.typeStation,0));
		templateTankSimulation(data);

		setDataResultRequest('.download-comb-stock',data);
	}, 'json');
}

/**
 * Platilla contenedores de Stock
 * @param obj data, type(type de estacion), 
 * @return string return html
 */
function templateStock(data,type,chart) {
	var html = '<br>';
	var detail = data.stations;
	var count = detail.length;
	var num = 1;
	var color_id, taxid;
	for(var i = 0; i<count; i++) {
		color_id = getRandomColor();

		if(taxid != detail[i].group.taxid) {
			html += (i != 0 ? '<hr>' : '');
			html += '<div class="panel-group-station"><h5 title="RUC: '+detail[i].group.taxid+'">'+detail[i].group.name+'</h5></div>';
			taxid = detail[i].group.taxid;
		}
		if(!detail[i].isConnection) {
			html += '<div class="container-station"><div class="panel panel-danger">'
			+'<div class="panel-heading"><span class="glyphicon glyphicon-exclamation-sign"></span> <strong>Sin conexión.</strong></div>';
		} else {
			html += '<div class="container-station"><div class="panel panel-default">';
		}
		html += '<div class="panel-body detail" data-station="'+detail[i].id+'"'
		+'data-begindate="'+data.beginDate+'" data-enddate="'+data.endDate+'" data-typestation="'+data.typeStation+'"'
		+'><span class="glyphicon glyphicon-stop" style="color: '+color_id+'"></span> '+num+'. '+detail[i].name
		+'<br><table class="table table-bordered table-striped none">'
		+'<thead> <tr> <th>Prod.</th> <th align="right">Cap.</th> <th align="right">% Disp.</th> <th align="right">Días Aprox.</th> </tr> </thead>'
		+'<tbody> ';
		html += templateTableDetailStock(detail[i].data,detail[i].id);
		html += ' </tbody> </table><br><div class="row container-canvas canvas-station-'+detail[i].id+'"></div>'
		+'</div></div>'
		+'</div></div>';
		num++;
	}
	return html;
}

/**
 * Contenido(tr-td) de tabla stocks combustible
 * @param obj detail, int id (codigo de estacion)
 */
function templateTableDetailStock(detail,id) {
	var html = '';
	var count = detail.length;
	for(var i = 0; i < count; i++) {
		console.log('Producto: '+detail[i].desc_comb);
		html += '<tr>'
		+'<th scope="row">'+detail[i].desc_comb+'</th>'
		+'<td align="right">'+numeral(detail[i].nu_capacidad).format('0,0')+'</td>'
		+'<td align="right">'+numeral(detail[i].porcentaje_existente).format('0,0')+'</td>'
		+'<td align="right">'+numeral(detail[i].tiempo_vaciar).format('0,0')+'</td>'
		+'</tr>';
		console.log('.canvas-station-'+id);
		//$('.canvas-station-'+id).append('<canvas id="canvas-tank-'+id+'-'+detail[i].cod_comb+'"></canvas>');
	};
	return html;
}

/**
 * Platilla para los graficos e información de los tanques
 * @param obj data $post
 */
function templateTankSimulation(data) {
	console.log('templateTankSimulation');
	var detail = data.stations;
	var count = detail.length;
	var append = '';
	var detailTank = '';
	var typeMedition = '';
	for(var i = 0; i<count; i++) {
		var id = detail[i].id;
		var data_ = detail[i].data;
		var count_ = data_.length;
		var inn = 0;

		for(var j = 0; j < count_; j++) {
			//Solo no se considera GNV
			typeMedition = data_[j].cod_comb != '11620307' ? 'gal' : 'ltr';
			append += '<div class="col-md-4 tank-in">'
			+'<div class="panel panel-default"><div class="panel-body panel-body-tank">'
			+'<div class="row info-tank info-tank-'+id+'-'+data_[j].cod_comb+'">'
			+'<div class="col-md-4" align="center">'
			+'<div class="name-tank msg-tank-'+id+'-'+data_[j].cod_comb+'"><label>'+data_[j].desc_comb+'</label></div>'
			+'<canvas id="canvas-tank-'+id+'-'+data_[j].cod_comb+'" class="canvas-tank" data-estation-id="'+id+'" data-cod-comb="'+data_[j].cod_comb+'" width="120" height="72"></canvas>'
			+'</div>'
			+'<div class="col-md-8 detail-tank detail-tank-'+id+'-'+data_[j].cod_comb+'"></div>'
			+'</div></div>'
			//+'<div class="panel-footer msg-tank msg-tank-'+id+'-'+data_[j].cod_comb+'" align="center"></div>'
			+'</div></div>';
			
			$('.canvas-station-'+id).append(append);
			renderTankSimulate({
				stock: data_[j].nu_medicion,
				percentaje: data_[j].porcentaje_existente,
				capacity: data_[j].nu_capacidad,
				unit: typeMedition,
				text: data_[j].desc_comb,
				elementId: id+'-'+data_[j].cod_comb,
				color: getColorComb(data_[j].cod_comb,true),
				debug: false
			});

			console.log('data_[j].nu_capacidad: '+data_[j].nu_capacidad+', data_[j].porcentaje_existente: '+data_[j].porcentaje_existente+' | '+id+'-'+data_[j].cod_comb);
			detailTank = '';
			detailTank += '<label class="label-detail-comb">Inventario:</label> '+numeral(data_[j].nu_medicion).format('0')+' '+typeMedition+'</div>'
			+'<div><label class="label-detail-comb">Promedio:</label> '+numeral(data_[j].nu_venta_promedio_dia).format('0')+' '+typeMedition+' por día</div>'
			+'<div><label class="label-detail-comb">Tiempo en vaciar:</label> '+numeral(data_[j].tiempo_vaciar).format('0')+' día(s)</div>'
			+'<div><label class="label-detail-comb">Última compra:</label> '+numeral(data_[j].cantidad_ultima_compra).format('0')+' '+typeMedition+', '+data_[j].fecha_ultima_compra;
			$('.detail-tank-'+id+'-'+data_[j].cod_comb).append(detailTank);
			if(data_[j].nu_capacidad <= 0) {
				$('.msg-tank-'+id+'-'+data_[j].cod_comb).prepend('<button class="resume-info-tank" title="Información" data-content="Se registró cero o menos<br>como capacidad del tanque.<br><p>Capacidad: '+numeral(data_[j].nu_capacidad).format('0')+' '+typeMedition+'</p>" data-placement="top" data-html="true" data-trigger="focus"><span style="color: #C65959;" class="glyphicon glyphicon-exclamation-sign"></span></button> ');
				$('.msg-tank-'+id+'-'+data_[j].cod_comb).addClass('msg-tank-mobile');
			}
			if(data_[j].porcentaje_existente > 100) {
				$('.msg-tank-'+id+'-'+data_[j].cod_comb).prepend('<button class="resume-info-tank" title="Información" data-content="La medición excede la<br>capacidad del tanque.<br><p>Medición: '+numeral(data_[j].nu_medicion).format('0')+' '+typeMedition+'</p><p>Capacidad: '+numeral(data_[j].nu_capacidad).format('0')+' '+typeMedition+'</p>" data-placement="top" data-html="true" data-trigger="focus"><span style="color: #F12F2F;" class="glyphicon glyphicon-exclamation-sign"></span></button> ');
				$('.msg-tank-'+id+'-'+data_[j].cod_comb).addClass('msg-tank-mobile');
			}
			if(parseFloat(data_[j].nu_medicion) <= 0 || isNaN(parseFloat(data_[j].nu_medicion))) {
				$('.msg-tank-'+id+'-'+data_[j].cod_comb).prepend('<button class="resume-info-tank" title="Información" data-content="No existe medición<br>para este producto." data-placement="top" data-html="true" data-trigger="focus"><span style="color: #F12F2F;" class="glyphicon glyphicon-exclamation-sign"></span></button> ');
				$('.msg-tank-'+id+'-'+data_[j].cod_comb).addClass('msg-tank-mobile');
			}
			append = '';
			detailTank = '';
		}
	}
	if(count > 0) {
		$('.container-ss-station').removeClass('none');
	}
}

/**
 * Simulación de medida del tanque
 * @param obj data $post
 */
function renderTankSimulate(data) {
	var isErrorCapacity = false, isErrorPercentaje = false, isErrorStock = false;
	$('.msg-tank-'+data.elementId).html('<span>'+data.text+'</span>');
	if(data.debug) {
		$('.msg-tank-'+data.elementId).append('<br>percentaje: '+data.percentaje+', capacity: '+data.capacity,', ');
	}

	var y = 10, x = 10, ye = 60, xe = 110;
	var conf = {widthLine: 2, colorTank: '200,0,0'};
	console.log('ancho de line: '+conf.widthLine);
	var canvas = document.getElementById('canvas-tank-'+data.elementId);
	var context = canvas.getContext('2d');

	var realMedition = data.stock;
	if(data.debug) {
		$('.msg-tank-'+data.elementId).append('<br>Real Medition: '+realMedition);
	}

	var medition = ((ye-y)*data.percentaje)/100;
	medition = ye-medition;
	if(data.debug) {
		$('.msg-tank-'+data.elementId).append('<br>Medition: '+medition);
	}

	if(data.capacity <= 0) {
		isErrorCapacity = true;
	}
	if(data.percentaje > 100) {
		isErrorPercentaje = true;
	}
	if(parseFloat(data.stock) <= 0 || isNaN(parseFloat(data.stock))) {
		isErrorStock = true;
	}

	//barra superior
	context.beginPath();
	context.moveTo(x, y);
	context.lineTo(xe, y);
	context.stroke();

	//barra derecha
	context.beginPath();
	context.moveTo(x, y);
	context.lineTo(x, ye);
	context.lineWidth = conf.widthLine;
	context.strokeStyle = '#5D5D5D';
	context.stroke();

	//barra izquierda
	context.beginPath();
	context.moveTo(xe, y);
	context.lineTo(xe, ye);
	context.stroke();

	//barra base
	context.beginPath();
	context.moveTo(x, ye);//60,150
	context.lineTo(xe, ye);
	context.stroke();

	if(!isErrorCapacity && !isErrorPercentaje && !isErrorStock) {
		context.fillStyle = data.color;
		context.fillRect (x+1, medition, (xe-x)-2, (ye-medition)-1);
	}

	if(!isErrorStock) {
		//(text) porcentaje de medicion
		context.fillStyle = 'rgb(0,0,0)';
		context.font = "20px Arial";
		if(data.percentaje >= 53 && data.percentaje <= 100) {
			context.fillText(numeral(data.percentaje).format('0')+'%',x+36,medition+20);
		} else if(data.percentaje > 0 && data.percentaje < 53) {
			context.fillText(numeral(data.percentaje).format('0')+'%',x+36,medition-5);
		}
	} else {
		console.log('\nOcurrió un error en la medición');
	}
}

/**
 * Función para el evento click en el gráfico del tanque(No usado)
 */
function detailInfoTank(t) {
	alert('data-estation-id: '+t.attr('data-estation-id')+', data-cod-comb: '+t.attr('data-cod-comb'));
}

/**
 * Mostar/Ocultar Popover Bootstrap
 */
function showPO(t,b) {
	if(b) {
		t.popover('show');
	} else {
		t.popover('hide');
	}
}

/**
 * Descargar hoja de calculo Stock de Combustibles
 * @param obj t (atributos del boton de descarga)
 */
function downloadCombStock(t) {
	console.log(t);

	var dateB = $('#start-date-request').val().split('/');//attr
	dateB = dateB[0] + '-' + dateB[1] + '-' + dateB[2];

	console.log('dateB: '+dateB);

	//validaciones
	var params = {
		id: t.attr('data-station') == '*' ? 'a' : t.attr('data-station'),
		dateBegin: dateB,
		typeStation: t.attr('data-typestation'),
		type_result: 1,
	};
	console.log('params.dateBegin: '+params.dateBegin);

	var url_ = url+'reports/resumeStock/'+params.id+'/'+params.dateBegin+'/'+params.typeStation;
	console.log('url_: '+url_);
	window.location = url_;
}

/**
 * Visualizar información estación en Modal
 * @param obj - element t
 */
function searchSumarySales(t) {
	$('.container-ss-station').addClass('none');
	$('.result-search').html('<br><br>'+loading());
	 var paramsRequest = {
		id: $('#select-station').val(),
		dateBegin: $('#start-date-request').val(),
		dateEnd: $('#end-date-request').val(),
		typeStation: $('#typeStation').val(),
		isMarket: $('#data-ismarket').val(),
		qtySale: $('#qty_sale').val(),
		typeCost: $('#type_cost').val(),
		typeResult: 1,
	}

    /*setContendModal('#normal-modal', '.modal-title', 'Cargando...', true);
    setContendModal('#normal-modal', '.modal-body', loading(), true);
	setContendModal('#normal-modal', '.modal-footer', '<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>', true);
	$('#normal-modal').modal();*/

	console.log('start: '+paramsRequest.dateBegin+', end: '+paramsRequest.dateEnd);
	$.post(url+'requests/getSumarySale', paramsRequest, function(data) {
		checkSession(data);
		dataSumarySale = data;
		console.log('requests/getSumarySale');
		console.log(data);
		var html = '<ul class="nav nav-tabs">'
		+'<li class="active"><a href="#quantity" data-toggle="tab">Galones</a>'
		+'<li><a href="#money" data-toggle="tab">Soles</a></li>'
		+'</li>'
		+'</ul>';
		html += '<div class="tab-content clearfix">';
		html += '<div class="tab-pane active" id="quantity">'
		+'<div class="quantity-include"></div><div class="quantity-exclude none"></div>'
		+'</div>';
		html += '<div class="tab-pane" id="money">';
		html += '<div class="money-include"></div><div class="money-exclude none"></div>';
		html += '</div>';
		html += '</div>';
		html += '<div class="graphics"></div>';


		$('.result-search').html(html);

		$('.money-include').html(templateTableSumarySales(data, 'money-include'));
		renderGraphicResume('money-include',paramsRequest);//0
		clearDataResumen();
		$('.quantity-include').html(templateTableSumarySales(data, 'quantity-include'));
		renderGraphicResume('quantity-include',paramsRequest);//1
		clearDataResumen();

		$('.money-exclude').html(templateTableSumarySales(data, 'money-exclude'));
		renderGraphicResume('money-exclude',paramsRequest);//2
		clearDataResumen();
		$('.quantity-exclude').html(templateTableSumarySales(data, 'quantity-exclude'));
		renderGraphicResume('quantity-exclude',paramsRequest);//3
		clearDataResumen();

		$('.btn-search-sale').prop('disabled', false);

		/*setContendModal('#normal-modal', '.modal-title', 'Detalle en '+data.stations[0].name, true);
		setContendModal('#normal-modal', '.modal-body', templateDetailStation(data,data.typeStation), true);
		setContendModal('#normal-modal', '.modal-footer', '<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>', true);*/
	}, 'json');
}

function templateTableSumarySales(data, type) {
	//limpiar arrays
	/*
	11620306	KEROSENE		KEROSENE
	11620303	GASOHOL 97		97 OCT
	11620305	GASOHOL 95		95 OCT
	11620304	DIESEL B5 UV	D2 PET
	11620301	GASOHOL 84		84 OCT
	11620302	GASOHOL 90		90 OCT
	11620307	GLP				GLP
	*/

	//var _type = type == 'money-include' || 'money-exclude' ? 'money' : 'quantity';
	var html = '<br><div>Excluir consumo <span class="glyphicon glyphicon-info-sign" title="Consumo interno de la empresa"></span>: '
	+'<div class="btn-group" aria-label="Default button group" role="group"><div class="btn-'+type+' true btn btn-default" data-action="true">Si</div><div class="btn-'+type+' false btn btn-success" data-action="false">No</div></div>'
	+'</div>'
	+'<div class="table-responsive" style="background-color: #fff; border-radius: 4px; padding: 5px;">';
	html += '<table class="table table-striped">'
	+'<thead>'
	+'<tr class="header-table-sumary">'
	+'<th colspan="9" style="text-align: center">Resumen de venta por estación y producto</th>'
	+'</tr>'
	+'<tr class="header-table-sumary">'
	+'<th>Estación</th>'
	+'<th style="text-align: right;">84</th>'
	+'<th style="text-align: right;">90</th>'
	+'<th style="text-align: right;">95</th>'
	+'<th style="text-align: right;">97</th>'
	+'<th style="text-align: right;">D2</th>'
	+'<th style="text-align: right;">GLP</th>'
	+'<th style="text-align: right;">GNV</th>'
	+'<th style="text-align: right;">Total</th>'
	+'</tr>'
	+'</thead>'
	+'<tbody>';
	var stations = data.stations;
	var countStations = stations.length;
	console.log('statios: '+stations);
	console.log('countStations: '+countStations);
	var total = [0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0];
	for (var i = 0; i < countStations; i++) {
		var attr = stations[i].isConnection ? '' : 'style="background-color: #ebccd1" title="Sin Conexión"';
		html += '<tr '+attr+'>'
		+'<th scope="row">'+stations[i].name+'</th>';
		var _data = stations[i].data;
		var _countData = _data.length;
		var product = [0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0];

		if (type == 'money-include') {
			for (var j = 0; j < _countData; j++) {
				console.log('neto venta: ('+j+') '+_data[j].neto_venta);

				if (_data[j].product_id == '11620301') {
					//84
					product[0] = _data[j].neto_venta;

				} else if (_data[j].product_id == '11620302') {
					//90
					product[1] = _data[j].neto_venta;

				} else if (_data[j].product_id == '11620305') {
					//95
					product[2] = _data[j].neto_venta;

				} else if (_data[j].product_id == '11620303') {
					//97
					product[3] = _data[j].neto_venta;

				} else if (_data[j].product_id == '11620304') {
					//D2
					product[4] = _data[j].neto_venta;

				} else if (_data[j].product_id == '11620307') {
					//GLP
					product[5] = _data[j].neto_venta;

				} else if (_data[j].product_id == '11620308') {
					//GNV
					if (_data[j].neto_venta != '') {
						product[6] = _data[j].neto_venta;
					}
				}
				product[7] += parseFloat(_data[j].neto_venta != '' ? _data[j].neto_venta : 0);

			}
		} else if (type == 'money-exclude') {
			for (var j = 0; j < _countData; j++) {
				console.log('neto venta: ('+j+') '+_data[j].neto_venta);
				console.log('importe_ci: '+_data[j].importe_ci);
				console.log('cantidad_ci: '+_data[j].cantidad_ci);

				if (_data[j].product_id == '11620301') {
					//84
					product[0] = _data[j].neto_venta - _data[j].importe_ci;

				} else if (_data[j].product_id == '11620302') {
					//90
					product[1] = _data[j].neto_venta - _data[j].importe_ci;

				} else if (_data[j].product_id == '11620305') {
					//95
					product[2] = _data[j].neto_venta - _data[j].importe_ci;

				} else if (_data[j].product_id == '11620303') {
					//97
					product[3] = _data[j].neto_venta - _data[j].importe_ci;

				} else if (_data[j].product_id == '11620304') {
					//D2
					product[4] = _data[j].neto_venta - _data[j].importe_ci;

				} else if (_data[j].product_id == '11620307') {
					//GLP
					product[5] = _data[j].neto_venta - _data[j].importe_ci;

				} else if (_data[j].product_id == '11620308') {
					//GNV
					if (_data[j].neto_venta != '') {
						product[6] = _data[j].neto_venta - _data[j].importe_ci;
					}
				}
				product[7] += parseFloat(_data[j].neto_venta != '' ? _data[j].neto_venta : 0) - parseFloat(_data[j].importe_ci);//menos importe_ci

			}
		} else if (type == 'quantity-include') {
			for (var j = 0; j < _countData; j++) {
					console.log('neto cantidad: ('+j+') '+_data[j].neto_cantidad);
				if (_data[j].product_id == '11620301') {
					//84
					product[0] = _data[j].neto_cantidad;
				} else if (_data[j].product_id == '11620302') {
					//90
					product[1] = _data[j].neto_cantidad;
				} else if (_data[j].product_id == '11620305') {
					//95
					product[2] = _data[j].neto_cantidad;
				} else if (_data[j].product_id == '11620303') {
					//97
					product[3] = _data[j].neto_cantidad;
				} else if (_data[j].product_id == '11620304') {
					//D2
					product[4] = _data[j].neto_cantidad;
				} else if (_data[j].product_id == '11620307') {
					//GLP
					product[5] = converterUM({type: 0, co: _data[j].neto_cantidad});

				} else if (_data[j].product_id == '11620308') {
					//GNV
					if (_data[j].neto_cantidad != '') {
						product[6] = converterUM({type: 1, co: _data[j].neto_cantidad});

					}
				}

				if(_data[j].product_id == '11620307') {
					product[7] += parseFloat(product[5]);
				} else if(_data[j].product_id == '11620308') {
					product[7] += parseFloat(product[6]);
				} else if(_data[j].product_id != '11620307' || _data[j].product_id == '11620308') {
					product[7] += parseFloat(_data[j].neto_cantidad != '' ? _data[j].neto_cantidad : 0);
				}
			}
		} else if (type == 'quantity-exclude') {
			for (var j = 0; j < _countData; j++) {
					console.log('neto cantidad: ('+j+') '+_data[j].neto_cantidad);
				if (_data[j].product_id == '11620301') {
					//84
					product[0] = _data[j].neto_cantidad - _data[j].cantidad_ci;
				} else if (_data[j].product_id == '11620302') {
					//90
					product[1] = _data[j].neto_cantidad - _data[j].cantidad_ci;
				} else if (_data[j].product_id == '11620305') {
					//95
					product[2] = _data[j].neto_cantidad - _data[j].cantidad_ci;
				} else if (_data[j].product_id == '11620303') {
					//97
					product[3] = _data[j].neto_cantidad - _data[j].cantidad_ci;
				} else if (_data[j].product_id == '11620304') {
					//D2
					product[4] = _data[j].neto_cantidad - _data[j].cantidad_ci;
				} else if (_data[j].product_id == '11620307') {
					//GLP
					product[5] = converterUM({type: 0, co: _data[j].neto_cantidad}) - converterUM({type: 0, co: _data[j].cantidad_ci});

				} else if (_data[j].product_id == '11620308') {
					//GNV
					if (_data[j].neto_cantidad != '') {
						product[6] = converterUM({type: 1, co: _data[j].neto_cantidad}) - converterUM({type: 1, co: _data[j].cantidad_ci});

					}
				}

				if (_data[j].product_id == '11620307') {
					product[7] += parseFloat(product[5]);
				} else if (_data[j].product_id == '11620308') {
					product[7] += parseFloat(product[6]);
				} else if (_data[j].product_id != '11620307' || _data[j].product_id == '11620308') {
					product[7] += parseFloat(_data[j].neto_cantidad != '' ? _data[j].neto_cantidad : 0) - parseFloat(_data[j].cantidad_ci);
				}
			}
		}

		html += '<td align="right">'+numeral(product[0]).format('0,0')+'</td>';
		html += '<td align="right">'+numeral(product[1]).format('0,0')+'</td>';
		html += '<td align="right">'+numeral(product[2]).format('0,0')+'</td>';
		html += '<td align="right">'+numeral(product[3]).format('0,0')+'</td>';
		html += '<td align="right">'+numeral(product[4]).format('0,0')+'</td>';
		html += '<td align="right">'+numeral(product[5]).format('0,0')+'</td>';
		html += '<td align="right">'+numeral(product[6]).format('0,0')+'</td>';
		html += '<td align="right">'+numeral(product[7]).format('0,0')+'</td>';

		total[0] += parseFloat(product[0]);
		total[1] += parseFloat(product[1]);
		total[2] += parseFloat(product[2]);
		total[3] += parseFloat(product[3]);
		total[4] += parseFloat(product[4]);
		total[5] += parseFloat(product[5]);
		total[6] += parseFloat(product[6]);
		total[7] += parseFloat(product[7]);

		html += '</tr>';
	}
	html += '</tbody>';
	html += '<tfoot>';
	html += '<tr class="header-table-sumary" style="font-weight: bold;">';
	html += '<td>Total</td>';
	html += '<td align="right">'+numeral(total[0]).format('0,0')+'</td>';
	html += '<td align="right">'+numeral(total[1]).format('0,0')+'</td>';
	html += '<td align="right">'+numeral(total[2]).format('0,0')+'</td>';
	html += '<td align="right">'+numeral(total[3]).format('0,0')+'</td>';
	html += '<td align="right">'+numeral(total[4]).format('0,0')+'</td>';
	html += '<td align="right">'+numeral(total[5]).format('0,0')+'</td>';
	html += '<td align="right">'+numeral(total[6]).format('0,0')+'</td>';
	html += '<td align="right">'+numeral(total[7]).format('0,0')+'</td>';
	html += '</tr>';
	html += '</tfoot>'
	+'</table></div><br><div class="graphics-'+type+'"></div><br>';

	stationColor.push(
		getColorComb('11620301', true)
	);
	stationColor.push(
		getColorComb('11620302', true)
	);
	stationColor.push(
		getColorComb('11620305', true)
	);
	stationColor.push(
		getColorComb('11620303', true)
	);
	stationColor.push(
		getColorComb('11620304', true)
	);
	stationColor.push(
		getColorComb('11620307', true)
	);
	stationColor.push(
		getColorComb('11620308', true)
	);

	for (var i = 0; i < (total.length) -1 ; i++) {
		if (type == 'money-include') {
			totalProductsInclude.push(
				numeral(total[i]).format('0')
			);
		} else if (type == 'money-exclude') {
			totalProductsExclude.push(
				numeral(total[i]).format('0')
			);
		} else if (type == 'quantity-include') {
			quiantityProductInclude.push(
				numeral(total[i]).format('0')
			);
		} else if (type == 'quantity-exclude') {
			quiantityProductExclude.push(
				numeral(total[i]).format('0')
			);
		}
	};

	return html;
}

function renderGraphicResume(type, paramsRequest) {
	$('.'+type).append('<canvas id="my-chart-'+type+'"></canvas><br><br><div class="btn-download-'+type+'"></div>');
	var ctx = document.getElementById('my-chart-'+type).getContext('2d');

	for (var i = 0; i < stationColor.length; i++) {
		console.log('stationColor: '+stationColor[i]);
	};

	var data = [];
	var label = '';
	var par;

	if (type == 'money-include') {
		label = 'Soles';
		data = totalProductsInclude;
		par = 2;
	} else if (type == 'money-exclude') {
		label = 'Soles';
		data = totalProductsExclude;
		par = 3;
	} else if (type == 'quantity-include') {
		label = 'Galones';
		data = quiantityProductInclude;
		par = 0;
	} else if (type == 'quantity-exclude') {
		label = 'Galones';
		data = quiantityProductExclude;
		par = 1;
	}

	for (var i = 0; i < data.length; i++) {
		console.log('data: '+data[i]);
	};

	var myChart = new Chart(ctx, {
		type: 'bar',
		data: {
			labels: ['84', '90', '95', '97', 'D2', 'GLP', 'GNV'],
			datasets: [
				{
					label: label,
					backgroundColor: stationColor,
					data: data,
				}
			]
		}
	});
	$('.btn-download-'+type).append('<button class="btn btn-success btn-block btn-lg download-sumary download-sumary-'+type+'" title="Generar información en Hoja de Cálculo"><span class="glyphicon glyphicon-download-alt"></span> Hoja de Cálculo</button>');
	$('.download-sumary-'+type).attr('data-typestation',paramsRequest.typeStation).attr('data-enddate',paramsRequest.dateEnd).attr('data-begindate',paramsRequest.dateBegin).attr('data-station',paramsRequest.id).attr('data-typecost',paramsRequest.typeCost).attr('data-qtysale',paramsRequest.qtySale).attr('data-include',par);
}

function generateCaclSumary() {
	$.get(url+'reports/generateCaclSumary', dataSumarySale, function(data) {
		checkSession(data);
		console.log('requests/generateCaclSumary');
		window.location = url+'reports/demoajax';
		console.log(data);
	});
}

function searchStatisticsSales(t) {
	$('.result-search').html('<br><br>'+loading());
	var paramsRequest = {
		id: $('#select-station').val(),
		dateBegin: $('#start-date-request').val(),
		dateEnd: $('#end-date-request').val(),

		_dateBegin: $('#_start-date-request').val(),
		_dateEnd: $('#_end-date-request').val(),

		typeStation: $('#typeStation').val(),
		isMarket: $('#data-ismarket').val(),
		qtySale: $('#qty_sale').val(),
		typeCost: $('#type_cost').val(),
		typeResult: 1,
	}

	console.log('start: '+paramsRequest.dateBegin+', end: '+paramsRequest.dateEnd);
	$.post(url+'requests/getStatisticsSale', paramsRequest, function(data) {
		checkSession(data);
		console.log('requests/getStatisticsSale');
		console.log(data);

		var html = '<div class="money-include"></div><div class="money-exclude none"></div>';

		$('.result-search').html(html);

		$('.money-include').html(templateTableStatistics(data, paramsRequest, 'money-include'));
		renderGraphicStatistics('money-include',paramsRequest);//1
		//clearDataResumen();

		$('.money-exclude').html(templateTableStatistics(data, paramsRequest, 'money-exclude'));
		renderGraphicStatistics('money-exclude',paramsRequest);//3
		//clearDataResumen();
		
		$('.btn-search-sale').prop('disabled', false);
	}, 'json');
}

function templateTableStatistics(data, pr, type) {
	var html = '<br><div>Excluir consumo <span class="glyphicon glyphicon-info-sign" title="Consumo interno de la empresa"></span>: '
	+'<div class="btn-group" aria-label="Default button group" role="group"><div class="btn-'+type+' true btn btn-default" data-action="true">Si</div><div class="btn-'+type+' false btn btn-success" data-action="false">No</div></div>'
	+'</div>';
	html += '<div class="table-responsive" style="background-color: #fff; border-radius: 4px; padding: 5px;">';
	html += '<table class="table">'
	+'<thead>'
	+'<tr class="header-table-sumary">'
	+'<th colspan="10" style="text-align: center">Estadística de Ventas</th>'
	+'</tr>'
	+'<tr class="header-table-sumary">'
	+'<th></th><th colspan="8" style="text-align: center">Galones</th><th style="text-align: center">Soles</th>'
	+'</tr>'
	+'<tr class="header-table-sumary">'
	+'<th>Estación</th>'
	+'<th style="text-align: right;">84</th>'
	+'<th style="text-align: right;">90</th>'
	+'<th style="text-align: right;">95</th>'
	+'<th style="text-align: right;">97</th>'
	+'<th style="text-align: right;">D2</th>'
	+'<th style="text-align: right;">GLP</th>'
	+'<th style="text-align: right;">GNV</th>'
	+'<th style="text-align: right;">Total</th>'
	+'<th style="text-align: right;">Tienda</th>'
	+'</tr>'
	+'</thead>'
	+'<tbody>';
	var stations = data.stations;
	var countStations = stations.length;
	console.log('statios: '+stations);
	console.log('countStations: '+countStations);
	var total = [0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0];
	var item = [];
	var sale_total = [0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0];
	var sale__total = [0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0];
	var dif_total = [0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0];

	for (var i = 0; i < countStations; i++) {
		var attr = stations[i].isConnection ? '' : 'style="background-color: #ebccd1" title="Sin Conexión"';
		/*html += '<tr '+attr+'>'
		+'<th scope="row">'+stations[i].name+'</th>';*/
		var _data = stations[i].data;
		var _countData = _data.length;
		console.log(type+' -> _countData: '+_countData);
		//var product = [0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0];
		var sale = [0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0];
		var sale_ = [0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0];
		var dif = [0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0];

		var text = [];

		text[0] = stations[i].name;

		text[1] = 'Anterior';

		if (type == 'money-include') {
			for (var j = 0; j < _countData; j++) {

				console.log('{type: '+_data[j].type);
				console.log('neto venta: ('+j+') '+_data[j].neto_venta+'}');

				if (_data[j].product_id == '11620301') {
					//84
					if(_data[j].type == 'actual') {
						sale[0] = _data[j].neto_venta;
					} else {
						sale_[0] = _data[j].neto_venta;
						dif[0] = amountPercentage({num1: sale[0], num2: sale_[0]});
					}

				} else if (_data[j].product_id == '11620302') {
					//90
					if(_data[j].type == 'actual') {
						sale[1] = _data[j].neto_venta;
					} else {
						sale_[1] = _data[j].neto_venta;
						dif[1] = amountPercentage({num1: sale[1], num2: sale_[1]});
					}

				} else if (_data[j].product_id == '11620305') {
					//95
					if(_data[j].type == 'actual') {
						sale[2] = _data[j].neto_venta;
					} else {
						sale_[2] = _data[j].neto_venta;
						dif[2] = amountPercentage({num1: sale[2], num2: sale_[2]});
					}

				} else if (_data[j].product_id == '11620303') {
					//97
					if(_data[j].type == 'actual') {
						sale[3] = _data[j].neto_venta;
					} else {
						sale_[3] = _data[j].neto_venta;
						dif[3] = amountPercentage({num1: sale[3], num2: sale_[3]});
					}

				} else if (_data[j].product_id == '11620304') {
					//D2
					if(_data[j].type == 'actual') {
						sale[4] = _data[j].neto_venta;
					} else {
						sale_[4] = _data[j].neto_venta;
						dif[4] = amountPercentage({num1: sale[4], num2: sale_[4]});
					}

				} else if (_data[j].product_id == '11620307') {
					//GLP
					if(_data[j].type == 'actual') {
						sale[5] = _data[j].neto_venta;
					} else {
						sale_[5] = _data[j].neto_venta;
						dif[5] = amountPercentage({num1: sale[5], num2: sale_[5]});
					}
					
					console.log('0.5: '+sale[5]);

				} else if (_data[j].product_id == '11620308') {
					//GNV
					if(_data[j].type == 'actual') {
						if (_data[j].neto_venta != '') {
							sale[6] = _data[j].neto_venta;
						}
					} else {
						if (_data[j].neto_venta != '') {
							sale_[6] = _data[j].neto_venta;
							dif[6] = amountPercentage({num1: sale[6], num2: sale_[6]});
						}
					}
				}
				if(_data[j].type == 'actual' && _data[j].product_id != 'MARKET') {
					sale[7] += parseFloat(_data[j].neto_venta != '' || _data[j].neto_venta != null ? _data[j].neto_venta : 0);
					console.log('(actual) _data[j].neto_venta: '+_data[j].neto_venta+', sale[7]: '+sale[7]);
				} else if(_data[j].product_id != 'MARKET') {
					sale_[7] += parseFloat(_data[j].neto_venta != '' || _data[j].neto_venta != null ? _data[j].neto_venta : 0);
					dif[7] = amountPercentage({num1: sale[7], num2: sale_[7]});
					dif[7] = parseFloat(dif[7]);
					console.log('(anterior) _data[j].neto_venta: '+_data[j].neto_venta+', sale[7]: '+sale[7]+', sale_[7]: '+sale_[7]+', dif[7]: '+dif[7]);
				}

				if (_data[j].product_id == 'MARKET') {
					//GNV
					if(_data[j].type == 'actual') {
						if (_data[j].neto_venta != '') {
							sale[8] = _data[j].neto_venta;
						}
					} else {
						if (_data[j].neto_venta != '') {
							sale_[8] = _data[j].neto_venta;
							dif[8] = amountPercentage({num1: sale[8], num2: sale_[8]});
						}
					}
				}

				console.log('stations.name: '+stations[i].name);
				if (j == 0) {
					text[0] = stations[i].name;
				} else if (j == 1) {
					text[1] = 'Anterior';
				}

			}
		} else if (type == 'money-exclude') {
			for (var j = 0; j < _countData; j++) {
				console.log('neto venta: ('+j+') '+_data[j].neto_venta);
				console.log('importe_ci: '+_data[j].importe_ci);
				console.log('cantidad_ci: '+_data[j].cantidad_ci);

				if (_data[j].product_id == '11620301') {
					//84
					if(_data[j].type == 'actual') {
						sale[0] = _data[j].neto_venta - _data[j].importe_ci;
					} else {
						sale_[0] = _data[j].neto_venta - _data[j].importe_ci;
						dif[0] = amountPercentage({num1: sale[0], num2: sale_[0]});
					}

				} else if (_data[j].product_id == '11620302') {
					//90
					if(_data[j].type == 'actual') {
						sale[1] = _data[j].neto_venta - _data[j].importe_ci;
					} else {
						sale_[1] = _data[j].neto_venta - _data[j].importe_ci;
						dif[1] = amountPercentage({num1: sale[1], num2: sale_[1]});
					}

				} else if (_data[j].product_id == '11620305') {
					//95
					if(_data[j].type == 'actual') {
						sale[2] = _data[j].neto_venta - _data[j].importe_ci;
					} else {
						sale_[2] = _data[j].neto_venta - _data[j].importe_ci;
						dif[2] = amountPercentage({num1: sale[2], num2: sale_[2]});
					}

				} else if (_data[j].product_id == '11620303') {
					//97
					if(_data[j].type == 'actual') {
						sale[3] = _data[j].neto_venta - _data[j].importe_ci;
					} else {
						sale_[3] = _data[j].neto_venta - _data[j].importe_ci;
						dif[3] = amountPercentage({num1: sale[3], num2: sale_[3]});
					}

				} else if (_data[j].product_id == '11620304') {
					//D2
					if(_data[j].type == 'actual') {
						sale[4] = _data[j].neto_venta - _data[j].importe_ci;
					} else {
						sale_[4] = _data[j].neto_venta - _data[j].importe_ci;
						dif[4] = amountPercentage({num1: sale[4], num2: sale_[4]});
					}

				} else if (_data[j].product_id == '11620307') {
					//GLP
					if(_data[j].type == 'actual') {
						sale[5] = _data[j].neto_venta - _data[j].importe_ci;
					} else {
						sale_[5] = _data[j].neto_venta - _data[j].importe_ci;
						dif[5] = amountPercentage({num1: sale[5], num2: sale_[5]});
					}

				} else if (_data[j].product_id == '11620308') {
					//GNV
					if (_data[j].neto_venta != '') {
						if(_data[j].type == 'actual') {
							sale[6] = _data[j].neto_venta - _data[j].importe_ci;
						} else {
							sale_[6] = _data[j].neto_venta - _data[j].importe_ci;
							dif[6] = amountPercentage({num1: sale[6], num2: sale_[6]});
						}
					}
				}
				//sale[7] += parseFloat(_data[j].neto_venta != '' ? _data[j].neto_venta : 0) - parseFloat(_data[j].importe_ci);//menos importe_ci
				//sale_[7]
				//dif[7]
				if(_data[j].type == 'actual' && _data[j].product_id != 'MARKET') {
					sale[7] += parseFloat(_data[j].neto_venta != '' ? _data[j].neto_venta : 0) - parseFloat(_data[j].importe_ci);
				} else if(_data[j].product_id != 'MARKET') {
					sale_[7] += parseFloat(_data[j].neto_venta != '' ? _data[j].neto_venta : 0) - parseFloat(_data[j].importe_ci);
					dif[7] = amountPercentage({num1: sale[7], num2: sale_[7]});
					dif[7] = parseFloat(dif[7]);
				}

				if (_data[j].product_id == 'MARKET') {
					//GNV
					if (_data[j].neto_venta != '') {
						if(_data[j].type == 'actual') {
							sale[8] = _data[j].neto_venta - _data[j].importe_ci;
						} else {
							sale_[8] = _data[j].neto_venta - _data[j].importe_ci;
							dif[8] = amountPercentage({num1: sale[8], num2: sale_[8]});
						}
					}
				}

				console.log('stations.name: '+stations[i].name);
				if (j == 0) {
					text[0] = stations[i].name;
				} else if (j == 1) {
					text[1] = 'Anterior';
				}

			}
		}

		text[2] = 'Diferencia (%)';

		html += renderRowTableStatistics({
			col_0: text[0],
			col_1: sale[0],
			col_2: sale[1],
			col_3: sale[2],
			col_4: sale[3],
			col_5: sale[4],
			col_6: sale[5],
			col_7: sale[6],
			col_8: sale[7],
			col_9: sale[8],
			format: '0,0',
			attr: ' title="Periodo actual: '+pr.dateBegin+' - '+pr.dateEnd+'"',
		});

		html += renderRowTableStatistics({
			col_0: text[1],
			col_1: sale_[0],
			col_2: sale_[1],
			col_3: sale_[2],
			col_4: sale_[3],
			col_5: sale_[4],
			col_6: sale_[5],
			col_7: sale_[6],
			col_8: sale_[7],
			col_9: sale_[8],
			format: '0,0',
			attr: ' title="Periodo anterior: '+pr._dateBegin+' - '+pr._dateEnd+'"',
		});

		html += renderRowTableStatistics({
			col_0: text[2],
			col_1: dif[0],
			col_2: dif[1],
			col_3: dif[2],
			col_4: dif[3],
			col_5: dif[4],
			col_6: dif[5],
			col_7: dif[6],
			col_8: dif[7],
			col_9: dif[8],
			format: '0.00',
			attr: ' class="col-dif" title=""',
		});

		sale_total[0] += parseFloat(sale[0]);
		sale_total[1] += parseFloat(sale[1]);
		sale_total[2] += parseFloat(sale[2]);
		sale_total[3] += parseFloat(sale[3]);
		sale_total[4] += parseFloat(sale[4]);
		sale_total[5] += parseFloat(sale[5]);
		sale_total[6] += parseFloat(sale[6]);
		sale_total[7] += parseFloat(sale[7]);
		sale_total[8] += parseFloat(sale[8]);

		sale__total[0] += parseFloat(sale_[0]);
		sale__total[1] += parseFloat(sale_[1]);
		sale__total[2] += parseFloat(sale_[2]);
		sale__total[3] += parseFloat(sale_[3]);
		sale__total[4] += parseFloat(sale_[4]);
		sale__total[5] += parseFloat(sale_[5]);
		sale__total[6] += parseFloat(sale_[6]);
		sale__total[7] += parseFloat(sale_[7]);
		sale__total[8] += parseFloat(sale_[8]);

		dif_total[0] = amountPercentage({num1: sale_total[0], num2: sale__total[0]});
		dif_total[1] = amountPercentage({num1: sale_total[1], num2: sale__total[1]});
		dif_total[2] = amountPercentage({num1: sale_total[2], num2: sale__total[2]});
		dif_total[3] = amountPercentage({num1: sale_total[3], num2: sale__total[3]});
		dif_total[4] = amountPercentage({num1: sale_total[4], num2: sale__total[4]});
		dif_total[5] = amountPercentage({num1: sale_total[5], num2: sale__total[5]});
		dif_total[6] = amountPercentage({num1: sale_total[6], num2: sale__total[6]});
		dif_total[7] = amountPercentage({num1: sale_total[7], num2: sale__total[7]});
		dif_total[8] = amountPercentage({num1: sale_total[8], num2: sale__total[8]});

		//html += '</tr>';
	};
	html += '</tbody>';
	html += '<tfoot>';
	html += '<tr class="header-table-sumary" style="font-weight: bold;">';
	html += '<td>Totales</td>';
	html += '<td align="right">'+numeral(sale_total[0]).format('0,0')+'</td>';
	html += '<td align="right">'+numeral(sale_total[1]).format('0,0')+'</td>';
	html += '<td align="right">'+numeral(sale_total[2]).format('0,0')+'</td>';
	html += '<td align="right">'+numeral(sale_total[3]).format('0,0')+'</td>';
	html += '<td align="right">'+numeral(sale_total[4]).format('0,0')+'</td>';
	html += '<td align="right">'+numeral(sale_total[5]).format('0,0')+'</td>';
	html += '<td align="right">'+numeral(sale_total[6]).format('0,0')+'</td>';
	html += '<td align="right">'+numeral(sale_total[7]).format('0,0')+'</td>';
	html += '<td align="right">'+numeral(sale_total[8]).format('0,0')+'</td>';
	html += '</tr>';

	html += '<tr class="header-table-sumary" style="font-weight: bold;">';
	html += '<td>Anterior</td>';
	html += '<td align="right">'+numeral(sale__total[0]).format('0,0')+'</td>';
	html += '<td align="right">'+numeral(sale__total[1]).format('0,0')+'</td>';
	html += '<td align="right">'+numeral(sale__total[2]).format('0,0')+'</td>';
	html += '<td align="right">'+numeral(sale__total[3]).format('0,0')+'</td>';
	html += '<td align="right">'+numeral(sale__total[4]).format('0,0')+'</td>';
	html += '<td align="right">'+numeral(sale__total[5]).format('0,0')+'</td>';
	html += '<td align="right">'+numeral(sale__total[6]).format('0,0')+'</td>';
	html += '<td align="right">'+numeral(sale__total[7]).format('0,0')+'</td>';
	html += '<td align="right">'+numeral(sale__total[8]).format('0,0')+'</td>';
	html += '</tr>';

	html += '<tr class="col-dif" style="font-weight: bold;">';
	html += '<td> '+text[2]+'</td>';
	html += '<td align="right">'+numeral(dif_total[0]).format('0.00')+'</td>';
	html += '<td align="right">'+numeral(dif_total[1]).format('0.00')+'</td>';
	html += '<td align="right">'+numeral(dif_total[2]).format('0.00')+'</td>';
	html += '<td align="right">'+numeral(dif_total[3]).format('0.00')+'</td>';
	html += '<td align="right">'+numeral(dif_total[4]).format('0.00')+'</td>';
	html += '<td align="right">'+numeral(dif_total[5]).format('0.00')+'</td>';
	html += '<td align="right">'+numeral(dif_total[6]).format('0.00')+'</td>';
	html += '<td align="right">'+numeral(dif_total[7]).format('0.00')+'</td>';
	html += '<td align="right">'+numeral(dif_total[8]).format('0.00')+'</td>';
	html += '</tr>';

	html += '</tfoot>'
	+'</table></div><br><div class="graphics-'+type+'"></div><br>';

	return html;
}

function renderRowTableStatistics(data) {
	var html = '<tr'+data.attr+'><th scope="row">'+data.col_0+'</th>';
	html += '<td align="right">'+numeral(data.col_1).format(data.format)+'</td>';
	html += '<td align="right">'+numeral(data.col_2).format(data.format)+'</td>';
	html += '<td align="right">'+numeral(data.col_3).format(data.format)+'</td>';
	html += '<td align="right">'+numeral(data.col_4).format(data.format)+'</td>';
	html += '<td align="right">'+numeral(data.col_5).format(data.format)+'</td>';
	html += '<td align="right">'+numeral(data.col_6).format(data.format)+'</td>';
	html += '<td align="right">'+numeral(data.col_7).format(data.format)+'</td>';
	html += '<td align="right">'+numeral(data.col_8).format(data.format)+'</td>';
	html += '<td align="right">'+numeral(data.col_9).format(data.format)+'</td></tr>';
	return html;
}

function renderGraphicStatistics(type, paramsRequest) {
	$('.'+type).append('<div class="btn-download-'+type+'"></div>');
	//var ctx = document.getElementById('my-chart-'+type).getContext('2d');

	for (var i = 0; i < stationColor.length; i++) {
		console.log('stationColor: '+stationColor[i]);
	};
	var par;

	if (type == 'money-include') {
		label = 'Soles';
		par = 0;
	} else if (type == 'money-exclude') {
		label = 'Soles';
		par = 1;
	}

	$('.btn-download-'+type).append('<button class="btn btn-success btn-block btn-lg download-statistics download-statistics-'+type+'" title="Generar información en Hoja de Cálculo"><span class="glyphicon glyphicon-download-alt"></span> Hoja de Cálculo</button>');
	$('.download-statistics-'+type).attr('data-typestation',paramsRequest.typeStation).attr('data-enddate2',paramsRequest.dateEnd).attr('data-begindate2',paramsRequest.dateBegin).attr('data-enddate1',paramsRequest._dateEnd).attr('data-begindate1',paramsRequest._dateBegin).attr('data-station',paramsRequest.id).attr('data-typecost',paramsRequest.typeCost).attr('data-qtysale',paramsRequest.qtySale).attr('data-include',par);
}

function downloadStatistics(t) {
	console.log('downloadStatistics');
	console.log(t);
	var dateB1 = t.attr('data-begindate1').split("/");
	dateB1 = dateB1[0] + '-' + dateB1[1] + '-' + dateB1[2];

	var dateE1 = t.attr('data-enddate1').split("/");
	dateE1 = dateE1[0] + '-' + dateE1[1] + '-' + dateE1[2];


	var dateB2 = t.attr('data-begindate2').split("/");
	dateB2 = dateB2[0] + '-' + dateB2[1] + '-' + dateB2[2];

	var dateE2 = t.attr('data-enddate2').split("/");
	dateE2 = dateE2[0] + '-' + dateE2[1] + '-' + dateE2[2];

	console.log('dateB1: '+dateB1+', dateE1: '+dateE1);

	//validaciones
	var params = {
		id: t.attr('data-station') == '*' ? 'a' : t.attr('data-station'),
		beginDate: dateB1,
		endDate: dateE1,
		beginDate_: dateB2,
		endDate_: dateE2,
		typeStation: t.attr('data-typestation'),
		qtySale: t.attr('data-qtysale'),
		typeCost: t.attr('data-typecost'),
		typeResult: 1,
		include: t.attr('data-include'),
	};
	console.log('params.beginDate: '+params.beginDate+', params.endDate: '+params.endDate);
	var url_ = url+'reports/generateCaclStatistics/'+params.id+'/'+params.beginDate+'/'+params.endDate+'/'+params.beginDate_+'/'+params.endDate_+'/'+params.typeStation+'/'+params.qtySale+'/'+params.typeCost+'/'+params.include;
	console.log('url_: '+url_);
	window.location = url_;
}

/**
 * Resumen de margen por lineas (ventas/market_productos_linea)
 */
function searchLineProduct() {
	var params = {
		id: $('#select-station').val(),
		dateBegin: $('#start-date-request').val(),
		dateEnd: $('#end-date-request').val(),

		typeStation: $('#typeStation').val(),
		isMarket: $('#data-ismarket').val(),
		qtySale: $('#qty_sale').val(),
		typeCost: $('#type_cost').val(),
		typeResult: 1,
	};
	$.post(url+'requests/getStationLines', params, function(data) {
		console.log(data);
		var req = {
			id: params.id,
			startDate: params.dateBegin,
			endDate: params.dateEnd,
		};
		var _prod = data.dataProducts;
		console.log('_prod.length:');
		console.log(_prod.length);
		if (_prod.length < 1) {
			$('.btn-search-sale').prop('disabled', false);
			$('.result-search').html('<div class="alert alert-info">No existe información</div>');
			return false;
		} else {
			var html = templateTableLineProduct(data,req);
			$('.result-search').html(html);
		}
		$('.btn-search-sale').prop('disabled', false);
		//$('.container-ss-station').removeClass('none');
	}, 'json');
}

function templateTableLineProduct(data, req) {
	var neto_cantidad = 0, neto_venta = 0, consumo_galon = 0, utilidad = 0;
	var html = '<br><div class="table-responsive" style="background-color: #fff; border-radius: 4px; padding: 5px;">';
	html += '<table class="table table-striped">';
	html += '<thead>';
	html += '<tr class="header-table-sumary">';
	html += '<th>Línea</th>';
	html += '<th style="text-align: right;">Cantidad</th>';
	html += '<th style="text-align: right;">Venta</th>';
	html += '<th style="text-align: right;">Costo</th>';
	html += '<th style="text-align: right;">Margen</th>';
	html += '</tr>'
	html += '</thead>';
	html += '<tbody class="result-line-product">';
	console.log(data.dataProducts);

	var dataProducts = data.dataProducts;
	for (var i = 0; i < dataProducts.length; i++) {
		html += renderRowTableLineProduct(dataProducts[i], req, true);
		neto_cantidad += parseFloat(dataProducts[i].neto_cantidad);
		neto_venta += parseFloat(dataProducts[i].neto_venta);
		consumo_galon += parseFloat(dataProducts[i].consumo_galon);
		utilidad += parseFloat(dataProducts[i].utilidad);
	};

	html += renderRowTableLineProduct({
		name: 'TOTAL',
		neto_cantidad: neto_cantidad,
		neto_venta: neto_venta,
		consumo_galon: consumo_galon,
		utilidad: utilidad,
	}, {}, false);

	html += '</tbody>';
	html += '</table>';
	html += '</div>';
	return html;
}

function renderRowTableLineProduct(data,req,isLink) {
	var html = '<tr>';
	if (isLink) {
		html += '<th scope="row"><a class="search-detail-products-line" data-start-date="'+req.startDate+'" data-end-date="'+req.endDate+'" data-id="'+req.id+'" data-line-id="'+data.code+'" data-line-name="'+data.product+'" title="Ver detalle">'+data.product+'</a></th>';
	} else {
		html += '<th scope="row">'+data.name+'</th>';
	}
	html += '<td align="right">'+numeral(data.neto_cantidad).format('0,0')+'</td>';
	html += '<td align="right">'+numeral(data.neto_venta).format('0,0')+'</td>';
	html += '<td align="right">'+numeral(data.consumo_galon).format('0,0')+'</td>';
	html += '<td align="right">'+numeral(data.utilidad).format('0,0')+'</td></tr>';
	return html;
}

function searchDetailProductsLine(t) {
	setContendModal('#normal-modal', '.modal-title', 'Cargando...', true);
	setContendModal('#normal-modal', '.modal-body', loading(), true);
	setContendModal('#normal-modal', '.modal-footer', '<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>', true);
	$('#normal-modal').modal();
	var params = {
		id: t.attr('data-id'),
		lineId: t.attr('data-line-id'),
		lineName: t.attr('data-line-name'),
		startDate: t.attr('data-start-date'),
		endDate: t.attr('data-end-date'),
		typeStation: 1,
		typeCost: 'avg',
	};
	console.log('console de parametros');
	console.log(params);
	$.post(url+'requests/getStationProductsLine', params, function(data) {
		console.log(data);
		var req = {
			id: params.id,
			startDate: params.startDate,
			endDate: params.endDate,
		};
		var html = templateTableProductLine(data,req);
		//$('.container-search').html(html);

		setContendModal('#normal-modal', '.modal-title', params.lineName, true);
		setContendModal('#normal-modal', '.modal-body', html, true);
		//var btn = '<button type="button" class="btn btn-success"><span class="glyphicon glyphicon-download-alt"></span> Hoja de Cálculo</button>';
		var btn = '';
		btn += '<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>';
		setContendModal('#normal-modal', '.modal-footer', btn, true);
	}, 'json');
}

function templateTableProductLine(data,req) {
	var neto_cantidad = 0, neto_venta = 0, consumo_galon = 0, utilidad = 0;
	var html = '<div class="row">';
	html += '<div class="col-md-6"><label>Fecha Inicio: </label> '+req.startDate+'</div>';
	html += '<div class="col-md-6"><label>Fecha Final: </label> '+req.endDate+'</div>';
	html += '</div>';
	html += '<br><div class="table-responsive" style="background-color: #fff; border-radius: 4px; padding: 5px;">';
	html += '<table class="table table-striped">';
	html += '<thead>';
	html += '<tr class="header-table-sumary">';
	html += '<th>Estación</th>';
	html += '<th>Producto</th>';
	html += '<th>Cantidad</th>';//cantidad vendida
	html += '<th>Venta</th>';//cantidad ingresada
	html += '<th>Costo</th>';//precio
	html += '<th>Margen</th>';//costo
	//stock
	html += '</tr>';
	html += '</thead>';
	html += '<tbody class="result-line-product">';
	console.log(data.stations);

	var stations = data.stations;
	for (var i = 0; i < stations.length; i++) {
		html += renderRowTableProductLine(stations[i], req);
		//agregar linea de total, asi como (templateTableLineProduct)
		console.log('stations[i]:');
		console.log(stations[i]);
		var _stations = sumRowTableProductLine(stations[i]);

		neto_cantidad += parseFloat(_stations.neto_cantidad);
		neto_venta += parseFloat(_stations.neto_venta);
		consumo_galon += parseFloat(_stations.consumo_galon);
		utilidad += parseFloat(_stations.utilidad);

		console.log('_stations.neto_cantidad');
		console.log(_stations.neto_cantidad);
		console.log('_stations.neto_venta');
		console.log(_stations.neto_venta);
		console.log('_stations.consumo_galon');
		console.log(_stations.consumo_galon);
		console.log('_stations.utilidad');
		console.log(_stations.utilidad);
	};

	html += `<th scope="row">TOTAL</th>
	<td></td>
	<td align="right">${numeral(neto_cantidad).format('0,0')}</td>
	<td align="right">${numeral(neto_venta).format('0,0')}</td>
	<td align="right">${numeral(consumo_galon).format('0,0')}</td>
	<td align="right">${numeral(utilidad).format('0,0')}</td></tr>`;

	html += '</tbody>';
	html += '</table>';
	html += '</div>';
	return html;
}

function renderRowTableProductLine(data,req) {
	console.log('renderRowTableProductLine!');
	console.log(data);
	var dataProducts = data.data;
	console.log('dataProducts count: '+dataProducts.length);
	console.log(dataProducts);
	var html = '';
	for (var i = 0; i < dataProducts.length; i++) {
		html += '<tr>';
		html += '<th scope="row">'+data.name+'</th>';
		html += '<td>'+dataProducts[i].product_name+'</td>';
		html += '<td align="right">'+numeral(dataProducts[i].neto_cantidad).format('0,0')+'</td>';
		html += '<td align="right">'+numeral(dataProducts[i].neto_venta).format('0,0')+'</td>';
		html += '<td align="right">'+numeral(dataProducts[i].consumo_galon).format('0,0')+'</td>';
		html += '<td align="right">'+numeral(dataProducts[i].utilidad).format('0,0')+'</td></tr>';
	};
	return html;
}

function sumRowTableProductLine(data) {
	console.log('sumRowTableProductLine!');
	var neto_cantidad = 0, neto_venta = 0, consumo_galon = 0, utilidad = 0;
	console.log(data);
	var dataProducts = data.data;
	for (var i = 0; i < dataProducts.length; i++) {
		neto_cantidad += parseFloat(dataProducts[i].neto_cantidad);
		neto_venta += parseFloat(dataProducts[i].neto_venta);
		consumo_galon += parseFloat(dataProducts[i].consumo_galon);
		utilidad += parseFloat(dataProducts[i].utilidad);
	};
	return {neto_cantidad: neto_cantidad, neto_venta: neto_venta, consumo_galon: consumo_galon, utilidad: utilidad};
}


function searchMerchandise(_this, isQty) {
	$('.result-search').html('<br><br>'+loading());
	console.log(_this);
	var th1 = '';
	var th2 = '';
	var td = '';
	var params = {
		orgId: $('#select-station').val(),
		startDate: $('#start-date-request').val(),
		endDate: $('#start-date-request').val(),
	};
	if (isQty) { 
		$('.btn-search-merchandise').prop('disabled', false);
	} else {
		$('.btn-search-merchandise-sale').prop('disabled', false);
	}
	$.post(url+'requests/getMovementsByOrgId', params, function(data) {
		console.log('[searchMerchandise]');
		console.log(data);
		checkSession(data);
		if (data.status == 4) {
			$('.result-search').html(templateTableSearchMerchandise(data, isQty));
		} else {
			$('.result-search').html('No existen resultados');
		}
	}, 'json');
}

function templateTableSearchMerchandise(data, is) {
	console.log('-> templateTableSearchMerchandise IS: ');
	console.log(is);
	var _products = data._products;
	var _dataProducts = data._dataProducts;
	var _orgs = data._orgs;

	var dataStation = data.dataStation;
	var countStations = data.stations;
	var products = data.products;
	console.log('estaciones:');
	console.log(countStations);
	var stations = countStations;
	countStations = countStations.length;
	console.log('cantidad de estaciones: '+countStations);
	var countStations_ = countStations * 2;
	var headStations = '';
	var headStations_ = '';
	var bodyProducts = '';
	var bodyData = '';

	console.log('------->');
	console.log(dataStation);
	console.log('------->');
	
	console.log('bodyData');
	console.log(bodyData);
	var bodyData_ = [];
	var _bodyData = '';
	var cad = '';

	for (var key in _orgs) {
		headStations += '<th colspan="2">'+_orgs[key].name+'</th>';
		console.log('concatenado con html (th): '+headStations);
		headStations_ += '<th>STOCK</th><th>VENTA</th>';
	}

	console.log('bodyData_:');
	console.log(bodyData_);

	for (var key1 in _products) {
		bodyProducts += `<tr><th scope="row">${_products[key1].product_code}</th>
			<td>${_products[key1].productgroup_name}</td>
			<td>${_products[key1].product_name}</td>
			<td>${_products[key1].uom_name}</td>`;
		var org = _dataProducts[key1];
		for (key2 in org) {
			if (is) {
				bodyProducts += `<td>${org[key2]._stk_real}</td><td>${org[key2]._countsale}</td>`;
			} else {
				bodyProducts += `<td>S/ ${org[key2]._amount_real}</td><td>S/ ${org[key2]._amountsale}</td>`;
			}
		}
		bodyProducts += `</tr>`;
	}

	console.log('bodyProducts:');
	console.log(bodyProducts);

	console.log(headStations);
	console.log(headStations_);
	var html = `<br><div class="table-responsive">
	<table class="table table-striped">
		<thead>
			<tr>
				<th colspan="3">CONSOLIDADOS TODAS LAS EESS</th>
				<th></th>
				${headStations}
			</tr>
			<tr>
				<th>Código</th>
				<th>Linea</th>
				<th>Producto</th>
				<th>Unidad de Medida</th>
				${headStations_}
			</tr>
		</thead>
		<tbody>
			${bodyProducts}
		</tbody>
	</table>
	</div>`;
	console.log(html);
	return html;
}

/**
 * Add
 */
function loadModalAddClient(_this) {
	setContendModal('#normal-modal', '.modal-title', 'Cargando...', true);
	setContendModal('#normal-modal', '.modal-body', loading(), true);
	setContendModal('#normal-modal', '.modal-footer', '<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>', true);
	$('#normal-modal').modal();
	$.ajax({
		url: url+'configuration/viewClientAdd',
		type: 'POST',
		//dataType: 'default: Intelligent Guess (Other values: xml, json, script, or html)',
		data: {param1: 'value1'},
		success: function(data) {
			console.log('loadModalAddClient');
			console.log(data);
			setContendModal('#normal-modal', '.modal-title', 'Agregar Cliente', true);
			setContendModal('#normal-modal', '.modal-body', data, true);
			setContendModal('#normal-modal', '.modal-footer', '<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>', true);
		}
	});
}

function loadModalAddOrg(_this) {
	setContendModal('#normal-modal', '.modal-title', 'Cargando...', true);
	setContendModal('#normal-modal', '.modal-body', loading(), true);
	setContendModal('#normal-modal', '.modal-footer', '<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>', true);
	$('#normal-modal').modal();
}

function loadModalAddWarehouse(_this) {
	setContendModal('#normal-modal', '.modal-title', 'Cargando...', true);
	setContendModal('#normal-modal', '.modal-body', loading(), true);
	setContendModal('#normal-modal', '.modal-footer', '<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>', true);
	$('#normal-modal').modal();
}

/**
 * Edit
 */
function loadModalEditClient(_this) {
	setContendModal('#normal-modal', '.modal-title', 'Cargando...', true);
	setContendModal('#normal-modal', '.modal-body', loading(), true);
	setContendModal('#normal-modal', '.modal-footer', '<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>', true);
	$('#normal-modal').modal();
}

function loadModalEditOrg(_this) {
	setContendModal('#normal-modal', '.modal-title', 'Cargando...', true);
	setContendModal('#normal-modal', '.modal-body', loading(), true);
	setContendModal('#normal-modal', '.modal-footer', '<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>', true);
	$('#normal-modal').modal();
}

function loadModalEditWarehouse(_this) {
	setContendModal('#normal-modal', '.modal-title', 'Cargando...', true);
	setContendModal('#normal-modal', '.modal-body', loading(), true);
	setContendModal('#normal-modal', '.modal-footer', '<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>', true);
	$('#normal-modal').modal();
}









/**
 ************************
 * Funciones OCSManager *
 ************************
 * A partir de esta linea se implementan las funciones generales para OCSManager
 * Funciones para todo el frontend
 */


/**
 * Limpia arreglos y objetos de estaciones
 */
function clearStations() {
	stations = [];
	stationsDesc = [];
	stationsTotal = [];
	stationsQty = [];
	stationsUtil = [];
	stationsPor = [];
	stationColor = [];
	dataStations = [];
	nameStation = {};
	porStation = {};
	gran_total = 0.0;

}

function clearDataResumen() {
	stationColor = [];
	totalProductsInclude = [];
	totalProductsExclude = [];
	quiantityProductInclude = [];
	quiantityProductExclude = [];
}

function loading() {
	return '<div class="spinner">'
	+'<div class="bounce1"></div>'
	+'<div class="bounce2"></div>'
	+'<div class="bounce3"></div>'
	+'</div>';
}

/**
 * Generar color aleatorio
 * @return string color
 */
function getRandomColor() {
	var letters = '0123456789ABCDEF';
	var color = '#';
	for(var i = 0; i < 6; i++ ) {
		color += letters[Math.floor(Math.random() * 16)];
	}
	return color;
}

/**
 * Copia de función converterUM(helper php)
 */
function converterUM(data) {
	if(data.type == 0) {
		return data.co / 3.7853;//11620307 - GLP
	} else if(data.type == 1) {
		return data.co / 3.15;//11620308 - GNV
	} else {
		return data.co;
	}
}

/**
 * Obtener Colores de combutible
 * @param string combId, boolean isHEX
 * @return string color
 */
function getColorComb(combId,isHEX) {
	var color = '';
	if(combId == '11620301') {
		//84
		color = '#EB281D';//rojo
	} else if(combId == '11620302') {
		//90
		color = '#36A133';//verde
	} else if(combId == '11620303') {
		//97
		color = '#F76516';//naranja
	} else if(combId == '11620304') {
		//Diesel
		color = '#C8C8C8';//gris
	} else if(combId == '11620305') {
		//95
		color = '#3336A1';//azulino
	} else if(combId == '11620306') {
		//Kerosene
		color = '#384636';//---
	} else if(combId == '11620307') {
		//GLP
		color = '#CEF523';//blanco
	} else if(combId == '11620308') {
		//GNV
		color = '#38AFFA';//celeste
	}
	return color;
}

/**
 * Limpar float en caso sea vacio
 * @param float val
 * @return float
 */
function clearFloat(val) {
	if(val != '') {
		return parseFloat(val);
	} else {
		return parseFloat(0.0);
	}
}

/**
 * Demo GET/JSON para obtener IP del cliente
 * @return string ip
 */
function getIPClient() {
	var ip = '0.0.0.0';
	$.getJSON("http://jsonip.com/?callback=?", function (data) {
		console.log(data);
		console.log(data.ip);
		ip = data.ip;
	});
	return ip;
}

/**
 * Verifica si existe una sesion activa en el servidor por medio del callback POST/JSON
 * @param obj data
 */
function checkSession(data) {
	if(data.status == 101) {
		window.location = url+'secure/login';
	}
}

function actionExclude(t, type) {
	console.log('type: '+type);
	console.log('data-action: '+t.attr('data-action'));
	if (type == 0) {
		if (t.attr('data-action') == 'true') {
			//$('.download-sumary').attr('data-include',2);
			console.log('excluir si 0');
			$('.money-include').addClass('none');
			$('.money-exclude').removeClass('none');

			$('.btn-money-exclude').removeClass('btn-default').removeClass('btn-success');
			$('.btn-money-exclude.true').addClass('btn-success');
			$('.btn-money-exclude.false').addClass('btn-default');
		} else {
			//$('.download-sumary').attr('data-include',3);
			console.log('excluir no 0');
			$('.money-exclude').addClass('none');
			$('.money-include').removeClass('none');

			$('.btn-money-include').removeClass('btn-default').removeClass('btn-success');
			$('.btn-money-include.false').addClass('btn-success');
			$('.btn-money-include.true').addClass('btn-default');
		}

		/*$('.btn-money-exclude').removeClass('btn-success');
		$('.btn-money-include').addClass('btn-default');*/
	} else {
		if (t.attr('data-action') == 'true') {
			//$('.download-sumary').attr('data-include',1);
			console.log('excluir si 1');
			$('.quantity-include').addClass('none');
			$('.quantity-exclude').removeClass('none');

			$('.btn-quantity-exclude').removeClass('btn-default').removeClass('btn-success');
			$('.btn-quantity-exclude.true').addClass('btn-success');
			$('.btn-quantity-exclude.false').addClass('btn-default');
		} else {
			//$('.download-sumary').attr('data-include',0);
			console.log('excluir no 1');
			$('.quantity-exclude').addClass('none');
			$('.quantity-include').removeClass('none');

			$('.btn-quantity-include').removeClass('btn-default').removeClass('btn-success');
			$('.btn-quantity-include.false').addClass('btn-success');
			$('.btn-quantity-include.true').addClass('btn-default');
		}

		/*$('.btn-quantity-exclude').removeClass('btn-success');
		$('.btn-quantity-include').addClass('btn-default');*/
	}
	/*t.removeClass('btn-default');
	t.addClass('btn-success');*/
}

function  amountPercentage(data) {
	if(data.num1 == 0 && data.num2 == 0) {
		return 0;
	} else if(data.num1 > 0 && data.num2 == 0) {
		return 100;
	} else if(data.num1 == 0 && data.num2 > 0) {
		return -100;
	} else {
		return (((data.num1*100)/data.num2) - 100);
	}
}