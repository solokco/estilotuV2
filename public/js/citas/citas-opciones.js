jQuery(function( $ ) {		
	
	function get_id( object_selected , parent_object ){
		
		var parent_id = jQuery(object_selected).closest(parent_object).attr("id");
		var element_id = parent_id.split("_");
		element_id = element_id[element_id.length-1];
		
		return element_id;
		
	}
	
	jQuery("body").on( "change", ".appoinment_user_assist , .appoinment_user_pay"  , function( event ) {
		
		var id_cita = get_id( this , "form" );
		var status_payment;
		var status_assist;
		
		if ( document.getElementById("asistencia_" + id_cita).checked ) {
			status_assist = 1;
		} else {
			status_assist = 0;
		}
		
		if ( document.getElementById("pay_" + id_cita).checked ) {
			status_payment = 1;
		} else {
			status_payment = 0;
		}
		
		jQuery( "body" ).after( "<div class='loading'>Loading&#8230;</div>" );
		
		$.ajax({
	        url: ajax_object.ajax_url,
	        type: "POST",
	        dataType: "JSON",
	        data: { 
				action: 'registrar_asistencia_y_pago_participante',
				id_cita: id_cita,
				appoinment_user_assist: status_assist,
				appoinment_user_paid: status_payment
            },      
	         
	        success: function( data, textStatus, jqXHR ) { // Si todo salio bien se ejecuta esto
				
				jQuery(".loading").remove();
			
			}
        })
        
        .fail(function( jqXHR, textStatus, errorThrown, data ) { // Si todo salio MAL se ejecuta esto
			alert('Ocurrio un error y no se pudo procesar su solicitud correctamente.');
			
			jQuery(".loading").remove();

        });

	});
	
	jQuery(".status_single").click( function( event ) {
				
		event.preventDefault();
		
		if ( confirm('Seguro que deseas cambiar el status de esta cita?') ) {
				
			var id_cita = get_id( this , "form" );
			var status = jQuery(this).val();
			
			jQuery( "body" ).after( "<div class='loading'>Loading&#8230;</div>" );
			
			$.ajax({
		        url: ajax_object.ajax_url,
		        type: "POST",
		        dataType: "JSON",
		        data: { 
					action: 'cambiar_status_cita_ajax',
					id_cita: id_cita,
					status:	status
	            },      
		         
		        success: function( data, textStatus, jqXHR ) { // Si todo salio bien se ejecuta esto
					
					jQuery(".loading").remove();
					jQuery("#cita_" + id_cita).remove();
				
				}
	        })
	        
	        .fail(function( jqXHR, textStatus, errorThrown, data ) { // Si todo salio MAL se ejecuta esto
				alert('Ocurrio un error y no se pudo procesar su solicitud correctamente.');
				
				jQuery(".loading").remove();
	
	        });
	        
		}
		
	});
	
	
	//FILTRO	
	var date_from 	= $("#date_from-filter").val();
	var date_to 	= $("#date_to-filter").val();
	
    var dateFormat = "yy-mm-dd",
		
		from = $( "#date_from-filter" ).datepicker({
			defaultDate: "+1",
			changeMonth: true,
			dateFormat: "yy-mm-dd"
			//numberOfMonths: 3
		})
			.datepicker("setDate", new Date( date_from ) )
			.on( "change", function() {
				to.datepicker( "option", "minDate", getDate( this ) );
			}),
			
				
      
		to = $( "#date_to-filter" ).datepicker({
			defaultDate: "+1w",
			changeMonth: true,
			dateFormat: "yy-mm-dd"
			//numberOfMonths: 3
		})
			.datepicker("setDate", new Date( date_to ) )
			.on( "change", function() {
				from.datepicker( "option", "maxDate", getDate( this ) );
			})
			
				
 
	function getDate( element ) {
		var date;
		
		try {
			date = $.datepicker.parseDate( dateFormat, element.value );
		} catch( error ) {
			date = null;
		}
	
		return date;
	}
	
});