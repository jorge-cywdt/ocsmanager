$( document ).ready(function() {
	if($('#username').length) {
		$('#username').focus();
	}

	if( $('#start-date-request').length ) {
		$('#start-date-request').datepicker({
			language: 'es',
			autoclose: true
		});
	}

	$('[data-toggle="popover"]').popover();

	/**
	 * NAME: Bootstrap 3 Multi-Level by Johne
	 * This script will active Triple level multi drop-down menus in Bootstrap 3.*
	 */
	$('li.dropdown-submenu').on('click', function(event) {
		event.stopPropagation();
		if ($(this).hasClass('open')) {
			$(this).removeClass('open');
		} else {
			$('li.dropdown-submenu').removeClass('open');
			$(this).addClass('open');
		}
	});

	$(document).on('click', '.btn-login', function() {
		login();
	});
	$('.keypress').keypress(function( event ) {
		if ( event.which == 13 ) {
			login();
		}
	});

	$(document).on('click', '.btn-search-sale', function() {
		$(this).prop('disabled', true);
		if ($('#typeStation').val() == 3) {
			searchSumarySales($(this).attr('data-ismarket'));
		} else if ($('#typeStation').val() == 4) {
			searchStatisticsSales($(this).attr('data-ismarket'));
		} else if ($('#typeStation').val() == 5) {
			searchLineProduct($(this).attr('data-ismarket'));
		} else {
			searchSale($(this).attr('data-ismarket'));
		}
	});
	$(document).on('click', '.detail-station', function() {
		viewDetailStation($(this));
	});

	/**
	 * Chart demo
	 * viewChart sin uso
	 */
	$(document).on('click', '.view-chart', function() {
		viewChart();
	});

	$(document).on('click', '.show-ltr', function() {
		if($(this).attr('data-ltr')) {
			var title = $(this).attr('data-ltr');
			$(this).prepend(title+' - ');
			$(this).attr('data-ltr', '');
		}
	});

	$(document).on('click', '.download-comb-sales', function() {
		downloadCombSales($(this));
	});

	$(document).on('click', '.all-result-sales-comb', function() {
		detailAllResult($(this));
	});

	$('#event_period').datepicker({
		language: 'es',
		autoclose: true,
		inputs: $('.actual_range')
	});

	$('#_event_period').datepicker({
		language: 'es',
		autoclose: true,
		inputs: $('.previous_range')
	});

	$(document).on('click', '.btn-search-stock', function() {
		console.log('search-stock');
		$(this).prop('disabled', true);
		searchStock($(this).attr('data-ismarket'));
	});

	/**
	 * Evento click sobre tanque para ver detalle
	 *
	 */
	$(document).on('click', '.canvas-tank', function() {
		console.log('canvas-tank');
		//detailInfoTank($(this));
	});

	$(document).on('click', '.resume-info-tank', function() {
		console.log('resume-info-tank');
		showPO($(this),true);
	});

	$(document).on('click', '.download-comb-stock', function() {
		downloadCombStock($(this));
	});

	$(document).on('click', '.download-sumary', function() {
		downloadSumary($(this));
	});

	$(document).on('click', '.btn-money-include, .btn-money-exclude', function() {
		actionExclude($(this), 0);
	});
	$(document).on('click', '.btn-quantity-include, .btn-quantity-exclude', function() {
		actionExclude($(this), 1);
	});

	$(document).on('click', '.download-statistics', function() {
		downloadStatistics($(this));
	});

	$(document).on('click', '.search-detail-products-line', function() {
		console.log('click en search-detail-products-line');
		searchDetailProductsLine($(this));
	});

	$(document).on('click', '.btn-search-merchandise', function() {
		$(this).prop('disabled', true);
		searchMerchandise($(this), true);
	});

	$(document).on('click', '.btn-search-merchandise-sale', function() {
		$(this).prop('disabled', true);
		searchMerchandise($(this), false);
	});

	/**
	 * Add item
	 */
	$(document).on('click', '.add-client', function() {
		loadModalAddClient($(this));
	});
	$(document).on('click', '.add-org', function() {
		loadModalAddOrg($(this));
	});
	$(document).on('click', '.add-warehouse', function() {
		loadModalAddWarehouse($(this));
	});

	/**
	 * Edit item
	 */
	$(document).on('click', '.edit-client', function() {
		loadModalEditClient($(this));
	});
	$(document).on('click', '.edit-org', function() {
		loadModalEditOrg($(this));
	});
	$(document).on('click', '.edit-warehouse', function() {
		loadModalEditWarehouse($(this));
	});
});