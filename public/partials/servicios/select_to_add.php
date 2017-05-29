<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       mingoagency.com
 * @since      1.0.0
 *
 * @package    Estilotu
 * @subpackage Estilotu/public/partials
 */	
?>
<style>
	#servicios_seleccionar_tipo {

	}
	
	#servicios_seleccionar_tipo .section {
		width: 30%;
		display: table-cell;
		margin-right:1%;
		text-align: center; 
	}
</style>
<section class="container-wrap main-color">
	
	<div class="section-container container">
		
		<div class="frm-row" id="servicios_seleccionar_tipo">
			
			<div class="section colm colm4">
				<a href="<?php echo add_query_arg('tipo_servicio' , 'cupos' ) ?>">
					<div class="tipo_servicio" id="cupos">
						<h3>Cupos</h3>
						<img src="https://cdn1.iconfinder.com/data/icons/all_google_icons_symbols_by_carlosjj-du/128/coupon_tag-r.png" alt="Cupos">
						<p>Servicio que se ejecuta de manera periódica en un lugar determinado a una hora determinada y tiene un cupo máximo permitido de participantes</p>
					</div>
				</a>
			</div>
			
			<div class="section colm colm4">	
				<a href="<?php echo add_query_arg('tipo_servicio' , 'evento' ) ?>">
					<div class="tipo_servicio" id="evento">
						<h3>Evento Especial</h3>
						<img src="https://cdn3.iconfinder.com/data/icons/ballicons-free/128/clock.png" alt="Evento Especial">
						<p>Servicio que se ofrece de manera especial y no recurrente</p>
					</div>
				</a>
			</div>
				
			<div class="section colm colm4">	
				<a href="<?php echo add_query_arg('tipo_servicio' , 'online' ) ?>">
					<div class="tipo_servicio" id="online" >
						<h3>Asistencia Online</h3>
						<img src="https://cdn3.iconfinder.com/data/icons/ballicons-free/128/bubbles.png" alt="Asistencia online">
						<p>El profesional recibe notificaiones a través del portal web sobre diferentes tipos de asistencia que puede ofrecer al cliente </p>
					</div>
				</a>
			</div>
						
			<div style="clear:both"></div>
		</div>
		
	</div>
	
</section>