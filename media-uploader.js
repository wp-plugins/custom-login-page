// Uploading files

var file_frame;

jQuery(document).ready(function(){
	
	if (jQuery("[id*='_url']").val()=='') {
		
		jQuery('#upload').show();
		jQuery('#preview').hide();
		jQuery('#remove').hide();
	
	} else {
		
		jQuery('#upload').hide();
		jQuery('#preview').show();
		jQuery('#remove').show();
		
	}
	
	jQuery('.upload-button').on('click', function( event ){
	
	event.preventDefault();
	
	// If the media frame already exists, reopen it.
	if ( file_frame ) {
	  file_frame.open();
	  return;
	}
	
	// Create the media frame.
	file_frame = wp.media.frames.file_frame = wp.media({
	  title: jQuery( this ).data( 'uploader_title' ),
	  button: {
		text: jQuery( this ).data( 'uploader_button_text' ),
	  },
	  multiple: false  // Set to true to allow multiple files to be selected
	});
	
	// When an image is selected, run a callback.
	file_frame.on( 'select', function() {
	  // We set multiple to false so only get one image from the uploader
	  img = file_frame.state().get('selection').first();
	
	  jQuery("[id$='_url']").val(img.attributes.url);
	  jQuery('#preview>img').attr('src', img.attributes.url);
	  jQuery('#upload').hide();
	  jQuery('#preview').show();
	  jQuery('#remove').show();
	
	});
	
	// Finally, open the modal
	file_frame.open();
	
	});
	
	jQuery('.remove-button').on('click', function(){
	
	  jQuery("[id$='_url']").val('');
	  jQuery('#upload').show();
	  jQuery('#preview>img').attr('src', '');
	  jQuery('#preview').hide();
	  jQuery('#remove').hide();
	
	});
	 
});