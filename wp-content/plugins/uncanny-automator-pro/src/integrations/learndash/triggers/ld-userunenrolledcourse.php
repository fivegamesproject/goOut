<?php

namespace Uncanny_Automator_Pro;

/**
 * Class LD_USERUNENROLLEDCOURSE
 *
 * @package Uncanny_Automator_Pro
 */
class LD_USERUNENROLLEDCOURSE {

	/**
	 * Integration code
	 *
	 * @var string
	 */
	public static $integration = 'LD';

	private $trigger_code;
	private $trigger_meta;

	/**
	 * Set up Automator trigger constructor.
	 */
	public function __construct() {
		$this->trigger_code = 'LD_USERUNENROLLEDCOURSE';
		$this->trigger_meta = 'LDCOURSE';
		$this->define_trigger();
	}

	/**
	 * Define and register the trigger by pushing it into the Automator object
	 */
	public function define_trigger() {

		$trigger = array(
			'author'              => Automator()->get_author_name( $this->trigger_code ),
			'support_link'        => Automator()->get_author_support_link( $this->trigger_code, 'integration/learndash/' ),
			'is_pro'              => true,
			'integration'         => self::$integration,
			'code'                => $this->trigger_code,
			/* translators: Logged-in trigger - LearnDash */
			'sentence'            => sprintf( __( 'A user is unenrolled from {{a course:%1$s}}', 'uncanny-automator-pro' ), $this->trigger_meta ),
			/* translators: Logged-in trigger - LearnDash */
			'select_option_name'  => __( 'A user is unenrolled from {{a course}}', 'uncanny-automator' ),
			'action'              => 'learndash_update_course_access',
			'priority'            => 10,
			'accepted_args'       => 4,
			'validation_function' => array( $this, 'update_course_access' ),
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
					Automator()->helpers->recipe->learndash->options->all_ld_courses( null, $this->trigger_meta ),
				),
			)
		);
	}

	/**
	 * Validation function when the trigger action is hit
	 *
	 * @param $user_id
	 * @param $course_id
	 * @param $access_list
	 * @param $remove
	 */
	public function update_course_access( $user_id, $course_id, $access_list, $remove ) {

		if ( empty( $remove ) ) {
			return;
		}

		$args = array(
			'code'         => $this->trigger_code,
			'meta'         => $this->trigger_meta,
			'post_id'      => $course_id,
			'user_id'      => $user_id,
			'is_signed_in' => true,
		);

		Automator()->maybe_add_trigger_entry( $args );
	}

}
