<?php

namespace Uncanny_Automator_Pro;

/**
 * Class WP_USER_META_KEY_NOT_EXISTS
 *
 * @package Uncanny_Automator_Pro
 */
class WP_USER_META_KEY_NOT_EXISTS extends Action_Condition {

	/**
	 * Method define_condition
	 *
	 * @return void
	 */
	public function define_condition() {

		$this->integration  = 'WP';
		$this->name         = __( '{{A specific}} meta key does not exist for the user', 'uncanny-automator-pro' );
		$this->code         = 'USER_META_KEY_NOT_EXISTS';
		$this->dynamic_name = sprintf(
		/* translators: the role name */
			esc_html__( '{{A specific:%1$s}} meta key does not exist for the user', 'uncanny-automator-pro' ),
			'METAKEY'
		);
		$this->is_pro        = true;
		$this->requires_user = true;
		$this->deprecated    = false;
	}

	/**
	 * Method fields
	 *
	 * @return array
	 */
	public function fields() {

		return array(
			$this->field->text(
				array(
					'option_code' => 'METAKEY',
					'label'       => esc_html__( 'Meta key', 'uncanny-automator-pro' ),
					'required'    => true,
				)
			),
		);
	}

	/**
	 * Evaluate_condition
	 *
	 * Has to use the $this->condition_failed( $message ); method if the condition is not met.
	 *
	 * @return void
	 * @throws \Exception
	 */
	public function evaluate_condition() {

		$meta_key  = $this->get_parsed_option( 'METAKEY' );
		$user_data = get_user_meta( $this->user_id );
		if ( empty( $user_data ) && ! is_array( $user_data ) ) {
			// Avoid fatals
			$user_data = array();
		}
		$condition_met = array_key_exists( $meta_key, $user_data );
		// If the conditions is not met, send an error message and mark the condition as failed.
		if ( true === $condition_met ) {
			$message = __( 'User has the required meta key: ', 'uncanny-automator-pro' ) . $meta_key;
			$this->condition_failed( $message );
		}
	}
}
