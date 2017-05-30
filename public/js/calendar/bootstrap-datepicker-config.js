jQuery(function( $ ) {
	
	var fecha_tope = config_vars.fecha_tope;
	var hora_cierre = config_vars.hora_cierre;
	
	var semana = [0, 1, 2, 3, 4, 5 , 6];
	var dias_inactivos 	= JSON.stringify(config_vars.disponible['dias_desactivados']);
	var dias_activos = [];
	
	function diff(A, B) {
	    return A.filter(function (a) {
	        return B.indexOf(a) == -1;
	    });
	}
	
	dias_activos = diff(semana , dias_inactivos);
	
	jQuery('#datepicker').datepicker({
		language: "es",
		format: 'yyyy-mm-dd',
		startDate: '0',
		endDate: '+'+fecha_tope+'d',
		//daysOfWeekHighlighted: dias_activos,
		daysOfWeekDisabled: dias_inactivos,
	    autoclose: true,
	    todayHighlight: true,
	    toggleActive: true,
	    datesDisabled: []
	})
	
	.on('changeDate', function( event ) {
	    
	    var fecha_servicio = event.format();
	    
	    jQuery( "body" ).after( "<div class='loading'>Loading&#8230;</div>" );
	    
	    		
		jQuery.ajax({
	        url: ajax_object.ajax_url,
	        type: "POST",
	        dataType: "JSON",
	        data: { 
				'action'			: 'cargar_cupos',
				"id_servicio"		: config_vars.id_servicio,
				'fecha_seleccionada': fecha_servicio
            },      
	         
	        success: function( data, textStatus, jqXHR ) { // Si todo salio bien se ejecuta esto

				cupos_disponibles = mostrar_cupos( data , fecha_servicio );

				jQuery(".lista_cupos_disponibles").html( cupos_disponibles );
				jQuery(".loading").remove();
			
			}
        })
        
        .fail(function( jqXHR, textStatus, errorThrown, data ) { // Si todo salio MAL se ejecuta esto
			jQuery("body").removeClass("loading");
			alert('Ocurrio un error y no se pudo procesar su solicitud correctamente.');

        });
	});
	
	
	jQuery('.input-group.date').datepicker({
		language: "es",
		format: 'yyyy-mm-dd',
		startDate: '0',
		endDate: '+'+fecha_tope+'d',
		//daysOfWeekHighlighted: dias_activos,
		daysOfWeekDisabled: dias_inactivos,
	    autoclose: true,
	    todayHighlight: true,
	    toggleActive: true,
	    datesDisabled: []
	});

    
    function formatAMPM(date) {
		var myTime = date.split(":");
		var hours = myTime[0];
		var minutes = myTime[1];
		var ampm = hours >= 12 ? 'pm' : 'am';
		hours = hours % 12;
		hours = hours ? hours : 12; // the hour '0' should be '12'
		//minutes = minutes < 10 ? '0'+minutes : minutes;
		var strTime = hours + ':' + minutes + ' ' + ampm;
		return strTime;
	}
    
    function mostrar_cupos( cupos , dia_seleccionado ) {
		var et_html;
		var ocupados;
		var disponible;
		var tiene_reserva;
		var close_time 		= hora_cierre * 1000;
		var today 			= new Date();
		var hora_actual 	= today.getTime();
		var dia_servicio 	= new Date(dia_seleccionado);

		console.log(cupos);
		
		// si viene vacio o nulo regreso que NO HAY CUPOS
		if ( cupos == 0 || cupos == null )
			return et_html = "<h2 class='sin_cupos'>No hay cupos disponibles para el "+ dia_seleccionado +"</h2>";
			
		et_html = 	"<h2>Cupos disponibles para " + dia_seleccionado + "</h2>";
			
		$.each(cupos.bloque_seleccionado , function( key, obj ) {
        	tiene_reserva = false;
        	disponible = obj.et_meta_cupos;
        	
        	hora_servicio = new Date(dia_seleccionado +" "+ obj.et_meta_hora_inicio).getTime();        	
        	hora_servicio = hora_servicio - close_time;
        	
        	// console.log("Hora actual: " + hora_actual + "; Hora del servicio " + hora_servicio)
        	
        	$.each(cupos.cupos.ocupado , function ( hora , veces_repetido ) {
	        	
	        	if ( hora == obj.et_meta_hora_inicio ) {
		        	disponible = disponible - veces_repetido;
	        	}
	        	
        	});
        	
        	$.each(cupos.cupos.reservado , function ( hora , reservado ) {
	        	
	        	if ( hora == obj.et_meta_hora_inicio && reservado == true ) {
		        	tiene_reserva = true;
	        	}
	        	
        	});
        	
        	et_html += 	"<div class='cupoDisponible v2' id='cupoDisponible_"+ key +"'>";
        	
        	et_html += 		"<form class='formulario_reserva_cupo v2' id='hacer_reserva_"+ key +"' action='"+ config_vars.url_user +"' method='post' >";
        	
        	et_html +=			"<header>"+ formatAMPM(obj.et_meta_hora_inicio) +" </header>";
			et_html +=	       	"<p>Duracion: " + obj.et_meta_duracion +" minutos</p>";
			et_html +=	   		"<p>Cupos Maximo: " + obj.et_meta_cupos +"</p>";
			et_html +=	   		"<p>Cupos Disponibles: "+ disponible +"</p> ";
        	
        	
			et_html +=		    "<input type='hidden' value='"+ config_vars.id_servicio +"' id='id_servicio_"+ config_vars.id_servicio +"' name='id_servicio'>";
			et_html +=		   	"<input type='hidden' value='"+ dia_seleccionado +"' id='servicio_dia_seleccionado' class='servicio_dia_seleccionado' name='servicio_dia_seleccionado'>";
			et_html +=		   	"<input type='hidden' value='"+ obj.et_meta_hora_inicio +"' id='et_meta_hora_inicio' name='et_meta_hora_inicio'>";
			et_html +=		   	"<input type='hidden' value='' id='et_meta_close_time' name='et_meta_close_time'> ";
			et_html +=		   	"<input type='hidden' value='"+ config_vars.token_id +"' id='et_meta_close_time' name='et_token'> ";
        	et_html +=			config_vars.nonce_field;
        	
        	if (tiene_reserva) {
	        	et_html +=		"<input disabled type='submit' value='Ya reservaste' class='button btn-morado servicio_reservado' id='boton_reservar' name='agotado'>"	;
        	}
        	
        	else if (disponible < 1 ) {
				et_html +=		"<input disabled type='submit' value='Cupos agotados' class='button btn-morado servicio_agotado' id='boton_reservar' name='agotado'>";
			}
			
			//else if ( hora_actual > hora_servicio ) {
			//	et_html +=		"<input disabled type='submit' value='Clase cerrada' class='button btn-morado servicio_cerrado' id='boton_reservar' name='agotado'>";
			//}
			
			else {
	        	et_html +=		"<input type='submit' value='reservar' class='button btn-morado' id='boton_reservar' name='is_reserve'>";
        	}
        	
        	et_html += 		"</form>";
        	
        	et_html += 	"</div>";
        	
        	console.log("1000");
        	
		});
		
		return et_html;
		
	}
    
	
});