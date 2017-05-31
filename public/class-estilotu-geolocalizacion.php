<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       mingoagency.com
 * @since      1.0.0
 *
 * @package    Estilotu
 * @subpackage Estilotu/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Estilotu
 * @subpackage Estilotu/public
 * @author     Carlos Carmona <ccarmona@mingoagency.com>
 */
class Estilotu_Geolocation_Public extends Estilotu_Public {

	// private $plugin_name;
	// public $version;

	public function __construct( ) {

		// $this->plugin_name = $plugin_name;
		// $this->version = $version;

		//$this->closest_services();
	}
	
	function add_decimal_params( $sqlarr ) {
	  
	  //remove_filter('get_meta_sql','add_decimal_params');
	 
	  $sqlarr['where'] = str_replace('DECIMAL','DECIMAL(11 , 7)' , $sqlarr['where']);
	  return $sqlarr;
	}
	
	public function closest_services () {
		
		$servicios_repetidos = $_POST['servicios_repetidos'];
		
		$lat = $_POST['latitud'];
		$lng = $_POST['longitud'];
		$categoria = $_POST['categoria'];
		$unit = $_POST['unit'];
		$distancia = $_POST['radius'];;
		
		if ( $unit == 'km' ) { $radius_earth = 6371.009; }
		elseif ( $unit == 'mi' ) { $radius_earth = 3958.761; }

		
		$maxLat = ( float ) $lat + rad2deg($distancia / $radius_earth);
	    $minLat = ( float ) $lat - rad2deg($distancia / $radius_earth);
	    
	    $maxLng = ( float ) $lng + rad2deg( $distancia / $radius_earth ) / cos( deg2rad( ( float ) $lat) ) ;
	    $minLng = ( float ) $lng - rad2deg( $distancia / $radius_earth ) / cos( deg2rad( ( float ) $lat) ) ;
				
		$closest_services_args = array(
			'post_type' 		=> 'servicios',
			'posts_per_page' 	=> -1,
			'post_status'    	=> 'publish',
			'post__not_in'		=> $servicios_repetidos,
			'meta_query' => array(
				
				'relation' => 'AND',
				array(
					'relation' => 'AND',
					array(
						'key' => 'et_meta_latitud',
						'value' => '',
						'compare' => '!='
					),
					
					array(
						'key' => 'et_meta_longitud',
						'value' => '',
						'compare' => '!='
					)
				),
				
				array(
					'relation' => 'AND',
					array(
						'key' => 'et_meta_latitud',
						'value' => array( $minLat , $maxLat  ),
						'type' => 'DECIMAL',
						'compare' => 'BETWEEN'
					),
					
					array(
						'key' => 'et_meta_longitud',
						'value' => array( $minLng , $maxLng ),
						'type' => 'DECIMAL',
						'compare' => 'BETWEEN'
					)
				)
			),
		    'tax_query' => array(
		        array(
		        'taxonomy' => 'servicios-categoria',
		        'field' => 'slug',
		        'terms' => $categoria,
		        'operator'  => ( empty($categoria) ? 'EXISTS' : 'IN' )
		        )
		    )
		);
		 
		$closest_services_query = new WP_Query( $closest_services_args );

		$servicio = array();		
		while ($closest_services_query->have_posts()) {
		    
		    $closest_services_query->the_post();
			
			$serv_id = get_the_ID();
		    
		    // traigo todos los custom
		    $servicio[$serv_id]["meta_fields"] = get_post_custom( );
		    
		    $servicio[$serv_id]["service_ID"] = $serv_id;
		    $servicio[$serv_id]["service_author"] = get_post_field ( 'post_author' , $serv_id);
		    $servicio[$serv_id]["service_author_avatar"] = get_avatar( $servicio[$serv_id]["service_author"], '64');
		    $servicio[$serv_id]["service_author_url"] = bp_core_get_user_domain( $servicio[$serv_id]["service_author"] );
		    
		    $servicio[$serv_id]["precio_servicio_visibilidad"] = get_post_meta($serv_id , 'et_meta_precio_visibilidad' , true);
		    
			/*
			$servicio[$serv_id]["lng"] = get_post_meta($serv_id, 'et_meta_longitud', true);
		    $servicio[$serv_id]["lat"] = get_post_meta($serv_id , 'et_meta_latitud' , true);
   		    $servicio[$serv_id]["precio_servicio"] = get_post_meta($serv_id , 'et_meta_precio' , true);
		    $servicio[$serv_id]["precio_servicio_visibilidad"] = get_post_meta($serv_id , 'et_meta_precio_visibilidad' , true);
		    $servicio[$serv_id]["precio_servicio_moneda"] = get_post_meta($serv_id , 'et_meta_precio_moneda' , true);
			*/
		    

		    $servicio[$serv_id]["service_title"] = get_the_title();
		    $servicio[$serv_id]["category"] = get_the_terms($serv_id , 'servicios-categoria');
		    $servicio[$serv_id]["service_url"] = get_post_permalink( $serv_id );
		    $servicio[$serv_id]["thumb_url"] = wp_get_attachment_image_src( get_post_thumbnail_id( $serv_id ) , 'medium' );

		    
		}
		
		wp_send_json($servicio);
	}
	
	public function show_map( $type , $latlon = "" ) { ?>
		
		<div id='map'></div>
		
		<?php 
		if ($type == "add"): 
			wp_enqueue_script( 'estilotu_servicios_add_map_init');

		elseif ($type == "list") : ?>
		
			<div id='legend_categories'></div>
			<div id='legend_distancia'>En KM<span class='distance green'> 1 </span><span class='distance yellow'> 2 </span><span class='distance red'> 4 </span></div>
			
			<?php wp_enqueue_script ('estilotu_servicios_list_map_init'); ?>
		
		<?php	
		elseif ($type == "single") :

			wp_enqueue_script( 'estilotu_servicios_single_map_init');
			wp_localize_script( 'estilotu_servicios_single_map_init', 'php_vars', $latlon );
				
		else:
		
			echo "No se puede mostrar el mapa";
		
		endif;
		wp_enqueue_script( 'estilotu_map_google_api');
		
		return;
		
	}
	
	public function lista_paises() {
		
		$countries = array(
			"AF" => "Afghanistan",
			"AX" => "Åland Islands",
			"AL" => "Albania",
			"DZ" => "Algeria",
			"AS" => "American Samoa",
			"AD" => "Andorra",
			"AO" => "Angola",
			"AI" => "Anguilla",
			"AQ" => "Antarctica",
			"AG" => "Antigua and Barbuda",
			"AR" => "Argentina",
			"AM" => "Armenia",
			"AW" => "Aruba",
			"AU" => "Australia",
			"AT" => "Austria",
			"AZ" => "Azerbaijan",
			"BS" => "Bahamas",
			"BH" => "Bahrain",
			"BD" => "Bangladesh",
			"BB" => "Barbados",
			"BY" => "Belarus",
			"BE" => "Belgium",
			"BZ" => "Belize",
			"BJ" => "Benin",
			"BM" => "Bermuda",
			"BT" => "Bhutan",
			"BO" => "Bolivia",
			"BA" => "Bosnia and Herzegovina",
			"BW" => "Botswana",
			"BV" => "Bouvet Island",
			"BR" => "Brazil",
			"IO" => "British Indian Ocean Territory",
			"BN" => "Brunei Darussalam",
			"BG" => "Bulgaria",
			"BF" => "Burkina Faso",
			"BI" => "Burundi",
			"KH" => "Cambodia",
			"CM" => "Cameroon",
			"CA" => "Canada",
			"CV" => "Cape Verde",
			"KY" => "Cayman Islands",
			"CF" => "Central African Republic",
			"TD" => "Chad",
			"CL" => "Chile",
			"CN" => "China",
			"CX" => "Christmas Island",
			"CC" => "Cocos (Keeling) Islands",
			"CO" => "Colombia",
			"KM" => "Comoros",
			"CG" => "Congo",
			"CD" => "Congo, The Democratic Republic of The",
			"CK" => "Cook Islands",
			"CR" => "Costa Rica",
			"CI" => "Cote D'ivoire",
			"HR" => "Croatia",
			"CU" => "Cuba",
			"CY" => "Cyprus",
			"CZ" => "Czech Republic",
			"DK" => "Denmark",
			"DJ" => "Djibouti",
			"DM" => "Dominica",
			"DO" => "Dominican Republic",
			"EC" => "Ecuador",
			"EG" => "Egypt",
			"SV" => "El Salvador",
			"GQ" => "Equatorial Guinea",
			"ER" => "Eritrea",
			"EE" => "Estonia",
			"ET" => "Ethiopia",
			"FK" => "Falkland Islands (Malvinas)",
			"FO" => "Faroe Islands",
			"FJ" => "Fiji",
			"FI" => "Finland",
			"FR" => "France",
			"GF" => "French Guiana",
			"PF" => "French Polynesia",
			"TF" => "French Southern Territories",
			"GA" => "Gabon",
			"GM" => "Gambia",
			"GE" => "Georgia",
			"DE" => "Germany",
			"GH" => "Ghana",
			"GI" => "Gibraltar",
			"GR" => "Greece",
			"GL" => "Greenland",
			"GD" => "Grenada",
			"GP" => "Guadeloupe",
			"GU" => "Guam",
			"GT" => "Guatemala",
			"GG" => "Guernsey",
			"GN" => "Guinea",
			"GW" => "Guinea-bissau",
			"GY" => "Guyana",
			"HT" => "Haiti",
			"HM" => "Heard Island and Mcdonald Islands",
			"VA" => "Holy See (Vatican City State)",
			"HN" => "Honduras",
			"HK" => "Hong Kong",
			"HU" => "Hungary",
			"IS" => "Iceland",
			"IN" => "India",
			"ID" => "Indonesia",
			"IR" => "Iran, Islamic Republic of",
			"IQ" => "Iraq",
			"IE" => "Ireland",
			"IM" => "Isle of Man",
			"IL" => "Israel",
			"IT" => "Italy",
			"JM" => "Jamaica",
			"JP" => "Japan",
			"JE" => "Jersey",
			"JO" => "Jordan",
			"KZ" => "Kazakhstan",
			"KE" => "Kenya",
			"KI" => "Kiribati",
			"KP" => "Korea, Democratic People's Republic of",
			"KR" => "Korea, Republic of",
			"KW" => "Kuwait",
			"KG" => "Kyrgyzstan",
			"LA" => "Lao People's Democratic Republic",
			"LV" => "Latvia",
			"LB" => "Lebanon",
			"LS" => "Lesotho",
			"LR" => "Liberia",
			"LY" => "Libyan Arab Jamahiriya",
			"LI" => "Liechtenstein",
			"LT" => "Lithuania",
			"LU" => "Luxembourg",
			"MO" => "Macao",
			"MK" => "Macedonia, The Former Yugoslav Republic of",
			"MG" => "Madagascar",
			"MW" => "Malawi",
			"MY" => "Malaysia",
			"MV" => "Maldives",
			"ML" => "Mali",
			"MT" => "Malta",
			"MH" => "Marshall Islands",
			"MQ" => "Martinique",
			"MR" => "Mauritania",
			"MU" => "Mauritius",
			"YT" => "Mayotte",
			"MX" => "Mexico",
			"FM" => "Micronesia, Federated States of",
			"MD" => "Moldova, Republic of",
			"MC" => "Monaco",
			"MN" => "Mongolia",
			"ME" => "Montenegro",
			"MS" => "Montserrat",
			"MA" => "Morocco",
			"MZ" => "Mozambique",
			"MM" => "Myanmar",
			"NA" => "Namibia",
			"NR" => "Nauru",
			"NP" => "Nepal",
			"NL" => "Netherlands",
			"AN" => "Netherlands Antilles",
			"NC" => "New Caledonia",
			"NZ" => "New Zealand",
			"NI" => "Nicaragua",
			"NE" => "Niger",
			"NG" => "Nigeria",
			"NU" => "Niue",
			"NF" => "Norfolk Island",
			"MP" => "Northern Mariana Islands",
			"NO" => "Norway",
			"OM" => "Oman",
			"PK" => "Pakistan",
			"PW" => "Palau",
			"PS" => "Palestinian Territory, Occupied",
			"PA" => "Panama",
			"PG" => "Papua New Guinea",
			"PY" => "Paraguay",
			"PE" => "Peru",
			"PH" => "Philippines",
			"PN" => "Pitcairn",
			"PL" => "Poland",
			"PT" => "Portugal",
			"PR" => "Puerto Rico",
			"QA" => "Qatar",
			"RE" => "Reunion",
			"RO" => "Romania",
			"RU" => "Russian Federation",
			"RW" => "Rwanda",
			"SH" => "Saint Helena",
			"KN" => "Saint Kitts and Nevis",
			"LC" => "Saint Lucia",
			"PM" => "Saint Pierre and Miquelon",
			"VC" => "Saint Vincent and The Grenadines",
			"WS" => "Samoa",
			"SM" => "San Marino",
			"ST" => "Sao Tome and Principe",
			"SA" => "Saudi Arabia",
			"SN" => "Senegal",
			"RS" => "Serbia",
			"SC" => "Seychelles",
			"SL" => "Sierra Leone",
			"SG" => "Singapore",
			"SK" => "Slovakia",
			"SI" => "Slovenia",
			"SB" => "Solomon Islands",
			"SO" => "Somalia",
			"ZA" => "South Africa",
			"GS" => "South Georgia and The South Sandwich Islands",
			"ES" => "Spain",
			"LK" => "Sri Lanka",
			"SD" => "Sudan",
			"SR" => "Suriname",
			"SJ" => "Svalbard and Jan Mayen",
			"SZ" => "Swaziland",
			"SE" => "Sweden",
			"CH" => "Switzerland",
			"SY" => "Syrian Arab Republic",
			"TW" => "Taiwan, Province of China",
			"TJ" => "Tajikistan",
			"TZ" => "Tanzania, United Republic of",
			"TH" => "Thailand",
			"TL" => "Timor-leste",
			"TG" => "Togo",
			"TK" => "Tokelau",
			"TO" => "Tonga",
			"TT" => "Trinidad and Tobago",
			"TN" => "Tunisia",
			"TR" => "Turkey",
			"TM" => "Turkmenistan",
			"TC" => "Turks and Caicos Islands",
			"TV" => "Tuvalu",
			"UG" => "Uganda",
			"UA" => "Ukraine",
			"AE" => "United Arab Emirates",
			"GB" => "United Kingdom",
			"US" => "United States",
			"UM" => "United States Minor Outlying Islands",
			"UY" => "Uruguay",
			"UZ" => "Uzbekistan",
			"VU" => "Vanuatu",
			"VE" => "Venezuela",
			"VN" => "Viet Nam",
			"VG" => "Virgin Islands, British",
			"VI" => "Virgin Islands, U.S.",
			"WF" => "Wallis and Futuna",
			"EH" => "Western Sahara",
			"YE" => "Yemen",
			"ZM" => "Zambia",
			"ZW" => "Zimbabwe"
		);
		
		return $countries;
	}

}