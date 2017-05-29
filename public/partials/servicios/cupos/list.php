<?php
/**
 * The template for displaying Archive pages
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * If you'd like to further customize these archive views, you may create a
 * new template file for each specific one. For example, Twenty Fourteen
 * already has tag.php for Tag archives, category.php for Category archives,
 * and author.php for Author archives.
 *
 * @link http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Kleo
 * @since Kleo 1.0
 */

get_header(); ?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/foundation/6.2.4/foundation.min.css">

<div class="row expanded fullw">

	<div class="columns small-12 medium-7">
		<div class="flexsearch">
			<div class="flexsearch--wrapper">
				<form class="flexsearch--form" id="buscador-servicios-form" action="#" method="post">
					<div class="flexsearch--input-wrapper">
						<input id="pac-input" class="" type="text" placeholder="Consigue servicios cerca de ti">
					</div>
					<!-- <input class="flexsearch--submit" id="buscador-servicios-submit" type="submit" value="&#10140;"/> -->
					
					<?php 
						$args = array(
							'show_option_all'    => 'Mostrar Todas',
							'taxonomy'           => 'servicios-categoria',
							'hide_if_empty'      => false,
							'value_field'	     => 'slug',
							'depth'              => 2,
							'tab_index'          => 2,
							'exclude'            => '',
							'include'            => '',
							'hierarchical'       => 1,
							'name'               => 'lista-categoria',
							'class'              => 'lista_categorias'
						);
						
						wp_dropdown_categories( $args ); ?>
				</form>
			</div>
		</div>
		
		<div class="row" id="lista_servicios_geolocation"></div>
	</div>

	<div class="columns small-12 medium-5">
		<?php 
			$mapa = new Estilotu_Geolocation_Public; 
			$mapa->show_map("list");
			
		?>
	</div>
</div>

<script>
	
/*
	function thirty_pc() {
	    var height = jQuery(window).height();
	    height = parseInt(height) + 'px';
	    jQuery("#lista_servicios_geolocation").css('height',height);
	}

	jQuery(document).ready(function() {
    	thirty_pc();
		jQuery(window).bind('resize', thirty_pc);
	});
*/
	
</script>

<?php get_footer(); ?>