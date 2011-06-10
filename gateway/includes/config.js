function BasicConfig() {
	//Forms Config
	$timepickerDefault = {
		timeOnlyTitle: "Escoja la Hora",
		timeText:"Hora",
		hourText:"Hora",
		minuteText:"Minuto",
		secondText:"Segundo",
		currentText:"Ahora",
		closeText:"Cerrar",
		stepMinute: 10
		
	};
	$.datepicker.regional['es'] = {
			closeText: 'Cerrar',
			prevText: '&#x3c;Ant',
			nextText: 'Sig&#x3e;',
			currentText: 'Hoy',
			monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
			'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
			monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun',
			'Jul','Ago','Sep','Oct','Nov','Dic'],
			dayNames: ['Domingo','Lunes','Martes','Mi&eacute;rcoles','Jueves','Viernes','S&aacute;bado'],
			dayNamesShort: ['Dom','Lun','Mar','Mi&eacute;','Juv','Vie','S&aacute;b'],
			dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','S&aacute;'],
			weekHeader: 'Sm',
			firstDay: 1,
			isRTL: false,
			showMonthAfterYear: false,
			yearSuffix: ''};
	$.datepicker.setDefaults($.datepicker.regional['es']);
	
	$('.date').datepicker({
		dateFormat: 'yy-mm-dd',
		showButtonPanel: true
	});
	
	// Element Config
	$('#message').dialog({
		title: $('#level_image').attr('alt'),
		buttons: {"Ok": function() { $(this).dialog("close"); } }
	} );
	
	
	$("#overlay").dialog({
		autoOpen: false,
		width: 700,
		modal: true,
		resizable: true,
		title: $('#overlay h3').attr('innerHTML')
	});
	$( "input:submit, ul.action a").button();
	
	//Behaviors
	$("form:not(.filter) :input:visible:enabled:first").focus();
};

$(document).ready(function(){
	BasicConfig();
	if(typeof ExtendedConfig == 'function') {
		ExtendedConfig();
	}
});
