<?php

namespace Uncanny_Automator_Pro;

/**
 * Class Add_Restrict_Content_Integration
 * @package Uncanny_Automator_Pro
 */
class Add_Restrict_Content_Integration {

	/**
	 * Integration code
	 * @var string
	 */
	public static $integration = 'RC';

	/**
	 * Add_Integration constructor.
	 */
	public function __construct() {
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

		if ( function_exists( 'rcp_get_membership_levels' ) ) {
			return true;
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
		$directory[] = dirname( __FILE__ ) . '/closures';

		return $directory;
	}

	/**
	 * Register the integration by pushing it into the global automator object
	 */
	public function add_integration_func() {

		Automator()->register->integration(
			self::$integration,
			array(
				'name'     => 'Restrict Content Pro',
				'icon_svg' => \Uncanny_Automator\Utilities::get_integration_icon( 'restrict-content-icon.svg' ),
			)
		);

	}
}
