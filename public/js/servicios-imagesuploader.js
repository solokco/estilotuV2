jQuery(function( $ ) {	
	
		
		function add_message($msg, $type){
			var html = "<div class='alert alert-"+$type+"'>" + $msg + "</div>";
			$(".ibenic_upload_message").empty().append(html);
			$(".ibenic_upload_message").fadeIn();
			setTimeout(function() { $(".ibenic_upload_message").fadeOut("slow"); }, 2000);
		}
		
		// Just to be sure that the input will be called
		$("#ibenic_file_upload").on("click", function(){
		  	$('#ibenic_file_input').click(function(event) {
				event.stopPropagation();
      			});
    		});

		$('#ibenic_file_input').on('change', prepareUpload);

		function prepareUpload(event) { 
			var file = event.target.files;
			var parent = $("#" + event.target.id).parent();
			var data = new FormData();
			
			data.append("action", "ibenic_file_upload");
			
			$.each(file, function(key, value) {
    			data.append("ibenic_file_upload", value);
  			});
  			
  			$.ajax({
				url: ajax_object.ajax_url,
				type: 'POST',
				data: data,
				//cache: false,
				dataType: 'json',
				processData: false, // Don't process the files
				//contentType: false, // Set content type to false as jQuery will tell the server its a query string request
				success: function(data, textStatus, jqXHR) {	
					
					console.log ( data );
					
					if( data.response == "SUCCESS" ){
		                var preview = "";
		                if( data.type === "image/jpg" 
		                  || data.type === "image/png" 
		                  || data.type === "image/gif"
		                  || data.type === "image/jpeg"
		                ) {
		                  preview = "<img src='" + data.url + "' />";
		                } else {
		                  preview = data.filename;
		                }
		  
		                var previewID = parent.attr("id") + "_preview";
		                var previewParent = $("#"+previewID);
		                previewParent.show();
		                previewParent.children(".ibenic_file_preview").empty().append( preview );
		                previewParent.children( "button" ).attr("data-fileurl",data.url );
		                parent.children("input").val("");
					    parent.hide();
						add_message( "File uploaded successfully", "success");
					
					} else {
					    //We removed the alert here. No need for it anymore
					    //add_message( data.error, "danger")
					}

				}

			})
			
			.fail(function( jqXHR, textStatus, errorThrown, data ) { // Si todo salio MAL se ejecuta esto
				alert('Ocurrio un error y no se pudo procesar su solicitud correctamente.');
			
				jQuery(".loading").remove();

        	});

		}
		
		$(".ibenic_file_delete").on("click", function(e){
			e.preventDefault();
			
			var fileurl = $(this).attr("data-fileurl");
			var data = { fileurl: fileurl, action: 'ibenic_file_delete' };
			
			console.log (fileurl);
			$.ajax({
				url: ajax_object.ajax_url,
				type: 'POST',
				data: data,
				cache: false,
				//dataType: 'json',
				success: function(data, textStatus, jqXHR) {	
					
					console.log(data);
					
					if( data.response == "SUCCESS" ){
						$("#ibenic_file_upload_preview").hide();
						$("#ibenic_file_upload").show();
						add_message( "File successfully deleted", "success");
					}
					
					if( data.response == "ERROR" ){
						add_message( data.error, "danger");
					}
				},
				
				error: function(jqXHR, textStatus, errorThrown) { 
					add_message( textStatus, "danger" );
				}
			});
		
		});



})