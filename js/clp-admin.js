jQuery(document).ready(function(){
	
//save main_form

	jQuery("#main_form").submit(function()	{
		jQuery("#mainmsg").html('<p class="save">' + message.saving + '</p>');		
		var section = 'main';
		var logo = jQuery("#logo").val();
		var url = jQuery("#url").val();
		var title = jQuery("#title").val();
		var body_background = jQuery("#body_background").val();
		var body_img_repeat = jQuery("#body_img_repeat").val();
		var body_img_pos = jQuery("#body_img_pos").val();
		var body_bg_color1 = jQuery("#body_bg_color1").val();
		var body_bg_color2 = jQuery("#body_bg_color2").val();
		var body_text_color = jQuery("#body_text_color").val();
		var mainnonce = jQuery("#mainnonce").val();
		var _wp_http_referer = jQuery("#main_form #_wp_http_referer").val();
		var data = {
			action: 'clp_save_settings',
			section: section,
			logo: logo,
			url: url,
			title: title,
			body_background: body_background,
			body_img_repeat: body_img_repeat,
			body_img_pos: body_img_pos,
			body_bg_color1: body_bg_color1,
			body_bg_color2: body_bg_color2,
			body_text_color: body_text_color,
			mainnonce: mainnonce,
			_wp_http_referer: _wp_http_referer
		};
		jQuery("#main_save").hide();
		jQuery(".main-save").show();
		jQuery.post(ajaxurl, data,
		function(response){
			jQuery("#mainmsg").html(response);
			jQuery(".main-save").hide();
			jQuery("#main_save").show();
		});
	return false;
	});
	
//save loginform_form

	jQuery("#loginform_form").submit(function()	{
		jQuery("#loginmsg").html('<p class="save">' + message.saving + '</p>');		
		var section = 'loginform';
		var loginform_background = jQuery("#loginform_background").val();
		var loginform_img_repeat = jQuery("#loginform_img_repeat").val();
		var loginform_img_pos = jQuery("#loginform_img_pos").val();
		var loginform_bg_color1 = jQuery("#loginform_bg_color1").val();
		var loginform_bg_color2 = jQuery("#loginform_bg_color2").val();
		var loginform_text_color = jQuery("#loginform_text_color").val();
		var loginform_border_style = jQuery("#loginform_border_style").val();
		var loginform_border_width = jQuery("#loginform_border_width").val();
		var loginform_border_color = jQuery("#loginform_border_color").val();
		var loginform_border_round = jQuery("#loginform_border_round").val();
		var loginform_shadow_x = jQuery("#loginform_shadow_x").val();
		var loginform_shadow_y = jQuery("#loginform_shadow_y").val();
		var loginform_shadow_softness = jQuery("#loginform_shadow_softness").val();
		var loginform_shadow_color = jQuery("#loginform_shadow_color").val();		
		var loginformnonce = jQuery("#loginformnonce").val();
		var _wp_http_referer = jQuery("#loginform_form #_wp_http_referer").val();
		var data = {
			action: 'clp_save_settings',
			section: section,
			loginform_background: loginform_background,
			loginform_img_repeat: loginform_img_repeat,
			loginform_img_pos: loginform_img_pos,
			loginform_bg_color1: loginform_bg_color1,
			loginform_bg_color2: loginform_bg_color2,
			loginform_text_color: loginform_text_color,
			loginform_border_style: loginform_border_style,
			loginform_border_width: loginform_border_width,
			loginform_border_color: loginform_border_color,
			loginform_border_round: loginform_border_round,
			loginform_shadow_x: loginform_shadow_x,
			loginform_shadow_y: loginform_shadow_y,
			loginform_shadow_softness: loginform_shadow_softness,
			loginform_shadow_color: loginform_shadow_color,
			loginformnonce: loginformnonce,
			_wp_http_referer: _wp_http_referer
		};
		jQuery("#loginform_save").hide();
		jQuery(".loginform_save").show();
		jQuery.post(ajaxurl, data,
		function(response){
			jQuery("#loginmsg").html(response);
			jQuery(".loginform_save").hide();
			jQuery("#loginform_save").show();
		});
	return false;
	});
	
//save button_form

	jQuery("#button_form").submit(function()	{
		jQuery("#buttonmsg").html('<p class="save">' + message.saving + '</p>');		
		var section = 'button';
		var button_bg_color1 = jQuery("#button_bg_color1").val();
		var button_bg_color2 = jQuery("#button_bg_color2").val();
		var button_text_color = jQuery("#button_text_color").val();
		var button_border_color = jQuery("#button_border_color").val();
		var btn_hover_bg_color1 = jQuery("#btn_hover_bg_color1").val();
		var btn_hover_bg_color2 = jQuery("#btn_hover_bg_color2").val();
		var btn_hover_text_color = jQuery("#btn_hover_text_color").val();
		var btn_hover_border_color = jQuery("#btn_hover_border_color").val();		
		var buttonnonce = jQuery("#buttonnonce").val();
		var _wp_http_referer = jQuery("#button_form #_wp_http_referer").val();
		var data = {
			action: 'clp_save_settings',
			section: section,
			button_bg_color1: button_bg_color1,
			button_bg_color2: button_bg_color2,
			button_text_color: button_text_color,
			button_border_color: button_border_color,
			btn_hover_bg_color1: btn_hover_bg_color1,
			btn_hover_bg_color2: btn_hover_bg_color2,
			btn_hover_text_color: btn_hover_text_color,
			btn_hover_border_color: btn_hover_border_color,
			buttonnonce: buttonnonce,
			_wp_http_referer: _wp_http_referer
		};
		jQuery("#button_save").hide();
		jQuery(".button_save").show();
		jQuery.post(ajaxurl, data,
		function(response){
			jQuery("#buttonmsg").html(response);
			jQuery(".button_save").hide();
			jQuery("#button_save").show();
		});
	return false;
	});
	
//save message_form

	jQuery("#message_form").submit(function()	{
		jQuery("#messagemsg").html('<p class="save">' + message.saving + '</p>');		
		var section = 'message';
		var loggedout_text_color = jQuery("#loggedout_text_color").val();
		var loggedout_bg_color = jQuery("#loggedout_bg_color").val();
		var loggedout_border_color = jQuery("#loggedout_border_color").val();
		var error_text_color = jQuery("#error_text_color").val();
		var error_bg_color = jQuery("#error_bg_color").val();
		var error_border_color = jQuery("#error_border_color").val();
		var input_text_color = jQuery("#input_text_color").val();
		var input_bg_color = jQuery("#input_bg_color").val();
		var input_border_color = jQuery("#input_border_color").val();
		var messagenonce = jQuery("#messagenonce").val();
		var _wp_http_referer = jQuery("#message_form #_wp_http_referer").val();
		var data = {
			action: 'clp_save_settings',
			section: section,
			loggedout_text_color: loggedout_text_color,
			loggedout_bg_color: loggedout_bg_color,
			loggedout_border_color: loggedout_border_color,
			error_text_color: error_text_color,
			error_bg_color: error_bg_color,
			error_border_color: error_border_color,
			input_text_color: input_text_color,
			input_bg_color: input_bg_color,
			input_border_color: input_border_color,
			messagenonce: messagenonce,
			_wp_http_referer: _wp_http_referer
		};
		jQuery("#message_save").hide();
		jQuery(".message_save").show();
		jQuery.post(ajaxurl, data,
		function(response){
			jQuery("#messagemsg").html(response);
			jQuery(".message_save").hide();
			jQuery("#message_save").show();
		});
	return false;
	});
	
//save link_form

	jQuery("#link_form").submit(function()	{
		jQuery("#linkmsg").html('<p class="save">' + message.saving + '</p>');		
		var section = 'link';
		var link_text_color = jQuery("#link_text_color").val();
		var link_textdecoration = jQuery("#link_textdecoration").val();
		var link_shadow_x = jQuery("#link_shadow_x").val();
		var link_shadow_y = jQuery("#link_shadow_y").val();
		var link_shadow_softness = jQuery("#link_shadow_softness").val();
		var link_shadow_color = jQuery("#link_shadow_color").val();
		var hover_text_color = jQuery("#hover_text_color").val();
		var hover_textdecoration = jQuery("#hover_textdecoration").val();
		var hover_shadow_x = jQuery("#hover_shadow_x").val();
		var hover_shadow_y = jQuery("#hover_shadow_y").val();
		var hover_shadow_softness = jQuery("#hover_shadow_softness").val();
		var hover_shadow_color = jQuery("#hover_shadow_color").val();		
		var linknonce = jQuery("#linknonce").val();
		var _wp_http_referer = jQuery("#link_form #_wp_http_referer").val();
		var data = {
			action: 'clp_save_settings',
			section: section,
			link_text_color: link_text_color,
			link_textdecoration: link_textdecoration,
			link_shadow_x: link_shadow_x,
			link_shadow_y: link_shadow_y,
			link_shadow_softness: link_shadow_softness,
			link_shadow_color: link_shadow_color,
			hover_text_color: hover_text_color,
			hover_textdecoration: hover_textdecoration,
			hover_shadow_x: hover_shadow_x,
			hover_shadow_y: hover_shadow_y,
			hover_shadow_softness: hover_shadow_softness,
			hover_shadow_color: hover_shadow_color,
			linknonce: linknonce,
			_wp_http_referer: _wp_http_referer
		};
		jQuery("#link_save").hide();
		jQuery(".link_save").show();
		jQuery.post(ajaxurl, data,
		function(response){
			jQuery("#linkmsg").html(response);
			jQuery(".link_save").hide();
			jQuery("#link_save").show();
		});
	return false;
	});
	
});