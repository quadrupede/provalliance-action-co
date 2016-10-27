<?php 
class WCMCA_Address
{
	public function __construct()
	{
		add_action('wp_ajax_nopriv_wcmca_get_state_dropmenu', array(&$this, 'ajax_get_state_by_country_id'));
		add_action('wp_ajax_wcmca_get_state_dropmenu', array(&$this, 'ajax_get_state_by_country_id'));
		
		add_action('wp_ajax_wcmca_save_new_address', array(&$this, 'ajax_save_new_address'));
		add_action('wp_ajax_wcmca_delete_address', array(&$this, 'delete_address'));
		add_action('wp_ajax_wcmca_get_address_by_id', array(&$this, 'ajax_get_address_by_id'));
		
		//add_action('wp', array(&$this, 'check_if_address_has_to_be_deleted'));
		
	}
	/* function check_if_address_has_to_be_deleted()
	{
		if(isset($_GET['wcmca_delete']))
			$this->delete_address($_GET['wcmca_delete']);
	} */
	
	function get_woocommerce_address_fields_by_type($type, $country = null)
	{
		return WC()->countries->get_address_fields( !isset($country) ? get_user_meta( get_current_user_id(), $type . '_country', true ) : $country, $type . '_' );
	}
	function delete_address()
	{
		global $wcmca_customer_model;
		if(isset($_POST['wcmca_delete_id']))
		{
			$user_id = isset($_POST['wcmca_user_id']) ? $_POST['wcmca_user_id']:get_current_user_id();
			$wcmca_customer_model->delete_addresses( $user_id, $_POST['wcmca_delete_id']);
		}
		
		wp_die();
	}
	function ajax_get_state_by_country_id()
	{
		$country_id = isset($_POST['country_id']) ? $_POST['country_id']: null;
		$type = isset($_POST['type']) ? $_POST['type']: 'billing';
		if(!isset($country_id))
			wp_die();
		
		ob_start();
		$this->get_state_by_country($country_id, null, $type);
		$html_to_return = ob_get_clean();
		echo json_encode(array( 'html'=> $html_to_return, 'field_attributes_and_options' => $this->get_address_field_attributes_and_options_by_locale($country_id)));
		wp_die();
	}
	function ajax_get_address_by_id()
	{
		$address_id = isset($_POST['address_id']) ? $_POST['address_id']: null;
		if(!isset($address_id))
			wp_die();
		
		echo json_encode($this->get_address_by_id($address_id));
		wp_die();
	}
	function ajax_save_new_address()
	{
		global $wcmca_customer_model;
		//wcmca_var_dump($_POST);
		$bad_chars = array('\\', '"', "'");
		$address = array();
		
		//old method
		/* $address['address_id'] = isset($_POST['wcmca_address_id']) ? (int)$_POST['wcmca_address_id'] : -1;
		$address['field_name_field'] = isset($_POST['wcmca_field_name_field']) ? $_POST['wcmca_field_name_field'] : "";
		$address['first_name_field'] =isset( $_POST['wcmca_first_name_field']) ? $_POST['wcmca_first_name_field'] : "";
		$address['last_name_field'] = isset($_POST['wcmca_last_name_field']) ? $_POST['wcmca_last_name_field'] : "";
		$address['address_1_field'] = isset($_POST['wcmca_address_1_field']) ? $_POST['wcmca_address_1_field'] : "";
		$address['address_2_field'] = isset($_POST['wcmca_address_2_field']) ? $_POST['wcmca_address_2_field'] : "";
		$address['email_field'] = isset($_POST['wcmca_email_field']) ? $_POST['wcmca_email_field'] : "";
		$address['company_field'] = isset($_POST['wcmca_company_field']) ? $_POST['wcmca_company_field'] : "";
		$address['phone_field'] = isset($_POST['wcmca_phone_field']) ? $_POST['wcmca_phone_field'] : "";
		$address['country_field'] = isset($_POST['wcmca_country_field']) ? $_POST['wcmca_country_field'] : "";
		$address['state_field'] = isset($_POST['wcmca_state_field']) ? str_replace($bad_chars, "", $_POST['wcmca_state_field']) : "";
		$address['city_field'] = isset($_POST['wcmca_city_field']) ? $_POST['wcmca_city_field'] : "";
		$address['postcode_field'] = isset($_POST['wcmca_postcode_field']) ? $_POST['wcmca_postcode_field'] : "";
		$address['vat_number_field'] = isset($_POST['wcmca_vat_number_field']) ? $_POST['wcmca_vat_number_field'] : "";
		
		//wcbcf support
		$address['persontype_field'] = isset($_POST['wcmca_persontype_field']) ? $_POST['wcmca_persontype_field'] : "1";
		$address['cpf_field'] = isset($_POST['wcmca_cpf_field']) ? $_POST['wcmca_cpf_field'] : "";
		$address['rg_field'] = isset($_POST['wcmca_rg_field']) ? $_POST['wcmca_rg_field'] : "";
		$address['cnpj_field'] = isset($_POST['wcmca_cnpj_field']) ? $_POST['wcmca_cnpj_field'] : "";
		$address['ie_field'] = isset($_POST['wcmca_ie_field']) ? $_POST['wcmca_ie_field'] : "";
		$address['birthdate_field'] = isset($_POST['wcmca_birthdate_field']) ? $_POST['wcmca_birthdate_field'] : "";
		$address['sex_field'] = isset($_POST['wcmca_sex_field']) ? $_POST['wcmca_sex_field'] : "";
		$address['cellphone_field'] = isset($_POST['wcmca_cellphone_field']) ? $_POST['wcmca_cellphone_field'] : "";
		$address['neighborhood_field'] = isset($_POST['wcmca_neighborhood_field']) ? $_POST['wcmca_neighborhood_field'] : "";
		$address['number_field'] = isset($_POST['wcmca_number_field']) ? $_POST['wcmca_number_field'] : "";
		 */
		
		//new 
		//ToDo: Server validation
		foreach($_POST as $address_field_name => $address_field_value)
			if (strpos($address_field_name, 'wcmca_') !== false)
			{
				$field_name = str_replace('wcmca_', "", $address_field_name);
				$address[$field_name] = strpos($address_field_value, '-||-') !== false ? explode("-||-", $address_field_value) : $address_field_value;
			}
		$user_id = isset($_POST['wcmca_user_id']) ? $_POST['wcmca_user_id']:get_current_user_id();
		if($address['address_id'] == -1)
			$wcmca_customer_model->add_addresses($user_id, $address);
		else
			$wcmca_customer_model->update_addresses($user_id,$address['address_id'], $address);
		//wcmca_var_dump($address);
		wp_die();
	}
	function get_address_by_id($address_id)
	{
		global $wcmca_customer_model;
		/*$address = null;
		 if($address_id == 'def_shipping' || $address_id == 'def_billing')
		{
			$prefix = $address_id == 'def_shipping' ? 'shipping' : 'billing';
			$customer = get_user_meta( get_current_user_id());
			//$customer = new WC_Customer(get_current_user_id());
			$address = array('first_name_field' => $customer[$prefix.'_first_name'][0],
							  'last_name_field' => $customer[$prefix.'_last_name'][0],
							  'address_1_field' => $customer[$prefix.'_address_1'][0],
							  'address_2_field' => isset($customer[$prefix.'_address_2']) ? $customer[$prefix.'_address_2'][0] : "",
							  'email_field' => $address_id == 'def_billing' ? $customer[$prefix.'_email'][0] : "",
							  'company_field' => isset($customer[$prefix.'_company']) ? $customer[$prefix.'_company'][0] : "",
							  'phone_field' => $address_id == 'def_billing' ? $customer[$prefix.'_phone'][0] : "",
							  'state_field' => isset($customer[$prefix.'_state']) ? $customer[$prefix.'_state'][0] : "",
							  'city_field' => isset($customer[$prefix.'_city']) ? $customer[$prefix.'_city'][0] : "",
							  'postcode_field' => isset($customer[$prefix.'_postcode']) ? $customer[$prefix.'_postcode'][0] : ""
							  );
		} */
		
		return $wcmca_customer_model->get_address_by_id(get_current_user_id(), $address_id);
	}
	function get_address_field_attributes_and_options_by_locale($country_id)
	{
		$countries_obj   = new WC_Countries();
		$label_data =  $countries_obj->get_country_locale();
		return $label_data[$country_id];
	}
	function get_state_by_country($country_id, $default_value = null, $type = 'billing')
	{
		$countries_obj   = new WC_Countries();
		$states = $countries_obj->get_states( $country_id ); //paramenter -> GB, IT ... is the "value" selected in the $countries select box
		$label_data =  $countries_obj->get_country_locale();
		//wcmca_var_dump($label_data[$country_id]);
		if ( is_array( $states ) && empty( $states ) ) //Like Germany, it doesn't have a states/provinces
		{
			woocommerce_form_field('wcmca_'.$type.'_state', array(
							'type'       => 'hidden',
							'class'      => array( 'form-row-last' ),
							'label_class' => array( 'wcmca_form_label' ),
							//'placeholder'    => __('Select a state','woocommerce-multiple-customer-addresses'),
							'value'    => $states,
							'required' => false,
							'label'      => !isset($label_data[$country_id]['state']['label']) ? "&nbsp;": $label_data[$country_id]['state']['label'],
							'custom_attributes'  => array('required' => 'required')
							));
		}
		elseif(is_array($states)) //Ex.: Italy, Brazil
		{
			$reordered_states = array();
			$reordered_states[""] = __('Select one','woocommerce-multiple-customer-addresses');
			foreach($states as $state_code => $state_name)
				$reordered_states[$state_code] = $state_name;
			
			$required = isset($label_data[$country_id]['state']['required']) ? $label_data[$country_id]['state']['required'] : false;
			$custom_attributes = $required ? array('required' => 'required') : array();
			woocommerce_form_field('wcmca_'.$type.'_state', array(
							'type'       => 'select',
							'required'          => $required,
							'class'      => array( 'form-row-last' ),
							'label'      => !isset($label_data[$country_id]['state']['label']) ? "&nbsp;": $label_data[$country_id]['state']['label'],//__('State / Province','woocommerce-multiple-customer-addresses'),
							'label_class' => array( 'wcmca_form_label' ),
							'input_class' => array('wcmca-state-select2','not_empty'),
							//'placeholder'    => __('Select a state','woocommerce-multiple-customer-addresses'),
							'options'    => $reordered_states,
							'custom_attributes'  => $custom_attributes
							)
			);
		}
		else //$states is false. Ex.: UK
		{
			$required = isset($label_data[$country_id]['state']['required']) ? $label_data[$country_id]['state']['required'] : false;
			$custom_attributes = $required ? array('required' => 'required') : array();
			woocommerce_form_field('wcmca_'.$type.'_state', array(
						'type'       => 'text',
						'class'      => array( 'form-row-last' ),
						'required'          => $required,
						'input_class' => array('wcmca_input_field', 'not_empty'),
						'label'      => !isset($label_data[$country_id]['state']['label']) ? " ": $label_data[$country_id]['state']['label'],//__('State / Province','woocommerce-multiple-customer-addresses'),
						'label_class' => array( 'wcmca_form_label' ),
						'custom_attributes'  => $custom_attributes
						//'placeholder'    => __('State / Country','woocommerce-multiple-customer-addresses')
						)
						);
		}
	}
	
	function get_countries()
	{
		$countries_obj   = new WC_Countries();
		$countries   = $countries_obj->get_allowed_countries();//$countries_obj->__get('countries');
		//$default_country = $countries_obj->get_base_country();
		//$default_county_states = $countries_obj->get_states( $default_country ); //paramenter -> GB, IT ... is the "value" selected in the $countries select box
		$reordered_states = array();
		$reordered_states[""] = __('Select one','woocommerce-multiple-customer-addresses');
		foreach($countries as $country_code => $country_name)
			$reordered_states[$country_code] = $country_name;
		
		return $reordered_states;
	}
	function country_code_to_name($code)
	{
		$countries_obj   = new WC_Countries();
		return  isset($countries_obj->countries[ $code ])  ? $countries_obj->countries[ $code ]  : $code;
	}
	function state_code_to_name($country_code, $state_code)
	{
		$countries_obj   = new WC_Countries();
		$result = $countries_obj->get_states($country_code );
		if($result)
			return $result[$state_code];
		
		return false;
	}
	
}
?>