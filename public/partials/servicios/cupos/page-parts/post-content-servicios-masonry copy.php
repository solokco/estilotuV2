<?php
/**
 * The template Masonry blog item
 *
 * @package WordPress
 * @subpackage Kleo
 * @since Kleo 1.0
 */
?>

	<article id="post-<?php the_ID(); ?>" <?php post_class(array("post-item")); ?>>
	<div class="post-content animated animate-when-almost-visible el-appear">
		<?php
		global $kleo_config;
		$kleo_post_format = get_post_format();
		
		$opciones_servicio = get_post_custom( ); 		
		$tipo = $opciones_servicio['et_meta_tipo'][0];

		switch ($kleo_post_format) {
			
			case 'video':

				//oEmbed video
				$video = get_cfield( 'embed' );
				// video bg self hosted
				$bg_video_args = array();
				$k_video = '';

				if (get_cfield( 'video_mp4' ) ) {
					$bg_video_args['mp4'] = get_cfield( 'video_mp4' );
				}
				if (get_cfield( 'video_ogv' ) ) {
					$bg_video_args['ogv'] = get_cfield( 'video_ogv' );
				}
				if (get_cfield( 'video_webm' ) ) {
					$bg_video_args['webm'] = get_cfield( 'video_webm' );
				}

				if ( !empty( $bg_video_args ) ) {
					$attr_strings = array(
							'preload="0"'
					);

                    if (get_cfield( 'video_poster' ) ) {
                        $attr_strings[] = 'poster="' . get_cfield( 'video_poster' ) . '"';
                    }

					$k_video .= sprintf( '<div class="kleo-video-wrap"><video %s controls="controls" class="kleo-video">', join( ' ', $attr_strings ) );

					$source = '<source type="%s" src="%s" />';
					foreach ( $bg_video_args as $video_type => $video_src ) {
						$video_type = wp_check_filetype( $video_src, wp_get_mime_types() );
						$k_video .= sprintf( $source, $video_type['type'], esc_url( $video_src ) );
					}

					$k_video .= '</video></div>';

					echo $k_video;
				}
				// oEmbed
				elseif ( !empty( $video ) ) {
					global $wp_embed;
					echo apply_filters( 'kleo_oembed_video', $video ); 
				}

				break;
			
			case 'audio':
			
				$audio = get_cfield('audio');
				if (!empty($audio)) { ?>
					<div class="post-audio">
						<audio preload="none" class="kleo-audio" id="audio_<?php the_id();?>" style="width:100%;" src="<?php echo $audio; ?>"></audio>
					</div>
					<?php
				}
				break;
				
			case 'gallery':
			
				$slides = get_cfield('slider');
				echo '<div class="kleo-banner-slider">'
					.'<div class="kleo-banner-items" data-speed="2000">';
				if ( $slides ) {
					foreach( $slides as $slide ) {
						if ( $slide ) {
							$image = aq_resize( $slide, $kleo_config['post_gallery_img_width'], $kleo_config['post_gallery_img_height'], true, true, true );
							//small hack for non-hosted images
							if (! $image ) {
								$image = $slide;
							}
							echo '<article>
								<a href="'. $slide .'" data-rel="prettyPhoto[inner-gallery]">
									<img src="'.$image.'" alt="">'
									. kleo_get_img_overlay()
								. '</a>
							</article>';
						}
					}		
				}
				
				echo '</div>'
					. '<a href="#" class="kleo-banner-prev"><i class="icon-angle-left"></i></a>'
					. '<a href="#" class="kleo-banner-next"><i class="icon-angle-right"></i></a>'
					. '<div class="kleo-banner-features-pager carousel-pager"></div>'
					.'</div>';
				
				break;
				
			case 'quote':	
			case 'link':
			
				echo '<div class="inner-content">'
				. get_the_content()
				. '</div><!--end inner-content-->';
				break;
				
			case 'status':
			
				echo '<div class="inner-content">'
				. get_the_content()
				. '</div><!--end inner-content-->';
				break;			
			
			case 'image':
			default:
				if ( kleo_get_post_thumbnail_url() != '' ) {
					echo '<div class="post-image">';
			
					$img_url = kleo_get_post_thumbnail_url();
					$image = aq_resize( $img_url, $kleo_config['post_gallery_img_width'], null, true, true, true );
					if( ! $image ) {
                        $image = $img_url;
                    }
                    echo '<a href="'. get_permalink() .'" class="element-wrap">'
                        . '<img src="' . $image . '">'
                        . kleo_get_img_overlay()
                        . '</a>';
					
					echo '</div><!--end post-image-->';
				}
				
				break;
		}
		?>
		
		<?php if ($kleo_post_format != 'quote' && $kleo_post_format != 'link' ) : ?>
				
		<div class="post-header">
			
			<?php if ($kleo_post_format != 'status'): ?>
			<h3 class="post-title entry-title"><a href="<?php the_permalink();?>"><?php the_title();?></a></h3>
			<?php if ( $tipo == "cupos" ) : 	 ?>
			<div class="dias masonry">
				<ul>
				<?php if ($opciones_servicio['et_meta_dias_activo_lunes'][0] == 'on' ): 	 ?>
					<li class="dia_activo dia_circulo">LU</li>
				<?php else: ?>
					<li class="dia_inactivo dia_circulo">LU</li>
				<?php endif; ?>
				
				<?php if ($opciones_servicio['et_meta_dias_activo_martes'][0] == 'on' ): 	 ?>
					<li class="dia_activo dia_circulo">MA</li>
				<?php else: ?>
					<li class="dia_inactivo dia_circulo">MA</li>
				<?php endif; ?>
				
				<?php if ($opciones_servicio['et_meta_dias_activo_miercoles'][0] == 'on' ): 	 ?>
					<li class="dia_activo dia_circulo">MI</li>
				<?php else: ?>
					<li class="dia_inactivo dia_circulo">MI</li>
				<?php endif; ?>
				
				<?php if ($opciones_servicio['et_meta_dias_activo_jueves'][0] == 'on' ): 	 ?>
					<li class="dia_activo dia_circulo">JU</li>
				<?php else: ?>
					<li class="dia_inactivo dia_circulo">JU</li>
				<?php endif; ?>
				
				<?php if ($opciones_servicio['et_meta_dias_activo_viernes'][0] == 'on' ): 	 ?>
					<li class="dia_activo dia_circulo">VI</li>
				<?php else: ?>
					<li class="dia_inactivo dia_circulo">VI</li>
				<?php endif; ?>
				
				<?php if ($opciones_servicio['et_meta_dias_activo_sabado'][0] == 'on' ): 	 ?>
					<li class="dia_activo dia_circulo">SA</li>
				<?php else: ?>
					<li class="dia_inactivo dia_circulo">SA</li>
				<?php endif; ?>
				
				<?php if ($opciones_servicio['et_meta_dias_activo_domingo'][0] == 'on' ): 	 ?>
					<li class="dia_activo dia_circulo">DO</li>
				<?php else: ?>
					<li class="dia_inactivo dia_circulo">DO</li>
				<?php endif; ?>
				</ul>
			</div>
			<?php endif; ?>
			<?php endif; ?>

                <span class="post-meta">
                    <?php kleo_entry_meta();?>
                </span>

		</div><!--end post-header-->
		
		<?php if ( $kleo_post_format != 'status' ): ?>
		
			<?php if (kleo_excerpt() != '<p></p>') : ?>
			<div class="post-info">

				<div class="entry-summary">
					<?php echo kleo_excerpt(); ?>
				</div><!-- .entry-summary -->

			</div><!--end post-info-->
			<?php endif; ?>
			
		<?php endif; ?>
		
		<?php endif; ?>
		
		<div class="post-footer">
			<small>
				<?php do_action('kleo_post_footer'); ?>
				
				<?php if ( !empty($opciones_servicio['et_meta_direccion'][0] ) ): ?>
				<div class="direccion">
					<p><i class="fa fa-map-marker fa-fw"></i><?php echo $opciones_servicio['et_meta_direccion'][0] ; ?><br>
					<?php echo $opciones_servicio['et_meta_ciudad'][0] ; ?><br>
					<?php echo $opciones_servicio['et_meta_pais'][0] ; ?></p>
				</div>
				<?php endif; ?>
				
				<?php if ( $tipo == "online" ) : ?>
					<p>Consulta Online</p>
				<?php endif; ?>
				
				<p class="label_precio">
					<?php if ($opciones_servicio['et_meta_precio_visibilidad'][0] == "private"): ?>
					
						<!-- <p>El precio de este servicio es privado</p> -->
					
					<?php elseif ($opciones_servicio['et_meta_precio_visibilidad'][0] == "hidden"): ?>
					
						<!--  <a href="#" class="btn btn-primary btn-lg mostrar_precio">Mostrar el precio</a> -->
					
					<?php else: ?>

						<i class="fa fa-money fa-fw"></i> <?php echo (number_format($opciones_servicio['et_meta_precio'][0] ,2,",",".") ); ?> <?php echo $opciones_servicio['et_meta_precio_moneda'][0]; ?>
		
					<?php endif; ?>	
				</p>
				
				<a href="<?php the_permalink();?>"><span class="muted pull-right">Reservar</span></a>
				<!-- <a href="<?php the_permalink();?>"><span class="muted pull-right"><?php _e("Read more","kleo_framework");?></span></a> -->
			</small>
		</div><!--end post-footer-->

	</div><!--end post-content-->
	</article>

