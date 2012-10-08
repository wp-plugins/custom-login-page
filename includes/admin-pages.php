<?php

/**
 *
 * Admin Field Functions 
 *
 * @ Class A5 Option Page
 *
 * @ A5 Plugin Framework
 *
 * Gets all sort of input fields for the flexible A5 settings pages, using the A5_OptionPage Class
 * 
 */
 
/***************************************************************************************************
 
	List of field functions their parameters:

	a5_textarea($field_id, $field_name, [$value], [$rows], [$cols], [$label], [$style], [$class], [$space], [$echo])

	a5_checkbox($field_id, $field_name, $value, [$label], [$style], [$class], [$space], [$echo])

	a5_radio($field_id, $field_name, array($value), [$label], [$style], [$class], [$space], [$echo])

	a5_select($field_id, $field_name, $value, array($options), [$label], [$default], [$style], [$class], [$multiple], [$space], [$echo])

	a5_checkgroup($field_id, $field_name, array($value), [$label], [$checkall], [$style], [$class], [$space], [$echo])

	a5_hidden_field($field_id, $field_name, $value, [$echo])

	a5_text_field($field_id, $field_name, $value, [$label], [$size], [$style], [$class], [$space], [$echo])

	a5_color_field($field_id, $field_name, $value, [$label], [$size], [$style], [$class], [$space], [$echo])

	a5_date_field($field_id, $field_name, $value, [$label], [$size], [$style], [$class], [$space], [$echo])

	a5_datetime_field($field_id, $field_name, $value, [$label], [$size], [$style], [$class], [$space], [$echo])

	a5_datetime_local_field($field_id, $field_name, $value, [$label], [$size], [$style], [$class], [$space], [$echo])

	a5_email_field($field_id, $field_name, $value, [$label], [$size], [$style], [$class], [$space], [$echo])

	a5_month_field($field_id, $field_name, $value, [$label], [$size], [$style], [$class], [$space], [$echo])

	a5_number_field($field_id, $field_name, $value, [$label], [$size], [$step], [$min], [$max], [$style], [$class], [$space], [$echo])

	a5_range_field($field_id, $field_name, $value, $min, $max, [$label], [$step], [$size], [$style], [$class], [$space], [$echo])

	a5_search_field($field_id, $field_name, $value, [$label], [$size], [$style], [$class], [$space], [$echo])

	a5_tel_field($field_id, $field_name, $value, [$label], [$size], [$style], [$class], [$space], [$echo])

	a5_time_field($field_id, $field_name, $value, [$label], [$size], [$style], [$class], [$space], [$echo])

	a5_url_field($field_id, $field_name, $value, [$label], [$size], [$style], [$class], [$space], [$echo])

	a5_week_field($field_id, $field_name, $value, [$label], [$size], [$style], [$class], [$space], [$echo])

	a5_resize_textarea(array($field_id), [$echo])
	
/**************************************************************************************************/ 

$a5_option_page = new A5_OptionPage;
 
/**
 *
 * get the version
 *
 */

function a5_option_page_version() {
	
	return A5_OptionPage::version; 

}

/**
 *
 * function to get text area
 *
 */
 
function a5_textarea($field_id, $field_name, $value = false, $rows = false, $cols = false, $label = false, $style = false, $class = false, $space = false, $echo = false) {
	
	global $a5_option_page;
	
	$args = array ( 'type' => 'textarea',
					'field_id' => $field_id,
					'field_name' => $field_name,
					'value' => $value,
					'label' => $label,
					'style' => $style,
					'class' => $class,
					'rows' => $rows,
					'cols' => $cols,
					'space' => $space,
					'echo' => $echo
					);
					
	$textearea = $a5_option_page->input_field($args);
	
	if ($echo === false) return $textarea;
	
}

/**
 *
 * function to get checkbox
 *
 */
 
function a5_checkbox($field_id, $field_name, $value, $label = false, $style = false, $class = false, $space = false, $echo = false) {
	
	global $a5_option_page;
	
	$args = array ( 'type' => 'checkbox',
					'field_id' => $field_id,
					'field_name' => $field_name,
					'value' => $value,
					'label' => $label,
					'style' => $style,
					'class' => $class,
					'space' => $space,
					'echo' => $echo
					);
					
	$checkbox = $a5_option_page->input_field($args);
	
	if ($echo === false) return $checkbox;	
	
}

/**
 *
 * function to get radio buttons
 *
 */
 
function a5_radio($field_id, $field_name, $value, $label = false, $style = false, $class = false, $space = false, $echo = false) {
	
	global $a5_option_page;
	
	$args = array ( 'type' => 'radio',
					'field_id' => $field_id,
					'field_name' => $field_name,
					'options' => $value,
					'text' => $label,
					'style' => $style,
					'class' => $class,
					'space' => $space,
					'echo' => $echo
					);
					
	$radio = $a5_option_page->input_field($args);
	
	if ($echo === false) return $radio;
	
}

/**
 *
 * function to get pulldown menu
 *
 */
 
function a5_select($field_id, $field_name, $value, $options, $label = false, $default = false, $style = false, $class = false, $multiple = false, $space = false, $echo = false) {
	
	global $a5_option_page;
	
	$args = array ( 'type' => 'select',
					'field_id' => $field_id,
					'field_name' => $field_name,
					'value' => (array) $value,
					'options' => $options,
					'label' => $label,
					'default' => $default,
					'style' => $style,
					'class' => $class,
					'multiple' => $multiple,
					'space' => $space,
					'echo' => $echo
					);
					
	$select = $a5_option_page->input_field($args);
	
	if ($echo === false) return $select;
	
}

/**
 *
 * function to get a group of checkboxes
 *
 */
 
function a5_checkgroup($field_id, $field_name, $value, $label = false, $checkall = false, $style = false, $class = false, $space = false, $echo = false) {
	
	global $a5_option_page;
	
	$args = array ( 'type' => 'checkgroup',
					'field_id' => $field_id,
					'field_name' => $field_name,
					'options' => $value,
					'label' => $label,
					'checkall' => $checkall,
					'style' => $style,
					'class' => $class,
					'space' => $space,
					'echo' => $echo
					);
					
	$checkgroup = $a5_option_page->input_field($args);
	
	if ($echo === false) return $checkgroup;
	
}


/**
 *
 * function to resize text areas
 *
 */
 
function a5_resize_textarea($field_id, $echo = false) {
	
	if (!is_array($field_id)) $field_id=array($field_id);
	
	global $a5_option_page;
	
	$args = array ( 'type' => 'resize',
					'field_id' => $field_id,
					'echo' => $echo
					);
					
	$resize = $a5_option_page->input_field($args);
	
	if ($echo === false) return $resize;

}

/**
 *
 * function to get a hidden input field
 *
 */
 
function a5_hidden_field($field_id, $field_name, $value, $echo = false) {
	
	global $a5_option_page;
	
	$args = array ( 'type' => 'hidden',
					'field_id' => $field_id,
					'field_name' => $field_name,
					'value' => $value,
					'echo' => $echo
					);
					
	$hidden_field = $a5_option_page->input_field($args);
	
	if ($echo === false) return $hidden_field;
	
}

/**
 *
 * function to get a text input field
 *
 */
 
function a5_text_field($field_id, $field_name, $value, $label = false, $size = false, $style = false, $class = false, $space = false, $echo = false) {
	
	global $a5_option_page;
	
	$args = array ( 'type' => 'text',
					'field_id' => $field_id,
					'field_name' => $field_name,
					'value' => $value,
					'label' => $label,
					'size' => $size,
					'style' => $style,
					'class' => $class,
					'space' => $space,
					'echo' => $echo
					);
					
	$text_field = $a5_option_page->input_field($args);
	
	if ($echo === false) return $text_field;
	
}

/**
 *
 * function to get a color input field
 *
 */
 
function a5_color_field($field_id, $field_name, $value, $label = false, $size = false, $style = false, $class = false, $space = false, $echo = false) {
	
	global $a5_option_page;
	
	$args = array ( 'type' => 'color',
					'field_id' => $field_id,
					'field_name' => $field_name,
					'value' => $value,
					'label' => $label,
					'size' => $size,
					'style' => $style,
					'class' => $class,
					'space' => $space,
					'echo' => $echo
					);
					
	$color_field = $a5_option_page->input_field($args);
	
	if ($echo === false) return $color_field;
	
}

/**
 *
 * function to get a date input field
 *
 */
 
function a5_date_field($field_id, $field_name, $value, $label = false, $size = false, $style = false, $class = false, $space = false, $echo = false) {
	
	global $a5_option_page;
	
	$args = array ( 'type' => 'date',
					'field_id' => $field_id,
					'field_name' => $field_name,
					'value' => $value,
					'label' => $label,
					'size' => $size,
					'style' => $style,
					'class' => $class,
					'space' => $space,
					'echo' => $echo
					);
					
	$date_field = $a5_option_page->input_field($args);
	
	if ($echo === false) return $date_field;
	
}

/**
 *
 * function to get a datetime input field
 *
 */
 
function a5_datetime_field($field_id, $field_name, $value, $label = false, $size = false, $style = false, $class = false, $space = false, $echo = false) {
	
	global $a5_option_page;
	
	$args = array ( 'type' => 'datetime',
					'field_id' => $field_id,
					'field_name' => $field_name,
					'value' => $value,
					'label' => $label,
					'size' => $size,
					'style' => $style,
					'class' => $class,
					'space' => $space,
					'echo' => $echo
					);
					
	$datetime_field = $a5_option_page->input_field($args);
	
	if ($echo === false) return $datetime_field;
	
}

/**
 *
 * function to get a datetime-locat input field
 *
 */
 
function a5_datetime_local_field($field_id, $field_name, $value, $label = false, $size = false, $style = false, $class = false, $space = false, $echo = false) {
	
	global $a5_option_page;
	
	$args = array ( 'type' => 'datetime-local',
					'field_id' => $field_id,
					'field_name' => $field_name,
					'value' => $value,
					'label' => $label,
					'size' => $size,
					'style' => $style,
					'class' => $class,
					'space' => $space,
					'echo' => $echo
					);
					
	$datetime_local_field = $a5_option_page->input_field($args);
	
	if ($echo === false) return $datetime_local_field;
	
}

/**
 *
 * function to get an email input field
 *
 */
 
function a5_email_field($field_id, $field_name, $value, $label = false, $size = false, $style = false, $class = false, $space = false, $echo = false) {
	
	global $a5_option_page;
	
	$args = array ( 'type' => 'email',
					'field_id' => $field_id,
					'field_name' => $field_name,
					'value' => $value,
					'label' => $label,
					'size' => $size,
					'style' => $style,
					'class' => $class,
					'space' => $space,
					'echo' => $echo
					);
					
	$email_field = $a5_option_page->input_field($args);

	if ($echo === false) return $email_field;	
	
}


/**
 *
 * function to get a month input field
 *
 */
 
function a5_month_field($field_id, $field_name, $value, $label = false, $size = false, $style = false, $class = false, $space = false, $echo = false) {
	
	global $a5_option_page;
	
	$args = array ( 'type' => 'month',
					'field_id' => $field_id,
					'field_name' => $field_name,
					'value' => $value,
					'label' => $label,
					'size' => $size,
					'style' => $style,
					'class' => $class,
					'space' => $space,
					'echo' => $echo
					);
					
	$month_field = $a5_option_page->input_field($args);
	
	if ($echo === false) return $month_field;
	
}

/**
 *
 * function to get a number input field
 *
 */
 
function a5_number_field($field_id, $field_name, $value, $label = false, $size = false, $step = false, $min = false, $max = false, $style = false, $class = false, $space = false, $echo = false) {
	
	global $a5_option_page;
	
	$args = array ( 'type' => 'number',
					'field_id' => $field_id,
					'field_name' => $field_name,
					'value' => $value,
					'label' => $label,
					'size' => $size,
					'min' => $min,
					'max' => $max,
					'step' => $step,
					'style' => $style,
					'class' => $class,
					'space' => $space,
					'echo' => $echo
					);
					
	$number_field = $a5_option_page->input_field($args);
	
	if ($echo === false) return $number_field;
	
}

/**
 *
 * function to get a range input field
 *
 */
 
function a5_range_field($field_id, $field_name, $value, $min, $max, $label = false, $step = false, $size = false, $style = false, $class = false, $space = false, $echo = false) {
	 
	global $a5_option_page;
	
	$args = array ( 'type' => 'range',
					'field_id' => $field_id,
					'field_name' => $field_name,
					'value' => $value,
					'label' => $label,
					'size' => $size,
					'min' => $min,
					'max' => $max,
					'step' => $step,
					'style' => $style,
					'class' => $class,
					'space' => $space,
					'echo' => $echo
					);
					
	$range_field = $a5_option_page->input_field($args);
	
	if ($echo === false) return $range_field;
	
}

/**
 *
 * function to get a search input field
 *
 */
 
function a5_search_field($field_id, $field_name, $value, $label = false, $size = false, $style = false, $class = false, $space = false, $echo = false) {
	
	global $a5_option_page;
	
	$args = array ( 'type' => 'search',
					'field_id' => $field_id,
					'field_name' => $field_name,
					'value' => $value,
					'label' => $label,
					'size' => $size,
					'style' => $style,
					'class' => $class,
					'space' => $space,
					'echo' => $echo
					);
					
	$search_field = $a5_option_page->input_field($args);
	
	if ($echo === false) return $search_field;
	
}

/**
 *
 * function to get a tel input field
 *
 */
 
function a5_tel_field($field_id, $field_name, $value, $label = false, $size = false, $style = false, $class = false, $space = false, $echo = false) {
	
	global $a5_option_page;
	
	$args = array ( 'type' => 'tel',
					'field_id' => $field_id,
					'field_name' => $field_name,
					'value' => $value,
					'label' => $label,
					'size' => $size,
					'style' => $style,
					'class' => $class,
					'space' => $space,
					'echo' => $echo
					);
					
	$tel_field = $a5_option_page->input_field($args);
	
	if ($echo === false) return $tel_field;
	
}

/**
 *
 * function to get a time input field
 *
 */
 
function a5_time_field($field_id, $field_name, $value, $label = false, $size = false, $style = false, $class = false, $space = false, $echo = false) {
	
	global $a5_option_page;
	
	$args = array ( 'type' => 'time',
					'field_id' => $field_id,
					'field_name' => $field_name,
					'value' => $value,
					'label' => $label,
					'size' => $size,
					'style' => $style,
					'class' => $class,
					'space' => $space,
					'echo' => $echo
					);
					
	$time_field = $a5_option_page->input_field($args);
	
	if ($echo === false) return $time_field;
	
}

/**
 *
 * function to get a url input field
 *
 */
 
function a5_url_field($field_id, $field_name, $value, $label = false, $size = false, $style = false, $class = false, $space = false, $echo = false) {
	
	global $a5_option_page;
	
	$args = array ( 'type' => 'url',
					'field_id' => $field_id,
					'field_name' => $field_name,
					'value' => $value,
					'label' => $label,
					'size' => $size,
					'style' => $style,
					'class' => $class,
					'space' => $space,
					'echo' => $echo
					);
					
	$url_field = $a5_option_page->input_field($args);
	
	if ($echo === false) return $url_field;
	
}

/**
 *
 * function to get a week input field
 *
 */
 
function a5_week_field($field_id, $field_name, $value, $label = false, $size = false, $style = false, $class = false, $space = false, $echo = false) {
	
	global $a5_option_page;
	
	$args = array ( 'type' => 'week',
					'field_id' => $field_id,
					'field_name' => $field_name,
					'value' => $value,
					'label' => $label,
					'size' => $size,
					'style' => $style,
					'class' => $class,
					'space' => $space,
					'echo' => $echo
					);
					
	$week_field = $a5_option_page->input_field($args);
	
	if ($echo === false) return $week_field;
	
}

/***************************************************************************************************
 
	List of page functions with their parameters:

	a5_open_page($id, [$echo])
	
	a5_next_page($id, [$echo])
	
	a5_close_page([$echo])
	
	a5_open_section([$echo])
	
	a5_next_section([$echo])
	
	a5_close_section([$echo])
	
	a5_container_left(array($fields), [$echo])
	
	a5_container_right($headline, array($text), [$special], [array($message, $priority)], [$echo])
	
	a5_submit_button($name, $button_text, [$text], [$echo])
	
	a5_nav_js([$echo])
	
/**************************************************************************************************/

/**
 *
 * open a page
 *
 */
 
function a5_open_page($id, $echo = true){
	
	$eol = "\r\n";
	$tab = "\t";
	
	global $a5_option_page;
	
	$args = array ( 'id' => $id,
					'class' => 'tabcontent',
					'echo' => false
					);
	
	$page_start = $a5_option_page->container($args).' <!-- / page &#39;'.$id.'&#39; start -->';
	
	$page_start.= $eol.$tab.'<form method="post" name="'.$id.'_form" id="'.$id.'_form" action="">';
	
	if ($echo === false) return $page_start;
	
	echo $page_start;
	
}

/**
 *
 * change to next page
 *
 */
 
function a5_next_page($id, $echo = true){
	
	$output = a5_close_page(false).a5_open_page($id, false);
	
	if ($echo === false) return $output;
	
	echo $output;
	
}

/**
 *
 * close page
 *
 */
 
function a5_close_page($echo = true){
	
	$eol = "\r\n";
	$tab = "\t";
	
	$page_end = $eol.$tab.'</form>'.$eol.$tab.'</div> <!-- / page end -->';
	
	if ($echo === false) return $page_end;
	
	echo $page_end;
	
}

/**
 *
 * open a section
 *
 */
 
function a5_open_section($echo = true){
	
	global $a5_option_page;
	
	$args = array ( 'class' => 'a5-option-container',
					'echo' => $echo
					);
	
	$section_begin = $a5_option_page->container($args).' <!-- / section start -->';
	
	if ($echo === false) return $section_begin;
	
	echo $section_begin;
	
}

/**
 *
 * change to next section
 *
 */
 
function a5_next_section($echo = true){
	
	$output = a5_close_section(false).a5_open_section(false);
	
	if ($echo === false) return $output;
	
	echo $output;
	
}

/**
 *
 * close section
 *
 */
 
function a5_close_section($echo = true){
	
	$eol = "\r\n";
	$tab = "\t";
	
	$section_end = $eol.$tab.'</div> <!-- / section end -->';
	
	if ($echo === false) return $section_end;
	
	echo $section_end;
	
}

/**
 *
 * building the left container
 *
 */
 
function a5_container_left($fields, $echo = true){
	
	global $a5_option_page;
	
	$eol = "\r\n";
	$tab = "\t";
	
	$args = array ( 'class' => 'a5-option-container-left',
					'echo' => false
					);
	
	$container = $a5_option_page->container($args);
	
	foreach ($fields as $field) $container.=$field;
	
	$container.=$eol.$tab.'</div>';
	
	if ($echo === false) return $container;
		
	echo $container;
	
}

/**
 *
 * building the right container
 *
 */
 
function a5_container_right($headline, $text, $special = false, $message = false, $echo = true){
	
	global $a5_option_page;
	
	$eol = "\r\n";
	$tab = "\t";
	
	if ($message !== false) :
	
		$count = 2;
	
		$priority = $message[1];
	
		$message = $eol.$tab.'<div id="'.$message[0].'"></div>';
		
	endif;
	
	$special = ($special) ? $eol.$tab.'<p><i>'.$special.'</i></p>' : '';
	
	$args = array ( 'class' => 'a5-option-container-right',
					'echo' => false
					);
	
	$container = $a5_option_page->container($args);
	
	if ($priority == 1) $container.=$message;
	
	$container.=$eol.$tab.'<h2>'.$headline.'</h2>';
	
	if ($priority == 2) $container.=$message;
	
	foreach ($text as $schnummschnick) :
	
		$container.=$eol.$tab.'<p>'.$schnummschnick.'</p>';
		
		$count++;
		
		if ($priority == $count) $container.=$message;
		
	endforeach;
	
	$container.=$special;
					
	$container.=$eol.$tab.'</div>';
	
	$container.=$eol.$tab.'<div style="clear: both;"></div>';
	
	if ($echo === false) return $container;
		
	echo $container;
	
}

/**
 *
 * the submit button
 *
 */
 
function a5_submit_button($name, $button_text, $text = false, $echo = true){
	
	$eol = "\r\n";
	$tab = "\t";
	
	$text = ($text) ? $eol.$tab.'<span style="font-weight: bold; color:#243e1f">'.$text.'</span>' : '';
	
	$output = $eol.$tab.'<div id="submit-container"> <!-- / submit -->'.$eol.$tab.'<p class="submit">'.$eol.$tab;
	$output.= '<input class="save-tab" name="'.$name.'" id="'.$name.'" value="'.$button_text.'" type="submit">'.$eol.$tab;
	$output.= '<img src="'.admin_url('/images/wpspin_light.gif').'" alt="" class="'.$name.'" style="display: none;" />'.$eol.$tab;
	$output.= $text.'</p>'.$eol.$tab.'</div> <!-- / submit end -->';
	
	if ($echo === false) return $output;
		
	echo $output;
	
}

/**
 *
 * the javascript at the end of the whole thing
 *
 */
function a5_nav_js($plugin_shortname, $echo = true){
	
	$eol = "\r\n";
	$tab = "\t";
	
	$output = $eol.$tab.'<script type="text/javascript">'.$eol.$tab.'var pages=new ddtabcontent("'.$plugin_shortname.'-pagetabs") //enter ID of Tab Container';
	$output.= $eol.$tab.'pages.setpersist(true) //toogle persistence of the tabs&#39; state'.$eol.$tab.'pages.setselectedClassTarget("link") //"link" or "linkparent"';
	$output.= $eol.$tab.'pages.init()'.$eol.$tab.'</script>'.$eol.$tab;
	
	if ($echo === false) return $output;
		
	echo $output;
	
}

?>