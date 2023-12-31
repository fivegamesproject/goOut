<?php

namespace Uncanny_Automator_Pro;

/**
 * Class WP_USERCREATED
 * @package Uncanny_Automator_Pro
 */
class WP_USERCREATED {

	/**
	 * Integration code
	 * @var string
	 */
	public static $integration = 'WP';

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
		$this->trigger_code = 'WPUSERCREATED';
		$this->trigger_meta = 'USERCREATED';
		$this->define_trigger();
	}

	/**
	 * Define and register the trigger by pushing it into the Automator object
	 */
	public function define_trigger() {

		$trigger = array(
			'author'              => Automator()->get_author_name( $this->trigger_code ),
			'support_link'        => Automator()->get_author_support_link( $this->trigger_code, 'integration/wordpress-core/' ),
			'is_pro'              => true,
			'integration'         => self::$integration,
			'code'                => $this->trigger_code,
			'meta'                => $this->trigger_meta,
			/* translators: Logged-in trigger - WordPress Core */
			'sentence'            => __( 'A user is created', 'uncanny-automator-pro' ),
			/* translators: Logged-in trigger - WordPress Core */
			'select_option_name'  => __( 'A user is created', 'uncanny-automator-pro' ),
			'action'              => 'user_register',
			'priority'            => 10,
			'accepted_args'       => 1,
			'validation_function' => array( $this, 'save_data' ),
			'options'             => array(),
		);

		Automator()->register->trigger( $trigger );
	}

	/**
	 * Validation function when the trigger action is hit
	 *
	 * @param $user_id
	 */
	public function save_data( $user_id ) {

		$args = array(
			'code'           => $this->trigger_code,
			'meta'           => $this->trigger_meta,
			'post_id'        => - 1,
			'ignore_post_id' => true,
			'user_id'        => $user_id,
			'is_signed_in'   => true,
		);

		$user_data = get_userdata( $user_id );
		$args      = Automator()->maybe_add_trigger_entry( $args, false );

		// Save trigger meta
		if ( $args ) {
			foreach ( $args as $result ) {
				if ( true === $result['result'] && $result['args']['trigger_id'] && $result['args']['trigger_log_id'] ) {

					$run_number = Automator()->get->trigger_run_number( $result['args']['trigger_id'], $result['args']['trigger_log_id'], $user_id );
					$save_meta  = array(
						'user_id'        => $user_id,
						'trigger_id'     => $result['args']['trigger_id'],
						'run_number'     => $run_number, //get run number
						'trigger_log_id' => $result['args']['trigger_log_id'],
						'ignore_user_id' => true,
					);

					$save_meta['meta_key']   = 'first_name';
					$save_meta['meta_value'] = $user_data->first_name;
					Automator()->insert_trigger_meta( $save_meta );

					$save_meta['meta_key']   = 'last_name';
					$save_meta['meta_value'] = $user_data->last_name;
					Automator()->insert_trigger_meta( $save_meta );

					$save_meta['meta_key']   = 'email';
					$save_meta['meta_value'] = $user_data->user_email;
					Automator()->insert_trigger_meta( $save_meta );

					$save_meta['meta_key']   = 'username';
					$save_meta['meta_value'] = $user_data->user_login;
					Automator()->insert_trigger_meta( $save_meta );

					$save_meta['meta_key']   = 'user_id';
					$save_meta['meta_value'] = $user_data->ID;
					Automator()->insert_trigger_meta( $save_meta );

					Automator()->maybe_trigger_complete( $result['args'] );
				}
			}
		}

	}
}
