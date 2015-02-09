<?php

/**
 *
 * Class CLP Dynamic CSS
 *
 * Extending A5 Dynamic Files
 *
 * Presses the dynamical CSS of the A5 Custom Login Plugin into virtual style sheets
 *
 */

class CLP_DynamicCSS extends A5_DynamicFiles {
	
	private static $options, $widget;
	
	public static $widget_css, $custom_css;
	
	function __construct($multisite) {
		
		// getting options and preparing the stylesheets
		
		self::$options = ($multisite) ? get_site_option('clp_options') : get_option('clp_options');
		
		self::$widget = ($multisite) ? get_site_option('clp_widget_options') : get_option('clp_widget_options');
		
		if (!isset(self::$options['inline'])) self::$options['inline'] = false;
		
		if (!isset(self::$options['compress'])) self::$options['compress'] = false;
		
		if (!isset(self::$options['priority'])) self::$options['priority'] = false;
		
		$eol = (self::$options['compress']) ? '' : "\r\n";
		$tab = (self::$options['compress']) ? '' : "\t";
		
		if (!isset(self::$options['h1_shadow_inset'])) self::$options['h1_shadow_inset'] = '';
		if (!isset(self::$options['logindiv_shadow_inset'])) self::$options['logindiv_shadow_inset'] = '';
		if (!isset(self::$options['loginform_shadow_inset'])) self::$options['loginform_shadow_inset'] = '';
		if (!isset(self::$options['input_shadow_inset'])) self::$options['input_shadow_inset'] = '';
		if (!isset(self::$widget['container_shadow_inset'])) self::$widget['container_shadow_inset'] = '';
		if (!isset(self::$widget['h1_shadow_inset'])) self::$widget['h1_shadow_inset'] = '';
		if (!isset(self::$widget['loginform_shadow_inset'])) self::$widget['loginform_shadow_inset'] = '';
		if (!isset(self::$widget['input_shadow_inset'])) self::$widget['input_shadow_inset'] = '';
		
		// part of the Custom Login Page
		
		parent::A5_DynamicFiles('login', 'css', 'all', false, self::$options['inline'], self::$options['priority']);
		
		if (isset(self::$options['css']) && !empty(self::$options['css']) && isset(self::$options['override'])) :
		
			self::$custom_css = (!self::$options['compress']) ? $eol.'/* CSS portion of the A5 Custom Login Page */'.$eol.$eol : '';
		
			self::$custom_css .= self::$options['css'];
		
		else :
		
			# collecting variables
			
			# body
			
			$body_style = '';
				
			if (!empty(self::$options['body_background'])) $body_style .= $eol.$tab.'background-image: url('.self::$options['body_background'].');';
			if (!empty(self::$options['body_img_repeat'])) $body_style .= $eol.$tab.'background-repeat: '.self::$options['body_img_repeat'].';';
			if (!empty(self::$options['body_img_pos'])) $body_style .= $eol.$tab.'background-position: '.self::$options['body_img_pos'].';';
			if (!empty(self::$options['body_bg_size'])) :
			
				$body_style .= $eol.$tab.'-webkit-background-size: '.self::$options['body_bg_size'].';';
				$body_style .= $eol.$tab.'-moz-background-size: '.self::$options['body_bg_size'].';';
				$body_style .= $eol.$tab.'-o-background-size: '.self::$options['body_bg_size'].';';
				$body_style .= $eol.$tab.'background-size: '.self::$options['body_bg_size'].';';
			
			endif;
			
			if (!empty(self::$options['body_bg_color1'])) : 
			
				$body_style .= $eol.$tab.'background-color: '.self::$options['body_bg_color1'].';';
				
			endif;
				
			if (!empty(self::$options['body_bg_color2'])) :
				
				$body_style .= $eol.$tab.'background-image: -webkit-gradient(linear, left top, left bottom, from('.self::$options['body_bg_color1'].'), to('.self::$options['body_bg_color2'].'));';
				$body_style .= $eol.$tab.'background-image: -webkit-linear-gradient(top, '.self::$options['body_bg_color1'].', '.self::$options['body_bg_color2'].');';
				$body_style .= $eol.$tab.'background-image: -moz-linear-gradient(top, '.self::$options['body_bg_color1'].', '.self::$options['body_bg_color2'].');';
				$body_style .= $eol.$tab.'background-image: -ms-linear-gradient(top, '.self::$options['body_bg_color1'].', '.self::$options['body_bg_color2'].');';
				$body_style .= $eol.$tab.'background-image: -o-linear-gradient(top, '.self::$options['body_bg_color1'].', '.self::$options['body_bg_color2'].');';
				$body_style .= $eol.$tab.'background-image: -linear-gradient(top, '.self::$options['body_bg_color1'].', '.self::$options['body_bg_color2'].');';
				
				$body_style .= $eol.$tab.'-webkit-background-size: cover;';
				$body_style .= $eol.$tab.'-moz-background-size: cover;';
				$body_style .= $eol.$tab.'-o-background-size: cover;';
				$body_style .= $eol.$tab.'background-size: cover;';
				
			endif;
			
			# 'body.login div#login h1 a
			
			$h1_style = '';
			
			if (!empty(self::$options['hide_logo'])) $h1_style .= $eol.$tab.'display: none;';
			
			if (!empty(self::$options['logo'])) :
			
				$bg_width = (!empty(self::$options['h1_width'])) ? self::$options['h1_width'] : '84';
				$bg_height = (!empty(self::$options['h1_height'])) ? self::$options['h1_height'].'px' : 'auto';
				$h1_width = (!empty(self::$options['logo_width'])) ? self::$options['logo_width'] : '84';
				$h1_height = (!empty(self::$options['logo_height'])) ? self::$options['logo_height'] : '84';
			
				$h1_style .= $eol.$tab.'background-image: url('.self::$options['logo'].');';
				$h1_style .= $eol.$tab.'background-position: center top;';
				$h1_style .= $eol.$tab.'background-repeat: no-repeat;';
				$h1_style .= $eol.$tab.'background-size: '.$bg_width.'px '.$bg_height.';';
				$h1_style .= $eol.$tab.'width: '.$h1_width.'px;';
				$h1_style .= $eol.$tab.'height: '.$h1_height.'px;';
				$h1_style .= $eol.$tab.'text-indent: -9999px;';
				$h1_style .= $eol.$tab.'display: block;';
				
				if (!empty(self::$options['h1_margin'])) $h1_style .= $eol.$tab.'margin: '.self::$options['h1_margin'].';';
				
				if (!empty(self::$options['h1_padding'])) $h1_style .= $eol.$tab.'padding: '.self::$options['h1_padding'].';';
				
				if (!empty(self::$options['h1_corner'])) :
				
					$h1_style .= $eol.$tab.'-webkit-border-radius: '.self::$options['h1_corner'].'px;';
					$h1_style .= $eol.$tab.'-moz-border-radius: '.self::$options['h1_corner'].'px;';
					$h1_style .= $eol.$tab.'border-radius: '.self::$options['h1_corner'].'px;';
					
				endif;
				
				if (!empty(self::$options['h1_shadow_x']) || self::$options['h1_shadow_x']=='0') :
					
					$h1_style .= $eol.$tab.'-webkit-box-shadow: '.self::$options['h1_shadow_x'].'px '.self::$options['h1_shadow_y'].'px '.self::$options['h1_shadow_softness'].'px '.self::$options['h1_shadow_color'].self::$options['h1_shadow_inset'].';';
					$h1_style .= $eol.$tab.'-moz-box-shadow: '.self::$options['h1_shadow_x'].'px '.self::$options['h1_shadow_y'].'px '.self::$options['h1_shadow_softness'].'px '.self::$options['h1_shadow_color'].self::$options['h1_shadow_inset'].';';
					$h1_style .= $eol.$tab.'box-shadow: '.self::$options['h1_shadow_x'].'px '.self::$options['h1_shadow_y'].'px '.self::$options['h1_shadow_softness'].'px '.self::$options['h1_shadow_color'].self::$options['h1_shadow_inset'].';';
					
				endif;
				
			endif;	
			
			# #login
			
			$logindiv_style = '';
			$label_style = '';
			
			if (isset(self::$options['logindiv_top']) && isset(self::$options['logindiv_left'])) :
			
				if (!empty(self::$options['logindiv_top']) || !empty(self::$options['logindiv_left']) || self::$options['logindiv_top'] == '0' || self::$options['logindiv_left']=='0') $logindiv_style .= $eol.$tab.'position: relative;';
				if (!empty(self::$options['logindiv_top']) || self::$options['logindiv_top'] == '0') $logindiv_style .= $eol.$tab.'top: '.self::$options['logindiv_top'].'px;';
				if (!empty(self::$options['logindiv_left']) || self::$options['logindiv_left'] == '0') $logindiv_style .= $eol.$tab.'left: '.self::$options['logindiv_left'].'px;';
				
			endif;
				
			if (!empty(self::$options['logindiv_bg_color1'])) $logindiv_style .= $eol.$tab.'background-color: '.self::$options['logindiv_bg_color1'].';';
			if (!empty(self::$options['logindiv_bg_color2'])) :
				
				$logindiv_style .= $eol.$tab.'background-clip: padding-box;';
				$logindiv_style .= $eol.$tab.'background-image: -webkit-gradient(linear, left top, left bottom, from('.self::$options['logindiv_bg_color1'].'), to('.self::$options['logindiv_bg_color2'].'));';
				$logindiv_style .= $eol.$tab.'background-image: -webkit-linear-gradient(top, '.self::$options['logindiv_bg_color1'].', '.self::$options['logindiv_bg_color2'].');';
				$logindiv_style .= $eol.$tab.'background-image: -moz-linear-gradient(top, '.self::$options['logindiv_bg_color1'].', '.self::$options['logindiv_bg_color2'].');';
				$logindiv_style .= $eol.$tab.'background-image: -ms-linear-gradient(top, '.self::$options['logindiv_bg_color1'].', '.self::$options['logindiv_bg_color2'].');';
				$logindiv_style .= $eol.$tab.'background-image: -o-linear-gradient(top, '.self::$options['logindiv_bg_color1'].', '.self::$options['logindiv_bg_color2'].');';
				$logindiv_style .= $eol.$tab.'background-image: -linear-gradient(top, '.self::$options['logindiv_bg_color1'].', '.self::$options['logindiv_bg_color2'].');';
				
			endif;
			if (!empty(self::$options['logindiv_transparency'])) :
				$logindiv_style .= $eol.$tab.'-ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity='.self::$options['logindiv_transparency'].')";';
				$logindiv_style .= $eol.$tab.'filter: alpha(Opacity='.self::$options['logindiv_transparency'].');';
				$logindiv_style .= $eol.$tab.'-moz-opacity: '.(self::$options['logindiv_transparency']/100).';';
				$logindiv_style .= $eol.$tab.'-khtml-opacity: '.(self::$options['logindiv_transparency']/100).';';
				$logindiv_style .= $eol.$tab.'opacity: '.(self::$options['logindiv_transparency']/100).';';
			endif;
			if (!empty(self::$options['logindiv_background'])) $logindiv_style .= $eol.$tab.'background-image: url('.self::$options['logindiv_background'].');';
			if (!empty(self::$options['logindiv_img_repeat'])) $logindiv_style .= $eol.$tab.'background-repeat: '.self::$options['logindiv_img_repeat'].';';
			if (!empty(self::$options['logindiv_img_pos'])) $logindiv_style .= $eol.$tab.'background-position: '.self::$options['logindiv_img_pos'].';';
			if (!empty(self::$options['logindiv_bg_size'])) :
			
				$logindiv_style .= $eol.$tab.'-webkit-background-size: '.self::$options['logindiv_bg_size'].';';
				$logindiv_style .= $eol.$tab.'-moz-background-size: '.self::$options['logindiv_bg_size'].';';
				$logindiv_style .= $eol.$tab.'-o-background-size: '.self::$options['logindiv_bg_size'].';';
				$logindiv_style .= $eol.$tab.'background-size: '.self::$options['logindiv_bg_size'].';';
			
			endif;
			if (!empty(self::$options['logindiv_border_style'])) $logindiv_style .= $eol.$tab.'border: '.self::$options['logindiv_border_style'].' '.self::$options['logindiv_border_width'].'px '.self::$options['logindiv_border_color'].';';
			if (!empty(self::$options['logindiv_border_round'])) :
				
				$logindiv_style .= $eol.$tab.'-webkit-border-radius: '.self::$options['logindiv_border_round'].'px;';
				$logindiv_style .= $eol.$tab.'-moz-border-radius: '.self::$options['logindiv_border_round'].'px;';
				$logindiv_style .= $eol.$tab.'border-radius: '.self::$options['logindiv_border_round'].'px;';
				
			endif;
			if (isset(self::$options['logindiv_shadow_x']) && (!empty(self::$options['logindiv_shadow_x']) || self::$options['logindiv_shadow_x']=='0')) :
				
				$logindiv_style .= $eol.$tab.'-webkit-box-shadow: '.self::$options['logindiv_shadow_x'].'px '.self::$options['logindiv_shadow_y'].'px '.self::$options['logindiv_shadow_softness'].'px '.self::$options['logindiv_shadow_color'].self::$options['logindiv_shadow_inset'].';';
				$logindiv_style .= $eol.$tab.'-moz-box-shadow: '.self::$options['logindiv_shadow_x'].'px '.self::$options['logindiv_shadow_y'].'px '.self::$options['logindiv_shadow_softness'].'px '.self::$options['logindiv_shadow_color'].self::$options['logindiv_shadow_inset'].';';
				$logindiv_style .= $eol.$tab.'box-shadow: '.self::$options['logindiv_shadow_x'].'px '.self::$options['logindiv_shadow_y'].'px '.self::$options['logindiv_shadow_softness'].'px '.self::$options['logindiv_shadow_color'].self::$options['logindiv_shadow_inset'].';';
				
			endif;
			if (!empty(self::$options['logindiv_width'])) $logindiv_style .= $eol.$tab.'width: '.self::$options['logindiv_width'].'px;';
			if (!empty(self::$options['logindiv_height'])) $logindiv_style .= $eol.$tab.'height: '.self::$options['logindiv_height'].'px;';
			if (!empty(self::$options['logindiv_padding'])) $logindiv_style .= $eol.$tab.'padding: '.self::$options['logindiv_padding'].';';
			if (!empty(self::$options['logindiv_margin'])) $logindiv_style .= $eol.$tab.'margin: '.self::$options['logindiv_margin'].';';
			
			if (!empty(self::$options['logindiv_text_color'])) :
				
				$logindiv_style .= $eol.$tab.'color: '.self::$options['logindiv_text_color'].';';
				$label_style .= $eol.$tab.'color: '.self::$options['logindiv_text_color'].';';
				
			endif;
			
			# .login form
			
			$loginform_style = '';
			
			if (isset(self::$options['loginform_transparency']) && self::$options['loginform_transparency'] == '0') :
			
				$loginform_style .= $eol.$tab.'background: transparent;';
			
			else:
				
				if (!empty(self::$options['loginform_bg_color1'])) $loginform_style .= $eol.$tab.'background-color: '.self::$options['loginform_bg_color1'].';';
				if (!empty(self::$options['loginform_bg_color2'])) :
					
					$loginform_style .= $eol.$tab.'background-clip: padding-box;';
					$loginform_style .= $eol.$tab.'background-image: -webkit-gradient(linear, left top, left bottom, from('.self::$options['loginform_bg_color1'].'), to('.self::$options['loginform_bg_color2'].'));';
					$loginform_style .= $eol.$tab.'background-image: -webkit-linear-gradient(top, '.self::$options['loginform_bg_color1'].', '.self::$options['loginform_bg_color2'].');';
					$loginform_style .= $eol.$tab.'background-image: -moz-linear-gradient(top, '.self::$options['loginform_bg_color1'].', '.self::$options['loginform_bg_color2'].');';
					$loginform_style .= $eol.$tab.'background-image: -ms-linear-gradient(top, '.self::$options['loginform_bg_color1'].', '.self::$options['loginform_bg_color2'].');';
					$loginform_style .= $eol.$tab.'background-image: -o-linear-gradient(top, '.self::$options['loginform_bg_color1'].', '.self::$options['loginform_bg_color2'].');';
					$loginform_style .= $eol.$tab.'background-image: -linear-gradient(top, '.self::$options['loginform_bg_color1'].', '.self::$options['loginform_bg_color2'].');';
					
				endif;
				
				if (!empty(self::$options['loginform_transparency'])) :
					$loginform_style .= $eol.$tab.'-ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity='.self::$options['loginform_transparency'].')";';
					$loginform_style .= $eol.$tab.'filter: alpha(Opacity='.self::$options['loginform_transparency'].');';
					$loginform_style .= $eol.$tab.'-moz-opacity: '.(self::$options['loginform_transparency']/100).';';
					$loginform_style .= $eol.$tab.'-khtml-opacity: '.(self::$options['loginform_transparency']/100).';';
					$loginform_style .= $eol.$tab.'opacity: '.(self::$options['loginform_transparency']/100).';';
				endif;
				
			endif;
				
			if (!empty(self::$options['loginform_background'])) $loginform_style .= $eol.$tab.'background-image: url('.self::$options['loginform_background'].');';
			if (!empty(self::$options['loginform_img_repeat'])) $loginform_style .= $eol.$tab.'background-repeat: '.self::$options['loginform_img_repeat'].';';
			if (!empty(self::$options['loginform_img_pos'])) $loginform_style .= $eol.$tab.'background-position: '.self::$options['loginform_img_pos'].';';
			
			if (!empty(self::$options['loginform_bg_size'])) :
			
				$loginform_style .= $eol.$tab.'-webkit-background-size: '.self::$options['loginform_bg_size'].';';
				$loginform_style .= $eol.$tab.'-moz-background-size: '.self::$options['loginform_bg_size'].';';
				$loginform_style .= $eol.$tab.'-o-background-size: '.self::$options['loginform_bg_size'].';';
				$loginform_style .= $eol.$tab.'background-size: '.self::$options['loginform_bg_size'].';';
			
			endif;
			
			if (!empty(self::$options['loginform_border_style']) && !empty(self::$options['loginform_border_width'])) $loginform_style .= $eol.$tab.'border: '.self::$options['loginform_border_style'].' '.self::$options['loginform_border_width'].'px '.self::$options['loginform_border_color'].';';
			if (isset(self::$options['loginform_border_style']) && self::$options['loginform_border_style'] == 'none') $loginform_style .= $eol.$tab.'border: medium none;';
			if (!empty(self::$options['loginform_border_round'])) :
				
				$loginform_style .= $eol.$tab.'-webkit-border-radius: '.self::$options['loginform_border_round'].'px;';
				$loginform_style .= $eol.$tab.'-moz-border-radius: '.self::$options['loginform_border_round'].'px;';
				$loginform_style .= $eol.$tab.'border-radius: '.self::$options['loginform_border_round'].'px;';
				
			endif;
			if (isset(self::$options['loginform_shadow_x']) && (!empty(self::$options['loginform_shadow_x']) || self::$options['loginform_shadow_x'] == '0')) :
				
				$loginform_style .= $eol.$tab.'-webkit-box-shadow: '.self::$options['loginform_shadow_x'].'px '.self::$options['loginform_shadow_y'].'px '.self::$options['loginform_shadow_softness'].'px '.self::$options['loginform_shadow_color'].self::$options['loginform_shadow_inset'].';';
				$loginform_style .= $eol.$tab.'-moz-box-shadow: '.self::$options['loginform_shadow_x'].'px '.self::$options['loginform_shadow_y'].'px '.self::$options['loginform_shadow_softness'].'px '.self::$options['loginform_shadow_color'].self::$options['loginform_shadow_inset'].';';
				$loginform_style .= $eol.$tab.'box-shadow: '.self::$options['loginform_shadow_x'].'px '.self::$options['loginform_shadow_y'].'px '.self::$options['loginform_shadow_softness'].'px '.self::$options['loginform_shadow_color'].self::$options['loginform_shadow_inset'].';';
			endif;
			
			if (!empty(self::$options['loginform_margin'])) $loginform_style .= $eol.$tab.'margin: '.self::$options['loginform_margin'].';';
			if (!empty(self::$options['loginform_padding'])) $loginform_style .= $eol.$tab.'padding: '.self::$options['loginform_padding'].';';		
			
			if (!empty(self::$options['loginform_text_color'])) :
				
				$loginform_style .= $eol.$tab.'color: rgb('.$this->rgb_color(self::$options['loginform_text_color']).');'.$eol.$tab.'color: rgba('.$this->rgb_color(self::$options['loginform_text_color'], 1).');';
				$label_style .= $eol.$tab.'color: rgb('.$this->rgb_color(self::$options['loginform_text_color']).');'.$eol.$tab.'color: rgba('.$this->rgb_color(self::$options['loginform_text_color'], 1).');';
				
			endif;
			
			# .forgetmenot
			
			if (!empty(self::$options['hide_rememberme'])) $rememberme_style = $eol.$tab.'display: none;'.$eol;
			
			# p.message
			
			$loggedout_style = '';
			
			if (isset(self::$options['loggedout_transparency']) && self::$options['loggedout_transparency'] == '0') :
			
				$loggedout_style .= $eol.$tab.'background: transparent;'.$eol.$tab.'border: none;'.$eol.$tab.'box-shadow: none !important;';
				
				if (!empty(self::$options['loggedout_text_color'])) :
				
					$loggedout_style .= $eol.$tab.'color: '.self::$options['loggedout_text_color'].' !important;';
					
				endif;
			
			else :
			
				if (!empty(self::$options['loggedout_bg_color'])) $loggedout_style = $eol.$tab.'background-color: '.self::$options['loggedout_bg_color'].' !important;';
				if (!empty(self::$options['loggedout_transparency'])) :
					$loggedout_style .= $eol.$tab.'-ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity='.self::$options['loggedout_transparency'].')" !important;';
					$loggedout_style .= $eol.$tab.'filter: alpha(Opacity='.self::$options['loggedout_transparency'].') !important;';
					$loggedout_style .= $eol.$tab.'-moz-opacity: '.(self::$options['loggedout_transparency']/100).' !important;';
					$loggedout_style .= $eol.$tab.'-khtml-opacity: '.(self::$options['loggedout_transparency']/100).' !important;';
					$loggedout_style .= $eol.$tab.'opacity: '.(self::$options['loggedout_transparency']/100).' !important;';
				endif;
			
				if (!empty(self::$options['loggedout_text_color'])) $loggedout_style .= $eol.$tab.'color: '.self::$options['loggedout_text_color'].' !important;';
				if (!empty(self::$options['loggedout_border_color'])) $loggedout_style .= $eol.$tab.'border-color: '.self::$options['loggedout_border_color'].' !important;';
				
			endif;
			
			# #login_error
			
			$error_style = '';
			
			if (isset(self::$options['error_transparency']) && self::$options['error_transparency'] == '0') :
			
				$error_style .= $eol.$tab.'background: transparent;'.$eol.$tab.'border: none;'.$eol.$tab.'box-shadow: none !important;';
				
				if (!empty(self::$options['error_text_color'])) :
				
					$error_style .= $eol.$tab.'color: '.self::$options['error_text_color'].' !important;';
					
				endif;
			
			else :
			
				if (!empty(self::$options['error_bg_color'])) $error_style .= $eol.$tab.'background-color: '.self::$options['error_bg_color'].' !important;';
				if (!empty(self::$options['error_transparency'])) :
					$error_style .= $eol.$tab.'-ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity='.self::$options['error_transparency'].')" !important;;';
					$error_style .= $eol.$tab.'filter: alpha(Opacity='.self::$options['error_transparency'].') !important;';
					$error_style .= $eol.$tab.'-moz-opacity: '.(self::$options['error_transparency']/100).' !important;';
					$error_style .= $eol.$tab.'-khtml-opacity: '.(self::$options['error_transparency']/100).' !important;';
					$error_style .= $eol.$tab.'opacity: '.(self::$options['error_transparency']/100).' !important;';
				endif;
				
				if (!empty(self::$options['error_text_color'])) $error_style .= $eol.$tab.'color: '.self::$options['error_text_color'].' !important;';
				if (!empty(self::$options['error_border_color'])) $error_style .= $eol.$tab.'border-color: '.self::$options['error_border_color'].' !important;';
				
			endif;
			
			# .input
			
			$input_style = '';
			
			if (!empty(self::$options['input_text_color'])) $input_style .= $eol.$tab.'color: '.self::$options['input_text_color'].' !important;';
			if (!empty(self::$options['input_bg_color'])) $input_style .= $eol.$tab.'background-color: '.self::$options['input_bg_color'].' !important;';
			if (!empty(self::$options['input_border_color'])) $input_style .= $eol.$tab.'border-color: '.self::$options['input_border_color'].' !important;';
			if (isset(self::$options['input_shadow_x']) && (!empty(self::$options['input_shadow_x']) || self::$options['input_shadow_x'] == '0')) :
				
				$input_style .= $eol.$tab.'-webkit-box-shadow: '.self::$options['input_shadow_x'].'px '.self::$options['input_shadow_y'].'px '.self::$options['input_shadow_softness'].'px '.self::$options['input_shadow_color'].self::$options['input_shadow_inset'].' !important;';
				$input_style .= $eol.$tab.'-moz-box-shadow: '.self::$options['input_shadow_x'].'px '.self::$options['input_shadow_y'].'px '.self::$options['input_shadow_softness'].'px '.self::$options['input_shadow_color'].self::$options['input_shadow_inset'].' !important;';
				$input_style .= $eol.$tab.'box-shadow: '.self::$options['input_shadow_x'].'px '.self::$options['input_shadow_y'].'px '.self::$options['input_shadow_softness'].'px '.self::$options['input_shadow_color'].self::$options['input_shadow_inset'].' !important;';
			endif;
			
			# #login_error a, #nav a, #backtoblog a
			
			$link_style = '';
			$hover_style = '';
			
			if (!empty(self::$options['link_text_color'])) $link_style .= $eol.$tab.'color: '.self::$options['link_text_color'].' !important;';
			if (!empty(self::$options['link_textdecoration'])) $link_style .= $eol.$tab.'text-decoration: '.self::$options['link_textdecoration'].' !important;';
			if (isset(self::$options['link_shadow_x']) && (!empty(self::$options['link_shadow_x']) || self::$options['link_shadow_x'] == '0')) $link_style .= $eol.$tab.'text-shadow: '.self::$options['link_shadow_x'].'px '.self::$options['link_shadow_y'].'px '.self::$options['link_shadow_softness'].'px '.self::$options['link_shadow_color'].' !important;';
			if (!empty($link_style) && empty(self::$options['link_shadow_x'])) $link_style .= $eol.$tab.'text-shadow: none !important;';
			if (!empty(self::$options['link_size'])) $link_style .= $eol.$tab.'font-size: '.self::$options['link_size'].';';
			
			if (!empty(self::$options['hover_text_color'])) $hover_style .= $eol.$tab.'color: '.self::$options['hover_text_color'].' !important;';
			if (!empty(self::$options['hover_textdecoration'])) $hover_style .= $eol.$tab.'text-decoration: '.self::$options['hover_textdecoration'].' !important;';
			if (isset(self::$options['hover_shadow_x']) && (!empty(self::$options['hover_shadow_x']) || self::$options['hover_shadow_x'] == '0')) $hover_style .= $eol.$tab.'text-shadow: '.self::$options['hover_shadow_x'].'px '.self::$options['hover_shadow_y'].'px '.self::$options['hover_shadow_softness'].'px '.self::$options['hover_shadow_color'].' !important;';
			
			# #backtoblog
			
			$backtoblog_style = '';
			
			if (!empty(self::$options['hide_backlink'])) $backtoblog_style .= $eol.$tab.'display: none;';
			
			# #wp-submit.button-primary
			
			$button_style = '';
			$btn_hover_style = '';
			
			if (!empty(self::$options['button_bg_color1'])) :
				
				$button_style .= $eol.$tab.'background: transparent !important;';
				$button_style .= $eol.$tab.'background-color: '.self::$options['button_bg_color1'].' !important;';
				
			endif;
			if (!empty(self::$options['button_bg_color2'])) :
			
				$button_style .= $eol.$tab.'background-clip: padding-box;';	
				$button_style .= $eol.$tab.'background-image: -webkit-gradient(linear, left top, left bottom, from('.self::$options['button_bg_color1'].'), to('.self::$options['button_bg_color2'].')) !important;';
				$button_style .= $eol.$tab.'background-image: -webkit-linear-gradient(top, '.self::$options['button_bg_color1'].', '.self::$options['button_bg_color2'].') !important;';
				$button_style .= $eol.$tab.'background-image: -moz-linear-gradient(top, '.self::$options['button_bg_color1'].', '.self::$options['button_bg_color2'].') !important;';
				$button_style .= $eol.$tab.'background-image: -ms-linear-gradient(top, '.self::$options['button_bg_color1'].', '.self::$options['button_bg_color2'].') !important;';
				$button_style .= $eol.$tab.'background-image: -o-linear-gradient(top, '.self::$options['button_bg_color1'].', '.self::$options['button_bg_color2'].') !important;';
				$button_style .= $eol.$tab.'background-image: -linear-gradient(top, '.self::$options['button_bg_color1'].', '.self::$options['button_bg_color2'].') !important;';
				
			endif;
			if (!empty(self::$options['button_text_color'])) $button_style .= $eol.$tab.'color: '.self::$options['button_text_color'].' !important;';
			if (!empty(self::$options['button_border_color'])) $button_style .= $eol.$tab.'border: solid 1px '.self::$options['button_border_color'].' !important;';
			
			if (!empty(self::$options['btn_hover_bg_color1'])) $btn_hover_style .= $eol.$tab.'background-color: '.self::$options['btn_hover_bg_color1'].' !important;';
			if (!empty(self::$options['btn_hover_bg_color2'])) :
			
			
				$btn_hover_style .= $eol.$tab.'background-clip: padding-box;';	
				$btn_hover_style .= $eol.$tab.'background-image: -webkit-gradient(linear, left top, left bottom, from('.self::$options['btn_hover_bg_color1'].'), to('.self::$options['btn_hover_bg_color2'].')) !important;';
				$btn_hover_style .= $eol.$tab.'background-image: -webkit-linear-gradient(top, '.self::$options['btn_hover_bg_color1'].', '.self::$options['btn_hover_bg_color2'].') !important;';
				$btn_hover_style .= $eol.$tab.'background-image: -moz-linear-gradient(top, '.self::$options['btn_hover_bg_color1'].', '.self::$options['btn_hover_bg_color2'].') !important;';
				$btn_hover_style .= $eol.$tab.'background-image: -ms-linear-gradient(top, '.self::$options['btn_hover_bg_color1'].', '.self::$options['btn_hover_bg_color2'].') !important;';
				$btn_hover_style .= $eol.$tab.'background-image: -o-linear-gradient(top, '.self::$options['btn_hover_bg_color1'].', '.self::$options['btn_hover_bg_color2'].') !important;';
				$btn_hover_style .= $eol.$tab.'background-image: -linear-gradient(top, '.self::$options['btn_hover_bg_color1'].', '.self::$options['btn_hover_bg_color2'].') !important;';
				
			endif;
			if (!empty(self::$options['btn_hover_text_color'])) $btn_hover_style .= $eol.$tab.'color: '.self::$options['btn_hover_text_color'].' !important;';
			if (!empty(self::$options['btn_hover_border_color'])) $btn_hover_style .= $eol.$tab.'border: solid 1px '.self::$options['btn_hover_border_color'].' !important;';
		
			# building the stylesheet
			
			$link_text_color = '';
			
			self::$custom_css = (!self::$options['compress']) ? $eol.'/* CSS portion of the A5 Custom Login Page */'.$eol.$eol : '';
			
			if(!empty($body_style)) self::$custom_css .= 'html body {'.$body_style.$eol.'}'.$eol;
			if(!empty($h1_style)) self::$custom_css .= 'div#login h1 a {'.$h1_style.$eol.'}'.$eol;
			if(!empty($logindiv_style)) self::$custom_css .= '#login {'.$logindiv_style.$eol.'}'.$eol;
			if(!empty($loginform_style)) self::$custom_css .= '#login form {'.$loginform_style.$eol.'}'.$eol;
			if(!empty($label_style)) self::$custom_css .= '#loginform label,'.$eol.'#lostpasswordform label,'.$eol.'#registerform label {'.$label_style.'}'.$eol;
			if(!empty($loggedout_style)) self::$custom_css .= 'p.message {'.$loggedout_style.$eol.'}'.$eol;
			if(!empty($error_style)) self::$custom_css .= '#login_error {'.$error_style.$eol.'}'.$eol;
			if(!empty($input_style)) self::$custom_css .= '.input {'.$input_style.$eol.'}'.$eol;
			if(!empty($rememberme_style)) self::$custom_css .= '.forgetmenot {'.$rememberme_style.$eol.'}'.$eol;
			if(!empty($backtoblog_style)) self::$custom_css .= '#backtoblog {'.$backtoblog_style.$eol.'}'.$eol;
			if(!empty($link_style)) :
			
				if (!empty(self::$options['link_text_color'])) $link_text_color = $eol.$tab.'color: '.self::$options['link_text_color'].' !important;';
				
				self::$custom_css .= '#nav {'.$link_text_color.$eol.$tab.'text-shadow: none !important;'.$eol.'}'.$eol;
				self::$custom_css .= '#login_error a,'.$eol.'#nav a,'.$eol.'#backtoblog a {'.$link_style.$eol.'}'.$eol;
				
			endif;
			if(!empty($hover_style)) self::$custom_css .= '#login_error a:hover,'.$eol.'#nav a:hover,'.$eol.'#backtoblog a:hover {'.$hover_style.$eol.'}'.$eol;
			if(!empty($button_style)) self::$custom_css .= '#wp-submit.button-primary {'.$button_style.$eol.'}'.$eol;
			if(!empty($btn_hover_style)) self::$custom_css .= '#wp-submit.button-primary:hover {'.$btn_hover_style.$eol.'}'.$eol;
			
		endif;
		
		if (isset(self::$options['css']) && !empty(self::$options['css']) && !isset(self::$options['override'])) self::$custom_css .= self::$options['css'];
		
		parent::$login_styles .= (!self::$options['compress']) ? self::$custom_css : str_replace(array("\r\n", "\n", "\r", "\t"), '', self::$custom_css);
		
		// part of the Widget
		
		parent::A5_DynamicFiles('wp', 'css', 'all', false, self::$options['inline'], self::$options['priority']);
		
		// for preview in admin
		
		parent::A5_DynamicFiles('admin', 'css', 'all', array('a5-custom-login_page_clp-widget-settings'), false);
		
		if (isset(self::$widget['css']) && !empty(self::$widget['css']) && isset(self::$widget['override'])) :
		
			self::$widget_css = (!self::$options['compress']) ? $eol.'/* CSS portion of the A5 Custom Login Widget */'.$eol.$eol : '';
		
			self::$widget_css .= self::$widget['css'];
		
		else :
		
			# collecting variables
			
			# widget_custom_login_widget[id^="custom_login_widget"]
			
			$container_style = '';
			$h_style = '';
				
			if (!empty(self::$widget['container_bg_color1'])) $container_style .= $eol.$tab.'background-color: '.self::$widget['container_bg_color1'].';';
			if (!empty(self::$widget['container_bg_color2'])) :
				
				$container_style .= $eol.$tab.'background-clip: padding-box;';
				$container_style .= $eol.$tab.'background-image: -webkit-gradient(linear, left top, left bottom, from('.self::$widget['container_bg_color1'].'), to('.self::$widget['container_bg_color2'].'));';
				$container_style .= $eol.$tab.'background-image: -webkit-linear-gradient(top, '.self::$widget['container_bg_color1'].', '.self::$widget['container_bg_color2'].');';
				$container_style .= $eol.$tab.'background-image: -moz-linear-gradient(top, '.self::$widget['container_bg_color1'].', '.self::$widget['container_bg_color2'].');';
				$container_style .= $eol.$tab.'background-image: -ms-linear-gradient(top, '.self::$widget['container_bg_color1'].', '.self::$widget['container_bg_color2'].');';
				$container_style .= $eol.$tab.'background-image: -o-linear-gradient(top, '.self::$widget['container_bg_color1'].', '.self::$widget['container_bg_color2'].');';
				$container_style .= $eol.$tab.'background-image: -linear-gradient(top, '.self::$widget['container_bg_color1'].', '.self::$widget['container_bg_color2'].');';
				
			endif;
			if (!empty(self::$widget['container_transparency'])) :
				$container_style .= $eol.$tab.'-ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity='.self::$widget['container_transparency'].')";';
				$container_style .= $eol.$tab.'filter: alpha(Opacity='.self::$widget['container_transparency'].');';
				$container_style .= $eol.$tab.'-moz-opacity: '.(self::$widget['container_transparency']/100).';';
				$container_style .= $eol.$tab.'-khtml-opacity: '.(self::$widget['container_transparency']/100).';';
				$container_style .= $eol.$tab.'opacity: '.(self::$widget['container_transparency']/100).';';
			endif;
			if (!empty(self::$widget['container_background'])) $container_style .= $eol.$tab.'background-image: url('.self::$widget['container_background'].');';
			if (!empty(self::$widget['container_img_repeat'])) $container_style .= $eol.$tab.'background-repeat: '.self::$widget['container_img_repeat'].';';
			if (!empty(self::$widget['container_img_pos'])) $container_style .= $eol.$tab.'background-position: '.self::$widget['container_img_pos'].';';
			if (!empty(self::$widget['container_border_style'])) $container_style .= $eol.$tab.'border: '.self::$widget['container_border_style'].' '.self::$widget['container_border_width'].'px '.self::$widget['container_border_color'].';';
			if (!empty(self::$widget['container_border_round'])) :
				
				$container_style .= $eol.$tab.'-webkit-border-radius: '.self::$widget['container_border_round'].'px;';
				$container_style .= $eol.$tab.'-moz-border-radius: '.self::$widget['container_border_round'].'px;';
				$container_style .= $eol.$tab.'border-radius: '.self::$widget['container_border_round'].'px;';
				
			endif;
			if (isset(self::$widget['container_shadow_x']) && (!empty(self::$widget['container_shadow_x']) || self::$widget['container_shadow_x']=='0')) :
				
				$container_style .= $eol.$tab.'-webkit-box-shadow: '.self::$widget['container_shadow_x'].'px '.self::$widget['container_shadow_y'].'px '.self::$widget['container_shadow_softness'].'px '.self::$widget['container_shadow_color'].self::$widget['container_shadow_inset'].';';
				$container_style .= $eol.$tab.'-moz-box-shadow: '.self::$widget['container_shadow_x'].'px '.self::$widget['container_shadow_y'].'px '.self::$widget['container_shadow_softness'].'px '.self::$widget['container_shadow_color'].self::$widget['container_shadow_inset'].';';
				$container_style .= $eol.$tab.'box-shadow: '.self::$widget['container_shadow_x'].'px '.self::$widget['container_shadow_y'].'px '.self::$widget['container_shadow_softness'].'px '.self::$widget['container_shadow_color'].self::$widget['container_shadow_inset'].';';
				
			endif;
			if (!empty(self::$widget['container_padding'])) $container_style .= $eol.$tab.'padding: '.self::$widget['container_padding'].';';
			if (!empty(self::$widget['container_margin'])) $container_style .= $eol.$tab.'margin: '.self::$widget['container_margin'].';';
			
			if (!empty(self::$widget['container_text_color'])) :
				
				$container_style .= $eol.$tab.'color: '.self::$widget['container_text_color'].';';
				$h_style .= $eol.$tab.'color: '.self::$widget['container_text_color'].' !important;';
				
			endif;
			
			# img a
			
			$widget_logo_style = '';
			
			if (!empty(self::$widget['logo'])) :
			
				$widget_logo_style .= $eol.$tab.'max-width: 100%;'.$eol.$tab.'height: auto;';
			
				if (!empty(self::$widget['h1_margin'])) $widget_logo_style .= $eol.$tab.'margin: '.self::$widget['h1_margin'].';';
				
				if (!empty(self::$widget['h1_padding'])) $widget_logo_style .= $eol.$tab.'padding: '.self::$widget['h1_padding'].';';
				
				if (!empty(self::$widget['h1_corner'])) :
				
					$widget_logo_style .= $eol.$tab.'-webkit-border-radius: '.self::$widget['h1_corner'].'px;';
					$widget_logo_style .= $eol.$tab.'-moz-border-radius: '.self::$widget['h1_corner'].'px;';
					$widget_logo_style .= $eol.$tab.'border-radius: '.self::$widget['h1_corner'].'px;';
					
				endif;
				
				if (!empty(self::$widget['h1_shadow_x']) || self::$widget['h1_shadow_x']=='0') :
					
					$widget_logo_style .= $eol.$tab.'-webkit-box-shadow: '.self::$widget['h1_shadow_x'].'px '.self::$widget['h1_shadow_y'].'px '.self::$widget['h1_shadow_softness'].'px '.self::$widget['h1_shadow_color'].self::$widget['h1_shadow_inset'].';';
					$widget_logo_style .= $eol.$tab.'-moz-box-shadow: '.self::$widget['h1_shadow_x'].'px '.self::$widget['h1_shadow_y'].'px '.self::$widget['h1_shadow_softness'].'px '.self::$widget['h1_shadow_color'].self::$widget['h1_shadow_inset'].';';
					$widget_logo_style .= $eol.$tab.'box-shadow: '.self::$widget['h1_shadow_x'].'px '.self::$widget['h1_shadow_y'].'px '.self::$widget['h1_shadow_softness'].'px '.self::$widget['h1_shadow_color'].self::$widget['h1_shadow_inset'].';';
					
				endif;
				
			endif;
			
			# form
			
			$loginform_style = '';
			$label_style = '';
			
			if (isset(self::$widget['loginform_transparency']) && self::$widget['loginform_transparency'] == '0') :
			
				$loginform_style .= $eol.$tab.'background: transparent;';
			
			else:
				
				if (!empty(self::$widget['loginform_bg_color1'])) $loginform_style .= $eol.$tab.'background-color: '.self::$widget['loginform_bg_color1'].';';
				if (!empty(self::$widget['loginform_bg_color2'])) :
					
					$loginform_style .= $eol.$tab.'background-clip: padding-box;';
					$loginform_style .= $eol.$tab.'background-image: -webkit-gradient(linear, left top, left bottom, from('.self::$widget['loginform_bg_color1'].'), to('.self::$widget['loginform_bg_color2'].'));';
					$loginform_style .= $eol.$tab.'background-image: -webkit-linear-gradient(top, '.self::$widget['loginform_bg_color1'].', '.self::$widget['loginform_bg_color2'].');';
					$loginform_style .= $eol.$tab.'background-image: -moz-linear-gradient(top, '.self::$widget['loginform_bg_color1'].', '.self::$widget['loginform_bg_color2'].');';
					$loginform_style .= $eol.$tab.'background-image: -ms-linear-gradient(top, '.self::$widget['loginform_bg_color1'].', '.self::$widget['loginform_bg_color2'].');';
					$loginform_style .= $eol.$tab.'background-image: -o-linear-gradient(top, '.self::$widget['loginform_bg_color1'].', '.self::$widget['loginform_bg_color2'].');';
					$loginform_style .= $eol.$tab.'background-image: -linear-gradient(top, '.self::$widget['loginform_bg_color1'].', '.self::$widget['loginform_bg_color2'].');';
					
				endif;
				
				if (!empty(self::$widget['loginform_transparency'])) :
					$loginform_style .= $eol.$tab.'-ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity='.self::$widget['loginform_transparency'].')";';
					$loginform_style .= $eol.$tab.'filter: alpha(Opacity='.self::$widget['loginform_transparency'].');';
					$loginform_style .= $eol.$tab.'-moz-opacity: '.(self::$widget['loginform_transparency']/100).';';
					$loginform_style .= $eol.$tab.'-khtml-opacity: '.(self::$widget['loginform_transparency']/100).';';
					$loginform_style .= $eol.$tab.'opacity: '.(self::$widget['loginform_transparency']/100).';';
				endif;
				
			endif;
				
			if (!empty(self::$widget['loginform_background'])) $loginform_style .= $eol.$tab.'background-image: url('.self::$widget['loginform_background'].');';
			if (!empty(self::$widget['loginform_img_repeat'])) $loginform_style .= $eol.$tab.'background-repeat: '.self::$widget['loginform_img_repeat'].';';
			if (!empty(self::$widget['loginform_img_pos'])) $loginform_style .= $eol.$tab.'background-position: '.self::$widget['loginform_img_pos'].';';
			if (!empty(self::$widget['loginform_border_style']) && !empty(self::$widget['loginform_border_width'])) $loginform_style .= $eol.$tab.'border: '.self::$widget['loginform_border_style'].' '.self::$widget['loginform_border_width'].'px '.self::$widget['loginform_border_color'].';';
			if (isset(self::$widget['loginform_border_style']) && self::$widget['loginform_border_style'] == 'none') $loginform_style .= $eol.$tab.'border: medium none;';
			if (!empty(self::$widget['loginform_border_round'])) :
				
				$loginform_style .= $eol.$tab.'-webkit-border-radius: '.self::$widget['loginform_border_round'].'px;';
				$loginform_style .= $eol.$tab.'-moz-border-radius: '.self::$widget['loginform_border_round'].'px;';
				$loginform_style .= $eol.$tab.'border-radius: '.self::$widget['loginform_border_round'].'px;';
				
			endif;
			if (isset(self::$widget['loginform_shadow_x']) && (!empty(self::$widget['loginform_shadow_x']) || self::$widget['loginform_shadow_x'] == '0')) :
				
				$loginform_style .= $eol.$tab.'-webkit-box-shadow: '.self::$widget['loginform_shadow_x'].'px '.self::$widget['loginform_shadow_y'].'px '.self::$widget['loginform_shadow_softness'].'px '.self::$widget['loginform_shadow_color'].self::$widget['loginform_shadow_inset'].';';
				$loginform_style .= $eol.$tab.'-moz-box-shadow: '.self::$widget['loginform_shadow_x'].'px '.self::$widget['loginform_shadow_y'].'px '.self::$widget['loginform_shadow_softness'].'px '.self::$widget['loginform_shadow_color'].self::$widget['loginform_shadow_inset'].';';
				$loginform_style .= $eol.$tab.'box-shadow: '.self::$widget['loginform_shadow_x'].'px '.self::$widget['loginform_shadow_y'].'px '.self::$widget['loginform_shadow_softness'].'px '.self::$widget['loginform_shadow_color'].self::$widget['loginform_shadow_inset'].';';
			endif;
			
			if (!empty(self::$widget['loginform_margin'])) $loginform_style .= $eol.$tab.'margin: '.self::$widget['loginform_margin'].';';
			if (!empty(self::$widget['loginform_padding'])) $loginform_style .= $eol.$tab.'padding: '.self::$widget['loginform_padding'].';';		
			
			if (!empty(self::$widget['loginform_text_color'])) :
				
				$loginform_style .= $eol.$tab.'color: rgb('.$this->rgb_color(self::$widget['loginform_text_color']).');'.$eol.$tab.'color: rgba('.$this->rgb_color(self::$widget['loginform_text_color'], 1).');';
				$label_style .= $eol.$tab.'color: rgb('.$this->rgb_color(self::$widget['loginform_text_color']).');'.$eol.$tab.'color: rgba('.$this->rgb_color(self::$widget['loginform_text_color'], 1).');';
				
			endif;
			
			# .input
			
			$input_style = '';
			
			if (!empty(self::$widget['input_text_color'])) $input_style .= $eol.$tab.'color: '.self::$widget['input_text_color'].';';
			if (!empty(self::$widget['input_bg_color'])) $input_style .= $eol.$tab.'background-color: '.self::$widget['input_bg_color'].';';
			if (!empty(self::$widget['input_border_color'])) $input_style .= $eol.$tab.'border-color: '.self::$widget['input_border_color'].';';
			if (!empty(self::$widget['input_float'])) $input_style .= $eol.$tab.'float: '.self::$widget['input_float'].';';
			if (isset(self::$widget['input_shadow_x']) && (!empty(self::$widget['input_shadow_x']) || self::$widget['input_shadow_x'] == '0')) :
				
				$input_style .= $eol.$tab.'-webkit-box-shadow: '.self::$widget['input_shadow_x'].'px '.self::$widget['input_shadow_y'].'px '.self::$widget['input_shadow_softness'].'px '.self::$widget['input_shadow_color'].self::$widget['input_shadow_inset'].';';
				$input_style .= $eol.$tab.'-moz-box-shadow: '.self::$widget['input_shadow_x'].'px '.self::$widget['input_shadow_y'].'px '.self::$widget['input_shadow_softness'].'px '.self::$widget['input_shadow_color'].self::$widget['input_shadow_inset'].';';
				$input_style .= $eol.$tab.'box-shadow: '.self::$widget['input_shadow_x'].'px '.self::$widget['input_shadow_y'].'px '.self::$widget['input_shadow_softness'].'px '.self::$widget['input_shadow_color'].self::$widget['input_shadow_inset'].';';
			endif;
			
			# #wp-submit.button-primary
			
			$button_style = '';
			$btn_hover_style = '';
			
			if (!empty(self::$widget['button_bg_color1'])) :
				
				$button_style .= $eol.$tab.'background: transparent;';
				$button_style .= $eol.$tab.'background-color: '.self::$widget['button_bg_color1'].';';
				
			endif;
			if (!empty(self::$widget['button_bg_color2'])) :
				
				$button_style .= $eol.$tab.'background-clip: padding-box;';
				$button_style .= $eol.$tab.'background-image: -webkit-gradient(linear, left top, left bottom, from('.self::$widget['button_bg_color1'].'), to('.self::$widget['button_bg_color2'].'));';
				$button_style .= $eol.$tab.'background-image: -webkit-linear-gradient(top, '.self::$widget['button_bg_color1'].', '.self::$widget['button_bg_color2'].');';
				$button_style .= $eol.$tab.'background-image: -moz-linear-gradient(top, '.self::$widget['button_bg_color1'].', '.self::$widget['button_bg_color2'].');';
				$button_style .= $eol.$tab.'background-image: -ms-linear-gradient(top, '.self::$widget['button_bg_color1'].', '.self::$widget['button_bg_color2'].');';
				$button_style .= $eol.$tab.'background-image: -o-linear-gradient(top, '.self::$widget['button_bg_color1'].', '.self::$widget['button_bg_color2'].');';
				$button_style .= $eol.$tab.'background-image: -linear-gradient(top, '.self::$widget['button_bg_color1'].', '.self::$widget['button_bg_color2'].');';
				
			endif;
			if (!empty(self::$widget['button_text_color'])) $button_style .= $eol.$tab.'color: '.self::$widget['button_text_color'].';';
			if (!empty(self::$widget['button_border_color'])) $button_style .= $eol.$tab.'border: solid 1px '.self::$widget['button_border_color'].';';
			
			if (!empty(self::$widget['btn_hover_bg_color1'])) $btn_hover_style .= $eol.$tab.'background-color: '.self::$widget['btn_hover_bg_color1'].';';
			if (!empty(self::$widget['btn_hover_bg_color2'])) :
				
				$btn_hover_style .= $eol.$tab.'background-clip: padding-box;';
				$btn_hover_style .= $eol.$tab.'background-image: -webkit-gradient(linear, left top, left bottom, from('.self::$widget['btn_hover_bg_color1'].'), to('.self::$widget['btn_hover_bg_color2'].'));';
				$btn_hover_style .= $eol.$tab.'background-image: -webkit-linear-gradient(top, '.self::$widget['btn_hover_bg_color1'].', '.self::$widget['btn_hover_bg_color2'].');';
				$btn_hover_style .= $eol.$tab.'background-image: -moz-linear-gradient(top, '.self::$widget['btn_hover_bg_color1'].', '.self::$widget['btn_hover_bg_color2'].');';
				$btn_hover_style .= $eol.$tab.'background-image: -ms-linear-gradient(top, '.self::$widget['btn_hover_bg_color1'].', '.self::$widget['btn_hover_bg_color2'].');';
				$btn_hover_style .= $eol.$tab.'background-image: -o-linear-gradient(top, '.self::$widget['btn_hover_bg_color1'].', '.self::$widget['btn_hover_bg_color2'].');';
				$btn_hover_style .= $eol.$tab.'background-image: -linear-gradient(top, '.self::$widget['btn_hover_bg_color1'].', '.self::$widget['btn_hover_bg_color2'].');';
				
			endif;
			if (!empty(self::$widget['btn_hover_text_color'])) $btn_hover_style .= $eol.$tab.'color: '.self::$widget['btn_hover_text_color'].';';
			if (!empty(self::$widget['btn_hover_border_color'])) $btn_hover_style .= $eol.$tab.'border: solid 1px '.self::$widget['btn_hover_border_color'].';';
			
			# a
			
			$link_style = '';
			$hover_style = '';
			
			if (!empty(self::$widget['link_text_color'])) $link_style .= $eol.$tab.'color: '.self::$widget['link_text_color'].';';
			if (!empty(self::$widget['link_textdecoration'])) $link_style .= $eol.$tab.'text-decoration: '.self::$widget['link_textdecoration'].';';
			if (isset(self::$widget['link_shadow_x']) && (!empty(self::$widget['link_shadow_x']) || self::$widget['link_shadow_x'] == '0')) $link_style .= $eol.$tab.'text-shadow: '.self::$widget['link_shadow_x'].'px '.self::$widget['link_shadow_y'].'px '.self::$widget['link_shadow_softness'].'px '.self::$widget['link_shadow_color'].';';
			if (!empty($link_style) && empty(self::$widget['link_shadow_x'])) $link_style .= $eol.$tab.'text-shadow: none;';
			if (!empty(self::$widget['link_size'])) $link_style .= $eol.$tab.'font-size: '.self::$widget['link_size'].';';
			
			if (!empty(self::$widget['hover_text_color'])) $hover_style .= $eol.$tab.'color: '.self::$widget['hover_text_color'].';';
			if (!empty(self::$widget['hover_textdecoration'])) $hover_style .= $eol.$tab.'text-decoration: '.self::$widget['hover_textdecoration'].';';
			if (isset(self::$widget['hover_shadow_x']) && (!empty(self::$widget['hover_shadow_x']) || self::$widget['hover_shadow_x'] == '0')) $hover_style .= $eol.$tab.'text-shadow: '.self::$widget['hover_shadow_x'].'px '.self::$widget['hover_shadow_y'].'px '.self::$widget['hover_shadow_softness'].'px '.self::$widget['hover_shadow_color'].';';
			
			# building the stylesheet
			
			$select = 'widget_custom_login_widget[id^="custom_login_widget"]';
			
			self::$widget_css = (!self::$options['compress']) ? $eol.'/* CSS portion of the A5 Custom Login Widget */'.$eol.$eol : '';
			
			if(!empty($container_style)) self::$widget_css .= self::build_widget_css($select, '').'{'.$container_style.$eol.'}'.$eol;
			if(!empty($widget_logo_style)) self::$widget_css .= self::build_widget_css($select, 'img').'{'.$widget_logo_style.$eol.'}'.$eol;
			if(!empty($h_style)) self::$widget_css .= self::build_widget_css($select, '.widget-title').'{'.$h_style.$eol.'}'.$eol;
			if(!empty($loginform_style)) self::$widget_css .= self::build_widget_css($select, 'form').'{'.$loginform_style.$eol.'}'.$eol;
			if(!empty($input_style)) self::$widget_css .= self::build_widget_css($select, 'input.input').'{'.$input_style.$eol.'}'.$eol;
			if(!empty($button_style)) self::$widget_css .= self::build_widget_css($select, '.button-primary').'{'.$button_style.$eol.'}'.$eol;
			if(!empty($btn_hover_style)) self::$widget_css .= self::build_widget_css($select, '.button-primary:hover').'{'.$btn_hover_style.$eol.'}'.$eol;
			if(!empty($link_style)) self::$widget_css .= self::build_widget_css($select, 'a').'{'.$link_style.$eol.'}'.$eol;
			if(!empty($hover_style)) self::$widget_css .= self::build_widget_css($select, 'a:hover').'{'.$hover_style.$eol.'}'.$eol;
		
		endif;
		
		if (isset(self::$widget['css']) && !empty(self::$widget['css']) && !isset(self::$widget['override'])) self::$widget_css .= self::$widget['css'];
		
		parent::$wp_styles .= (!self::$options['compress']) ? self::$widget_css : str_replace(array("\r\n", "\n", "\r", "\t"), '', self::$widget_css);
		
		parent::$admin_styles .= self::$widget_css;

	}
	
	/**
	 *
	 * get RGB color for labels and input fields
	 *
	 */
	function rgb_color($color, $opacity = false) {
		
		$r = hexdec(substr($color, 1 , 2 ));
		
		$g = hexdec(substr($color, 3 , 2 ));
		
		$b = hexdec(substr($color, 5 , 2 ));
		
		$output = ($opacity) ? $r.','.$g.','.$b.','.$opacity : $r.','.$g.','.$b;
		
		return $output;
		
	}
	
} // CLP_Dynamic CSS

?>