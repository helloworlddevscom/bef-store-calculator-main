<?php
class PayPalAdvancedGateway {

  /**
   * [$test_mode description]
   * @var boolean
   */
  public $test_mode;

  /**
   * [$checkout_url description]
   * @var [type]
   */
  public $checkout_url;

  /**
   * [$_payment_key description]
   * @var [type]
   */
  private $_payment_key;

  /**
   * [$_line_items description]
   * @var array
   */
  private $_line_items = array();

  /**
   * [$_line_item_counter description]
   * @var integer
   */
  private $_line_item_counter = 1;

  /**
   * [$_verbosity description]
   * @var string
   */
  private static $_verbosity = 'HIGH';

  /**
   * [$_token_endpoint description]
   * @var [type]
   */
  private $_token_endpoint;

  /**
   * [$_partner description]
   * @var string
   */
  private $_partner;

  /**
   * [$_manager_vendor description]
   * @var [type]
   */
  private $_manager_vendor;

  /**
   * [$_manager_username description]
   * @var [type]
   */
  private $_manager_username;

  /**
   * [$_manager_password description]
   * @var [type]
   */
  private $_manager_password;

  /**
   * [$_first_name description]
   * @var [type]
   */
  private $_first_name;

  /**
   * [$_last_name description]
   * @var [type]
   */
  private $_last_name;

  /**
   * [$_street_1 description]
   * @var [type]
   */
  private $_street_1;

  /**
   * [$_street_2 description]
   * @var [type]
   */
  private $_street_2;

  /**
   * [$_city description]
   * @var [type]
   */
  private $_city;

  /**
   * [$_state description]
   * @var [type]
   */
  private $_state;

  /**
   * [$_country description]
   * @var [type]
   */
  private $_country;

  /**
   * [$_zip description]
   * @var [type]
   */
  private $_zip;

  /**
   * [$_email description]
   * @var [type]
   */
  private $_email;

  /**
   * [$_cart_details description]
   * @var array
   */
  private $_cart_details = array();

  /**
   * [$_subtotal description]
   * @var [type]
   */
  private $_subtotal;

  /**
   * [$_discount description]
   * @var [type]
   */
  private $_discount;

  /**
   * [$_tax description]
   * @var [type]
   */
  private $_tax;

  /**
   * [$_total description]
   * @var [type]
   */
  private $_total;

  /**
   * [$_item_total description]
   * @var integer
   */
  private $_item_total = 0;

  /**
   * [$_fees description]
   * @var array
   */
  private $_fees = array();

  /**
   * [$_currency description]
   * @var [type]
   */
  private $_currency;

  /**
   * [$_discount_code description]
   * @var [type]
   */
  private $_discount_code;

  /**
   * [$_bncode description]
   * @var string
   */
  private static $_bncode = 'Vimeography_SP';

  /**
   * [__construct description]
   * @param [type] $purchase_data
   */
  public function __construct($purchase_data = array()) {
    $this->test_mode       = edd_is_test_mode();

    $this->_set_eppa_paypal_manager_credentials();
    $this->checkout_url    = $this->test_mode  ? 'https://pilot-payflowlink.paypal.com' : 'https://payflowlink.paypal.com';
    $this->_token_endpoint = $this->test_mode  ? 'https://pilot-payflowpro.paypal.com'  : 'https://payflowpro.paypal.com';

    if (! empty($purchase_data) ) {
      $this->_first_name   = isset( $purchase_data['user_info']['first_name'] )         ? $purchase_data['user_info']['first_name']         : '';
      $this->_last_name    = isset( $purchase_data['user_info']['last_name'] )          ? $purchase_data['user_info']['last_name']          : '';
      $this->_street_1     = isset( $purchase_data['user_info']['address']['line1'] )   ? $purchase_data['user_info']['address']['line1']   : '';
      $this->_street_2     = isset( $purchase_data['user_info']['address']['line2'] )   ? $purchase_data['user_info']['address']['line2']   : '';
      $this->_city         = isset( $purchase_data['user_info']['address']['city'] )    ? $purchase_data['user_info']['address']['city']    : '';
      $this->_state        = isset( $purchase_data['user_info']['address']['state'] )   ? $purchase_data['user_info']['address']['state']   : '';
      $this->_country      = isset( $purchase_data['user_info']['address']['country'] ) ? $purchase_data['user_info']['address']['country'] : '';
      $this->_zip          = isset( $purchase_data['user_info']['address']['zip'] )     ? $purchase_data['user_info']['address']['zip']     : '';
      $this->_email        = $purchase_data['user_email'];
      $this->_cart_details = $purchase_data['cart_details'];

      $this->_subtotal = number_format($purchase_data['subtotal'], 2);
      $this->_discount = number_format($purchase_data['discount'], 2);
      $this->_tax      = number_format($purchase_data['tax'], 2);
      $this->_total    = number_format($purchase_data['price'], 2);

      $this->_fees          = $purchase_data['fees'];
      $this->_currency      = edd_get_currency();
      $this->_discount_code = $purchase_data['user_info']['discount'];
      $this->_payment_key   = $purchase_data['purchase_key'];

      $this->_add_cart_line_items();
    }
  }

  /**
   * [add_line_item description]
   * @param [type] $item_data
   */
  private function _add_line_item($item_data) {
    $n = $this->_line_item_counter;

    $line_item = array(
      'L_NAME'   . $n => $item_data['name'],
      'L_DESC'   . $n => $item_data['name'],
      'L_COST'   . $n => number_format($item_data['subtotal'], 2, '.', ''),
      'L_QTY'    . $n => $item_data['quantity'],
      'L_SKU'    . $n => $item_data['number']
    );

    if ( isset ( $item_data['tax'] ) ) {
      $line_item['L_TAXAMT' . $n] = number_format( $item_data['tax'], 2 );
    }

    $this->_line_items[] = $line_item;
    $this->_line_item_counter++;
  }


  /**
   * [_add_cart_line_items description]
   */
  private function _add_cart_line_items() {
    foreach( $this->_cart_details as $item ) {
      $item_data   = array(
        'name'     => html_entity_decode( $item['name'], ENT_COMPAT, 'UTF-8' ),
        'subtotal' => $item['subtotal'],
        'number'   => $item['id'],
        'quantity' => $item['quantity']
      );

      if ( isset ( $item['discount'] ) ) {
        $item_data['subtotal'] -= $item['discount'];
      }

      if ( isset ( $item['tax'] ) ) {
        $item_data['tax'] = $item['tax'];
      }

      $this->_item_total += $item_data['subtotal'];
      $this->_add_line_item( $item_data );
    }
  }


  /**
   * [retrieve_token description]
   *
   * @return array
   */
  public function retrieve_token() {
    $paypal_data = array(
      'PARTNER'             => $this->_partner,
      'VENDOR'              => $this->_manager_vendor,
      'USER'                => $this->_manager_username,
      'PWD'                 => $this->_manager_password,
      'TRXTYPE'             => 'S',
      'CREATESECURETOKEN'   => 'Y',
      'SECURETOKENID'       => self::_generate_secure_token_id(),
      'RETURNURL'           => self::_get_paypal_return_url(),
      'CANCELURL'           => self::_get_paypal_cancel_url(),
      'ERRORURL'            => self::_get_paypal_cancel_url(),
      'SILENTPOSTURL'       => self::_get_paypal_silent_post_url(),
      'LOCALECODE'          => self::get_locale(),
      'AMT'                 => $this->_total,
      'ITEMAMT'             => number_format( $this->_item_total, 2 ),
      'CURRENCY'            => $this->_currency,
      'TAXAMT'              => $this->_tax,
      'FREIGHTAMT'          => 0,
      'USER1'               => $this->_payment_key,
      'BILLTOFIRSTNAME'     => $this->_first_name,
      'BILLTOLASTNAME'      => $this->_last_name,
      'BILLTOSTREET'        => $this->_street_1,
      'BILLTOSTREET2'       => $this->_street_2,
      'BILLTOCITY'          => $this->_city,
      'BILLTOSTATE'         => $this->_state,
      'BILLTOZIP'           => $this->_zip,
      'BILLTOCOUNTRY'       => $this->_country,
      'EMAIL'               => $this->_email,
      'BILLTOEMAIL'         => $this->_email,
      'BUTTONSOURCE'        => self::$_bncode,
      'TEMPLATE'            => self::_get_eppa_manager_layout()
    );

    // Add line items to paypal data
    if ($this->_line_items) {
      foreach($this->_line_items as $i => $item) {
        $paypal_data = array_merge($paypal_data, $item);
      }
    }

    $token = $this->_paypal_query($paypal_data);

    // https://developer.paypal.com/docs/classic/payflow/integration-guide/#result-values-and-respmsg-text
    if ( strtoupper( $token['RESPMSG'] ) !== 'APPROVED' ) {
      throw new Exception( __( $token['RESPMSG'] ) );
    } else {
      return $token;
    }
  }


  /**
   * [refund_transaction description]
   * @since  1.0.5
   * @access public
   * @return mixed
   */
  public function refund_transaction( $transaction_id ) {
    $paypal_data = array(
      'PARTNER'   => $this->_partner,
      'VENDOR'    => $this->_manager_vendor,
      'USER'      => $this->_manager_username,
      'PWD'       => $this->_manager_password,
      'TRXTYPE'   => 'C',
      'ORIGID'    => $transaction_id
    );

    $response = $this->_paypal_query( $paypal_data );

    if ( strtoupper( $response['RESPMSG'] ) !== 'APPROVED' ) {
      throw new Exception( __( 'Something went wrong while refunding the Charge in PayPal.', 'edd_eppa' ) );
    } else {
      return TRUE;
    }
  }


  /**
   * [paypal_query description]
   * @param  [type] $params
   * @return [type]
   */
  private function _paypal_query($params) {

    $query = urldecode( http_build_query( $params ) );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $this->_token_endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    $response = curl_exec($ch);
    curl_close($ch);

    if (! $response) {
      edd_set_error( 0, __( 'Could not retrieve the token.', 'edd_eppa' ) );
      edd_send_back_to_checkout( '?payment-mode=paypal_payments_advanced' );
    }

    parse_str($response, $result);

    return $result;
  }

  /**
   * Verify a PPA Transaction
   *
   * @param  string $token
   * @return array
   */
  public function ppa_inquiry_transaction($token) {
    $params = array(
      'TRXTYPE' => 'I',
      'PARTNER' => $this->_partner,
      'VENDOR'  => $this->_manager_vendor,
      'USER'    => $this->_manager_username,
      'PWD'     => $this->_manager_password,
      'VERBOSITY'   => self::$_verbosity,
      'SECURETOKEN' => $token, # or PNREF
    );

    $remote_post_vars = array(
      'timeout'    => 20,
      'sslverify'  => false,
      'body'       => build_query($params)
    );

    return wp_remote_post( $this->_token_endpoint, $remote_post_vars );
  }

  /**
   * [get_purchase_id_by_token description]
   * @param  [type] $token
   * @return [type]
   */
  public function get_purchase_id_by_token( $token ) {
    global $wpdb;

    $purchase = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_edd_ppe_token' AND meta_value = %s LIMIT 1", $token ) );

    if ( $purchase != NULL ) {
      return $purchase;
    }

    return 0;
  }

  /**
   * [get_locale description]
   * @return [type]
   */
  public function get_locale() {
    $lang = substr( $_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2 );

    if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
      switch ( ICL_LANGUAGE_CODE ) {
        // WPML is active so use its language code
        case "fr":
          $locale = 'FR';
          break;
        case "it":
          $locale = 'IT';
          break;
        case "de":
          $locale = 'DE';
          break;
        default:
          $locale = 'US';
          break;
      }
    } else {

      switch ( $lang ) {
        // use browser language code
        case "fr":
          $locale = 'FR';
          break;
        case "it":
          $locale = 'IT';
          break;
        case "de":
          $locale = 'DE';
          break;
        default:
          $locale = 'US';
          break;
      }
    }

    return $locale;
  }

  /**
   * Set the PayPal Manager credentials based on the env
   *
   * @return void
   */
  private function _set_eppa_paypal_manager_credentials() {
    global $edd_options;

    $this->_partner          = $edd_options['live_paypal_manager_partner'] ? trim( $edd_options['live_paypal_manager_partner'] ) : 'PayPal';
    $this->_manager_username = $this->test_mode ? trim( $edd_options['test_paypal_manager_username'] ) : trim( $edd_options['live_paypal_manager_username'] );
    $this->_manager_password = $this->test_mode ? trim( $edd_options['test_paypal_manager_password'] ) : trim( $edd_options['live_paypal_manager_password'] );
    $this->_manager_vendor   = $this->test_mode ? trim( $this->_manager_username ) : trim( $edd_options['live_paypal_manager_vendor'] );

    if ( empty($this->_manager_username) OR empty($this->_manager_password) ) {
      edd_set_error( 0, __( 'You must enter your PayPal Manager details in settings', 'edd_eppa' ) );
      edd_send_back_to_checkout( '?payment-mode=paypal_payments_advanced' );
    }
  }

  /**
   * [_generate_secure_token_id description]
   * @return string
   */
  private static function _generate_secure_token_id() {
    return uniqid('', true);
  }

  /**
   * [_get_paypal_silent_post_url description]
   * @return string
   */
  private function _get_paypal_silent_post_url() {
    return trailingslashit( home_url( 'index.php' ) ) . '?edd-listener=eppa-silent-post';
  }

  /**
   * [_get_paypal_return_url description]
   * @return string
   */
  private function _get_paypal_return_url() {
    global $edd_options;
    return add_query_arg( 'payment-confirmation', 'paypal_payments_advanced', get_permalink( $edd_options['success_page'] ) );
  }

  /**
   * [_get_paypal_cancel_url description]
   * @return [type]
   */
  private function _get_paypal_cancel_url() {
    global $edd_options;
    return function_exists( 'edd_get_failed_transaction_uri' ) ? edd_get_failed_transaction_uri() : get_permalink( $edd_options['purchase_page'] );
  }

  /**
   * [_get_eppa_manager_layout description]
   * @return [type] [description]
   */
  private function _get_eppa_manager_layout() {
    global $edd_options;
    return $edd_options['eppa_manager_layout'];
  }

}