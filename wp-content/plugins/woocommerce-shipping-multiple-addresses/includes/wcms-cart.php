<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WC_MS_Cart {
    private $wcms;

    public function __construct( WC_Ship_Multiple $wcms ) {
        $this->wcms = $wcms;

        // duplicate cart POST
        add_action( 'template_redirect', array($this, 'duplicate_cart_post') );
        add_action( 'wp_ajax_wc_duplicate_cart', array( $this, 'duplicate_cart_ajax' ) );
        add_action( 'wp_ajax_nopriv_wc_duplicate_cart', array( $this, 'duplicate_cart_ajax' ) );

        add_action( 'woocommerce_cart_totals_after_shipping', array(&$this, 'remove_shipping_calculator') );

        /* WCMS Cart */
        add_action( 'woocommerce_cart_actions', array( $this, 'show_duplicate_cart_button' ) );

        // cleanup
        add_action( 'woocommerce_cart_emptied', array( $this->wcms, 'clear_session' ) );
        add_action( 'woocommerce_cart_updated', array( $this, 'cart_updated' ) );
    }

    public function duplicate_cart_post() {
        global $woocommerce;

        if ( isset($_POST['duplicate_submit']) ) {
            $address_ids    = (isset($_POST['address_ids'])) ? (array)$_POST['address_ids'] : array();
            $fields         = $woocommerce->countries->get_address_fields( $woocommerce->countries->get_base_country(), 'shipping_' );

            $user_addresses = $this->wcms->address_book->get_user_addresses( wp_get_current_user() );

            $data   = (wcms_session_isset('cart_item_addresses')) ? wcms_session_get('cart_item_addresses') : array();
            $rel    = (wcms_session_isset('wcms_item_addresses')) ? wcms_session_get('wcms_item_addresses') : array();

            for ($x = 0; $x < count($address_ids); $x++ ) {
                $added      = $this->duplicate_cart();
                $address_id = $address_ids[ $x ];
                $address    = $user_addresses[ $address_id ];

                foreach ( $added as $item ) {
                    $qtys           = $item['qty'];
                    $product_id     = $item['id'];
                    $sig            = $item['key'] .'_'. $product_id .'_';

                    $i = 1;
                    for ( $y = 0; $y < $qtys; $y++ ) {
                        $rel[ $address_id ][]  = $item['key'];

                        while ( isset($data['shipping_first_name_'. $sig . $i]) ) {
                            $i++;
                        }

                        $_sig = $sig . $i;
                        if ( $fields ) foreach ( $fields as $key => $field ) {
                            $data[$key .'_'. $_sig] = $address[ $key ];
                        }
                    }

                    $cart_address_ids_session = wcms_session_get( 'cart_address_ids' );

                    if (!wcms_session_isset( 'cart_address_ids' ) || ! in_array($sig, $cart_address_ids_session) ) {
                        $cart_address_sigs_session = wcms_session_get( 'cart_address_sigs' );
                        $cart_address_sigs_session[$_sig] = $address_id;
                        wcms_session_set( 'cart_address_sigs', $cart_address_sigs_session);
                    }
                }
            }

            wcms_session_set( 'cart_item_addresses', $data );
            wcms_session_set( 'address_relationships', $rel );
            wcms_session_set( 'wcms_item_addresses', $rel );

            wp_redirect( get_permalink( woocommerce_get_page_id('multiple_addresses') ) );
            exit;
        }
    }

    public function duplicate_cart_ajax() {
        global $woocommerce;

        $this->load_cart_files();

        $checkout   = new WC_Checkout();
        $cart       = $woocommerce->cart;
        $user       = wp_get_current_user();

        $shipFields = $woocommerce->countries->get_address_fields( $woocommerce->countries->get_base_country(), 'shipping_' );
        $address    = $_POST['address'];
        $add_id     = ( isset($_POST['address_id']) && !empty($_POST['address_id']) ) ? $_POST['address_id'] : false;

        $add = $this->duplicate_cart();

        $addresses = $this->wcms->address_book->get_user_addresses( $user );

        if ( $add_id !== false ) {
            $address    = $addresses[$add_id];
            $id         = $add_id;
        } else {
            $address    = $_POST['address'];
            $id         = rand(100,1000);
        }

        foreach ( $address as $key => $value ) {
            $new_key = str_replace( 'shipping_', '', $key);
            $address[$new_key] = $value;
        }

        $formatted_address  = wcms_get_formatted_address( $address );
        $json_address       = json_encode($address);

        if ( $user->ID > 0 ) {
            $vals = '';
            foreach ($address as $key => $value) {
                $vals .= $value;
            }

            $md5 = md5($vals);
            $saved = false;

            foreach ($addresses as $addr) {
                $vals = '';
                if( !is_array($addr) ) { continue; }
                foreach ($addr as $key => $value) {
                    $vals .= $value;
                }
                $addrMd5 = md5($vals);

                if ($md5 == $addrMd5) {
                    // duplicate address!
                    $saved = true;
                    break;
                }
            }

            if (! $saved && ! $add_id ) {
                // address is unique, save!
                $id = count($addresses);
                $addresses[] = $address;

                $this->wcms->address_book->save_user_addresses( $user->ID, $addresses );
            }
        }

        $html = '
            <div class="account-address">
                <address>'. $formatted_address .'</address>
                <div style="display: none;">';

        ob_start();
        foreach ($shipFields as $key => $field) {
            $val = ( isset( $address[ $key ] ) ) ? $address[ $key ] : '';
            $key .= '_' . $id;

            woocommerce_form_field( $key, $field, $val );
        }

        do_action( 'woocommerce_after_checkout_shipping_form', $checkout);
        $html .= ob_get_clean();

        $html .= '
                    <input type="hidden" name="addresses[]" value="'. $id .'" />
                    <textarea style="display:none;">'. $json_address .'</textarea>
                </div>

                <ul class="items-column" id="items_column_'. $id .'">';

        foreach ( $add as $product ) {
            $html .= '
                <li data-product-id="'. $product['id'] .'" data-key="'. $product['key'] .'" class="address-item address-item-'. $product['id'] .' address-item-key-'. $product['key'] .'">
                    <span class="qty">'. $product['qty'] .'</span>
                    <h3 class="title">'. get_the_title($product['id']) .'</h3>
                    '. $woocommerce->cart->get_item_data( $product['content'] );

            for ($item_qty = 0; $item_qty < $product['qty']; $item_qty++):
                $html .= '<input type="hidden" name="items_'. $id.'[]" value="'. $product['key'] .'">';
            endfor;

            $html .= '<a class="remove" href="#"><img style="width: 16px; height: 16px;" src="'. plugins_url('images/delete.png', self::FILE) .'" class="remove" title="'. __('Remove', 'wc_shipping_multiple_address') .'"></a>
                </li>';

        }

        $html .= '    </ul>
            </div>
            ';

        $return = json_encode(array( 'ack' => 'OK', 'id' => $id, 'html' => $html));
        die($return);
        exit;
    }

    public function remove_shipping_calculator() {
        global $woocommerce;

        if ( isset($woocommerce->session) && isset($woocommerce->session->cart_item_addresses) ) {
            $script = 'jQuery(document).ready(function(){
                    jQuery("tr.shipping").remove();
                });';

            if ( function_exists('wc_enqueue_js') ) {
                wc_enqueue_js( $script );
            } else {
                $woocommerce->add_inline_js( $script );
            }

            echo '<tr class="multi-shipping">
                    <th>'. __( 'Shipping', 'woocommerce' ) .'</th>
                    <td>'. woocommerce_price($woocommerce->session->shipping_total) .'</td>
                </tr>';
        }
    }

    public function show_duplicate_cart_button() {
        $ms_settings = get_option( 'woocommerce_multiple_shipping_settings', array() );

        if ( isset($ms_settings['cart_duplication']) && $ms_settings['cart_duplication'] != 'no' ) {
            $dupe_url = add_query_arg('duplicate-form', '1', get_permalink(woocommerce_get_page_id('multiple_addresses')));

            echo '<a class="button expand" href="' . $dupe_url . '" >' . __('Duplicate Cart', 'wc_shipping_multiple_address') . '</a>';
        }
    }

    public function cart_updated() {
        global $woocommerce;

        $cart = $woocommerce->cart->get_cart();

        if ( empty($cart) || !$this->cart_is_eligible_for_multi_shipping() ) {
            wcms_session_delete( 'cart_item_addresses' );
            wcms_session_delete( 'cart_address_sigs' );
            wcms_session_delete( 'address_relationships' );
            wcms_session_delete( 'shipping_methods' );
            wcms_session_delete( 'wcms_original_cart' );
        }
    }

    public function duplicate_cart( $multiplier = 1 ) {
        global $woocommerce;

        $this->load_cart_files();

        $cart           = $woocommerce->cart;
        $current_cart   = $cart->get_cart();
        $orig_cart      = array();

        if ( wcms_session_isset('wcms_original_cart') ) {
            $orig_cart = wcms_session_get( 'wcms_original_cart' );
        }

        if ( !empty($orig_cart) ) {
            $contents = wcms_session_get( 'wcms_original_cart' );
        } else {
            $contents = $cart->get_cart();
            wcms_session_set( 'wcms_original_cart', $contents );
        }

        $added = array();
        foreach ( $contents as $cart_key => $content ) {
            $add_qty        = $content['quantity'] * $multiplier;
            $current_qty    = (isset($current_cart[$cart_key])) ? $current_cart[$cart_key]['quantity'] : 0;

            $cart->set_quantity( $cart_key, $current_qty + $add_qty );

            $added[] = array(
                'id'        => $content['product_id'],
                'qty'       => $add_qty,
                'key'       => $cart_key,
                'content'   => $content
            );
        }

        return $added;
    }

    public function load_cart_files() {
        global $woocommerce;

        if ( file_exists($woocommerce->plugin_path() .'/classes/class-wc-cart.php') ) {
            require_once $woocommerce->plugin_path() .'/classes/abstracts/abstract-wc-session.php';
            require_once $woocommerce->plugin_path() .'/classes/class-wc-session-handler.php';
            require_once $woocommerce->plugin_path() .'/classes/class-wc-cart.php';
            require_once $woocommerce->plugin_path() .'/classes/class-wc-checkout.php';
            require_once $woocommerce->plugin_path() .'/classes/class-wc-customer.php';
        } else {
            require_once $woocommerce->plugin_path() .'/includes/abstracts/abstract-wc-session.php';
            require_once $woocommerce->plugin_path() .'/includes/class-wc-session-handler.php';
            require_once $woocommerce->plugin_path() .'/includes/class-wc-cart.php';
            require_once $woocommerce->plugin_path() .'/includes/class-wc-checkout.php';
            require_once $woocommerce->plugin_path() .'/includes/class-wc-customer.php';
        }

        if (! $woocommerce->session )
            $woocommerce->session = new WC_Session_Handler();

        if (! $woocommerce->customer )
            $woocommerce->customer = new WC_Customer();
    }

    /**
     * Check if the contents of the current cart are valid for multiple shipping
     *
     * To pass, there must be 1 or more items in the cart that passes the @see WC_Cart::needs_shipping() test.
     * If there is only 1 item in the cart, it must have a quantity of 2 or more. And child items
     * from Bundles and Composite Products are excluded from the count.
     *
     * This method will automatically return false if the only available shipping method is Local Pickup
     *
     * @return bool
     */
    public function cart_is_eligible_for_multi_shipping() {
        global $woocommerce;

        $sess_item_address  = wcms_session_get( 'cart_item_addresses' );
        $has_item_address   = (!wcms_session_isset( 'cart_item_addresses' ) || empty( $sess_item_address )) ? false : true;
        $item_allowed       = false;
        $contents           = wcms_get_real_cart_items();

	    if ( empty( $contents ) ) {
	    	// no real, shippable products. Return false immediately
		    return apply_filters( 'wc_ms_cart_is_eligible', false );
	    } elseif ( count( $contents ) > 1) {
            $item_allowed = true;
        } else {
            $content = current( $contents );
            if ( $content && $content['quantity'] > 1) {
                $item_allowed = true;
            }
        }

        // do not allow to set multiple addresses if only local pickup is available
        $available_methods = $this->wcms->get_available_shipping_methods();

        if ( count($available_methods) == 1 && ( isset($available_methods['local_pickup']) || isset($available_methods['local_pickup_plus']) ) ) {
            $item_allowed = false;
        } elseif (isset($_POST['shipping_method']) && ( $_POST['shipping_method'] == 'local_pickup' || $_POST['shipping_method'] == 'local_pickup_plus' ) ) {
            $item_allowed = false;
        }

        // do not allow if any of the cart items is in the excludes list
        $settings           = get_option( 'woocommerce_multiple_shipping_settings', array() );
        $excl_products      = (isset($settings['excluded_products'])) ? $settings['excluded_products'] : array();
        $excl_categories    = (isset($settings['excluded_categories'])) ? $settings['excluded_categories'] : array();

        if ( $excl_products || $excl_categories ) {

            foreach ( $contents as $cart_item ) {
                if ( in_array($cart_item['product_id'], $excl_products) ) {
                    $item_allowed = false;
                    break;
                }

                // item categories
                $cat_ids = wp_get_object_terms( $cart_item['product_id'], 'product_cat', array('fields' => 'ids') );

                foreach ( $cat_ids as $cat_id ) {
                    if ( in_array( $cat_id, $excl_categories ) ) {
                        $item_allowed = false;
                        break 2;
                    }
                }

            }
        }

        return apply_filters( 'wc_ms_cart_is_eligible', $item_allowed );
    }
}