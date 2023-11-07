<?php

namespace Uncanny_Automator_Pro;

use Uncanny_Automator\Recipe;

/**
 * Class WSS_REJECT_LEAD
 *
 * @package Uncanny_Automator_Pro
 */
class WSS_REJECT_LEAD {

	use Recipe\Actions;

	/**
	 * Set up Automator action constructor.
	 */
	public function __construct() {
		if ( ! function_exists( 'wwlc_check_plugin_dependencies' ) ) {
			return;
		}
		$this->setup_action();
	}

	/**
	 * Define and register the action by pushing it into the Automator object
	 */
	protected function setup_action() {
		$this->set_integration( 'WHOLESALESUITE' );
		$this->set_action_code( 'WSSL_REJECT_LEAD' );
		$this->set_action_meta( 'WSSL_LEAD' );
		$this->set_requires_user( true );
		$this->set_is_pro( true );
		/* translators: Action - Wholesale suite - lead capture */
		$this->set_sentence( sprintf( esc_attr__( 'Reject a wholesale lead matching {{a user ID or email:%1$s}}', 'uncanny-automator-pro' ), $this->get_action_meta() ) );
		/* translators: Action - Wholesale suite - lead capture */
		$this->set_readable_sentence( esc_attr__( 'Reject a wholesale lead matching {{a user ID or email}}', 'uncanny-automator-pro' ) );
		$this->set_options_callback( array( $this, 'load_options' ) );
		$this->register_action();
	}

	/**
	 * load_options
	 *
	 * @return array
	 */
	public function load_options() {
		return Automator()->utilities->keep_order_of_options(
			array(
				'options' => array(
					Automator()->helpers->recipe->field->text(
						array(
							'option_code' => $this->get_action_meta(),
							'input_type'  => 'text',
							'label'       => esc_attr__( 'User', 'uncanny-automator-pro' ),
							'description' => esc_attr__( 'Enter a valid User ID or email address', 'uncanny-automator-pro' ),
						)
					),
				),
			)
		);

	}

	/**
	 * Process the action.
	 *
	 * @param $user_id
	 * @param $action_data
	 * @param $recipe_id
	 * @param $args
	 * @param $parsed
	 *
	 * @return void.
	 * @throws \Exception
	 */
	protected function process_action( $user_id, $action_data, $recipe_id, $args, $parsed ) {
		$user_email_id = isset( $parsed[ $this->get_action_meta() ] ) ? sanitize_text_field( $parsed[ $this->get_action_meta() ] ) : '';

		if ( empty( $user_email_id ) ) {
			return;
		}

		$user = get_userdata( $user_email_id );
		if ( false === $user ) {
			$user = get_user_by( 'email', $user_email_id );
		}

		if ( false !== $user ) {
			$new_user_account = new \WWLC_User_Account( array( 'WWLC_Emails' ) );
			$new_user_account->wwlc_reject_user( array( 'userID' => $user->ID ), \WWLC_Emails::instance() );
			Automator()->complete->action( $user_id, $action_data, $recipe_id );

			return;
		}

		$action_data['do-nothing']           = true;
		$action_data['complete_with_errors'] = true;
		Automator()->complete->action( $user_id, $action_data, $recipe_id, sprintf( __( 'A user matching (%s) was not found.', 'uncanny-automator-pro' ), $user_email_id ) );
	}

}
