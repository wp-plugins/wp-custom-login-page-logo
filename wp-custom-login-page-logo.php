<?php
/*
Plugin Name: WP Custom Login Page Logo
Plugin URI: http://wp.larsactionhero.com/development/plugins/wp-custom-login-page-logo/
Description: Customize the admin logo on /wp-admin login page.
Version: 1.1.4
Author: Lars Ortlepp
Author URI: http://larsactionhero.com
License: GPL2
*/

/*
* update options!
****************************************
*/
if($_POST['wpclpl_save']==1){
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
	return filter_var( $input, FILTER_SANITIZE_STRING );
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
	<?php _e('This Plugin allows you to change the default (wordpress-) logo at the admin login page.','wpclpl'); ?>
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
        <input type="text" class="wpclpl-logo-url" name="wpclpl_logo_url" value="<?php echo $wpclpl_plugin_logo_url; ?>" placeholder="<?php _e('Insert URL here or select image with button below.','wpclpl'); ?>" /><br />
        <span class="wpclpl-description"><?php _e('Insert image url here','wpclpl'); ?> <code>(e.g. http://www.mywebsite.com/wp-content/themes/mytheme/images/mylogo.jpg)</code>
	        <br /><?php _e('Or select an image with button below.','wpclpl'); ?></span>
        <input class="wpclpl-logo-upload-btn button" type="button" value="<?php esc_attr_e('Select an image file','wpclpl'); ?>" />  
        <span class="wpclpl-description"><?php _e('Select an existing image from the media library or upload a new one.','wpclpl');?></span>
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
		<div class="wpclpl-logo-preview-wrap" style="min-height: 100px;">  
			<a href="<?php echo esc_url( $wpclpl_plugin_options['wpclpl_logo_url'] );?>?TB_inline=true&height=400&width=400&inlineId=wpclpl-logo-preview"  class="thickbox">
				<img style="max-width:100%;" class="wpclpl-logo-preview" src="<?php echo esc_url( $wpclpl_plugin_options['wpclpl_logo_url'] ); ?>" id="wpclpl-logo-preview" /></a>
		</div>
		<p>
		<?php
		 wpclpl_image_dimensions();
		 ?>
		</p>
		<p>
			<input class="wpclpl-logo-remove-img-btn button" type="button" value="<?php esc_attr_e('Remove Image','wpclpl'); ?>" />
			<span class="wpclpl-description">(<?php _e('File in Media Library will not be deleted','wpclpl'); ?>)</span>
		</p>
		<?php
		
	} else {
		?>
		<div class="wpclpl-currentlogo"></div>
		<br clear="left" />
		<p class="wpclpl-default-logo">(<?php _e('Default WP Logo','wpclpl'); ?>)</p>
		<?php
	}

}  


/*
* returns image dimensions
****************************************
*/
function wpclpl_image_dimensions( $return = false ){

	global $wpclpl_plugin_options;
	
	if( !empty( $wpclpl_plugin_options['wpclpl_logo_url'] ) ){
		
		$server_url = 'http://'.$_SERVER['SERVER_NAME'];
		$wpclpl_logo_url = esc_url($wpclpl_plugin_options['wpclpl_logo_url']);
		
		$wpclpl_logo_url = str_replace($server_url, '..',$wpclpl_logo_url);
		$wpclpl_logo_dimensions = getimagesize( $wpclpl_logo_url );
		
		$wpclpl_logo_width = $wpclpl_logo_dimensions[0];
		$wpclpl_logo_height = $wpclpl_logo_dimensions[1];
		
		if( !$return ){
		?>
		<span class="wpclpl-description"><?php _e('Original size','wpclpl'); ?>: <span id="wpclpl-logo-width"><?php echo $wpclpl_logo_width; ?></span> x <span id="wpclpl-logo-height"><?php echo $wpclpl_logo_height; ?></span>px</span>
		<?php
		} else {
		return array($wpclpl_logo_width,$wpclpl_logo_height);
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
				
				<?php if(!empty($wpclpl_logo_url)) { ?>
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
		
		<p><input class="wpclpl-logo-example-css-btn button" type="button" value="Load example CSS" /> </p>

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
		 
			 if( wpclpl_update_options() ){
			 ?>
				<script> jQuery(function($){ $('.settings-save-ok').fadeIn(500).delay(3000).slideUp(500); }); </script>
			 <?php
			 } else {
			  ?>
				<script> jQuery(function($){ $('.settings-save-error').fadeIn(500).delay(3000).slideUp(500); }); </script>
			 <?php
			 
			 } // eof  if( wpclpl_update_options() )

		 } // eof if($_POST["wpclpl_save"]==1) 
		 ?>

			<div class="wpclpl-modal-box">
				<div>
					<h4>Confirm reset</h4>
					<p><?php _e('This will reset all your settings, including the custom image, additonal text and any entered custom css.'); ?>
					<br />
					<?php _e('Are you sure you want to continue?'); ?></p>
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
					<input name="reset" type="button" class="wpclpl-reset-btn  button-secondary" value="<?php _e('Reset to default', 'wpclpl'); ?>" />	
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
	return ( update_option('wpclpl_plugin_options', $wpclpl_plugin_options_arr ) )  ? 1 : 0;
	  
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
function custom_login_enqueue_scripts(){
	wp_enqueue_script('jquery');
}


function wpclpl_custom_login_logo() {

	global $wpclpl_plugin_options;

	// js doesn't like line breaks in strings...
	$wpclpl_additional_text = str_ireplace(array("\r","\n",'\r','\n'),'', $wpclpl_plugin_options['wpclpl_additional_text']);
	
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
    // do we have addition text? -------------------------------------
    if(!empty($wpclpl_additional_text)) {

	// we need jquery here... 
	if(wp_script_is('jquery')) {
	   // zzzz...
	} else {
	   add_action( 'login_enqueue_scripts', 'custom_login_enqueue_scripts' );
	}

 ?>
    
    <script>
    jQuery(function($){
    	var wpclpl_additional_text = '<?php echo $wpclpl_additional_text; ?>';
    	$('<p style="text-align:center">'+wpclpl_additional_text+'</p>').insertAfter("#login h1");
    });	    
    </script>
    
    <?php } // eof if( !empty($wpclpl_additional_text) )
	} else {
		$wpclpl_plugin_logo_url = '';
	} // eof	if( !empty( $wpclpl_plugin_options['wpclpl_logo_url'] ) ) 

}
add_action('login_head', 'wpclpl_custom_login_logo');




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
