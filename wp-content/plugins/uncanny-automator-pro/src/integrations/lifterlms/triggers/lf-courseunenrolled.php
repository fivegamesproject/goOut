<?php

namespace Uncanny_Automator_Pro;

/**
 * Class LF_COURSEUNENROLLED
 *
 * @package Uncanny_Automator_Pro
 */
class LF_COURSEUNENROLLED {

	/**
	 * Integration code
	 *
	 * @var string
	 */
	public static $integration = 'LF';

	private $trigger_code;
	private $trigger_meta;

	/**
	 * Set up Automator trigger constructor.
	 */
	public function __construct() {
		$this->trigger_code = 'LFCOURSEUNENROLLED';
		$this->trigger_meta = 'LFCOURSEEN';
		$this->define_trigger();
	}

	/**
	 * Define and register the trigger by pushing it into the Automator object
	 */
	public function define_trigger() {

		$trigger = array(
			'author'              => Automator()->get_author_name( $this->trigger_code ),
			'support_link'        => Automator()->get_author_support_link( $this->trigger_code, 'integration/lifterlms/' ),
			'integration'         => self::$integration,
			'code'                => $this->trigger_code,
			'is_pro'              => true,
			/* translators: Logged-in trigger - LifterLMS */
			'sentence'            => sprintf( esc_attr__( 'A user is unenrolled from {{a course:%1$s}} {{a number of:%2$s}} time(s)', 'uncanny-automator' ), $this->trigger_meta, 'NUMTIMES' ),
			/* translators: Logged-in trigger - LifterLMS */
			'select_option_name'  => esc_attr__( 'A user is unenrolled from {{a course}}', 'uncanny-automator' ),
			'action'              => 'llms_user_removed_from_course',
			'priority'            => 20,
			'accepted_args'       => 4,
			'validation_function' => array( $this, 'lf_course_unenrolled' ),
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
					Automator()->helpers->recipe->lifterlms->options->all_lf_courses( esc_attr__( 'Course', 'uncanny-automator' ), $this->trigger_meta ),
					Automator()->helpers->recipe->options->number_of_times(),
				),
			)
		);
	}

	/**
	 * Validation function when the trigger action is hit
	 *
	 * @param $student_id
	 * @param $course_id
	 * @param $trigger
	 * @param $status
	 */
	public function lf_course_unenrolled( $student_id, $course_id, $trigger, $status ) {
		if ( empty( $course_id ) || $status != 'cancelled' ) {
			return;
		}

		$args = array(
			'code'    => $this->trigger_code,
			'meta'    => $this->trigger_meta,
			'post_id' => $course_id,
			'user_id' => $student_id,
		);

		Automator()->process->user->maybe_add_trigger_entry( $args );
	}

}
