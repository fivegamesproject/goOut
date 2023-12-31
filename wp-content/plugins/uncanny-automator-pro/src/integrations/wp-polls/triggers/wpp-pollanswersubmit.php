<?php

namespace Uncanny_Automator_Pro;

/**
 * Class WPP_POLLANSWERSUBMIT
 *
 * @package Uncanny_Automator_Pro
 */
class WPP_POLLANSWERSUBMIT {

	/**
	 * Integration code
	 *
	 * @var string
	 */
	public static $integration = 'WPP';

	private $trigger_code;
	private $trigger_meta;

	/**
	 * Set up Automator trigger constructor.
	 */
	public function __construct() {
		$this->trigger_code = 'WPPOLLANSWERSUBMIT';
		$this->trigger_meta = 'WPPOLL';
		$this->define_trigger();
	}

	/**
	 * Define and register the trigger by pushing it into the Automator object
	 */
	public function define_trigger() {

		$trigger = array(
			'author'              => Automator()->get_author_name( $this->trigger_code ),
			'support_link'        => Automator()->get_author_support_link( $this->trigger_code, 'integration/wp-polls/' ),
			'integration'         => self::$integration,
			'code'                => $this->trigger_code,
			'is_pro'              => true,
			/* translators: Logged-in trigger - WP Polls */
			'sentence'            => sprintf( esc_attr__( 'A user submits {{a poll:%1$s}} with {{a specific answer:%2$s}} selected', 'uncanny-automator-pro' ), $this->trigger_meta, 'WPPOLLANSWER:' . $this->trigger_meta ),
			/* translators: Logged-in trigger - WP Polls */
			'select_option_name'  => esc_attr__( 'A user submits {{a poll}} with {{a specific answer}} selected', 'uncanny-automator-pro' ),
			'action'              => 'wp_polls_vote_poll_success',
			'priority'            => 1,
			'accepted_args'       => 0,
			'validation_function' => array( $this, 'poll_success' ),
			'options_callback'    => array( $this, 'load_options' ),

		);

		Automator()->register->trigger( $trigger );
	}

	/**
	 * @return array[]
	 */
	public function load_options() {
		global $wpdb;

		// Get Poll Questions
		$questions = $wpdb->get_results( "SELECT pollq_id, pollq_question FROM $wpdb->pollsq ORDER BY pollq_id DESC" );

		$questions_options = array();

		foreach ( $questions as $question ) {
			$title = $question->pollq_question;

			if ( empty( $title ) ) {
				$title = sprintf( __( 'ID: %s (no title)', 'uncanny-automator-pro' ), $question->pollq_id );
			}

			$questions_options[ $question->pollq_id ] = $title;
		}

		return Automator()->utilities->keep_order_of_options(
			array(
				'options'       => array(),
				'options_group' => array(
					$this->trigger_meta => array(
						Automator()->helpers->recipe->field->select_field_ajax(
							$this->trigger_meta,
							esc_attr__( 'Poll', 'uncanny-automator-pro' ),
							$questions_options,
							'',
							'',
							false,
							true,
							array(
								'target_field' => 'WPPOLLANSWER',
								'endpoint'     => 'select_answers_from_wppoll',
							),
							array(
								$this->trigger_meta => __( 'Poll question', 'uncanny-automator-pro' ),
								$this->trigger_meta . '_ANSWERS' => __( 'Poll answers', 'uncanny-automator-pro' ),
								$this->trigger_meta . '_START' => __( 'Poll start date', 'uncanny-automator-pro' ),
								$this->trigger_meta . '_END' => __( 'Poll end date', 'uncanny-automator-pro' ),
							)
						),
						Automator()->helpers->recipe->field->select_field(
							'WPPOLLANSWER',
							esc_attr__( 'Answers', 'uncanny-automator' ),
							array(),
							'',
							false,
							false,
							array( 'WPPOLLANSWER' => __( 'Poll selected answer(s)', 'uncanny-automator-pro' ) ),
							array( 'supports_tokens' => false )
						),
					),
				),
			)
		);
	}

	public function poll_success() {

		if ( isset( $_REQUEST['action'] ) && sanitize_key( $_REQUEST['action'] ) === 'polls' ) {

			// Get Poll ID
			$poll_id = ( isset( $_REQUEST['poll_id'] ) ? (int) sanitize_key( $_REQUEST['poll_id'] ) : 0 );

			// Ensure Poll ID Is Valid
			if ( $poll_id === 0 ) {
				return;
			}

			// Verify Referer
			if ( ! check_ajax_referer( 'poll_' . $poll_id . '-nonce', 'poll_' . $poll_id . '_nonce', false ) ) {
				return;
			}

			$view = sanitize_key( $_REQUEST['view'] );
			if ( 'process' === $view ) {
				$poll_aid_array = array_unique( array_map( 'intval', array_map( 'sanitize_key', explode( ',', $_POST[ "poll_$poll_id" ] ) ) ) );

				$recipes              = Automator()->get->recipes_from_trigger_code( $this->trigger_code );
				$required_poll_id     = Automator()->get->meta_from_recipes( $recipes, $this->trigger_meta );
				$required_question_id = Automator()->get->meta_from_recipes( $recipes, 'WPPOLLANSWER' );

				foreach ( $recipes as $recipe_id => $recipe ) {
					foreach ( $recipe['triggers'] as $trigger ) {
						$trigger_id = $trigger['ID'];//return early for all products
						if (
							isset( $required_poll_id[ $recipe_id ] )
							&& isset( $required_poll_id[ $recipe_id ][ $trigger_id ] )
							&& isset( $required_question_id[ $recipe_id ] )
							&& isset( $required_question_id[ $recipe_id ][ $trigger_id ] )
						) {
							if (
								(string) $poll_id === (string) $required_poll_id[ $recipe_id ][ $trigger_id ]
								&& in_array( $required_question_id[ $recipe_id ][ $trigger_id ], $poll_aid_array )
							) {

								$pass_args = array(
									'code'           => $this->trigger_code,
									'meta'           => $this->trigger_meta,
									'ignore_post_id' => true,
									'user_id'        => get_current_user_id(),
								);

								$args = Automator()->maybe_add_trigger_entry( $pass_args, false );

								if ( isset( $args ) ) {
									foreach ( $args as $result ) {
										if ( true === $result['result'] ) {

											$trigger_meta = array(
												'user_id' => get_current_user_id(),
												'trigger_id' => $result['args']['trigger_id'],
												'trigger_log_id' => $result['args']['trigger_log_id'],
												'run_number' => $result['args']['run_number'],
											);

											$trigger_meta['meta_key']   = $this->trigger_meta;
											$trigger_meta['meta_value'] = $poll_id;
											Automator()->insert_trigger_meta( $trigger_meta );

											$trigger_meta['meta_key']   = 'WPPOLLANSWER';
											$trigger_meta['meta_value'] = maybe_serialize( $poll_aid_array );
											Automator()->insert_trigger_meta( $trigger_meta );

											Automator()->maybe_trigger_complete( $result['args'] );
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}

}
