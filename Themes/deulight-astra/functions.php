<?php 
	 add_action( 'wp_enqueue_scripts', 'deulight_astra_enqueue_styles' );
	 function deulight_astra_enqueue_styles() {
 		  wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' ); 
 		  }

	// Intimation to skylinecms about the order updates
	
	add_action( 'woocommerce_thankyou', 'get_the_new_order_detials_and_Send_API' );
	
	function get_the_new_order_detials_and_Send_API( $order_id ) {
	if ( ! $order_id ) {
        return;
    }

    $order = wc_get_order( $order_id );
	//$latest_order_id = get_last_order_id(); // Last order ID
	//$order = wc_get_order( $latest_order_id ); // Get an instance of the WC_Order oject
	$order_details = $order->get_data(); // Get the order data in an array
	$order_id = $order->get_id();
		
	$url = '/Cron/eCommerceSalesOrderRetrive/219/'.$order_id;
	// create & initialize a curl session
	$curl = curl_init();

	// set our url with curl_setopt()
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	$headers = array(
      'Content-Type:application/json',
    );
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);    
    $result = curl_exec($curl);               
       if (FALSE === $result)
       throw new Exception(curl_error($curl), curl_errno($curl));
       $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
       curl_close($curl);
	   $res=json_decode($result,true);

	//echo '<pre>';
	//print_r($order_id);
	//echo '</pre>';
	//echo "<script>console.log("");</script>";
}
	add_action( 'woocommerce_order_status_changed', 'get_the_new_order_detials_and_Send_API' );
	
// manufacturer list tag in contact form 7

add_action( 'wpcf7_init', 'custom_add_form_tag_manufacturelist' );

function custom_add_form_tag_manufacturelist() {
    wpcf7_add_form_tag( array( 'manufacturelist', 'manufacturelist*' ), 
'custom_manufacturelist_form_tag_handler', false );
}

function custom_manufacturelist_form_tag_handler( $tag ) {

    $tag = new WPCF7_FormTag( $tag );

    $atts = array();

    $validation_error = wpcf7_get_validation_error( $tag->type );

    $class = wpcf7_form_controls_class( $tag->type );

    if ( $validation_error ) {
        $class .= ' wpcf7-not-valid';
    }

    $atts['class'] = $tag->get_class_option( $class );
    $atts['aria-required'] = 'true';
    $atts['aria-invalid'] = $validation_error ? 'true' : 'false';

    $atts = wpcf7_format_atts( $atts );

    $manufacturelist = '';
	
	$url ='/RestAPI/Staffords?s=getRIRIManufacturers';
	$args = [
			'headers' => array(
				'Content-Type' => 'application/json',
				'Authorization' => 'Basic ******************',
			),
			'method' => 'GET',
		];

	$response = wp_remote_get( $url, $args );
	
	if ( is_wp_error( $response ) ) {
			$submission->set_response($response->get_error_message());
			$error_message = $response->get_error_message();
			return "Something went wrong: $error_message";
		}
	
	$body_string = wp_remote_retrieve_body($response);
		
	$body = json_decode($body_string);
		
	if ( $body->Status == 'OK'){
		$term_names = $body->Data;
		$output = '<span class="wpcf7-form-control-wrap '.sanitize_html_class( $tag->name ).'"><select name="ManufacturerID" id="getUnittypes" '.$atts.'>';
		$output .= "<option value=\"\"> - Choose Manufacturer - </option>";
		foreach ($term_names as $abbrev=>$term_name) {
				$names = $term_name->ManufacturerName;
				$user_id = 	$term_name->ManufacturerID;
				$selected = ($user_id == $term_name) ? ' selected="selected" ' : '';
				$output .= '<option value="'.$user_id.'"'. $selected .'>'.$names.'</option>';
		}
		$output .= "</select></span>";
		$output .= $validation_error;
	}
				
    return $output;
}

	
	// API call for displaying the Get a Quote price
	add_action( 'cfdb7_before_save', 'drulight_user_signin_cf7_data' );

function drulight_user_signin_cf7_data( $form_data ) {
	
	$wpcf7 = WPCF7_ContactForm::get_current();
	
	$form_id = $wpcf7->id();
		
	if ($form_id === 24630){
		
		//$submission = WPCF7_Submission::get_instance();
		$wpcf7 = WPCF7_ContactForm::get_current();
		$properties = $wpcf7->get_properties();
		$submission = WPCF7_Submission::get_instance();
		
		$data = [
			's' => $form_data['s-80'], 
			'AgeOfMachine' => $form_data['AgeOfMachine'][0], 
			'ManufacturerID' => $form_data['ManufacturerID'][0], 
			'UnitTypeID' => $form_data['UnitTypeID'][0], 
			'RiriTransitID' => $form_data['RiriTransitID'][0],
		];
		
		$url = '/RestAPI/Staffords?s=getRIRIQuote&ManufacturerID='.$form_data['ManufacturerID'][0].'&AgeOfMachine='.$form_data['AgeOfMachine'][0].'&UnitTypeID='.$form_data['UnitTypeID'][0].'&RiriTransitID='.$form_data['RiriTransitID'][0];
		//$data = wp_json_encode( $data );
		
		//echo '<pre>';
		//print_r ($url);
		//echo '</pre>';
		
		$args = [
			'headers' => array(
				'Content-Type' => 'application/json',
				'Authorization' => 'Basic **********************',
			),
			'method' => 'GET',
		];

		$response = wp_remote_get( $url, $args );
		
		//echo '<pre>';
		//print_r ($response);
		//echo '</pre>';
		
		if ( is_wp_error( $response ) ) {
			$submission->set_response($response->get_error_message());
			$error_message = $response->get_error_message();
			return "Something went wrong: $error_message";
		}
		$body_string = wp_remote_retrieve_body($response);
		//$body = json_decode( wp_remote_retrieve_body( $response ), true ); // decode the JSON feed
		$body = json_decode($body_string);
		//echo '<pre>';
		//print_r($response);
		//echo '</pre>';
		
		$quotePrice = $body->Price;
		
		$successMsg = "Hi " . $form_data['your-name'] . ", \n\n Your quote request for the following machine: \n\n Age of Machine : " . $form_data['AgeOfMachine'][0] . ", \n Equipment Serial Number : " . $form_data['serial-number'] . ", \n Manufacturer : " . $form_data['s-90'] . ", \n Model : " . $form_data['s-100'] . ", \n Unit Type : " . $form_data['s-110'] . ", \n\n Quote Price : £ " . $quotePrice . ".";
		$errorMsg = "Hi " . $form_data['your-name'] . ", \n\n Your quote request for the following machine: \n\n Age of Machine : " . $form_data['AgeOfMachine'][0] . ", \n Equipment Serial Number : " . $form_data['serial-number'] . ", \n Manufacturer : " . $form_data['s-90'] . ", \n Model : " . $form_data['s-100'] . ", \n Unit Type : " . $form_data['s-110'] . ", \n\n We could not generate a quote price as the machine is older than 5 years. \n\n A member of our team will be in contact with you.";
		
		if ( $body->Status == 'Success'){
				$properties['messages']['mail_sent_ok'] = $successMsg;
				//$submission->set_response( "Hi \n\n Quote Price : £ " . $body->Price );
			} else {
				//$submission->set_response($body_string);
				$properties['messages']['mail_sent_ok'] = $errorMsg;
			}
				
			$wpcf7->set_properties($properties);
			
	}

   } 
	
	
 ?>