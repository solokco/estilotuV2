<?php
/** 
 * Displays Calendar
 * @package WordPress
 * @subpackage Kleo
 * @since Kleo 1.0
 */
global $tipo;
global $servicio_meta;

$servicio_meta = get_post_custom() ;
$tipo = $servicio_meta['et_meta_tipo'][0];
?>