<?php

namespace Uncanny_Automator_Pro;

/**
 * Class AFFWP_WCSALE
 *
 * @package Uncanny_Automator_Pro
 */
class AFFWP_WCSALE {

	/**
	 * Integration code
	 *
	 * @var string
	 */
	public static $integration = 'AFFWP';

	/**
	 * @var string
	 */
	private $trigger_code;
	/**
	 * @var string
	 */
	private $trigger_meta;

	/**
	 * Set up Automator trigger constructor.
	 */
	public function __construct() {
		$this->trigger_code = 'WCSALES';
		$this->trigger_meta = 'REFERSWCPRODUCT';
		if ( class_exists( 'WooCommerce' ) ) {
			$this->define_trigger();
		}
	}

	/**
	 * Define and register the trigger by pushing it into the Automator object
	 */
	public function define_trigger() {

		$trigger = array(
			'author'              => Automator()->get_author_name( $this->trigger_code ),
			'support_link'        => Automator()->get_author_support_link( $this->trigger_code, 'integration/affiliatewp/' ),
			'integration'         => self::$integration,
			'code'                => $this->trigger_code,
			'is_pro'              => true,
			/* translators: Logged-in trigger - Affiliate WP */
			'sentence'            => sprintf( __( 'A user purchases {{a WooCommerce product:%1$s}} using an affiliate referral', 'uncanny-automator-pro' ), $this->trigger_meta ),
			/* translators: Logged-in trigger - Affiliate WP */
			'select_option_name'  => __( 'A user purchases {{a WooCommerce product}} using an affiliate referral', 'uncanny-automator-pro' ),
			'action'              => 'affwp_insert_referral',
			'priority'            => 99,
			'accepted_args'       => 1,
			'validation_function' => array( $this, 'refers_wc_sale' ),
			'options_callback'    => array( $this, 'load_options' ),
		);

		Automator()->register->trigger( $trigger );
	}

	/**
	 * Callback method to trigger's options_callback property.
	 *
	 * @return mixed[];
	 */
	public function load_options() {
		return Automator()->utilities->keep_order_of_options(
			array(
				'options' => array(
					Automator()->helpers->recipe->affiliate_wp->options->pro->get_wc_products( null, $this->trigger_meta, array( 'any_option' => true ) ),
				),
			)
		);
		return Automator()->utilities->keep_order_of_options( $options );
	}

	/**
	 * @param $referral_id
	 *
	 * @return mixed
	 */
	public function refers_wc_sale( $referral_id ) {
		$referral = affwp_get_referral( $referral_id );

		if ( 'woocommerce' !== (string) $referral->context ) {
			return;
		}

		$order_id = $referral->reference;
		if ( empty( $order_id ) ) {
			return;
		}

		$order = wc_get_order( $order_id );
		if ( ! $order instanceof \WC_Order ) {
			return;
		}

		$user_id = $order->get_customer_id();
		if ( 0 === absint( $user_id ) ) {
			// Its a logged in recipe and
			// user ID is 0. Skip process
			return;
		}

		$items       = $order->get_items();
		$product_ids = array();
		/** @var \WC_Order_Item_Product $item */
		foreach ( $items as $item ) {
			$product_ids[] = (int) $item->get_product_id();
		}
		$recipes            = Automator()->get->recipes_from_trigger_code( $this->trigger_code );
		$required_product   = Automator()->get->meta_from_recipes( $recipes, $this->trigger_meta );
		$matched_recipe_ids = array();

		foreach ( $recipes as $recipe_id => $recipe ) {
			foreach ( $recipe['triggers'] as $trigger ) {
				$trigger_id = $trigger['ID'];
				if ( in_array( absint( $required_product[ $recipe_id ][ $trigger_id ] ), $product_ids, false ) || intval( '-1' ) === intval( $required_product[ $recipe_id ][ $trigger_id ] ) ) {
					$matched_recipe_ids[] = array(
						'recipe_id'  => $recipe_id,
						'trigger_id' => $trigger_id,
					);
				}
			}
		}

		$user      = get_user_by( 'id', $user_id );
		$affiliate = affwp_get_affiliate( $referral->affiliate_id );
		if ( ! empty( $matched_recipe_ids ) ) {
			foreach ( $matched_recipe_ids as $matched_recipe_id ) {
				$pass_args = array(
					'code'             => $this->trigger_code,
					'meta'             => $this->trigger_meta,
					'user_id'          => $user_id,
					'recipe_to_match'  => $matched_recipe_id['recipe_id'],
					'trigger_to_match' => $matched_recipe_id['trigger_id'],
					'ignore_post_id'   => true,
				);

				$args = Automator()->maybe_add_trigger_entry( $pass_args, false );

				if ( $args ) {
					foreach ( $args as $result ) {
						if ( true === $result['result'] ) {

							$trigger_meta = array(
								'user_id'        => $user_id,
								'trigger_id'     => $result['args']['trigger_id'],
								'trigger_log_id' => $result['args']['trigger_log_id'],
								'run_number'     => $result['args']['run_number'],
							);

							$trigger_meta['meta_key']   = $this->trigger_meta;
							$trigger_meta['meta_value'] = maybe_serialize( $referral->description );
							Automator()->insert_trigger_meta( $trigger_meta );

							$trigger_meta['meta_key']   = 'REFERRALTYPE';
							$trigger_meta['meta_value'] = maybe_serialize( $referral->type );
							Automator()->insert_trigger_meta( $trigger_meta );

							$trigger_meta['meta_key']   = 'AFFILIATEWPID';
							$trigger_meta['meta_value'] = maybe_serialize( $referral->affiliate_id );
							Automator()->insert_trigger_meta( $trigger_meta );

							$trigger_meta['meta_key']   = 'AFFILIATEWPSTATUS';
							$trigger_meta['meta_value'] = maybe_serialize( $affiliate->status );
							Automator()->insert_trigger_meta( $trigger_meta );

							$trigger_meta['meta_key']   = 'AFFILIATEWPREGISTERDATE';
							$trigger_meta['meta_value'] = maybe_serialize( $affiliate->date_registered );
							Automator()->insert_trigger_meta( $trigger_meta );

							$trigger_meta['meta_key']   = 'AFFILIATEWPPAYMENTEMAIL';
							$trigger_meta['meta_value'] = maybe_serialize( affwp_get_affiliate_payment_email( $affiliate->ID ) );
							Automator()->insert_trigger_meta( $trigger_meta );

							$trigger_meta['meta_key']   = 'AFFILIATEWPACCEMAIL';
							$trigger_meta['meta_value'] = maybe_serialize( $user->user_email );
							Automator()->insert_trigger_meta( $trigger_meta );

							$trigger_meta['meta_key']   = 'AFFILIATEWPWEBSITE';
							$trigger_meta['meta_value'] = maybe_serialize( $user->user_url );
							Automator()->insert_trigger_meta( $trigger_meta );

							$trigger_meta['meta_key']   = 'AFFILIATEWPURL';
							$trigger_meta['meta_value'] = maybe_serialize( affwp_get_affiliate_referral_url( array( 'affiliate_id' => $referral->affiliate_id ) ) );
							Automator()->insert_trigger_meta( $trigger_meta );

							$trigger_meta['meta_key']   = 'AFFILIATEWPREFRATE';
							$trigger_meta['meta_value'] = affwp_get_affiliate_rate( $referral->affiliate_id );
							Automator()->insert_trigger_meta( $trigger_meta );

							$trigger_meta['meta_key']   = 'AFFILIATEWPREFRATETYPE';
							$trigger_meta['meta_value'] = affwp_get_affiliate_rate_type( $referral->affiliate_id );
							Automator()->insert_trigger_meta( $trigger_meta );

							$trigger_meta['meta_key']   = 'AFFILIATEWPPROMOMETHODS';
							$trigger_meta['meta_value'] = maybe_serialize( get_user_meta( $affiliate->user_id, 'affwp_promotion_method', true ) );
							Automator()->insert_trigger_meta( $trigger_meta );

							$trigger_meta['meta_key']   = 'AFFILIATEWPNOTES';
							$trigger_meta['meta_value'] = maybe_serialize( affwp_get_affiliate_meta( $affiliate->affiliate_id, 'notes', true ) );
							Automator()->insert_trigger_meta( $trigger_meta );

							$trigger_meta['meta_key']   = 'REFERRALAMOUNT';
							$trigger_meta['meta_value'] = number_format( affwp_calc_referral_amount( $order->get_total(), $affiliate->ID ), 2 );
							Automator()->insert_trigger_meta( $trigger_meta );

							$trigger_meta['meta_key']   = 'REFERRALDATE';
							$trigger_meta['meta_value'] = maybe_serialize( date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $referral->date ) ) );
							Automator()->insert_trigger_meta( $trigger_meta );

							$trigger_meta['meta_key']   = 'REFERRALDESCRIPTION';
							$trigger_meta['meta_value'] = maybe_serialize( $referral->description );
							Automator()->insert_trigger_meta( $trigger_meta );

							$trigger_meta['meta_key']   = 'REFERRALCONTEXT';
							$trigger_meta['meta_value'] = maybe_serialize( $referral->context );
							Automator()->insert_trigger_meta( $trigger_meta );

							$trigger_meta['meta_key']   = 'REFERRALREFERENCE';
							$trigger_meta['meta_value'] = maybe_serialize( $referral->reference );
							Automator()->insert_trigger_meta( $trigger_meta );

							$trigger_meta['meta_key']   = 'REFERRALCUSTOM';
							$trigger_meta['meta_value'] = maybe_serialize( $referral->custom );
							Automator()->insert_trigger_meta( $trigger_meta );

							$trigger_meta['meta_key']   = 'REFERRALSTATUS';
							$trigger_meta['meta_value'] = maybe_serialize( affwp_get_referral_status( $referral->ID ) );
							Automator()->insert_trigger_meta( $trigger_meta );

							$dynamic_coupons = affwp_get_dynamic_affiliate_coupons( $affiliate->ID, false );
							$coupons         = '';
							if ( isset( $dynamic_coupons ) && is_array( $dynamic_coupons ) ) {
								foreach ( $dynamic_coupons as $coupon ) {
									$coupons .= $coupon->coupon_code . '<br/>';
								}
							}

							$trigger_meta['meta_key']   = 'AFFILIATEWPCOUPON';
							$trigger_meta['meta_value'] = maybe_serialize( $coupons );
							Automator()->insert_trigger_meta( $trigger_meta );

							Automator()->maybe_trigger_complete( $result['args'] );
						}
					}
				}
			}
		}
	}

}
