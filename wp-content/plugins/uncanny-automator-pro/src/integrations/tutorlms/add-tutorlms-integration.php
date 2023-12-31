<?php
/**
 * Contains Integration Class
 *
 * @since   2.3.0
 * @version 2.3.0
 * @package Uncanny_Automator_Pro
 */

namespace Uncanny_Automator_Pro;

/**
 * Adds Integration
 *
 * @since 2.3.0
 */
class Add_Tutorlms_Integration {

	/**
	 * Integration code
	 *
	 * @since 2.3.0
	 * @var string
	 */
	public static $integration = 'TUTORLMS';

	/**
	 * Add_Integration constructor.
	 *
	 * @since 2.3.0
	 */
	public function __construct() {

		// Add directories to auto loader
		// add_filter( 'automator_pro_integration_directory', [ $this, 'add_integration_directory_func' ], 11 );

		// Add code, name and icon set to automator
		// $this->add_integration_func();

		// Verify is the plugin is active based on integration code
		// add_filter( 'uncanny_automator_maybe_add_integration', [ $this, 'plugin_active' ], 30, 2 );
	}

	/**
	 * Conditionally Loads Integration.
	 *
	 * @param bool   $status Is Integration already active?
	 * @param string $plugin The integration identifier.
	 *
	 * @return bool
	 * @since 2.3.0
	 *
	 */
	public function plugin_active( $status, $plugin ) {

		// not our code, bail early.
		if ( self::$integration !== $plugin ) {
			return $status;
		}

		// otherwise, return if Tutor LMS is active.
		return class_exists( '\TUTOR\Tutor' );
	}

	/**
	 * Set the directories that the auto loader will run in
	 *
	 * @param $directory
	 *
	 * @return array
	 * @since 2.3.0
	 *
	 */
	public function add_integration_directory_func( $directory ) {

		$directory[] = dirname( __FILE__ ) . '/helpers';
		$directory[] = dirname( __FILE__ ) . '/actions';
		$directory[] = dirname( __FILE__ ) . '/triggers';
		$directory[] = dirname( __FILE__ ) . '/tokens';

		return $directory;
	}

	/**
	 * Registers Integration.
	 *
	 * @since 2.3.0
	 */
	public function add_integration_func() {

		// set up configuration.
		$integration_config = array(
			'name'     => __( 'Tutor LMS', 'uncanny-automator' ),
			'icon_svg' => \Uncanny_Automator\Utilities::get_integration_icon( 'tutorlms-icon.svg' ),
		);

		// global automator object.

		// register integration into automator.
		Automator()->register->integration( self::$integration, $integration_config );

	}
}
