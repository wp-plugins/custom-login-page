<?php

/**
 *
 * Class Ads Easy Widget
 *
 * @ Ads Easy
 *
 * building the actual widget
 *
 */
class Custom_Login_Widget extends WP_Widget {
	
	const language_file = 'custom-login-page';
	
	private static $options;
	
	function __construct() {
			
		$widget_opts = array( 'description' => __('With this widget you can add a customized login form to your sidebar.', self::language_file) );
		$control_opts = array ( 'width' => 400 );
		
		parent::WP_Widget(false, $name = 'A5 Custom Login Widget', $widget_opts, $control_opts);
		
		if (!function_exists('is_plugin_active_for_network')) require_once(ABSPATH.'/wp-admin/includes/plugin.php');
		
		self::$options = (is_plugin_active_for_network(CLP_BASE)) ? get_site_option('clp_widget_options') : get_option('clp_widget_options');
		
		if (!empty(self::$options['login_form_top'])) add_filter('login_form_top', array($this, 'print_login_form_top'));
		if (!empty(self::$options['login_form'])) add_filter('login_form_middle', array($this, 'print_login_form_middle'));
		if (!empty(self::$options['login_form_bottom'])) add_filter('login_form_bottom', array($this, 'print_login_form_bottom'));
	
	}
	
	function print_login_form_top() {
		
		return self::$options['login_form_top'];
	
	}
	
	function print_login_form_middle() {
		
		return self::$options['login_form'];
	
	}
	
	function print_login_form_bottom() {
		
		return self::$options['login_form_bottom'];
	
	}

	function form($instance) {
		
		$defaults = array(
			'title' => NULL,
			'homepage' => 1,
			'frontpage' => false,
			'page' => false,
			'category' => 1,
			'single' => false,
			'date' => false,
			'tag' => false,
			'attachment' => false,
			'taxonomy' => false,
			'author' => false,
			'search' => false,
			'not_found' => false,
			'not_show_logged_in' => false
		);
		
		$instance = wp_parse_args( (array) $instance, $defaults );
		
		$title = esc_attr($instance['title']);
		$homepage = $instance['homepage'];
		$frontpage = $instance['frontpage'];
		$page = $instance['page'];
		$category = $instance['category'];
		$single = $instance['single'];
		$date = $instance['date'];
		$tag = $instance['tag'];
		$attachment = $instance['attachment'];
		$taxonomy = $instance['taxonomy'];
		$author = $instance['author'];
		$search = $instance['search'];
		$not_found = $instance['not_found'];
		$not_show_logged_in = $instance['not_show_logged_in'];
		
		$base_id = 'widget-'.$this->id_base.'-'.$this->number.'-';
		$base_name = 'widget-'.$this->id_base.'['.$this->number.']';
		
		$options = array (
			array($base_id.'homepage', $base_name.'[homepage]', $homepage, __('Homepage', self::language_file)),
			array($base_id.'frontpage', $base_name.'[frontpage]', $frontpage, __('Frontpage (e.g. a static page as homepage)', self::language_file)),
			array($base_id.'page', $base_name.'[page]', $page, __('&#34;Page&#34; pages', self::language_file)),
			array($base_id.'category', $base_name.'[category]', $category, __('Category pages', self::language_file)),
			array($base_id.'single', $base_name.'[single]', $single, __('Single post pages', self::language_file)),
			array($base_id.'date', $base_name.'[date]', $date, __('Archive pages', self::language_file)),
			array($base_id.'tag', $base_name.'[tag]', $tag, __('Tag pages', self::language_file)),
			array($base_id.'attachment', $base_name.'[attachment]', $attachment, __('Attachments', self::language_file)),
			array($base_id.'taxonomy', $base_name.'[taxonomy]', $taxonomy, __('Custom Taxonomy pages (only available, if having a plugin)', self::language_file)),
			array($base_id.'author', $base_name.'[author]', $author, __('Author pages', self::language_file)),
			array($base_id.'search', $base_name.'[search]', $search, __('Search Results', self::language_file)),
			array($base_id.'not_found', $base_name.'[not_found]', $not_found, __('&#34;Not Found&#34;', self::language_file))
		);
		
		$checkall = array($base_id.'checkall', $base_name.'[checkall]', __('Check all', self::language_file));
		
		a5_text_field($base_id.'title', $base_name.'[title]', $title, __('Title:', self::language_file), array('space' => true, 'class' => 'widefat'));
		a5_checkbox($base_id.'not_show_logged_in', $base_name.'[not_show_logged_in]', $not_show_logged_in, __('Don&#39;t show widget to logged in users.', self::language_file), array('space' => true));
		a5_checkgroup(false, false, $options, __('Check, where you want to show the widget. By default, it is showing on the homepage and the category pages:', self::language_file), $checkall);
		
	}
	
	function update($new_instance, $old_instance) {
		 
		 $instance = $old_instance;
		 
		 $instance['title'] = strip_tags($new_instance['title']);
		 $instance['homepage'] = $new_instance['homepage'];
		 $instance['frontpage'] = $new_instance['frontpage'];
		 $instance['page'] = $new_instance['page'];
		 $instance['category'] = $new_instance['category'];
		 $instance['single'] = $new_instance['single'];
		 $instance['date'] = $new_instance['date']; 
		 $instance['tag'] = $new_instance['tag'];
		 $instance['attachment'] = $new_instance['attachment'];
		 $instance['taxonomy'] = $new_instance['taxonomy'];
		 $instance['author'] = $new_instance['author'];
		 $instance['search'] = $new_instance['search'];
		 $instance['not_found'] = $new_instance['not_found'];
		 $instance['not_show_logged_in'] = $new_instance['not_show_logged_in'];
		 
		 return $instance;
	}
	 
	function widget($args, $instance) {
		
		if (is_user_logged_in() && $instance['not_show_logged_in']) :
		
			echo '<!-- the custom login widget doesn\'t show to logged in users -->';
			
		else :
		
			// get the type of page, we're actually on
				
			if (is_front_page()) $clp_pagetype='frontpage';
			if (is_home()) $clp_pagetype='homepage';
			if (is_page()) $clp_pagetype='page';
			if (is_category()) $clp_pagetype='category';
			if (is_single()) $clp_pagetype='single';
			if (is_date()) $clp_pagetype='date';
			if (is_tag()) $clp_pagetype='tag';
			if (is_attachment()) $clp_pagetype='attachment';
			if (is_tax()) $clp_pagetype='taxonomy';
			if (is_author()) $clp_pagetype='author';
			if (is_search()) $clp_pagetype='search';
			if (is_404()) $clp_pagetype='not_found';
			
			// display only, if said so in the settings of the widget
			
			if (isset ($clp_pagetype) && $instance[$clp_pagetype]) :
				
				// the widget is displayed
				
				extract( $args );
				
				$title = apply_filters('widget_title', $instance['title']);	
				
				echo $before_widget;
				
				if ( $title ) echo $before_title . $title . $after_title;
				
				if (!is_user_logged_in()) :
					
					$formargs['redirect'] = (isset(self::$options['redirect']) && !empty(self::$options['redirect'])) ? self::$options['redirect'] : home_url($_SERVER['REQUEST_URI']);
					$formargs['form_id'] = (isset(self::$options['form_id']) && !empty(self::$options['form_id'])) ? self::$options['form_id'] : 'loginform';
					$formargs['label_username'] = (isset(self::$options['label_username']) && !empty(self::$options['label_username'])) ? self::$options['label_username'] : __('Username');
					$formargs['label_password'] = (isset(self::$options['label_password']) && !empty(self::$options['label_password'])) ? self::$options['label_password'] : __('Password');
					$formargs['label_remember'] = (isset(self::$options['label_remember']) && !empty(self::$options['label_remember'])) ? self::$options['label_remember'] : __('Remember Me');
					$formargs['label_log_in'] = (isset(self::$options['label_log_in']) && !empty(self::$options['label_log_in'])) ? self::$options['label_log_in'] : __('Log In');
					$formargs['id_username'] = (isset(self::$options['id_username']) && !empty(self::$options['id_username'])) ? self::$options['id_username'] : 'user_login';
					$formargs['id_password'] = (isset(self::$options['id_password']) && !empty(self::$options['id_password'])) ? self::$options['id_password'] : 'user_pass';
					$formargs['id_remember'] = (isset(self::$options['id_remember']) && !empty(self::$options['id_remember'])) ? self::$options['id_remember'] : 'rememberme';
					$formargs['id_submit'] = (isset(self::$options['id_submit']) && !empty(self::$options['id_submit'])) ? self::$options['id_submit'] : 'wp-submit';
					$formargs['remember'] = (isset(self::$options['hide_remember']) && !empty(self::$options['hide_remember'])) ? false : true;
					$formargs['value_username'] = (isset(self::$options['value_username']) && !empty(self::$options['value_username'])) ? self::$options['value_username'] : NULL;
					$formargs['value_remember'] = (isset(self::$options['value_remember']) && !empty(self::$options['value_remember'])) ? true : false;
					
					if (isset(self::$options['title']) && !empty(self::$options['title'])) $title_tag = ' title="'.self::$options['title'].'"';
					
					if (isset(self::$options['logo']) && !empty(self::$options['logo'])) $img_tag = '<img src="'.self::$options['logo'].'"'.$title_tag.' />';
					
					if (isset($img_tag)) echo (isset(self::$options['url']) && !empty(self::$options['url'])) ? '<a href="'.self::$options['url'].'"'.$title_tag.'>'.$img_tag.'</a>' : $img_tag;
					
					if (isset(self::$options['login_message']) && !empty(self::$options['login_message'])) echo self::$options['login_message'];
					
					wp_login_form($formargs);
					
					if (isset(self::$options['login_footer']) && !empty(self::$options['login_footer'])) : echo self::$options['login_footer']; endif;
					
				else :
				
					wp_loginout( home_url() );
					
					echo ' | ';
					
					wp_register('', '');
				
				endif;
				
				echo $after_widget;
			
			endif;
		
		endif;
	
	} // function widget

} // class widget

add_action('widgets_init', create_function('', 'return register_widget("Custom_Login_Widget");'));

?>