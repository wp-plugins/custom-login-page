<?php

/**
 *
 * Class A5 Dynamic Files
 *
 * @ A5 Plugin Framework
 * Version: 0.99 beta
 *
 * Handels styles or javascript in either dynamical files or inline
 * 
 * @ parameter $place = 'wp' selects where to attach the file or print inline (wp, admin, login)
 * @ optional $type = 'css' the filetype that should be generated (css, js, export)
 * @ optional [(array) $hooks], [(bool) $inline], [(array) $args] for exporting only
 *
 */

class A5_DynamicFiles {
	
	const version = '0.99 beta';
	
	public static $styles = '';
	
	public static $scripts = '';
	
	private static $type, $hooks;
	
	function A5_DynamicFiles($place = 'wp', $type = false, $hooks = false, $inline = false) {
		
		self::$type = ($type) ? $type : 'css';
		
		if ($hooks === false) :
		
			self::$hooks = $hooks;
			
		else :
		
			foreach ($hooks as $hook) self::$hooks[] = $hook;
		
		endif;
		
		if (true === $inline) :
		
			add_action($place.'_head', array(&$this, 'print_inline'));
		
		else :
		
			add_action('init', array (&$this, 'add_rewrite'));
			add_action('template_redirect', array (&$this, 'css_template'));
			
			add_action ($place.'_enqueue_scripts', array (&$this, $place.'_enqueue_scripts'));
			
		endif;

	}
	
	// preparing for the virtual file
	
	function add_rewrite() {
		
		global $wp;
		$wp->add_query_var('A5_file');
	
	}
	
	function css_template() {
		
		$A5_file = get_query_var('A5_file');
		
		if ($A5_file == 'wp_css' || $A5_file == 'admin_css' || $A5_file == 'login_css') :
		   
			header('Content-type: text/css');
			
			echo $this->write_dss();
			
			exit;
		
		endif;
		
		if ($A5_file == 'wp_js' || $A5_file == 'admin_js' || $A5_file == 'login_js') :
		
			header('Content-type: text/javascript');
			
			echo $this->write_djs();
			
			exit;
		
		endif;
		
		if ($A5_file == 'export') :
		
			extract($args);
			
			header('Content-Description: File Transfer');
			header('Content-Disposition: attachment; filename="'.$name.'-' . str_replace('.','-', $_SERVER['SERVER_NAME']) . '-' . date('Y') . date('m') . date('d') . '.txt"');
			header('Content-Type: text/plain; charset=utf-8');
			
			echo json_encode($options);
			
			exit;
		
		endif;
	
	}
	
	// getting css to frontend
	
	function wp_enqueue_scripts () {
		
		$A5_css_file=get_bloginfo('url').'/?A5_file=wp_css';
			
		wp_register_style('A5-framework', $A5_css_file, false, self::version, 'all');
		wp_enqueue_style('A5-framework');
		
	}
	
	// getting css to backend
	
	function admin_enqueue_scripts ($hook) {
		
		if (self::$hooks !== false) :
		
			if (!in_array($hook, self::$hooks)) return;
			
		endif;
		
		$A5_css_file=get_bloginfo('url').'/?A5_file=admin_css';
			
		wp_register_style('A5-framework', $A5_css_file, false, self::version, 'all');
		wp_enqueue_style('A5-framework');
		
	}
	
	// getting css to login screen
	
	function login_enqueue_scripts () {
		
		$A5_css_file=get_bloginfo('url').'/?A5_file=login_css';
			
		wp_register_style('A5-framework', $A5_css_file, false, self::version, 'all');
		wp_enqueue_style('A5-framework');
		
	}
	
	// writing the styles to a dynamic file
	
	function write_dss() {
	
		$eol = "\r\n";
		
		$css_text = '@charset "UTF-8";'.$eol.'/* CSS Document createtd by the A5 Plugin Framework */'.$eol;
		
		$css_text .= self::$styles;
		
		echo $css_text;	
		
	}
	
	// writing the javascript to a dynamic file
	
	function write_djs() {
	
		$eol = "\r\n";
		
		$js_text = '// JavaScript Document createtd by the A5 Plugin Framework'.$eol;
		
		$js_text .= self::$scripts;
		
		echo $js_text;	
		
	}
	
	// writing the styles inline
	
	function print_inline() {
		
		$eol = "\r\n";
		
		$inline_text = ('css' == self::$type) ? '<style>'.$eol.'/* CSS Styles created by the A5 Plugin Framework */'.$eol : '<script type="text/javascript">'.$eol;
		
		$inline_text .= self::$styles;
		
		$inline_text .= ('css' == self::$type) ? $eol.'</style>'.$eol : $eol.'</script>'.$eol;
		
		echo $inline_text;	
		
	}
	
} // A5_Dynamic CSS

?>