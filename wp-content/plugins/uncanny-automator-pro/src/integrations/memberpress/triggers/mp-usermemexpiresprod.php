<?php //phpcs:ignore WordPress.Files.FileName.InvalidClassFileName

namespace Uncanny_Automator_Pro;

/**
 * Class MP_USERMEMEXPIRESPROD
 *
 * @package Uncanny_Automator_Pro
 */
class MP_USERMEMEXPIRESPROD {

	/**
	 * Integration code
	 *
	 * @var string
	 */
	public static $integration = 'MP';

	/**
	 * @var string
	 */
	private $trigger_code;
	/**
	 * @var string
	 */
	private $trigger_meta;


	/**
	 * SetAutomatorTriggers constructor.
	 */
	public function __construct() {
		$this->trigger_code = 'USERMEMEXPIRESPROD';
		$this->trigger_meta = 'MPPRODUCT';
		$this->define_trigger();
	}


	/**
	 * Define and register the trigger by pushing it into the Automator object
	 */
	public function define_trigger() {

		$trigger = array(
			'author'              => Automator()->get_author_name(),
			'support_link'        => Automator()->get_author_support_link( $this->trigger_code, 'integration/memberpress/' ),
			'integration'         => self::$integration,
			'code'                => $this->trigger_code,
			'is_pro'              => true,
			/* translators: Logged-in trigger - MemberPress */
			'sentence'            => sprintf( esc_attr__( "A user's membership to {{a specific product:%1\$s}} expires", 'uncanny-automator-pro' ), $this->trigger_meta ),
			/* translators: Logged-in trigger - MemberPress */
			'select_option_name'  => esc_attr__( "A user's membership to {{a specific product}} expires", 'uncanny-automator-pro' ),
			'action'              => 'mepr-event-transaction-expired',
			'priority'            => 99,
			'accepted_args'       => 1,
			'validation_function' => array( $this, 'mp_product_expired' ),
			'options_callback'    => array( $this, 'load_options' ),
		);

		Automator()->register->trigger( $trigger );
	}

	/**
	 * @return array[]
	 */
	public function load_options() {
		return Automator()->utilities->keep_order_of_options(
			array(
				'options' => array(
					Automator()->helpers->recipe->memberpress->options->pro->all_memberpress_products( null, $this->trigger_meta, array( 'uo_include_any' => true ) ),
				),
			)
		);
	}

	/**
	 * @param \MeprEvent $event
	 */
	public function mp_product_expired( \MeprEvent $event ) {

		/** @var \MeprTransaction $transaction */
		$transaction = $event->get_data();

		// is transaction actually expired?
		if ( false === $transaction->is_expired() ) {
			return;
		}

		// if it is a subscription check if the subscription is expired.
		if ( isset( $transaction->subscription_id ) && (int) $transaction->subscription_id > 0 ) {
			/** @var \MeprSubscription $sub */
			$sub = $transaction->subscription();
			if ( ( isset( $sub->status ) && \MeprSubscription::$cancelled_str !== (string) $sub->status ) || ! $sub->is_expired() ) {
				return;
			}
		}

		/** @var \MeprUser $user */
		$user       = $transaction->user();
		$product_id = absint( $transaction->rec->product_id );
		$user_id    = absint( $user->rec->ID );

		$recipes            = Automator()->get->recipes_from_trigger_code( $this->trigger_code );
		$required_product   = Automator()->get->meta_from_recipes( $recipes, $this->trigger_meta );
		$matched_recipe_ids = array();

		//Add where option is set to Any product
		foreach ( $recipes as $recipe_id => $recipe ) {
			foreach ( $recipe['triggers'] as $trigger ) {
				$trigger_id = $trigger['ID'];//return early for all products
				if ( absint( $required_product[ $recipe_id ][ $trigger_id ] ) === $product_id || intval( '-1' ) === intval( $required_product[ $recipe_id ][ $trigger_id ] ) ) {
					$matched_recipe_ids[] = array(
						'recipe_id'  => $recipe_id,
						'trigger_id' => $trigger_id,
					);
				}
			}
		}

		if ( empty( $matched_recipe_ids ) ) {
			return;
		}

		foreach ( $matched_recipe_ids as $matched_recipe_id ) {
			$args = array(
				'code'             => $this->trigger_code,
				'meta'             => $this->trigger_meta,
				'user_id'          => $user_id,
				'recipe_to_match'  => $matched_recipe_id['recipe_id'],
				'trigger_to_match' => $matched_recipe_id['trigger_id'],
				'ignore_post_id'   => true,
				'is_signed_in'     => true,
			);
			$args = Automator()->maybe_add_trigger_entry( $args, false );

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
						$trigger_meta['meta_value'] = $product_id;
						Automator()->insert_trigger_meta( $trigger_meta );

						Automator()->maybe_trigger_complete( $result['args'] );
					}
				}
			}
		}
	}

}
