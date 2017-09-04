<h3>Filtros</h3>
		
<form action="" method="post">
	<label for="date_from-filter">Desde</label>
	<input type="text" id="date_from-filter" name="date_from-filter" value="<?php echo $fecha_from; ?> ">
	<label for="date_to-filter">Hasta</label>
	<input type="text" id="date_to-filter" name="date_to-filter" value="<?php echo $fecha_to; ?> ">
	
	<select name="status-filter">
		<option value="confirm" <?php selected( $selected , "confirm" ); ?> ><?php _e("Confirmadas" , "estilotu"); ?></option>
		<option value="cancel" <?php selected( $selected , "cancel" ); ?> ><?php _e("Canceledas" , "estilotu"); ?></option>
		<option value="on hold" <?php selected( $selected , "on hold" ); ?> ><?php _e("En espera" , "estilotu"); ?></option>
	</select>
	
	<?php wp_nonce_field( 'filtros_citas', 'filtros_citas_nonce' ); ?>
	
	<p><input type="submit" value="submit" name="filtrar_citas"></p>
	
</form>