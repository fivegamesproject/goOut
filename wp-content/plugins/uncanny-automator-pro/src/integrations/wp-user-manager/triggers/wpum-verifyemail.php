<?php

namespace Uncanny_Automator_Pro;

class WPUM_VERIFYEMAIL {

	/**
	 * Integration code
	 *
	 * @var string
	 */
	public static $integration = 'WPUSERMANAGER';

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
		$this->trigger_code = 'WPUMUSERVERIFY';
		$this->trigger_meta = 'WPUMUVEMAILVERIFY';
		if ( class_exists( 'WPUM_User_Verification' ) ) {
			$this->define_trigger();
		}
	}

	/**
	 * Define and register the trigger by pushing it into the Automator object
	 */
	public function define_trigger() {

		$trigger = array(
			'author'              => Automator()->get_author_name( $this->trigger_code ),
			'support_link'        => Automator()->get_author_support_link( $this->trigger_code, 'integration/wp-user-manager/' ),
			'integration'         => self::$integration,
			'code'                => $this->trigger_code,
			'is_pro'              => true,
			/* translators: Logged-in trigger - WP User Manager */
			'sentence'            => __( 'A user verifies their email address', 'uncanny-automator-pro' ),
			/* translators: Logged-in trigger - WP User Manager */
			'select_option_name'  => __( 'A user verifies their email address', 'uncanny-automator-pro' ),
			'action'              => 'wpumuv_after_user_verification',
			'priority'            => 99,
			'accepted_args'       => 1,
			'validation_function' => array( $this, 'wpum_user_verified_email' ),
			'options'             => array(),
		);

		Automator()->register->trigger( $trigger );
	}

	/**
	 * @param $user_id
	 */
	public function wpum_user_verified_email( $user_id ) {

		if ( 0 === absint( $user_id ) ) {
			// Its a logged in recipe and
			// user ID is 0. Skip process
			return;
		}

		$pass_args = array(
			'code'           => $this->trigger_code,
			'meta'           => $this->trigger_meta,
			'user_id'        => $user_id,
			'ignore_post_id' => true,
		);

		Automator()->maybe_add_trigger_entry( $pass_args );

	}

}
