<?php
add_action( 'wp_enqueue_scripts', 'enqueue_child_theme_styles', PHP_INT_MAX);
function enqueue_child_theme_styles() {
  wp_enqueue_style( 'parent-style', get_template_directory_uri().'/style.css' );
}

/*********************** idyllic child theme Category SLIDERS ***********************************/
function idyllic_category_sliders_child() {
	$idyllic_settings = idyllic_get_theme_options();
	$excerpt = get_the_excerpt();
	global $idyllic_excerpt_length;
	$slider_custom_text = $idyllic_settings['idyllic_secondary_text'];
	$slider_custom_url = $idyllic_settings['idyllic_secondary_url'];
	$idyllic_slider_design_layout = $idyllic_settings['idyllic_slider_design_layout'];
	$idyllic_slider_animation_option = $idyllic_settings['idyllic_slider_animation_option'];
	$query = new WP_Query(array(
					'posts_per_page' =>  intval($idyllic_settings['idyllic_category_slider_number']),
					'post_type' => array(
						'post'
					) ,
					'category__in' => intval($idyllic_settings['idyllic_category_slider']),
				));
	if($query->have_posts() && !empty($idyllic_settings['idyllic_category_slider'])){
		$idyllic_category_sliders_display = '';
		$slider_animation_classes='';
		if($idyllic_slider_animation_option == 'animation-bottom'){
			$slider_animation_classes = 'animation-bottom';
		}elseif($idyllic_slider_animation_option == 'animation-top'){
			$slider_animation_classes = 'animation-top';
		}elseif($idyllic_slider_animation_option == 'animation-left'){
			$slider_animation_classes = 'animation-left';
		}elseif($idyllic_slider_animation_option == 'animation-right'){
			$slider_animation_classes = 'animation-right';
		}elseif($idyllic_slider_animation_option == 'no-animation'){
			$slider_animation_classes = '';
		}
		$idyllic_category_sliders_display 	.= '<div class="main-slider '.esc_attr($slider_animation_classes).'">';
		if($idyllic_slider_design_layout=='layer-slider'){
			$idyllic_category_sliders_display 	.= '<div class="layer-slider">';
		}else{
			$idyllic_category_sliders_display 	.= '<div class="multi-slider">';
		}
		$idyllic_category_sliders_display 	.= '<ul class="slides">';
		while ($query->have_posts()):$query->the_post();
			$attachment_id = get_post_thumbnail_id();
			$image_attributes = wp_get_attachment_image_src($attachment_id,'idyllic_slider_image');
			$title_attribute = apply_filters('the_title', get_the_title(get_queried_object_id()));
			$excerpt = get_the_excerpt();
				$idyllic_category_sliders_display    	.= '<li>';
				if ($image_attributes) {
					$idyllic_category_sliders_display 	.= '<div class="image-slider" title="'.the_title_attribute('echo=0').'"' .' style="background-image:url(' ."'" .esc_url($image_attributes[0])."'" .')">';
				}else{
					$idyllic_category_sliders_display 	.= '<div class="image-slider">';
				}
				$idyllic_category_sliders_display 	.= '<article class="slider-content">';
				if ($title_attribute != '' || $excerpt != '') {
					$idyllic_category_sliders_display 	.= '<div class="slider-text-content">';

					$remove_link = $idyllic_settings['idyllic_slider_link'];
						if($remove_link == 0){
							if ($title_attribute != '') {
								$idyllic_category_sliders_display .= '<h2 class="slider-title"><a href="'.esc_url(get_permalink()).'" title="'.the_title_attribute('echo=0').'" rel="bookmark">'.get_the_title().'</a></h2><!-- .slider-title -->';
							}
						}else{
							$idyllic_category_sliders_display .= '<h2 class="slider-title">'.get_the_title().'</h2><!-- .slider-title -->';
						}
					if ($excerpt != '') {
							$idyllic_category_sliders_display .= '<div class="slider-text">'.strip_tags( get_the_content(), '<h1><h2><h3><h4><h5><h6><p><a><br>' ).'</div><!-- end .slider-text -->';
					}
					$idyllic_category_sliders_display 	.= '</div><!-- end .slider-text-content -->';
				}
				if($idyllic_settings['idyllic_slider_button'] == 0){
					$idyllic_category_sliders_display 	.='<div class="slider-buttons">';
					$excerpt_text = $idyllic_settings['idyllic_tag_text'];
					if($excerpt_text == '' || $excerpt_text == 'Check it Now') :
						$idyllic_category_sliders_display 	.= '<a title='.'"'.the_title_attribute('echo=0'). '"'. ' '.'href="'.esc_url(get_permalink()).'"'.' class="btn-default vivid-red">'.esc_html__('Check it now', 'idyllic').'</a>';
					else:
						$idyllic_category_sliders_display 	.= '<a title='.'"'.the_title_attribute('echo=0'). '"'. ' '.'href="'.esc_url(get_permalink()).'"'.' class="btn-default vivid-red">'.esc_html__('Check it now', 'idyllic').'</a>';
					endif;

					if(!empty($slider_custom_text)){
						$idyllic_category_sliders_display 	.= '<a title="'.esc_attr($slider_custom_text).'"' .' href="'.esc_url($slider_custom_url). '"'. ' class="btn-default vivid-blue" target="_blank">'.esc_attr($slider_custom_text). '</a>';
					}

					$idyllic_category_sliders_display 	.= '</div><!-- end .slider-buttons -->';
				}
				$idyllic_category_sliders_display 	.='</article><!-- end .slider-content --> ';
				$idyllic_category_sliders_display 	.='</div><!-- end .image-slider -->
				</li>';
			endwhile;
			wp_reset_postdata();
			$idyllic_category_sliders_display .= '</ul><!-- end .slides -->
				</div> <!-- end .layer-slider -->
			</div> <!-- end .main-slider -->';
				echo $idyllic_category_sliders_display;
			}
}

/********************  IDYLLIC CHILD THEME OPTIONS ******************************************/

add_action( 'customize_register', 'childtheme_customize_register');

function childtheme_customize_register( $wp_customize ) {
$wp_customize->add_section('idyllic_banner_image', array(
	'title' => __('Banner Background Image', 'idyllic'),
	'priority' => 520,
	'panel' => 'idyllic_options_panel'
));
$wp_customize->add_setting( 'idyllic_theme_options[idyllic-img-upload-banner-image]',array(
	'default'	=> $idyllic_settings['idyllic-img-upload-banner-image'],
	'capability' => 'edit_theme_options',
	'sanitize_callback' => 'esc_url_raw',
	'type' => 'option',
));
$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'idyllic_theme_options[idyllic-img-upload-banner-image]', array(
	'label' => __('Banner Background Image','idyllic'),
	'id' => 'banner-background-image',
	'description' => __('Image will be displayed on every page as header banner','idyllic'),
	'priority'	=> 60,
	'section' => 'idyllic_banner_image',
	)
));

$wp_customize->add_section( 'idyllic_user_dashboard', array(
	'title' => __('Text for User Dashboard','idyllic'),
	'priority' => 550,
	'panel' =>'idyllic_options_panel'
));
$wp_customize->add_setting('idyllic_theme_options[idyllic_user_edit_dashboard]', array(
	'default' =>$idyllic_settings['idyllic_user_edit_dashboard'],
	'sanitize_callback' => 'sanitize_text_field',
	'type' => 'textarea',
	'capability' => 'manage_options'
));
$wp_customize->add_control('idyllic_theme_options[idyllic_user_edit_dashboard]', array(
    'priority' =>70,
	'id' => 'page-edit-list-dashboard',
	'label' => __('Title', 'idyllic'),
	'section' => 'idyllic_user_dashboard',
	'type' => 'textarea',
));

}

// Add a custom user role

$result = add_role( 'website-manager', __(
'Website Manager' ),
array( ) );

// Login check for dashboard

/*function loginCheck(){
if ( is_user_logged_in() ) {		
	$current_user = wp_get_current_user();
if ( $current_user->ID == 31 ) { ?> 
<style type = "text/css">
#wpadminbar{
    display: none !important;
}
.fbuilder_draggable{
	margin:0 !important;
}
</style><?php }}} 
add_action( 'init', 'loginCheck' );
*/

function wpb_add_google_fonts() {
 
wp_enqueue_style( 'wpb-google-fonts', 'http://fonts.googleapis.com/css?family=Didact+Gothic:300italic,400italic,700italic,400,700,300', false ); 
}
 
add_action( 'wp_enqueue_scripts', 'wpb_add_google_fonts' );

// Add a custom user role

$result = add_role( 'site-manager', __(
'Site Manager' ),
array( ) );


/**
 * Add the product's short description (excerpt) to the WooCommerce shop/category pages. The description displays after the product's name, but before the product's price.
 *
 * Ref: https://gist.github.com/om4james/9883140
 *
 */
function woocommerce_after_shop_loop_item_title_short_description() {
	global $product;
	if ( ! $product->post->post_excerpt ) return;
	?>
	<div itemprop="description" style="font-size:smaller;"> 
		<?php echo substr(apply_filters( 'woocommerce_short_description', $product->post->post_excerpt ),0,230); ?>
	</div>
	<?php
}

add_action('woocommerce_after_shop_loop_item_title', 'woocommerce_after_shop_loop_item_title_short_description', 5);

add_filter("mime_types", "add_csv_plain");
function add_csv_plain($mime_types)
{

    unset($mime_types['txt|asc|c|cc|h|srt']);
    $mime_types['txt|asc|c|cc|h|srt|csv'] = 'text/plain';

    return $mime_types;
}

add_filter("woocommerce_csv_product_import_valid_filetypes", "add_csv_plain_woocommerce");
function add_csv_plain_woocommerce()
{
    return [
        'txt|csv' => 'text/plain',
        'csv' => 'text/csv',
    ];
}
/* show vat on single product details */

/** remove add to basket on single product page */
add_filter( 'woocommerce_is_purchasable', '__return_false');


