<?php

namespace Uncanny_Automator_Pro;

/**
 * Class BDB_UNFOLLOWUSER
 *
 * @package Uncanny_Automator_Pro
 */
class BDB_UNFOLLOWUSER {

	/**
	 * Integration code
	 *
	 * @var string
	 */
	public static $integration = 'BDB';

	private $action_code;
	private $action_meta;

	/**
	 * Set Action constructor.
	 */
	public function __construct() {
		$this->action_code = 'BDBUNFOLLOWUSER';
		$this->action_meta = 'BDBALLUSERS';
		$this->define_action();
	}

	/**
	 * Define and register the action by pushing it into the Automator object.
	 */
	public function define_action() {

		$action = array(
			'author'             => Automator()->get_author_name(),
			'support_link'       => Automator()->get_author_support_link( $this->action_code, 'integration/buddyboss/' ),
			'is_pro'             => true,
			'integration'        => self::$integration,
			'code'               => $this->action_code,
			/* translators: Action - BuddyBoss */
			'sentence'           => sprintf( esc_attr__( 'Stop following {{a user:%1$s}}', 'uncanny-automator-pro' ), $this->action_meta ),
			/* translators: Action - BuddyBoss */
			'select_option_name' => esc_attr__( 'Stop following {{a user}}', 'uncanny-automator-pro' ),
			'priority'           => 10,
			'accepted_args'      => 1,
			'execution_function' => array( $this, 'bdb_stop_following' ),
			'options_callback'   => array( $this, 'load_options' ),
		);

		Automator()->register->action( $action );
	}

	/**
	 * @return array[]
	 */
	public function load_options() {
		return Automator()->utilities->keep_order_of_options(
			array(
				'options' => array(
					Automator()->helpers->recipe->buddyboss->options->all_buddyboss_users( null, $this->action_meta ),
				),
			)
		);
	}

	/**
	 * @param $user_id
	 * @param $action_data
	 * @param $recipe_id
	 * @param $args
	 */
	public function bdb_stop_following( $user_id, $action_data, $recipe_id, $args ) {

		$follower_ids = array_map( 'intval', json_decode( $action_data['meta'][ $this->action_meta ] ) );

		$message = '';

		if ( function_exists( 'bp_stop_following' ) || ( bp_is_active( 'follow' ) && function_exists( 'bp_follow_stop_following' ) ) ) {
			foreach ( $follower_ids as $k => $follower_id ) {
				if ( $follower_id == $user_id ) {
					continue;
				}
				$args = array(
					'follower_id' => $user_id,
					'leader_id'   => $follower_id,
				);
				if ( bp_is_active( 'follow' ) && function_exists( 'bp_follow_stop_following' ) ) {
					$following = bp_follow_stop_following( $args );
				} elseif ( function_exists( 'bp_stop_following' ) ) {
					$following = bp_stop_following( $args );
				}
				if ( $following == false ) {
					$action_data['complete_with_errors'] = true;
					$message                             .= 'The user was not following a member ID - ' . $follower_id . '. ';
				}
			}

			Automator()->complete_action( $user_id, $action_data, $recipe_id, $message );
		}
	}

}
