<?php
/*
Plugin Name: A5 Custom Login Page
Description: Just customize your login page (or that of your community etc.) by giving the WP login page a different look, with your own logo and special colours and styles.
Version: 1.5
Author: Waldemar Stoffel
Author URI: http://www.waldemarstoffel.com
Plugin URI: http://wasistlos.waldemarstoffel.com/plugins-fur-wordpress/a5-custom-login-page
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
 * ------------------------------------------------------
 *  ACKNOWLEDGEMENTS
 * ------------------------------------------------------
 * 
 * Thx to Jorge Ballesteros - http://motivando.me
 * for the translation into Spanish
 *
 * ------------------------------------------------------
 */

/**
 *
 * Stop direct call
 *
 */
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) die('Sorry, you don&#39;t have direct access to this page.');

class A5_CustomLoginPage {
	
	static $options;
	
	const language_file = 'custom-login-page';
	
	function A5_CustomLoginPage(){
		
		self::$options = get_option('clp_options');
		
		register_activation_hook(  __FILE__, array($this, 'start_clp') ); 
		register_deactivation_hook(  __FILE__, array($this, 'unset_clp') );	
		
		add_filter('plugin_row_meta', array($this, 'clp_register_links'),10,2);
		add_filter( 'plugin_action_links', array($this, 'clp_plugin_action_links'), 10, 2 );
		
		add_action('login_enqueue_scripts', array($this, 'clp_login_css'));
		add_action('admin_menu', array($this, 'clp_admin_menu'));
		add_action('admin_init', array($this, 'clp_register_admin_extras'));
		add_action('admin_enqueue_scripts', array($this, 'clp_admin_css'));
		add_action('wp_ajax_clp_save_settings', array($this, 'clp_save_settings'));
		add_action('init', array($this, 'clp_add_rewrite'));
		add_action('template_redirect', array($this, 'clp_css_template'));
		
		if (!empty(self::$options['url'])) add_filter( 'login_headerurl', array($this, 'clp_headerurl') );
		if (!empty(self::$options['title'])) add_filter( 'login_headertitle', array($this, 'clp_headertitle') );
		
		/**
		 *
		 * Importing language file
		 *
		 */
		load_plugin_textdomain(self::language_file, false , basename(dirname(__FILE__)).'/languages');
		
	}	
	
	/**
	 *
	 * Adds links to the plugin page
	 *
	 */
	function clp_register_links($links, $file) {
		
		$base = plugin_basename(__FILE__);
		if ($file == $base) {
			$links[] = '<a href="http://wordpress.org/extend/plugins/custom-login-page/faq/" target="_blank">'.__('FAQ', self::language_file).'</a>';
			$links[] = '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=32XGSBKTQNNHA" target="_blank">'.__('Donate', self::language_file).'</a>';
		}
		
		return $links;
	
	}
	
	function clp_plugin_action_links( $links, $file ) {
		
		$base = plugin_basename(__FILE__);
		
		if ($file == $base) array_unshift($links, '<a href="'.admin_url( 'themes.php?page=clp-settings' ).'">'.__('Settings', self::language_file).'</a>');
	
		return $links;
	
	}
	
	/**
	 *
	 * Changes the link behind the logo
	 *
	 */
	function clp_headerurl() {
		
		return self::$options['url'];
		
	}
	
	/**
	 *
	 * Changes the Title tag of the logo
	 *
	 */
	function clp_headertitle() {
		
		return self::$options['title'];
		
	}
	
	/**
	 *
	 * Changes the Error Message
	 *
	 */
	function clp_custom_error() {
		
		return self::$options['error_custom_message'];
		
	}
	
	/**
	 *
	 * Adds the style sheet to the login page
	 *
	 */
	function clp_login_css() {
		
		$clp_css_file = get_bloginfo('url').'/?clpfile=css';
		
		wp_register_style('clp', $clp_css_file, false, self::$options['version'], 'all');
		
		wp_enqueue_style('clp');
		
	}

	/**
	 *
	 * Setting version on activation
	 *
	 */
	function start_clp() {
		
		add_option('clp_options', array('version' => '1.5'));
	
	}
	
	/**
	 *
	 * Cleaning on deactivation
	 *
	 */
	function unset_clp() {
		
		delete_option('clp_options');
		
	}
	
	/**
	 *
	 * Creating Settings Page
	 *
	 */
	function clp_admin_menu() {
		
		add_theme_page('A5 Custom Login Page', 'A5 Custom Login Page', 'administrator', 'clp-settings', array($this, 'clp_options_page'));	
		
	}	
	
	/**
	 *
	 * register styles and scripts for settings page
	 *
	 */
	function clp_register_admin_extras() {
		 
		 self::$options=get_option('clp_options');
		 
		 wp_register_style('clp-admin', plugins_url('/css/clp-admin-css.css', __FILE__), false, self::$options['version'], 'all');
		 wp_register_script('clp-admin-script', plugins_url('/js/clp-admin.js', __FILE__), array('jquery'), self::$options['version'], true);
		 wp_register_script('clp-colorpicker', plugins_url('/js/jscolor/jscolor.js', __FILE__), false, '1.3.11', true);
		 wp_register_script('clp-admin-tabs', plugins_url('/js/tabcontent.js', __FILE__), false, '2.2', false);
	
	}
	
	/**
	 *
	 * Adding scripts and stylesheet to settings page
	 *
	 */
	function clp_admin_css($hook) {
		
		if ($hook != 'appearance_page_clp-settings') return;
		
		wp_enqueue_style('clp-admin');
		wp_enqueue_script('clp-admin-script');
		wp_enqueue_script('clp-colorpicker');
		wp_enqueue_script('clp-admin-tabs');
		wp_localize_script('clp-admin-script', 'message', $this->clp_localize_admin());	
		
	}
	
	/**
	 *
	 * Adding l10n to the script
	 *
	 */
	function clp_localize_admin() {
		
		return array ('saving' => __('Saving...', self::language_file));
		
	}
	
	/**
	 *
	 * settings page
	 *
	 */
	function clp_options_page() {
	
		?>
		
	<table width="100%" cellpadding="2" cellspacing="0"><tr><td valign="middle" width="380"><a href="<?php _e('http://wasistlos.waldemarstoffel.com/plugins-fur-wordpress/a5-custom-login-page'); ?>"><div id="a5-logo" class="icon32" style="background: url('<?php echo plugins_url('/img/a5-icon-34.png', __FILE__);?>');"></div></a><h2 style="margin:0 30px 0 0; padding: 5px 0 5px 0;">
	A5 Custom Login Page <?php _e('Settings', self::language_file); ?></h2></td><td valign="middle">&nbsp;</td>
	</tr></table>
	
	<div class="wrap" style="margin: 0 10px 0 0">
		
	<table>
	<tr>
	<td valign="top" width="200">
	
	<ul id="clp-pagetabs">
		<li><a href="#" id="main-tab" rel="main" class="selected"><?php _e('Body', self::language_file); ?></a></li>
		<li><a href="#" id="logindiv-tab" rel="logindiv"><?php _e('Login Container', self::language_file); ?></a></li>
		<li><a href="#" id="loginform-tab" rel="loginform"><?php _e('Login Form', self::language_file); ?></a></li>
		<li><a href="#" id="button-tab" rel="button"><?php _e('Button', self::language_file); ?></a></li>
		<li><a href="#" id="message-tab" rel="message"><?php _e('Messages and Input Fields', self::language_file); ?></a></li>
		<li><a href="#" id="link-tab" rel="link"><?php _e('Links', self::language_file); ?></a></li>
	</ul>
	
	</td>
	<td valign="top" width="100%">
	<div id="main" class="tabcontent">
		<form method="post" name="main_form" id="main_form" action="">
		  <div class="clp-container">
			<div class="clp-container-left">
			<?php wp_nonce_field('save_main','mainnonce'); ?>
			<label for="logo"><?php _e('Logo URL', self::language_file); ?></label>
			<input name="logo" id="logo" type="text" size="40" style="width: 95%;" value="<?php echo self::$options['logo']; ?>" />
			<label for="url"><?php _e('URL to link to', self::language_file); ?></label>
			<input name="url" id="url" type="text" size="40" style="width: 95%;" value="<?php echo self::$options['url']; ?>" />
			<label for="title"><?php _e('Title tag of the logo', self::language_file); ?></label>
			<input name="title" id="title" type="text" size="40" style="width: 95%;" value="<?php echo self::$options['title']; ?>" />
			</div>
			<div class="clp-container-right">
			<h2><?php _e('Logo', self::language_file); ?></h2>
			<div id="mainmsg"></div>
			<p><?php _e('You can enter the url of the logo, that you want to have in place of the WP logo on the login screen. Just upload any picture (best is a png or gif with transparent background) via the uploader on the Media section and copy the url of that file here.', self::language_file); ?></p>
			<p><?php _e('In the URL field, you enter the URL to which the logo should link.', self::language_file); ?></p>
			<p><i><?php _e('You can leave any of the fields empty to keep the default settings of Wordpress.', self::language_file); ?></i></p>
			</div>
			<div style="clear: both;"></div>
		  </div>
		  <div class="clp-container">
			<div class="clp-container-left">
			<label for="logo_width"><?php _e('Width of the Logo (in px)', self::language_file); ?></label>
			<input name="logo_width" id="logo_width" type="text" size="40" style="width: 95%;" value="<?php echo self::$options['logo_width']; ?>" />
			<label for="logo_height"><?php _e('Height of the Logo (in px)', self::language_file); ?></label>
			<input name="logo_height" id="logo_height" type="text" size="40" style="width: 95%;" value="<?php echo self::$options['logo_height']; ?>" />
			<label for="h1_width"><?php _e('Width of the Logo Container (in px)', self::language_file); ?></label>
			<input name="h1_width" id="h1_width" type="text" size="40" style="width: 95%;" value="<?php echo self::$options['h1_width']; ?>" />
			<label for="h1_height"><?php _e('Height of the Logo Container (in px)', self::language_file); ?></label>
			<input name="h1_height" id="h1_height" type="text" size="40" style="width: 95%;" value="<?php echo self::$options['h1_height']; ?>" />                        
			</div>
			<div class="clp-container-right">
			<h2><?php _e('Position and Size of the Logo', self::language_file); ?></h2>
			<div id="mainmsg"></div>
			<p><?php _e('If your logo is larger than the default WP-logo (274px by 63px), you can enter the width and the height of it here.', self::language_file); ?></p>
			<p><?php _e('The width and height of the logo-container are by default 326px and 67px. They are used to move the Logo around, since the background-position is always &#39;center top&#39;.', self::language_file); ?></p>
			<p><i><?php _e('You can leave any of the fields empty to keep the default settings of Wordpress.', self::language_file); ?></i></p>
			</div>
			<div style="clear: both;"></div>
		  </div>          
		  <div class="clp-container">
			<div class="clp-container-left">
			<label for="body_background"><?php _e('Background Picture', self::language_file); ?></label>
			<input name="body_background" id="body_background" type="text" size="40" style="width: 95%;" value="<?php echo self::$options['body_background']; ?>" />
			<label for="body_img_repeat"><?php _e('Background Repeat', self::language_file); ?></label>
			<select name="body_img_repeat" id="body_img_repeat" style="width: 135px;">
			<option value=""><?php _e('default', self::language_file); ?></option>
			<option value="no-repeat"<?php selected(self::$options['body_img_repeat'], 'no-repeat'); ?>>no-repeat</option>
			<option value="repeat-x"<?php selected(self::$options['body_img_repeat'], 'repeat-x'); ?>>repeat-x</option>
			<option value="repeat-y"<?php selected(self::$options['body_img_repeat'], 'repeat-y'); ?>>repeat-y</option>
			</select>         
			<label for="body_img_pos"><?php _e('Position of the Background Picture', self::language_file); ?></label>
			<input name="body_img_pos" id="body_img_pos" type="text" size="40" style="width: 95%;" value="<?php echo self::$options['body_img_pos']; ?>" />        
			<label for="body_bg_color1"><?php _e('Background Colour', self::language_file); ?></label>
			<input name="body_bg_color1" id="body_bg_color1" type="text" value="<?php echo self::$options['body_bg_color1']; ?>" class="color {hash:true,caps:false,required:false,pickerPosition:'right'}"/>
			<label for="body_bg_color2"><?php _e('Second Background Colour (for Gradient)', self::language_file); ?></label>
			<input name="body_bg_color2" id="body_bg_color2" type="text" value="<?php echo self::$options['body_bg_color2']; ?>" class="color {hash:true,caps:false,required:false,pickerPosition:'right'}"/>               
			<label for="body_text_color"><?php _e('Text Colour', self::language_file); ?></label>
			<input name="body_text_color" id="body_text_color" type="text" value="<?php echo self::$options['body_text_color']; ?>" class="color {hash:true,caps:false,required:false,pickerPosition:'right'}"/>
			</div>
			<div class="clp-container-right">
			<h2><?php _e('Body', self::language_file); ?></h2>
			<p><?php _e('You can enter the url of the background picture, that you want to have on the login page. Just upload any picture via the uploader on the Media section and copy the url of that file here. Leave it empty, if you don&#39;t want a picture. Background images are tiled by default. You can select the direction of repeating the image or to not repeat it. The position of the image can be something like &#39;100px 50%&#39; or &#39center top&#39;.', self::language_file); ?></p>
			<p><?php _e('In the last section, you choose the background colour and the colour of the text in the html body element. If you give two background colours, you can create a gradient. Colour no. 1 will always be up.', self::language_file); ?></p>
			<p><i><?php _e('You can leave any of the fields empty to keep the default settings of Wordpress.', self::language_file); ?></i></p>
			</div>
			<div style="clear: both;"></div>
		  </div>    
		  <div id="submit-container" class="clp-container" style="background: none repeat scroll 0% 0% transparent; border: medium none;">	
			<p class="submit">
			<input class="save-tab" name="main_save" id="main_save" value="<?php esc_attr_e('Save Changes', self::language_file); ?>" type="submit"><img src="<?php echo admin_url('/images/wpspin_light.gif'); ?>" alt="" class="main-save" style="display: none;" />
			<span style="font-weight: bold; color:#243e1f"><?php _e('Save style', self::language_file); ?></span>
			</p></div>
		</form>
	</div>
	<div id="logindiv" class="tabcontent">
		<form method="post" name="logindiv_form" id="logindiv_form" action="">
		  <div class="clp-container">
			<div class="clp-container-left">
			<?php wp_nonce_field('save_logindiv','logindivnonce'); ?>
			<label for="logindiv_background"><?php _e('Background Picture', self::language_file); ?></label>
			<input name="logindiv_background" id="logindiv_background" type="text" size="40" style="width: 95%;" value="<?php echo self::$options['logindiv_background']; ?>" />
			<label for="logindiv_img_repeat"><?php _e('Background Repeat', self::language_file); ?></label>
			<select name="logindiv_img_repeat" id="logindiv_img_repeat" style="width: 150px;">
			<option value=""><?php _e('default', self::language_file); ?></option>
			<option value="no-repeat"<?php if (self::$options['logindiv_img_repeat']=='no-repeat') echo ' selected="selected"'; ?>>no-repeat</option>
			<option value="repeat-x"<?php if (self::$options['logindiv_img_repeat']=='repeat-x') echo ' selected="selected"'; ?>>repeat-x</option>
			<option value="repeat-y"<?php if (self::$options['logindiv_img_repeat']=='repeat-y') echo ' selected="selected"'; ?>>repeat-y</option>
			</select>         
			<label for="logindiv_img_pos"><?php _e('Position of the Background Picture', self::language_file); ?></label>
			<input name="logindiv_img_pos" id="logindiv_img_pos" type="text" size="40" style="width: 95%;" value="<?php echo self::$options['logindiv_img_pos']; ?>" />        
			<label for="logindiv_bg_color1"><?php _e('Background Colour', self::language_file); ?></label>
			<input name="logindiv_bg_color1" id="logindiv_bg_color1" type="text" value="<?php echo self::$options['logindiv_bg_color1']; ?>" class="color {hash:true,caps:false,required:false,pickerPosition:'right'}"/>
			<label for="logindiv_bg_color2"><?php _e('Second Background Colour (for Gradient)', self::language_file); ?></label>
			<input name="logindiv_bg_color2" id="logindiv_bg_color2" type="text" value="<?php echo self::$options['logindiv_bg_color2']; ?>" class="color {hash:true,caps:false,required:false,pickerPosition:'right'}"/>               
			<label for="logindiv_text_color"><?php _e('Text Colour', self::language_file); ?></label>
			<input name="logindiv_text_color" id="logindiv_text_color" type="text" value="<?php echo self::$options['logindiv_text_color']; ?>" class="color {hash:true,caps:false,required:false,pickerPosition:'right'}"/>
            <label for="logindiv_transparency"><?php _e('Transparency (in percent)', self::language_file); ?></label>
			<input name="logindiv_transparency" id="logindiv_transparency" type="number" min="0" max="100" step="1" value="<?php echo self::$options['logindiv_transparency']; ?>" />
			<label for="logindiv_border_style"><?php _e('Border Style', self::language_file); ?></label>
			<select name="logindiv_border_style" id="logindiv_border_style" style="width: 220px;">
			<option value=""><?php _e('choose a border style', self::language_file); ?></option>
			<option value="none"<?php selected(self::$options['logindiv_border_style'], 'none'); ?>>none</option>
			<option value="dotted"<?php selected(self::$options['logindiv_border_style'], 'dotted'); ?>>dotted</option>
			<option value="dashed"<?php selected(self::$options['logindiv_border_style'], 'dashed'); ?>>dashed</option>
			<option value="solid"<?php selected(self::$options['logindiv_border_style'], 'solid'); ?>>solid</option>
			<option value="double"<?php selected(self::$options['logindiv_border_style'], 'double'); ?>>double</option>
			<option value="groove"<?php selected(self::$options['logindiv_border_style'], 'groove'); ?>>groove</option>
			<option value="ridge"<?php selected(self::$options['logindiv_border_style'], 'ridge'); ?>>ridge</option>
			<option value="inset"<?php selected(self::$options['logindiv_border_style'], 'inset'); ?>>inset</option>
			<option value="outset"<?php selected(self::$options['logindiv_border_style'], 'outset'); ?>>outset</option>
			</select>
			<label for="logindiv_border_width"><?php _e('Border Width (in px)', self::language_file); ?></label>
			<input name="logindiv_border_width" id="logindiv_border_width" type="number" value="<?php echo self::$options['logindiv_border_width']; ?>" />
			<label for="logindiv_border_color"><?php _e('Border Colour', self::language_file); ?></label>
			<input name="logindiv_border_color" id="logindiv_border_color" type="text" value="<?php echo self::$options['logindiv_border_color']; ?>" class="color {hash:true,caps:false,required:false,pickerPosition:'right'}"/>
			<label for="logindiv_border_round"><?php _e('Rounded Corners (in px)', self::language_file); ?></label>
			<input name="logindiv_border_round" id="logindiv_border_round" type="number" value="<?php echo self::$options['logindiv_border_round']; ?>" />
			<label for="logindiv_shadow_x"><?php _e('Shadow (x-direction in px)', self::language_file); ?></label>
			<input name="logindiv_shadow_x" id="logindiv_shadow_x" type="number" value="<?php echo self::$options['logindiv_shadow_x']; ?>" />
			<label for="logindiv_shadow_y"><?php _e('Shadow (y-direction in px)', self::language_file); ?></label>
			<input name="logindiv_shadow_y" id="logindiv_shadow_y" type="number" value="<?php echo self::$options['logindiv_shadow_y']; ?>" />
			<label for="logindiv_shadow_softness"><?php _e('Shadow (softness in px)', self::language_file); ?></label>
			<input name="logindiv_shadow_softness" id="logindiv_shadow_softness" type="number" value="<?php echo self::$options['logindiv_shadow_softness']; ?>" />
			<label for="logindiv_shadow_color"><?php _e('Shadow Colour', self::language_file); ?></label>
			<input name="logindiv_shadow_color" id="logindiv_shadow_color" type="text" value="<?php echo self::$options['logindiv_shadow_color']; ?>" class="color {hash:true,caps:false,required:false,pickerPosition:'right'}"/>
			</div>
			<div class="clp-container-right">
			<h2><?php _e('Login Container', self::language_file); ?></h2>
			<div id="logindivmsg"></div>
			<p><?php _e('You can enter the url of the background picture, that you want to have on the login container. Just upload any picture via the uploader on the Media section and copy the url of that file here. Leave it empty, if you don&#39;t want a picture. Background images are tiled by default. You can select the direction of repeating the image or to not repeat it. The position of the image can be something like &#39;100px 50%&#39; or &#39;center top&#39;.', self::language_file); ?></p>
			<p><?php _e('In the next section, you choose the background colour and the colour of the text in the login container. If you give two background colours, you can create a gradient. Colour no. 1 will always be up.', self::language_file); ?></p>
			<p><?php _e('Choose a border, if wanting one. Define style, width and whether or not, you want to have rounded corners (is not supported by all browsers).', self::language_file); ?></p>
			<p><?php _e('At last, give the container a shadow (is not supported by all browsers).', self::language_file); ?></p>
			<p><i><?php _e('You can leave any of the fields empty to keep the default settings of Wordpress.', self::language_file); ?></i></p>
			</div>
			<div style="clear: both;"></div>
		  </div>
		  <div class="clp-container">
			<div class="clp-container-left">
			<label for="logindiv_left"><?php _e('Position (x-direction in px)', self::language_file); ?></label>
			<input name="logindiv_left" id="logindiv_left" type="number" value="<?php echo self::$options['logindiv_left']; ?>" />
			<label for="logindiv_top"><?php _e('Position (y-direction in px)', self::language_file); ?></label>
			<input name="logindiv_top" id="logindiv_top" type="number" value="<?php echo self::$options['logindiv_top']; ?>" />
            <label for="logindiv_width"><?php _e('Width (in px)', self::language_file); ?></label>
			<input name="logindiv_width" id="logindiv_width" type="number" value="<?php echo self::$options['logindiv_width']; ?>" />
			<label for="logindiv_height"><?php _e('Height (in px)', self::language_file); ?></label>
			<input name="logindiv_height" id="logindiv_height" type="number" value="<?php echo self::$options['logindiv_height']; ?>" />
			<label for="logindiv_padding"><?php _e('Padding', self::language_file); ?></label>
			<input name="logindiv_padding" id="logindiv_padding" type="text" value="<?php echo self::$options['logindiv_padding']; ?>" />
			</div>
			<div class="clp-container-right">
			<h2><?php _e('Position and Size of the Login Container', self::language_file); ?></h2>
			<p><?php _e('Here you can give the whole login container a position. If you enter &#39;0&#39; in both of the fields, it will be in the top left corner of the screen.', self::language_file); ?></p>
            <p><?php _e('The Padding is given as css value. I.e. &#39;144px 0 0&#39; (which is the default padding of the login container).', self::language_file); ?></p>
			<p><i><?php _e('You can leave any of the fields empty to keep the default settings of Wordpress.', self::language_file); ?></i></p>
			</div>
			<div style="clear: both;"></div>
		  </div> 
		  <div id="submit-container" class="clp-container" style="background: none repeat scroll 0% 0% transparent; border: medium none;">	
			<p class="submit">
			<input class="save-tab" name="logindiv_save" id="logindiv_save" value="<?php esc_attr_e('Save Changes', self::language_file); ?>" type="submit"><img src="<?php echo admin_url('/images/wpspin_light.gif'); ?>" alt="" class="logindiv_save" style="display: none;" />
			<span style="font-weight: bold; color:#243e1f"><?php _e('Save style', self::language_file); ?></span>
			</p></div>
		</form>
	</div>
	<div id="loginform" class="tabcontent">
		<form method="post" name="loginform_form" id="loginform_form" action="">
		  <div class="clp-container">
			<div class="clp-container-left">
			<?php wp_nonce_field('save_loginform','loginformnonce'); ?>
			<label for="loginform_background"><?php _e('Background Picture', self::language_file); ?></label>
			<input name="loginform_background" id="loginform_background" type="text" size="40" style="width: 95%;" value="<?php echo self::$options['loginform_background']; ?>" />
			<label for="loginform_img_repeat"><?php _e('Background Repeat', self::language_file); ?></label>
			<select name="loginform_img_repeat" id="loginform_img_repeat" style="width: 150px;">
			<option value=""><?php _e('default', self::language_file); ?></option>
			<option value="no-repeat"<?php selected(self::$options['loginform_img_repeat'], 'no-repeat'); ?>>no-repeat</option>
			<option value="repeat-x"<?php selected(self::$options['loginform_img_repeat'], 'repeat-x'); ?>>repeat-x</option>
			<option value="repeat-y"<?php selected(self::$options['loginform_img_repeat'], 'repeat-y'); ?>>repeat-y</option>
			</select>         
			<label for="loginform_img_pos"><?php _e('Position of the Background Picture', self::language_file); ?></label>
			<input name="loginform_img_pos" id="loginform_img_pos" type="text" size="40" style="width: 95%;" value="<?php echo self::$options['loginform_img_pos']; ?>" />        
			<label for="loginform_bg_color1"><?php _e('Background Colour', self::language_file); ?></label>
			<input name="loginform_bg_color1" id="loginform_bg_color1" type="text" value="<?php echo self::$options['loginform_bg_color1']; ?>" class="color {hash:true,caps:false,required:false,pickerPosition:'right'}"/>
			<label for="loginform_bg_color2"><?php _e('Second Background Colour (for Gradient)', self::language_file); ?></label>
			<input name="loginform_bg_color2" id="loginform_bg_color2" type="text" value="<?php echo self::$options['loginform_bg_color2']; ?>" class="color {hash:true,caps:false,required:false,pickerPosition:'right'}"/>               
			<label for="loginform_text_color"><?php _e('Text Colour', self::language_file); ?></label>
			<input name="loginform_text_color" id="loginform_text_color" type="text" value="<?php echo self::$options['loginform_text_color']; ?>" class="color {hash:true,caps:false,required:false,pickerPosition:'right'}"/>
            <label for="loginform_transparency"><?php _e('Transparency (in percent)', self::language_file); ?></label>
			<input name="loginform_transparency" id="loginform_transparency" type="number" min="0" max="100" step="1" value="<?php echo self::$options['loginform_transparency']; ?>" />
			<label for="loginform_border_style"><?php _e('Border Style', self::language_file); ?></label>
			<select name="loginform_border_style" id="loginform_border_style" style="width: 220px;">
			<option value=""><?php _e('choose a border style', self::language_file); ?></option>
			<option value="none"<?php selected(self::$options['loginform_border_style'],'none'); ?>>none</option>
			<option value="dotted"<?php selected(self::$options['loginform_border_style'],'dotted'); ?>>dotted</option>
			<option value="dashed"<?php selected(self::$options['loginform_border_style'],'dashed'); ?>>dashed</option>
			<option value="solid"<?php selected(self::$options['loginform_border_style'],'solid'); ?>>solid</option>
			<option value="double"<?php selected(self::$options['loginform_border_style'],'double'); ?>>double</option>
			<option value="groove"<?php selected(self::$options['loginform_border_style'],'groove'); ?>>groove</option>
			<option value="ridge"<?php selected(self::$options['loginform_border_style'],'ridge'); ?>>ridge</option>
			<option value="inset"<?php selected(self::$options['loginform_border_style'],'inset'); ?>>inset</option>
			<option value="outset"<?php selected(self::$options['loginform_border_style'],'outset'); ?>>outset</option>
			</select>
			<label for="loginform_border_width"><?php _e('Border Width (in px)', self::language_file); ?></label>
			<input name="loginform_border_width" id="loginform_border_width" type="number" value="<?php echo self::$options['loginform_border_width']; ?>" />
			<label for="loginform_border_color"><?php _e('Border Colour', self::language_file); ?></label>
			<input name="loginform_border_color" id="loginform_border_color" type="number" value="<?php echo self::$options['loginform_border_color']; ?>" class="color {hash:true,caps:false,required:false,pickerPosition:'right'}"/>
			<label for="loginform_border_round"><?php _e('Rounded Corners (in px)', self::language_file); ?></label>
			<input name="loginform_border_round" id="loginform_border_round" type="number" value="<?php echo self::$options['loginform_border_round']; ?>" />
            <label for="loginform_margin"><?php _e('Margin', self::language_file); ?></label>
			<input name="loginform_margin" id="loginform_margin" type="text" value="<?php echo self::$options['loginform_margin']; ?>" />
            <label for="loginform_padding"><?php _e('Padding', self::language_file); ?></label>
			<input name="loginform_padding" id="loginform_padding" type="text" value="<?php echo self::$options['loginform_padding']; ?>" />
			<label for="loginform_shadow_x"><?php _e('Shadow (x-direction in px)', self::language_file); ?></label>
			<input name="loginform_shadow_x" id="loginform_shadow_x" type="number" value="<?php echo self::$options['loginform_shadow_x']; ?>" />
			<label for="loginform_shadow_y"><?php _e('Shadow (y-direction in px)', self::language_file); ?></label>
			<input name="loginform_shadow_y" id="loginform_shadow_y" type="number" value="<?php echo self::$options['loginform_shadow_y']; ?>" />
			<label for="loginform_shadow_softness"><?php _e('Shadow (softness in px)', self::language_file); ?></label>
			<input name="loginform_shadow_softness" id="loginform_shadow_softness" type="number" value="<?php echo self::$options['loginform_shadow_softness']; ?>" />
			<label for="loginform_shadow_color"><?php _e('Shadow Colour', self::language_file); ?></label>
			<input name="loginform_shadow_color" id="loginform_shadow_color" type="text" value="<?php echo self::$options['loginform_shadow_color']; ?>" class="color {hash:true,caps:false,required:false,pickerPosition:'right'}"/>
			</div>
			<div class="clp-container-right">
			<h2><?php _e('Login Form', self::language_file); ?></h2>
			<div id="loginmsg"></div>
			<p><?php _e('You can enter the url of the background picture, that you want to have in the login form. Just upload any picture via the uploader on the Media section and copy the url of that file here. Leave it empty, if you don&#39;t want a picture. Background images are tiled by default. You can select the direction of repeating the image or to not repeat it. The position of the image can be something like &#39;100px 50%&#39; or &#39center top&#39;.', self::language_file); ?></p>
			<p><?php _e('In the next section, you choose the background colour and the colour of the text in the login form. If you give two background colours, you can create a gradient. Colour no. 1 will always be up.', self::language_file); ?></p>
			<p><?php _e('Choose a border, if wanting one. Define style, width and whether or not, you want to have rounded corners (is not supported by all browsers).', self::language_file); ?></p>
			<p><?php _e('Margin and Padding are given as css values. The form has a left margin of 8px by default and a padding of 26px 24px 46px. By changing the top and the bottom padding, you can stretch the form in its length.', self::language_file); ?></p>
            <p><?php _e('At last, give the form a shadow (is not supported by all browsers).', self::language_file); ?></p>
			<p><i><?php _e('You can leave any of the fields empty to keep the default settings of Wordpress.', self::language_file); ?></i></p>
			</div>
			<div style="clear: both;"></div>
		  </div> 
		  <div id="submit-container" class="clp-container" style="background: none repeat scroll 0% 0% transparent; border: medium none;">	
			<p class="submit">
			<input class="save-tab" name="loginform_save" id="loginform_save" value="<?php esc_attr_e('Save Changes', self::language_file); ?>" type="submit"><img src="<?php echo admin_url('/images/wpspin_light.gif'); ?>" alt="" class="loginform_save" style="display: none;" />
			<span style="font-weight: bold; color:#243e1f"><?php _e('Save style', self::language_file); ?></span>
			</p></div>
		</form>
	</div>
	<div id="button" class="tabcontent">
		<form method="post" name="button_form" id="button_form" action="">
		  <div class="clp-container">
			<div class="clp-container-left">
			<?php wp_nonce_field('save_button','buttonnonce'); ?>
			<label for="button_bg_color1"><?php _e('Background Colour', self::language_file); ?></label>
			<input name="button_bg_color1" id="button_bg_color1" type="text" value="<?php echo self::$options['button_bg_color1']; ?>" class="color {hash:true,caps:false,required:false,pickerPosition:'right'}"/>
			<label for="button_bg_color2"><?php _e('Second Background Colour (for Gradient)', self::language_file); ?></label>
			<input name="button_bg_color2" id="button_bg_color2" type="text" value="<?php echo self::$options['button_bg_color2']; ?>" class="color {hash:true,caps:false,required:false,pickerPosition:'right'}"/>               
			<label for="button_text_color"><?php _e('Text Colour', self::language_file); ?></label>
			<input name="button_text_color" id="button_text_color" type="text" value="<?php echo self::$options['button_text_color']; ?>" class="color {hash:true,caps:false,required:false,pickerPosition:'right'}"/>
			<label for="button_border_color"><?php _e('Border Colour', self::language_file); ?></label>
			<input name="button_border_color" id="button_border_color" type="text" value="<?php echo self::$options['button_border_color']; ?>" class="color {hash:true,caps:false,required:false,pickerPosition:'right'}"/>
			<label for="btn_hover_bg_color1"><?php _e('Hover Background Colour', self::language_file); ?></label>
			<input name="btn_hover_bg_color1" id="btn_hover_bg_color1" type="text" value="<?php echo self::$options['btn_hover_bg_color1']; ?>" class="color {hash:true,caps:false,required:false,pickerPosition:'right'}"/>
			<label for="btn_hover_bg_color2"><?php _e('Second Hover Background Colour (for Gradient)', self::language_file); ?></label>
			<input name="btn_hover_bg_color2" id="btn_hover_bg_color2" type="text" value="<?php echo self::$options['btn_hover_bg_color2']; ?>" class="color {hash:true,caps:false,required:false,pickerPosition:'right'}"/>               
			<label for="btn_hover_text_color"><?php _e('Hover Text Colour', self::language_file); ?></label>
			<input name="btn_hover_text_color" id="btn_hover_text_color" type="text" value="<?php echo self::$options['btn_hover_text_color']; ?>" class="color {hash:true,caps:false,required:false,pickerPosition:'right'}"/>
			<label for="btn_hover_border_color"><?php _e('Hover Border Colour', self::language_file); ?></label>
			<input name="btn_hover_border_color" id="btn_hover_border_color" type="text" value="<?php echo self::$options['btn_hover_border_color']; ?>" class="color {hash:true,caps:false,required:false,pickerPosition:'right'}"/>
			</div>
			<div class="clp-container-right">
			<h2><?php _e('Submit Button', self::language_file); ?></h2>
			<div id="buttonmsg"></div>
			<p><?php _e('Enter the background, text and border colour of the submit button here. The button will look static if you don&#39;t give values for the hover state of it. If you want to have a gradient, enter two background colours. The first one will be up then.', self::language_file); ?></p>
			<p><i><?php _e('You can leave any of the fields empty to keep the default settings of Wordpress.', self::language_file); ?></i></p>
			</div>
			<div style="clear: both;"></div>
		  </div>    
		  <div id="submit-container" class="clp-container" style="background: none repeat scroll 0% 0% transparent; border: medium none;">	
			<p class="submit">
			<input class="save-tab" name="button_save" id="button_save" value="<?php esc_attr_e('Save Changes', self::language_file); ?>" type="submit"><img src="<?php echo admin_url('/images/wpspin_light.gif'); ?>" alt="" class="button_save" style="display: none;" />
			<span style="font-weight: bold; color:#243e1f"><?php _e('Save style', self::language_file); ?></span>
			</p></div>
		</form>
	</div>
	<div id="message" class="tabcontent">
		<form method="post" name="message_form" id="message_form" action="">
		  <div class="clp-container">
			<div class="clp-container-left"> 
			<?php wp_nonce_field('save_message','messagenonce'); ?>        
			<label for="loggedout_text_color"><?php _e('Text Colour', self::language_file); ?></label>
			<input name="loggedout_text_color" id="loggedout_text_color" type="text" value="<?php echo self::$options['loggedout_text_color']; ?>" class="color {hash:true,caps:false,required:false,pickerPosition:'right'}"/>
			<label for="loggedout_bg_color"><?php _e('Background Colour', self::language_file); ?></label>
			<input name="loggedout_bg_color" id="loggedout_bg_color" type="text" value="<?php echo self::$options['loggedout_bg_color']; ?>" class="color {hash:true,caps:false,required:false,pickerPosition:'right'}"/>               
			<label for="loggedout_border_color"><?php _e('Border Colour', self::language_file); ?></label>
			<input name="loggedout_border_color" id="loggedout_border_color" type="text" value="<?php echo self::$options['loggedout_border_color']; ?>" class="color {hash:true,caps:false,required:false,pickerPosition:'right'}"/>
			</div>
			<div class="clp-container-right">
			<h2><?php _e('Logged Out Message', self::language_file); ?></h2>
			<div id="messagemsg"></div>
			<p><?php _e('This changes the colours of the text container, that appears, when you have successfully logged out.', self::language_file); ?></p>
			<p><i><?php _e('You can leave any of the fields empty to keep the default settings of Wordpress.', self::language_file); ?></i></p>
			</div>
			<div style="clear: both;"></div>
		  </div>
		  <div class="clp-container">
			<div class="clp-container-left">         
			<label for="error_text_color"><?php _e('Text Colour', self::language_file); ?></label>
			<input name="error_text_color" id="error_text_color" type="text" value="<?php echo self::$options['error_text_color']; ?>" class="color {hash:true,caps:false,required:false,pickerPosition:'right'}"/>
			<label for="error_bg_color"><?php _e('Background Colour', self::language_file); ?></label>
			<input name="error_bg_color" id="error_bg_color" type="text" value="<?php echo self::$options['error_bg_color']; ?>" class="color {hash:true,caps:false,required:false,pickerPosition:'right'}"/>               
			<label for="error_border_color"><?php _e('Border Colour', self::language_file); ?></label>
			<input name="error_border_color" id="error_border_color" type="text" value="<?php echo self::$options['error_border_color']; ?>" class="color {hash:true,caps:false,required:false,pickerPosition:'right'}"/>
			<label for="error_custom_message"><?php _e('Error Message', self::language_file); ?></label>
			<input name="error_custom_message" id="error_custom_message" type="text" size="40" style="width: 95%;" value="<?php echo self::$options['error_custom_message']; ?>" />
			</div>
			<div class="clp-container-right">
			<h2><?php _e('Error Message', self::language_file); ?></h2>
			<p><?php _e('This changes the colours of the text container, that appears, when you get an error logging in.', self::language_file); ?></p>
			<p><?php _e('Furthermore, you can enter your own error message here. By default, Wordpress says that either the username or the password is wrong, which is perhaps a hint to foreigners that you don&#39;t wish to give.', self::language_file); ?></p>
			<p><i><?php _e('You can leave any of the fields empty to keep the default settings of Wordpress.', self::language_file); ?></i></p>
			</div>        
			<div style="clear: both;"></div>
		  </div>
		  <div class="clp-container">
			<div class="clp-container-left">         
			<label for="input_text_color"><?php _e('Text Colour', self::language_file); ?></label>
			<input name="input_text_color" id="input_text_color" type="text" value="<?php echo self::$options['input_text_color']; ?>" class="color {hash:true,caps:false,required:false,pickerPosition:'right'}"/>
			<label for="input_bg_color"><?php _e('Background Colour', self::language_file); ?></label>
			<input name="input_bg_color" id="input_bg_color" type="text" value="<?php echo self::$options['input_bg_color']; ?>" class="color {hash:true,caps:false,required:false,pickerPosition:'right'}"/>               
			<label for="input_border_color"><?php _e('Border Colour', self::language_file); ?></label>
			<input name="input_border_color" id="input_border_color" type="text" value="<?php echo self::$options['input_border_color']; ?>" class="color {hash:true,caps:false,required:false,pickerPosition:'right'}"/>
			</div>
			<div class="clp-container-right">
			<h2><?php _e('Input Fields', self::language_file); ?></h2>
			<p><?php _e('This changes the colours of the name and password fields of the log in form.', self::language_file); ?></p>
			<p><i><?php _e('You can leave any of the fields empty to keep the default settings of Wordpress.', self::language_file); ?></i></p>
			</div>        
			<div style="clear: both;"></div>
		  </div> 
		  <div id="submit-container" class="clp-container" style="background: none repeat scroll 0% 0% transparent; border: medium none;">	
			<p class="submit">
			<input class="save-tab" name="message_save" id="message_save" value="<?php esc_attr_e('Save Changes', self::language_file); ?>" type="submit"><img src="<?php echo admin_url('/images/wpspin_light.gif'); ?>" alt="" class="message_save" style="display: none;" />
			<span style="font-weight: bold; color:#243e1f"><?php _e('Save style', self::language_file); ?></span>
			</p></div>
		</form>
	</div>
	<div id="link" class="tabcontent">
		<form method="post" name="link_form" id="link_form" action="">
		  <div class="clp-container">
			<div class="clp-container-left">
			<?php wp_nonce_field('save_link','linknonce'); ?>       
			<label for="link_text_color"><?php _e('Text Colour', self::language_file); ?></label>
			<input name="link_text_color" id="link_text_color" type="text" value="<?php echo self::$options['link_text_color']; ?>" class="color {hash:true,caps:false,required:false,pickerPosition:'right'}"/>
			<label for="link_textdecoration"><?php _e('Text Decoration', self::language_file); ?></label>
			<select name="link_textdecoration" id="link_textdecoration" style="width: 160px;">
			<option value=""><?php _e('choose a text decoration', self::language_file); ?></option>
			<option value="none"<?php selected(self::$options['link_textdecoration'], 'none'); ?>>none</option>
			<option value="underline"<?php selected(self::$options['link_textdecoration'], 'underline'); ?>>underline</option>
			<option value="overline"<?php selected(self::$options['link_textdecoration'], 'overline'); ?>>overline</option>
			<option value="line-through"<?php selected(self::$options['link_textdecoration'], 'line-through'); ?>>line-through</option>
			<option value="blink"<?php selected(self::$options['link_textdecoration'], 'blink'); ?>>blink</option>
			</select>
			<label for="link_shadow_x"><?php _e('Shadow (x-direction in px)', self::language_file); ?></label>
			<input name="link_shadow_x" id="link_shadow_x" type="number" value="<?php echo self::$options['link_shadow_x']; ?>" />
			<label for="link_shadow_y"><?php _e('Shadow (y-direction in px)', self::language_file); ?></label>
			<input name="link_shadow_y" id="link_shadow_y" type="number" value="<?php echo self::$options['link_shadow_y']; ?>" />
			<label for="link_shadow_softness"><?php _e('Shadow (softness in px)', self::language_file); ?></label>
			<input name="link_shadow_softness" id="link_shadow_softness" type="number" value="<?php echo self::$options['link_shadow_softness']; ?>" />
			<label for="link_shadow_color"><?php _e('Shadow Colour', self::language_file); ?></label>
			<input name="link_shadow_color" id="link_shadow_color" type="text" value="<?php echo self::$options['link_shadow_color']; ?>" class="color {hash:true,caps:false,required:false,pickerPosition:'right'}"/>
			<label for="hover_text_color"><?php _e('Hover Colour', self::language_file); ?></label>
			<input name="hover_text_color" id="hover_text_color" type="text" value="<?php echo self::$options['hover_text_color']; ?>" class="color {hash:true,caps:false,required:false,pickerPosition:'right'}"/>
			<label for="hover_textdecoration"><?php _e('Hover Text Decoration', self::language_file); ?></label>
			<select name="hover_textdecoration" id="hover_textdecoration" style="width: 160px;">
			<option value=""><?php _e('choose a text decoration', self::language_file); ?></option>
			<option value="none"<?php selected(self::$options['hover_textdecoration'], 'none'); ?>>none</option>
			<option value="underline"<?php selected(self::$options['hover_textdecoration'], 'underline'); ?>>underline</option>
			<option value="overline"<?php selected(self::$options['hover_textdecoration'], 'overline'); ?>>overline</option>
			<option value="line-through"<?php selected(self::$options['hover_textdecoration'], 'line-through'); ?>>line-through</option>
			<option value="blink"<?php selected(self::$options['hover_textdecoration'], 'blink'); ?>>blink</option>
			</select>
			<label for="hover_shadow_x"><?php _e('Shadow (x-direction in px)', self::language_file); ?></label>
			<input name="hover_shadow_x" id="hover_shadow_x" type="number" value="<?php echo self::$options['hover_shadow_x']; ?>" />
			<label for="hover_shadow_y"><?php _e('Shadow (y-direction in px)', self::language_file); ?></label>
			<input name="hover_shadow_y" id="hover_shadow_y" type="number" value="<?php echo self::$options['hover_shadow_y']; ?>" />
			<label for="hover_shadow_softness"><?php _e('Shadow (softness in px)', self::language_file); ?></label>
			<input name="hover_shadow_softness" id="hover_shadow_softness" type="number" value="<?php echo self::$options['hover_shadow_softness']; ?>" />
			<label for="hover_shadow_color"><?php _e('Shadow Colour', self::language_file); ?></label>
			<input name="hover_shadow_color" id="hover_shadow_color" type="text" value="<?php echo self::$options['hover_shadow_color']; ?>" class="color {hash:true,caps:false,required:false,pickerPosition:'right'}"/>
			</div>
			<div class="clp-container-right">
			<h2><?php _e('Links', self::language_file); ?></h2>
			<div id="linkmsg"></div>
			<p><?php _e('Style the links by giving a text colour, text decoration and shadow for the link and the hover style.', self::language_file); ?></p>
			<p><i><?php _e('You can leave any of the fields empty to keep the default settings of Wordpress.', self::language_file); ?></i></p>
			</div>
			<div style="clear: both;"></div>
		  </div> 
		  <div id="submit-container" class="clp-container" style="background: none repeat scroll 0% 0% transparent; border: medium none;">	
			<p class="submit">
			<input class="save-tab" name="link_save" id="link_save" value="<?php esc_attr_e('Save Changes', self::language_file); ?>" type="submit"><img src="<?php echo admin_url('/images/wpspin_light.gif'); ?>" alt="" class="link_save" style="display: none;" />
			<span style="font-weight: bold; color:#243e1f"><?php _e('Save style', self::language_file); ?></span>
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
	
	/**
	 *
	 * saving the settings
	 *
	 */
	function clp_save_settings() {
		
		$section=$_POST['section'];
		
		switch ($section) :
			
			case 'main':
			
			if (!wp_verify_nonce($_POST['mainnonce'],'save_main')) :
				
				$output = '<p class="error">'.__('Error in Datatransfer.', self::language_file).'</p>';
			
			else :
			
				self::$options['logo'] = $_POST['logo'];
				self::$options['url'] = $_POST['url'];
				self::$options['title'] = $_POST['title'];
				self::$options['logo_width'] = $_POST['logo_width'];
				self::$options['logo_height'] = $_POST['logo_height'];
				self::$options['h1_width'] = $_POST['h1_width'];
				self::$options['h1_height'] = $_POST['h1_height'];
				self::$options['body_background'] = $_POST['body_background'];
				self::$options['body_img_repeat'] = $_POST['body_img_repeat'];
				self::$options['body_img_pos'] = $_POST['body_img_pos'];
				self::$options['body_bg_color1'] = $_POST['body_bg_color1'];
				self::$options['body_bg_color2'] = $_POST['body_bg_color2'];
				self::$options['body_text_color'] = $_POST['body_text_color'];
								
				update_option('clp_options', self::$options);
				$output='<p class="save">'.__('Settings saved', self::language_file).'</p>';
			
			endif;
			
				echo $output;
				die();
			
			break;
	
			case 'logindiv':
			
			if (!wp_verify_nonce($_POST['logindivnonce'],'save_logindiv')) :
				
				$output = '<p class="error">'.__('Error in Datatransfer.', self::language_file).'</p>';
			
			else :
			
				self::$options['logindiv_background'] = $_POST['logindiv_background'];
				self::$options['logindiv_img_repeat'] = $_POST['logindiv_img_repeat'];
				self::$options['logindiv_img_pos'] = $_POST['logindiv_img_pos'];
				self::$options['logindiv_bg_color1'] = $_POST['logindiv_bg_color1'];
				self::$options['logindiv_bg_color2'] = $_POST['logindiv_bg_color2'];
				self::$options['logindiv_text_color'] = $_POST['logindiv_text_color'];
				self::$options['logindiv_transparency'] = $_POST['logindiv_transparency'];
				self::$options['logindiv_border_style'] = $_POST['logindiv_border_style'];
				self::$options['logindiv_border_width'] = $_POST['logindiv_border_width'];
				self::$options['logindiv_border_color'] = $_POST['logindiv_border_color'];
				self::$options['logindiv_border_round'] = $_POST['logindiv_border_round'];
				self::$options['logindiv_shadow_x'] = $_POST['logindiv_shadow_x'];
				self::$options['logindiv_shadow_y'] = $_POST['logindiv_shadow_y'];
				self::$options['logindiv_shadow_softness'] = $_POST['logindiv_shadow_softness'];
				self::$options['logindiv_shadow_color'] = $_POST['logindiv_shadow_color'];
				self::$options['logindiv_left'] = $_POST['logindiv_left'];
				self::$options['logindiv_top'] = $_POST['logindiv_top'];
				self::$options['logindiv_width'] = $_POST['logindiv_width'];
				self::$options['logindiv_height'] = $_POST['logindiv_height'];
				self::$options['logindiv_padding'] = $_POST['logindiv_padding'];
				
				update_option('clp_options', self::$options);
				$output='<p class="save">'.__('Settings saved', self::language_file).'</p>';
			
			endif;
				
				echo $output;
				die();
			
			break;
			
			case 'loginform':
			
			if (!wp_verify_nonce($_POST['loginformnonce'],'save_loginform')) :
				
				$output = '<p class="error">'.__('Error in Datatransfer.', self::language_file).'</p>';
			
			else :
			
				self::$options['loginform_background'] = $_POST['loginform_background'];
				self::$options['loginform_img_repeat'] = $_POST['loginform_img_repeat'];
				self::$options['loginform_img_pos'] = $_POST['loginform_img_pos'];
				self::$options['loginform_bg_color1'] = $_POST['loginform_bg_color1'];
				self::$options['loginform_bg_color2'] = $_POST['loginform_bg_color2'];
				self::$options['loginform_text_color'] = $_POST['loginform_text_color'];
				self::$options['loginform_transparency'] = $_POST['loginform_transparency'];				
				self::$options['loginform_border_style'] = $_POST['loginform_border_style'];
				self::$options['loginform_border_width'] = $_POST['loginform_border_width'];
				self::$options['loginform_border_color'] = $_POST['loginform_border_color'];
				self::$options['loginform_border_round'] = $_POST['loginform_border_round'];
				self::$options['loginform_margin'] = $_POST['loginform_margin'];
				self::$options['loginform_padding'] = $_POST['loginform_padding'];				
				self::$options['loginform_shadow_x'] = $_POST['loginform_shadow_x'];
				self::$options['loginform_shadow_y'] = $_POST['loginform_shadow_y'];
				self::$options['loginform_shadow_softness'] = $_POST['loginform_shadow_softness'];
				self::$options['loginform_shadow_color'] = $_POST['loginform_shadow_color'];
				
				update_option('clp_options', self::$options);
				$output='<p class="save">'.__('Settings saved', self::language_file).'</p>';
			
			endif;
			
				echo $output;
				die();
			
			break;
			
			case 'button':
			
			if (!wp_verify_nonce($_POST['buttonnonce'],'save_button')) :
				
				$output = '<p class="error">'.__('Error in Datatransfer.', self::language_file).'</p>';
			
			else :
			
				self::$options['button_bg_color1'] = $_POST['button_bg_color1'];
				self::$options['button_bg_color2'] = $_POST['button_bg_color2'];
				self::$options['button_text_color'] = $_POST['button_text_color'];
				self::$options['button_border_color'] = $_POST['button_border_color'];
				self::$options['btn_hover_bg_color1'] = $_POST['btn_hover_bg_color1'];
				self::$options['btn_hover_bg_color2'] = $_POST['btn_hover_bg_color2'];
				self::$options['btn_hover_text_color'] = $_POST['btn_hover_text_color'];
				self::$options['btn_hover_border_color'] = $_POST['btn_hover_border_color'];
				
				update_option('clp_options', self::$options);
				$output='<p class="save">'.__('Settings saved', self::language_file).'</p>';
			
			endif;
			
				echo $output;
				die();
			
			break;
			
			case 'message':
			
			if (!wp_verify_nonce($_POST['messagenonce'],'save_message')) :
				
				$output = '<p class="error">'.__('Error in Datatransfer.', self::language_file).'</p>';
			
			else :
			
				self::$options['loggedout_text_color'] = $_POST['loggedout_text_color'];
				self::$options['loggedout_bg_color'] = $_POST['loggedout_bg_color'];
				self::$options['loggedout_border_color'] = $_POST['loggedout_border_color'];
				self::$options['error_text_color'] = $_POST['error_text_color'];
				self::$options['error_bg_color'] = $_POST['error_bg_color'];
				self::$options['error_border_color'] = $_POST['error_border_color'];
				self::$options['error_custom_message'] = $_POST['error_custom_message'];
				self::$options['input_text_color'] = $_POST['input_text_color'];
				self::$options['input_bg_color'] = $_POST['input_bg_color'];
				self::$options['input_border_color'] = $_POST['input_border_color'];
				
				update_option('clp_options', self::$options);
				$output='<p class="save">'.__('Settings saved', self::language_file).'</p>';
			
			endif;
			
				echo $output;
				die();
			
			break;
			
			case 'link':
			
			if (!wp_verify_nonce($_POST['linknonce'],'save_link')) :
				
				$output = '<p class="error">'.__('Error in Datatransfer.', self::language_file).'</p>';
			
			else :
			
				self::$options['link_text_color'] = $_POST['link_text_color'];
				self::$options['link_textdecoration'] = $_POST['link_textdecoration'];
				self::$options['link_shadow_x'] = $_POST['link_shadow_x'];
				self::$options['link_shadow_y'] = $_POST['link_shadow_y'];
				self::$options['link_shadow_softness'] = $_POST['link_shadow_softness'];
				self::$options['link_shadow_color'] = $_POST['link_shadow_color'];
				self::$options['hover_text_color'] = $_POST['hover_text_color'];
				self::$options['hover_textdecoration'] = $_POST['hover_textdecoration'];
				self::$options['hover_shadow_x'] = $_POST['hover_shadow_x'];
				self::$options['hover_shadow_y'] = $_POST['hover_shadow_y'];
				self::$options['hover_shadow_softness'] = $_POST['hover_shadow_softness'];
				self::$options['hover_shadow_color'] = $_POST['hover_shadow_color'];
				
				update_option('clp_options', self::$options);
				$output='<p class="save">'.__('Settings saved', self::language_file).'</p>';
			
			endif;
			
				echo $output;
				die();
			
			break;
			
			default: break;
			
		endswitch;
		
	}
	
	/**
	 *
	 * Printing the dss
	 *
	 */
	function clp_get_the_style() {
		
		# collecting variables
		
		if (self::$options['version'] !='1.5') :
			
			self::$options['version']='1.5';
			update_option('clp_options', self::$options);
			
		endif;
		
		$eol = "\r\n";
		
		# body.login
		
		if (!empty(self::$options['body_text_color'])) $body_style = 'color: '.self::$options['body_text_color'].';'.$eol;
		if (!empty(self::$options['body_bg_color1'])) $body_style .= 'background-color: '.self::$options['body_bg_color1'].';'.$eol;
		if (!empty(self::$options['body_bg_color2'])) :
			
			$body_style .= 'background-image: -webkit-gradient(linear, left top, left bottom, from('.self::$options['body_bg_color1'].'), to('.self::$options['body_bg_color2'].'));'.$eol;
			$body_style .= 'background-image: -webkit-linear-gradient(top, '.self::$options['body_bg_color1'].', '.self::$options['body_bg_color2'].');'.$eol;
			$body_style .= 'background-image: -moz-linear-gradient(top, '.self::$options['body_bg_color1'].', '.self::$options['body_bg_color2'].');'.$eol;
			$body_style .= 'background-image: -ms-linear-gradient(top, '.self::$options['body_bg_color1'].', '.self::$options['body_bg_color2'].');'.$eol;
			$body_style .= 'background-image: -o-linear-gradient(top, '.self::$options['body_bg_color1'].', '.self::$options['body_bg_color2'].');'.$eol;
			$body_style .= 'background-image: -linear-gradient(top, '.self::$options['body_bg_color1'].', '.self::$options['body_bg_color2'].');'.$eol;
			
		endif;
		if (!empty(self::$options['body_background'])) $body_style .= 'background-image: url('.self::$options['body_background'].');'.$eol;
		if (!empty(self::$options['body_img_repeat'])) $body_style .= 'background-repeat: '.self::$options['body_img_repeat'].';'.$eol;
		if (!empty(self::$options['body_img_pos'])) $body_style .= 'background-position: '.self::$options['body_img_pos'].';'.$eol;
		
		# .login h1 a
		
		if (!empty(self::$options['logo'])) :
		
			$bg_width = (!empty(self::$options['logo_width'])) ? self::$options['logo_width'] : '274';
			$bg_height = (!empty(self::$options['logo_height'])) ? self::$options['logo_height'] : '63';
			$h1_width = (!empty(self::$options['h1_width'])) ? self::$options['h1_width'] : '326';
			$h1_height = (!empty(self::$options['h1_height'])) ? self::$options['h1_height'] : '67';
		
			$h1_style = 'background-image: url('.self::$options['logo'].');'.$eol;
			$h1_style .= 'background-position: center top;'.$eol;
			$h1_style .= 'background-repeat: no-repeat;'.$eol;
			$h1_style .= 'background-size: '.$bg_width.'px '.$bg_height.'px;'.$eol;
			$h1_style .= 'width: '.$h1_width.'px;'.$eol;
			$h1_style .= 'height: '.$h1_height.'px;'.$eol;
			
		endif;	
		
		# #login
		
		if (!empty(self::$options['logindiv_top']) || !empty(self::$options['logindiv_left']) || self::$options['logindiv_top']=='0' || self::$options['logindiv_left']=='0') $logindiv_style = 'position: absolute;'.$eol;
		if (!empty(self::$options['logindiv_top']) || self::$options['logindiv_top']=='0') $logindiv_style .= 'top: '.self::$options['logindiv_top'].'px;'.$eol;
		if (!empty(self::$options['logindiv_left']) || self::$options['logindiv_left']=='0') $logindiv_style .= 'left: '.self::$options['logindiv_left'].'px;'.$eol;
		if (!empty(self::$options['logindiv_bg_color1'])) $logindiv_style .= 'background-color: '.self::$options['logindiv_bg_color1'].';'.$eol;
		if (!empty(self::$options['logindiv_bg_color2'])) :
			
			$logindiv_style .= 'background-image: -webkit-gradient(linear, left top, left bottom, from('.self::$options['logindiv_bg_color1'].'), to('.self::$options['logindiv_bg_color2'].'));'.$eol;
			$logindiv_style .= 'background-image: -webkit-linear-gradient(top, '.self::$options['logindiv_bg_color1'].', '.self::$options['logindiv_bg_color2'].');'.$eol;
			$logindiv_style .= 'background-image: -moz-linear-gradient(top, '.self::$options['logindiv_bg_color1'].', '.self::$options['logindiv_bg_color2'].');'.$eol;
			$logindiv_style .= 'background-image: -ms-linear-gradient(top, '.self::$options['logindiv_bg_color1'].', '.self::$options['logindiv_bg_color2'].');'.$eol;
			$logindiv_style .= 'background-image: -o-linear-gradient(top, '.self::$options['logindiv_bg_color1'].', '.self::$options['logindiv_bg_color2'].');'.$eol;
			$logindiv_style .= 'background-image: -linear-gradient(top, '.self::$options['logindiv_bg_color1'].', '.self::$options['logindiv_bg_color2'].');'.$eol;
			
		endif;
		if (!empty(self::$options['logindiv_transparency'])) :
			$logindiv_style .= '-ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity='.self::$options['logindiv_transparency'].')";'.$eol;
			$logindiv_style .= 'filter: alpha(Opacity='.self::$options['logindiv_transparency'].');'.$eol;
			$logindiv_style .= '-moz-opacity: '.(self::$options['logindiv_transparency']/100).';'.$eol;
			$logindiv_style .= '-khtml-opacity: '.(self::$options['logindiv_transparency']/100).';'.$eol;
			$logindiv_style .= 'opacity: '.(self::$options['logindiv_transparency']/100).';'.$eol;
		endif;
		if (!empty(self::$options['logindiv_background'])) $logindiv_style .= 'background-image: url('.self::$options['logindiv_background'].');'.$eol;
		if (!empty(self::$options['logindiv_img_repeat'])) $logindiv_style .= 'background-repeat: '.self::$options['logindiv_img_repeat'].';'.$eol;
		if (!empty(self::$options['logindiv_img_pos'])) $logindiv_style .= 'background-position: '.self::$options['logindiv_img_pos'].';'.$eol;
		if (!empty(self::$options['logindiv_border_style'])) $logindiv_style .= 'border: '.self::$options['logindiv_border_style'].' '.self::$options['logindiv_border_width'].'px '.self::$options['logindiv_border_color'].';'.$eol;
		if (!empty(self::$options['logindiv_border_round'])) :
			
			$logindiv_style .= '-webkit-border-radius: '.self::$options['logindiv_border_round'].'px;'.$eol;
			$logindiv_style .= '-moz-border-radius: '.self::$options['logindiv_border_round'].'px;'.$eol;
			$logindiv_style .= 'border-radius: '.self::$options['logindiv_border_round'].'px;'.$eol;
			
		endif;
		if (!empty(self::$options['logindiv_shadow_x']) || self::$options['logindiv_shadow_x']=='0') :
			
			$logindiv_style .= '-webkit-box-shadow: '.self::$options['logindiv_shadow_x'].'px '.self::$options['logindiv_shadow_y'].'px '.self::$options['logindiv_shadow_softness'].'px '.self::$options['logindiv_shadow_color'].';'.$eol;
			$logindiv_style .= '-moz-box-shadow: '.self::$options['logindiv_shadow_x'].'px '.self::$options['logindiv_shadow_y'].'px '.self::$options['logindiv_shadow_softness'].'px '.self::$options['logindiv_shadow_color'].';'.$eol;;
			$logindiv_style .= 'box-shadow: '.self::$options['logindiv_shadow_x'].'px '.self::$options['logindiv_shadow_y'].'px '.self::$options['logindiv_shadow_softness'].'px '.self::$options['logindiv_shadow_color'].';'.$eol;
			
		endif;
		if (!empty(self::$options['logindiv_width'])) $logindiv_style .= 'width: '.self::$options['logindiv_width'].'px;'.$eol;
		if (!empty(self::$options['logindiv_height'])) $logindiv_style .= 'height: '.self::$options['logindiv_height'].'px;'.$eol;
		if (!empty(self::$options['logindiv_padding'])) $logindiv_style .= 'padding: '.self::$options['logindiv_padding'].';'.$eol;
		
		if (!empty(self::$options['logindiv_text_color'])) :
			
			$logindiv_style .= 'color: '.self::$options['logindiv_text_color'].';'.$eol;
			$label_style = 'color: '.self::$options['logindiv_text_color'].';'.$eol;
			
		endif;
		
		# .login form
		
		if (!empty(self::$options['loginform_bg_color1'])) $loginform_style = 'background-color: '.self::$options['loginform_bg_color1'].';'.$eol;
		if (!empty(self::$options['loginform_bg_color2'])) :
			
			$loginform_style .= 'background-image: -webkit-gradient(linear, left top, left bottom, from('.self::$options['loginform_bg_color1'].'), to('.self::$options['loginform_bg_color2'].'));'.$eol;
			$loginform_style .= 'background-image: -webkit-linear-gradient(top, '.self::$options['loginform_bg_color1'].', '.self::$options['loginform_bg_color2'].');'.$eol;
			$loginform_style .= 'background-image: -moz-linear-gradient(top, '.self::$options['loginform_bg_color1'].', '.self::$options['loginform_bg_color2'].');'.$eol;
			$loginform_style .= 'background-image: -ms-linear-gradient(top, '.self::$options['loginform_bg_color1'].', '.self::$options['loginform_bg_color2'].');'.$eol;
			$loginform_style .= 'background-image: -o-linear-gradient(top, '.self::$options['loginform_bg_color1'].', '.self::$options['loginform_bg_color2'].');'.$eol;
			$loginform_style .= 'background-image: -linear-gradient(top, '.self::$options['loginform_bg_color1'].', '.self::$options['loginform_bg_color2'].');'.$eol;
			
		endif;
		if (!empty(self::$options['loginform_transparency'])) :
			$loginform_style .= '-ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity='.self::$options['loginform_transparency'].')";'.$eol;
			$loginform_style .= 'filter: alpha(Opacity='.self::$options['loginform_transparency'].');'.$eol;
			$loginform_style .= '-moz-opacity: '.(self::$options['loginform_transparency']/100).';'.$eol;
			$loginform_style .= '-khtml-opacity: '.(self::$options['loginform_transparency']/100).';'.$eol;
			$loginform_style .= 'opacity: '.(self::$options['loginform_transparency']/100).';'.$eol;
		endif;
		if (!empty(self::$options['loginform_background'])) $loginform_style .= 'background-image: url('.self::$options['loginform_background'].');'.$eol;
		if (!empty(self::$options['loginform_img_repeat'])) $loginform_style .= 'background-repeat: '.self::$options['loginform_img_repeat'].';'.$eol;
		if (!empty(self::$options['loginform_img_pos'])) $loginform_style .= 'background-position: '.self::$options['loginform_img_pos'].';'.$eol;
		if (!empty(self::$options['loginform_border_style'])) $loginform_style .= 'border: '.self::$options['loginform_border_style'].' '.self::$options['loginform_border_width'].'px '.self::$options['loginform_border_color'].';'.$eol;
		if (!empty(self::$options['loginform_border_round'])) :
			
			$loginform_style .= '-webkit-border-radius: '.self::$options['loginform_border_round'].'px;'.$eol;
			$loginform_style .= '-moz-border-radius: '.self::$options['loginform_border_round'].'px;'.$eol;
			$loginform_style .= 'border-radius: '.self::$options['loginform_border_round'].'px;'.$eol;
			
		endif;
		if (!empty(self::$options['loginform_shadow_x']) || self::$options['loginform_shadow_x']=='0') :
			
			$loginform_style .= '-webkit-box-shadow: '.self::$options['loginform_shadow_x'].'px '.self::$options['loginform_shadow_y'].'px '.self::$options['loginform_shadow_softness'].'px '.self::$options['loginform_shadow_color'].';'.$eol;
			$loginform_style .= '-moz-box-shadow: '.self::$options['loginform_shadow_x'].'px '.self::$options['loginform_shadow_y'].'px '.self::$options['loginform_shadow_softness'].'px '.self::$options['loginform_shadow_color'].';'.$eol;;
			$loginform_style .= 'box-shadow: '.self::$options['loginform_shadow_x'].'px '.self::$options['loginform_shadow_y'].'px '.self::$options['loginform_shadow_softness'].'px '.self::$options['loginform_shadow_color'].';'.$eol;
		endif;
		if (!empty(self::$options['loginform_margin'])) $loginform_style .= 'margin: '.self::$options['loginform_margin'].';'.$eol;
		if (!empty(self::$options['loginform_padding'])) $loginform_style .= 'padding: '.self::$options['loginform_padding'].';'.$eol;		
		
		if (!empty(self::$options['loginform_text_color'])) :
			
			$loginform_style .= 'color: '.self::$options['loginform_text_color'].';'.$eol;
			$label_style = 'color: '.self::$options['loginform_text_color'].';'.$eol;
			
		endif;
		
		# .login .message
		
		if (!empty(self::$options['loggedout_text_color'])) $loggedout_style = 'color: '.self::$options['loggedout_text_color'].';'.$eol;
		if (!empty(self::$options['loggedout_bg_color'])) $loggedout_style .= 'background-color: '.self::$options['loggedout_bg_color'].';'.$eol;
		if (!empty(self::$options['loggedout_border_color'])) $loggedout_style .= 'border-color: '.self::$options['loggedout_border_color'].';'.$eol;
		
		# #login_error
		
		if (!empty(self::$options['error_text_color'])) $error_style = 'color: '.self::$options['error_text_color'].';'.$eol;
		if (!empty(self::$options['error_bg_color'])) $error_style .= 'background-color: '.self::$options['error_bg_color'].' !important;'.$eol;
		if (!empty(self::$options['error_border_color'])) $error_style .= 'border-color: '.self::$options['error_border_color'].' !important;'.$eol;
		
		# .input
		
		if (!empty(self::$options['input_text_color'])) $input_style = 'color: '.self::$options['input_text_color'].' !important;'.$eol;
		if (!empty(self::$options['input_bg_color'])) $input_style .= 'background-color: '.self::$options['input_bg_color'].' !important;'.$eol;
		if (!empty(self::$options['input_border_color'])) $input_style .= 'border-color: '.self::$options['input_border_color'].' !important;'.$eol;
		
		# #login_error a, .login #nav a, .login #backtoblog a
		
		if (!empty(self::$options['link_text_color'])) $link_style = 'color: '.self::$options['link_text_color'].' !important;'.$eol;
		if (!empty(self::$options['link_textdecoration'])) $link_style .= 'text-decoration: '.self::$options['link_textdecoration'].' !important;'.$eol;
		if (!empty(self::$options['link_shadow_x']) || self::$options['link_shadow_x']=='0') $link_style .= 'text-shadow: '.self::$options['link_shadow_x'].'px '.self::$options['link_shadow_y'].'px '.self::$options['link_shadow_softness'].'px '.self::$options['link_shadow_color'].' !important;'.$eol;
		if (!empty($link_style) && empty(self::$options['link_shadow_x'])) $link_style .= 'text-shadow: none !important;'.$eol;
		
		if (!empty(self::$options['hover_text_color'])) $hover_style = 'color: '.self::$options['hover_text_color'].' !important;'.$eol;
		if (!empty(self::$options['hover_textdecoration'])) $hover_style .= 'text-decoration: '.self::$options['hover_textdecoration'].' !important;'.$eol;
		if (!empty(self::$options['hover_shadow_x']) || self::$options['hover_shadow_x']=='0') $hover_style .= 'text-shadow: '.self::$options['hover_shadow_x'].'px '.self::$options['hover_shadow_y'].'px '.self::$options['hover_shadow_softness'].'px '.self::$options['hover_shadow_color'].' !important;'.$eol;
		
		# .button-primary
		
		if (!empty(self::$options['button_bg_color1'])) :
			
			$button_style = 'background: transparent !important;'.$eol;
			$button_style .= 'background-color: '.self::$options['button_bg_color1'].' !important;'.$eol;
			
		endif;
		if (!empty(self::$options['button_bg_color2'])) :
			
			$button_style .= 'background-image: -webkit-gradient(linear, left top, left bottom, from('.self::$options['button_bg_color1'].'), to('.self::$options['button_bg_color2'].')) !important;'.$eol;
			$button_style .= 'background-image: -webkit-linear-gradient(top, '.self::$options['button_bg_color1'].', '.self::$options['button_bg_color2'].') !important;'.$eol;
			$button_style .= 'background-image: -moz-linear-gradient(top, '.self::$options['button_bg_color1'].', '.self::$options['button_bg_color2'].') !important;'.$eol;
			$button_style .= 'background-image: -ms-linear-gradient(top, '.self::$options['button_bg_color1'].', '.self::$options['button_bg_color2'].') !important;'.$eol;
			$button_style .= 'background-image: -o-linear-gradient(top, '.self::$options['button_bg_color1'].', '.self::$options['button_bg_color2'].') !important;'.$eol;
			$button_style .= 'background-image: -linear-gradient(top, '.self::$options['button_bg_color1'].', '.self::$options['button_bg_color2'].') !important;'.$eol;
			
		endif;
		if (!empty(self::$options['button_text_color'])) $button_style .= 'color: '.self::$options['button_text_color'].' !important;'.$eol;
		if (!empty(self::$options['button_border_color'])) $button_style .= 'border: solid 1px '.self::$options['button_border_color'].' !important;'.$eol;
		
		if (!empty(self::$options['btn_hover_bg_color1'])) $btn_hover_style = 'background-color: '.self::$options['btn_hover_bg_color1'].' !important;'.$eol;
		if (!empty(self::$options['btn_hover_bg_color2'])) :
			
			$btn_hover_style .= 'background-image: -webkit-gradient(linear, left top, left bottom, from('.self::$options['btn_hover_bg_color1'].'), to('.self::$options['btn_hover_bg_color2'].')) !important;'.$eol;
			$btn_hover_style .= 'background-image: -webkit-linear-gradient(top, '.self::$options['btn_hover_bg_color1'].', '.self::$options['btn_hover_bg_color2'].') !important;'.$eol;
			$btn_hover_style .= 'background-image: -moz-linear-gradient(top, '.self::$options['btn_hover_bg_color1'].', '.self::$options['btn_hover_bg_color2'].') !important;'.$eol;
			$btn_hover_style .= 'background-image: -ms-linear-gradient(top, '.self::$options['btn_hover_bg_color1'].', '.self::$options['btn_hover_bg_color2'].') !important;'.$eol;
			$btn_hover_style .= 'background-image: -o-linear-gradient(top, '.self::$options['btn_hover_bg_color1'].', '.self::$options['btn_hover_bg_color2'].') !important;'.$eol;
			$btn_hover_style .= 'background-image: -linear-gradient(top, '.self::$options['btn_hover_bg_color1'].', '.self::$options['btn_hover_bg_color2'].') !important;'.$eol;
			
		endif;
		if (!empty(self::$options['btn_hover_text_color'])) $btn_hover_style .= 'color: '.self::$options['btn_hover_text_color'].' !important;'.$eol;
		if (!empty(self::$options['btn_hover_border_color'])) $btn_hover_style .= 'border: solid 1px '.self::$options['btn_hover_border_color'].' !important;'.$eol;
	
		#building the stylesheet
		
		$clp_css='@charset "UTF-8";'.$eol.'/* CSS Document */'.$eol.$eol;
		
		if(!empty($body_style)) $clp_css.='body.login {'.$eol.$body_style.'}'.$eol;
		if(!empty($h1_style)) $clp_css.='.login h1 a {'.$eol.$h1_style.'}'.$eol;
		if(!empty($logindiv_style)) $clp_css.='#login {'.$eol.$logindiv_style.'}'.$eol;
		if(!empty($loginform_style)) $clp_css.='.login form {'.$eol.$loginform_style.'}'.$eol;
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
	function clp_add_rewrite() {
		   global $wp;
		   $wp->add_query_var('clpfile');
	}
	
	function clp_css_template() {
		   if (get_query_var('clpfile') == 'css') :
				   
				   header('Content-type: text/css');
				   echo $this->clp_get_the_style();
				   
				   exit;
		   endif;
	}
	
} // end of class

$a5_custom_login_page = new A5_CustomLoginPage;

?>