<?php

namespace Uncanny_Automator_Pro;

/**
 * Class ANON_HF_SUBMITFIELD
 *
 * @package Uncanny_Automator_Pro
 */
class ANON_HF_SUBMITFIELD {

	/**
	 * Integration code
	 *
	 * @var string
	 */
	public static $integration = 'HF';

	private $trigger_code;
	private $trigger_meta;

	/**
	 * Set up Automator trigger constructor.
	 */
	public function __construct() {
		$this->trigger_code = 'ANONHFSUBMITFIELD';
		$this->trigger_meta = 'ANONHFFORM';
		$this->define_trigger();
	}

	/**
	 * Define and register the trigger by pushing it into the Automator object
	 */
	public function define_trigger() {

		$trigger = array(
			'author'              => Automator()->get_author_name(),
			'support_link'        => Automator()->get_author_support_link( $this->trigger_code, 'integration/happyforms/' ),
			'is_pro'              => true,
			'integration'         => self::$integration,
			'code'                => $this->trigger_code,
			'sentence'            => sprintf(
			/* translators: Logged-in trigger - Formidable */
				__( '{{A form:%1$s}} is submitted with {{a specific value:%2$s}} in {{a specific field:%3$s}}', 'uncanny-automator-pro' ),
				$this->trigger_meta,
				'SUBVALUE' . ':' . $this->trigger_meta,
				$this->trigger_code . ':' . $this->trigger_meta
			),
			/* translators: Logged-in trigger - Formidable */
			'select_option_name'  => __( '{{A form}} is submitted with {{a specific value}} in {{a specific field}}', 'uncanny-automator-pro' ),
			'action'              => 'happyforms_submission_success',
			'priority'            => 10,
			'accepted_args'       => 3,
			'type'                => 'anonymous',
			'validation_function' => array( $this, 'hf_submit_form' ),
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
				'options'       => array(),
				'options_group' => array(
					$this->trigger_meta => array(
						Automator()->helpers->recipe->happyforms->options->all_happyforms_forms(
							null,
							$this->trigger_meta,
							array(
								'token'        => false,
								'is_ajax'      => true,
								'target_field' => $this->trigger_code,
								'endpoint'     => 'select_form_fields_HFFORMS',
							)
						),
						Automator()->helpers->recipe->field->select_field( $this->trigger_code, __( 'Field', 'uncanny-automator-pro' ) ),
						Automator()->helpers->recipe->field->text_field( 'SUBVALUE', __( 'Value', 'uncanny-automator-pro' ) ),
					),
				),
			)
		);
	}

	/**
	 * Validation function when the trigger action is hit
	 *
	 * @param $entry_id
	 * @param $form_id
	 */
	public function hf_submit_form( $submission, $form, $misc ) {

		$recipes = Automator()->get->recipes_from_trigger_code( $this->trigger_code );

		if ( empty( $submission ) ) {
			return;
		}

		$conditions = Automator()->helpers->recipe->happyforms->pro->match_condition( $submission, $form['ID'], $recipes, $this->trigger_meta, $this->trigger_code, 'SUBVALUE' );

		if ( ! $conditions ) {
			return;
		}

		$user_id = get_current_user_id();
		if ( ! empty( $conditions ) ) {
			foreach ( $conditions['recipe_ids'] as $trigger_id => $recipe_id ) {
				if ( ! Automator()->is_recipe_completed( $recipe_id, $user_id ) ) {
					$args = array(
						'code'             => $this->trigger_code,
						'meta'             => $this->trigger_meta,
						'recipe_to_match'  => $recipe_id,
						'trigger_to_match' => $trigger_id,
						'ignore_post_id'   => true,
						'user_id'          => $user_id,
					);

					$result = Automator()->maybe_add_trigger_entry( $args, false );

					if ( $result ) {
						foreach ( $result as $r ) {
							if ( true === $r['result'] ) {
								if ( isset( $r['args'] ) && isset( $r['args']['trigger_log_id'] ) ) {
									//Saving form values in trigger log meta for token parsing!
									$fi_args = array(
										'trigger_id'     => (int) $r['args']['trigger_id'],
										'meta_key'       => $this->trigger_meta,
										'user_id'        => $user_id,
										'trigger_log_id' => $r['args']['trigger_log_id'],
										'run_number'     => $r['args']['run_number'],
									);

									Automator()->helpers->recipe->happyforms->extract_save_hf_fields( $submission, $form['ID'], $fi_args );
								}
								Automator()->maybe_trigger_complete( $r['args'] );
							}
						}
					}
				}
			}
		}

	}

}
