<?php

namespace Uncanny_Automator_Pro;

/**
 * Class BDB_USERPROFILETYPECHANGED
 *
 * @package Uncanny_Automator_Pro
 */
class BDB_USERPROFILETYPECHANGED {

	/**
	 * Integration code
	 *
	 * @var string
	 */
	public static $integration = 'BDB';

	private $trigger_code;
	private $trigger_meta;

	/**
	 * SetAutomatorTriggers constructor.
	 */
	public function __construct() {
		$this->trigger_code = 'BDBUSERPROFILETYPECHANGED';
		$this->trigger_meta = 'BDBPROFILETYPE';
		$this->define_trigger();

	}

	/**
	 *
	 */
	public function define_trigger() {

		$trigger = array(
			'author'              => Automator()->get_author_name(),
			'support_link'        => Automator()->get_author_support_link( $this->trigger_code, 'integration/buddyboss/' ),
			'is_pro'              => true,
			'integration'         => self::$integration,
			'code'                => $this->trigger_code,
			/* translators: Logged-in trigger - BuddyBoss */
			'sentence'            => sprintf( __( "A user's profile type is set to {{a specific type:%1\$s}}", 'uncanny-automator-pro' ), $this->trigger_meta ),
			/* translators: Logged-in trigger - BuddyBoss */
			'select_option_name'  => __( "A user's profile type is set to {{a specific type}}", 'uncanny-automator-pro' ),
			'action'              => 'bp_set_member_type',
			'priority'            => 10,
			'accepted_args'       => 3,
			'validation_function' => array(
				$this,
				'bp_set_member_type_updated',
			),
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
				'options' => array(
					Automator()->helpers->recipe->buddyboss->pro->get_profile_types(
						__( 'Profile type', 'uncanny-automator' ),
						$this->trigger_meta
					),
				),
			)
		);
	}

	/**
	 * @param $user_id
	 * @param $member_type
	 * @param $append
	 */
	public function bp_set_member_type_updated( $user_id, $member_type, $append ) {

		if ( empty( $member_type ) ) {
			return;
		}

		// Get post id of selected profile type.
		$post_id = bp_member_type_post_by_type( $member_type );

		if ( empty( $post_id ) ) {
			return;
		}

		$args = array(
			'code'    => $this->trigger_code,
			'meta'    => $this->trigger_meta,
			'user_id' => $user_id,
			'post_id' => $post_id,
		);
		Automator()->maybe_add_trigger_entry( $args );

	}

}
