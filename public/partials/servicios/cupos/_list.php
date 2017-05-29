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
			
			<small><?php the_time('F jS, Y'); ?> by <?php the_author_posts_link(); ?></small>
			
			<div class="entry">
				<?php the_content(); ?>
			</div>
			
			<p class="postmetadata"><?php _e( 'Posted in' ); ?> <?php the_category( ', ' ); ?></p>
			
			<a href="<?php echo esc_url(add_query_arg( 'id_servicio' , get_the_ID() , bp_loggedin_user_domain() . "servicios/editar/" ) ); ?>">Editar</button>
			
		</div>

	<?php endwhile; 

else : ?>

 	<p><?php _e( 'Sorry, no posts matched your criteria.' ); ?></p>

 <?php endif; ?>