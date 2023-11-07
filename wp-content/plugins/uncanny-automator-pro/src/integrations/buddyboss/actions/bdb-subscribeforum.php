<?php

namespace Uncanny_Automator_Pro;

/**
 * @package Uncanny_Automator_Pro
 * class BDB_SUBSCRIBEFORUM
 */
class BDB_SUBSCRIBEFORUM {

	/**
	 * Integration code
	 *
	 * @var string
	 */
	public static $integration = 'BDB';

	private $action_code;
	/**
	 * @var string
	 */
	private $action_meta;

	/**
	 * Set Triggers constructor.
	 */
	public function __construct() {
		$this->action_code = 'BDBSUBSCRIBEFORUM';
		$this->action_meta = 'BDBSUBSCRIBE';
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
			'sentence'           => sprintf( esc_attr__( 'Subscribe the user to {{a forum:%1$s}}', 'uncanny-automator-pro' ), $this->action_meta ),
			/* translators: Action - BuddyBoss */
			'select_option_name' => esc_attr__( 'Subscribe the user to {{a forum}}', 'uncanny-automator-pro' ),
			'priority'           => 10,
			'accepted_args'      => 1,
			'execution_function' => array( $this, 'subscribe_to_a_forum' ),
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
					Automator()->helpers->recipe->buddyboss->options->pro->list_buddyboss_forums( esc_attr__( 'Forum', 'uncanny-automator-pro' ), $this->action_meta, array( 'uo_include_any' => false ), true ),
				),
			)
		);
	}

	/**
	 * Subscribe to BuddyBoss Forum
	 *
	 * @param string $user_id
	 * @param array $action_data
	 * @param string $recipe_id
	 *
	 * @return void
	 */
	public function subscribe_to_a_forum( $user_id, $action_data, $recipe_id, $args ) {
		if ( bbp_is_subscriptions_active() === false ) {
			return;
		}

		$forum_ids = array_map( 'intval', json_decode( $action_data['meta'][ $this->action_meta ] ) );

		if ( ! empty( $forum_ids ) ) {
			foreach ( $forum_ids as $forum_id ) {
				$is_subscription = bbp_is_user_subscribed( $user_id, $forum_id );
				$success         = false;

				if ( true === $is_subscription ) {
					$action_data['complete_with_errors'] = true;
					$action_data['do-nothing']           = true;
					Automator()->complete_action( $user_id, $action_data, $recipe_id, __( 'The user is already subscribed to the specified forum.', 'uncanny-automator-pro' ) );

					return;
				} else {
					$success = bbp_add_user_subscription( $user_id, $forum_id );
					// Do additional subscriptions actions
					do_action( 'bbp_subscriptions_handler', $success, $user_id, $forum_id, 'bbp_subscribe' );
				}

				if ( $success === false && $is_subscription === false ) {
					$action_data['complete_with_errors'] = true;
					$action_data['do-nothing']           = true;
					Automator()->complete_action( $user_id, $action_data, $recipe_id, __( 'There was a problem subscribing to that forum!', 'uncanny-automator-pro' ) );

					return;
				}

				Automator()->complete->action( $user_id, $action_data, $recipe_id );

			}
		}
	}

}
