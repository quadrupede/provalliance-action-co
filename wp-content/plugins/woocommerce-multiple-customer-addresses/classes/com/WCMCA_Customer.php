<?php 
class WCMCA_Customer
{
	public function __construct()
	{
		
	} 
	private function array_sort($array, $on, $order=SORT_ASC)
	{

		$new_array = array();
		$sortable_array = array();

		if (count($array) > 0) {
			foreach ($array as $k => $v) {
				if (is_array($v)) {
					foreach ($v as $k2 => $v2) {
						if ($k2 == $on) {
							$sortable_array[$k] = $v2;
						}
					}
				} else {
					$sortable_array[$k] = $v;
				}
			}

			switch ($order) {
				case SORT_ASC:
					asort($sortable_array);
					break;
				case SORT_DESC:
					arsort($sortable_array);
					break;
			}

			foreach ($sortable_array as $k => $v) {
				$new_array[$k] = $array[$k];
			}
		}

		return $new_array;
	}
	private function unset_old_addresses_default($type, $old_addresses)
	{
		foreach($old_addresses as $key => $old_address)
			if(isset($old_address[$type."_is_default_address"]))
			{
				//wcmca_var_dump($key);
				unset($old_addresses[$key][$type."_is_default_address"]);
			}
			
		//wcmca_var_dump($old_addresses);
		return $old_addresses;
	}
	public function update_addresses($user_id, $address_id, $new_address)
	{
		$old_addresses = $this->get_addresses($user_id);
		$this->check_address_identifier_field($new_address);
		if($old_addresses)
		{
			//old default reset
			if(isset($new_address[$new_address['type']."_is_default_address"]))
			{
				//wcsts_var_dump($new_address[$new_address['type']."_is_default_address"]);
				$old_addresses = $this->unset_old_addresses_default($new_address['type'],$old_addresses);	
			}
			$old_addresses[$address_id] = $new_address;
		}
		else
			$old_addresses = array($address_id => $new_address);
		update_user_meta( $user_id, '_wcmca_additional_addresses', $old_addresses );
	}
	public function delete_addresses($user_id, $address_id)
	{
		if(!$user_id)
			return;
		$old_addresses = $this->get_addresses($user_id);
		
		if($old_addresses && isset($old_addresses[$address_id]))
			unset($old_addresses[$address_id]);
		
		update_user_meta($user_id, '_wcmca_additional_addresses', $old_addresses );
	}
	public function add_addresses($user_id, $new_address)
	{
		$old_addresses = $this->get_addresses($user_id);
		$this->check_address_identifier_field($new_address);
		if($old_addresses)
		{
			//old default reset
			$old_addresses = $this->unset_old_addresses_default($new_address['type'],$old_addresses);
			//$new_address['address_id'] = count($old_addresses); //useless, the address_id will be the index inside the address array
			$old_addresses[] = $new_address;
		}
		else
		{
			//$new_address['address_id'] = 0; //useless, the address_id will be the index inside the address array
			$old_addresses = array($new_address);
		}
		if(!add_user_meta( $user_id, '_wcmca_additional_addresses', $old_addresses, true ))
			wcmca_var_dump(update_user_meta( $user_id, '_wcmca_additional_addresses', $old_addresses ));
	}
	private function check_address_identifier_field(&$new_address)
	{
		$type = $new_address['type'];
		$bad_chars = array("/", "\\", "'", '"');
		if(!isset($new_address['address_internal_name']))
			$new_address['address_internal_name'] = $new_address[$type.'_first_name']." ".
													$new_address[$type.'_last_name']." - ".
													(isset($new_address[$type.'_company']) && $new_address[$type.'_company'] != "" ? $new_address[$type.'_company']." - " : "").
													$new_address[$type.'_address_1']." ".
													//$new_address[$type.'_postcode']." ".
													(isset($new_address[$type.'_address_2']) && $new_address[$type.'_address_2'] != "" ? $new_address[$type.'_address_2']." - " : " - ").
													$new_address[$type.'_city'].
													(isset($new_address[$type.'_state']) && $new_address[$type.'_state'] != "" ? ", ".$new_address[$type.'_state'] : "");
		
		$new_address['address_internal_name'] = str_replace($bad_chars, "", $new_address['address_internal_name']);
	}
	public function get_addresses($user_id)
	{
		$result = get_user_meta($user_id, '_wcmca_additional_addresses', true);
		$result = !is_array($result) ? array() : $this->array_sort($result, 'address_internal_name');
		
		return $result;
	}
	public function get_addresses_by_type($user_id)
	{
		$result = array('billing'=>array(), 'shipping'=>array());
		foreach((array)$this->get_addresses($user_id) as $address_id => $address)
		{
			if(!isset($address['type']))
			{
				$this->delete_addresses($user_id, $address_id);
				continue;
			}
			
			$result[$address['type']][$address_id] = $address;
		}
		return $result;
	}
	public function get_address_by_id($user_id, $address_id)
	{
		if($address_id == "last_used_billing" || $address_id == "last_used_shipping")
		{
			$prefix = $address_id == 'last_used_shipping' ? 'shipping' : 'billing';
			$customer = get_user_meta( $user_id);
			//wcmca_var_dump($customer);
			//$customer = new WC_Customer(get_current_user_id());
			
			//Old method
			/* $result = array(  'first_name_field' => $customer[$prefix.'_first_name'][0],
							  'last_name_field' => $customer[$prefix.'_last_name'][0],
							  'address_1_field' => $customer[$prefix.'_address_1'][0],
							  'address_2_field' => isset($customer[$prefix.'_address_2']) ? $customer[$prefix.'_address_2'][0] : "",
							  'email_field' => isset($customer[$prefix.'_email']) ? $customer[$prefix.'_email'][0] : "",
							  'company_field' => isset($customer[$prefix.'_company']) ? $customer[$prefix.'_company'][0] : "",
							  'phone_field' => isset($customer[$prefix.'_phone']) ? $customer[$prefix.'_phone'][0] : "",
							  'state_field' => isset($customer[$prefix.'_state']) ? $customer[$prefix.'_state'][0] : "",
							  'city_field' => isset($customer[$prefix.'_city']) ? $customer[$prefix.'_city'][0] : "",
							  'country_field' => isset($customer[$prefix.'_country']) ? $customer[$prefix.'_country'][0] : "",
							  'postcode_field' => isset($customer[$prefix.'_postcode']) ? $customer[$prefix.'_postcode'][0] : ""
							  );
							  
			if ( wcmca_is_wcbcf_active())
			{
				$result['persontype_field'] = isset($customer[$prefix.'_persontype']) ? $customer[$prefix.'_persontype'][0] : "";
				$result['cpf_field'] = isset($customer[$prefix.'_cpf']) ? $customer[$prefix.'_cpf'][0] : "";
				$result['rg_field'] = isset($customer[$prefix.'_rg']) ? $customer[$prefix.'_rg'][0] : "";
				$result['cnpj_field'] = isset($customer[$prefix.'_cnpj']) ? $customer[$prefix.'_cnpj'][0] : "";
				$result['ie_field'] = isset($customer[$prefix.'_ie']) ? $customer[$prefix.'_ie'][0] : "";
				$result['birthdate_field'] = isset($customer[$prefix.'_birthdate']) ? $customer[$prefix.'_birthdate'][0] : "";
				$result['sex_field'] = isset($customer[$prefix.'_sex']) ? $customer[$prefix.'_sex'][0] : "";
				$result['cellphone_field'] = isset($customer[$prefix.'_cellphone']) ? $customer[$prefix.'_cellphone'][0] : "";
				$result['neighborhood_field'] = isset($customer[$prefix.'_neighborhood']) ? $customer[$prefix.'_neighborhood'][0] : "";
				$result['number_field'] = isset($customer[$prefix.'_number']) ? $customer[$prefix.'_number'][0] : "";
			} */
			
			//new method
			$result = array('type' => $prefix);
			foreach($customer as $meta_field_name => $meta_field_value)
			{
				if(isset($meta_field_value[0]) && strpos($meta_field_name, $prefix) !== false)
				{
					/* wcmca_var_dump($meta_field_name);
					wcmca_var_dump($meta_field_value); */
					$result[$meta_field_name] = stripslashes($meta_field_value[0]);
				}
			}
		}
		else
		{
			$result = get_user_meta($user_id, '_wcmca_additional_addresses', true);
			$result = is_array($result) && isset($result[$address_id]) ? $result[$address_id] : array();
		}
		
		return $result;
	}
}
?>