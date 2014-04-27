<?php
/*
Plugin Name: A5 Custom Login Page
Description: Just customize your login page (or that of your community etc.) by giving the WP login page a different look, with your own logo and special colours and styles.
Version: 2.1
Author: Waldemar Stoffel
Author URI: http://www.waldemarstoffel.com
Plugin URI: http://wasistlos.waldemarstoffel.com/plugins-fur-wordpress/a5-custom-login-page
License: GPL3
Text Domain: custom-login-page
*/

/*  Copyright 2011 - 2014 Waldemar Stoffel  (email: stoffel@atelier-fuenf.de)

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
 * Thx to Branco Radenovich - http://webhostinggeeks.com/blog
 * for the translation into Slovak
 *
 * ------------------------------------------------------
 */

/* Stop direct call */

defined('ABSPATH') OR exit;

if (!defined('CLP_PATH')) define( 'CLP_PATH', plugin_dir_path(__FILE__) );
if (!defined('CLP_BASE')) define( 'CLP_BASE', plugin_basename(__FILE__) );

# loading the framework
if (!class_exists('A5_FormField')) require_once CLP_PATH.'class-lib/A5_FormFieldClass.php';
if (!class_exists('A5_OptionPage')) require_once CLP_PATH.'class-lib/A5_OptionPageClass.php';
if (!class_exists('A5_DynamicFiles')) require_once CLP_PATH.'class-lib/A5_DynamicFileClass.php';

#loading plugin specific classes
if (!class_exists('CLP_Admin')) require_once CLP_PATH.'class-lib/CLP_AdminClass.php';
if (!class_exists('CLP_DynamicCSS')) require_once CLP_PATH.'class-lib/CLP_DynamicCSSClass.php';

class A5_CustomLoginPage {
	
	private static $options;
	
	const language_file = 'custom-login-page', version = '1.9.1';
	
	function __construct(){
		
		register_activation_hook(__FILE__, array(&$this, '_install')); 
		register_deactivation_hook(__FILE__, array(&$this, '_uninstall'));	
		
		add_filter('plugin_row_meta', array(&$this, 'register_links'), 10, 2);
		add_filter('plugin_action_links', array(&$this, 'plugin_action_links'), 10, 2);
		
		if (is_multisite()) :
		
			$plugins = get_site_option('active_sitewide_plugins');
			
			if (isset($plugins[CLP_BASE])) :
				
				self::$options = get_site_option('clp_options');
				
				if (self::$options['version'] != self::version) :
				
					self::$options['version'] = self::version;
					
					self::$options['multisite'] = true;
					
					update_site_option('clp_options', self::$options);
					
				endif;
				
			else:
			
				$plugins = get_option('active_plugins');
			
				if (in_array(CLP_BASE, $plugins)) :
				
					self::$options = get_option('clp_options');
					
					if (self::$options['version'] != self::version) :
						
						self::$options['version'] = self::version;
						
						self::$options['multisite'] = false;
						
						update_option('clp_options', self::$options);
						
					endif;
					
				endif;
				
			endif;
			
		else:
		
			$plugins = get_option('active_plugins');
			
				if (in_array(CLP_BASE, $plugins)) :
			
				self::$options = get_option('clp_options');
				
				if (self::$options['version'] != self::version) :
					
					self::$options['version'] = self::version;
					
					self::$options['multisite'] = false;
					
					update_option('clp_options', self::$options);
					
				endif;
				
			endif;
		
		endif;
		
		if (!empty(self::$options['url'])) add_filter('login_headerurl', array(&$this, 'clp_headerurl'));
		if (!empty(self::$options['title'])) add_filter('login_headertitle', array(&$this, 'clp_headertitle'));
		if (!empty(self::$options['error_custom_message'])) add_filter('login_errors', array(&$this, 'clp_custom_error'));
		if (!empty(self::$options['logout_custom_message'])) add_filter('login_messages', array(&$this, 'clp_custom_logout'));
		if (!empty(self::$options['svg'])) add_filter('login_message', array(&$this, 'clp_print_svg'));
		
		/**
		 *
		 * Importing language file
		 *
		 */
		load_plugin_textdomain(self::language_file, false , basename(dirname(__FILE__)).'/languages');
		
		// redirecting to the export file
		
		add_action('init', array (&$this, 'add_rewrite'));
		add_action('template_redirect', array (&$this, 'export_template'));
		
		$CLP_DynamicCSS = new CLP_DynamicCSS(self::$options['multisite']);
		$CLP_Admin = new CLP_Admin(self::$options['multisite']);
		
	}	
	
	/**
	 *
	 * Adds links to the plugin page
	 *
	 */
	function register_links($links, $file) {
		
		if ($file == CLP_BASE) :
		
			$links[] = '<a href="http://wordpress.org/extend/plugins/custom-login-page/faq/" target="_blank">'.__('FAQ', self::language_file).'</a>';
			$links[] = '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=32XGSBKTQNNHA" target="_blank">'.__('Donate', self::language_file).'</a>';
			
		endif;
		
		return $links;
	
	}
	
	function plugin_action_links( $links, $file ) {
		
		if ($file == CLP_BASE) array_unshift($links, '<a href="'.admin_url( 'themes.php?page=clp-settings' ).'">'.__('Settings', self::language_file).'</a>');
	
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
	 * Changes the Logout Message
	 *
	 */
	function clp_custom_logout() {
	
		return self::$options['logout_custom_message'];
		
	}
	
	/**
	 *
	 * Printing SVG above the login form
	 *
	 */
	function clp_print_svg() {
		
			return self::$options['svg'];
	}

	/**
	 *
	 * Setting version on activation
	 *
	 */
	function _install() {
		
		$screen = get_current_screen();
		
		$default = array(
			'version' => self::version,
			'multisite' => false
		);
		
		if (is_multisite() && $screen->is_network) :
		
			$default['multisite'] = true;
		
			add_site_option('clp_options', $default);
			
		else:
		
			add_option('clp_options', $default);
			
		endif;
	
	}
	
	/**
	 *
	 * Cleaning on deactivation
	 *
	 */
	function _uninstall() {
		
		$screen = get_current_screen();
		
		if (is_multisite() && $screen->is_network) :
		
			delete_site_option('clp_options');
			
		else:
		
			delete_option('clp_options');
			
		endif;
		
	}

	/**
	 *
	 * redirect to export file
	 *
	 */
	function add_rewrite() {
	
		global $wp;
		
		$wp->add_query_var('clpfile');
	
	}
	
	function export_template() {
		
		$clpfile = get_query_var('clpfile');
		
		if ('export' == $clpfile) :
		
			self::$options['log'] = 'original A5 CLP file';
			
			unset(self::$options['multisite']);
			
			header('Content-Description: File Transfer');
			header('Content-Disposition: attachment; filename="a5-clp-' . str_replace('.','-', $_SERVER['SERVER_NAME']) . '-' . date('Y') . date('m') . date('d') . '.txt"');
			header('Content-Type: text/plain; charset=utf-8');
			
			echo json_encode(self::$options);
			
			exit;
		
		endif;
	}
	
} // end of class

$A5_CustomLoginPage = new A5_CustomLoginPage;

?>