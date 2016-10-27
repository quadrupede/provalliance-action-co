<?php 
class WCMCA_Option
{
	public function __construct()
	{
	}
	public function is_vat_identification_number_enabled()
	{
		add_filter('acf/settings/current_language',  array(&$this, 'cl_acf_set_language'), 100);
		
		$is_vat_identification_number_enabled = get_field('wcmca_vat_idetification_field', 'option');
		$is_vat_identification_number_enabled = $is_vat_identification_number_enabled != null ? (boolean)$is_vat_identification_number_enabled : false;
		//wcmca_var_dump($is_vat_identification_number_enabled);
		remove_filter('acf/settings/current_language', array(&$this,'cl_acf_set_language'), 100);
		
		return $is_vat_identification_number_enabled;
	}
	public function is_vat_identification_number_required()
	{
		add_filter('acf/settings/current_language',  array(&$this, 'cl_acf_set_language'), 100);
		
		$is_vat_identification_number_required = get_field('wcmca_vat_identification_enable_required', 'option');
		$is_vat_identification_number_required = $is_vat_identification_number_required != null && $is_vat_identification_number_required == 'yes'? true : false;
		
		remove_filter('acf/settings/current_language', array(&$this,'cl_acf_set_language'), 100);
		
		return $is_vat_identification_number_required;
	}
	public function get_required_fields()
	{
		$fields = array();
		
		add_filter('acf/settings/current_language',  array(&$this, 'cl_acf_set_language'), 100);
		$fields['billing_first_and_last_name_disable_required'] = get_field('wcmca_billing_first_and_last_name_disable_required', 'option');
		$fields['billing_first_and_last_name_disable_required'] = $fields['billing_first_and_last_name_disable_required'] != null && $fields['billing_first_and_last_name_disable_required'] == 'yes' ? true : false;
		
		$fields['shipping_first_and_last_name_disable_required'] = get_field('wcmca_shipping_first_and_last_name_disable_required', 'option');
		$fields['shipping_first_and_last_name_disable_required'] = $fields['shipping_first_and_last_name_disable_required'] != null && $fields['shipping_first_and_last_name_disable_required'] == 'yes' ? true : false;
		
		$fields['billing_company_name_enable_required'] = get_field('wcmca_billing_company_name_enable_required', 'option');
		$fields['billing_company_name_enable_required'] = $fields['billing_company_name_enable_required'] != null && $fields['billing_company_name_enable_required'] == 'yes' ? true : false;
		
		$fields['shipping_company_name_enable_required'] = get_field('wcmca_shipping_company_name_enable_required', 'option');
		$fields['shipping_company_name_enable_required'] = $fields['shipping_company_name_enable_required'] != null && $fields['shipping_company_name_enable_required'] == 'yes' ? true : false;
		
		remove_filter('acf/settings/current_language', array(&$this,'cl_acf_set_language'), 100);
		return $fields;
	}
	public function is_identifier_field_disabled()
	{
		$result = true;
		
		add_filter('acf/settings/current_language',  array(&$this, 'cl_acf_set_language'), 100);
		$result = get_field('wcmca_disable_identifier_field', 'option');
		$result = $result != null && $result == 'yes' ? true : false;
		remove_filter('acf/settings/current_language', array(&$this,'cl_acf_set_language'), 100);
		
		return $result;
	}
	public function display_fields_labels()
	{
		$result = true;
		
		add_filter('acf/settings/current_language',  array(&$this, 'cl_acf_set_language'), 100);
		$result = get_field('wcmca_my_account_page_display_fields_labels', 'option');
		$result = $result != null && $result == 'yes' ? false : true;
		remove_filter('acf/settings/current_language', array(&$this,'cl_acf_set_language'), 100);
		
		return $result;
	}
	public function which_addresse_type_are_disabled()
	{
		$addresses = array();
		
		add_filter('acf/settings/current_language',  array(&$this, 'cl_acf_set_language'), 100);
		$addresses['billing'] = get_field('wcmca_disable_billing_multiple_addresses', 'option');
		$addresses['billing'] = $addresses['billing'] != null ? (boolean)$addresses['billing'] : false;
		
		$addresses['shipping'] = get_field('wcmca_disable_shipping_multiple_addresses', 'option');
		$addresses['shipping'] = $addresses['shipping'] != null ? (boolean)$addresses['shipping'] : false;
		remove_filter('acf/settings/current_language', array(&$this,'cl_acf_set_language'), 100);
		
		return $addresses;
	}
	public function get_custom_css_rules()
	{
		$css = array();
		
		add_filter('acf/settings/current_language',  array(&$this, 'cl_acf_set_language'), 100);
		$css['my_account_page'] = get_field('wcmca_custom_css_rules_my_account_page', 'option');
		$css['my_account_page'] = $css['my_account_page'] != null ? $css['my_account_page'] : "";
		
		$css['checkout_page'] = get_field('wcmca_custom_css_rules_checkout_page', 'option');
		$css['checkout_page'] = $css['checkout_page'] != null ? $css['checkout_page'] : "";
		remove_filter('acf/settings/current_language', array(&$this,'cl_acf_set_language'), 100);
		
		return $css;
	}
	public function get_style_options()
	{
		$css = array();
		
		add_filter('acf/settings/current_language',  array(&$this, 'cl_acf_set_language'), 100);
		$css['default_badge_backgroud_color'] = get_field('wcmca_default_badge_backgroud_color', 'option');
		$css['default_badge_backgroud_color'] = $css['default_badge_backgroud_color'] != null ? $css['default_badge_backgroud_color'] : "#000000";
		
		$css['default_badge_text_color'] = get_field('wcmca_default_badge_text_color', 'option');
		$css['default_badge_text_color'] = $css['default_badge_text_color'] != null ? $css['default_badge_text_color'] : "#FFFFFF";
		remove_filter('acf/settings/current_language', array(&$this,'cl_acf_set_language'), 100);
		
		return $css;
	}
	function cl_acf_set_language() 
	{
	  return acf_get_setting('default_language');
	}
}
?>