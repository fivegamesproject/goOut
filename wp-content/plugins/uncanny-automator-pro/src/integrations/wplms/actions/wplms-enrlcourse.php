<?php

namespace Uncanny_Automator_Pro;

/**
 * Class WPLMS_ENRLCOURSE
 *
 * @package Uncanny_Automator_Pro
 */
class WPLMS_ENRLCOURSE {

	/**
	 * Integration code
	 *
	 * @var string
	 */
	public static $integration = 'WPLMS';

	private $action_code;
	private $action_meta;

	/**
	 * Set up Automator action constructor.
	 */
	public function __construct() {
		$this->action_code = 'WPLMSENRLCOURSE';
		$this->action_meta = 'WPLMSCOURSE';
		$this->define_action();
	}

	/**
	 * Define and register the action by pushing it into the Automator object
	 */
	public function define_action() {

		$action = array(
			'author'             => Automator()->get_author_name( $this->action_code ),
			'support_link'       => Automator()->get_author_support_link( $this->action_code, 'integration/wp-lms/' ),
			'is_pro'             => true,
			'integration'        => self::$integration,
			'code'               => $this->action_code,
			/* translators: Action - LifterLMS */
			'sentence'           => sprintf( esc_attr__( 'Enroll the user in {{a course:%1$s}}', 'uncanny-automator-pro' ), $this->action_meta ),
			/* translators: Action - LifterLMS */
			'select_option_name' => esc_attr__( 'Enroll the user in {{a course}}', 'uncanny-automator-pro' ),
			'priority'           => 10,
			'accepted_args'      => 1,
			'execution_function' => array( $this, 'wplms_enroll_in_course' ),
			'options_callback'   => array( $this, 'load_options' ),
		);

		Automator()->register->action( $action );
	}

	/**
	 * @return array[]
	 */
	public function load_options() {
		return Automator()->utilities->keep_order_of_options(
			array(
				'options' => array(
					Automator()->helpers->recipe->wplms->options->all_wplms_courses( esc_attr__( 'Course', 'uncanny-automator' ), $this->action_meta, false ),
				),
			)
		);
	}

	/**
	 * Validation function when the action is hit
	 *
	 * @param $user_id
	 * @param $action_data
	 * @param $recipe_id
	 */
	public function wplms_enroll_in_course( $user_id, $action_data, $recipe_id, $args ) {

		if ( ! function_exists( 'bp_course_add_user_to_course' ) ) {
			$error_message = 'The function bp_course_add_user_to_course does not exist';
			Automator()->complete_action( $user_id, $action_data, $recipe_id, $error_message );

			return;
		}

		$course_id = $action_data['meta'][ $this->action_meta ];

		//Enroll to New Course
		bp_course_add_user_to_course( $user_id, $course_id, '', true );

		Automator()->complete_action( $user_id, $action_data, $recipe_id );
	}

}
