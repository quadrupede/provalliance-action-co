jQuery(document).ready(function() {

	 
});
function wcmca_init_custom_select2(type) // state || country
{
	if(typeof type === 'undefined')
		return;
	try{
		
		if(jQuery('.wcmca-'+type+'-select2').is('select'))
			jQuery('.wcmca-'+type+'-select2').select2({ 
			width: "100%",
			allowClear: true
		   });
	}catch(Error){};
}