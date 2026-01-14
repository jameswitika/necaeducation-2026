jQuery( document ).ready( function() {

	// Grabs the nonce from the nonce field
	var nonce = jQuery('[name="input_135"]').val();
	
	jQuery(document).bind('gform_post_render', function(){
		jQuery('[name="input_136"]').change( function() {
			if(this.value == 'Create a new employer in our system')
			{
				jQuery('[name="input_139"]').val('NULL');
			}
			else
			{
				jQuery('[name="input_139"]').val('');
			}
		});
		
		jQuery('[name="input_134"]').autocomplete({
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
		        jQuery('[name="input_126"]').val(ui.item.party_id);
		        jQuery('[name="input_139"]').val(ui.item.value);
		        
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