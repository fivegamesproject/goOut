<?php

namespace Uncanny_Automator_Pro;

/**
 * Class ANON_NF_SUBFIELD
 *
 * @package Uncanny_Automator_Pro
 */
class ANON_NF_SUBFIELD {

	/**
	 * Integration code
	 *
	 * @var string
	 */
	public static $integration = 'NF';

	private $trigger_code;
	private $trigger_meta;

	/**
	 * Set up Automator trigger constructor.
	 */
	public function __construct() {
		$this->trigger_code = 'ANONNFSUBFIELD';
		$this->trigger_meta = 'ANONNFFORMS';
		$this->define_trigger();
	}

	/**
	 * Define and register the trigger by pushing it into the Automator object
	 */
	public function define_trigger() {

		$trigger = array(
			'author'              => Automator()->get_author_name( $this->trigger_code ),
			'support_link'        => Automator()->get_author_support_link( $this->trigger_code, 'integration/ninja-forms/' ),
			'is_pro'              => true,
			'integration'         => self::$integration,
			'code'                => $this->trigger_code,
			'sentence'            => sprintf(
			/* translators: Anonymous triggers - Ninja Forms */
				__( '{{A form:%1$s}} is submitted with {{a specific value:%2$s}} in {{a specific field:%3$s}}', 'uncanny-automator-pro' ),
				$this->trigger_meta,
				'SUBVALUE' . ':' . $this->trigger_meta,
				$this->trigger_code . ':' . $this->trigger_meta
			),
			/* translators: Anonymous triggers - Ninja Forms */
			'select_option_name'  => __( '{{A form}} is submitted with {{a specific value}} in {{a specific field}}', 'uncanny-automator-pro' ),
			'action'              => 'ninja_forms_after_submission',
			'type'                => 'anonymous',
			'priority'            => 20,
			'accepted_args'       => 1,
			'validation_function' => array( $this, 'nform_submit' ),
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
						Automator()->helpers->recipe->ninja_forms->options->list_ninja_forms(
							null,
							$this->trigger_meta,
							array(
								'token'        => false,
								'is_ajax'      => true,
								'target_field' => $this->trigger_code,
								'endpoint'     => 'select_form_fields_ANONNFFORMS',
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
	 * @param array $entry form submission.
	 */
	public function nform_submit( $entry ) {

		$recipes = Automator()->get->recipes_from_trigger_code( $this->trigger_code );

		if ( empty( $entry ) ) {
			return;
		}

		$conditions = Automator()->helpers->recipe->ninja_forms->pro->match_condition( $entry, $recipes, $this->trigger_meta, $this->trigger_code, 'SUBVALUE' );

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
									$ninja_args = array(
										'trigger_id'     => (int) $r['args']['trigger_id'],
										'meta_key'       => $this->trigger_meta,
										'user_id'        => $user_id,
										'trigger_log_id' => $r['args']['trigger_log_id'],
										'run_number'     => $r['args']['run_number'],
									);

									Automator()->helpers->recipe->ninja_forms->pro->extract_save_ninja_fields( $entry, $ninja_args );
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
