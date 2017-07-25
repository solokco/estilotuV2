<!--
<ul>

	<?php 
	foreach ( $posts_array as $post ) : setup_postdata( $post ); ?>
		
		<li>
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</li>
		
	<?php endforeach; 

	wp_reset_postdata();?>
		
</ul>
-->

<?php 
global $bp;
$id_usuario_seleccionado = bp_displayed_user_id();
	
if ( $the_query->have_posts() ) : 

	while ( $the_query->have_posts() ) : 
		
		$the_query->the_post(); ?>

		<div class="servicio col-sm-4">
			
			<div class="imagen_servicio">
				<a href="<?php the_permalink(); ?>" title="Ir a servicio <?php the_title_attribute(); ?>">
					<?php echo get_the_post_thumbnail( ); ?>
				</a>
			</div>
			
			<h2>
				<a href="<?php the_permalink(); ?>" rel="bookmark" title="Ir a servicio <?php the_title_attribute(); ?>">
					<?php the_title(); ?>
				</a>
			</h2>
			
			<div class="entry">
				<?php the_content(); ?>
			</div>
			
			<p class="postmetadata"><?php _e( 'Posted in' ); ?> <?php the_category( ', ' ); ?></p>
			
			<?php if ( $id_usuario_seleccionado == $bp->loggedin_user->id ): ?>
				<a href="<?php echo esc_url(add_query_arg( 'id_servicio' , get_the_ID() , bp_loggedin_user_domain() . "servicios/editar/" ) ); ?>">Editar</button>
			<?php endif;?>
			
		</div>

	<?php endwhile; 

else : ?>

 	<p><?php _e( 'Sorry, no posts matched your criteria.' ); ?></p>

 <?php endif; ?>