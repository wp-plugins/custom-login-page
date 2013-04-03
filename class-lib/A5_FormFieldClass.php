<?php

/**
 *
 * Class A5 Form Fields
 *
 * @ A5 Plugin Framework
 * Version: 0.9.3 alpha
 *
 * Gets all sort of input fields for plugins by Atelier 5
 *
 * The fields can be gotten directly from the class of from the field functions, provided with the class.
 *
 */

class A5_FormField {
	
	public $formfield;
	
	function __construct($args){
		
		extract($args);
		
		$eol = "\r\n";
		$tab = "\t";
		
		$id = (isset($field_id)) ? ' id="'.$field_id.'"' : '';
		$label = (isset($label)) ? '<label for="'.$field_id.'">'.$label.'</label>' : '';
		$name = (isset($field_name)) ? ' name="'.$field_name.'"' : '';
		$atts = '';
		
		// wrapping the fiel into paragraph tags, if wanted
		
		if (isset($attributes['space'])) :
			
			$space = true;
			
			unset($attributes['space']);
			
		endif;
		
		// getting all extra attributes to the fields (there is no sanitizing at the moment)
		
		if (isset($attributes)) foreach ($attributes as $attribute => $attr_value) $atts .= ' '.$attribute.'="'.$attr_value.'"';
		
		// getting different types of input elements	
		
		switch ($type) :
		
			case 'textarea' :
			
				$this->formfield = $eol.$tab.'<textarea'.$name.$id.$atts.'>'.$value.'</textarea>';
			
				break;
				
			case 'select' :
			
				if (empty($value)) $value[0] = false;
			
				$this->formfield = '<select'.$name.$id.$atts.'>';
				
				if (isset($default)) $this->formfield .= $eol.$tab.'<option value="" '.selected( $value[0], false, false ).'>'.$default.'</option>';
				
				foreach ($options as $option) :
				
					$selected = (in_array($option[0], $value)) ? ' selected="selected"' : '';
				
					$this->formfield .= $eol.$tab.'<option value="'.$option[0].'"'.$selected.' >'.$option[1].'</option>';
				
				endforeach;
				
				$this->formfield .= $eol.$tab.'</select>';
			
				break;
				
			case 'resize' :
			
				$this->formfield = $eol.'<script type="text/javascript"><!--'.$eol.'jQuery(document).ready(function() {';
																										   
				foreach ($field_id as $field) :
				
					$this->formfield .= $eol.$tab.'jQuery("#'.$field.'").autoResize();';
				
				endforeach;
				
				$this->formfield .= $eol.'});'.$eol.'--></script>'.$eol;
			
				break;
				
			default :
			
				$field_type = (isset($type)) ? ' type="'.$type.'"' : ' type="text"';
				
				if ('img' != $type) :
				
					$value = (isset($value)) ? ' value="'.$value.'"' : ' value=""';
					
				endif;
				
				$this->formfield = '<input'.$name.$id.$field_type.$value.$atts.' />'.$eol;
			
				break;
		
		endswitch;
		
		$this->formfield = (!strstr($type, 'checkbox') && !strstr($type, 'radio')) ? $eol.$tab.$label.$eol.$tab.$this->formfield : $eol.$tab.$this->formfield.$eol.$tab.$label;
		
		$this->formfield = (isset($space)) ? '<p>'.$this->formfield.$eol.'</p>'.$eol : $this->formfield;
		
		return $this->formfield;
		
	}
	
} // A5_FormField

?>