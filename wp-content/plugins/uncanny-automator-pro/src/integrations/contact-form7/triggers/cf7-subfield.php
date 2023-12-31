<?php

namespace Uncanny_Automator_Pro;

/**
 * Class CF7_SUBFIELD
 * @package Uncanny_Automator_Pro
 */
class CF7_SUBFIELD {

	/**
	 * Integration code
	 * @var string
	 */
	public static $integration = 'CF7';

	private $trigger_code;
	private $trigger_meta;

	/**
	 * Set up Automator trigger constructor.
	 */
	public function __construct() {
		$this->trigger_code = 'CF7SUBFIELD';
		$this->trigger_meta = 'CF7FIELDS';
		$this->define_trigger();
	}

	/**
	 * Define and register the trigger by pushing it into the Automator object
	 */
	public function define_trigger() {

		$trigger = array(
			'author'              => Automator()->get_author_name( $this->trigger_code ),
			'support_link'        => Automator()->get_author_support_link( $this->trigger_code, 'integration/contact-form-7/' ),
			'is_pro'              => true,
			'integration'         => self::$integration,
			'code'                => $this->trigger_code,
			'sentence'            => sprintf(
			/* translators: Logged-in trigger - Contact Form 7 */
				__( 'A user submits {{a form:%1$s}} with {{a specific value:%2$s}} in {{a specific field:%3$s}}', 'uncanny-automator-pro' ),
				$this->trigger_meta,
				'SUBVALUE:' . $this->trigger_meta,
				$this->trigger_code . ':' . $this->trigger_meta
			),
			/* translators: Logged-in trigger - Contact Form 7 */
			'select_option_name'  => __( 'A user submits {{a form}} with {{a specific value}} in {{a specific field}}', 'uncanny-automator-pro' ),
			'action'              => 'wpcf7_submit',
			'priority'            => 99,
			'accepted_args'       => 2,
			'validation_function' => array( $this, 'wpcf7_submit' ),
			'options_group'       => array(
				$this->trigger_meta => array(
					Automator()->helpers->recipe->contact_form7->options->list_contact_form7_forms(
						null,
						$this->trigger_meta,
						array(
							'token'        => false,
							'is_ajax'      => true,
							'target_field' => $this->trigger_code,
							'endpoint'     => 'select_form_fields_CF7FORMS',
						)
					),
					Automator()->helpers->recipe->field->select_field( $this->trigger_code, __( 'Field', 'uncanny-automator-pro' ) ),
					Automator()->helpers->recipe->field->text_field( 'SUBVALUE', __( 'Value', 'uncanny-automator-pro' ) ),
				),
			),
		);

		Automator()->register->trigger( $trigger );
	}

	/**
	 * Validation function when the trigger action is hit
	 *
	 * @param $form
	 * @param $result
	 */
	public function wpcf7_submit( $form, $result ) {

		if ( 'validation_failed' === (string) $result['status'] ) {
			return;
		}

		if ( ! is_user_logged_in() ) {
			return;
		}

		if ( empty( $form ) ) {
			return;
		}

		$user_id = wp_get_current_user()->ID;

		if ( empty( $form ) ) {
			return;
		}

		$recipes    = Automator()->get->recipes_from_trigger_code( $this->trigger_code );
		$conditions = $this->match_condition( $form, $recipes, $this->trigger_meta, $this->trigger_code, 'SUBVALUE' );

		if ( empty( $conditions ) ) {
			return;
		}

		foreach ( $conditions['recipe_ids'] as $trigger_id => $recipe_id ) {
			if ( ! Automator()->is_recipe_completed( $recipe_id, $user_id ) ) {
				$trigger_args = array(
					'code'             => $this->trigger_code,
					'meta'             => $this->trigger_meta,
					'recipe_to_match'  => $recipe_id,
					'trigger_to_match' => $trigger_id,
					'ignore_post_id'   => true,
					'user_id'          => $user_id,
				);

				$args = Automator()->maybe_add_trigger_entry( $trigger_args, false );

				$recipe_to_match = Automator()->get_recipes_data( true, $recipe_id );
				do_action( 'automator_save_cf7_form', $form, $recipe_to_match, $args );

				if ( $args ) {
					foreach ( $args as $result ) {
						if ( true === $result['result'] ) {
							Automator()->maybe_trigger_complete( $result['args'] );
							break;
						}
					}
				}
			}
		}

	}

	/**
	 * @param      $form
	 * @param null $recipes
	 * @param null $trigger_meta
	 * @param null $trigger_code
	 * @param null $trigger_second_code
	 *
	 * @return array|bool
	 */
	public function match_condition( $form, $recipes = null, $trigger_meta = null, $trigger_code = null, $trigger_second_code = null ) {

		if ( null === $recipes ) {
			return false;
		}

		$matches        = array();
		$recipe_ids     = array();
		$entry_to_match = $form->id();

		foreach ( $recipes as $recipe ) {
			foreach ( $recipe['triggers'] as $trigger ) {
				if ( key_exists( $trigger_meta, $trigger['meta'] ) && (string) $trigger['meta'][ $trigger_meta ] === (string) $entry_to_match ) {
					$matches[ $trigger['ID'] ]    = array(
						'field' => $trigger['meta'][ $trigger_code ],
						'value' => $trigger['meta'][ $trigger_second_code ],
					);
					$recipe_ids[ $trigger['ID'] ] = $recipe['ID'];
				}
			}
		}

		if ( ! empty( $matches ) ) {
			foreach ( $matches as $trigger_id => $match ) {
				$post_input = isset( $_POST[ $match['field'] ] ) ? $_POST[ $match['field'] ] : '';
				// Check if input is an array or string
				if ( is_array( $post_input ) ) {
					$trigger_match = explode( ',', $match['value'] );
					// if input count is less then match then it does not match
					if ( count( $trigger_match ) > count( $post_input ) ) {
						unset( $recipe_ids[ $trigger_id ] );
					} elseif ( ! empty( array_diff( $trigger_match, $post_input ) ) ) {
						unset( $recipe_ids[ $trigger_id ] );
					}
				} else {
					if ( $post_input !== $match['value'] ) {
						unset( $recipe_ids[ $trigger_id ] );
					}
				}
			}
		}

		if ( ! empty( $recipe_ids ) ) {
			return array(
				'recipe_ids' => $recipe_ids,
				'result'     => true,
			);
		}

		return false;
	}
}
