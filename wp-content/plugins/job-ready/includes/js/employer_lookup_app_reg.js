jQuery( document ).ready( function() {

	// Grabs the nonce from the nonce field
	var nonce = jQuery('[name="input_150"]').val();
	var form_id = jQuery('.gform_wrapper form').data('formid');
	var form_page = 3;
	
	jQuery(document).bind('gform_post_render', function(){
		jQuery('[name="input_145"]').change( function() {
			if(this.value == 'Create a new employer in our system')
			{
				jQuery('[name="input_147"]').val('NULL');
			}
			else
			{
				jQuery('[name="input_147"]').val('');
			}
		});
		
		jQuery('[name="input_146"]').autocomplete({
			source: function( request, response ) {
				jQuery.ajax({
	      			url: NECA_Ajax.ajaxurl,
	      			dataType: "jsonp",
	      			data: {
						action: 'neca_employer_lookup',
						nonce: nonce,
	      				keyword: request.term,

	      			},
	      			success: function( data ) {
	      				if (typeof data.formError !== 'undefined') 
	      				{
	          		        alert(data.message);
	          		        response( {} );
	     				}
	      				else
	      				{
	      					response( data );
	      				}
	      			},
	    		});
			},
			minLength: 3,
			
			select: function( event, ui ) {
		        jQuery('[name="input_148"]').val(ui.item.party_id);
		        jQuery('[name="input_147"]').val(ui.item.value);
		        
		        jQuery(document).trigger('gform_post_render', [form_id, form_page]);
		        
		        // Display the employer details
		        show_employer_details(ui.item.party_id);
			}
			
		});
		
	});
	
	
	function show_employer_details(employer_party_id)
	{
		// Clear the current employer details
		jQuery('.employer_details').html('Loading details...');
		
		// Add ui.autocomplete-loading class
		jQuery('.employer_details').addClass('ui-autocomplete-loading');
		
		jQuery.ajax({
  			url: NECA_Ajax.ajaxurl,
  			dataType: "html",
  			data: {
				action: 'jra_employer_lookup',
				nonce: nonce,
  				employer_party_id: employer_party_id,

  			},
  			success: function( data ) {
  				// Removes the ui-autocomplete-loading class
  				jQuery('.employer_details').removeClass('ui-autocomplete-loading');
  				
  				// Display the Employer Details beneath the search form
  				jQuery('.employer_details').html(data);
  			},
		});
	}
	
});