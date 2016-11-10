<?php 
class WCMCA_Html
{
	var $allowed_field_type = array('text','datepicker', 'number', 'multiselect', 'select','checkbox','radio', 'phone', 'tel', 'email','state', 'country');
	public function __construct()
	{ 
		add_action('admin_menu', array(&$this,'init_admin_pages'));
		add_action('wp_ajax_wcmca_get_addresses_html_popup_by_user_id', array(&$this, 'ajax_get_addresses_html_popup_by_user_id'));
	}
	function init_admin_pages()
	{
		/* add_options_page( 
			__('Edit page','woocommerce-multiple-customer-addresses'),
			__('Edit page','woocommerce-multiple-customer-addresses'),
			'manage_woocommerce',
			'woocommerce-multiple-customer-addresses-edit-user',
			array(&$this, 'render_admin_user_addresses_edit_page')
		); */
		//add_pages_page('Edit addresses', 'WooCommerce Multiple Customer Adresses', 'manage_woocommerce', 'woocommerce-multiple-customer-addresses-edit-user', array(&$this, 'render_admin_user_addresses_edit_page'));
		//add_management_page('Edit addresses', 'WooCommerce Multiple Customer Adresses', 'manage_woocommerce', 'woocommerce-multiple-customer-addresses-edit-user', array(&$this, 'render_admin_user_addresses_edit_page'));
		
		//Parent slug is null, in this way the page is not showed in admin menu
		add_submenu_page(null, 'Edit addresses', 'WooCommerce Multiple Customer Adresses', 'manage_woocommerce', 'woocommerce-multiple-customer-addresses-edit-user', array(&$this, 'render_admin_user_addresses_edit_page'));
	}
	function curPageURL() 
	{
		 $pageURL = 'http';
		 if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
		 $pageURL .= "://";
		 if ($_SERVER["SERVER_PORT"] != "80") {
		  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		 } else {
		  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		 }
		 return $pageURL;
	}
	public function common_js()
	{
		?>
		<script>
				var wcmca_ajax_url = '<?php echo admin_url('admin-ajax.php'); ?>';
				var wcmca_is_wcbcf_active = false <?php //if($wcmca_is_wcbcf_active) echo 'true'; else echo 'false'; ?>;
				var wcmca_current_url = '<?php echo $this->curPageURL(); ?>';
				var wcmca_confirm_delete_message = '<?php _e('Are you sure?','woocommerce-multiple-customer-addresses'); ?>';
				var wcmca_state_string = '<?php _e('State','woocommerce-multiple-customer-addresses'); ?>';
				var wcmca_postcode_string = '<?php _e('Postcode / ZIP','woocommerce-multiple-customer-addresses'); ?>';
				var wcmca_city_string = '<?php _e('City','woocommerce-multiple-customer-addresses'); ?>';
			</script>
			<?php 
	}
	public function render_custom_css($page)
	{
		global $wcmca_option_model;
		$css = $wcmca_option_model->get_custom_css_rules();
		if(!isset($css[$page]))
			return;
		?>
		<style type="text/css">
		<?php echo $css[$page]; ?>
		</style>
		<?php 
	}
	//add admin user edit addresses page link button
	public function add_multiple_address_link_to_user_admin_profile_page($user)
	{
		if(!current_user_can('manage_woocommerce'))
			return;
		?>
		<h2><?php _e('Additional addresses','woocommerce-multiple-customer-addresses'); ?></h2>
		<table class="form-table">
			<tbody>
				<tr>
					<th><?php _e('Addresses list','woocommerce-multiple-customer-addresses'); ?></th>
					<td>
						<a class="button button-primary wcmca_primary" target="_blank" href="<?php echo get_admin_url(); ?>admin.php?page=woocommerce-multiple-customer-addresses-edit-user&user_id=<?php echo $user->ID; ?>"><?php _e('View & Edit','woocommerce-multiple-customer-addresses'); ?></a>
					</td>
				</tr>
			</tbody>			
		</table>
		<?php 
	}
	// ------------------ ORDER PAGE ---------------------- //
	//Admin order edit page -> container rendering
	public function render_admin_order_page_additional_addresses_loading_tools()
	{
		global  $wcmca_option_model;
		$which_addresses_to_hide = $wcmca_option_model->which_addresse_type_are_disabled();
		$this->addresses_list_common_scripts();
		wp_dequeue_script('wcmca-additional-addresses');
		wp_dequeue_script('wcmca-additional-addresses-ui');
		
		
		wp_enqueue_script('wcmca-admin-order-edit-ui', WCMCA_PLUGIN_PATH.'/js/admin-order-edit-ui.js', array('jquery'));
		wp_enqueue_script('wcmca-admin-order-edit', WCMCA_PLUGIN_PATH.'/js/admin-order-edit.js', array('jquery'));
		wp_enqueue_script('jquery-ui-tooltip');
		
		wp_enqueue_style('wcmca-backend-edit-user-addresses', WCMCA_PLUGIN_PATH.'/css/backend-edit-user-addresses.css');
		wp_enqueue_style('wcmca-backend-edit-order-addresses', WCMCA_PLUGIN_PATH.'/css/backend-order-edit.css');
		?>
		<script>
		var wcmca_load_additional_addresses_text_button = "<?php _e( 'Click to load addresses list', 'woocommerce-multiple-customer-addresses' ); ?>";
		var wcmca_loader_html = '<img class="wcmca_preloader_image" src="<?php echo WCMCA_PLUGIN_PATH.'/img/loader.gif' ?>" ></img>';
		var wcmca_hide_billing_addresses_selection = <?php if($which_addresses_to_hide['billing']) echo "true"; else echo "false"; ?>;
		var wcmca_hide_shipping_addresses_selection = <?php if($which_addresses_to_hide['shipping']) echo "true"; else echo "false"; ?>;
		</script>
		<div id="wcmca_additional_addresses_container" class="mfp-hide"></div>
		<?php 
	}
	//Admin order page -> ajax call to retrieve data  
	function ajax_get_addresses_html_popup_by_user_id()
	{
		$user_id = isset($_POST['user_id']) && is_numeric($_POST['user_id']) && $_POST['user_id'] > 0 ? $_POST['user_id'] : $user_id;
		$type = isset($_POST['type']) ? $_POST['type'] : null;
		
		?>
		<a href= "#"  id="wcmca_close_button" class="mfp-close">X</a>
		<?php 
			if(isset($user_id) && isset($type))
				$this->render_addresses_list($user_id, $type, false);
			else 
				echo "<h3>".__('Please select a regisered user.','woocommerce-multiple-customer-addresses')."</h3>";
		 wp_die();
	}
	// ------------------ END ORDER PAGE ---------------------- //
	
	
	//Admin user edit addresses page 
	public function render_admin_user_addresses_edit_page($user_id = null)
	{
		$this->addresses_list_common_scripts();
		wp_enqueue_style('wcmca-backend-edit-user-addresses', WCMCA_PLUGIN_PATH.'/css/backend-edit-user-addresses.css');		
		
		$user_id = isset($_GET['user_id']) && is_numeric($_GET['user_id']) && $_GET['user_id'] > 0 ? $_GET['user_id'] : $user_id;
		
		?>
			<div class="wrap white-box">
			<?php if(isset($user_id)) 
					$this->render_addresses_list($user_id, null, false); ?>
			</div>
		<?php 
	}
	function addresses_list_common_scripts()
	{
		wp_enqueue_style('wcmca-magnific-popup', WCMCA_PLUGIN_PATH.'/css/vendor/magnific-popup.css'); 
		wp_enqueue_style('wcmca-additional-addresses',WCMCA_PLUGIN_PATH.'/css/frontend-my-account-addresses-list.css');
			
		if(!is_admin())		
			wp_enqueue_script('wcmca-custom-select2',WCMCA_PLUGIN_PATH.'/js/select2-manager.js', array('jquery', 'select2')); 
		wp_enqueue_script('wcmca-additional-addresses-ui',WCMCA_PLUGIN_PATH.'/js/frontend-address-form-ui.js', array('jquery'));  
		wp_enqueue_script('wcmca-additional-addresses',WCMCA_PLUGIN_PATH.'/js/frontend-address-form.js', array('jquery')); 
		
		wp_enqueue_script('wcmca-magnific-popup', WCMCA_PLUGIN_PATH.'/js/vendor/jquery.magnific-popup.js', array('jquery'));
	}
	//Woocommerce My account page (used also for admin order and user profile pages)
	public function render_addresses_list($user_id = null, $type_to_show_in_order_edit_page = null, $include_scripts = true)
	{
		global $wcmca_address_model, $wcmca_customer_model,$wcmca_option_model;
		$is_vat_identification_number_enabled = $wcmca_option_model->is_vat_identification_number_enabled();
		$default_addresses_style = $wcmca_option_model->get_style_options();
		//$address_fields = array();
		$which_addresses_to_hide = $wcmca_option_model->which_addresse_type_are_disabled();
		$user_id = !isset($user_id) ? get_current_user_id() : $user_id;
		/* if(isset($_GET['wcmca_delete']))
			$wcmca_address_model->delete_address($_GET['wcmca_delete']); */
		if($include_scripts)
		{
			$this->addresses_list_common_scripts();	
		}
		?>
		<div id="wcmca_custom_addresses">
			<div class="u-columns woocommerce-Addresses col2-set addresses">
			<?php if(!$which_addresses_to_hide['billing'] && !isset($type_to_show_in_order_edit_page)): ?>
				<div class="u-column1 col-1 woocommerce-Address">
					<a href="#wcmca_address_form_container_billing" class="button wcmca_add_new_address_button" id="wcmca_add_new_address_button_billing"><?php _e('Add new billing address','woocommerce-multiple-customer-addresses'); ?></a>
				</div>
			<?php endif;
				 if(!$which_addresses_to_hide['shipping'] && !isset($type_to_show_in_order_edit_page) ): ?>
				<div class="u-column2 col-2 woocommerce-Address">
					<a href="#wcmca_address_form_container_shipping" class="button wcmca_add_new_address_button" id="wcmca_add_new_address_button_shipping"><?php _e('Add new shipping address','woocommerce-multiple-customer-addresses'); ?></a>
				</div>
			<?php endif; ?>
			</div>
			<?php 
				$addresses_by_type = $wcmca_customer_model->get_addresses_by_type($user_id);
				//wcmca_var_dump($addresses_by_type);
				$col_counter = 0;
				foreach($addresses_by_type as $type => $addresses)
				  if(!empty($addresses) && !$which_addresses_to_hide[$type] && (!isset($type_to_show_in_order_edit_page) || $type_to_show_in_order_edit_page == $type))
				  { 
			  
					include(WCMCA_PLUGIN_ABS_PATH.'/templates/my_account.php');
				  }	//end if !empty($addresses)			
				?>
			
			<?php $this->common_js(); ?>
			
			<form id="wcmca_address_form_container_billing" class="mfp-hide">
				<?php $this->render_address_form('billing', $user_id ); ?>
			</form>
			<form id="wcmca_address_form_container_shipping" class="mfp-hide">
				<?php $this->render_address_form('shipping', $user_id ); ?>
			</form>
		</div>
		<?php
	}
	
	//New/Edit address popup HTML
	public function render_address_form($type = 'billing', $user_id=null)
	{
		global $wcmca_address_model, $wcmca_option_model;
		$is_vat_identification_number_enabled = $wcmca_option_model->is_vat_identification_number_enabled();
		$is_identifier_field_disabled = $wcmca_option_model->is_identifier_field_disabled();
		$countries = $wcmca_address_model->get_countries();	
		//WCBCF (Brazialian extra fields) support
		$wcbcf_settings = get_option( 'wcbcf_settings' );
		$wcmca_is_wcbcf_active = wcmca_is_wcbcf_active();
		$required_fields = $wcmca_option_model->get_required_fields();
		?>
		
		<div id="wcmca_form_popup_container_<?php echo $type; ?>">
			<a href= "#"  id="wcmca_close_address_form_button_<?php echo $type; ?>" class="mfp-close">X</a>
			<div class="woocommerce">
				<div  id="wcmca_address_form_<?php echo $type; ?>">
					<div id="wcmca_address_form_fieldset_<?php echo $type; ?>">
					<input id="wcmca_address_id_<?php echo $type; ?>" name="wcmca_address_id" type="hidden" value="-1"></input>
					<?php if(isset($user_id)): ?>
						<input type="hidden" name="wcmca_user_id" value="<?php echo $user_id;?>"></input>
					<?php endif; ?>
						<?php 
						$address_fields = $wcmca_address_model->get_woocommerce_address_fields_by_type($type);
						
						//Field name
						if(!$is_identifier_field_disabled)
							woocommerce_form_field('wcmca_address_internal_name', array(
									'type'       => 'text',
									'class'      => array( 'form-row-wide' ),
									'required'   => true,
									'input_class' => array('not_empty' ,'wcmca_input_field'),
									'label'      => __('Identifier / Name (it is used to identify this address for a future usage. Ex.: "Office address")','woocommerce-multiple-customer-addresses'),
									'label_class' => array( 'wcmca_form_label' ),
									'custom_attributes'    => array('required' => 'required'),
									//'placeholder'    => __('First Name','woocommerce-multiple-customer-addresses')
									)
									);
								
						//Is default checkbox
						$default_address_label = $type == "shipping" ? __('Make this address as default shipping address','woocommerce-multiple-customer-addresses'): __('Make this address as default billing address','woocommerce-multiple-customer-addresses');
						woocommerce_form_field('wcmca_'.$type.'_is_default_address', array(
								'type'       => 'checkbox',
								'class'      => array( 'form-row-wide' ),
								'required'   => false,
								'label'      => $default_address_label,
								'label_class' => array( 'wcmca_default_checkobx_label' )
								)
								);
								
						$was_prev_field_first_row = false;
						foreach($address_fields as $field_name => $address_field)
						{
							if($field_name == 'billing_state' || $field_name == 'shipping_state' 
								|| (isset($address_field['type']) && !in_array($address_field['type'],$this->allowed_field_type)) || 
								(isset($address_field['enabled']) && !$address_field['enabled']))
								{
									//wcmca_var_dump($address_field['type']);
									continue;
								}
							else if($field_name == 'billing_country' || $field_name == 'shipping_country')
							{
								$was_prev_field_first_row = $was_prev_field_last_row = false;
								?>
								<div class="wcmca_divider"></div>
								<?php 
								woocommerce_form_field('wcmca_'.$type.'_country', array(
									'type'       => 'select',
									'class'      => array( 'form-row-first' ),
									'input_class' => array('wcmca-country-select2', 'not_empty'),
									'required'   => true,
									'label'      => __('Select a country','woocommerce-multiple-customer-addresses'),
									'label_class' => array( 'wcmca_form_label' ),
									//placeholder'    => __('Select a country','woocommerce-multiple-customer-addresses'),
									'options'    => $countries,
									'custom_attributes'  => array('required' => 'required')
									)
								);
								?> 
									<div id="wcmca_country_field_container_<?php echo $type; ?>"></div>
									<img class="wcmca_preloader_image" src="<?php echo WCMCA_PLUGIN_PATH.'/img/loader.gif' ?>" ></img>
									<div class="wcmca_divider"></div>
								<?php 
							}
							else
							{
								
								$is_required = isset($address_field['required']) ? $address_field['required'] : false;
								$custom_attributes = isset($address_field['custom_attributes']) ? $address_field['custom_attributes'] : array();
								$class_to_assign = $address_field['class'];
								
								//row class managment
								if(wcmca_array_element_contains_substring('form-row-first', $address_field['class']) && $was_prev_field_first_row)
								{
									$class_to_assign = array( 'form-row-last' );
									$was_prev_field_last_row = true;
								}
								else
									$was_prev_field_last_row = wcmca_array_element_contains_substring('form-row-last', $address_field['class']) ? true : false;
								
								if(wcmca_array_element_contains_substring('form-row-last', $address_field['class']) && (!$was_prev_field_first_row /* || $was_prev_field_last_row */))
								{
									$class_to_assign = array( 'form-row-wide' );
									$was_prev_field_first_row = false;
								}
								else
									$was_prev_field_first_row = wcmca_array_element_contains_substring('form-row-first', $address_field['class']) ? true : false;
								
								
								
								//requirement managment and class managment
								if($is_required)
									$custom_attributes['required'] = 'required';
								$input_class = isset($address_field['required']) && $address_field['required'] ? array('not_empty' ,'wcmca_input_field') : array('wcmca_input_field');
								$label_class = array( 'wcmca_form_label' );
								
								//field options managment
								$field_options = isset($address_field['options']) ? $address_field['options'] : array(); 
								
								//Support for Checkout Field Editor Pro Advanced
								if(isset($address_field['options_object']))
								{
									$field_options = array();
									foreach($address_field['options_object'] as $object_option)
										$field_options[$object_option['key']] = $object_option['text'];
								}
								//extra field type managment
								if(isset($address_field['type']) && $address_field['type'] == "multiselect")
								{	
									$address_field['type'] = 'select';
									$custom_attributes['multiple'] = 'multiple';
								}
								elseif(isset($address_field['type']) && ($address_field['type'] == "radio" || $address_field['type'] == "checkbox"))
								{
									$custom_attributes['data-default'] = isset($woocommerce_address_field['default'])  ? $woocommerce_address_field['default'] :  0;
									$label_class = array( 'wcmca_form_inline_input_label' );
									$input_class = isset($address_field['required']) && $address_field['required'] ? array('not_empty' ,'wcmca_inline_input_field') : array('wcmca_inline_input_field');
								}
								
								//Forcing/Unforcing required
								if( (($field_name == 'billing_first_name' || $field_name == 'billing_last_name') && $required_fields['billing_first_and_last_name_disable_required']) || 
								    (($field_name == 'shipping_first_name' || $field_name == 'shipping_last_name') && $required_fields['shipping_first_and_last_name_disable_required']) )
									{
										$is_required = false;
										$input_class =  array('wcmca_input_field');
										if(isset($custom_attributes['required']))
											unset($custom_attributes['required']);
									}
								if( ($field_name == 'billing_company'  && $required_fields['billing_company_name_enable_required']) || 
								    ($field_name == 'shipping_company'  && $required_fields['shipping_company_name_enable_required'])  )
									{
										$is_required = true;
										$input_class = array('not_empty' ,'wcmca_input_field');
										$custom_attributes['required'] = 'required';
									}
								 @woocommerce_form_field('wcmca_'.$field_name, array(
										'type'       => isset($address_field['type']) ? $address_field['type'] : 'text',
										'autocomplete' => isset($address_field['autocomplete']) ? $address_field['autocomplete'] : false,
										'class'      => $class_to_assign,//array( 'form-row-first' ),
										'required'   => $is_required,
										'input_class' => $input_class,
										'label'      => isset($address_field['label']) ? $address_field['label'] : "",										
										'description'    => isset($address_field['description']) ? $address_field['description'] : '',
										'label_class' => $label_class,
										'placeholder'    => isset($address_field['placeholder']) ? $address_field['placeholder'] : '',
										'maxlength'    => isset($address_field['maxlength']) ? $address_field['maxlength'] : false,
										'validate'    => isset($address_field['validate']) ? $address_field['validate'] : array(),
										'custom_attributes'    => $custom_attributes,
										'options'    => $field_options,
										),
										$address_field['type'] == 'checkbox' && $address_field['default'] ? true : null /* $address_field['checked'] */
									);
							}
						}
						
						?>
						
						<p class="wcmca_save_address_button_container">
						<button class="button" class="wcmca_save_address_button" id="wcmca_save_address_button_<?php echo $type; ?>"><?php _e('Save','woocommerce-multiple-customer-addresses'); ?></button>
						</p>
					</div>
				 </div>
			</div>
		</div>
		<!--<div id="wcmca_form_background_overlay" ></div>-->
		<?php		
	}
	function render_address_form_popup()
	{
		 $this->common_js(); ?>
		<div id="wcmca_custom_addresses" display="height:1px">
		</div>
		<div id="wcmca_address_form_container_billing" class="mfp-hide">
				<?php $this->render_address_form('billing'); ?>
		</div>
		<div id="wcmca_address_form_container_shipping" class="mfp-hide">
			<?php $this->render_address_form('shipping'); ?>
		</div>
		<?php
	}
	
	//Checkout page -> dropdown menus
	function render_address_select_menu($type = 'billing')
	{
		global $wcmca_customer_model, $wcmca_option_model;
		
		wp_enqueue_style('wcmca-magnific-popup', WCMCA_PLUGIN_PATH.'/css/vendor/magnific-popup.css');
		wp_enqueue_style('wcmca-additional-addresses', WCMCA_PLUGIN_PATH.'/css/frontend-checkout.css');
		
		wp_enqueue_script('wcmca-custom-select2',WCMCA_PLUGIN_PATH.'/js/select2-manager.js', array('jquery', 'select2')); 
		wp_enqueue_script('wcmca-magnific-popup', WCMCA_PLUGIN_PATH.'/js/vendor/jquery.magnific-popup.js', array('jquery'));		
		wp_enqueue_script('wcmca-additional-addresses-ui', WCMCA_PLUGIN_PATH.'/js/frontend-checkout-ui.js', array('jquery'));
		wp_enqueue_script('wcmca-additional-addresses', WCMCA_PLUGIN_PATH.'/js/frontend-checkout.js', array('jquery'));
		
		wp_enqueue_script('wcmca-address-form-ui',WCMCA_PLUGIN_PATH.'/js/frontend-address-form-ui.js', array('jquery'));  
		wp_enqueue_script('wcmca-address-form',WCMCA_PLUGIN_PATH.'/js/frontend-address-form.js', array('jquery'));  
		
		$addresses = $wcmca_customer_model->get_addresses(get_current_user_id());
		$which_addresses_to_hide = $wcmca_option_model->which_addresse_type_are_disabled();
	 
		if($which_addresses_to_hide[$type])
			return;
		?>
		
		<p class="form-row form-row">
			<label><?php _e('Select an address','woocommerce-multiple-customer-addresses'); ?></label>
			<select class="wcmca_address_select_menu" data-type="<?php echo $type; ?>" id="wcmca_address_select_menu_<?php echo $type; ?>">
				<?php if(empty($addresses)): ?>
					<!-- <option selected disabled><?php _e('Select an address','woocommerce-multiple-customer-addresses'); ?></option>
					<option value="def_shipping"><?php _e('Default billing address','woocommerce-multiple-customer-addresses'); ?></option>
					<option value="def_billing"><?php _e('Default shipping address','woocommerce-multiple-customer-addresses'); ?></option> -->
					<option selected disabled><?php _e('There are no additional addresses','woocommerce-multiple-customer-addresses'); ?></option>
			<?php else: ?>
					<!-- <option selected disabled><?php _e('Select an address to load','woocommerce-multiple-customer-addresses'); ?></option> -->
					<?php if($type == 'shipping'): ?>
						<option value="last_used_<?php echo $type; ?>"><?php _e('Last used shipping address','woocommerce-multiple-customer-addresses'); ?></option>
					<?php else: ?>
						<option value="last_used_<?php echo $type; ?>"><?php _e('Last used billing address','woocommerce-multiple-customer-addresses'); ?></option>
					<?php endif; ?>
			<?php endif;
				foreach( $addresses as $address_id => $address)
					if(isset($address['address_internal_name']) && $address['type'] == $type)
					{
						$selected = isset($address[$address['type']."_is_default_address"]) ? 'selected="selected"': "";
						echo '<option value="'.$address_id.'" '.$selected.'>'.$address[$type.'_country']."-".$address[$type.'_company']."-".$address[$type.'_city']."-".$address['address_internal_name'].'</option>';
					}
			?>
			</select>
			<!-- #wcmca_custom_addresses -->
			<a href="#wcmca_address_form_container_<?php echo $type; ?>" id="wcmca_add_new_address_button_<?php echo $type; ?>" class ="button wcmca_add_new_address_button"><?php _e('Add new address','woocommerce-multiple-customer-addresses'); ?></a>
		</p>
		<p>
			<img class="wcmca_loader_image" id="wcmca_loader_image_<?php echo $type; ?>" src="<?php echo WCMCA_PLUGIN_PATH.'/img/loader.gif' ?>" ></img>
		</p>
		<?php 
	}
}
?>