var wcmca_force_state_change = false;
var wcmca_state_forced_value = "";
var wcmca_state_forced_value_type = "";
var wcmca_loading_in_progress = 0;
jQuery(document).ready(function()
{
	jQuery(document).on('change','.wcmca_address_select_menu',wcmca_on_address_select);
	jQuery(document).on('country_to_state_changed',wcmca_refresh_state_select); //no need to use
	wcmca_load_default_addresses();
});
function wcmca_load_default_addresses()
{
	var type = new Array('billing', 'shipping');
	for(var i=0; i<type.length; i++)
		if(jQuery('#wcmca_address_select_menu_'+type[i]).val() != 'last_used_'+type[i])
			jQuery('#wcmca_address_select_menu_'+type[i]).trigger('change');
}
function wcmca_on_address_select(event)
{
	if(event.target.value == 'none')
		return;
	
	var random = Math.floor((Math.random() * 1000000) + 999);
	var formData = new FormData();
	var formType = jQuery(event.currentTarget).data('type');
	formData.append('action', 'wcmca_get_address_by_id'); 
	formData.append('address_id', event.target.value); 
	
	//1. load ajax fields: call the ajax_get_address_by_id
	
	//UI
	wcmca_loading_address_start(formType);
	++wcmca_loading_in_progress;
	//if(wcmca_loading_in_progress == 1) //it shows only for the first load
		jQuery( document.body ).trigger( 'update_checkout' );
	
	jQuery.ajax({
		url: wcmca_ajax_url+"?nocache="+random,
		type: 'POST',
		data: formData,
		async: true,
		success: function (data) 
		{
			//UI	
			wcmca_loading_address_end(formType);
			//2. populate fields
			wcmca_fill_form_fields(data, formType);	
						
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
function wcmca_refresh_state_select(event)
{
	/*  console.log("state_changed");
	 console.log(wcmca_state_forced_value); */
	
	if(wcmca_force_state_change)
	{
		wcmca_force_state_change = false;
		if(jQuery("#"+wcmca_state_forced_value_type+"_state").css('display') == 'none') //is the select2
		{
			try{
				var $state_select2 = jQuery('#'+wcmca_state_forced_value_type+'_state').select2();
					$state_select2.val(wcmca_state_forced_value).trigger("change");
			}catch(error){}
		}
		else
			jQuery('#'+wcmca_state_forced_value_type+'_state').val(wcmca_state_forced_value);			
	}
}
function wcmca_reset_checkout_input_text_fields(type)
{
	jQuery('.woocommerce-'+type+'-fields').find('input').each(function(index, element)
	{
		if(jQuery(element).attr('type') == 'checkbox' || jQuery(element).attr('type') == 'radio')
			jQuery(element).prop('checked', false);
		else
			jQuery(element).val("");
	});
}
function wcmca_fill_form_fields(data, formType) //billing || shipping
{
	var result =  JSON.parse(data);
	
	/*if(formType == 'shipping')
		jQuery('#ship-to-different-address-checkbox').prop('checked',true);*/
	
	jQuery.each(result, function(element_name, value)
	{
		//if(element_name.indexOf(formType) !== -1 )
		//if(jQuery("#"+element_name).length )
		{
			value = value.indexOf("-||-") !== -1 ? value.split("-||-") : value;
			//Checkbox
			if(jQuery("#"+element_name).attr('type') == 'checkbox')
				jQuery("#"+element_name).prop('checked', 'checked');
			//Radio
			else if( value !== 'undefined' && typeof value.constructor !== 'Array' && jQuery("#"+element_name+'_field input').first().attr('type') == 'radio')
			{
				jQuery("#"+element_name+'_'+value).prop('checked', 'checked');
			}
			//Text and select
			else
			{
				jQuery('#'+element_name).val(value);
				try{
					var $generic_select2 = jQuery('#'+element_name).select2();
					$generic_select2.val(value);  
				}catch(error){}
			}				
		}
	});
	
	//STATE
	wcmca_force_state_change = true;
	wcmca_state_forced_value = formType == 'billing' ? result.billing_state : result.shipping_state;
	wcmca_state_forced_value_type = formType;
	
	//COUNTRY
	try{
		var $country_select2 = jQuery('#'+formType+'_country').select2();
		$country_select2.val(formType == 'billing' ? result.billing_country : result.shipping_country).trigger("change");  
	}catch(error){}
	
}