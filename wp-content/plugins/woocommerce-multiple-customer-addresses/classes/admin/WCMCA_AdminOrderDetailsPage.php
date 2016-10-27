<?php 
class WCMCA_AdminOrderDetailsPage
{
	public function __construct()
	{
		add_action( 'woocommerce_admin_order_data_after_order_details', array( &$this,'add_additional_addresses_loading_tools')); 
		add_action( 'woocommerce_admin_order_data_after_billing_address', array( &$this,'add_custom_billing_fields'), 10, 1 );
		add_action( 'woocommerce_process_shop_order_meta', array( &$this, 'on_save_order_details_admin_page' ), 5, 2 );//save order	
	}
	public function add_custom_billing_fields($order)
	{
		global $wcmca_option_model;
		if(!$wcmca_option_model->is_vat_identification_number_enabled())
			return;
		$billing_vat_number = get_post_meta($order->id, 'billing_vat_number',true);
		$billing_vat_number = $billing_vat_number ? $billing_vat_number : "";
		?>
		<p class="form-row form-row-wide">
			<label class="wpuef_label"><?php _e( 'VAT Identification Number', 'woocommerce-multiple-customer-addresses' ); ?></label>
			<input class="input-text wpuef_input_text" type="text" placeholder="<?php _e( 'VAT Identification Number', 'woocommerce-multiple-customer-addresses' ); ?>" value="<?php echo $billing_vat_number; ?>" name="billing_vat_number" />
		</p>
		<?php
	}
	public function on_save_order_details_admin_page( $order_id, $order )
	{
		if(isset($_POST['billing_vat_number']))
			update_post_meta($order_id,'billing_vat_number',$_POST['billing_vat_number']);
	}
	public function add_additional_addresses_loading_tools($order)
	{
		global $wcmca_html_helper;
		$wcmca_html_helper->render_admin_order_page_additional_addresses_loading_tools();
	}
}
?>