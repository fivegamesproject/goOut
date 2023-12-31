<?php

namespace Uncanny_Automator_Pro;

/**
 * Class Add_Nf_Integration
 * @package Uncanny_Automator_Pro
 */
class Add_Nf_Integration {

	/**
	 * Integration code
	 * @var string
	 */
	public static $integration = 'NF';

	/**
	 * Add_Integration constructor.
	 */
	public function __construct() {

		// Add directories to auto loader
		// add_filter( 'automator_pro_integration_directory', [ $this, 'add_integration_directory_func' ], 11 );

		// Add code, name and icon set to automator
		// $this->add_integration_func();

		// Verify is the plugin is active based on integration code
		//      add_filter( 'uncanny_automator_maybe_add_integration', [
		//          $this,
		//          'plugin_active',
		//      ], 30, 2 );
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
			if ( class_exists( 'Ninja_Forms' ) ) {
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
			'NF',
			array(
				'name'        => 'Ninja Forms',
				'icon_16'     => \Uncanny_Automator\Utilities::get_integration_icon( 'integration-ninjaforms-icon-16.png' ),
				'icon_32'     => \Uncanny_Automator\Utilities::get_integration_icon( 'integration-ninjaforms-icon-32.png' ),
				'icon_64'     => \Uncanny_Automator\Utilities::get_integration_icon( 'integration-ninjaforms-icon-64.png' ),
				'logo'        => \Uncanny_Automator\Utilities::get_integration_icon( 'integration-ninjaforms.png' ),
				'logo_retina' => \Uncanny_Automator\Utilities::get_integration_icon( 'integration-ninjaforms@2x.png' ),
			)
		);
	}
}
