<?php

namespace Uncanny_Automator_Pro;

/**
 * Class USER_NOT_IN_GROUP_OR_CHILDREN
 *
 * @package Uncanny_Automator_Pro
 */
class LD_USER_NOT_IN_GROUP_OR_CHILDREN extends Action_Condition {

	/**
	 * Define_condition
	 *
	 * @return void
	 */
	public function define_condition() {

		$this->integration = 'LD';
		$this->name        = __( 'The user is not a member of {{a group}} or its child groups', 'uncanny-automator-pro' );
		$this->code        = 'USER_NOT_IN_GROUP_OR_CHILDREN';
		// translators: A token matches a value
		$this->dynamic_name  = sprintf( esc_html__( 'The user is not a member of {{a group:%1$s}} or its child groups', 'uncanny-automator-pro' ), 'GROUP' );
		$this->is_pro        = true;
		$this->requires_user = true;
	}

	/**
	 * Fields
	 *
	 * @return array
	 */
	public function fields() {

		$groups_field_args = array(
			'option_code'           => 'GROUP',
			'label'                 => esc_html__( 'Group', 'uncanny-automator-pro' ),
			'required'              => true,
			'options'               => $this->ld_groups_options(),
			'supports_custom_value' => true,
		);

		return array(
			// Group field
			$this->field->select_field_args( $groups_field_args ),
		);
	}

	/**
	 * Load options
	 *
	 * @return array[]
	 */
	public function ld_groups_options() {
		$args      = array(
			'post_type'      => 'groups',
			'posts_per_page' => 9999, //phpcs:ignore WordPress.WP.PostsPerPage.posts_per_page_posts_per_page
			'orderby'        => 'title',
			'order'          => 'ASC',
			'post_status'    => 'publish',
		);
		$ld_groups = array();
		$groups    = Automator()->helpers->recipe->options->wp_query( $args, false, false );
		if ( empty( $groups ) ) {
			return array();
		}
		foreach ( $groups as $group_id => $group_title ) {
			$ld_groups[] = array(
				'value' => $group_id,
				'text'  => $group_title,
			);
		}

		return $ld_groups;
	}

	/**
	 * Evaluate_condition
	 *
	 * @return void
	 * @throws \Exception
	 */
	public function evaluate_condition() {

		$user_groups = learndash_get_users_group_ids( $this->user_id, true );
		if ( ! empty( $user_groups ) ) {
			$parsed_group   = $this->get_parsed_option( 'GROUP' );
			$group_children = learndash_get_group_children( $parsed_group );
			$all_groups     = array_merge( array( $parsed_group ), $group_children );
			$user_in_group  = array_intersect( $user_groups, $all_groups );

			// Check if the user is enrolled in the group here
			if ( ! empty( $user_in_group ) ) {
				// translators: group name
				$message = sprintf( __( 'User is a member of %s or its children.', 'uncanny-automator-pro' ), $this->get_option( 'GROUP_readable' ) );
				$this->condition_failed( $message );
			}
		}
	}
}
