<?php 
class WCMCA_OrderDetailsPage
{
	public function __construct()
	{
		add_action('woocommerce_order_details_after_customer_details', array(&$this, 'show_custom_fields'));
	}
	function show_custom_fields($order)
	{
		global $wcmca_option_model;
		$billing_vat_number = get_post_meta($order->id, 'billing_vat_number',true);
		$billing_vat_number = $billing_vat_number ? $billing_vat_number : "";
		if(!$wcmca_option_model->is_vat_identification_number_enabled())
			return;
		?>
		<tr>
			<th><?php _e( 'VAT Identification Number:', 'woocommerce-multiple-customer-addresses' ); ?></th>
			<td><?php echo $billing_vat_number; ?></td>
		</tr>
		<?php 
	}
}
?>