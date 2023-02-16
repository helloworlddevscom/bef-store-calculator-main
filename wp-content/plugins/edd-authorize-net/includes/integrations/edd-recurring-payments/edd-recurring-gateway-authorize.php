<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;

/**
 * The gateway class which integrates Authorize.net with EDD Recurring Payments
 *
 * @since 2.0
 */
class EDD_Recurring_Authorize extends EDD_Recurring_Gateway {

	private $api_login_id;

	private $transaction_key;

	private $is_sandbox_mode;

	/**
	 * Get Authorize started
	 */
	public function init() {

		$this->id            = 'authorize';
		$this->friendly_name = __( 'Authorize.net', 'edda' );

		// Load Authorize SDK and define its contants.
		$this->define_authorize_values();

		// Handle webhooks from Authoriz.net that relate to subscriptions.
		add_action( 'edd_authorizenet_webhook_endpoint_handler', array( $this, 'handle_webhooks' ), 10, 3 );

		add_action( 'edd_recurring_download_price_row', array( $this, 'show_settings_note_about_daily_subscriptions' ), 1000 );

		add_filter( 'edd_payment_details_transaction_id-' . $this->id, array( $this, 'translate_temporary_transaction_ids' ), 10, 2 );
		add_filter( 'edd_subscription_details_transaction_id_' . $this->id, array( $this, 'translate_temporary_transaction_ids' ), 10, 2 );

	}

	/**
	 * Set API Login ID, Transaction Key and Mode
	 *
	 * @return void
	 */
	public function define_authorize_values() {
		$this->api_login_id    = edd_get_option( 'edda_api_login' );
		$this->transaction_key = edd_get_option( 'edd_transaction_key' );
		$this->is_sandbox_mode = edd_is_test_mode();

		// Set the Authorize.net endpoint based on test mode or live mode in EDD.
		if ( $this->is_sandbox_mode ) {
			$this->endpoint = \net\authorize\api\constants\ANetEnvironment::SANDBOX;
		} else {
			$this->endpoint = \net\authorize\api\constants\ANetEnvironment::PRODUCTION;
		}

	}

	/**
	 * Show a note that Authorize.net does not work with daily subscriptions
	 *
	 * @access      public
	 * @since       2.0
	 * @return      void
	 */
	public function show_settings_note_about_daily_subscriptions() {
		?>
		<div class="eddauthnet-daily-subs-warning" style="padding: 10px;">
			<span><?php echo esc_html( __( 'Note: Authorize.net does not allow daily subscriptions', 'edda' ) ); ?></span>
		</div>
		<?php
	}

	/**
	 * Records purchased subscriptions in the database and creates an edd_payment record.
	 * This method was taken directly from the EDD Recurring Gateway base class because
	 * the sub needs to remain "pending" until the webhook comes in. The base class only sets
	 * subscriptions to "pending if they are offsite, but this is onsite. Therefore we have to overwrite
	 * this method here.
	 *
	 * @access      public
	 * @since       2.0
	 * @return      void
	 */
	public function record_signup() {

		$payment_data = array(
			'price'        => $this->purchase_data['price'],
			'date'         => $this->purchase_data['date'],
			'user_email'   => $this->purchase_data['user_email'],
			'purchase_key' => $this->purchase_data['purchase_key'],
			'currency'     => edd_get_currency(),
			'downloads'    => $this->purchase_data['downloads'],
			'user_info'    => $this->purchase_data['user_info'],
			'cart_details' => $this->purchase_data['cart_details'],
			'status'       => 'pending',
		);

		foreach ( $this->subscriptions as $key => $item ) {

			// If there's a free trial, adjust the cart details for this payment so that relevant values are reduced to be free.
			if ( ! empty( $item['has_trial'] ) ) {
				$payment_data['cart_details'][ $key ]['item_price'] = $item['initial_amount'] - $item['initial_tax'];
				$payment_data['cart_details'][ $key ]['tax']        = $item['initial_tax'];
				$payment_data['cart_details'][ $key ]['price']      = 0;
				$payment_data['cart_details'][ $key ]['discount']   = 0;

			}
		}

		// Record the completed payment.
		$this->payment_id = edd_insert_payment( $payment_data );
		$payment          = new EDD_Payment( $this->payment_id );
		$payment->status = 'complete';  // This can be complete as we've already verified the card is chargeable.
		$payment->save();

		// Set subscription_payment.
		$payment->update_meta( '_edd_subscription_payment', true );

		/*
		* We need to delete pending subscription records to prevent duplicates. This ensures no duplicate subscription records are created when a purchase is being recovered. See:
		* https://github.com/easydigitaldownloads/edd-recurring/issues/707
		* https://github.com/easydigitaldownloads/edd-recurring/issues/762
		*/
		global $wpdb;
		$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}edd_subscriptions WHERE parent_payment_id = %d AND status = 'pending';", $this->payment_id ) );

		$subscriber = new EDD_Recurring_Subscriber( $this->customer_id );

		// Now create the subscription record(s).
		foreach ( $this->subscriptions as $subscription ) {

			if ( isset( $subscription['status'] ) ) {
				$status = $subscription['status'];
			} else {
				$status = 'active';
			}

			$trial_period = ! empty( $subscription['has_trial'] ) ? $subscription['trial_quantity'] . ' ' . $subscription['trial_unit'] : '';

			$args = array(
				'product_id'         => $subscription['id'],
				'user_id'            => $this->purchase_data['user_info']['id'],
				'parent_payment_id'  => $this->payment_id,
				'status'             => $status,
				'period'             => $subscription['period'],
				'initial_amount'     => $subscription['initial_amount'],
				'initial_tax_rate'   => $subscription['initial_tax_rate'],
				'initial_tax'        => $subscription['initial_tax'],
				'recurring_amount'   => $subscription['recurring_amount'],
				'recurring_tax_rate' => $subscription['recurring_tax_rate'],
				'recurring_tax'      => $subscription['recurring_tax'],
				'bill_times'         => $subscription['bill_times'],
				'expiration'         => $subscriber->get_new_expiration( $subscription['id'], $subscription['price_id'], $trial_period ),
				'trial_period'       => $trial_period,
				'profile_id'         => $subscription['profile_id'],
				'transaction_id'     => $subscription['transaction_id'],
			);

			$args = apply_filters( 'edd_recurring_pre_record_signup_args', $args, $this );
			$sub  = $subscriber->add_subscription( $args );

			if ( ! $this->offsite && $trial_period ) {
				$subscriber->add_meta( 'edd_recurring_trials', $subscription['id'] );
			}
		}

		// Now look if the gateway reported any failed subscriptions and log a payment note.
		if ( ! empty( $this->failed_subscriptions ) ) {

			foreach ( $this->failed_subscriptions as $failed_subscription ) {
				$note = sprintf( __( 'Failed creating subscription for %1$s. Gateway returned: %2$s', 'edd-recurring' ), $failed_subscription['subscription']['name'], $failed_subscription['error'] );
				$payment->add_note( $note );
			}

			$payment->update_meta( '_edd_recurring_failed_subscriptions', $this->failed_subscriptions );
		}

		if ( ! empty( $this->custom_meta ) ) {
			foreach ( $this->custom_meta as $key => $value ) {
				$payment->update_meta( $key, $value );
			}
		}

	}

	/**
	 * Creates subscription payment profiles and sets the IDs so they can be stored
	 *
	 * @return bool true on success and false on failure
	 */
	public function create_payment_profiles() {

		$card_info = $this->purchase_data['card_info'];
		$user_info = $this->purchase_data['user_info'];

		foreach ( $this->subscriptions as $key => $subscription ) {

			/**
			 * Authorize.net does not charge subscription payments immediately.
			 * Rather, they batch charge them all at a set time. That means we don't know
			 * if a transaction actually went through until possibly hours later.
			 * But the customer will believe that they paid now, and won't want to wait hours to get their subcription access.
			 * We have a catch22: the customer has not ACTUALLY paid yet, but they need access now.
			 * Therefore, the approach we will take is to authorize their card for the amount, but not actually charge it.
			 * If the authoriztation works, we will assume the subscription payment will go through, and give the customer access now.
			 * If the payment fails once Authorize.net does their batches, we will fail the subscription then through the webhook.
			 */

			 if ( ! empty( $subscription['has_trial'] ) ) {
				$authorization_amount = '1.00';
			} else {
				$authorization_amount = $subscription['initial_amount'];
			}

			$card_authorized = $this->pre_authorize_amount( $authorization_amount, $card_info, $user_info );
			if ( true !== $card_authorized['success'] ) {
				$error_message = edd_authorize_net_error_code_to_message( $card_authorized['error_code'] );
				edd_set_error( 'edd_recurring_authorize_error', $error_message . wp_json_encode( $card_authorized['response'] ), 'edd-recurring' );
				return false;
			}

			/**
			 * Now that we have authorized the card and proven it can work, we'll create the subscription with
			 * the assumtption that the payment will work.
			 */
			$response = $this->create_authorize_net_subscription( $subscription, $card_info, $user_info );

			// If th subscription was created at Authorize.net successfully, and we are now waiting for them to process the 1st payment.
			if ( 'Ok' === $response->getMessages()->getResultCode() ) {

				$this->subscriptions[ $key ]['profile_id']     = $response->getSubscriptionId();
				$this->subscriptions[ $key ]['transaction_id'] = 'waiting_for_webhook';

				if ( ! empty( $subscription['has_trial'] ) ) {

					$this->subscriptions[ $key ]['status']         = 'trialling';
					$this->subscriptions[ $key ]['transaction_id'] = 'waiting_for_trial_completion';

				}

				$is_success = true;

			} else {

				// Something went wrong with the siubscription creation, and we know what it was.
				if ( isset( $response->messages->message ) ) {

					edd_set_error( 'edd_recurring_authorize_error', $response->messages->message->code . ': ' . $response->messages->message->text, 'edd-recurring' );

				} else {

					// Something went wrong with the siubscription creation, and we don't know what it was.
					edd_set_error( 'edd_recurring_authorize_error', __( 'Your subscription cannot be created due to an error at the gateway.', 'edd-recurring' ) );

				}

				// TODO: Should log the error.
				$is_success = false;

			}
		}

		return $is_success;
	}

	/**
	 * Test a credit card to see if it is chargeable at Authorize.net.
	 *
	 * @return array $response If the authorization was successful, return a success array, otherwise an array with the error code.
	 */
	private function pre_authorize_amount( $authorization_amount, $card_info, $user_info ) {

		$merchant_authentication = new AnetAPI\MerchantAuthenticationType();
		$merchant_authentication->setName( $this->api_login_id );
		$merchant_authentication->setTransactionKey( $this->transaction_key );

		/**
		 * Add credit card details and create payment object.
		 */
		$formatted_card_info  = $this->format_card_info( $card_info );

		$credit_card = new AnetAPI\CreditCardType();
		$credit_card->setCardNumber( $formatted_card_info['cardNumber'] );
		$credit_card->setExpirationDate( $formatted_card_info['expirationDate'] );
		$credit_card->setCardCode( $formatted_card_info['cardCode'] );

		$payment = new AnetAPI\PaymentType();
		$payment->setCreditCard( $credit_card );

		// Test an auth-only transaction - no actual charge takes place.
		$transaction_request_type = new AnetAPI\TransactionRequestType();
		$transaction_request_type->setTransactionType( 'authOnlyTransaction' );
		$transaction_request_type->setAmount( $authorization_amount );
		$transaction_request_type->setPayment( $payment );
		$request = new AnetAPI\CreateTransactionRequest();
		$request->setMerchantAuthentication( $merchant_authentication );
		$request->setTransactionRequest( $transaction_request_type );
		$controller = new AnetController\CreateTransactionController( $request );
		$response   = $controller->executeWithApiResponse( $this->endpoint );

		// Handle the response.
		if ( 'Ok' !== $response->getMessages()->getResultCode() ) {
			return array(
				'success'    => false,
				'error_code' => $response->getMessages()->getResultCode(),
				'response'   => $response,
			);
		}

		return array(
			'success' => true,
		);
	}

	/**
	 * Creates a new Automatted Recurring Billing (ARB) subscription
	 *
	 * @param EDD_Subscription $subscription The EDD_Subscription object from which to gnenrate an Auth.net subscription.
	 * @param array            $card_info The card info.
	 * @param array            $user_info The user info.
	 * @return array $response The response from AnetController\ARBCreateSubscriptionController->executeWithApiResponse
	 */
	public function create_authorize_net_subscription( $subscription, $card_info, $user_info ) {

		// Set date to same timezone as Authorize's servers (Pacific Standard Time) to prevent conflicts.
		date_default_timezone_set( 'America/Los_Angeles' );
		$today = new DateTime( 'today' );

		// Create a merchantAuthenticationType object with authentication details.
		$merchant_authentication = new AnetAPI\MerchantAuthenticationType();
		$merchant_authentication->setName( $this->api_login_id );
		$merchant_authentication->setTransactionKey( $this->transaction_key );

		// Set the transaction's refId.
		$ref_id = 'ref' . time();

		// Subscription Type Info.
		$authnet_subscription = new AnetAPI\ARBSubscriptionType();
		$authnet_subscription->setName( $this->generate_subscription_name( $subscription['id'], $subscription['name'], $subscription['price_id'] ) );

		// Get the EDD Interval for this subscription.
		$edd_interval_array = $this->get_interval( $subscription['period'] );

		$interval = new AnetAPI\PaymentScheduleType\IntervalAType();
		$interval->setLength( $edd_interval_array['length'] );
		$interval->setUnit( $edd_interval_array['unit'] );

		$payment_schedule = new AnetAPI\PaymentScheduleType();
		$payment_schedule->setInterval( $interval );
		$payment_schedule->setStartDate( $today );
		$payment_schedule->setTotalOccurrences( ( 0 === $subscription['bill_times'] ) ? 9999 : $subscription['bill_times'] );

		// Apply the free trial.
		if ( ! empty( $subscription['has_trial'] ) ) {
			$payment_schedule->setStartDate( new DateTime( '+' . $subscription['trial_quantity'] . ' ' . $subscription['trial_unit'] ) );
		}

		$authnet_subscription->setPaymentSchedule( $payment_schedule );
		$authnet_subscription->setAmount( $subscription['recurring_amount'] );

		$formatted_card_info = $this->format_card_info( $card_info );

		$credit_card = new AnetAPI\CreditCardType();
		$credit_card->setCardNumber( $formatted_card_info['cardNumber'] );
		$credit_card->setExpirationDate( $formatted_card_info['expirationDate'] );
		$credit_card->setCardCode( $formatted_card_info['cardCode'] );

		$payment = new AnetAPI\PaymentType();
		$payment->setCreditCard( $credit_card );
		$authnet_subscription->setPayment( $payment );

		$order = new AnetAPI\OrderType();
		$order->setInvoiceNumber( isset( $subscription['parent_payment_id'] ) ? $subscription['parent_payment_id'] : false );
		$order->setDescription( $this->generate_subscription_name( $subscription['id'], $subscription['name'], $subscription['price_id'] ) );
		$authnet_subscription->setOrder( $order );

		$bill_to = new AnetAPI\NameAndAddressType();
		$bill_to->setFirstName( $user_info['first_name'] );
		$bill_to->setLastName( $user_info['last_name'] );

		$authnet_subscription->setBillTo( $bill_to );

		$request = new AnetAPI\ARBCreateSubscriptionRequest();
		$request->setmerchantAuthentication( $merchant_authentication );
		$request->setRefId( $ref_id );
		$request->setSubscription( $authnet_subscription );
		$controller = new AnetController\ARBCreateSubscriptionController( $request );

		// Create the subscription at Authorize.net.
		$response = $controller->executeWithApiResponse( $this->endpoint );

		// If the response from Authorize.net had no value.
		if ( null === $response ) {
			return array(
				'success' => false,
				'msg_id'  => 'no_response_returned',
				'message' => __( 'No response returned', 'edda' ),
			);
		}

		// Check to see if the API request was successfully received and acted upon.
		if ( 'Ok' !== $response->getMessages()->getResultCode() ) {

			// Check if Authorize.net gave us any helpful error messages we can show.
			$error_messages = $response->getMessages()->getMessage();
			if ( null !== $error_messages ) {
				edd_set_error( $error_messages[0]->getCode(), $error_messages[0]->getText() );
			} else {

				// If there are no errors, but we have a response code, build errors using it.
				if ( ! empty( $response ) ) {
					$response_code = $response->getResponseCode();

					switch ( $response_code ) {
						case '2':
							// https://developer.authorize.net/api/reference/responseCodes.html?code=2.
							edd_set_error( 'declined', __( 'This transaction has been declined.', 'edda' ) );
							break;
						case '3':
							// https://developer.authorize.net/api/reference/responseCodes.html?code=3.
							edd_set_error( 'declined', __( 'This transaction has been declined.', 'edda' ) );
							break;
						case '4':
							// https://developer.authorize.net/api/reference/responseCodes.html?code=4.
							edd_set_error( 'declined', __( 'This transaction has been declined.', 'edda' ) );
							break;
						case '5':
							// https://developer.authorize.net/api/reference/responseCodes.html?code=5.
							edd_set_error( 'declined', __( 'A valid amount is required', 'edda' ) );
							break;
						case '6':
							// https://developer.authorize.net/api/reference/responseCodes.html?code=6.
							edd_set_error( 'declined', __( 'The credit card number is invalid.', 'edda' ) );
							break;
						case '7':
							// https://developer.authorize.net/api/reference/responseCodes.html?code=7.
							edd_set_error( 'declined', __( 'Credit card expiration date is invalid.', 'edda' ) );
							break;
						case '8':
							// https://developer.authorize.net/api/reference/responseCodes.html?code=8.
							edd_set_error( 'declined', __( 'The credit card has expired.', 'edda' ) );
							break;
						default:
							edd_set_error( 'api_error', sprintf( __( 'An error occurred. Error data: %s', 'edda' ), print_r( $response, true ) ) );
							break;
					}
				} else {
					edd_set_error( 'api_error', sprintf( __( 'An error occurred. Error data: %s', 'edda' ), print_r( $response, true ) ) );
				}
			}
		}

		return $response;

	}

	/**
	 * Generates subscription name
	 *
	 * @param  integer $download_id The Download ID of the subscription, from where the name actually comes.
	 * @param  string  $form_title The name of the download.
	 * @param  integer $price_id The price ID of the download.
	 * @return string
	 */
	public function generate_subscription_name( $download_id, $form_title = '', $price_id = 0 ) {

		if ( ! empty( $form_title ) ) {
			$subscription_name = $form_title;
		} else {
			$subscription_name = get_post_field( 'post_title', $download_id );
		}

		if ( 0 !== $price_id ) {
			$subscription_name .= ' - ' . edd_get_price_option_name( $download_id, $price_id );
		}

		return $subscription_name;
	}

	/**
	 * Gets interval length and interval unit for Authorize.net based on Give subscription period
	 *
	 * @param  string $subscription_period The interval for this subscription.
	 * @return array
	 */
	public function get_interval( $subscription_period ) {

		$length = '1';
		$unit   = 'days';

		switch ( $subscription_period ) {

			case 'day':
				$unit = 'days';
				break;
			case 'week':
				$length = '7';
				$unit   = 'days';
				break;
			case 'month':
				$length = '1';
				$unit   = 'months';
				break;
			case 'quarter':
				$length = '3';
				$unit   = 'months';
				break;
			case 'semi-year':
				$length = '6';
				$unit   = 'months';
				break;
			case 'year':
				$length = '12';
				$unit   = 'months';
				break;
		}

		return array(
			'length' => $length,
			'unit'   => $unit,
		);
	}

	/**
	 * Determines if the subscription can be cancelled.
	 *
	 * @param  bool             $ret Determines if the subscription can be updated.
	 * @param  EDD_Subscription $subscription EDD Subscription object.
	 * @return bool
	 */
	public function can_cancel( $ret, $subscription ) {
		if ( 'authorize' === $subscription->gateway && ! empty( $subscription->profile_id ) && in_array( $subscription->status, $this->get_cancellable_statuses(), true ) ) {
			$ret = true;
		}
		return $ret;
	}

	/**
	 * Cancels a subscription
	 *
	 * @param  EDD_Subscription $subscription The EDD Subscription being cancelled..
	 * @param  bool             $valid A boolean value from EDD Recurring.
	 * @return bool
	 */
	public function cancel( $subscription, $valid ) {

		if ( empty( $valid ) ) {
			return false;
		}

		// Create a merchantAuthenticationType object with authentication details.
		$merchant_authentication = new AnetAPI\MerchantAuthenticationType();
		$merchant_authentication->setName( $this->api_login_id );
		$merchant_authentication->setTransactionKey( $this->transaction_key );

		// Set the transaction's refId.
		$ref_id  = 'ref' . time();
		$request = new AnetAPI\ARBCancelSubscriptionRequest();
		$request->setMerchantAuthentication( $merchant_authentication );
		$request->setRefId( $ref_id );
		$request->setSubscriptionId( $subscription->profile_id );

		$controller = new AnetController\ARBCancelSubscriptionController( $request );
		$response   = $controller->executeWithApiResponse( $this->endpoint );

		// If it was cancelled successfully.
		if ( ( null !== $response ) && ( 'Ok' === $response->getMessages()->getResultCode() ) ) {
			return true;
		}

		return false;

	}

	/**
	 * Determines if the subscription can be updated
	 *
	 * @access public
	 * @since  2.0
	 * @param  bool             $ret Determines if the subscription can be updated.
	 * @param  EDD_Subscription $subscription EDD Subscription object.
	 * @return bool
	 */
	public function can_update( $ret, $subscription ) {
		if ( 'authorize' === $subscription->gateway && ! empty( $subscription->profile_id ) && ( 'active' === $subscription->status || 'failing' === $subscription->status || 'trialling' === $subscription->status ) ) {
			return true;
		}
		return $ret;
	}

	/**
	 * Process the update payment form
	 *
	 * @since 2.0
	 * @param int $subscriber    EDD_Recurring_Subscriber.
	 * @param int $subscription  EDD_Subscription.
	 * @return void
	 */
	public function update_payment_method( $subscriber, $subscription ) {

		$card_number    = isset( $_POST['card_number'] ) && is_numeric( $_POST['card_number'] ) ? $_POST['card_number'] : '';
		$card_exp_month = isset( $_POST['card_exp_month'] ) && is_numeric( $_POST['card_exp_month'] ) ? $_POST['card_exp_month'] : '';
		$card_exp_year  = isset( $_POST['card_exp_year'] ) && is_numeric( $_POST['card_exp_year'] ) ? $_POST['card_exp_year'] : '';
		$card_cvc       = isset( $_POST['card_cvc'] ) && is_numeric( $_POST['card_cvc'] ) ? $_POST['card_cvc'] : '';
		$card_zip       = isset( $_POST['card_zip'] ) ? sanitize_text_field( $_POST['card_zip'] ) : '';

		$card_info = array(
			'card_number'    => $card_number,
			'card_exp_month' => $card_exp_month,
			'card_exp_year'  => $card_exp_year,
			'card_cvc'       => $card_cvc,
		);

		$formatted_card_info = $this->format_card_info( $card_info );
		$values              = array_search( '', $formatted_card_info, true );

		if ( ! empty( $values ) ) {
			edd_set_error( 'edd_recurring_authnet', __( 'Please enter all required fields.', 'edd-recurring' ) );
		}

		$errors = edd_get_errors();

		if ( ! $errors ) {

			// Create a merchantAuthenticationType object with authentication details.
			$merchant_authentication = new AnetAPI\MerchantAuthenticationType();
			$merchant_authentication->setName( $this->api_login_id );
			$merchant_authentication->setTransactionKey( $this->transaction_key );

			// Set the transaction's refId.
			$ref_id               = 'ref' . time();
			$authnet_subscription = new AnetAPI\ARBSubscriptionType();

			$credit_card = new AnetAPI\CreditCardType();
			$credit_card->setCardNumber( $formatted_card_info['cardNumber'] );
			$credit_card->setExpirationDate( $formatted_card_info['expirationDate'] );
			$credit_card->setCardCode( $formatted_card_info['cardCode'] );

			$payment = new AnetAPI\PaymentType();
			$payment->setCreditCard( $credit_card );
			$authnet_subscription->setPayment( $payment );

			$request = new AnetAPI\ARBUpdateSubscriptionRequest();
			$request->setMerchantAuthentication( $merchant_authentication );
			$request->setRefId( $ref_id );
			$request->setSubscriptionId( $subscription->profile_id );
			$request->setSubscription( $authnet_subscription );
			$controller = new AnetController\ARBUpdateSubscriptionController( $request );
			$response   = $controller->executeWithApiResponse( $this->endpoint );

			if ( ( null !== $response ) && ( 'Ok' === $response->getMessages()->getResultCode() ) ) {
				$error_messages = $response->getMessages()->getMessage();

			} else {

				$error_messages = $response->getMessages()->getMessage();

				if ( ! $response->getMessages()->getMessage() ) {
					edd_set_error( 'edd_recurring_authorize_error', __( 'There was an error updating your payment method.', 'edd-recurring' ) );
				} else {
					edd_set_error( 'edd_recurring_authorize_error', $error_messages[0]->getCode() . ': ' . $error_messages[0]->getText(), 'edd-recurring' );
				}
			}
		}
	}

	/**
	 * Given a transaction ID, get the associated subscription from Authorize.net
	 *
	 * @since 2.0
	 * @param string $transaction_id The transaction ID for which want to get the associated Auth.net subscriptiion.
	 * @return \net\authorize\api\contract\v1\SubscriptionPaymentType|false
	 */
	protected function get_transaction_subscription( $transaction_id ) {

		edd_debug_log( 'Getting get_transaction_subscription for ' . $transaction_id );

		// Create a merchantAuthenticationType object with authentication details.
		$merchant_authentication = new AnetAPI\MerchantAuthenticationType();
		$merchant_authentication->setName( $this->api_login_id );
		$merchant_authentication->setTransactionKey( $this->transaction_key );

		edd_debug_log( 'So far so good ' . $transaction_id );

		$request = new AnetAPI\GetTransactionDetailsRequest();

		edd_debug_log( 'one' );
		edd_debug_log( wp_json_encode( $request ) );

		$request->setMerchantAuthentication( $merchant_authentication );

		edd_debug_log( 'two' );

		$request->setTransId( $transaction_id );

		edd_debug_log( 'three' );

		$controller = new AnetController\GetTransactionDetailsController( $request );

		edd_debug_log( 'four' );
		edd_debug_log( wp_json_encode( $controller ) );

		$response = $controller->executeWithApiResponse( $this->endpoint );

		edd_debug_log( 'RESPONSER is ' . wp_json_encode( $response ) );

		if ( ( null !== $response ) && ( 'Ok' === $response->getMessages()->getResultCode() ) ) {

			edd_debug_log( 'Its good ' . wp_json_encode( $response ) );

			return $response->getTransaction()->getSubscription();
		} else {

			edd_debug_log( 'Its NO good ' . wp_json_encode( $response ) );

			return false;
		}
	}

	/**
	 * When displaying the transaction IDs to the user, if one is a temporary one from Authorize.net, localize it with a user-friendly message.
	 *
	 * @since 2.0
	 * @param string $transaction_id The transaction ID for which want to get the associated Auth.net subscriptiion.
	 * @return \net\authorize\api\contract\v1\SubscriptionPaymentType|false
	 */
	public function translate_temporary_transaction_ids( $sub_transaction_id, $edd_payment_id ) {

		if ( 'waiting_for_webhook' === $sub_transaction_id ) {
			return __( 'Waiting for Authorize.net to complete the transaction', 'edda' );
		}

		if ( 'waiting_for_trial_completion' === $sub_transaction_id ) {
			return __( 'When the trial period is completed, the first transaction ID will be here.', 'edda' );
		}

		return $sub_transaction_id;
	}

	/**
	 * Processes webhooks from Authorize.net that relate to subscriptions
	 * This method is hooked to the edd_authorizenet_webhook_endpoint_handler hook in the authorize.net gateway
	 *
	 * @access      public
	 * @since       2.0
	 * @param       array $webhook_data The data in this Authorize.net webhook.
	 * @return      void
	 */
	public function handle_webhooks( $webhook_data ) {

		edd_debug_log( 'RECURRING WEBHOOKS ARE RUNNING' );

		// Get the event type this webhook is for.
		$event_type = $webhook_data['eventType'];

		// Handle subscription related webhooks.
		switch ( $event_type ) {

			/**
			* Auth capture created. Let's check if it's for a renewal payment.
			*
			* Since Authorize.net sends this webhook for both one-time payments and subscriptions,
			* we have to fetch the full transaction details from Authorize.net in a separate call to
			* find out which subscription it's for. We do that using $this->get_transaction_subscription( $transaction_id )
			*
			* 'payload' => array(
			*      'responseCode' => 1,     // Success
			*      'authCode'     => '',
			*      'avsResponse'  => 'Y',
			*      'authAmount'   => 00.00,
			*      'entityName'   => 'transaction',
			*      'id'           => '123', // Transaction ID.
			* )
			*/
			case 'net.authorize.payment.authcapture.created':
				edd_debug_log( 'Authorize.net webhook RECURRING - net.authorize.payment.authcapture.created' );
				edd_debug_log( 'Authorize.net webhook PAYLOAD - net.authorize.payment.authcapture.created' . wp_json_encode( $webhook_data ) );

				$transaction_id = ! empty( $webhook_data['payload']['id'] ) ? $webhook_data['payload']['id'] : '';

				if ( empty( $transaction_id ) ) {
					edd_debug_log( 'Exiting Authorize.net webhook - missing transaction ID.', true );

					wp_die( __( 'Missing transaction ID.', 'edda' ), __( 'Error', 'edda' ), array( 'response' => 500 ) );
				}

				edd_debug_log( sprintf( 'Processing payment.authcapture.created for transaction ID %s.', $transaction_id ) );

				$subscription = $this->get_transaction_subscription( $transaction_id );

				edd_debug_log( sprintf( '$subscription value is %s', wp_json_encode( $subscription ) ) );

				if ( empty( $subscription ) ) {
					edd_debug_log( 'Exiting Authorize.net webhook - this is a one-time payment.' );

					die();
				}

				edd_debug_log( sprintf( 'Sub ID is %s.', wp_json_encode( $subscription->getId() ) ) );

				$subscription_id  = $subscription->getId();
				$edd_subscription = new EDD_Subscription( $subscription_id, true );

				edd_debug_log( sprintf( 'EDD Subscription:  %s.', wp_json_encode( $edd_subscription ) ) );

				if ( 0 === $edd_subscription->id ) {
					edd_debug_log( sprintf( 'Exiting Authorize.net webhook - unable to find associated EDD Subscription ID %s.', $subscription_id ) );
					die();
				}

				// Based on the response code Authorize.net gives us, carry out the actions for this Subscription.
				if ( 1 === $webhook_data['payload']['responseCode'] ) {

					edd_debug_log( sprintf( 'Authorize.net webhook - net.authorize.payment.authcapture.created for subscription ID %s with data %s.', $edd_subscription->id, wp_json_encode( $webhook_data ) ) );

					// If the subscription does not have a transaction ID yet, this is the initial payment.
					// Assign the transaction ID to the sub and to the initial EDD Payment.
					if ( 'waiting_for_webhook' === $edd_subscription->transaction_id || 'waiting_for_trial_completion' === $edd_subscription->transaction_id ) {
						$edd_subscription->set_transaction_id( $transaction_id );
						$edd_parent_payment                 = edd_get_payment( $edd_subscription->parent_payment_id );
						$edd_parent_payment->transaction_id = $transaction_id;
						$edd_subscription->add_note( 'Authorize.net Webhook (net.authorize.payment.authcapture.created) assigned the transaction ID to this subscription: ' . $transaction_id );
					} else {

						// This is a renewal payment for the subscription, so log a renewal payment.
						$payment_id = $edd_subscription->add_payment(
							array(
								'amount' => sanitize_text_field( $webhook_data['payload']['authAmount'] ),
							)
						);

						// Now that we've added a renewal payment to the subscription, we have to also renew it.
						$edd_subscription->renew( $payment_id );

					}
				} elseif ( 2 === $webhook_data['payload']['responseCode'] ) {

					// Declined.
					$edd_subscription->failing();
					do_action( 'edd_recurring_payment_failed', $edd_subscription );
					do_action( 'edd_recurring_authorizenet_silent_post_error', $edd_subscription );

					// Payment declined.
					edd_debug_log( sprintf( 'Processing Authorize.net webhook - declined payment for Subscription %s with error code %s.', $edd_subscription->id, $webhook_data['payload']['responseCode'] ) );

				} elseif ( 3 === $webhook_data['payload']['responseCode'] || 8 === $webhook_data['payload']['responseCode'] ) {

					// An expired card.
					$edd_subscription->failing();
					do_action( 'edd_recurring_payment_failed', $edd_subscription );
					do_action( 'edd_recurring_authorizenet_silent_post_error', $edd_subscription );

					// Payment declined.
					edd_debug_log( sprintf( 'Processing Authorize.net webhook - declined payment for Subscription %s with error code %s.', $edd_subscription->id, $webhook_data['payload']['responseCode'] ) );

				} else {

					// Other Error.
					do_action( 'edd_recurring_authorizenet_silent_post_error', $edd_subscription );

					// Payment declined.
					edd_debug_log( sprintf( 'Authorize.net webhook - net.authorize.payment.authcapture.created for subscription ID %s with error code %s.', $edd_subscription->id, $webhook_data['payload']['responseCode'] ) );

				}

				break;

			/**
			* Subscription cancelled.
			* This would only be used if the subscription were manually cancelled inside Authorize.net.
			*
			* 'payload' => array(
			*      'name'       => '',          // Subscription name
			*      'amount'     => 00.00,       // Subscription price
			*      'status'     => 'cancelled', // Subscription status
			*      'profile'    => array(
			*          'customerProfileId'        => 123,
			*          'customerPaymentProfileId' => 123,
			*      ),
			*      'entityName' => 'subscription',
			*      'id'         => '123',       // Subscription ID.
			* )
			*/
			case 'net.authorize.customer.subscription.cancelled':
				$subscription_id = 'anet_' . $webhook_data['payload']['id'];

				edd_debug_log( sprintf( 'Authorize.net webhook - net.authorize.customer.subscription.cancelled for subscription ID %s.', $subscription_id ) );

				$edd_subscription = new EDD_Subscription( $subscription_id, true );

				// Cancel the EDD Subscription so it matches the Authorize.net Subscription.
				$edd_subscription->cancel();

				break;

			case 'net.authorize.customer.subscription.updated':
				$subscription_id = 'anet_' . $webhook_data['payload']['id'];

				edd_debug_log( sprintf( 'Authorize.net webhook - net.authorize.customer.subscription.updated for subscription ID %s.', $subscription_id ) );

				break;
			case 'net.authorize.payment.capture.created':
				$subscription_id = 'anet_' . $webhook_data['payload']['id'];

				edd_debug_log( sprintf( 'Authorize.net webhook - net.authorize.payment.capture.created. Webhook Data: %s.', wp_json_encode( $webhook_data ) ) );

				break;
		}

	}

	/**
	 * Link the recurring profile in Authorize.net.
	 *
	 * @since  2.0
	 * @param  string $profile_id   The recurring profile id.
	 * @param  object $subscription The Subscription object.
	 * @return string               The link to return or just the profile id.
	 */
	public function link_profile_id( $profile_id, $subscription ) {

		if ( ! empty( $profile_id ) ) {
			$html = '<a href="%s" target="_blank">' . $profile_id . '</a>';

			$payment  = new EDD_Payment( $subscription->parent_payment_id );
			$base_url = 'live' === $payment->mode ? 'https://authorize.net/' : 'https://sandbox.authorize.net/';
			$link     = esc_url( $base_url . 'ui/themes/sandbox/ARB/SubscriptionDetail.aspx?SubscrID=' . $profile_id );

			$profile_id = sprintf( $html, $link );
		}

		return $profile_id;

	}

	/**
	 * Given the $card_info array, generate the card info array for use with the API
	 *
	 * @since   2.0
	 * @param   array $card_info The Card Info from the checkout form.
	 * @return  array            Formatted card info for the Authorize.net API
	 */
	private function format_card_info( $card_info = array() ) {
		$card_details = array(
			'cardNumber'     => str_replace( ' ', '', wp_strip_all_tags( trim( $card_info['card_number'] ) ) ),
			'expirationDate' => wp_strip_all_tags( trim( $card_info['card_exp_month'] ) ) . '/' . wp_strip_all_tags( trim( $card_info['card_exp_year'] ) ),
			'cardCode'       => wp_strip_all_tags( trim( $card_info['card_cvc'] ) ),
		);

		return $card_details;
	}

}
