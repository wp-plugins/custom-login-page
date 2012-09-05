<?php

/**
 *
 * Class A5 Option Page
 *
 * @ A5 Plugin Framework
 *
 * Gets all sort of input fields for the flexible A5 settings pages
 *
 */

class A5_OptionPage {
	
	const version = '1.0';
	
	public $page_item;
	
	public function input_field($args){
		
		extract($args);
		
		$eol = "\r\n";
		$tab = "\t";
		
		$style = ($style) ? ' style="'.$style.'"' : '';
		$cols = ($cols) ? ' cols="'.$cols.'"' : '';
		$rows = ($rows) ? ' rows="'.$rows.'"' : '';
		$min = ($min) ? ' min="'.$min.'"' : '';
		$max = ($max) ? ' max="'.$max.'"' : '';
		$step = ($step) ? ' step="'.$step.'"' : '';
		$size = ($size) ? ' size="'.$size.'"' : '';
		$class = ($class) ? ' class="'.$class.'"' : '';
		$label = ($label) ? '<label for="'.$field_name.'">'.$label.'</label>' : '';


		switch ($type) :
		
			case 'textarea' :
			
				$output = $eol.$tab.$label.$eol.$tab.'<textarea'.$class.' name="'.$field_name.'" id="'.$field_name.'"'.$cols.$rows.$style.'>'.$value.'</textarea>';
			
				break;
				
			case 'checkbox' :
			
				$output = $eol.$tab.$label.$eol.$tab.'<input'.$class.' name="'.$field_name.'" id="'.$field_name.'" type="checkbox" value="1" '.checked( 1, $value, false ).' '.$style.'/>';
			
				break;
				
			case 'radio' :
			
				$output = '';
			
				foreach ($text as $id => $label) :
			
					$output .= $eol.$tab.'<label for="'.$field_name.'-'.$id.'">'.$eol.$tab.$label.'</label>'.$eol.$tab.'<input'.$class.' id="'.$field_name.'-'.$id.'" name="'.$field_name.'" type="radio" value="'.$options[$id].'" '.checked( $options[$id], $value, false ).' '.$style.'/><br />';
					
				endforeach;
			
				break;
				
			case 'select' :
			
				$output = $eol.$tab.$label.$eol.$tab.'<select name="'.$field_name.'" id="'.$field_name.'"'.$class.$style.'>';
				
				if ($default) $output .= $eol.$tab.'<option value="" '.selected( $value, false, false ).'>'.$default.'</option>';
				
				foreach ($options as $option) :
				
					$output .= '<option value="'.$option[0].'" '.selected( $value, $option[0], false ).' >'.$option[1].'</option>';
				
				endforeach;
				
				$output .= $eol.$tab.'</select>';
			
				break;
				
			case 'checkgroup' :
			
				$output = ($text) ? '<p>'.$text.'</p>' : '';
				$output .= $eol.'<fieldset>'.$eol.'<p>'.$eol.$tab;
				
				foreach ($options as $option) :
				
					$output .= '<label for="'.$option[0].'">'.$eol.$tab.'<input id="'.$option[0].'" name="'.$option[0].'" type="checkbox" value="1" '.checked( 1, $option[1], false ).$class.$style.' />&nbsp;'.$option[2].$eol.$tab.'</label><br />'.$eol.$tab;
					
				endforeach;
				
				$output .= $eol.'</p>'.$eol;
				
				$output .= ($checkall) ? '<p>'.$eol.$tab.'<input id="checkall" name="'.$name_base.'[checkall]" type="checkbox"'.$class.$style.' />&nbsp;'.$checkall.$eol.'</p>'.$eol.'</fieldset>'.$eol : $eol.'</fieldset>'.$eol;
			
				break;
				
			case 'resize' :
			
				$output = $eol.'<script type="text/javascript"><!--'.$eol.'jQuery(document).ready(function() {';
																										   
				foreach ($field_name as $field) :
				
					$output .= $eol.$tab.'jQuery("#'.$field.'").autoResize();';
				
				endforeach;
				
				$output .= $eol.'});'.$eol.'--></script>'.$eol;
			
				break;
				
			default :
			
				$output = $eol.$tab.$label.$eol.$tab.'<input name="'.$field_name.'" id="'.$field_name.'" type="'.$type.'" value="'.$value.'"'.$min.$max.$step.$size.$class.$style.' />'.$eol;
			
				break;
		
		endswitch;
		
		$this->page_item = ($space) ? '<p>'.$output.$eol.'</p>'.$eol : $output;
		
		if ($echo === true) : 
		
			echo $this->page_item;
			
		else : 
		
			return $this->page_item;
			
		endif;
		
	} // input fields
	
	function container($args){
		
		extract($args);
		
		$eol = "\r\n";
		$tab = "\t";
		
		$id = ($id) ? ' id="'.$id.'"' : '';
		$style = ($style) ? ' style="'.$style.'"' : '';
		$class = ($class) ? ' class="'.$class.'"' : '';
		
		$this->page_item = $eol.$tab.'<div'.$id.$class.$style.'>';
		
		if ($echo === true) : 
	
			echo $this->page_item;
			
		else : 
		
			return $this->page_item;
			
		endif;
		
	} // container
	
} // A5_OptionPage

?>