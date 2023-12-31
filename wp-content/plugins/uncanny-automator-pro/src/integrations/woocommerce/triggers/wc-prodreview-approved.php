<?php

namespace Uncanny_Automator_Pro;

/**
 * Class WC_PRODREVIEW_APPROVED
 * @package Uncanny_Automator_Pro
 */
class WC_PRODREVIEW_APPROVED {

	/**
	 * Integration code
	 * @var string
	 */
	public static $integration = 'WC';

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
		$this->trigger_code = 'WCPRODREVIEWAPPRVD';
		$this->trigger_meta = 'WOOPRODUCT';
		$this->define_trigger();
	}

	/**
	 * Define and register the trigger by pushing it into the Automator object
	 */
	public function define_trigger() {

		$trigger = array(
			'author'              => Automator()->get_author_name( $this->trigger_code ),
			'support_link'        => Automator()->get_author_support_link( $this->trigger_code, 'integration/woocommerce/' ),
			'is_pro'              => true,
			'integration'         => self::$integration,
			'code'                => $this->trigger_code,
			/* translators: Logged-in trigger - WooCommerce */
			'sentence'            => sprintf( __( "A user's review on {{a product:%1\$s}} is approved", 'uncanny-automator-pro' ), $this->trigger_meta ),
			/* translators: Logged-in trigger - WooCommerce */
			'select_option_name'  => __( "A user's review on {{a product}} is approved", 'uncanny-automator-pro' ),
			'action'              => 'transition_comment_status',
			'priority'            => 90,
			'accepted_args'       => 3,
			'validation_function' => array( $this, 'comment_approved' ),
			'options_callback'    => array( $this, 'load_options' ),
		);

		Automator()->register->trigger( $trigger );
	}

	/**
	 * @return array
	 */
	public function load_options() {
		$options = Automator()->helpers->recipe->woocommerce->options->all_wc_products( __( 'Product', 'uncanny-automator' ) );

		$options['options'] = array( '-1' => __( 'Any product', 'uncanny-automator' ) ) + $options['options'];
		$options_array      = array(
			'options' => array(
				$options,
			),
		);

		return Automator()->utilities->keep_order_of_options( $options_array );
	}

	/**
	 * Validation function when the trigger action is hit
	 *
	 * @param int|string $new_status The new comment status.
	 * @param int|string $old_status The old comment status.
	 * @param \WP_Comment $comment Comment object.
	 */
	public function comment_approved( $new_status, $old_status, $comment ) {
		if ( 'review' !== (string) $comment->comment_type ) {
			return;
		}

		if ( 'approved' !== (string) $new_status ) {
			return;
		}

		if ( ! $comment instanceof \WP_Comment ) {
			return;
		}

		if ( isset( $comment->user_id ) && 0 === absint( $comment->user_id ) ) {
			return;
		}

		$recipes            = Automator()->get->recipes_from_trigger_code( $this->trigger_code );
		$required_post      = Automator()->get->meta_from_recipes( $recipes, $this->trigger_meta );
		$matched_recipe_ids = array();

		//Add where option is set to Any post / specific post
		foreach ( $recipes as $recipe_id => $recipe ) {
			foreach ( $recipe['triggers'] as $trigger ) {
				$trigger_id = $trigger['ID'];
				if ( intval( '-1' ) === intval( $required_post[ $recipe_id ][ $trigger_id ] ) || absint( $required_post[ $recipe_id ][ $trigger_id ] ) === absint( $comment->comment_post_ID ) ) {
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

		//	If recipe matches
		foreach ( $matched_recipe_ids as $matched_recipe_id ) {
			$pass_args = array(
				'code'             => $this->trigger_code,
				'meta'             => $this->trigger_meta,
				'user_id'          => $comment->user_id,
				'recipe_to_match'  => $matched_recipe_id['recipe_id'],
				'trigger_to_match' => $matched_recipe_id['trigger_id'],
				'post_id'          => $comment->comment_post_ID,
			);

			$args = Automator()->maybe_add_trigger_entry( $pass_args, false );

			do_action( 'uap_wp_comment_approve', $comment, $matched_recipe_id['recipe_id'], $matched_recipe_id['trigger_id'], $args );
			do_action( 'uap_wc_trigger_save_product_meta', $comment->comment_post_ID, $matched_recipe_id['recipe_id'], $args, 'product' );

			if ( $args ) {
				foreach ( $args as $result ) {
					if ( true === $result['result'] ) {
						Automator()->maybe_trigger_complete( $result['args'] );
					}
				}
			}
		}
	}

}
