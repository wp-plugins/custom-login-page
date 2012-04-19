<?php
/*
Plugin Name: A5 Custom Login Page
Description: Just customize your loginpage (or that of your community etc.) by giving the WP login page a different look, with your own logo and special colours and styles.
Version: 1.3
Author: Waldemar Stoffel
Author URI: http://www.waldemarstoffel.com
Plugin URI: http://wasistlos.waldemarstoffel.com/plugins-fur-wordpress/custom-login-page
License: GPL3
Text Domain: custom-login-page
*/

/*  Copyright 2011 Waldemar Stoffel  (email: stoffel@atelier-fuenf.de)

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/

/**
 *
 * Stop direct call
 *
 */
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die("Sorry, you don't have direct access to this page."); }

$clp_options=get_option('clp_options');

/**
 *
 * Adds links to the plugin page
 *
 */
function clp_register_links($links, $file) {
	
	$base = plugin_basename(__FILE__);
	if ($file == $base) {
		$links[] = '<a href="'.admin_url().'themes.php?page=clp-settings">'.__('Settings', 'custom-login-page').'</a>';
		$links[] = '<a href="http://wordpress.org/extend/plugins/custom-login-page/faq/" target="_blank">'.__('FAQ', 'custom-login-page').'</a>';
		$links[] = '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=32XGSBKTQNNHA" target="_blank">'.__('Donate', 'custom-login-page').'</a>';
	}
	
	return $links;

}
add_filter('plugin_row_meta', 'clp_register_links',10,2);

/**
 *
 * Changes the link behind the logo
 *
 */
function clp_headerurl() {
	
	global $clp_options;
	return $clp_options['url'];
	
}
if (!empty($clp_options['url'])) add_filter( 'login_headerurl', 'clp_headerurl' );

/**
 *
 * Changes the Title tag of the logo
 *
 */
function clp_headertitle() {
	
	global $clp_options;
	return $clp_options['title'];
	
}
if (!empty($clp_options['title'])) add_filter( 'login_headertitle', 'clp_headertitle' );

/**
 *
 * Adds the style sheet to the login page
 *
 */
function clp_login_css() {
	
	$clp_css_file=get_bloginfo('url').'/?clpfile=css';
	
	echo "<link rel='stylesheet' id='clp-css' href='" .$clp_css_file. "' type='text/css' media='all' />\r\n";
	
}
add_action('login_enqueue_scripts', 'clp_login_css');


/**
 *
 * Importing language file
 *
 */
load_plugin_textdomain('custom-login-page', false , basename(dirname(__FILE__)).'/languages');


/**
 *
 * Setting version on activation
 *
 */
register_activation_hook(  __FILE__, 'start_clp' );

function start_clp() {
	
	add_option('clp_options', array('version' => '1.3'));

}

/**
 *
 * Cleaning on deactivation
 *
 */
register_deactivation_hook(  __FILE__, 'unset_clp' );

function unset_clp() {
	
	delete_option('clp_options');
	
}

/**
 *
 * Creating Settings Page
 *
 */
function clp_admin_menu() {
	
	$pages=add_theme_page('A5 Custom Login Page', 'A5 Custom Login Page', 'administrator', 'clp-settings', 'clp_options_page');	
	
	add_action('admin_print_styles-'.$pages, 'clp_admin_css');
	add_action('admin_print_scripts-'.$pages, 'clp_admin_js');
}
add_action('admin_menu', 'clp_admin_menu');

/**
 *
 * register styles and scripts for settings page
 *
 */
function clp_register_admin_extras() {
	 
	 $clp_options=get_option('clp_options');
	 
	 wp_register_style('clp-admin', plugins_url('/css/clp-admin-css.css', __FILE__), false, $clp_options['version'], 'all');
	 wp_register_script('clp-admin-script', plugins_url('/js/clp-admin.js', __FILE__), array('jquery'), $clp_options['version'], false);
	 wp_register_script('clp-colorpicker', plugins_url('/js/jscolor/jscolor.js', __FILE__), false, '1.3.11', false);
	 wp_register_script('clp-admin-tabs', plugins_url('/js/tabcontent.js', __FILE__), false, '2.2', false);

}
add_action('admin_init', 'clp_register_admin_extras');

/**
 *
 * Adding stylesheet to settings page
 *
 */
function clp_admin_css() {
	
	wp_enqueue_style('clp-admin');
	
}

/**
 *
 * Adding scripts to settings page
 *
 */
function clp_admin_js() {
	
	wp_enqueue_script('clp-admin-script');
	wp_enqueue_script('clp-colorpicker');
	wp_enqueue_script('clp-admin-tabs');
	wp_localize_script('clp-admin-script', 'message', clp_localize_admin());
	
}

/**
 *
 * Adding l10n to the script
 *
 */
 function clp_localize_admin() {
	
	return array (
		
		'saving' => __('Saving...', 'custom-login-page')
		
		);
	
}

/**
 *
 * settings page
 *
 */
function clp_options_page() {
	
	global $clp_options;

	?>
	
<table width="100%" cellpadding="2" cellspacing="0"><tr><td valign="middle" width="380"><h2 style="margin:0 30px 0 0; padding: 5px 0 5px 0;">
A5 Custom Login Page <?php _e('Settings', 'custom-login-page'); ?></h2></td><td valign="middle">&nbsp;</td>
</tr></table>

<div class="wrap" style="margin: 0 10px 0 0">
	
<table>
<tr>
<td valign="top" width="200">

<ul id="clp-pagetabs">
	<li><a href="#" id="main-tab" rel="main" class="selected"><?php _e('Body', 'custom-login-page'); ?></a></li>
	<li><a href="#" id="logindiv-tab" rel="logindiv"><?php _e('Login Container', 'custom-login-page'); ?></a></li>
    <li><a href="#" id="loginform-tab" rel="loginform"><?php _e('Login Form', 'custom-login-page'); ?></a></li>
    <li><a href="#" id="button-tab" rel="button"><?php _e('Button', 'custom-login-page'); ?></a></li>
    <li><a href="#" id="message-tab" rel="message"><?php _e('Messages and Input Fields', 'custom-login-page'); ?></a></li>
    <li><a href="#" id="link-tab" rel="link"><?php _e('Links', 'custom-login-page'); ?></a></li>
</ul>

</td>
<td valign="top" width="100%">
<div id="main" class="tabcontent">
    <form method="post" name="main_form" id="main_form" action="">
      <div class="clp-container">
        <div class="clp-container-left">
        <?php wp_nonce_field('save_main','mainnonce'); ?>
        <label for="logo"><?php _e('Logo URL', 'custom-login-page'); ?></label>
        <input name="logo" id="logo" type="text" size="40" style="width: 95%;" value="<?php echo $clp_options['logo']; ?>" />
        <label for="url"><?php _e('URL to link to', 'custom-login-page'); ?></label>
        <input name="url" id="url" type="text" size="40" style="width: 95%;" value="<?php echo $clp_options['url']; ?>" />
        <label for="title"><?php _e('Title tag of the logo', 'custom-login-page'); ?></label>
        <input name="title" id="title" type="text" size="40" style="width: 95%;" value="<?php echo $clp_options['title']; ?>" />
        </div>
        <div class="clp-container-right">
        <h2><?php _e('Logo', 'custom-login-page'); ?></h2>
        <div id="mainmsg"></div>
        <p><?php _e('You can enter the url of the logo, that you want to have in place of the WP logo on the login screen. Just upload any picture (best is a png or gif with transparent background) via the uploader on the Media section and copy the url of that file here.', 'custom-login-page'); ?></p>
        <p><?php _e('In the URL field, you enter the URL to which the logo should link.', 'custom-login-page'); ?></p>
        <p><?php _e('Title refers to the title tag behind the logo.', 'custom-login-page'); ?></p>
        <p><i><?php _e('You can leave any of the fields empty to keep the default settings of Wordpress.', 'custom-login-page'); ?></i></p>
        </div>
        <div style="clear: both;"></div>
	  </div>
      <div class="clp-container">
        <div class="clp-container-left">
        <label for="body_background"><?php _e('Background Picture', 'custom-login-page'); ?></label>
        <input name="body_background" id="body_background" type="text" size="40" style="width: 95%;" value="<?php echo $clp_options['body_background']; ?>" />
        <label for="body_img_repeat"><?php _e('Background Repeat', 'custom-login-page'); ?></label>
        <select name="body_img_repeat" id="body_img_repeat" style="width: 135px;">
        <option value=""><?php _e('default', 'custom-login-page'); ?></option>
        <option value="no-repeat"<?php if ($clp_options['body_img_repeat']=='repeat-x') echo ' selected="selected"'; ?>>no-repeat</option>
        <option value="repeat-x"<?php if ($clp_options['body_img_repeat']=='repeat-x') echo ' selected="selected"'; ?>>repeat-x</option>
        <option value="repeat-y"<?php if ($clp_options['body_img_repeat']=='repeat-y') echo ' selected="selected"'; ?>>repeat-y</option>
        </select>         
        <label for="body_img_pos"><?php _e('Position of the Background Picture', 'custom-login-page'); ?></label>
        <input name="body_img_pos" id="body_img_pos" type="text" size="40" style="width: 95%;" value="<?php echo $clp_options['body_img_pos']; ?>" />        
        <label for="body_bg_color1"><?php _e('Background Colour', 'custom-login-page'); ?></label>
        <input name="body_bg_color1" id="body_bg_color1" type="text" value="<?php echo $clp_options['body_bg_color1']; ?>" class="color {hash:true,caps:false,required:false,pickerPosition:'right'}"/>
        <label for="body_bg_color2"><?php _e('Second Background Colour (for Gradient)', 'custom-login-page'); ?></label>
        <input name="body_bg_color2" id="body_bg_color2" type="text" value="<?php echo $clp_options['body_bg_color2']; ?>" class="color {hash:true,caps:false,required:false,pickerPosition:'right'}"/>               
        <label for="body_text_color"><?php _e('Text Colour', 'custom-login-page'); ?></label>
        <input name="body_text_color" id="body_text_color" type="text" value="<?php echo $clp_options['body_text_color']; ?>" class="color {hash:true,caps:false,required:false,pickerPosition:'right'}"/>
        </div>
        <div class="clp-container-right">
        <h2><?php _e('Body', 'custom-login-page'); ?></h2>
        <p><?php _e('You can enter the url of the background picture, that you want to have on the login page. Just upload any picture via the uploader on the Media section and copy the url of that file here. Leave it empty, if you don&#39;t want a picture. Background images are tiled by default. You can select the direction of repeating the image or to not repeat it. The position of the image can be something like &#39;100px 50%&#39; or &#39center top&#39;.', 'custom-login-page'); ?></p>
        <p><?php _e('In the last section, you choose the background colour and the colour of the text in the html body element. If you give two background colours, you can create a gradient. Colour no. 1 will always be up.', 'custom-login-page'); ?></p>
        <p><i><?php _e('You can leave any of the fields empty to keep the default settings of Wordpress.', 'custom-login-page'); ?></i></p>
        </div>
        <div style="clear: both;"></div>
	  </div>    
      <div id="submit-container" class="clp-container" style="background: none repeat scroll 0% 0% transparent; border: medium none;">	
		<p class="submit">
		<input class="save-tab" name="main_save" id="main_save" value="<?php esc_attr_e('Save Changes', 'custom-login-page'); ?>" type="submit"><img src="<?php admin_url(); ?>/wp-admin/images/wpspin_light.gif" alt="" class="main-save" style="display: none;" />
		<span style="font-weight: bold; color:#243e1f"><?php _e('Save style', 'custom-login-page'); ?></span>
		</p></div>
    </form>
</div>
<div id="logindiv" class="tabcontent">
    <form method="post" name="logindiv_form" id="logindiv_form" action="">
      <div class="clp-container">
        <div class="clp-container-left">
        <?php wp_nonce_field('save_logindiv','logindivnonce'); ?>
        <label for="logindiv_background"><?php _e('Background Picture', 'custom-login-page'); ?></label>
        <input name="logindiv_background" id="logindiv_background" type="text" size="40" style="width: 95%;" value="<?php echo $clp_options['logindiv_background']; ?>" />
        <label for="logindiv_img_repeat"><?php _e('Background Repeat', 'custom-login-page'); ?></label>
        <select name="logindiv_img_repeat" id="logindiv_img_repeat" style="width: 150px;">
        <option value=""><?php _e('default', 'custom-login-page'); ?></option>
        <option value="no-repeat"<?php if ($clp_options['logindiv_img_repeat']=='no-repeat') echo ' selected="selected"'; ?>>no-repeat</option>
        <option value="repeat-x"<?php if ($clp_options['logindiv_img_repeat']=='repeat-x') echo ' selected="selected"'; ?>>repeat-x</option>
        <option value="repeat-y"<?php if ($clp_options['logindiv_img_repeat']=='repeat-y') echo ' selected="selected"'; ?>>repeat-y</option>
        </select>         
        <label for="logindiv_img_pos"><?php _e('Position of the Background Picture', 'custom-login-page'); ?></label>
        <input name="logindiv_img_pos" id="logindiv_img_pos" type="text" size="40" style="width: 95%;" value="<?php echo $clp_options['logindiv_img_pos']; ?>" />        
        <label for="logindiv_bg_color1"><?php _e('Background Colour', 'custom-login-page'); ?></label>
        <input name="logindiv_bg_color1" id="logindiv_bg_color1" type="text" value="<?php echo $clp_options['logindiv_bg_color1']; ?>" class="color {hash:true,caps:false,required:false,pickerPosition:'right'}"/>
        <label for="logindiv_bg_color2"><?php _e('Second Background Colour (for Gradient)', 'custom-login-page'); ?></label>
        <input name="logindiv_bg_color2" id="logindiv_bg_color2" type="text" value="<?php echo $clp_options['logindiv_bg_color2']; ?>" class="color {hash:true,caps:false,required:false,pickerPosition:'right'}"/>               
        <label for="logindiv_text_color"><?php _e('Text Colour', 'custom-login-page'); ?></label>
        <input name="logindiv_text_color" id="logindiv_text_color" type="text" value="<?php echo $clp_options['logindiv_text_color']; ?>" class="color {hash:true,caps:false,required:false,pickerPosition:'right'}"/>
        <label for="logindiv_border_style"><?php _e('Border Style', 'custom-login-page'); ?></label>
        <select name="logindiv_border_style" id="logindiv_border_style" style="width: 220px;">
        <option value=""><?php _e('choose a border style', 'custom-login-page'); ?></option>
        <option value="none"<?php if ($clp_options['logindiv_border_style']=='none') echo ' selected="selected"'; ?>>none</option>
        <option value="dotted"<?php if ($clp_options['logindiv_border_style']=='dotted') echo ' selected="selected"'; ?>>dotted</option>
        <option value="dashed"<?php if ($clp_options['logindiv_border_style']=='dashed') echo ' selected="selected"'; ?>>dashed</option>
        <option value="solid"<?php if ($clp_options['logindiv_border_style']=='solid') echo ' selected="selected"'; ?>>solid</option>
        <option value="double"<?php if ($clp_options['logindiv_border_style']=='double') echo ' selected="selected"'; ?>>double</option>
        <option value="groove"<?php if ($clp_options['logindiv_border_style']=='groove') echo ' selected="selected"'; ?>>groove</option>
        <option value="ridge"<?php if ($clp_options['logindiv_border_style']=='ridge') echo ' selected="selected"'; ?>>ridge</option>
        <option value="inset"<?php if ($clp_options['logindiv_border_style']=='inset') echo ' selected="selected"'; ?>>inset</option>
        <option value="outset"<?php if ($clp_options['logindiv_border_style']=='outset') echo ' selected="selected"'; ?>>outset</option>
        </select>
        <label for="logindiv_border_width"><?php _e('Border Width (in px)', 'custom-login-page'); ?></label>
        <input name="logindiv_border_width" id="logindiv_border_width" type="text" value="<?php echo $clp_options['logindiv_border_width']; ?>" />
        <label for="logindiv_border_color"><?php _e('Border Colour', 'custom-login-page'); ?></label>
        <input name="logindiv_border_color" id="logindiv_border_color" type="text" value="<?php echo $clp_options['logindiv_border_color']; ?>" class="color {hash:true,caps:false,required:false,pickerPosition:'right'}"/>
        <label for="logindiv_border_round"><?php _e('Rounded Corners (in px)', 'custom-login-page'); ?></label>
        <input name="logindiv_border_round" id="logindiv_border_round" type="text" value="<?php echo $clp_options['logindiv_border_round']; ?>" />
        <label for="logindiv_shadow_x"><?php _e('Shadow (x-direction in px)', 'custom-login-page'); ?></label>
        <input name="logindiv_shadow_x" id="logindiv_shadow_x" type="text" value="<?php echo $clp_options['logindiv_shadow_x']; ?>" />
        <label for="logindiv_shadow_y"><?php _e('Shadow (y-direction in px)', 'custom-login-page'); ?></label>
        <input name="logindiv_shadow_y" id="logindiv_shadow_y" type="text" value="<?php echo $clp_options['logindiv_shadow_y']; ?>" />
        <label for="logindiv_shadow_softness"><?php _e('Shadow (softness in px)', 'custom-login-page'); ?></label>
        <input name="logindiv_shadow_softness" id="logindiv_shadow_softness" type="text" value="<?php echo $clp_options['logindiv_shadow_softness']; ?>" />
        <label for="logindiv_shadow_color"><?php _e('Shadow Colour', 'custom-login-page'); ?></label>
        <input name="logindiv_shadow_color" id="logindiv_shadow_color" type="text" value="<?php echo $clp_options['logindiv_shadow_color']; ?>" class="color {hash:true,caps:false,required:false,pickerPosition:'right'}"/>
        </div>
        <div class="clp-container-right">
        <h2><?php _e('Login Container', 'custom-login-page'); ?></h2>
        <div id="logindivmsg"></div>
        <p><?php _e('You can enter the url of the background picture, that you want to have on the login container. Just upload any picture via the uploader on the Media section and copy the url of that file here. Leave it empty, if you don&#39;t want a picture. Background images are tiled by default. You can select the direction of repeating the image or to not repeat it. The position of the image can be something like &#39;100px 50%&#39; or &#39;center top&#39;.', 'custom-login-page'); ?></p>
        <p><?php _e('In the next section, you choose the background colour and the colour of the text in the login container. If you give two background colours, you can create a gradient. Colour no. 1 will always be up.', 'custom-login-page'); ?></p>
        <p><?php _e('Choose a border, if wanting one. Define style, width and whether or not, you want to have rounded corners (is not supported by all browsers).', 'custom-login-page'); ?></p>
        <p><?php _e('At last, give the container a shadow (is not supported by all browsers).', 'custom-login-page'); ?></p>
        <p><i><?php _e('You can leave any of the fields empty to keep the default settings of Wordpress.', 'custom-login-page'); ?></i></p>
        </div>
        <div style="clear: both;"></div>
	  </div>
      <div class="clp-container">
        <div class="clp-container-left">
        <label for="logindiv_left"><?php _e('Position (x-direction in px)', 'custom-login-page'); ?></label>
        <input name="logindiv_left" id="logindiv_left" type="text" value="<?php echo $clp_options['logindiv_left']; ?>" />
        <label for="logindiv_top"><?php _e('Position (y-direction in px)', 'custom-login-page'); ?></label>
        <input name="logindiv_top" id="logindiv_top" type="text" value="<?php echo $clp_options['logindiv_top']; ?>" />
        <label for="logindiv_margin"><?php _e('Margin (in px)', 'custom-login-page'); ?></label>
        <input name="logindiv_margin" id="logindiv_margin" type="text" value="<?php echo $clp_options['logindiv_margin']; ?>" />
        </div>
        <div class="clp-container-right">
        <h2><?php _e('Position of the Login Container', 'custom-login-page'); ?></h2>
        <p><?php _e('Here you can give the whole login container a position. If you enter &#39;0&#39; in both of the fields, it will be in the top left corner. The margin defines the empty space around the container.', 'custom-login-page'); ?></p>
        <p><i><?php _e('You can leave any of the fields empty to keep the default settings of Wordpress.', 'custom-login-page'); ?></i></p>
        </div>
        <div style="clear: both;"></div>
	  </div> 
      <div id="submit-container" class="clp-container" style="background: none repeat scroll 0% 0% transparent; border: medium none;">	
		<p class="submit">
		<input class="save-tab" name="logindiv_save" id="logindiv_save" value="<?php esc_attr_e('Save Changes', 'custom-login-page'); ?>" type="submit"><img src="<?php admin_url(); ?>/wp-admin/images/wpspin_light.gif" alt="" class="logindiv_save" style="display: none;" />
		<span style="font-weight: bold; color:#243e1f"><?php _e('Save style', 'custom-login-page'); ?></span>
		</p></div>
    </form>
</div>
<div id="loginform" class="tabcontent">
    <form method="post" name="loginform_form" id="loginform_form" action="">
      <div class="clp-container">
        <div class="clp-container-left">
        <?php wp_nonce_field('save_loginform','loginformnonce'); ?>
        <label for="loginform_background"><?php _e('Background Picture', 'custom-login-page'); ?></label>
        <input name="loginform_background" id="loginform_background" type="text" size="40" style="width: 95%;" value="<?php echo $clp_options['loginform_background']; ?>" />
        <label for="loginform_img_repeat"><?php _e('Background Repeat', 'custom-login-page'); ?></label>
        <select name="loginform_img_repeat" id="loginform_img_repeat" style="width: 150px;">
        <option value=""><?php _e('default', 'custom-login-page'); ?></option>
        <option value="no-repeat"<?php if ($clp_options['loginform_img_repeat']=='no-repeat') echo ' selected="selected"'; ?>>no-repeat</option>
        <option value="repeat-x"<?php if ($clp_options['loginform_img_repeat']=='repeat-x') echo ' selected="selected"'; ?>>repeat-x</option>
        <option value="repeat-y"<?php if ($clp_options['loginform_img_repeat']=='repeat-y') echo ' selected="selected"'; ?>>repeat-y</option>
        </select>         
        <label for="loginform_img_pos"><?php _e('Position of the Background Picture', 'custom-login-page'); ?></label>
        <input name="loginform_img_pos" id="loginform_img_pos" type="text" size="40" style="width: 95%;" value="<?php echo $clp_options['loginform_img_pos']; ?>" />        
        <label for="loginform_bg_color1"><?php _e('Background Colour', 'custom-login-page'); ?></label>
        <input name="loginform_bg_color1" id="loginform_bg_color1" type="text" value="<?php echo $clp_options['loginform_bg_color1']; ?>" class="color {hash:true,caps:false,required:false,pickerPosition:'right'}"/>
        <label for="loginform_bg_color2"><?php _e('Second Background Colour (for Gradient)', 'custom-login-page'); ?></label>
        <input name="loginform_bg_color2" id="loginform_bg_color2" type="text" value="<?php echo $clp_options['loginform_bg_color2']; ?>" class="color {hash:true,caps:false,required:false,pickerPosition:'right'}"/>               
        <label for="loginform_text_color"><?php _e('Text Colour', 'custom-login-page'); ?></label>
        <input name="loginform_text_color" id="loginform_text_color" type="text" value="<?php echo $clp_options['loginform_text_color']; ?>" class="color {hash:true,caps:false,required:false,pickerPosition:'right'}"/>
        <label for="loginform_border_style"><?php _e('Border Style', 'custom-login-page'); ?></label>
        <select name="loginform_border_style" id="loginform_border_style" style="width: 220px;">
        <option value=""><?php _e('choose a border style', 'custom-login-page'); ?></option>
        <option value="none"<?php if ($clp_options['loginform_border_style']=='none') echo ' selected="selected"'; ?>>none</option>
        <option value="dotted"<?php if ($clp_options['loginform_border_style']=='dotted') echo ' selected="selected"'; ?>>dotted</option>
        <option value="dashed"<?php if ($clp_options['loginform_border_style']=='dashed') echo ' selected="selected"'; ?>>dashed</option>
        <option value="solid"<?php if ($clp_options['loginform_border_style']=='solid') echo ' selected="selected"'; ?>>solid</option>
        <option value="double"<?php if ($clp_options['loginform_border_style']=='double') echo ' selected="selected"'; ?>>double</option>
        <option value="groove"<?php if ($clp_options['loginform_border_style']=='groove') echo ' selected="selected"'; ?>>groove</option>
        <option value="ridge"<?php if ($clp_options['loginform_border_style']=='ridge') echo ' selected="selected"'; ?>>ridge</option>
        <option value="inset"<?php if ($clp_options['loginform_border_style']=='inset') echo ' selected="selected"'; ?>>inset</option>
        <option value="outset"<?php if ($clp_options['loginform_border_style']=='outset') echo ' selected="selected"'; ?>>outset</option>
        </select>
        <label for="loginform_border_width"><?php _e('Border Width (in px)', 'custom-login-page'); ?></label>
        <input name="loginform_border_width" id="loginform_border_width" type="text" value="<?php echo $clp_options['loginform_border_width']; ?>" />
        <label for="loginform_border_color"><?php _e('Border Colour', 'custom-login-page'); ?></label>
        <input name="loginform_border_color" id="loginform_border_color" type="text" value="<?php echo $clp_options['loginform_border_color']; ?>" class="color {hash:true,caps:false,required:false,pickerPosition:'right'}"/>
        <label for="loginform_border_round"><?php _e('Rounded Corners (in px)', 'custom-login-page'); ?></label>
        <input name="loginform_border_round" id="loginform_border_round" type="text" value="<?php echo $clp_options['loginform_border_round']; ?>" />
        <label for="loginform_shadow_x"><?php _e('Shadow (x-direction in px)', 'custom-login-page'); ?></label>
        <input name="loginform_shadow_x" id="loginform_shadow_x" type="text" value="<?php echo $clp_options['loginform_shadow_x']; ?>" />
        <label for="loginform_shadow_y"><?php _e('Shadow (y-direction in px)', 'custom-login-page'); ?></label>
        <input name="loginform_shadow_y" id="loginform_shadow_y" type="text" value="<?php echo $clp_options['loginform_shadow_y']; ?>" />
        <label for="loginform_shadow_softness"><?php _e('Shadow (softness in px)', 'custom-login-page'); ?></label>
        <input name="loginform_shadow_softness" id="loginform_shadow_softness" type="text" value="<?php echo $clp_options['loginform_shadow_softness']; ?>" />
        <label for="loginform_shadow_color"><?php _e('Shadow Colour', 'custom-login-page'); ?></label>
        <input name="loginform_shadow_color" id="loginform_shadow_color" type="text" value="<?php echo $clp_options['loginform_shadow_color']; ?>" class="color {hash:true,caps:false,required:false,pickerPosition:'right'}"/>
        </div>
        <div class="clp-container-right">
        <h2><?php _e('Login Form', 'custom-login-page'); ?></h2>
        <div id="loginmsg"></div>
        <p><?php _e('You can enter the url of the background picture, that you want to have in the login form. Just upload any picture via the uploader on the Media section and copy the url of that file here. Leave it empty, if you don&#39;t want a picture. Background images are tiled by default. You can select the direction of repeating the image or to not repeat it. The position of the image can be something like &#39;100px 50%&#39; or &#39center top&#39;.', 'custom-login-page'); ?></p>
        <p><?php _e('In the next section, you choose the background colour and the colour of the text in the login form. If you give two background colours, you can create a gradient. Colour no. 1 will always be up.', 'custom-login-page'); ?></p>
        <p><?php _e('Choose a border, if wanting one. Define style, width and whether or not, you want to have rounded corners (is not supported by all browsers).', 'custom-login-page'); ?></p>
        <p><?php _e('At last, give the form a shadow (is not supported by all browsers).', 'custom-login-page'); ?></p>
        <p><i><?php _e('You can leave any of the fields empty to keep the default settings of Wordpress.', 'custom-login-page'); ?></i></p>
        </div>
        <div style="clear: both;"></div>
	  </div> 
      <div id="submit-container" class="clp-container" style="background: none repeat scroll 0% 0% transparent; border: medium none;">	
		<p class="submit">
		<input class="save-tab" name="loginform_save" id="loginform_save" value="<?php esc_attr_e('Save Changes', 'custom-login-page'); ?>" type="submit"><img src="<?php admin_url(); ?>/wp-admin/images/wpspin_light.gif" alt="" class="loginform_save" style="display: none;" />
		<span style="font-weight: bold; color:#243e1f"><?php _e('Save style', 'custom-login-page'); ?></span>
		</p></div>
    </form>
</div>
<div id="button" class="tabcontent">
    <form method="post" name="button_form" id="button_form" action="">
      <div class="clp-container">
        <div class="clp-container-left">
        <?php wp_nonce_field('save_button','buttonnonce'); ?>
        <label for="button_bg_color1"><?php _e('Background Colour', 'custom-login-page'); ?></label>
        <input name="button_bg_color1" id="button_bg_color1" type="text" value="<?php echo $clp_options['button_bg_color1']; ?>" class="color {hash:true,caps:false,required:false,pickerPosition:'right'}"/>
        <label for="button_bg_color2"><?php _e('Second Background Colour (for Gradient)', 'custom-login-page'); ?></label>
        <input name="button_bg_color2" id="button_bg_color2" type="text" value="<?php echo $clp_options['button_bg_color2']; ?>" class="color {hash:true,caps:false,required:false,pickerPosition:'right'}"/>               
        <label for="button_text_color"><?php _e('Text Colour', 'custom-login-page'); ?></label>
        <input name="button_text_color" id="button_text_color" type="text" value="<?php echo $clp_options['button_text_color']; ?>" class="color {hash:true,caps:false,required:false,pickerPosition:'right'}"/>
        <label for="button_border_color"><?php _e('Border Colour', 'custom-login-page'); ?></label>
        <input name="button_border_color" id="button_border_color" type="text" value="<?php echo $clp_options['button_border_color']; ?>" class="color {hash:true,caps:false,required:false,pickerPosition:'right'}"/>
        <label for="btn_hover_bg_color1"><?php _e('Hover Background Colour', 'custom-login-page'); ?></label>
        <input name="btn_hover_bg_color1" id="btn_hover_bg_color1" type="text" value="<?php echo $clp_options['btn_hover_bg_color1']; ?>" class="color {hash:true,caps:false,required:false,pickerPosition:'right'}"/>
        <label for="btn_hover_bg_color2"><?php _e('Second Hover Background Colour (for Gradient)', 'custom-login-page'); ?></label>
        <input name="btn_hover_bg_color2" id="btn_hover_bg_color2" type="text" value="<?php echo $clp_options['btn_hover_bg_color2']; ?>" class="color {hash:true,caps:false,required:false,pickerPosition:'right'}"/>               
        <label for="btn_hover_text_color"><?php _e('Hover Text Colour', 'custom-login-page'); ?></label>
        <input name="btn_hover_text_color" id="btn_hover_text_color" type="text" value="<?php echo $clp_options['btn_hover_text_color']; ?>" class="color {hash:true,caps:false,required:false,pickerPosition:'right'}"/>
        <label for="btn_hover_border_color"><?php _e('Hover Border Colour', 'custom-login-page'); ?></label>
        <input name="btn_hover_border_color" id="btn_hover_border_color" type="text" value="<?php echo $clp_options['btn_hover_border_color']; ?>" class="color {hash:true,caps:false,required:false,pickerPosition:'right'}"/>
        </div>
        <div class="clp-container-right">
        <h2><?php _e('Submit Button', 'custom-login-page'); ?></h2>
        <div id="buttonmsg"></div>
        <p><?php _e('Enter the background, text and border colour of the submit button here. The button will look static if you don&#39;t give values for the hover state of it. If you want to have a gradient, enter two background colours. The first one will be up then.', 'custom-login-page'); ?></p>
        <p><i><?php _e('You can leave any of the fields empty to keep the default settings of Wordpress.', 'custom-login-page'); ?></i></p>
        </div>
        <div style="clear: both;"></div>
	  </div>    
      <div id="submit-container" class="clp-container" style="background: none repeat scroll 0% 0% transparent; border: medium none;">	
		<p class="submit">
		<input class="save-tab" name="button_save" id="button_save" value="<?php esc_attr_e('Save Changes', 'custom-login-page'); ?>" type="submit"><img src="<?php admin_url(); ?>/wp-admin/images/wpspin_light.gif" alt="" class="button_save" style="display: none;" />
		<span style="font-weight: bold; color:#243e1f"><?php _e('Save style', 'custom-login-page'); ?></span>
		</p></div>
    </form>
</div>
<div id="message" class="tabcontent">
    <form method="post" name="message_form" id="message_form" action="">
      <div class="clp-container">
        <div class="clp-container-left"> 
        <?php wp_nonce_field('save_message','messagenonce'); ?>        
        <label for="loggedout_text_color"><?php _e('Text Colour', 'custom-login-page'); ?></label>
        <input name="loggedout_text_color" id="loggedout_text_color" type="text" value="<?php echo $clp_options['loggedout_text_color']; ?>" class="color {hash:true,caps:false,required:false,pickerPosition:'right'}"/>
        <label for="loggedout_bg_color"><?php _e('Background Colour', 'custom-login-page'); ?></label>
        <input name="loggedout_bg_color" id="loggedout_bg_color" type="text" value="<?php echo $clp_options['loggedout_bg_color']; ?>" class="color {hash:true,caps:false,required:false,pickerPosition:'right'}"/>               
        <label for="loggedout_border_color"><?php _e('Border Colour', 'custom-login-page'); ?></label>
        <input name="loggedout_border_color" id="loggedout_border_color" type="text" value="<?php echo $clp_options['loggedout_border_color']; ?>" class="color {hash:true,caps:false,required:false,pickerPosition:'right'}"/>
        </div>
        <div class="clp-container-right">
        <h2><?php _e('Logged Out Message', 'custom-login-page'); ?></h2>
        <div id="messagemsg"></div>
        <p><?php _e('This changes the colours of the text container, that appears, when you have successfully logged out.', 'custom-login-page'); ?></p>
        <p><i><?php _e('You can leave any of the fields empty to keep the default settings of Wordpress.', 'custom-login-page'); ?></i></p>
        </div>
        <div style="clear: both;"></div>
	  </div>
      <div class="clp-container">
        <div class="clp-container-left">         
        <label for="error_text_color"><?php _e('Text Colour', 'custom-login-page'); ?></label>
        <input name="error_text_color" id="error_text_color" type="text" value="<?php echo $clp_options['error_text_color']; ?>" class="color {hash:true,caps:false,required:false,pickerPosition:'right'}"/>
        <label for="error_bg_color"><?php _e('Background Colour', 'custom-login-page'); ?></label>
        <input name="error_bg_color" id="error_bg_color" type="text" value="<?php echo $clp_options['error_bg_color']; ?>" class="color {hash:true,caps:false,required:false,pickerPosition:'right'}"/>               
        <label for="error_border_color"><?php _e('Border Colour', 'custom-login-page'); ?></label>
        <input name="error_border_color" id="error_border_color" type="text" value="<?php echo $clp_options['error_border_color']; ?>" class="color {hash:true,caps:false,required:false,pickerPosition:'right'}"/>
        </div>
        <div class="clp-container-right">
        <h2><?php _e('Error Message', 'custom-login-page'); ?></h2>
        <p><?php _e('This changes the colours of the text container, that appears, when you get an error logging in.', 'custom-login-page'); ?></p>
        <p><i><?php _e('You can leave any of the fields empty to keep the default settings of Wordpress.', 'custom-login-page'); ?></i></p>
        </div>        
        <div style="clear: both;"></div>
	  </div>
      <div class="clp-container">
        <div class="clp-container-left">         
        <label for="input_text_color"><?php _e('Text Colour', 'custom-login-page'); ?></label>
        <input name="input_text_color" id="input_text_color" type="text" value="<?php echo $clp_options['input_text_color']; ?>" class="color {hash:true,caps:false,required:false,pickerPosition:'right'}"/>
        <label for="input_bg_color"><?php _e('Background Colour', 'custom-login-page'); ?></label>
        <input name="input_bg_color" id="input_bg_color" type="text" value="<?php echo $clp_options['input_bg_color']; ?>" class="color {hash:true,caps:false,required:false,pickerPosition:'right'}"/>               
        <label for="input_border_color"><?php _e('Border Colour', 'custom-login-page'); ?></label>
        <input name="input_border_color" id="input_border_color" type="text" value="<?php echo $clp_options['input_border_color']; ?>" class="color {hash:true,caps:false,required:false,pickerPosition:'right'}"/>
        </div>
        <div class="clp-container-right">
        <h2><?php _e('Input Fields', 'custom-login-page'); ?></h2>
        <p><?php _e('This changes the colours of the name and password fields of the log in form.', 'custom-login-page'); ?></p>
        <p><i><?php _e('You can leave any of the fields empty to keep the default settings of Wordpress.', 'custom-login-page'); ?></i></p>
        </div>        
        <div style="clear: both;"></div>
	  </div> 
      <div id="submit-container" class="clp-container" style="background: none repeat scroll 0% 0% transparent; border: medium none;">	
		<p class="submit">
		<input class="save-tab" name="message_save" id="message_save" value="<?php esc_attr_e('Save Changes', 'custom-login-page'); ?>" type="submit"><img src="<?php admin_url(); ?>/wp-admin/images/wpspin_light.gif" alt="" class="message_save" style="display: none;" />
		<span style="font-weight: bold; color:#243e1f"><?php _e('Save style', 'custom-login-page'); ?></span>
		</p></div>
    </form>
</div>
<div id="link" class="tabcontent">
    <form method="post" name="link_form" id="link_form" action="">
      <div class="clp-container">
        <div class="clp-container-left">
        <?php wp_nonce_field('save_link','linknonce'); ?>       
        <label for="link_text_color"><?php _e('Text Colour', 'custom-login-page'); ?></label>
        <input name="link_text_color" id="link_text_color" type="text" value="<?php echo $clp_options['link_text_color']; ?>" class="color {hash:true,caps:false,required:false,pickerPosition:'right'}"/>
        <label for="link_textdecoration"><?php _e('Text Decoration', 'custom-login-page'); ?></label>
        <select name="link_textdecoration" id="link_textdecoration" style="width: 160px;">
        <option value=""><?php _e('choose a text decoration', 'custom-login-page'); ?></option>
        <option value="none"<?php if ($clp_options['link_textdecoration']=='none') echo ' selected="selected"'; ?>>none</option>
        <option value="underline"<?php if ($clp_options['link_textdecoration']=='underline') echo ' selected="selected"'; ?>>underline</option>
        <option value="overline"<?php if ($clp_options['link_textdecoration']=='overline') echo ' selected="selected"'; ?>>overline</option>
        <option value="line-through"<?php if ($clp_options['link_textdecoration']=='line-through') echo ' selected="selected"'; ?>>line-through</option>
        <option value="blink"<?php if ($clp_options['link_textdecoration']=='blink') echo ' selected="selected"'; ?>>blink</option>
        </select>
        <label for="link_shadow_x"><?php _e('Shadow (x-direction in px)', 'custom-login-page'); ?></label>
        <input name="link_shadow_x" id="link_shadow_x" type="text" value="<?php echo $clp_options['link_shadow_x']; ?>" />
        <label for="link_shadow_y"><?php _e('Shadow (y-direction in px)', 'custom-login-page'); ?></label>
        <input name="link_shadow_y" id="link_shadow_y" type="text" value="<?php echo $clp_options['link_shadow_y']; ?>" />
        <label for="link_shadow_softness"><?php _e('Shadow (softness in px)', 'custom-login-page'); ?></label>
        <input name="link_shadow_softness" id="link_shadow_softness" type="text" value="<?php echo $clp_options['link_shadow_softness']; ?>" />
        <label for="link_shadow_color"><?php _e('Shadow Colour', 'custom-login-page'); ?></label>
        <input name="link_shadow_color" id="link_shadow_color" type="text" value="<?php echo $clp_options['link_shadow_color']; ?>" class="color {hash:true,caps:false,required:false,pickerPosition:'right'}"/>
        <label for="hover_text_color"><?php _e('Hover Colour', 'custom-login-page'); ?></label>
        <input name="hover_text_color" id="hover_text_color" type="text" value="<?php echo $clp_options['hover_text_color']; ?>" class="color {hash:true,caps:false,required:false,pickerPosition:'right'}"/>
        <label for="hover_textdecoration"><?php _e('Hover Text Decoration', 'custom-login-page'); ?></label>
        <select name="hover_textdecoration" id="hover_textdecoration" style="width: 160px;">
        <option value=""><?php _e('choose a text decoration', 'custom-login-page'); ?></option>
        <option value="none"<?php if ($clp_options['hover_textdecoration']=='none') echo ' selected="selected"'; ?>>none</option>
        <option value="underline"<?php if ($clp_options['hover_textdecoration']=='underline') echo ' selected="selected"'; ?>>underline</option>
        <option value="overline"<?php if ($clp_options['hover_textdecoration']=='overline') echo ' selected="selected"'; ?>>overline</option>
        <option value="line-through"<?php if ($clp_options['hover_textdecoration']=='line-through') echo ' selected="selected"'; ?>>line-through</option>
        <option value="blink"<?php if ($clp_options['hover_textdecoration']=='blink') echo ' selected="selected"'; ?>>blink</option>
        </select>
        <label for="hover_shadow_x"><?php _e('Shadow (x-direction in px)', 'custom-login-page'); ?></label>
        <input name="hover_shadow_x" id="hover_shadow_x" type="text" value="<?php echo $clp_options['hover_shadow_x']; ?>" />
        <label for="hover_shadow_y"><?php _e('Shadow (y-direction in px)', 'custom-login-page'); ?></label>
        <input name="hover_shadow_y" id="hover_shadow_y" type="text" value="<?php echo $clp_options['hover_shadow_y']; ?>" />
        <label for="hover_shadow_softness"><?php _e('Shadow (softness in px)', 'custom-login-page'); ?></label>
        <input name="hover_shadow_softness" id="hover_shadow_softness" type="text" value="<?php echo $clp_options['hover_shadow_softness']; ?>" />
        <label for="hover_shadow_color"><?php _e('Shadow Colour', 'custom-login-page'); ?></label>
        <input name="hover_shadow_color" id="hover_shadow_color" type="text" value="<?php echo $clp_options['hover_shadow_color']; ?>" class="color {hash:true,caps:false,required:false,pickerPosition:'right'}"/>
        </div>
        <div class="clp-container-right">
        <h2><?php _e('Links', 'custom-login-page'); ?></h2>
        <div id="linkmsg"></div>
        <p><?php _e('Style the links by giving a text colour, text decoration and shadow for the link and the hover style.', 'custom-login-page'); ?></p>
        <p><i><?php _e('You can leave any of the fields empty to keep the default settings of Wordpress.', 'custom-login-page'); ?></i></p>
        </div>
        <div style="clear: both;"></div>
	  </div> 
      <div id="submit-container" class="clp-container" style="background: none repeat scroll 0% 0% transparent; border: medium none;">	
		<p class="submit">
		<input class="save-tab" name="link_save" id="link_save" value="<?php esc_attr_e('Save Changes', 'custom-login-page'); ?>" type="submit"><img src="<?php admin_url(); ?>/wp-admin/images/wpspin_light.gif" alt="" class="link_save" style="display: none;" />
		<span style="font-weight: bold; color:#243e1f"><?php _e('Save style', 'custom-login-page'); ?></span>
		</p></div>
    </form>
</div>
</td>
</tr>
</table>

</div><!-- / class=wrap -->
<script type="text/javascript">
var pages=new ddtabcontent("clp-pagetabs") //enter ID of Tab Container
pages.setpersist(true) //toogle persistence of the tabs' state
pages.setselectedClassTarget("link") //"link" or "linkparent"
pages.init()
</script>
<?php
	
}

/*
**
** saving the settings
**
*/

function clp_save_settings() {
	
	global $clp_options;
	
	$section=$_POST['section'];
	
	switch ($section) {
		
		case 'main':
		
		if (!wp_verify_nonce($_POST['mainnonce'],'save_main')) :
			
			$output = '<p class="error">'.__('Error in Datatransfer.', 'custom-login-page').'</p>';
		
		else :
		
			$clp_options['logo'] = $_POST['logo'];
			$clp_options['url'] = $_POST['url'];
			$clp_options['title'] = $_POST['title'];
			$clp_options['body_background'] = $_POST['body_background'];
			$clp_options['body_img_repeat'] = $_POST['body_img_repeat'];
			$clp_options['body_img_pos'] = $_POST['body_img_pos'];
			$clp_options['body_bg_color1'] = $_POST['body_bg_color1'];
			$clp_options['body_bg_color2'] = $_POST['body_bg_color2'];
			$clp_options['body_text_color'] = $_POST['body_text_color'];
			
			update_option('clp_options', $clp_options);
			$output='<p class="save">'.__('Settings saved', 'custom-login-page').'</p>';
		
		endif;
		
			echo $output;
			die();
		
		break;

		case 'logindiv':
		
		if (!wp_verify_nonce($_POST['logindivnonce'],'save_logindiv')) :
			
			$output = '<p class="error">'.__('Error in Datatransfer.', 'custom-login-page').'</p>';
		
		else :
		
			$clp_options['logindiv_background'] = $_POST['logindiv_background'];
			$clp_options['logindiv_img_repeat'] = $_POST['logindiv_img_repeat'];
			$clp_options['logindiv_img_pos'] = $_POST['logindiv_img_pos'];
			$clp_options['logindiv_bg_color1'] = $_POST['logindiv_bg_color1'];
			$clp_options['logindiv_bg_color2'] = $_POST['logindiv_bg_color2'];
			$clp_options['logindiv_text_color'] = $_POST['logindiv_text_color'];
			$clp_options['logindiv_border_style'] = $_POST['logindiv_border_style'];
			$clp_options['logindiv_border_width'] = $_POST['logindiv_border_width'];
			$clp_options['logindiv_border_color'] = $_POST['logindiv_border_color'];
			$clp_options['logindiv_border_round'] = $_POST['logindiv_border_round'];
			$clp_options['logindiv_shadow_x'] = $_POST['logindiv_shadow_x'];
			$clp_options['logindiv_shadow_y'] = $_POST['logindiv_shadow_y'];
			$clp_options['logindiv_shadow_softness'] = $_POST['logindiv_shadow_softness'];
			$clp_options['logindiv_shadow_color'] = $_POST['logindiv_shadow_color'];
			$clp_options['logindiv_left'] = $_POST['logindiv_left'];
			$clp_options['logindiv_top'] = $_POST['logindiv_top'];
			$clp_options['logindiv_margin'] = $_POST['logindiv_margin'];
			
			update_option('clp_options', $clp_options);
			$output='<p class="save">'.__('Settings saved', 'custom-login-page').'</p>';
		
		endif;
		
			echo $output;
			die();
		
		break;
		
		case 'loginform':
		
		if (!wp_verify_nonce($_POST['loginformnonce'],'save_loginform')) :
			
			$output = '<p class="error">'.__('Error in Datatransfer.', 'custom-login-page').'</p>';
		
		else :
		
			$clp_options['loginform_background'] = $_POST['loginform_background'];
			$clp_options['loginform_img_repeat'] = $_POST['loginform_img_repeat'];
			$clp_options['loginform_img_pos'] = $_POST['loginform_img_pos'];
			$clp_options['loginform_bg_color1'] = $_POST['loginform_bg_color1'];
			$clp_options['loginform_bg_color2'] = $_POST['loginform_bg_color2'];
			$clp_options['loginform_text_color'] = $_POST['loginform_text_color'];
			$clp_options['loginform_border_style'] = $_POST['loginform_border_style'];
			$clp_options['loginform_border_width'] = $_POST['loginform_border_width'];
			$clp_options['loginform_border_color'] = $_POST['loginform_border_color'];
			$clp_options['loginform_border_round'] = $_POST['loginform_border_round'];
			$clp_options['loginform_shadow_x'] = $_POST['loginform_shadow_x'];
			$clp_options['loginform_shadow_y'] = $_POST['loginform_shadow_y'];
			$clp_options['loginform_shadow_softness'] = $_POST['loginform_shadow_softness'];
			$clp_options['loginform_shadow_color'] = $_POST['loginform_shadow_color'];
			
			update_option('clp_options', $clp_options);
			$output='<p class="save">'.__('Settings saved', 'custom-login-page').'</p>';
		
		endif;
		
			echo $output;
			die();
		
		break;
		
		case 'button':
		
		if (!wp_verify_nonce($_POST['buttonnonce'],'save_button')) :
			
			$output = '<p class="error">'.__('Error in Datatransfer.', 'custom-login-page').'</p>';
		
		else :
		
			$clp_options['button_bg_color1'] = $_POST['button_bg_color1'];
			$clp_options['button_bg_color2'] = $_POST['button_bg_color2'];
			$clp_options['button_text_color'] = $_POST['button_text_color'];
			$clp_options['button_border_color'] = $_POST['button_border_color'];
			$clp_options['btn_hover_bg_color1'] = $_POST['btn_hover_bg_color1'];
			$clp_options['btn_hover_bg_color2'] = $_POST['btn_hover_bg_color2'];
			$clp_options['btn_hover_text_color'] = $_POST['btn_hover_text_color'];
			$clp_options['btn_hover_border_color'] = $_POST['btn_hover_border_color'];
			
			update_option('clp_options', $clp_options);
			$output='<p class="save">'.__('Settings saved', 'custom-login-page').'</p>';
		
		endif;
		
			echo $output;
			die();
		
		break;
		
		case 'message':
		
		if (!wp_verify_nonce($_POST['messagenonce'],'save_message')) :
			
			$output = '<p class="error">'.__('Error in Datatransfer.', 'custom-login-page').'</p>';
		
		else :
		
			$clp_options['loggedout_text_color'] = $_POST['loggedout_text_color'];
			$clp_options['loggedout_bg_color'] = $_POST['loggedout_bg_color'];
			$clp_options['loggedout_border_color'] = $_POST['loggedout_border_color'];
			$clp_options['error_text_color'] = $_POST['error_text_color'];
			$clp_options['error_bg_color'] = $_POST['error_bg_color'];
			$clp_options['error_border_color'] = $_POST['error_border_color'];
			$clp_options['input_text_color'] = $_POST['input_text_color'];
			$clp_options['input_bg_color'] = $_POST['input_bg_color'];
			$clp_options['input_border_color'] = $_POST['input_border_color'];
			
			update_option('clp_options', $clp_options);
			$output='<p class="save">'.__('Settings saved', 'custom-login-page').'</p>';
		
		endif;
		
			echo $output;
			die();
		
		break;
		
		case 'link':
		
		if (!wp_verify_nonce($_POST['linknonce'],'save_link')) :
			
			$output = '<p class="error">'.__('Error in Datatransfer.', 'custom-login-page').'</p>';
		
		else :
		
			$clp_options['link_text_color'] = $_POST['link_text_color'];
			$clp_options['link_textdecoration'] = $_POST['link_textdecoration'];
			$clp_options['link_shadow_x'] = $_POST['link_shadow_x'];
			$clp_options['link_shadow_y'] = $_POST['link_shadow_y'];
			$clp_options['link_shadow_softness'] = $_POST['link_shadow_softness'];
			$clp_options['link_shadow_color'] = $_POST['link_shadow_color'];
			$clp_options['hover_text_color'] = $_POST['hover_text_color'];
			$clp_options['hover_textdecoration'] = $_POST['hover_textdecoration'];
			$clp_options['hover_shadow_x'] = $_POST['hover_shadow_x'];
			$clp_options['hover_shadow_y'] = $_POST['hover_shadow_y'];
			$clp_options['hover_shadow_softness'] = $_POST['hover_shadow_softness'];
			$clp_options['hover_shadow_color'] = $_POST['hover_shadow_color'];
			
			update_option('clp_options', $clp_options);
			$output='<p class="save">'.__('Settings saved', 'custom-login-page').'</p>';
		
		endif;
		
			echo $output;
			die();
		
		break;
		
		default: break;
		
	}
	
}
add_action('wp_ajax_clp_save_settings', 'clp_save_settings');

/**
 *
 * Printing the dss
 *
 */
function clp_get_the_style() {
	
	# collecting variables
	
	global $clp_options;
	
	if ($clp_options['version'] !='1.3') {
		
		$clp_options['version']='1.3';
		update_option('clp_options', $clp_options);
		
	}
	
	$eol = "\r\n";
	
	# body
	
	if (!empty($clp_options['body_text_color'])) $body_style = 'color: '.$clp_options['body_text_color'].' !important;'.$eol;
	if (!empty($clp_options['body_bg_color1'])) $body_style .= 'background-color: '.$clp_options['body_bg_color1'].' !important;'.$eol;
	if (!empty($clp_options['body_bg_color2'])) {
		
		$body_style .= 'background-image: -webkit-gradient(linear, left top, left bottom, from('.$clp_options['body_bg_color1'].'), to('.$clp_options['body_bg_color2'].')) !important;'.$eol;
		$body_style .= 'background-image: -webkit-linear-gradient(top, '.$clp_options['body_bg_color1'].', '.$clp_options['body_bg_color2'].') !important;'.$eol;
		$body_style .= 'background-image: -moz-linear-gradient(top, '.$clp_options['body_bg_color1'].', '.$clp_options['body_bg_color2'].') !important;'.$eol;
		$body_style .= 'background-image: -ms-linear-gradient(top, '.$clp_options['body_bg_color1'].', '.$clp_options['body_bg_color2'].') !important;'.$eol;
		$body_style .= 'background-image: -o-linear-gradient(top, '.$clp_options['body_bg_color1'].', '.$clp_options['body_bg_color2'].') !important;'.$eol;
		$body_style .= 'background-image: -linear-gradient(top, '.$clp_options['body_bg_color1'].', '.$clp_options['body_bg_color2'].') !important;'.$eol;
		
	}
	if (!empty($clp_options['body_background'])) $body_style .= 'background-image: url('.$clp_options['body_background'].') !important;'.$eol;
	if (!empty($clp_options['body_img_repeat'])) $body_style .= 'background-repeat: '.$clp_options['body_img_repeat'].' !important;'.$eol;
	if (!empty($clp_options['body_img_pos'])) $body_style .= 'background-position: '.$clp_options['body_img_pos'].' !important;'.$eol;
	
	# h1 a
	
	if (!empty($clp_options['logo'])) $h1_style = 'background: transparent url('.$clp_options['logo'].') no-repeat center top !important;'.$eol;
	
	# #login
	
	if (!empty($clp_options['logindiv_top']) || !empty($clp_options['logindiv_left']) || $clp_options['logindiv_top']=='0' || $clp_options['logindiv_left']=='0') $logindiv_style = 'position: absolute;'.$eol;
	if (!empty($clp_options['logindiv_top']) || $clp_options['logindiv_top']=='0') $logindiv_style .= 'top: '.$clp_options['logindiv_top'].'px !important;'.$eol;
	if (!empty($clp_options['logindiv_left']) || $clp_options['logindiv_left']=='0') $logindiv_style .= 'left: '.$clp_options['logindiv_left'].'px !important;'.$eol;
	if (!empty($clp_options['logindiv_margin'])) $logindiv_style .= 'margin: '.$clp_options['logindiv_margin'].'px !important;'.$eol;
	if (!empty($clp_options['logindiv_bg_color1'])) $logindiv_style .= 'background-color: '.$clp_options['logindiv_bg_color1'].' !important;'.$eol;
	if (!empty($clp_options['logindiv_bg_color2'])) {
		
		$logindiv_style .= 'background-image: -webkit-gradient(linear, left top, left bottom, from('.$clp_options['logindiv_bg_color1'].'), to('.$clp_options['logindiv_bg_color2'].')) !important;'.$eol;
		$logindiv_style .= 'background-image: -webkit-linear-gradient(top, '.$clp_options['logindiv_bg_color1'].', '.$clp_options['logindiv_bg_color2'].') !important;'.$eol;
		$logindiv_style .= 'background-image: -moz-linear-gradient(top, '.$clp_options['logindiv_bg_color1'].', '.$clp_options['logindiv_bg_color2'].') !important;'.$eol;
		$logindiv_style .= 'background-image: -ms-linear-gradient(top, '.$clp_options['logindiv_bg_color1'].', '.$clp_options['logindiv_bg_color2'].') !important;'.$eol;
		$logindiv_style .= 'background-image: -o-linear-gradient(top, '.$clp_options['logindiv_bg_color1'].', '.$clp_options['logindiv_bg_color2'].') !important;'.$eol;
		$logindiv_style .= 'background-image: -linear-gradient(top, '.$clp_options['logindiv_bg_color1'].', '.$clp_options['logindiv_bg_color2'].') !important;'.$eol;
		
	}
	if (!empty($clp_options['logindiv_background'])) $logindiv_style .= 'background-image: url('.$clp_options['logindiv_background'].') !important;'.$eol;
	if (!empty($clp_options['logindiv_img_repeat'])) $logindiv_style .= 'background-repeat: '.$clp_options['logindiv_img_repeat'].' !important;'.$eol;
	if (!empty($clp_options['logindiv_img_pos'])) $logindiv_style .= 'background-position: '.$clp_options['logindiv_img_pos'].' !important;'.$eol;
	if (!empty($clp_options['logindiv_border_style'])) $logindiv_style .= 'border: '.$clp_options['logindiv_border_style'].' '.$clp_options['logindiv_border_width'].'px '.$clp_options['logindiv_border_color'].' !important;'.$eol;
	if (!empty($clp_options['logindiv_border_round'])) {
		
		$logindiv_style .= '-webkit-border-radius: '.$clp_options['logindiv_border_round'].'px;'.$eol;
		$logindiv_style .= '-moz-border-radius: '.$clp_options['logindiv_border_round'].'px;'.$eol;
		$logindiv_style .= 'border-radius: '.$clp_options['logindiv_border_round'].'px;'.$eol;
		
	}
	if (!empty($clp_options['logindiv_shadow_x']) || $clp_options['logindiv_shadow_x']=='0') {
		
		$logindiv_style .= '-webkit-box-shadow: '.$clp_options['logindiv_shadow_x'].'px '.$clp_options['logindiv_shadow_y'].'px '.$clp_options['logindiv_shadow_softness'].'px '.$clp_options['logindiv_shadow_color'].';'.$eol;
		$logindiv_style .= '-moz-box-shadow: '.$clp_options['logindiv_shadow_x'].'px '.$clp_options['logindiv_shadow_y'].'px '.$clp_options['logindiv_shadow_softness'].'px '.$clp_options['logindiv_shadow_color'].';'.$eol;;
		$logindiv_style .= 'box-shadow: '.$clp_options['logindiv_shadow_x'].'px '.$clp_options['logindiv_shadow_y'].'px '.$clp_options['logindiv_shadow_softness'].'px '.$clp_options['logindiv_shadow_color'].';'.$eol;
		
	}
	
	if (!empty($clp_options['logindiv_text_color'])) {
		
		$logindiv_style .= 'color: '.$clp_options['logindiv_text_color'].' !important;'.$eol;
		$label_style = 'color: '.$clp_options['logindiv_text_color'].' !important;'.$eol;
		
	}
	
	# #loginform, #lostpasswordform, #registerform
	
	if (!empty($clp_options['loginform_bg_color1'])) $loginform_style = 'background-color: '.$clp_options['loginform_bg_color1'].' !important;'.$eol;
	if (!empty($clp_options['loginform_bg_color2'])) {
		
		$loginform_style .= 'background-image: -webkit-gradient(linear, left top, left bottom, from('.$clp_options['loginform_bg_color1'].'), to('.$clp_options['loginform_bg_color2'].')) !important;'.$eol;
		$loginform_style .= 'background-image: -webkit-linear-gradient(top, '.$clp_options['loginform_bg_color1'].', '.$clp_options['loginform_bg_color2'].') !important;'.$eol;
		$loginform_style .= 'background-image: -moz-linear-gradient(top, '.$clp_options['loginform_bg_color1'].', '.$clp_options['loginform_bg_color2'].') !important;'.$eol;
		$loginform_style .= 'background-image: -ms-linear-gradient(top, '.$clp_options['loginform_bg_color1'].', '.$clp_options['loginform_bg_color2'].') !important;'.$eol;
		$loginform_style .= 'background-image: -o-linear-gradient(top, '.$clp_options['loginform_bg_color1'].', '.$clp_options['loginform_bg_color2'].') !important;'.$eol;
		$loginform_style .= 'background-image: -linear-gradient(top, '.$clp_options['loginform_bg_color1'].', '.$clp_options['loginform_bg_color2'].') !important;'.$eol;
		
	}
	if (!empty($clp_options['loginform_background'])) $loginform_style .= 'background-image: url('.$clp_options['loginform_background'].') !important;'.$eol;
	if (!empty($clp_options['loginform_img_repeat'])) $loginform_style .= 'background-repeat: '.$clp_options['loginform_img_repeat'].' !important;'.$eol;
	if (!empty($clp_options['loginform_img_pos'])) $loginform_style .= 'background-position: '.$clp_options['loginform_img_pos'].' !important;'.$eol;
	if (!empty($clp_options['loginform_border_style'])) $loginform_style .= 'border: '.$clp_options['loginform_border_style'].' '.$clp_options['loginform_border_width'].'px '.$clp_options['loginform_border_color'].' !important;'.$eol;
	if (!empty($clp_options['loginform_border_round'])) {
		
		$loginform_style .= '-webkit-border-radius: '.$clp_options['loginform_border_round'].'px;'.$eol;
		$loginform_style .= '-moz-border-radius: '.$clp_options['loginform_border_round'].'px;'.$eol;
		$loginform_style .= 'border-radius: '.$clp_options['loginform_border_round'].'px;'.$eol;
		
	}
	if (!empty($clp_options['loginform_shadow_x']) || $clp_options['loginform_shadow_x']=='0') {
		
		$loginform_style .= '-webkit-box-shadow: '.$clp_options['loginform_shadow_x'].'px '.$clp_options['loginform_shadow_y'].'px '.$clp_options['loginform_shadow_softness'].'px '.$clp_options['loginform_shadow_color'].';'.$eol;
		$loginform_style .= '-moz-box-shadow: '.$clp_options['loginform_shadow_x'].'px '.$clp_options['loginform_shadow_y'].'px '.$clp_options['loginform_shadow_softness'].'px '.$clp_options['loginform_shadow_color'].';'.$eol;;
		$loginform_style .= 'box-shadow: '.$clp_options['loginform_shadow_x'].'px '.$clp_options['loginform_shadow_y'].'px '.$clp_options['loginform_shadow_softness'].'px '.$clp_options['loginform_shadow_color'].';'.$eol;
		
	}
	
	if (!empty($clp_options['loginform_text_color'])) {
		
		$loginform_style .= 'color: '.$clp_options['loginform_text_color'].' !important;'.$eol;
		$label_style = 'color: '.$clp_options['loginform_text_color'].' !important;'.$eol;
		
	}
	
	# .login .message
	
	if (!empty($clp_options['loggedout_text_color'])) $loggedout_style = 'color: '.$clp_options['loggedout_text_color'].';'.$eol;
	if (!empty($clp_options['loggedout_bg_color'])) $loggedout_style .= 'background-color: '.$clp_options['loggedout_bg_color'].';'.$eol;
	if (!empty($clp_options['loggedout_border_color'])) $loggedout_style .= 'border-color: '.$clp_options['loggedout_border_color'].';'.$eol;
	
	# #login_error
	
	if (!empty($clp_options['error_text_color'])) $error_style = 'color: '.$clp_options['error_text_color'].';'.$eol;
	if (!empty($clp_options['error_bg_color'])) $error_style .= 'background-color: '.$clp_options['error_bg_color'].' !important;'.$eol;
	if (!empty($clp_options['error_border_color'])) $error_style .= 'border-color: '.$clp_options['error_border_color'].' !important;'.$eol;
	
	# .input
	
	if (!empty($clp_options['input_text_color'])) $input_style = 'color: '.$clp_options['input_text_color'].' !important;'.$eol;
	if (!empty($clp_options['input_bg_color'])) $input_style .= 'background-color: '.$clp_options['input_bg_color'].' !important;'.$eol;
	if (!empty($clp_options['input_border_color'])) $input_style .= 'border-color: '.$clp_options['input_border_color'].' !important;'.$eol;
	
	# #login_error a, .login #nav a, .login #backtoblog a
	
	if (!empty($clp_options['link_text_color'])) $link_style = 'color: '.$clp_options['link_text_color'].' !important;'.$eol;
	if (!empty($clp_options['link_textdecoration'])) $link_style .= 'text-decoration: '.$clp_options['link_textdecoration'].' !important;'.$eol;
	if (!empty($clp_options['link_shadow_x']) || $clp_options['link_shadow_x']=='0') $link_style .= 'text-shadow: '.$clp_options['link_shadow_x'].'px '.$clp_options['link_shadow_y'].'px '.$clp_options['link_shadow_softness'].'px '.$clp_options['link_shadow_color'].' !important;'.$eol;
	if (!empty($link_style) && empty($clp_options['link_shadow_x'])) $link_style .= 'text-shadow: none !important;'.$eol;
	
	if (!empty($clp_options['hover_text_color'])) $hover_style = 'color: '.$clp_options['hover_text_color'].' !important;'.$eol;
	if (!empty($clp_options['hover_textdecoration'])) $hover_style .= 'text-decoration: '.$clp_options['hover_textdecoration'].' !important;'.$eol;
	if (!empty($clp_options['hover_shadow_x']) || $clp_options['hover_shadow_x']=='0') $hover_style .= 'text-shadow: '.$clp_options['hover_shadow_x'].'px '.$clp_options['hover_shadow_y'].'px '.$clp_options['hover_shadow_softness'].'px '.$clp_options['hover_shadow_color'].' !important;'.$eol;
	
	# .button-primary
	
	if (!empty($clp_options['button_bg_color1'])) {
		
		$button_style = 'background: transparent !important;'.$eol;
		$button_style .= 'background-color: '.$clp_options['button_bg_color1'].' !important;'.$eol;
		
	}
	if (!empty($clp_options['button_bg_color2'])) {
		
		$button_style .= 'background-image: -webkit-gradient(linear, left top, left bottom, from('.$clp_options['button_bg_color1'].'), to('.$clp_options['button_bg_color2'].')) !important;'.$eol;
		$button_style .= 'background-image: -webkit-linear-gradient(top, '.$clp_options['button_bg_color1'].', '.$clp_options['button_bg_color2'].') !important;'.$eol;
		$button_style .= 'background-image: -moz-linear-gradient(top, '.$clp_options['button_bg_color1'].', '.$clp_options['button_bg_color2'].') !important;'.$eol;
		$button_style .= 'background-image: -ms-linear-gradient(top, '.$clp_options['button_bg_color1'].', '.$clp_options['button_bg_color2'].') !important;'.$eol;
		$button_style .= 'background-image: -o-linear-gradient(top, '.$clp_options['button_bg_color1'].', '.$clp_options['button_bg_color2'].') !important;'.$eol;
		$button_style .= 'background-image: -linear-gradient(top, '.$clp_options['button_bg_color1'].', '.$clp_options['button_bg_color2'].') !important;'.$eol;
		
	}
	if (!empty($clp_options['button_text_color'])) $button_style .= 'color: '.$clp_options['button_text_color'].' !important;'.$eol;
	if (!empty($clp_options['button_border_color'])) $button_style .= 'border: solid 1px '.$clp_options['button_border_color'].' !important;'.$eol;
	
	if (!empty($clp_options['btn_hover_bg_color1'])) $btn_hover_style = 'background-color: '.$clp_options['btn_hover_bg_color1'].' !important;'.$eol;
	if (!empty($clp_options['btn_hover_bg_color2'])) {
		
		$btn_hover_style .= 'background-image: -webkit-gradient(linear, left top, left bottom, from('.$clp_options['btn_hover_bg_color1'].'), to('.$clp_options['btn_hover_bg_color2'].')) !important;'.$eol;
		$btn_hover_style .= 'background-image: -webkit-linear-gradient(top, '.$clp_options['btn_hover_bg_color1'].', '.$clp_options['btn_hover_bg_color2'].') !important;'.$eol;
		$btn_hover_style .= 'background-image: -moz-linear-gradient(top, '.$clp_options['btn_hover_bg_color1'].', '.$clp_options['btn_hover_bg_color2'].') !important;'.$eol;
		$btn_hover_style .= 'background-image: -ms-linear-gradient(top, '.$clp_options['btn_hover_bg_color1'].', '.$clp_options['btn_hover_bg_color2'].') !important;'.$eol;
		$btn_hover_style .= 'background-image: -o-linear-gradient(top, '.$clp_options['btn_hover_bg_color1'].', '.$clp_options['btn_hover_bg_color2'].') !important;'.$eol;
		$btn_hover_style .= 'background-image: -linear-gradient(top, '.$clp_options['btn_hover_bg_color1'].', '.$clp_options['btn_hover_bg_color2'].') !important;'.$eol;
		
	}
	if (!empty($clp_options['btn_hover_text_color'])) $btn_hover_style .= 'color: '.$clp_options['btn_hover_text_color'].' !important;'.$eol;
	if (!empty($clp_options['btn_hover_border_color'])) $btn_hover_style .= 'border: solid 1px '.$clp_options['btn_hover_border_color'].' !important;'.$eol;

	#building the stylesheet
	
	$clp_css='@charset "UTF-8";'.$eol.'/* CSS Document */'.$eol.$eol;
	
	if(!empty($body_style)) $clp_css.='body {'.$eol.$body_style.'}'.$eol;
	if(!empty($h1_style)) $clp_css.='h1 a {'.$eol.$h1_style.'}'.$eol;
	if(!empty($logindiv_style)) $clp_css.='#login {'.$eol.$logindiv_style.'}'.$eol;
	if(!empty($loginform_style)) $clp_css.='#loginform, #lostpasswordform, #registerform {'.$eol.$loginform_style.'}'.$eol;
	if(!empty($label_style)) $clp_css.='#loginform label, #lostpasswordform label, #registerform label {'.$eol.$label_style.'}'.$eol;
	if(!empty($loggedout_style)) $clp_css.='.login .message {'.$eol.$loggedout_style.'}'.$eol;
	if(!empty($error_style)) $clp_css.='#login_error {'.$eol.$error_style.'}'.$eol;
	if(!empty($input_style)) $clp_css.='.input {'.$eol.$input_style.'}'.$eol;
	if(!empty($link_style)) $clp_css.='#login_error a, .login #nav a, .login #backtoblog a {'.$eol.$link_style.'}'.$eol;
	if(!empty($hover_style)) $clp_css.='#login_error a:hover, .login #nav a:hover, .login #backtoblog a:hover {'.$eol.$hover_style.'}'.$eol;
	if(!empty($button_style)) $clp_css.='.button-primary {'.$eol.$button_style.'}'.$eol;
	if(!empty($btn_hover_style)) $clp_css.='.button-primary:hover {'.$eol.$btn_hover_style.'}'.$eol;

	return $clp_css;
	
}

/**
 *
 * redirect to dss
 *
 */

add_action('init','clp_add_rewrite');
function clp_add_rewrite() {
       global $wp;
       $wp->add_query_var('clpfile');
}

add_action('template_redirect','clp_css_template');
function clp_css_template() {
       if (get_query_var('clpfile') == 'css') {
               
			   header('Content-type: text/css');
			   echo clp_get_the_style();
			   
               exit;
       }
}

add_action('init','clp_penisverlaengerung');
function clp_penisverlaengerung() {
	
	if (is_page('login')) :
	
		wp_deregister_style('wp-admin');
		
	endif;

}

?>