<?php

namespace Uncanny_Automator_Pro;

/**
 * Class Add_Wpff_Integration
 * @package Uncanny_Automator_Pro
 */
class Add_Wpff_Integration {

	/**
	 * Integration code
	 * @var string
	 */
	public static $integration = 'WPFF';

	/**
	 * Add_Integration constructor.
	 */
	public function __construct() {

		// Add directories to auto loader
		// add_filter( 'automator_pro_integration_directory', [ $this, 'add_integration_directory_func' ], 11 );

		// Add code, name and icon set to automator
		// $this->add_integration_func();

		// Verify is the plugin is active based on integration code
		// add_filter( 'uncanny_automator_maybe_add_integration', [ $this, 'plugin_active', ], 30, 2 );
	}

	/**
	 * Only load this integration and its triggers and actions if the related plugin is active
	 *
	 * @param $status
	 * @param $plugin
	 *
	 * @return bool
	 */
	public function plugin_active( $status, $plugin ) {

		if ( self::$integration === $plugin ) {
			if ( defined( 'FLUENTFORM' ) ) {
				$status = true;
			} else {
				$status = false;
			}
		}

		return $status;
	}

	/**
	 * Set the directories that the auto loader will run in
	 *
	 * @param $directory
	 *
	 * @return array
	 */
	public function add_integration_directory_func( $directory ) {

		$directory[] = dirname( __FILE__ ) . '/helpers';
		$directory[] = dirname( __FILE__ ) . '/actions';
		$directory[] = dirname( __FILE__ ) . '/triggers';
		$directory[] = dirname( __FILE__ ) . '/tokens';

		return $directory;
	}

	/**
	 * Register the integration by pushing it into the global automator object
	 */
	public function add_integration_func() {

		Automator()->register->integration(
			'WPFF',
			array(
				'name'     => 'Fluent Forms',
				'icon_svg' => Utilities::get_integration_icon( 'wp-fluent-forms-icon.svg' ),
			)
		);
	}
}
