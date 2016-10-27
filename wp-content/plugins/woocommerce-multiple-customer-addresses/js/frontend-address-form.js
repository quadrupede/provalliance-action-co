var wcmca_is_edit_first_open = false;
var wcmca_preselected_state = "";
var select_country_ajax_request = 0;
var wcmca_ajax_loader;
jQuery(document).ready(function()
{
	//jQuery(document).on('click', '#wcmca_add_new_address_button, #wcmca_add_new_address_button_billing, #wcmca_add_new_address_button_shipping', wcmca_on_show_address_form);
	jQuery(document).on('click','#wcmca_close_address_form_button_billing, #wcmca_close_address_form_button_shipping' , wcmca_on_hide_address_form);
	//jQuery(document).on('click','#wcmca_form_background_overlay' , wcmca_on_hide_address_form);
	jQuery(document).on('click','.wcmca_delete_address_button' , wcmca_delete_address);
	jQuery(document).on('click','button#wcmca_save_address_button_billing' , wcmca_save_address_billing);
	jQuery(document).on('click','button#wcmca_save_address_button_shipping' , wcmca_save_address_shipping);
	jQuery(document).on('change','#wcmca_billing_country' , wcmca_on_billing_country_selection);
	jQuery(document).on('change','#wcmca_shipping_country' , wcmca_on_shipping_country_selection);
	jQuery(document).on('click','.wcmca_edit_address_button' , wcmca_edit_address);
	jQuery('#wcmca_billing_country, #wcmca_shipping_country').trigger('change');
	
	jQuery('.wcmca_add_new_address_button, #wcmca_add_new_address_button_billing, #wcmca_add_new_address_button_shipping, .wcmca_edit_address_button').magnificPopup({
          type: 'inline',
		  showCloseBtn:false,
          preloader: false,
            callbacks: {
            
			
			beforeOpen: function() {
              wcmca_reset_input_text_fields();
            }
			 /* close: function(event) {
				  wcmca_on_hide_address_form(event)
				} */
          } 
        });
	
    //UI	
	if (typeof wcmca_init_custom_select2 == 'function')
		wcmca_init_custom_select2('country');
});
/* function wcmca_on_show_address_form(event)
{
	event.stopImmediatePropagation();
	event.preventDefault();
	
	jQuery('#wcmca_address_id').val(-1);
	
	wcmca_show_address_form();
	return false;
} */
function wcmca_reset_input_text_fields()
{
	jQuery("#wcmca_billing_country").val("");
	wcmca_end_loading_state_field('billing');
	wcmca_end_loading_state_field('shipping');
	
	jQuery('input.wcmca_input_field.input-text').val("");
	jQuery('input.input-checkbox:not(#ship-to-different-address-checkbox)').prop('checked', false);
	//only for my account, sets checkboxes according to span values
	jQuery('#wcmca_address_details_billing span, #wcmca_address_details_shipping span').each(function(index, element)
	{
		if(jQuery("#wcmca_"+field_name).attr('type') == 'checkbox' && jQuery("#wcmca_"+field_name).attr('default') == 1)
			jQuery("#wcmca_"+field_name).prop('checked', 'checked');
		else if(jQuery("#wcmca_"+field_name).attr('type') == 'checkbox')
			jQuery("#wcmca_"+field_name).prop('checked', false)
	});
	jQuery('#wcmca_address_id_billing').val(-1);
	jQuery('#wcmca_address_id_shipping').val(-1);
};
function wcmca_on_hide_address_form(event)
{
	if(typeof event !== 'undefined' && event != null)
	{
		event.stopImmediatePropagation();
		event.preventDefault();
	}
	wcmca_hide_address_form();
	return false;
}
function wcmca_on_billing_country_selection(event)
{
	wcmca_on_country_selection('billing', event.target.value);
}
function wcmca_on_shipping_country_selection(event)
{
	wcmca_on_country_selection('shipping', event.target.value);
}
function wcmca_on_country_selection(type, id)
{
	var random = Math.floor((Math.random() * 1000000) + 999);
	var formData = new FormData();
	formData.append('action', 'wcmca_get_state_dropmenu'); 
	formData.append('type', type); 
	formData.append('country_id', id);
	
	if(id == "")
	{
		jQuery("#wcmca_billing_country").val("");
		//UI
		if(typeof wcmca_ajax_loader !== 'undefined')
			try{
				wcmca_ajax_loader.abort();
			}
			catch(e){};
		wcmca_end_loading_state_field(type);
		wcmca_remove_state_field(type);
		return;
	}
	
	//UI	
	wcmca_start_loading_state_field(type);
	select_country_ajax_request++;
	wcmca_ajax_loader = jQuery.ajax({
		url: wcmca_ajax_url+"?nocache="+random,
		type: 'POST',
		data: formData,
		async: true,
		success: function (data) 
		{
			//UI	
			wcmca_end_loading_state_field(type);
			
			select_country_ajax_request--;
			var result = jQuery.parseJSON(data);
			jQuery('#wcmca_country_field_container_'+type).html(result.html);
			
			//UI
			wcmca_update_fields_options_and_attributes(result.field_attributes_and_options, type);
			if (typeof wcmca_init_custom_select2 == 'function')
				wcmca_init_custom_select2('state');
			
			if(select_country_ajax_request == 0  && wcmca_is_edit_first_open)
			{
				wcmca_is_edit_first_open = false;
				if(wcmca_preselected_state != "")
					jQuery('#wcmca_'+type+'_state').val(wcmca_preselected_state);
			}
						
		},
		error: function (data) 
		{
			select_country_ajax_request--;
			//console.log(data);
			//alert("Error: "+data);
		},
		cache: false,
		contentType: false,
		processData: false
	});
}
function wcmca_delete_address(event)
{
	event.stopImmediatePropagation();
	event.preventDefault();
	
	var random = Math.floor((Math.random() * 1000000) + 999);
	var id = jQuery(event.currentTarget).data('id');
	var formData = new FormData();
	formData.append('action', 'wcmca_delete_address');
	formData.append('wcmca_delete_id', id);
	if(jQuery(event.currentTarget).data('user-id'))
		formData.append('wcmca_user_id', jQuery(event.currentTarget).data('user-id'));
	
	if(confirm(wcmca_confirm_delete_message))
	{
		//UI
		wcmca_show_saving_loader();
	
		jQuery.ajax({
			url: wcmca_ajax_url+"?nocache="+random,
			type: 'POST',
			data: formData,
			async: true,
			success: function (data) 
			{
				window.location.href = wcmca_current_url+'#wcmca_custom_addresses';
				location.reload(true);
							
			},
			error: function (data) 
			{
				//console.log(data);
				//alert("Error: "+data);
			},
			cache: false,
			contentType: false,
			processData: false
		});
		
	}
	
	return false;
}
function wcmca_save_address_shipping(event)
{
	event.stopImmediatePropagation();
	event.preventDefault();
	wcmca_save_address('shipping');
}
function wcmca_save_address_billing(event)
{
	event.stopImmediatePropagation();
	event.preventDefault();
	wcmca_save_address('billing');
}
function wcmca_save_address(type)
{ 
	//console.log("saving");
	var type = type;
	var random = Math.floor((Math.random() * 1000000) + 999);
	var formData = new FormData();
	var error = false;
	var data_to_send = new Array();
	formData.append('action', 'wcmca_save_new_address');
	formData.append('wcmca_type', type);
	
	jQuery('div#wcmca_address_form_'+type+' input, div#wcmca_address_form_'+type+' select').each(function(index, obj)
	{
		if(jQuery(this).hasClass('not_empty') && (!this.value || this.value==""))
		{
			wcma_highlight_empty_field(this);
			error = true;
		}
		else
		{
			//No longer used -> is used the .serialize() method
			//formData.append(this.name, this.value);
		}
	});
	//No longer used -> is used the .serialize() method
	/* jQuery('div#wcmca_address_form_'+type+' select').each(function(index, obj)
	{
		formData.append(this.name, this.value);
	}); */
	
	//console.log(error)
	if(error)
	{
		// event.stopImmediatePropagation();
		// event.preventDefault();
		return false;
	}
	
	var serialized_data = jQuery('#wcmca_address_form_fieldset_'+type+' input, #wcmca_address_form_fieldset_'+type+' select').serializeArray();
	jQuery.each(serialized_data,function(key,input){
        //formData.append(input.name,input.value);
		if(typeof data_to_send[input.name] === 'undefined')
			data_to_send[input.name] = new Array();
		data_to_send[input.name].push(input.value);
    }); 
	for (var elem_name in data_to_send)
		if(data_to_send[elem_name].length == 1)
			formData.append(elem_name, data_to_send[elem_name][0]);
		else
			formData.append(elem_name, data_to_send[elem_name].join("-||-"))
	
	//UI
	wcmca_on_hide_address_form(null);
	wcmca_show_saving_loader(type);
	
	jQuery.ajax({
		url: wcmca_ajax_url+"?nocache="+random,
		type: 'POST',
		data:formData,
		async: true,
		success: function (data) 
		{
			//UI	
			wcmca_end_loading_state_field(type);
			setTimeout(function(){ window.location.href = wcmca_current_url+'#wcmca_custom_addresses'; location.reload(true); }, 500);
						
		},
		error: function (data) 
		{
			//console.log(data);
			//alert("Error: "+data);
		},
		cache: false,
		contentType: false,
		processData: false
	});
	return false;
}
function wcmca_edit_address(event)
{
	var id = jQuery(event.currentTarget).data('id');
	var type = jQuery(event.currentTarget).data('type');
	jQuery('#wcmca_address_id_'+type).val(id);
	/* console.log(id);
	console.log(jQuery('#wcmca_address_id').val()); */
	
	jQuery('#wcmca_address_details_'+id+' span').each(function(index, element)
	{
		var value = jQuery(element).text().trim();
		var data = String(jQuery(element).data('code'));
		//var field_name = jQuery(element).attr('id').replace('wcmca_','');
		var field_name = jQuery(element).data('name');
		data = data !== 'undefined' && data.indexOf("-||-") !== -1 ? data.split("-||-") : data;
		
		//Special field
		/* if(field_name == 'billing_is_default_address' || field_name == 'shipping_is_default_address')
		{
			jQuery("#wcmca_"+field_name).prop('checked', jQuery(element).text() == 'yes' ? 'checked' : false)
		} */
		//Checkbox
		if(jQuery("#wcmca_"+field_name).attr('type') == 'checkbox')
			jQuery("#wcmca_"+field_name).prop('checked', value != "" ? 'checked' : false);
		
		//Radio
		else if( data !== 'undefined' && typeof data.constructor !== 'Array' && jQuery("#wcmca_"+field_name+'_field input').first().attr('type') == 'radio' /*  jQuery("#wcmca_"+field_name+'_'+data).attr('type') == 'radio' */)
		{
			//console.log(jQuery("#wcmca_"+field_name+'_'+data));
			jQuery("#wcmca_"+field_name+'_'+data).prop('checked', 'checked');
		}
		//Text, select and hidden
		else	
			jQuery("#wcmca_"+field_name).val(data === 'undefined' ? value : data);
		
		if(field_name == 'billing_state' || field_name == 'shipping_state')
			wcmca_preselected_state = data ;
	});
	
	//1. set state select box
	wcmca_is_edit_first_open = true;
	//wcmca_preselected_state = jQuery('#wcmca_state_'+id).data('code') ;
	//jQuery('#wcmca_country_field').trigger('change');
	if(type == 'billing')
		jQuery('#wcmca_billing_country').trigger('change');
	else
		jQuery('#wcmca_shipping_country').trigger('change');
	
	return false;
}