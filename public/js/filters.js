jQuery(function( $ ) {		
	
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