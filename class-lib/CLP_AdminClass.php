<?php

/**
 *
 * Class A5 Custom Login Page Admin
 *
 * @ A5 Custom Login Page
 *
 * building admin page
 *
 */
class CLP_Admin extends A5_OptionPage {
	
	const language_file = 'custom-login-page';
	
	static $options;
	
	function __construct($multisite) {
		
		add_action('admin_init', array(&$this, 'initialize_settings'));
		add_action('contextual_help', array(&$this, 'add_help_text'));
		add_action('admin_enqueue_scripts', array(&$this, 'enqueue_scripts'));	
		add_action('admin_menu', array(&$this, 'add_admin_menu'));
		
		self::$options = ($multisite) ? get_site_option('clp_options') : get_option('clp_options');
		
	}
	
	/**
	 *
	 * Add options-page for single site
	 *
	 */
	function add_admin_menu() {
		
		add_menu_page('A5 Custom Login', 'A5 Custom Login', 'administrator', 'clp-settings', array(&$this, 'build_options_page'), plugins_url('custom-login-page/img/a5-icon-16.png'), 62);
		
		add_submenu_page('clp-settings', 'Custom Login Page', 'Custom Login Page', 'administrator', 'clp-settings', array(&$this, 'build_options_page'));
		
	}
	
	/**
	 *
	 * Make all the admin stuff draggable
	 *
	 */
	function enqueue_scripts($hook){
		
		if ('toplevel_page_clp-settings' != $hook) return;
		
		wp_enqueue_script('dashboard');
		
		if (wp_is_mobile()) wp_enqueue_script('jquery-touch-punch');
		
		// getting the build in iris color picker
		
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'iris', admin_url( 'js/iris.min.js' ), array( 'jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch' ), false, true );
		
		wp_register_script('a5-color-picker-script', plugins_url('custom-login-page/color-picker.js'), array('wp-color-picker'), '1.0', true);
		wp_enqueue_script('a5-color-picker-script');
		
	}
	
	/**
	 *
	 * Adding Contextual Help
	 *
	 */
	function add_help_text() {
		
		$screen = get_current_screen();
		
		if ($screen->id != 'toplevel_page_clp-settings') return;
		
		$content = self::tag_it(__('In these settings you will be guided step by step through the process of styling your login page. The basic options are very foolproof but also very limited.', self::language_file), 'p');
		$content .= self::tag_it(__('If you are familiar with coding your own css, you can use only the css tab to write your entire style sheet there. Next to the input for the css, you find a help box with all the elements on the page that you can style.', self::language_file), 'p');
		if (!is_multisite()) $content .= self::tag_it(__('There is also a priview. So, you just can play around a bit and after saving the settings see, how it looks. No need to have two browser windows open.', self::language_file), 'p');
		
		$screen->add_help_tab( array(
			'id'      => 'clp-general-help',
			'title'   => __('General'),
			'content' => $content,
		));
		
		$content = self::tag_it(sprintf(__('With the margin of the logo, you can position the logo more precisely. Give a CSS value here, i.e. %s to locate it 180 px left.', self::language_file), '&#39;0 0 0 -180px&#39;'), 'p');
		$content .= self::tag_it(sprintf(__('With the padding of the logo, you can position the shadow more precisely. Give a CSS value here, i.e. %s to get rid of it completely.', self::language_file), '&#39;0 0 0 -180px&#39;'), 'p');
		
		$screen->add_help_tab( array(
			'id'      => 'clp-logo-help',
			'title'   => __('Logo', self::language_file),
			'content' => $content,
		));
		
		
	}
	
	/**
	 *
	 * Initialize the admin screen of the plugin
	 *
	 */
	 
	function initialize_settings() {
		
		register_setting('clp_options', 'clp_options', array(&$this, 'validate'));
		
		// main tab
		
		add_settings_section('clp_options', false, array(&$this, 'clp_custom_message_section'), 'clp_message');
		
		add_settings_field('clp_logout_custom_message', __('Logout Message', self::language_file), array(&$this, 'logout_custom_message_input'), 'clp_message', 'clp_options');
		
		add_settings_field('clp_error_custom_message', __('Error Message', self::language_file), array(&$this, 'error_custom_message_input'), 'clp_message', 'clp_options');
		
		add_settings_section('clp_options', __('Export Settings', self::language_file), array(&$this, 'clp_export_section'), 'clp_export');
		
		add_settings_field('clp_export', __('Download a file with your settings', self::language_file), array(&$this, 'export_input'), 'clp_export', 'clp_options');
		
		add_settings_section('clp_options', __('Import Settings', self::language_file), array(&$this, 'clp_import_section'), 'clp_import');
		
		add_settings_field('clp_import', __('This will overlay any existing setting, you already have.', self::language_file), array(&$this, 'import_input'), 'clp_import', 'clp_options');
		
		add_settings_field('clp_impex_resize', false, array(&$this, 'impex_resize_field'), 'clp_import', 'clp_options');
		
		// advanced tab
		
		add_settings_section('clp_options', false, array(&$this, 'clp_custom_redirect_section'), 'clp_redirect');
		
		add_settings_field('clp_custom_redirect', __('Redirect per User Role', self::language_file), array(&$this, 'custom_redirect_input'), 'clp_redirect', 'clp_options');
		
		add_settings_section('clp_options', false, array(&$this, 'clp_hide_section'), 'clp_hide');
		
		add_settings_field('clp_hide_nav', __('Hide register and lost password links.', self::language_file), array(&$this, 'hide_nav_input'), 'clp_hide', 'clp_options');
		
		add_settings_field('clp_hide_backlink', __('Hide back to blog link.', self::language_file), array(&$this, 'hide_backlink_input'), 'clp_hide', 'clp_options');
		
		add_settings_section('clp_options', false, array(&$this, 'clp_debug_section'), 'clp_debug');
		
		add_settings_field('clp_compress', __('Compress Style Sheet:', self::language_file), array(&$this, 'compress_field'), 'clp_debug', 'clp_options', array(__('Click here to compress the style sheet.', self::language_file)));
		
		add_settings_field('clp_debug', __('Check, to write styles inline instead of to a virtual CSS file.', self::language_file), array(&$this, 'debug_input'), 'clp_debug', 'clp_options');
		
		if (isset(self::$options['inline']) && !empty(self::$options['inline'])) add_settings_field('clp_priority', __('Give a value for the priority of the style inline (this can help with other plugins overriding our styles).', self::language_file), array(&$this, 'priority_input'), 'clp_debug', 'clp_options');
		
		// body and button tab
		
		add_settings_section('clp_options', false, array(&$this, 'clp_body_section'), 'clp_body');
		
		add_settings_field('clp_body_background', __('Background Picture', self::language_file), array(&$this, 'body_background_input'), 'clp_body', 'clp_options');
		
		add_settings_field('clp_body_img_repeat', __('Background Repeat', self::language_file), array(&$this, 'body_img_repeat_input'), 'clp_body', 'clp_options');
		
		add_settings_field('clp_body_img_pos', __('Position of the Background Picture', self::language_file), array(&$this, 'body_img_pos_input'), 'clp_body', 'clp_options');
		
		add_settings_field('clp_body_bg_color1', __('Background Colour', self::language_file), array(&$this, 'body_bg_color1_input'), 'clp_body', 'clp_options');
		
		add_settings_field('clp_body_bg_color2', __('Second Background Colour (for Gradient)', self::language_file), array(&$this, 'body_bg_color2_input'), 'clp_body', 'clp_options');
		
		add_settings_field('clp_body_bg_size', __('Background Size', self::language_file), array(&$this, 'body_bg_size_input'), 'clp_body', 'clp_options');	
		
		add_settings_section('clp_options', false, array(&$this, 'clp_button_section'), 'clp_button');
		
		add_settings_field('clp_button_bg_color1', __('Background Colour', self::language_file), array(&$this, 'button_bg_color1_input'), 'clp_button', 'clp_options');
		
		add_settings_field('clp_button_bg_color2', __('Second Background Colour (for Gradient)', self::language_file), array(&$this, 'button_bg_color2_input'), 'clp_button', 'clp_options');
		
		add_settings_field('clp_button_text_color', __('Text Colour', self::language_file), array(&$this, 'button_text_color_input'), 'clp_button', 'clp_options');
		
		add_settings_field('clp_button_border_color', __('Border Colour', self::language_file), array(&$this, 'button_border_color_input'), 'clp_button', 'clp_options');
		
		add_settings_field('clp_btn_hover_bg_color1', __('Hover Background Colour', self::language_file), array(&$this, 'btn_hover_bg_color1_input'), 'clp_button', 'clp_options');
		
		add_settings_field('clp_btn_hover_bg_color2', __('Second Hover Background Colour (for Gradient)', self::language_file), array(&$this, 'btn_hover_bg_color2_input'), 'clp_button', 'clp_options');
		
		add_settings_field('clp_btn_hover_text_color', __('Hover Text Colour', self::language_file), array(&$this, 'btn_hover_text_color_input'), 'clp_button', 'clp_options');
		
		add_settings_field('clp_btn_hover_border_color', __('Hover Border Colour', self::language_file), array(&$this, 'btn_hover_border_color_input'), 'clp_button', 'clp_options');	
		
		// logo tab
		
		add_settings_section('clp_options', false, array(&$this, 'clp_logo_section'), 'clp_logo');
		
		add_settings_field('clp_logo_url', __('Logo URL', self::language_file), array(&$this, 'logo_url_input'), 'clp_logo', 'clp_options');
		
		add_settings_field('clp_link_url', __('URL to link to', self::language_file), array(&$this, 'link_url_input'), 'clp_logo', 'clp_options');
		
		add_settings_field('clp_logo_title', __('Title tag of the logo', self::language_file), array(&$this, 'logo_title_input'), 'clp_logo', 'clp_options');
		
		add_settings_section('clp_options', false, array(&$this, 'clp_logo_size_section'), 'clp_logo_size');
		
		add_settings_field('clp_logo_width', __('Width of the Logo (in px)', self::language_file), array(&$this, 'logo_width_input'), 'clp_logo_size', 'clp_options');
		
		add_settings_field('clp_logo_height', __('Height of the Logo (in px)', self::language_file), array(&$this, 'logo_height_input'), 'clp_logo_size', 'clp_options');
		
		add_settings_field('clp_h1_width', __('Width of the Logo Container (in px)', self::language_file), array(&$this, 'h1_width_input'), 'clp_logo_size', 'clp_options');
		
		add_settings_field('clp_h1_height', __('Height of the Logo Container (in px)', self::language_file), array(&$this, 'h1_height_input'), 'clp_logo_size', 'clp_options');
		
		add_settings_field('clp_h1_margin', __('Margin of the Logo Container (CSS)', self::language_file), array(&$this, 'h1_margin_input'), 'clp_logo_size', 'clp_options');
		
		add_settings_field('clp_h1_padding', __('Padding of the Logo Container (CSS)', self::language_file), array(&$this, 'h1_padding_input'), 'clp_logo_size', 'clp_options');
		
		add_settings_section('clp_options', false, array(&$this, 'clp_logo_style_section'), 'clp_logo_style');
		
		add_settings_field('clp_h1_corner', __('Rounded Corners (in px)', self::language_file), array(&$this, 'h1_corner_input'), 'clp_logo_style', 'clp_options');
		
		add_settings_field('clp_h1_shadow_x', __('Shadow (x-direction in px)', self::language_file), array(&$this, 'h1_shadow_x_input'), 'clp_logo_style', 'clp_options');
		
		add_settings_field('clp_h1_shadow_y', __('Shadow (y-direction in px)', self::language_file), array(&$this, 'h1_shadow_y_input'), 'clp_logo_style', 'clp_options');
		
		add_settings_field('clp_h1_shadow_softness', __('Shadow (softness in px)', self::language_file), array(&$this, 'h1_shadow_softness_input'), 'clp_logo_style', 'clp_options');
		
		add_settings_field('clp_h1_shadow_color', __('Shadow Colour', self::language_file), array(&$this, 'h1_shadow_color_input'), 'clp_logo_style', 'clp_options');
		
		// logindiv tab
	
		add_settings_section('clp_options', false, array(&$this, 'clp_logindiv_section'), 'clp_logindiv');
		
		add_settings_field('clp_logindiv_background', __('Background Picture', self::language_file), array(&$this, 'logindiv_background_input'), 'clp_logindiv', 'clp_options');
		
		add_settings_field('clp_logindiv_img_repeat', __('Background Repeat', self::language_file), array(&$this, 'logindiv_img_repeat_input'), 'clp_logindiv', 'clp_options');
		
		add_settings_field('clp_logindiv_img_pos', __('Position of the Background Picture', self::language_file), array(&$this, 'logindiv_img_pos_input'), 'clp_logindiv', 'clp_options');
		
		add_settings_field('clp_logindiv_bg_color1', __('Background Colour', self::language_file), array(&$this, 'logindiv_bg_color1_input'), 'clp_logindiv', 'clp_options');
		
		add_settings_field('clp_logindiv_bg_color2', __('Second Background Colour (for Gradient)', self::language_file), array(&$this, 'logindiv_bg_color2_input'), 'clp_logindiv', 'clp_options');
		
		add_settings_field('clp_logindiv_text_color', __('Text Colour', self::language_file), array(&$this, 'logindiv_text_color_input'), 'clp_logindiv', 'clp_options');
		
		add_settings_field('clp_logindiv_transparency', __('Transparency (in percent)', self::language_file), array(&$this, 'logindiv_transparency_input'), 'clp_logindiv', 'clp_options');
		
		add_settings_field('clp_logindiv_border_style', __('Border Style', self::language_file), array(&$this, 'logindiv_border_style_input'), 'clp_logindiv', 'clp_options');
		
		add_settings_field('clp_logindiv_border_width', __('Border Width (in px)', self::language_file), array(&$this, 'logindiv_border_width_input'), 'clp_logindiv', 'clp_options');
		
		add_settings_field('clp_logindiv_border_color', __('Border Colour', self::language_file), array(&$this, 'logindiv_border_color_input'), 'clp_logindiv', 'clp_options');
		
		add_settings_field('clp_logindiv_border_round', __('Rounded Corners (in px)', self::language_file), array(&$this, 'logindiv_border_round_input'), 'clp_logindiv', 'clp_options');
		
		add_settings_field('clp_logindiv_shadow_x', __('Shadow (x-direction in px)', self::language_file), array(&$this, 'logindiv_shadow_x_input'), 'clp_logindiv', 'clp_options');
		
		add_settings_field('clp_logindiv_shadow_y', __('Shadow (y-direction in px)', self::language_file), array(&$this, 'logindiv_shadow_y_input'), 'clp_logindiv', 'clp_options');
		
		add_settings_field('clp_logindiv_shadow_softness', __('Shadow (softness in px)', self::language_file), array(&$this, 'logindiv_shadow_softness_input'), 'clp_logindiv', 'clp_options');
		
		add_settings_field('clp_logindiv_shadow_color', __('Shadow Colour', self::language_file), array(&$this, 'logindiv_shadow_color_input'), 'clp_logindiv', 'clp_options');
		
		add_settings_section('clp_options', false, array(&$this, 'clp_logindiv_pos_section'), 'clp_logindiv_pos');
		
		add_settings_field('clp_logindiv_left', __('Position (x-direction in px)', self::language_file), array(&$this, 'logindiv_left_input'), 'clp_logindiv_pos', 'clp_options');
		
		add_settings_field('clp_logindiv_top', __('Position (y-direction in px)', self::language_file), array(&$this, 'logindiv_top_input'), 'clp_logindiv_pos', 'clp_options');
		
		add_settings_field('clp_logindiv_width', __('Width (in px)', self::language_file), array(&$this, 'logindiv_width_input'), 'clp_logindiv_pos', 'clp_options');
		
		add_settings_field('clp_logindiv_height', __('Height (in px)', self::language_file), array(&$this, 'logindiv_height_input'), 'clp_logindiv_pos', 'clp_options');
		
		add_settings_field('clp_logindiv_padding', __('Padding', self::language_file), array(&$this, 'logindiv_padding_input'), 'clp_logindiv_pos', 'clp_options');
		
		add_settings_field('clp_logindiv_margin', __('Margin', self::language_file), array(&$this, 'logindiv_margin_input'), 'clp_logindiv_pos', 'clp_options');
		
		// login form tab
		
		add_settings_section('clp_options', false, array(&$this, 'clp_loginform_section'), 'clp_loginform');
		
		add_settings_field('clp_loginform_background', __('Background Picture', self::language_file), array(&$this, 'loginform_background_input'), 'clp_loginform', 'clp_options');
		
		add_settings_field('clp_loginform_img_repeat', __('Background Repeat', self::language_file), array(&$this, 'loginform_img_repeat_input'), 'clp_loginform', 'clp_options');
		
		add_settings_field('clp_loginform_img_pos', __('Position of the Background Picture', self::language_file), array(&$this, 'loginform_img_pos_input'), 'clp_loginform', 'clp_options');
		
		add_settings_field('clp_loginform_bg_color1', __('Background Colour', self::language_file), array(&$this, 'loginform_bg_color1_input'), 'clp_loginform', 'clp_options');
		
		add_settings_field('clp_loginform_bg_color2', __('Second Background Colour (for Gradient)', self::language_file), array(&$this, 'loginform_bg_color2_input'), 'clp_loginform', 'clp_options');
		
		add_settings_field('clp_loginform_text_color', __('Text Colour', self::language_file), array(&$this, 'loginform_text_color_input'), 'clp_loginform', 'clp_options');
		
		add_settings_field('clp_loginform_transparency', __('Transparency (in percent)', self::language_file), array(&$this, 'loginform_transparency_input'), 'clp_loginform', 'clp_options');
		
		add_settings_field('clp_loginform_border_style', __('Border Style', self::language_file), array(&$this, 'loginform_border_style_input'), 'clp_loginform', 'clp_options');
		
		add_settings_field('clp_loginform_border_width', __('Border Width (in px)', self::language_file), array(&$this, 'loginform_border_width_input'), 'clp_loginform', 'clp_options');
		
		add_settings_field('clp_loginform_border_color', __('Border Colour', self::language_file), array(&$this, 'loginform_border_color_input'), 'clp_loginform', 'clp_options');
		
		add_settings_field('clp_loginform_border_round', __('Rounded Corners (in px)', self::language_file), array(&$this, 'loginform_border_round_input'), 'clp_loginform', 'clp_options');
		
		add_settings_field('clp_loginform_padding', __('Padding', self::language_file), array(&$this, 'loginform_padding_input'), 'clp_loginform', 'clp_options');
		
		add_settings_field('clp_loginform_margin', __('Margin', self::language_file), array(&$this, 'loginform_margin_input'), 'clp_loginform', 'clp_options');
		
		add_settings_field('clp_loginform_shadow_x', __('Shadow (x-direction in px)', self::language_file), array(&$this, 'loginform_shadow_x_input'), 'clp_loginform', 'clp_options');
		
		add_settings_field('clp_loginform_shadow_y', __('Shadow (y-direction in px)', self::language_file), array(&$this, 'loginform_shadow_y_input'), 'clp_loginform', 'clp_options');
		
		add_settings_field('clp_loginform_shadow_softness', __('Shadow (softness in px)', self::language_file), array(&$this, 'loginform_shadow_softness_input'), 'clp_loginform', 'clp_options');
		
		add_settings_field('clp_loginform_shadow_color', __('Shadow Colour', self::language_file), array(&$this, 'loginform_shadow_color_input'), 'clp_loginform', 'clp_options');
		
		// message tab
		
		add_settings_section('clp_options', false, array(&$this, 'clp_logout_message_section'), 'clp_logout_message');
		
		add_settings_field('clp_loggedout_text_color', __('Text Colour', self::language_file), array(&$this, 'loggedout_text_color_input'), 'clp_logout_message', 'clp_options');
		
		add_settings_field('clp_loggedout_bg_color', __('Background Colour', self::language_file), array(&$this, 'loggedout_bg_color_input'), 'clp_logout_message', 'clp_options');
		
		add_settings_field('clp_loggedout_border_color', __('Border Colour', self::language_file), array(&$this, 'loggedout_border_color_input'), 'clp_logout_message', 'clp_options');
		
		add_settings_field('clp_loggedout_transparency', __('Transparency (in percent)', self::language_file), array(&$this, 'loggedout_transparency_input'), 'clp_logout_message', 'clp_options');
		
		add_settings_section('clp_options', false, array(&$this, 'clp_error_message_section'), 'clp_error_message');
		
		add_settings_field('clp_error_text_color', __('Text Colour', self::language_file), array(&$this, 'error_text_color_input'), 'clp_error_message', 'clp_options');
		
		add_settings_field('clp_error_bg_color', __('Background Colour', self::language_file), array(&$this, 'error_bg_color_input'), 'clp_error_message', 'clp_options');
		
		add_settings_field('clp_error_border_color', __('Border Colour', self::language_file), array(&$this, 'error_border_color_input'), 'clp_error_message', 'clp_options');
		
		add_settings_field('clp_error_transparency', __('Transparency (in percent)', self::language_file), array(&$this, 'error_transparency_input'), 'clp_error_message', 'clp_options');
		
		add_settings_section('clp_options', false, array(&$this, 'clp_input_section'), 'clp_input');
		
		add_settings_field('clp_input_text_color', __('Text Colour', self::language_file), array(&$this, 'input_text_color_input'), 'clp_input', 'clp_options');
		
		add_settings_field('clp_input_bg_color', __('Background Colour', self::language_file), array(&$this, 'input_bg_color_input'), 'clp_input', 'clp_options');
		
		add_settings_field('clp_input_border_color', __('Border Colour', self::language_file), array(&$this, 'input_border_color_input'), 'clp_input', 'clp_options');
		
		// link tab
		
		add_settings_section('clp_options', false, array(&$this, 'clp_link_section'), 'clp_link');
		
		add_settings_field('clp_link_size', __('Font Size', self::language_file), array(&$this, 'link_size_input'), 'clp_link', 'clp_options');
		
		add_settings_field('clp_link_text_color', __('Text Colour', self::language_file), array(&$this, 'link_text_color_input'), 'clp_link', 'clp_options');
		
		add_settings_field('clp_link_textdecoration', __('Text Decoration', self::language_file), array(&$this, 'link_textdecoration_input'), 'clp_link', 'clp_options');
		
		add_settings_field('clp_link_shadow_x', __('Shadow (x-direction in px)', self::language_file), array(&$this, 'link_shadow_x_input'), 'clp_link', 'clp_options');
		
		add_settings_field('clp_link_shadow_y', __('Shadow (y-direction in px)', self::language_file), array(&$this, 'link_shadow_y_input'), 'clp_link', 'clp_options');
		
		add_settings_field('clp_link_shadow_softness', __('Shadow (softness in px)', self::language_file), array(&$this, 'link_shadow_softness_input'), 'clp_link', 'clp_options');
		
		add_settings_field('clp_link_shadow_color', __('Shadow Colour', self::language_file), array(&$this, 'link_shadow_color_input'), 'clp_link', 'clp_options');
		
		add_settings_section('clp_options', false, array(&$this, 'clp_hover_section'), 'clp_hover');
		
		add_settings_field('clp_hover_text_color', __('Text Colour', self::language_file), array(&$this, 'hover_text_color_input'), 'clp_hover', 'clp_options');
		
		add_settings_field('clp_hover_textdecoration', __('Text Decoration', self::language_file), array(&$this, 'hover_textdecoration_input'), 'clp_hover', 'clp_options');
		
		add_settings_field('clp_hover_shadow_x', __('Shadow (x-direction in px)', self::language_file), array(&$this, 'hover_shadow_x_input'), 'clp_hover', 'clp_options');
		
		add_settings_field('clp_hover_shadow_y', __('Shadow (y-direction in px)', self::language_file), array(&$this, 'hover_shadow_y_input'), 'clp_hover', 'clp_options');
		
		add_settings_field('clp_hover_shadow_softness', __('Shadow (softness in px)', self::language_file), array(&$this, 'hover_shadow_softness_input'), 'clp_hover', 'clp_options');
		
		add_settings_field('clp_hover_shadow_color', __('Shadow Colour', self::language_file), array(&$this, 'hover_shadow_color_input'), 'clp_hover', 'clp_options');
		
		// css tab
		
		add_settings_section('clp_options', __('CSS', self::language_file), array(&$this, 'clp_css_section'), 'clp_css');
		
		add_settings_field('clp_css', __('Own CSS', self::language_file), array(&$this, 'css_input'), 'clp_css', 'clp_options');
		
		add_settings_field('clp_css_override', __('Override other styles', self::language_file), array(&$this, 'override_input'), 'clp_css', 'clp_options', array(__('By checking this, all other styles will be replaced by your CSS. Otherwise, your CSS is additional.', self::language_file)));
		
		add_settings_field('clp_css_resize', false, array(&$this, 'css_resize_field'), 'clp_css', 'clp_options');
		
		add_settings_section('clp_options', __('SVG', self::language_file), array(&$this, 'clp_svg_section'), 'clp_svg');
		
		add_settings_field('clp_svg', __('Some SVG', self::language_file), array(&$this, 'svg_input'), 'clp_svg', 'clp_options');
		
		// html tab
		
		add_settings_section('clp_options', __('Aditional html snippets', self::language_file), array(&$this, 'clp_html_section'), 'clp_html');
		
		add_settings_field('clp_login_message', __('Above Form', self::language_file), array(&$this, 'login_message_input'), 'clp_html', 'clp_options');
		
		add_settings_field('clp_login_form', __('Inside Form', self::language_file), array(&$this, 'login_form_input'), 'clp_html', 'clp_options');
		
		add_settings_field('clp_login_footer', __('Beneath Form', self::language_file), array(&$this, 'login_footer_input'), 'clp_html', 'clp_options');
		
		add_settings_field('clp_html_resize', false, array(&$this, 'html_resize_field'), 'clp_html', 'clp_options');
	
	}
	
	// main tab
	
	function clp_custom_message_section() {
	
		self::tag_it(__('You can enter your own logout message here. You can make your blog a bit more personal like that.', self::language_file), 'p', 1, false, true);
		self::tag_it(__('Furthermore, you can enter your own error message. By default, Wordpress says that either the username or the password is wrong, which is perhaps a hint to foreigners that you don&#39;t wish to give..', self::language_file), 'p', 1, false, true);
		self::tag_it(__('If you don&#39;t want to style your login page item by item, you can as well move on to enter a whole style sheet in the css tab.', self::language_file), 'p', 1, false, true);
		self::tag_it(__('You can leave any of the fields empty to keep the default settings of Wordpress.', self::language_file), 'p', 1, false, true);
		
	}
	
	function logout_custom_message_input() {
		
		a5_text_field('logout_custom_message', 'clp_options[logout_custom_message]', @self::$options['logout_custom_message'], false, array('style' => 'min-width: 350px; max-width: 500px;'));
	
	}
	
	function error_custom_message_input() {
		
		a5_text_field('error_custom_message', 'clp_options[error_custom_message]', @self::$options['error_custom_message'], false, array('style' => 'min-width: 350px; max-width: 500px;'));
		
	}
	
	function clp_export_section() {
		
		self::tag_it(__('Export the current A5 Custom Login Page settings and download them as a text file. The content of this text file can be imported into this or another A5 Custom Login Page installation:', self::language_file), 'p', 1, false, true);
		self::tag_it(sprintf(_x('The file will be named %s. After you downloaded it, you can (but don&#39;t need to) rename the file to something more meaningful.', '%s is the file name', self::language_file), '<code>a5-clp-' . str_replace('.','-', $_SERVER['SERVER_NAME']) . '-' . date('y') . date('m') . date('d') . '.txt</code>'), 'p', 1, false, true);
		
	}
	
	function export_input() {
	
		echo '<a class="button" href="' . get_bloginfo('url') . '/?clpfile=export" id="settings-download"><strong>'. __('Export &amp; Download', self::language_file) .'</strong> A5 Custom Login Page Settings File</a>';
	
	}
	
	function clp_import_section() {
		
		self::tag_it(__('Enter the content of your text file with the settings here.', self::language_file), 'p', 1, false, true);
		
	}
	
	function import_input() {
	
		a5_textarea('import', 'clp_options[import]', false, false, array('style' => 'height: 200px; min-width: 100%;'));
	
	}
	
	function impex_resize_field() {
		
		a5_resize_textarea(array('import'), true);
		
	}
	
	// advanced tab
	
	function clp_custom_redirect_section() {
	
		self::tag_it(__('You can enter redirections for each user role of the blog.', self::language_file), 'p', 1, false, true);
		self::tag_it(__('If you leave a field empty, the plugin will redirect to the default page.', self::language_file), 'p', 1, false, true);
		
	}
	
	function custom_redirect_input() {
		
		$userroles = get_editable_roles();
		
		$rows = '';
			
		foreach ($userroles as $role => $details) :
		
			$nicename = translate_user_role($details['name']);
			
			$cells = self::tag_it('<label for="custom_redirect-'.$role.'">'.$nicename.'</label>', 'td', 2);
			
			$cells .= self::tag_it(a5_url_field('custom_redirect-'.$role, 'clp_options[custom_redirect]['.$role.']', @self::$options['custom_redirect'][$role], false, array('style' => 'min-width: 350px; max-width: 500px;', 'placeholder' => 'http://example.com'), false), 'td', 2);
			
			$rows .= self::tag_it($cells, 'tr', 1);
			
		endforeach;
		
		self::tag_it($rows, 'table', 0, false, true);
		
	}
	
	function clp_hide_section() {
	
		self::tag_it(__('You can hide the links under login form if wanting to.', self::language_file), 'p', 1, false, true);
		
	}
	
	function hide_nav_input() {
		
		a5_checkbox('hide_nav', 'clp_options[hide_nav]', @self::$options['hide_nav']);
	
	}
	
	function hide_backlink_input() {
		
		a5_checkbox('hide_backlink', 'clp_options[hide_backlink]', @self::$options['hide_backlink']);
		
	}
	
	function clp_debug_section() {
		
		self::tag_it(__('There seem to be problems with the virtual stylesheet in some environments. By choosing to write the styles inline, those can be avoided.).', self::language_file), 'p', 1, false, true);
		
	}
	
	function compress_field($labels) {
		
		a5_checkbox('compress', 'clp_options[compress]', @self::$options['compress'], $labels[0]);
		
	}
	
	function debug_input() {
	
		a5_checkbox('inline', 'clp_options[inline]', @self::$options['inline']);
	
	}
	
	function priority_input() {
	
		a5_number_field('priority', 'clp_options[priority]', @self::$options['priority'], false, array('step' => 10));
	
	}
	
	// body and button tab
	
	function clp_body_section() {
		
		self::tag_it(__('You can enter the url of the background picture, that you want to have on the login page. Just upload any picture via the uploader on the Media section and copy the url of that file here. Leave it empty, if you don&#39;t want a picture. Background images are tiled by default. You can select the direction of repeating the image or to not repeat it. The position of the image can be something like &#39;100px 50%&#39; or &#39center top&#39;. If you want the background picture to cover the whole screen, put &#39;cover&#39; as size. Otherwise, fill in any css (like &#39;auto 150px&#39;).', self::language_file), 'p', 1, false, true);
		self::tag_it(__('In the last section, you choose the background colour and the colour of the text in the html body element. If you give two background colours, you can create a gradient. Colour no. 1 will always be up.', self::language_file), 'p', 1, false, true);
		self::tag_it(__('You can leave any of the fields empty to keep the default settings of Wordpress.', self::language_file), 'p', 1, false, true);
		
	}
	
	function body_background_input() {
		
		a5_text_field('body_background', 'clp_options[body_background]', @self::$options['body_background'], false, array('style' => 'min-width: 350px; max-width: 500px;'));
		
	}
	
	function body_img_repeat_input() {
		
		$options = array(array('no-repeat', 'no-repeat'), array('repeat-x', 'repeat-x'), array('repeat-y', 'repeat-y'));
		
		a5_select('body_img_repeat', 'clp_options[body_img_repeat]', $options, @self::$options['body_img_repeat'], false, __('default', self::language_file), array('style' => 'min-width: 350px; max-width: 500px;'));
			
	}
		
	function body_img_pos_input() {
		
		a5_text_field('body_img_pos', 'clp_options[body_img_pos]', @self::$options['body_img_pos'], false, array('style' => 'min-width: 350px; max-width: 500px;'));
		
	}
	
	function body_bg_color1_input() {
		
		a5_text_field('body_bg_color1', 'clp_options[body_bg_color1]', @self::$options['body_bg_color1'], false, array('class' => 'color-picker'));
		
	}
	
	function body_bg_color2_input() {
		
		a5_text_field('body_bg_color2', 'clp_options[body_bg_color2]', @self::$options['body_bg_color2'], false, array('class' => 'color-picker'));
		
	}
	
	function body_bg_size_input() {
		
		a5_text_field('body_bg_size', 'clp_options[body_bg_size]', @self::$options['body_bg_size'], false, array('style' => 'min-width: 350px; max-width: 500px;'));
		
	}
	
	function clp_button_section() {
		
		self::tag_it(__('Enter the background, text and border colour of the submit button here. The button will look static if you don&#39;t give values for the hover state of it. If you want to have a gradient, enter two background colours. The first one will be up then.', self::language_file), 'p', 1, false, true);
		
	}
	
	function button_bg_color1_input() {
		
		a5_text_field('button_bg_color1', 'clp_options[button_bg_color1]', @self::$options['button_bg_color1'], false, array('class' => 'color-picker'));
		
	}
	
	function button_bg_color2_input() {
		
		a5_text_field('button_bg_color2', 'clp_options[button_bg_color2]', @self::$options['button_bg_color2'], false, array('class' => 'color-picker'));
		
	}
	
	function button_text_color_input() {
		
		a5_text_field('button_text_color', 'clp_options[button_text_color]', @self::$options['button_text_color'], false, array('class' => 'color-picker'));
		
	}
	
	function button_border_color_input() {
		
		a5_text_field('button_border_color', 'clp_options[button_border_color]', @self::$options['button_border_color'], false, array('class' => 'color-picker'));
		
	}
	
	function btn_hover_bg_color1_input() {
		
		a5_text_field('btn_hover_bg_color1', 'clp_options[btn_hover_bg_color1]', @self::$options['btn_hover_bg_color1'], false, array('class' => 'color-picker'));
		
	}
	
	function btn_hover_bg_color2_input() {
		
		a5_text_field('btn_hover_bg_color2', 'clp_options[btn_hover_bg_color2]', @self::$options['btn_hover_bg_color2'], false, array('class' => 'color-picker'));
		
	}
	
	function btn_hover_text_color_input() {
		
		a5_text_field('btn_hover_text_color', 'clp_options[btn_hover_text_color]', @self::$options['btn_hover_text_color'], false, array('class' => 'color-picker'));
		
	}
	
	function btn_hover_border_color_input() {
		
		a5_text_field('btn_hover_border_color', 'clp_options[btn_hover_border_color]', @self::$options['btn_hover_border_color'], false, array('class' => 'color-picker'));
		
	}
	
	// logo tab
	
	function clp_logo_section() {
		
		self::tag_it(__('You can enter the url of the logo, that you want to have in place of the WP logo on the login screen. Just upload any picture (best is a png or gif with transparent background) via the uploader on the Media section and copy the url of that file here.', self::language_file), 'p', 1, false, true);
		self::tag_it(__('In the URL field, you enter the URL to which the logo should link.', self::language_file), 'p', 1, false, true);
		self::tag_it(__('You can leave any of the fields empty to keep the default settings of Wordpress.', self::language_file), 'p', 1, false, true);
		
	}
	
	function logo_url_input() {
			
		a5_text_field('logo', 'clp_options[logo]', @self::$options['logo'], false, array('style' => 'min-width: 350px; max-width: 500px;'));
		
	}
		
	function link_url_input() {
		
		a5_url_field('url', 'clp_options[url]', @self::$options['url'], false, array('style' => 'min-width: 350px; max-width: 500px;', 'placeholder' => home_url('/')));
		
	}
	
	function logo_title_input() {
		
		a5_text_field('title', 'clp_options[title]', @self::$options['title'], false, array('style' => 'min-width: 350px; max-width: 500px;'));
		
	}
	
	function clp_logo_size_section() {
		
		self::tag_it(__('If your logo is larger than the default WP-logo (274px by 63px), you can enter the width and the height of it here.', self::language_file), 'p', 1, false, true);
		self::tag_it(__('The width and height of the logo-container are by default 326px and 67px. They are used to move the Logo around, since the background-position is always &#39;center top&#39;.', self::language_file), 'p', 1, false, true);
		
	}
	
	function logo_width_input() {
		
		a5_number_field('logo_width', 'clp_options[logo_width]', @self::$options['logo_width'], false, array('step' => 1));
		
	}
	
	function logo_height_input() {
		
		a5_number_field('logo_height', 'clp_options[logo_height]', @self::$options['logo_height'], false, array('step' => 1));
		
	}
	
	function h1_width_input() {
		
		a5_number_field('h1_width', 'clp_options[h1_width]', @self::$options['h1_width'], false, array('step' => 1));
		
	}
	
	function h1_height_input() {
		
		a5_number_field('h1_height', 'clp_options[h1_height]', @self::$options['h1_height'], false, array('step' => 1));
		
	}
	
	function h1_margin_input() {
		
		a5_text_field('h1_margin', 'clp_options[h1_margin]', @self::$options['h1_margin']);
		
	}
	
	function h1_padding_input() {
		
		a5_text_field('h1_padding', 'clp_options[h1_padding]', @self::$options['h1_padding']);
	
	}
	
	function clp_logo_style_section() {
		
		self::tag_it(__('Here you can style the logo a bit. Give it a shadow or round corners if you like.', self::language_file), 'p', 1, false, true);
		
	}
	
	function h1_corner_input() {
		
		a5_number_field('h1_corner', 'clp_options[h1_corner]', @self::$options['h1_corner'], false, array('step' => 1));
		
	}
		
	function h1_shadow_x_input() {
		
		a5_number_field('h1_shadow_x', 'clp_options[h1_shadow_x]', @self::$options['h1_shadow_x'], false, array('step' => 1));
		
	}
	
	function h1_shadow_y_input() {
		
		a5_number_field('h1_shadow_y', 'clp_options[h1_shadow_y]', @self::$options['h1_shadow_y'], false, array('step' => 1));
		
	}
	
	function h1_shadow_softness_input() {
		
		a5_number_field('h1_shadow_softness', 'clp_options[h1_shadow_softness]', @self::$options['h1_shadow_softness'], false, array('step' => 1));
		
	}
	
	function h1_shadow_color_input() {
		
		a5_text_field('h1_shadow_color', 'clp_options[h1_shadow_color]', @self::$options['h1_shadow_color'], false, array('class' => 'color-picker'));
		
	}
	
	// logindiv tab
	
	function clp_logindiv_section() {
		
		self::tag_it(__('You can enter the url of the background picture, that you want to have on the login container. Just upload any picture via the uploader on the Media section and copy the url of that file here. Leave it empty, if you don&#39;t want a picture. Background images are tiled by default. You can select the direction of repeating the image or to not repeat it. The position of the image can be something like &#39;100px 50%&#39; or &#39;center top&#39;.', self::language_file), 'p', 1, false, true);
		self::tag_it(__('In the next section, you choose the background colour and the colour of the text in the login container. If you give two background colours, you can create a gradient. Colour no. 1 will always be up.', self::language_file), 'p', 1, false, true);
		self::tag_it(__('Choose a border, if wanting one. Define style, width and whether or not, you want to have rounded corners (is not supported by all browsers).', self::language_file), 'p', 1, false, true);
		self::tag_it(__('At last, give the container a shadow (is not supported by all browsers).', self::language_file), 'p', 1, false, true);
		self::tag_it(__('You can leave any of the fields empty to keep the default settings of Wordpress.', self::language_file), 'p', 1, false, true);
		
	}
	
	function logindiv_background_input() {
		
		a5_text_field('logindiv_background', 'clp_options[logindiv_background]', @self::$options['logindiv_background']);	
		
	}
	
	function logindiv_img_repeat_input() {
		
		$options = array(array('no-repeat', 'no-repeat'), array('repeat-x', 'repeat-x'), array('repeat-y', 'repeat-y'));
		
		a5_select('logindiv_img_repeat', 'clp_options[logindiv_img_repeat]', $options, @self::$options['logindiv_img_repeat'], false, __('default', self::language_file), array('style' => 'min-width: 350px; max-width: 500px;'));
		
	}
		
	function logindiv_img_pos_input() {
		
		a5_text_field('logindiv_img_pos', 'clp_options[logindiv_img_pos]', @self::$options['logindiv_img_pos']);
		
	}
		
	function logindiv_bg_color1_input() {
		
		a5_text_field('logindiv_bg_color1', 'clp_options[logindiv_bg_color1]', @self::$options['logindiv_bg_color1'], false, array('class' => 'color-picker'));
		
	}
		
	function logindiv_bg_color2_input() {	

		a5_text_field('logindiv_bg_color2', 'clp_options[logindiv_bg_color2]', @self::$options['logindiv_bg_color2'], false, array('class' => 'color-picker'));
		
	}
	
	function logindiv_text_color_input() {
	
		a5_text_field('logindiv_text_color', 'clp_options[logindiv_text_color]', @self::$options['logindiv_text_color'], false, array('class' => 'color-picker'));
		
	}
	
	function logindiv_transparency_input() {
		
		a5_number_field('logindiv_transparency', 'clp_options[logindiv_transparency]', @self::$options['logindiv_transparency'], false, array('step' => 1, 'min' => 0, 'max' => 100));
		
	}
	
	function logindiv_border_style_input() {
		
		$border_style = array(array('none', 'none'), array('dotted', 'dotted'), array('dashed', 'dashed'), array('solid', 'solid'), array('double', 'double'), array('groove', 'groove'), array('ridge', 'rigde'), array('inset', 'inset'), array('outset', 'outset'));
		
		a5_select('logindiv_border_style', 'clp_options[logindiv_border_style]', $border_style, @self::$options['logindiv_border_style'], false, __('choose a border style', self::language_file), array('style' => 'min-width: 350px; max-width: 500px;'));
		
	}
	
	function logindiv_border_width_input() {
		
		a5_number_field('logindiv_border_width', 'clp_options[logindiv_border_width]', @self::$options['logindiv_border_width'], false, array('step' => 1));
		
	}
	
	function logindiv_border_color_input() {
		
		a5_text_field('logindiv_border_color', 'clp_options[logindiv_border_color]', @self::$options['logindiv_border_color'], false, array('class' => 'color-picker'));
	
	}
	
	function logindiv_border_round_input() {
		
		a5_number_field('logindiv_border_round', 'clp_options[logindiv_border_round]', @self::$options['logindiv_border_round'], false, array('step' => 1));
		
	}
	
	function logindiv_shadow_x_input() {
		
		a5_number_field('logindiv_shadow_x', 'clp_options[logindiv_shadow_x]', @self::$options['logindiv_shadow_x'], false, array('step' => 1));
		
	}
		
	function logindiv_shadow_y_input() {
		
		a5_number_field('logindiv_shadow_y', 'clp_options[logindiv_shadow_y]', @self::$options['logindiv_shadow_y'], false, array('step' => 1));
		
	}
	
	function logindiv_shadow_softness_input() {
			
		a5_number_field('logindiv_shadow_softness', 'clp_options[logindiv_shadow_softness]', @self::$options['logindiv_shadow_softness'], false, array('step' => 1));
		
	}
	
	function logindiv_shadow_color_input() {
		
		a5_text_field('logindiv_shadow_color', 'clp_options[logindiv_shadow_color]', @self::$options['logindiv_shadow_color'], false, array('class' => 'color-picker'));
	
	}
	
	function clp_logindiv_pos_section() {
		
		self::tag_it(__('Here you can give the whole login container a position. If you enter &#39;0&#39; in both of the fields, it will be in the top left corner of the screen.', self::language_file), 'p', 1, false, true);
		self::tag_it(__('The Padding and Margin are given as css value. I.e. &#39;144px 0 0&#39; (which is the default padding of the login container).', self::language_file), 'p', 1, false, true);
		
	}
	
	function logindiv_left_input() {
		
		a5_number_field('logindiv_left', 'clp_options[logindiv_left]', @self::$options['logindiv_left'], false, array('step' => 1));
			
	}
	
	function logindiv_top_input() {
		
		a5_number_field('logindiv_top', 'clp_options[logindiv_top]', @self::$options['logindiv_top'], false, array('step' => 1));
		
	}
	
	function logindiv_width_input() {
		
		a5_number_field('logindiv_width', 'clp_options[logindiv_width]', @self::$options['logindiv_width'], false, array('step' => 1));
		
	}
	
	function logindiv_height_input() {
		
		a5_number_field('logindiv_height', 'clp_options[logindiv_height]', @self::$options['logindiv_height'], false, array('step' => 1));
		
	}
	
	function logindiv_padding_input() {
		
		a5_text_field('logindiv_padding', 'clp_options[logindiv_padding]', @self::$options['logindiv_padding']);
		
	}
	
	function logindiv_margin_input() {
		
		a5_text_field('logindiv_margin', 'clp_options[logindiv_margin]', @self::$options['logindiv_margin']);
		
	}
	
	// loginform tab
	
	function clp_loginform_section() {
		
		self::tag_it(__('You can enter the url of the background picture, that you want to have in the login form. Just upload any picture via the uploader on the Media section and copy the url of that file here. Leave it empty, if you don&#39;t want a picture. Background images are tiled by default. You can select the direction of repeating the image or to not repeat it. The position of the image can be something like &#39;100px 50%&#39; or &#39center top&#39;.', self::language_file), 'p', 1, false, true);
		self::tag_it(__('In the next section, you choose the background colour and the colour of the text in the login form. If you give two background colours, you can create a gradient. Colour no. 1 will always be up.', self::language_file), 'p', 1, false, true);
		self::tag_it(__('Choose a border, if wanting one. Define style, width and whether or not, you want to have rounded corners (is not supported by all browsers).', self::language_file), 'p', 1, false, true);
		self::tag_it(__('Margin and Padding are given as css values. The form has a left margin of 8px by default and a padding of 26px 24px 46px. By changing the top and the bottom padding, you can stretch the form in its length.', self::language_file), 'p', 1, false, true);
		self::tag_it(__('At last, give the form a shadow (is not supported by all browsers).', self::language_file), 'p', 1, false, true);
		self::tag_it(__('You can leave any of the fields empty to keep the default settings of Wordpress.', self::language_file), 'p', 1, false, true);
		
	}
	
	function loginform_background_input() {
		
		a5_text_field('loginform_background', 'clp_options[loginform_background]', @self::$options['loginform_background'], false, array('style' => 'min-width: 350px; max-width: 500px;'));	
		
	}
	
	function loginform_img_repeat_input() {
		
		$options = array(array('no-repeat', 'no-repeat'), array('repeat-x', 'repeat-x'), array('repeat-y', 'repeat-y'));
		
		a5_select('loginform_img_repeat', 'clp_options[loginform_img_repeat]', $options, @self::$options['loginform_img_repeat'], false, __('default', self::language_file), array('style' => 'min-width: 350px; max-width: 500px;'));
		
	}
		
	function loginform_img_pos_input() {
		
		a5_text_field('loginform_img_pos', 'clp_options[loginform_img_pos]', @self::$options['loginform_img_pos']);
		
	}
		
	function loginform_bg_color1_input() {
		
		a5_text_field('loginform_bg_color1', 'clp_options[loginform_bg_color1]', @self::$options['loginform_bg_color1'], false, array('class' => 'color-picker'));
		
	}
		
	function loginform_bg_color2_input() {	

		a5_text_field('loginform_bg_color2', 'clp_options[loginform_bg_color2]', @self::$options['loginform_bg_color2'], false, array('class' => 'color-picker'));
		
	}
	
	function loginform_text_color_input() {
	
		a5_text_field('loginform_text_color', 'clp_options[loginform_text_color]', @self::$options['loginform_text_color'], false, array('class' => 'color-picker'));
		
	}
	
	function loginform_transparency_input() {
		
		a5_number_field('loginform_transparency', 'clp_options[loginform_transparency]', @self::$options['loginform_transparency'], false, array('step' => 1, 'min' => 0, 'max' => 100));
		
	}
	
	function loginform_border_style_input() {
		
		$border_style = array(array('none', 'none'), array('dotted', 'dotted'), array('dashed', 'dashed'), array('solid', 'solid'), array('double', 'double'), array('groove', 'groove'), array('ridge', 'rigde'), array('inset', 'inset'), array('outset', 'outset'));
		
		a5_select('loginform_border_style', 'clp_options[loginform_border_style]', $border_style, @self::$options['loginform_border_style'], false, __('choose a border style', self::language_file), array('style' => 'min-width: 350px; max-width: 500px;'));
		
	}
	
	function loginform_border_width_input() {
		
		a5_number_field('loginform_border_width', 'clp_options[loginform_border_width]', @self::$options['loginform_border_width'], false, array('step' => 1));
		
	}
	
	function loginform_border_color_input() {
		
		a5_text_field('loginform_border_color', 'clp_options[loginform_border_color]', @self::$options['loginform_border_color'], false, array('class' => 'color-picker'));
	
	}
	
	function loginform_border_round_input() {
		
		a5_number_field('loginform_border_round', 'clp_options[loginform_border_round]', @self::$options['loginform_border_round'], false, array('step' => 1));
		
	}
	
	function loginform_shadow_x_input() {
		
		a5_number_field('loginform_shadow_x', 'clp_options[loginform_shadow_x]', @self::$options['loginform_shadow_x'], false, array('step' => 1));
		
	}
	
	function loginform_padding_input() {
		
		a5_text_field('loginform_padding', 'clp_options[loginform_padding]', @self::$options['loginform_padding']);
		
	}
	
	function loginform_margin_input() {
		
		a5_text_field('loginform_margin', 'clp_options[loginform_margin]', @self::$options['loginform_margin']);
		
	}
		
	function loginform_shadow_y_input() {
		
		a5_number_field('loginform_shadow_y', 'clp_options[loginform_shadow_y]', @self::$options['loginform_shadow_y'], false, array('step' => 1));
		
	}
	
	function loginform_shadow_softness_input() {
			
		a5_number_field('loginform_shadow_softness', 'clp_options[loginform_shadow_softness]', @self::$options['loginform_shadow_softness'], false, array('step' => 1));
		
	}
	
	function loginform_shadow_color_input() {
		
		a5_text_field('loginform_shadow_color', 'clp_options[loginform_shadow_color]', @self::$options['loginform_shadow_color'], false, array('class' => 'color-picker'));
	
	}
	
	// message tab
	
	function clp_logout_message_section() {
		
		self::tag_it(__('This changes the the text container, that appears, when you have successfully logged out.', self::language_file), 'p', 1, false, true);
		self::tag_it(__('You can leave any of the fields empty to keep the default settings of Wordpress.', self::language_file), 'p', 1, false, true);
		
	}
		
	function loggedout_text_color_input() {
		
		a5_text_field('loggedout_text_color', 'clp_options[loggedout_text_color]', @self::$options['loggedout_text_color'], false, array('class' => 'color-picker'));
		
	}
	
	function loggedout_bg_color_input() {
			
		a5_text_field('loggedout_bg_color', 'clp_options[loggedout_bg_color]', @self::$options['loggedout_bg_color'], false, array('class' => 'color-picker'));
		
	}
		
	function loggedout_border_color_input() {
			
		a5_text_field('loggedout_border_color', 'clp_options[loggedout_border_color]', @self::$options['loggedout_border_color'], false, array('class' => 'color-picker'));
		
	}
	
	function loggedout_transparency_input() {
			
		a5_number_field('loggedout_transparency', 'clp_options[loggedout_transparency]', @self::$options['loggedout_transparency'], false, array('step' => 1, 'min' => 0, 'max' => 100));
		
	}
	
	function clp_error_message_section() {
		
		self::tag_it(__('This changes the text container, that appears, when you get an error logging in.', self::language_file), 'p', 1, false, true);
		
	}
		
	function error_text_color_input() {
		
		a5_text_field('error_text_color', 'clp_options[error_text_color]', @self::$options['error_text_color'], false, array('class' => 'color-picker'));
		
	}
	
	function error_bg_color_input() {
			
		a5_text_field('error_bg_color', 'clp_options[error_bg_color]', @self::$options['error_bg_color'], false, array('class' => 'color-picker'));
		
	}
		
	function error_border_color_input() {
			
		a5_text_field('error_border_color', 'clp_options[error_border_color]', @self::$options['error_border_color'], false, array('class' => 'color-picker'));
		
	}
	
	function error_transparency_input() {
			
		a5_number_field('error_transparency', 'clp_options[error_transparency]', @self::$options['error_transparency'], false, array('step' => 1, 'min' => 0, 'max' => 100));
		
	}
	
	function clp_input_section() {
		
		self::tag_it(__('This changes the colours of the name and password fields of the log in form.', self::language_file), 'p', 1, false, true);
		
	}
		
	function input_text_color_input() {
		
		a5_text_field('input_text_color', 'clp_options[input_text_color]', @self::$options['input_text_color'], false, array('class' => 'color-picker'));
		
	}
	
	function input_bg_color_input() {
			
		a5_text_field('input_bg_color', 'clp_options[input_bg_color]', @self::$options['input_bg_color'], false, array('class' => 'color-picker'));
		
	}
		
	function input_border_color_input() {
			
		a5_text_field('input_border_color', 'clp_options[input_border_color]', @self::$options['input_border_color'], false, array('class' => 'color-picker'));
		
	}
	
	// link tab
	
	function clp_link_section() {
	
		self::tag_it(__('Style the links by giving a text colour, text decoration and shadow for the link and the hover style.', self::language_file), 'p', 1, false, true);
		self::tag_it(sprintf(__('For the font size, give a css value, such as %1$s or %2$s.', self::language_file), '<em>&#39;12px&#39;</em>', '<em>&#39;1em&#39;</em>'), 'p', 1, false, true);
		self::tag_it(__('You can leave any of the fields empty to keep the default settings of Wordpress.', self::language_file), 'p', 1, false, true);
		
	}
	
	function link_size_input() {
		
		a5_text_field('link_size', 'clp_options[link_size]', @self::$options['link_size']);
		
	}
	
	function link_text_color_input() {
			
		a5_text_field('link_text_color', 'clp_options[link_text_color]', @self::$options['link_text_color'], false, array('class' => 'color-picker'));
		
	}
	
	function link_textdecoration_input() {
		
		$textdeco = array(array('none', 'none'), array('underline', 'underline'), array('overline', 'overline'), array('line-through', 'line-through'), array('blink', 'blink'));
		
		a5_select('link_textdecoration', 'clp_options[link_textdecoration]', $textdeco, @self::$options['link_textdecoration'], false, false, array('style' => 'min-width: 350px; max-width: 500px;'));
		
	}
		
	function link_shadow_x_input() {
		
		a5_number_field('link_shadow_x', 'clp_options[link_shadow_x]', @self::$options['link_shadow_x'], false, array('step' => 1));
		
	}
	
	function link_shadow_y_input() {
		
		a5_number_field('link_shadow_y', 'clp_options[link_shadow_y]', @self::$options['link_shadow_y'], false, array('step' => 1));
		
	}
	
	function link_shadow_softness_input() {
		
		a5_number_field('link_shadow_softness', 'clp_options[link_shadow_softness]', @self::$options['link_shadow_softness'], false, array('step' => 1));
		
	}
	
	function link_shadow_color_input() {
		
		a5_text_field('link_shadow_color', 'clp_options[link_shadow_color]', @self::$options['link_shadow_color'], false, array('class' => 'color-picker'));
		
	}
	
	function clp_hover_section() {
	
		self::tag_it(__('The same for the hover state.', self::language_file), 'p', 1, false, true);
		
	}
	
	function hover_text_color_input() {
			
		a5_text_field('hover_text_color', 'clp_options[hover_text_color]', @self::$options['hover_text_color'], false, array('class' => 'color-picker'));
		
	}
	
	function hover_textdecoration_input() {
		
		$textdeco = array(array('none', 'none'), array('underline', 'underline'), array('overline', 'overline'), array('line-through', 'line-through'), array('blink', 'blink'));
		
		a5_select('hover_textdecoration', 'clp_options[hover_textdecoration]', $textdeco, @self::$options['hover_textdecoration'], false, false, array('style' => 'min-width: 350px; max-width: 500px;'));
		
	}
		
	function hover_shadow_x_input() {
		
		a5_number_field('hover_shadow_x', 'clp_options[hover_shadow_x]', @self::$options['hover_shadow_x'], false, array('step' => 1));
		
	}
	
	function hover_shadow_y_input() {
		
		a5_number_field('hover_shadow_y', 'clp_options[hover_shadow_y]', @self::$options['hover_shadow_y'], false, array('step' => 1));
		
	}
	
	function hover_shadow_softness_input() {
		
		a5_number_field('hover_shadow_softness', 'clp_options[hover_shadow_softness]', @self::$options['hover_shadow_softness'], false, array('step' => 1));
		
	}
	
	function hover_shadow_color_input() {
		
		a5_text_field('hover_shadow_color', 'clp_options[hover_shadow_color]', @self::$options['hover_shadow_color'], false, array('class' => 'color-picker'));
		
	}
	
	// css tab
	
	function clp_css_section() {
		
		self::tag_it(__('Here you can enter some css. You either can enter an entire style sheet or just some additional css.', self::language_file), 'p', 1, false, true);
		self::tag_it(__('This gives you much more freedom with styling your login widget than the rather foolproof but very limited options .', self::language_file), 'p', 1, false, true);
		self::tag_it(__('You can of course copy the style sheet written by the plugin, paste it here and start finetuning.', self::language_file), 'p', 1, false, true);
		
	}
	
	function css_input() {
	
		a5_textarea('css', 'clp_options[css]', @self::$options['css'], false, array('style' => 'height: 200px; min-width: 100%;'));
	
	}
	
	function override_input($labels) {
		
		a5_checkbox('override', 'clp_options[override]', @self::$options['override'], $labels[0]);
		
	}
	
	function clp_svg_section() {
		
		self::tag_it(__('Here you can enter some svg, i.e. filter rules to use in Firefox. In case you wish to use the svg as an image, it will be just above the login form.', self::language_file), 'p', 1, false, true);
		
	}
	
	function svg_input() {
	
		a5_textarea('svg', 'clp_options[svg]', @self::$options['svg'], false, array('style' => 'height: 200px; min-width: 100%;'));
	
	}
	
	function css_resize_field() {
		
		a5_resize_textarea(array('css', 'svg'), true);
		
	}
	
	// html tab
	
	function clp_html_section() {
		
		self::tag_it(__('If you want to have some additional html in your login page, there are three places to put it. Above the form, in the form and under it.', self::language_file), 'p', 1, false, true);
		self::tag_it(__('You can use the additional css to style the html snippets that you enter here.', self::language_file), 'p', 1, false, true);
		
	}
	
	function login_message_input() {
	
		a5_textarea('login_message', 'clp_options[login_message]', @self::$options['login_message'], false, array('style' => 'height: 200px; min-width: 100%;'));
	
	}
	
	function login_form_input() {
	
		a5_textarea('login_form', 'clp_options[login_form]', @self::$options['login_form'], false, array('style' => 'height: 200px; min-width: 100%;'));
	
	}
	
	function login_footer_input() {
	
		a5_textarea('login_footer', 'clp_options[login_footer]', @self::$options['login_footer'], false, array('style' => 'height: 200px; min-width: 100%;'));
	
	}
	
	function html_resize_field() {
		
		a5_resize_textarea(array('login_message', 'login_form', 'login_footer'), true);
		
	}
	
	/**
	 *
	 * Actually build the option pages
	 *
	 */
	function build_options_page() {
		
		// tabed browsing
		
		$default_tab = (isset(self::$options['last_open'])) ? self::$options['last_open'] : 'main_tab';
		
		$active = (isset($_GET['tab'])) ? $_GET['tab'] : $default_tab;
		
		// this is only necessary if the plugin is activated for network
		
		if (@$_GET['action'] == 'update') :
		
			$input = $_POST['clp_options'];
			
			self::$options = $this->validate($input);
			
			update_site_option('clp_options', self::$options);
			
			$this->initialize_settings();
		
		endif;
		
		// the main options page begins here
		
		$eol = "\r\n";
		
		$tab = "\t";
		
		$dtab = $tab.$tab;
		
		// navigation
		
		self::open_page('A5 Custom Login Page', __('http://wasistlos.waldemarstoffel.com/plugins-fur-wordpress/a5-custom-login-page', self::language_file), 'custom-login-page');
		
		settings_errors();
		
		$tabs ['main_tab'] = array( 'class' => ($active == 'main_tab') ? ' nav-tab-active' : '', 'text' => __('General Options', self::language_file));
		$tabs ['advanced_tab'] = array( 'class' => ($active == 'advanced_tab') ? ' nav-tab-active' : '', 'text' => __('Advanced Options', self::language_file));
		$tabs ['body_tab'] = array( 'class' => ($active == 'body_tab') ? ' nav-tab-active' : '', 'text' => __('Body & Submit Button', self::language_file));
		$tabs ['logo_tab'] = array( 'class' => ($active == 'logo_tab') ? ' nav-tab-active' : '', 'text' => __('Logo', self::language_file));
		$tabs ['logindiv_tab'] = array( 'class' => ($active == 'logindiv_tab') ? ' nav-tab-active' : '', 'text' => __('Login Container', self::language_file));
		$tabs ['loginform_tab'] = array( 'class' => ($active == 'loginform_tab') ? ' nav-tab-active' : '', 'text' => __('Login Form', self::language_file));
		$tabs ['message_tab'] = array( 'class' => ($active == 'message_tab') ? ' nav-tab-active' : '', 'text' => __('Messages & Input Fields', self::language_file));
		$tabs ['link_tab'] = array( 'class' => ($active == 'link_tab') ? ' nav-tab-active' : '', 'text' => __('Links', self::language_file));
		$tabs ['css_tab'] = array( 'class' => ($active == 'css_tab') ? ' nav-tab-active' : '', 'text' => __('CSS', self::language_file));
		$tabs ['html_tab'] = array( 'class' => ($active == 'html_tab') ? ' nav-tab-active' : '', 'text' => __('Additional HTML', self::language_file));
		if (!is_multisite()) $tabs ['preview_tab'] = array( 'id' => 'prev', 'class' => ($active == 'preview_tab') ? ' nav-tab-active' : ' thickbox', 'text' => __('Preview'));
		
		$args = array(
			'page' => 'clp-settings',
			'menu_items' => $tabs
		);
		
		self::nav_menu($args);

		$action = (is_plugin_active_for_network(CLP_BASE)) ? '?page=clp-settings&tab='.$active.'&action=update' : 'options.php';
		
		self::open_form($action);
		
		// nonce and stuff which is the same for all tabs
		
		wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false);
		wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false);
		
		a5_hidden_field('tab', 'clp_options[tab]', $active, true);
		
		settings_fields('clp_options');
		
		if (!is_multisite()) add_thickbox();

		// the actual option tabs
		
		if ($active == 'main_tab') :
		
			self::open_tab();
			
			self::sortable('top', self::postbox(__('Logged Out and Error Messages', self::language_file), 'main-options', 'clp_message'));
			
			self::sortable('middle', self::postbox(__('Import / Export', self::language_file), 'impex', array('clp_export', 'clp_import')));
			
			if (WP_DEBUG === true) self::sortable('deep-down', self::debug_info(self::$options, __('Debug Info', self::language_file)));
			
			submit_button();
			
			self::close_tab();
			
		endif;
		
		if ($active == 'advanced_tab') :
		
			self::open_tab();
			
			self::sortable('top', self::postbox(__('Custom Redirects', self::language_file), 'redirect', 'clp_redirect'));
			
			self::sortable('middle', self::postbox(__('Hide Links', self::language_file), 'hide-links', 'clp_hide'));
			
			self::sortable('bottom', self::postbox(__('Debug dynamical CSS', self::language_file), 'debug-css', 'clp_debug'));
			
			if (WP_DEBUG === true) self::sortable('deep-down', self::debug_info(self::$options, __('Debug Info', self::language_file)));
			
			submit_button();
			
			self::close_tab();
			
		endif;
		
		if ($active == 'body_tab') :
		
			self::open_tab();
			
			self::sortable('top', self::postbox(__('Body', self::language_file), 'body', 'clp_body'));
			
			self::sortable('middle', self::postbox(__('Submit Button', self::language_file), 'submit-button', 'clp_button'));
			
			if (WP_DEBUG === true) self::sortable('deep-down', self::debug_info(self::$options, __('Debug Info', self::language_file)));
			
			submit_button();
			
			self::close_tab();
			
		endif;
		
		if ($active == 'logo_tab') :
		
			self::open_tab();
			
			self::sortable('top', self::postbox(__('Logo of the Login Screen', self::language_file), 'logo', 'clp_logo'));
			
			self::sortable('middle', self::postbox(__('Position and Size of the Logo', self::language_file), 'logo-pos', 'clp_logo_size'));
			
			self::sortable('bottom', self::postbox(__('Styling of the Logo', self::language_file), 'logo-style', 'clp_logo_style'));
			
			if (WP_DEBUG === true) self::sortable('deep-down', self::debug_info(self::$options, __('Debug Info', self::language_file)));
			
			submit_button();
			
			self::close_tab();;
			
		endif;
		
		if ($active == 'logindiv_tab') :
		
			self::open_tab();
			
			self::sortable('top', self::postbox(__('Login Container', self::language_file), 'login', 'clp_logindiv'));
			
			self::sortable('middle', self::postbox(__('Position and Size of the Login Container', self::language_file), 'login-pos', 'clp_logindiv_pos'));
			
			if (WP_DEBUG === true) self::sortable('deep-down', self::debug_info(self::$options, __('Debug Info', self::language_file)));
			
			submit_button();
			
			self::close_tab();
			
		endif;
		
		if ($active == 'loginform_tab') :
		
			self::open_tab('clp', 'loginform');
			
			self::sortable('top', self::postbox(__('Login Form', self::language_file), 'form', 'clp_loginform'));
			
			if (WP_DEBUG === true) self::sortable('deep-down', self::debug_info(self::$options, __('Debug Info', self::language_file)));
			
			submit_button();
			
			self::close_tab();
			
		endif;
		
		if ($active == 'message_tab') :
		
			self::open_tab();
			
			self::sortable('top', self::postbox(__('Logout Message', self::language_file), 'message-logout', 'clp_logout_message'));
			
			self::sortable('middle', self::postbox(__('Error Message', self::language_file), 'message-error', 'clp_error_message'));
			
			self::sortable('bottom', self::postbox(__('Input Fields', self::language_file), 'fields', 'clp_input'));
			
			if (WP_DEBUG === true) self::sortable('deep-down', self::debug_info(self::$options, __('Debug Info', self::language_file)));
			
			submit_button();
			
			self::close_tab();
			
		endif;
		
		if ($active == 'link_tab') :
		
			self::open_tab();
			
			self::sortable('top', self::postbox(__('Links', self::language_file), 'link', 'clp_link'));
			
			self::sortable('middel', self::postbox(__('Links Hover', self::language_file), 'link-hover', 'clp_hover'));
			
			if (WP_DEBUG === true) self::sortable('deep-down', self::debug_info(self::$options, __('Debug Info', self::language_file)));
			
			submit_button();
			
			self::close_tab();
			
		endif;
		
		if ($active == 'css_tab') :
		
			self::open_tab(2);
			
			self::sortable('top', self::postbox(__('CSS and SVG', self::language_file), 'css-svg', array('clp_css', 'clp_svg')));
		
			if (WP_DEBUG === true) self::sortable('deep-down', self::debug_info(self::$options, __('Debug Info', self::language_file)));
			
			submit_button();
			
			$elements = array('body.login', 
				'body.login div#login',
				'body.login div#login h1',
				'body.login div#login h1 a',
				'body.login div#login form#loginform',
				'body.login div#login form#loginform p',
				'body.login div#login form#loginform p label',
				'body.login div#login form#loginform input',
				'body.login div#login form#loginform input#user_login',
				'body.login div#login form#loginform input#user_pass',
				'body.login div#login form#loginform p.forgetmenot',
				'body.login div#login form#loginform p.forgetmenot input#rememberme',
				'body.login div#login form#loginform p.submit',
				'body.login div#login form#loginform p.submit input#wp-submit',
				'body.login div#login p#nav',
				'body.login div#login p#nav a',
				'body.login div#login p#backtoblog',
				'body.login div#login p#backtoblog a');
		
			$content = self::tag_it(__('To be able to use your own css it is important to know, what elements you actually can style on the login page. In the list below you find all neccessary selectors for your style sheet.', self::language_file), 'p');
			
			$content .= self::tag_it(self::list_it($elements, false, false, false, false), 'b');
			
			$content .= self::tag_it(__('In order to override the original styles, your selectors have to be more precise than the original ones. By using the ones in the list exactly how they are there, you should be fine.', self::language_file), 'p');
			
			self::column('1');
			
			self::sortable('side_top', self::help_box($content, __('CSS Help', self::language_file)));
			
			$donationtext = self::tag_it(__('If you like the plugin and find it useful, you might think of rewarding the dozens of hours of work that were spent creating it.', self::language_file), 'p');
			
			self::sortable('side_middle', self::donation_box($donationtext, __('Donations', self::language_file), '32XGSBKTQNNHA', 'http%3A%2F%2Fwasistlos.waldemarstoffel.com%2Fplugins-fur-wordpress%2Fa5-custom-login-page'));
			
			self::close_tab();
			
		endif;
		
		if ($active == 'html_tab') :
		
			self::open_tab();
			
			self::sortable('top', self::postbox(__('HTML additions', self::language_file), 'html-additions', 'clp_html'));
			
			if (WP_DEBUG === true) self::sortable('deep-down', self::debug_info(self::$options, __('Debug Info', self::language_file)));
			
			submit_button();
			
			self::close_tab();
			
		endif;
		
		if ($active == 'preview_tab') :
		
			self::open_tab();
			
			echo self::open_sortable('top');
			
			echo self::open_postbox(__('Preview'), 'preview-result');
			
			echo '<iframe src="'.wp_login_url().'" sandbox="" style="width: 100%; height: 650px;"></iframe>';
		
			echo self::close_postbox();
			
			if (WP_DEBUG === true) self::sortable('deep-down', self::debug_info(self::$options, __('Debug Info', self::language_file)));
			
			echo self::close_sortable();
			
			self::close_tab();
			
		endif;
		
	}
	
	/**
	 *
	 * Validate the options and handle the import - export stuff
	 *
	 */
		
	function validate($input) {
		
		if (isset($input['import']) && !empty($input['import'])) :
		
			$import_options = stripslashes($input['import']);
			
			$options = json_decode($import_options, true);
			
			if ($options['log'] != 'original A5 CLP file') :
			
				add_settings_error('clp_options', 'failed-on-import', __('This doesn&#39;t seem to be a valid settings file.', self::language_file), 'error');
				
				unset($options);
				
				return self::$options;
			
			else:
			
				unset($options['log']);
				
				$options['multisite'] = self::$options['multisite'];
				$options['version'] = self::$options['version'];
				
				add_settings_error('clp_options', 'success-on-import', __('Settings successfully imported.', self::language_file), 'updated');
				
				$clp_error = true;
				
				return $options;
			
			endif;
		
		else :
		
			self::$options['last_open'] = $input['tab'];
			
			switch($input['tab']) :
			
				case 'main_tab' :
				
					self::$options['logout_custom_message'] = trim($input['logout_custom_message']);
					self::$options['error_custom_message'] = trim($input['error_custom_message']);
					
					break;
					
				case 'advanced_tab' :
				
					self::$options['custom_redirect'] = ($input['custom_redirect']);
					self::$options['hide_nav'] = (@$input['hide_nav']) ? true : false;
					self::$options['hide_backlink'] = (@$input['hide_backlink']) ? true : false;
					self::$options['compress'] = (@$input['compress']) ? true : false;
					self::$options['inline'] = (@$input['inline']) ? true : false;
					self::$options['priority'] = @trim($input['priority']);
					
					break;
					
				case 'body_tab' :
					
					self::$options['body_background'] = trim($input['body_background']);
					self::$options['body_img_repeat'] = trim($input['body_img_repeat']);
					self::$options['body_img_pos'] = trim($input['body_img_pos']);
					self::$options['body_bg_color1'] = trim($input['body_bg_color1']);
					self::$options['body_bg_color2'] = trim($input['body_bg_color2']);
					self::$options['body_bg_size'] = trim($input['body_bg_size']);
					self::$options['button_bg_color1'] = trim($input['button_bg_color1']);
					self::$options['button_bg_color2'] = trim($input['button_bg_color2']);
					self::$options['button_text_color'] = trim($input['button_text_color']);
					self::$options['button_border_color'] = trim($input['button_border_color']);
					self::$options['btn_hover_bg_color1'] = trim($input['btn_hover_bg_color1']);
					self::$options['btn_hover_bg_color2'] = trim($input['btn_hover_bg_color2']);
					self::$options['btn_hover_text_color'] = trim($input['btn_hover_text_color']);
					self::$options['btn_hover_border_color'] = trim($input['btn_hover_border_color']);
					
					break;
					
				case 'logo_tab' :
					
					self::$options['logo'] = trim($input['logo']);
					self::$options['url'] = trim($input['url']);
					self::$options['title'] = trim($input['title']);
					self::$options['logo_width'] = trim($input['logo_width']);
					self::$options['logo_height'] = trim($input['logo_height']);
					self::$options['h1_width'] = trim($input['h1_width']);
					self::$options['h1_height'] = trim($input['h1_height']);
					self::$options['h1_margin'] = trim($input['h1_margin']);
					self::$options['h1_padding'] = trim($input['h1_padding']);
					self::$options['h1_corner'] = trim($input['h1_corner']);
					self::$options['h1_shadow_x'] = trim($input['h1_shadow_x']);
					self::$options['h1_shadow_y'] = trim($input['h1_shadow_y']);
					self::$options['h1_shadow_softness'] = trim($input['h1_shadow_softness']);
					self::$options['h1_shadow_color'] = trim($input['h1_shadow_color']);
					
					break;
					
				case 'logindiv_tab' :
				
					self::$options['logindiv_background'] = trim($input['logindiv_background']);
					self::$options['logindiv_img_repeat'] = trim($input['logindiv_img_repeat']);
					self::$options['logindiv_img_pos'] = trim($input['logindiv_img_pos']);
					self::$options['logindiv_bg_color1'] = trim($input['logindiv_bg_color1']);
					self::$options['logindiv_bg_color2'] = trim($input['logindiv_bg_color2']);
					self::$options['logindiv_text_color'] = trim($input['logindiv_text_color']);
					self::$options['logindiv_transparency'] = trim($input['logindiv_transparency']);
					self::$options['logindiv_border_style'] = trim($input['logindiv_border_style']);
					self::$options['logindiv_border_width'] = trim($input['logindiv_border_width']);
					self::$options['logindiv_border_color'] = trim($input['logindiv_border_color']);
					self::$options['logindiv_border_round'] = trim($input['logindiv_border_round']);
					self::$options['logindiv_shadow_x'] = trim($input['logindiv_shadow_x']);
					self::$options['logindiv_shadow_y'] = trim($input['logindiv_shadow_y']);
					self::$options['logindiv_shadow_softness'] = trim($input['logindiv_shadow_softness']);
					self::$options['logindiv_shadow_color'] = trim($input['logindiv_shadow_color']);
					self::$options['logindiv_left'] = trim($input['logindiv_left']);
					self::$options['logindiv_top'] = trim($input['logindiv_top']);
					self::$options['logindiv_width'] = trim($input['logindiv_width']);
					self::$options['logindiv_height'] = trim($input['logindiv_height']);
					self::$options['logindiv_padding'] = trim($input['logindiv_padding']);
					self::$options['logindiv_margin'] = trim($input['logindiv_margin']);
				
					break;
					
				case 'loginform_tab' :
				
					self::$options['loginform_background'] = trim($input['loginform_background']);
					self::$options['loginform_img_repeat'] = trim($input['loginform_img_repeat']);
					self::$options['loginform_img_pos'] = trim($input['loginform_img_pos']);
					self::$options['loginform_bg_color1'] = trim($input['loginform_bg_color1']);
					self::$options['loginform_bg_color2'] = trim($input['loginform_bg_color2']);
					self::$options['loginform_text_color'] = trim($input['loginform_text_color']);
					self::$options['loginform_transparency'] = trim($input['loginform_transparency']);				
					self::$options['loginform_border_style'] = trim($input['loginform_border_style']);
					self::$options['loginform_border_width'] = trim($input['loginform_border_width']);
					self::$options['loginform_border_color'] = trim($input['loginform_border_color']);
					self::$options['loginform_border_round'] = trim($input['loginform_border_round']);
					self::$options['loginform_margin'] = trim($input['loginform_margin']);
					self::$options['loginform_padding'] = trim($input['loginform_padding']);				
					self::$options['loginform_shadow_x'] = trim($input['loginform_shadow_x']);
					self::$options['loginform_shadow_y'] = trim($input['loginform_shadow_y']);
					self::$options['loginform_shadow_softness'] = trim($input['loginform_shadow_softness']);
					self::$options['loginform_shadow_color'] = trim($input['loginform_shadow_color']);
				
					break;
					
				case 'message_tab' :
				
					self::$options['loggedout_text_color'] = trim($input['loggedout_text_color']);
					self::$options['loggedout_bg_color'] = trim($input['loggedout_bg_color']);
					self::$options['loggedout_border_color'] = trim($input['loggedout_border_color']);
					self::$options['loggedout_transparency'] = trim($input['loggedout_transparency']);
					self::$options['error_text_color'] = trim($input['error_text_color']);
					self::$options['error_bg_color'] = trim($input['error_bg_color']);
					self::$options['error_border_color'] = trim($input['error_border_color']);
					self::$options['error_transparency'] = trim($input['error_transparency']);
					self::$options['input_text_color'] = trim($input['input_text_color']);
					self::$options['input_bg_color'] = trim($input['input_bg_color']);
					self::$options['input_border_color'] = trim($input['input_border_color']);
				
					break;
					
				case 'link_tab' :
				
					self::$options['link_text_color'] = trim($input['link_text_color']);
					self::$options['link_textdecoration'] = trim($input['link_textdecoration']);
					self::$options['link_shadow_x'] = trim($input['link_shadow_x']);
					self::$options['link_shadow_y'] = trim($input['link_shadow_y']);
					self::$options['link_shadow_softness'] = trim($input['link_shadow_softness']);
					self::$options['link_shadow_color'] = trim($input['link_shadow_color']);
					self::$options['hover_text_color'] = trim($input['hover_text_color']);
					self::$options['hover_textdecoration'] = trim($input['hover_textdecoration']);
					self::$options['hover_shadow_x'] = trim($input['hover_shadow_x']);
					self::$options['hover_shadow_y'] = trim($input['hover_shadow_y']);
					self::$options['hover_shadow_softness'] = trim($input['hover_shadow_softness']);
					self::$options['hover_shadow_color'] = trim($input['hover_shadow_color']);
					self::$options['link_size'] = trim($input['link_size']);
				
					break;
					
				case 'css_tab' :
				
					self::$options['css'] = trim($input['css']);
					self::$options['override'] = (@$input['override']) ? true : NULL;
					self::$options['svg'] = trim($input['svg']);
				
					break;
					
				case 'html_tab' :
				
					self::$options['login_message'] = trim($input['login_message']);
					self::$options['login_form'] = trim($input['login_form']);
					self::$options['login_footer'] = trim($input['login_footer']);
				
					break;
			
			endswitch;
			
			if (is_plugin_active_for_network(CLP_BASE)) add_settings_error('clp_options', 'settings_updated', __('Settings saved.'), 'updated');
			
			return self::$options;
			
		endif;
	
	}

} // end of class

?>