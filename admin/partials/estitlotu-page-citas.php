<div class="wrap">

	<h2> <?php _e( 'EstiloTú Citas', 'estilotu' ) ?> </h2>

	<form id="citas-filter" method="post">
		<?php $wp_list_table->views(); ?>
		<?php $wp_list_table->display(); ?>
	</form>
	
</div>