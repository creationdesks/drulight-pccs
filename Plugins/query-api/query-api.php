<?php
/*
	Plugin Name: Query APIs
	Plugin URI:	https://pccsuk.com
	Description : Plugin working with external API Calls in WordPress.
	version: 0.1.0
	Author:	Gururaju Acharya
	Author URI: https://creationdesks.com
	License: GPL-3.0+
	License URI: http://www.gnu.org/licenses/gpl-2.0.txt
*/


// if this file is called firectly, abort!!!
defined( 'ABSPATH' ) or die;

/**
 * Register a custom menu page to view the information queried.
 */
function skyline_register_my_custom_menu_page() {
	add_menu_page(
		__( 'Query API Test Settings', 'query-apis' ),
		'Query API Test',
		'manage_options',
		'api-test.php',
		'skyline_get_send_data_model',
		'dashicons-testimonial',
		16
	);
}
add_action( 'admin_menu', 'skyline_register_my_custom_menu_page' );	

/*
 * api call to get manufacturer details.
*/
function skyline_get_send_data_manufacturer() {

   $url = '/RestAPI/Staffords?s=getRIRIManufacturers';
    
   $args = array(
        'headers' => array(
            'Content-Type' => 'application/json',
			'Authorization' => 'Basic ***********************',
        ),
        'body'    => array(),
    );

	$response = wp_remote_get( $url, $args );
	
	if ( is_wp_error( $response ) ) {
		$error_message = $response->get_error_message();
		return "Something went wrong: $error_message";
	}
	
	$result = json_decode( wp_remote_retrieve_body( $response ) ); // decode the JSON feed
	
	return $result;
}

/*
 * api call to get Trsnit description details.
*/

function skyline_get_send_data_transit() {

   $url = '/RestAPI/Staffords?s=getTransitOptions';
    
   $args = array(
        'headers' => array(
            'Content-Type' => 'application/json',
			'Authorization' => 'Basic ***************************',
        ),
        'body'    => array(),
    );

	$response = wp_remote_get( $url, $args );
	
	if ( is_wp_error( $response ) ) {
		$error_message = $response->get_error_message();
		return "Something went wrong: $error_message";
	}
	
	$result = json_decode( wp_remote_retrieve_body( $response ) ); // decode the JSON feed
	
	return $result;
}

/*
 * api call to get Trsnit description details.
*/

function skyline_get_send_data_model() {

   $url = '/RestAPI/Staffords?s=getRIRIModels&ManufacturerID=29369';
    
   $args = array(
        'headers' => array(
            'Content-Type' => 'application/json',
			'Authorization' => 'Basic **********************',
        ),
        'body'    => array(),
    );

	$response = wp_remote_get( $url, $args );
	
	if ( is_wp_error( $response ) ) {
		$error_message = $response->get_error_message();
		return "Something went wrong: $error_message";
	}
	
	$result = json_decode( wp_remote_retrieve_body( $response ) ); // decode the JSON feed
	echo '<pre>';
	print_r($result);
	echo '</pre>';
}


/*
 * contact form 7 drop down form control for manufacturer details. 
 */

function pine_dynamic_select_field_values ( $scanned_tag, $replace ) {  
  
    if ( $scanned_tag['name'] != 'ManufacturerID' )  
        return $scanned_tag;

    $rows = skyline_get_send_data_manufacturer();
  
    if ( ! $rows )  
        return $scanned_tag;

    foreach ( $rows as $row ) {  
         $scanned_tag['raw_values'][] = $row->ManufacturerName . '|' . $row->ManufacturerID;
    }
	
	if ( WPCF7_USE_PIPE ) {
				$pipes = new WPCF7_Pipes( $scanned_tag['raw_values'] );
				$scanned_tag['values'] = $pipes->collect_befores();
				$scanned_tag['pipes'] = $pipes;
			} else {
				$scanned_tag['values'] = $scanned_tag['raw_values'];
			}
	
	return $scanned_tag;  
}  

add_filter( 'wpcf7_form_tag', 'pine_dynamic_select_field_values', 10, 2);

/*
 * contact form 7 drop down form control for Trsnit details. 
 */

function transit_dynamic_select_field_values ( $scanned_tag, $replace ) {  
  
    if ( $scanned_tag['name'] != 'RiriTransitID' )  
        return $scanned_tag;

    $rows = skyline_get_send_data_transit();
  
    if ( ! $rows )  
        return $scanned_tag;

    foreach ( $rows as $row ) {  
         $scanned_tag['raw_values'][] = $row->TransitDescription . '+£' . $row->Price . '|' . $row->RiriTransitID;
    }
	
	if ( WPCF7_USE_PIPE ) {
				$pipes = new WPCF7_Pipes( $scanned_tag['raw_values'] );
				$scanned_tag['values'] = $pipes->collect_befores();
				$scanned_tag['pipes'] = $pipes;
			} else {
				$scanned_tag['values'] = $scanned_tag['raw_values'];
			}
	
	return $scanned_tag;  
}  

add_filter( 'wpcf7_form_tag', 'transit_dynamic_select_field_values', 10, 2);

/*
 * contact form 7 drop down form tag control. 
 */

function so48515097_cf7_select_values($tag)
{
    if ($tag['basetype'] != 'select') {
        return $tag;
    }

    $values = [];
    $labels = [];
    foreach ($tag['raw_values'] as $raw_value) {
        $raw_value_parts = explode('|', $raw_value);
        if (count($raw_value_parts) >= 2) {
            $values[] = $raw_value_parts[1];
            $labels[] = $raw_value_parts[0];
        } else {
            $values[] = $raw_value;
            $labels[] = $raw_value;
        }
    }
    $tag['values'] = $values;
    $tag['labels'] = $labels;

    // Optional but recommended:
    //    Display labels in mails instead of values
    //    You can still use values using [_raw_tag] instead of [tag]
	
	//$reversed_raw_values = array_map(function ($raw_value) {
      //  $raw_value_parts = explode('|', $raw_value);
        //return implode('|', array_reverse($raw_value_parts));
    //}, $tag['raw_values']);
    //$tag['pipes'] = new \WPCF7_Pipes($reversed_raw_values);

    return $tag;
}
add_filter('wpcf7_form_tag', 'so48515097_cf7_select_values', 10);