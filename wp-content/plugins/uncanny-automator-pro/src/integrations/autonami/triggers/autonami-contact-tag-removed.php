<?php

namespace Uncanny_Automator_Pro;

/**
 * Class AUTONAMI_CONTACT_TAG_REMOVED
 *
 * @package Uncanny_Automator
 */
class AUTONAMI_CONTACT_TAG_REMOVED {

	use \Uncanny_Automator\Recipe\Triggers;

	/**
	 * @var Autonami_Pro_Helpers
	 */
	public $helpers;

	/**
	 * Set up Automator trigger constructor.
	 */
	public function __construct() {

		$this->helpers = new Autonami_Pro_Helpers();
		$this->setup_trigger();
		$this->register_trigger();

	}

	/**
	 * Define and register the trigger by pushing it into the Automator object
	 */
	public function setup_trigger() {

		$this->set_integration( 'AUTONAMI' );
		$this->set_trigger_code( 'CONTACT_TAG_REMOVED' );
		$this->set_trigger_meta( 'TAG' );
		$this->set_is_login_required( false );
		$this->set_trigger_type( 'anonymous' );
		$this->set_is_pro( true );
		$this->set_support_link( $this->helpers->support_link( $this->trigger_code ) );

		/* Translators: List name */
		$this->set_sentence( sprintf( '{{A tag:%1$s}} is removed from a contact', $this->get_trigger_meta() ) );

		$this->set_readable_sentence( '{{A tag}} is removed from a contact' );

		$this->add_action( 'automator_bwfan_tag_removed_from_contact' );
		$this->set_action_args_count( 2 );

		$this->set_options_callback( array( $this, 'load_options' ) );

	}

	/**
	 * Method load_options
	 *
	 * @return void
	 */
	public function load_options() {

		$options[] = $this->helpers->get_tag_dropdown();

		return array( 'options' => $options );

	}

	/**
	 * Method do_continue_anon_trigger
	 *
	 * @param mixed $args
	 *
	 * @return void
	 */
	public function do_continue_anon_trigger( ...$args ) {

		return true;

	}

	/**
	 *  Validation function when the trigger action is hit
	 *
	 * @param $data
	 */
	public function validate_trigger( ...$args ) {
		return true;
	}

	/**
	 * Method prepare_to_run
	 *
	 * @param $data
	 */
	public function prepare_to_run( $data ) {
		$this->set_conditional_trigger( true );
	}

	/**
	 * Check list ID against the trigger meta
	 *
	 * @param $args
	 */
	public function trigger_conditions( $args ) {

		$tag_id = $this->helpers->extract_tag_id_from_args( $args );

		$this->do_find_any( true ); // Support "Any tag" option

		// Find the tag in trigger meta
		$this->do_find_this( $this->get_trigger_meta() );
		$this->do_find_in( $tag_id );

	}

}
