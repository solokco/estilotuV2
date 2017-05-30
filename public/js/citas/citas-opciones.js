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
	
});