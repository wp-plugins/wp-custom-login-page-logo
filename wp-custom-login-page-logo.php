<?php
/*
Plugin Name: WP Custom Login Page Logo
Plugin URI: http://wp.larsactionhero.com/development/plugins/wp-custom-login-page-logo/
Description: Customize the admin logo on /wp-admin login page.
Version: 1.2.8
Author: Lars Ortlepp
Author URI: http://larsactionhero.com
License: GPL2
*/


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


/*
* Init: load textdomain & plugin settings links
***************************************
*/
function wpclpl_init(){
	
	load_plugin_textdomain( 'wpclpl', FALSE, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'wpclpl_plugin_links', 10, 2 );
	
}


/*
* load language files
****************************************	
*/
function wpclpl_load_textdomain(){
	load_plugin_textdomain( 'wpclpl', FALSE, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}


/*
* plugin links
****************************************	
*/
function wpclpl_plugin_links( $links ){

	// settings urlsm, etc
	$wpclpl_settings_url = admin_url('options-general.php?page=wp-custom-login-page-logo.php');
	$wpclpl_docs_url = 'http://wp.larsactionhero.com/development/plugins/wp-custom-login-page-logo/';

	$wpclpl_plugin_links = array(
		'<a href="'.$wpclpl_settings_url.'">' . __( 'Settings', 'wpclpl' ) . '</a>',
	    /*'<a href="'.$wpclpl_docs_url.'" target="_blank">' . __( 'Documentation', 'wpclpl' ) . '</a>'*/
	);
        
	return array_merge( $wpclpl_plugin_links, $links );    

}


add_action( 'admin_init', 'wpclpl_init' );


/*
* update options
****************************************
*/

if($_POST['wpclpl_save']=='1'){
	wpclpl_update_options();
}

/*
* add options page
****************************************
*/
function register_wpclpl_plugin_option_page(){
   add_options_page('Custom Login Page Logo', 'Custom Login Page Logo', 'manage_options', basename(__FILE__), 'wpclpl_admin_options_page');
}
add_action('admin_menu','register_wpclpl_plugin_option_page');



/*
* filter input
****************************************
*/
function wpclpl_filter_vars( $input ){
	if($input) {
		return filter_var( $input, FILTER_SANITIZE_STRING );
	} else {
		return '';
	}
}


// get options at load...
if( get_option( 'wpclpl_plugin_options' ) != false) {
	$wpclpl_plugin_options = get_option('wpclpl_plugin_options');
} else {
	// defaults...
	$wpclpl_plugin_options = array(
		'wpclpl_logo_url'=>'',
		'wpclpl_additional_text'=>'',
		'wpclpl_custom_css'=>'',
		);
}




/*
* 
****************************************
*/   
function wpclpl_settings_header_text() {
	?>
	<p class="wpclpl-plugin-information">
	<?php _e('This Plugin allows you to change the default (wordPress-) logo at the admin login page.','wpclpl'); ?>
	<br />
	<?php _e('Helpful if you want to customize the login page for your clients or a company.','wpclpl'); ?>
	</p>
	<?php
}
    
    
/*
* settings fields
****************************************
*/   
function wpclpl_settings_logo() {  

	global $wpclpl_plugin_options;

	$wpclpl_plugin_logo_url =  ( !empty( $wpclpl_plugin_options['wpclpl_logo_url'] ) ) ? esc_url($wpclpl_plugin_options['wpclpl_logo_url']) : '';
	?>
     <p>
     	<input class="wpclpl-logo-upload-btn button" type="button" value="<?php esc_attr_e('Select an image file','wpclpl'); ?>" /> 
     	<span class="wpclpl-description">
     		<?php _e('Select an existing image from the media library or upload a new one.','wpclpl');?><br />
	 		<?php _e('You also can insert an image url manually:','wpclpl'); ?><br />
        <input type="text" class="wpclpl-logo-url" name="wpclpl_logo_url" value="<?php echo $wpclpl_plugin_logo_url; ?>" placeholder="<?php _e('Insert URL here or select image with button below.','wpclpl'); ?>" />     
        <code>(<?php _e('e.g.','wpclpl'); ?> http://www.mywebsite.com/wp-content/themes/mytheme/images/mylogo.jpg)</code></span>
     </p>
     <?php
}  
    
    
/*
* this function only returns the plain url 
* of the custom logo
****************************************
*/
function wpclpl_settings_logo_plain_url() {  
	global $wpclpl_plugin_options;
	echo ( !empty( $wpclpl_plugin_options['wpclpl_logo_url'] ) ) ? 'background-image: url("'.esc_url($wpclpl_plugin_options['wpclpl_logo_url']).'");'."\n" : '';
}


/*
* html preview output on admin panel
****************************************
*/
function wpclpl_settings_logo_preview() {  
	
	global $wpclpl_plugin_options;

	if( !empty( $wpclpl_plugin_options['wpclpl_logo_url'] ) ){
	
	?>
		<div class="wpclpl-logo-preview-wrap">  
			<a href="<?php echo esc_url( $wpclpl_plugin_options['wpclpl_logo_url'] );?>?TB_inline=true&height=400&width=400&inlineId=wpclpl-logo-preview"  class="thickbox">
				<img style="max-width:100%;" class="wpclpl-logo-preview" src="<?php echo esc_url( $wpclpl_plugin_options['wpclpl_logo_url'] ); ?>" id="wpclpl-logo-preview" /></a>
		</div>
		<p>
		<?php
		 wpclpl_image_dimensions();
		 ?>
		</p>
		<p>
			<input class="wpclpl-logo-remove-img-btn wpclpl-reset-btn button" id="wpclpl-modal-box-reset-image" type="button" value="<?php esc_attr_e('Remove Image','wpclpl'); ?>" />
			<span class="wpclpl-description">(<?php _e('File in Media Library will not be deleted','wpclpl'); ?>)</span>
		</p>
		<?php
		
	} else {
		
		?>
		<div class="wpclpl-currentlogo" style="background-image: url('<?php echo admin_url(); ?>images/wordpress-logo.svg?ver=20131107')"></div>
		<br clear="left" />
		<p class="wpclpl-default-logo" style="">(<?php _e('Default WP Logo','wpclpl'); ?>)</p>
		<?php
	}

}  



/*
* we check if file is located on our server or external
****************************************
*/
function wpclpl_file_is_local(){
	
	global $wpclpl_plugin_options;
	return ( stristr( esc_url($wpclpl_plugin_options['wpclpl_logo_url']), $_SERVER['SERVER_NAME'] )===false ) ? false : true;
	
}



/*
* returns image dimensions
****************************************
*/
function wpclpl_image_dimensions( $return=false ){

	global $wpclpl_plugin_options;
	
	if( !empty( $wpclpl_plugin_options['wpclpl_logo_url'] ) ){
				
		
		// file location
		if( wpclpl_file_is_local() === true ){
				
			$wpclpl_logo_url = esc_url($wpclpl_plugin_options['wpclpl_logo_url']);
		
			$wpclpl_logo_url = str_replace('http://', '', $wpclpl_logo_url);	
			$wpclpl_logo_url = str_replace('https://','', $wpclpl_logo_url);	
			$wpclpl_logo_url = str_replace($_SERVER['SERVER_NAME'],'', $wpclpl_logo_url);
			$wpclpl_logo_url = str_replace('www.','', $wpclpl_logo_url);
	
			$wpclpl_logo_dimensions = getimagesize( $_SERVER['DOCUMENT_ROOT'].$wpclpl_logo_url );
			
			$wpclpl_logo_width = $wpclpl_logo_dimensions[0];
			$wpclpl_logo_height = $wpclpl_logo_dimensions[1];
			
			if( !$return ){
			?>
			<span class="wpclpl-description"><?php _e('Original size','wpclpl'); ?>: <span id="wpclpl-logo-width"><?php echo $wpclpl_logo_width; ?></span> x <span id="wpclpl-logo-height"><?php echo $wpclpl_logo_height; ?></span>px</span>
			<?php
			} else {
				return array($wpclpl_logo_width,$wpclpl_logo_height);
			}
			
		} else {
			
			// external file: can't read image dimensions.
			if( !$return ){
			?>
			<span class="wpclpl-description"><?php _e("Note: The Plugin can not read dimensions of external files. Please add them to your stylesheet manually.",'wpclpl'); ?></span>
			<?php
			} else { 
				return '';
			}
			
		}
		
		/*		
		echo '
			<input type="text" name="wpclpl_logo_width" class="wpclpl-logo-dimensions" value="' . $wpclpl_logo_width . '" /> px width<br />
			<input type="text" name="wpclpl_logo_height" class="wpclpl-logo-dimensions" value="' . $wpclpl_logo_height . '" /> px height';
		*/
		
	}
	
}




/*
* additional custom text
****************************************
*/
function wpclpl_settings_add_text(){

	global $wpclpl_plugin_options;
	
	$wpclpl_additional_text = ( !empty( $wpclpl_plugin_options['wpclpl_additional_text']) ) ? $wpclpl_plugin_options['wpclpl_additional_text'] : '';
	
	?>
	<input name="wpclpl_additional_text" type="text" class="wpclpl-additional-text" placeholder="<?php _e('This text will appear below the custom logo','wpclpl'); ?>." value="<?php echo  $wpclpl_plugin_options['wpclpl_additional_text']; ?>" />
	<br />
	<span class="wpclpl-description"><?php _e('Add some optional user information. This text will appear below the custom logo.','wpclpl'); ?></span>
	<?php

}







/*
*		we build the css output.
*		values are added as follows:
****************************************
*		1. the background-image url
*		2. the detected size (width/height)
*		3. the user's custom styles
*/
	  
function wpclpl_build_css_output(){
	
	global $wpclpl_plugin_options;
	
	$output = '';
	
	// 1. add bg image
	$image_url = (!empty($wpclpl_plugin_options['wpclpl_logo_url'])) ? esc_url($wpclpl_plugin_options['wpclpl_logo_url']) : '';
	$output .= 'background-image: url('.$image_url.');'."\n";

	// 2. add dimensions, we receive an array
	$image_dimensions =  (!empty($wpclpl_plugin_options['wpclpl_logo_url'])) ? wpclpl_image_dimensions(true) : '';
	$output .= 'width: '.$image_dimensions[0].'px;'."\n";
	$output .= 'height: '.$image_dimensions[1].'px;'."\n";
	
	return $output;
	
}




/*
* custom css
****************************************
*/
function wpclpl_settings_custom_css($return = false){

	global $wpclpl_plugin_options;
	$wpclpl_logo_url = ( !empty( $wpclpl_plugin_options['wpclpl_logo_url'] ) ) ? esc_url($wpclpl_plugin_options['wpclpl_logo_url']) : '';

	  if( !$return ){
		  ?>
		<script type="text/javascript">
		jQuery(function($){
			<?php
				$image_dimensions =  wpclpl_image_dimensions(true);
			?>
			$('.wpclpl-logo-example-css-btn').click(function(){
			
				exampleCss = 'padding: 0;'+"\n";
				exampleCss += 'background-size: cover;'+"\n";
				exampleCss += 'background-position: center center;'+"\n";
				exampleCss += 'background-repeat: no-repeat;'+"\n";
				exampleCss += 'background-color: #fff;'+"\n";
				
				<?php if(!empty($wpclpl_logo_url) && (!empty($image_dimensions))) { ?>
				exampleCss += 'width: <?php echo $image_dimensions[0].'px; /* '; _e('matched to image dimensions','wpclpl'); echo ' */'; ?>'+"\n";
				exampleCss += 'height: <?php echo $image_dimensions[1].'px; /* '; _e('matched to image dimensions','wpclpl'); echo ' */'; ?>'+"\n";
				<?php } ?>
				
				exampleCss += 'box-shadow: 0px 1px 3px rgba(0, 0, 0, 0.15);'+"\n";
				$('.wpclpl-custom-css').val(exampleCss);
				
				var backgroundImage = 'background-image:url('+$('.wpclpl-logo-url').val()+');';
				var cssPreview = backgroundImage+"\n"+$('.wpclpl-custom-css').val();
				$('#wpclpl-preview-css').html( cssPreview );
				
			});
			
		});
		</script>
		<?php
		$custom_css =  ( !empty( $wpclpl_plugin_options['wpclpl_logo_url'] ) ) ? wpclpl_build_css_output() : '';		
		?>
		<textarea class="wpclpl-custom-css" name="wpclpl_custom_css" ><?php echo $wpclpl_plugin_options['wpclpl_custom_css']; ?></textarea>
		<br />
		<span class="wpclpl-description"><?php _e('Enter your custom css style for your logo.','wpclpl'); ?><br />
		<?php _e('There\'s nothing to see at the beginning because the login page logo is styled by default.','wpclpl') ?>
		<br /><?php _e('You also may load an example css to start and customize it.','wpclpl'); ?></strong>
		
		<p><input class="wpclpl-logo-example-css-btn button" type="button" value="<?php _e('Load example CSS','wpclpl'); ?>" /> </p>

		<p class="wpclpl-notice">
			<strong><?php _e('Note:  There\'s no need to insert an','wpclpl'); ?> </strong><code><?php _e('background-image','wpclpl'); ?></code> <strong><?php _e('value here, it will be added by default to the final output.','wpclpl'); ?><br />
			<?php 
			// _e('The final css output can be viewed by clicking "Preview CSS" below here.','wpclpl'); 
			_e('The final css output is displayed below.','wpclpl'); 
			?></strong>
			</span>
		</p>
		
		
		<?php
	  } else {
	  
	  	// output the plain css 
		  return $wpclpl_plugin_options['wpclpl_custom_css'];
	  }

}



/*preview of custom css output
****************************************
*/
function wpclpl_settings_custom_css_preview(){

	/*
	<input class="wpclpl-logo-preview-css-btn button" type="button" value="<?php esc_attr_e('Preview CSS','wpclpl') ?>" /> <small>(<?php _e('Just a helpful feature...','wpclpl'); ?>)</small>	
	*/
	
	?>
	<pre id="wpclpl-preview-css"></pre>
	<?php	
}




/*
* output of admin options page
****************************************
*/
function wpclpl_admin_options_page(){ ?>

		<?php 
		// update options, if successful, show message
		if($_POST["wpclpl_save"]==1){ 		
		?>
			<script> jQuery(function($){ $('.wpclpl-settings-save-ok').fadeIn(500).delay(3000).slideUp(500); }); </script>
		<?php
		} // eof if($_POST["wpclpl_save"]==1) 
		?>

		<?php // modal windows: reset image only ?>
		<div class="wpclpl-modal-box wpclpl-modal-box-reset-image">
			<div>
				<h4><?php _e('Confirm reset','wpclpl'); ?></h4>
				<p><?php _e('This will remove the custom image.<br />(File will be kept in the library).','wpclpl'); ?>
				<br />
				<?php _e('Are you sure you want to continue?','wpclpl'); ?></p>
				<p><input type="button" class="wpclpl-reset-cancel button-secondary" value="<?php _e('No, keep settings', 'wpclpl'); ?>" /> <input type="button" class="wpclpl-reset-confirmed button-primary" value="<?php _e('Reset all settings', 'wpclpl'); ?>" /></p>
			</div>			
		</div>
			
			
		<?php // modal windows: reset all settings ?>
		<div class="wpclpl-modal-box wpclpl-modal-box-reset-all">
			<div>
				<h4><?php _e('Confirm reset','wpclpl'); ?></h4>
				<p><?php _e('This will reset all your settings, including the custom image, additonal text and any entered styles.', 'wpclpl'); ?>
				<br />
				<?php _e('Are you sure you want to continue?','wpclpl'); ?></p>
				<p><input type="button" class="wpclpl-reset-cancel button-secondary" value="<?php _e('No, keep settings', 'wpclpl'); ?>" /> <input type="button" class="wpclpl-reset-confirmed button-primary" value="<?php _e('Reset all settings', 'wpclpl'); ?>" /></p>
			</div>			
		</div>	
			
			
    	<div class="wrap">
			<div id="icon-themes" class="icon32"><br /></div>
			<h2><?php _e( 'Custom Login Page Logo', 'wpclpl' ); ?></h2>
			<p class="wpclpl-settings-save-ok"><?php _e('Settings saved.', 'wpclpl'); ?></p>
			<p class="wpclpl-settings-save-error"><?php _e('Error: Could not save settings.', 'wpclpl'); ?><br /><?php _e('Please try again.', 'wpclpl'); ?></p>
			
			<!-- form -->
			<form class="wpclpl-options-form" action="" enctype="multipart/form-data" method="post" enctype="multipart/form-data">
			<?php $wpclpl_plugin_options = (get_option( 'wpclpl_plugin_options' ) != false) ? get_option('wpclpl_plugin_options') : '';
				settings_fields('plugin_wpclpl_options');
				do_settings_sections('wpclpl');
			?>
			  <p class="submit">
			    	<input id="submit_options_form" type="submit" class="button-primary" value="<?php esc_attr_e('Save Settings', 'wpclpl'); ?>" />
					<input name="reset" type="button" class="wpclpl-reset-btn wpclpl-reset-to-default button-secondary" id="wpclpl-modal-box-reset-all" value="<?php _e('Reset to default', 'wpclpl'); ?>" />	
					<input type="hidden" name="wpclpl_save" value="1" />	
			    </p>     
			</form>
			<!-- // form -->
	</div>
<?php
}
   


/*
* if we have post data, save it.
****************************************
*/    
function wpclpl_update_options(){

	// collect values in array...
	$wpclpl_plugin_options_arr = array( 
		'wpclpl_logo_url' => wpclpl_filter_vars( $_POST['wpclpl_logo_url'] ),
		'wpclpl_additional_text' => wpclpl_filter_vars( $_POST['wpclpl_additional_text'] ),
		'wpclpl_custom_css' => wpclpl_filter_vars( $_POST['wpclpl_custom_css'] ) 
	);
	
	// ...and store' em	
	return ( (update_option('wpclpl_plugin_options', $wpclpl_plugin_options_arr )===TRUE) ) ? 1 : 0;
	  
}
      

/*
* enqueue neccessary styles & scripts
****************************************
*/    
function wpclpl_enqueue_styles_scripts() {

	wp_enqueue_style( 'wpclpl_plugin_styles', plugins_url( '/css/wp-custom-login-page-logo.css', __FILE__ ) );
	wp_enqueue_script( 'wpclpl_plugin_scripts', plugins_url( '/js/wp-custom-login-page-logo.js', __FILE__ ), array( 'jquery', 'media-upload', 'thickbox') );
	wp_enqueue_style('thickbox');
	wp_enqueue_script('thickbox');
	wp_enqueue_script('media-upload');
	wp_enqueue_script('wpclpl-upload'); 
	
}
if($_GET['page'] == "wp-custom-login-page-logo.php"){
	add_action( 'admin_enqueue_scripts', 'wpclpl_enqueue_styles_scripts' );
}



/*
* final html output on admin login page
****************************************
*/

function wpclpl_custom_login_logo() {

	// we need jquery here... 
	if(!wp_script_is('jquery')) {

	/* we need jquery BEFORE our script is called. 
	* note: 
	* wp_enqueue_script() always loads jquery AFTER our script which causes an error. 
	* usually there is a true/false parameter to change head or footer output - but for any reason this is ignored on the login page.
	* will look for a better solution in the future. :)
	* i know this is not the best solution, but it works...
	*/
	?>
	<script type="text/javascript" src="http://<?php echo $_SERVER['SERVER_NAME']; ?>/wp-includes/js/jquery/jquery.js"></script>
	<script type="text/javascript" src="http://<?php echo $_SERVER['SERVER_NAME']; ?>/wp-includes/js/jquery/jquery-migrate.min.js"></script>
	<?php }


	global $wpclpl_plugin_options;

	// do we have an image url? -------------------------------------
	if( !empty( $wpclpl_plugin_options['wpclpl_logo_url'] ) ){
		$wpclpl_plugin_logo_url =  esc_url($wpclpl_plugin_options['wpclpl_logo_url']);
	?>
	
	<style type="text/css">                                                                                   
    body.login div#login h1 a {
    	<?php echo wpclpl_settings_logo_plain_url(); ?>
	    <?php echo wpclpl_settings_custom_css(true); ?>
    }
    </style>
 
    <?php

	} else {
		$wpclpl_plugin_logo_url = '';
	} // eof	if( !empty( $wpclpl_plugin_options['wpclpl_logo_url'] ) ) 

}
add_action('login_head', 'wpclpl_custom_login_logo');



/*
* add the footer javascript...
****************************************
*/ 
function wpclpl_footer_js(){
	global $wpclpl_plugin_options;

	// js doesn't like line breaks in strings...
	$wpclpl_additional_text = str_ireplace(array("\r","\n",'\r','\n'),'', $wpclpl_plugin_options['wpclpl_additional_text']);
	
	if($_GET['loggedout'] != "true"){ 
?>
 <script>
    jQuery(function($){
    	var wpclpl_additional_text = '<?php echo $wpclpl_additional_text; ?>';
    	$('<p style="text-align:center">'+wpclpl_additional_text+'</p>').insertAfter("#login h1");
    });	    
    </script>

<?php
    }
}
add_action('login_footer', 'wpclpl_footer_js');



/*
* init settings
****************************************
*/   
function wpclpl_options_settings_init() {  
    
    //register_setting( 'plugin_wpclpl_options', 'plugin_wpclpl_options');  
  
    // Form 
    add_settings_section('wpclpl_settings_header', __( 'Settings', 'wpclpl' ), 'wpclpl_settings_header_text', 'wpclpl');  
  
    // Logo uploader  
    add_settings_field('wpclpl_settings_logo',  __( 'Logo', 'wpclpl' ), 'wpclpl_settings_logo', 'wpclpl', 'wpclpl_settings_header');
    
    // Current Image Preview  
	add_settings_field('wpclpl_settings_logo_preview',  __( 'Logo Preview', 'wpclpl' ), 'wpclpl_settings_logo_preview', 'wpclpl', 'wpclpl_settings_header');  
	
	// additional text to appear
	add_settings_field('wpclpl_settings_add_text',  __( 'Additional Text', 'wpclpl' ), 'wpclpl_settings_add_text', 'wpclpl', 'wpclpl_settings_header');  
	
	// custom css
	add_settings_field('wpclpl_settings_custom_css',  __( 'Custom CSS Styles', 'wpclpl' ), 'wpclpl_settings_custom_css', 'wpclpl', 'wpclpl_settings_header');  
	
	// custom css output: preview
	add_settings_field('wpclpl_settings_custom_css_preview',  __( 'Preview CSS Output', 'wpclpl' ), 'wpclpl_settings_custom_css_preview', 'wpclpl', 'wpclpl_settings_header');  
	
	

}
add_action( 'admin_init', 'wpclpl_options_settings_init' );
