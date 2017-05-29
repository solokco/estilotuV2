jQuery(function( $ ) {			
	
	var cantidad_bloques = 1;
	var dia;
	var dia_id;
	var dia_nombre;
	var clone;
	var regex;
	var element_to_clone = [];
	var sectionsCount;
	
	function get_element_id( id_parent ) {
		dia 		= id_parent.split("_");
		dia_nombre	= dia[0];
		
		switch(dia_nombre) {
		    case "lunes":
		        dia_id = 0;
		        break;
		    
		    case "martes":
		        dia_id = 1;
		        break;
			
			case "miercoles":
		        dia_id = 2;
		        break;
			
			case "jueves":
		        dia_id = 3;
		        break;
			
			case "viernes":
				dia_id = 4;
		        break;
		    
		    case "sabado":
				dia_id = 5;
		        break;    
		        
		    case "domingo":
				dia_id = 6;
		        break;
		}
		
		cantidad_bloques = jQuery(".clone_" + dia_nombre).length;
		element_to_clone = [ id_parent , dia_nombre , dia_id , cantidad_bloques ];
		
		return element_to_clone;
	}
		
	
	/* ************************************ */
	/* ELIMINAR BLOQUE DE HORARIOS		 	*/
	/* ************************************ */
	jQuery("body").on( "change", ".ShowHideReset"  , function( event ) {
		var dia_seleccionado;
		var check_seleccionado;
		
		check_seleccionado = jQuery(this).attr("id") ;
		check_seleccionado = check_seleccionado.split("_");
		
		dia_seleccionado = check_seleccionado[1] ;
		
		if ( this.checked ) {
        	
        	jQuery("#contenedor_" + dia_seleccionado ).show();
        	
		} else {
			
			jQuery(".clone_" + dia_seleccionado + ":not(:first)").remove();
			jQuery("#contenedor_" + dia_seleccionado ).hide();
			
		}
		
	});
	/* ************************************ */
	
	/* ************************************ */
	/* ELIMINAR BLOQUE DE HORARIOS		 	*/
	/* ************************************ */
	jQuery("body").on( "click", "a.delete"  , function( event ) {
		event.preventDefault();
		
		var cantidad_bloques;
		
		element_to_clone = get_element_id ( jQuery(this).closest("table").attr("id") ) ;

		if (element_to_clone[3] > 1 ) {
			
			jQuery(this).parents(".frm-row").remove();
			
			cantidad_bloques = element_to_clone[3] - 1;
			
			jQuery(".clone_" + element_to_clone[1] ).each(function(key, obj) {
			    
			    var disponible 	= "disponible["+ element_to_clone[1] +"][bloque]["+ key +"][et_meta_hora_inicio]";
			    var duracion 	= "disponible["+ element_to_clone[1] +"][bloque]["+ key +"][et_meta_duracion]";
			    var cupos	 	= "disponible["+ element_to_clone[1] +"][bloque]["+ key +"][et_meta_cupos]";
			    var id_table 	= element_to_clone[1] + "_" + element_to_clone[2] + "_" + key;
			    
			    jQuery(this).attr('id', id_table);
			    jQuery(this).find( ".hora_inicio" ).attr('name', disponible);
			    jQuery(this).find( ".duracion" ).attr('name', duracion);
			    jQuery(this).find( ".et_meta_cupos" ).attr('name', cupos);

			});
		}

	});
	/* ************************************ */
	
	/* ************************************ */
	/* CLONACION DE BLOQUE HORARIOS			 	*/
	/* ************************************ */
	jQuery("body").on( "click", "a.clone"  , function( event ) {
		event.preventDefault();

		element_to_clone = get_element_id ( jQuery(this).closest("table").attr("id") ) ;
		
		var clonedElement;
		var cantidad_bloques = parseInt(element_to_clone[3]);
		var id_bloque_nuevo = cantidad_bloques + 1;
		var id_ultimo_bloque = "#" + element_to_clone[1] + "_" + element_to_clone[2] + "_" + id_bloque_nuevo;

		clonedElement = jQuery( "#" + element_to_clone[0] ).clone()
        .insertAfter( jQuery(this).closest("table") )
        .attr("id", element_to_clone[1] + "_" + element_to_clone[2] + "_" + id_bloque_nuevo );
		
		var disponible 	= "disponible["+ element_to_clone[1] +"][bloque]["+ id_bloque_nuevo +"][et_meta_hora_inicio]";
	    var duracion 	= "disponible["+ element_to_clone[1] +"][bloque]["+ id_bloque_nuevo +"][et_meta_duracion]";
	    var cupos	 	= "disponible["+ element_to_clone[1] +"][bloque]["+ id_bloque_nuevo +"][et_meta_cupos]";
		
		jQuery(clonedElement).find( ".hora_inicio" ).attr('name', disponible);
	    jQuery(clonedElement).find( ".duracion" ).attr('name', duracion);
	    jQuery(clonedElement).find( ".et_meta_cupos" ).attr('name', cupos);
		
	})
		
});