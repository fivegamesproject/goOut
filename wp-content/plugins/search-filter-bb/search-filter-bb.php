<?php
/**
 * Plugin Name:       Search & Filter  - Beaver Builder Extension
 * Plugin URI:        https://searchandfilter.com
 * Description:       Adds Search & Filter integration for Beaver Builder - supports the Posts Module, Product Archives + Post Type Archives
 * Version:           1.0.0
 * Author:            Code Amp
 * Author URI:        https://www.codeamp.com
 * Text Domain:       search-filter-bb
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.html
 * Domain Path:       /languages
 */

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

add_action( 'plugins_loaded', array( 'Search_Filter_BB_Extension', 'get_instance' ) );

Class Search_Filter_BB_Extension
{
	/**
	 * Plugin Version
	 *
	 * @since 1.0.0
	 *
	 * @var string The plugin version.
	 */
	const VERSION = '1.0.1';
	const PLUGIN_UPDATE_URL = 'https://searchandfilter.com';
	const PLUGIN_UPDATE_ID = 197286;
	private static $plugin_path = '';
	private static $plugin_url = '';
	
	/**
	 * Instance
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 * @static
	 *
	 * @var Search_Filter_Elementor_Extension The single instance of the class.
	 */
	 
	protected static $instance = null;
	
	private $search_form_options = array();
	
	/**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @static
	 *
	 * @return Search_Filter_BB_Extension An instance of the class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}
	
	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 */
	private function __construct()
	{
		// Check if BB and S&F is installed and active
        if ( ( ! defined( 'FL_BUILDER_VERSION' ) ) || ( ! defined( 'SEARCH_FILTER_VERSION' ) ) ) {
			
            // Display notice that BB is required
            // add_action('admin_notices', array( $this, 'show_bb_version_notice' ));
			/*if ( ! class_exists( 'FLBuilder' ) ) {
				return;	
			}*/
			
            return;
        }
		else {
			self::$plugin_path = plugin_dir_path( __FILE__ );
			self::$plugin_url = plugin_dir_url( __FILE__ );
			
			
			add_filter( 'fl_builder_render_settings_field', array( $this, 'render_settings_field' ), 1000, 3 );
			add_action( 'fl_builder_loop_settings_after_form', array( $this, 'add_sf_field' ), 1000 );
			
			add_filter( 'fl_builder_module_attributes', array( $this, 'module_attributes' ) , 1000, 2 );
			add_filter( 'fl_builder_module_custom_class', array( $this, 'module_custom_class' ), 1000, 2 );
			add_filter( 'fl_builder_loop_query_args', array( $this, 'sf_fl_builder_loop_query_args' ), 1000 );
			
			add_filter( 'search_filter_admin_option_display_results', array( $this, 'search_filter_admin_option_display_results' ), 10, 2 );
			add_filter( 'search_filter_form_attributes', array( $this, 'search_filter_form_attributes' ), 10, 2 );
			
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 10);
			
			add_action( 'admin_init', array( $this, 'update_plugin_handler' ), 0 );
			add_action( 'init', array( $this, 'register_modules' ) );
		}
	}
	
	public function module_custom_class($classes, $module){
		
		if ( ! isset ( $module->settings ) ){
			return $classes;
		}
		
		if ( ! isset( $module->settings->data_source ) ){
			return $classes;
		}
		
		if ( 'search_filter' !== $module->settings->data_source){
			return $classes;
		}
		
		if ( ! isset ( $module->settings->search_filter_form ) ){
			return $classes;
		}
		
		$classes .= ' search-filter-results-'.intval($module->settings->search_filter_form);
		
		return $classes;
		
	}
	
	public function module_attributes($attributes, $module){
		
		if ( ! isset( $module->settings ) ) {
			return $attributes;
		}
		
		if ( ! isset( $module->settings->data_source ) ) {
			return $attributes;
		}
		
		if ( $module->settings->data_source !== "search_filter" ) {
			return $attributes;
		}
		
		$settings = $module->settings;

		if ( ( $module->settings->layout === "grid" ) || ( $module->settings->layout === "columns" ) || ( $module->settings->layout === "gallery" ) || ( $module->settings->layout === "feed" ) ) {
			$data = array(
				'id' 			=> $module->node,
				'layout' 		=> $settings->layout,
				'pagination' 	=> $settings->pagination,
				'postSpacing'	=> $settings->post_spacing,
				'postWidth'		=> $settings->post_width,
				'matchHeight'	=> array(
					'default'	 => $settings->match_height,
					'medium' 	 => $settings->match_height_medium,
					'responsive' => $settings->match_height_responsive,
				),
				'isRTL' 		=> is_rtl() ? 'true' : 'false'
			);
			
			$attributes['data-sf-bb'] = esc_attr( wp_json_encode( $data ) );
		}
		
		return $attributes;
	}
	
	public function render_settings_field($field, $name, $settings){
		
		if($name=="data_source"){
			if(is_array($field['options'])){
				$field['options']['search_filter'] = "Search & Filter";
			}
		}
		
		return $field;
	}
	public function add_sf_field($settings){
		?>
<div class="fl-custom-query fl-loop-data-source" data-source="search_filter">
	<div id="fl-builder-settings-section-general" class="fl-builder-settings-section">
		<h3 class="fl-builder-settings-title">
			<span class="fl-builder-settings-title-text-wrap"><?php _e( 'Search & Filter', 'search-filter-bb' ); ?></span>
		</h3>
		<table class="fl-form-table">
		<?php

		//get search forms
		$search_form_options = $this->get_search_form_options();
		
		FLBuilder::render_settings_field('search_filter_form', array(
			'type'          => 'select',
			'label'         => __( 'Choose Your Search & Filter Form', 'search-filter-bb' ),
			'options'       => $search_form_options,		
		), $settings);
		
		?>
		</table>
	</div>
	
</div>
		<?php
	}
	
	public function enqueue_scripts(){	
	
		wp_register_script( 'search-filter-pro-bb', plugins_url( 'js/bb-frontend-search-filter.js', __FILE__ ), array( 'jquery' ), "1.0.1" );
		wp_localize_script( 'search-filter-pro-bb', 'SFVC_DATA', array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'home_url' => (home_url('/')) ));
		wp_enqueue_script( 'search-filter-pro-bb' );
	}
	
	public function sf_fl_builder_loop_query_args($args){
		
		if(!isset($args['settings'])){
			return $args;
		}
		
		if(!isset($args['settings']->data_source)){
			return $args;
		}
		
		if($args['settings']->data_source!=="search_filter"){
			return $args;
		}
		
		if(!isset($args['settings']->search_filter_form)){
			return $args;
		}
		
		$sfid = intval($args['settings']->search_filter_form);
		$args['search_filter_id'] = $sfid;
		
		if(isset($_GET['sf_paged'])){
			
			global $wp_the_query;
			
			//this get pagination working within the query (not sure what offset is for exactly, but removing it get paged working)
			$args['paged'] = intval($_GET['sf_paged']);
			unset($args['offset']);
			
			//then the pagination needs fixing, which is taken from global $wp_the_query
			global $wp_the_query;
			$wp_the_query->set( 'paged' , intval($_GET['sf_paged']) );	
		}
		
		return $args;
	}
	public function search_filter_form_attributes($attributes, $sfid){
		
		if(isset($attributes['data-display-result-method']))
		{
			if($attributes['data-display-result-method']=="bb_posts_module")
			{
				$attributes['data-ajax-target'] = '.search-filter-results-'.$sfid;
				$attributes['data-ajax-links-selector'] = '.page-numbers a';
				//$attributes['data-ajax-data-type'] = 'json';
			}
		}
		
		return $attributes;
	}
	public function search_filter_admin_option_display_results($display_results_options){
		
		$display_results_options['bb_posts_module'] = array(
            'label'         => __('Beaver Builder Posts Module'),
            'description'   => 
				'<p>'.__('Search Results will displayed using one of Beaver Builders Posts Modules.', 'search-filter-bb').'</p>'.
				'<p>'.__('Remember to set the <strong>Content Source</strong> in your Posts Module to use this Search Form.', 'search-filter-bb').'</p>',
            'base'          => 'shortcode'
        );
		
		return $display_results_options;
	}
	
	private function get_search_form_options()
	{
		if(empty($this->search_form_options)){

			$posts_query = 'post_type=search-filter-widget&post_status=publish&posts_per_page=-1';

			$custom_posts = new WP_Query( $posts_query );
			if( $custom_posts->post_count > 0 ){
				foreach ($custom_posts->posts as $post){
					$this->search_form_options[$post->ID] = html_entity_decode(esc_html($post->post_title) );
				}
			}
		}
		return $this->search_form_options;
	}
	
	/**
	 * Handle plugin updates
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function update_plugin_handler() {

		
		// retrieve our license key from the DB
		// $license_key = trim( get_option( 'edd_sample_license_key' ) );
		$license_key = 'search-filter-extension-free';

		// setup the updater
		$edd_updater = new Search_Filter_BB_Plugin_Updater( self::PLUGIN_UPDATE_URL, __FILE__,
			array(
				'version' => self::VERSION,
				'license' => 'search-filter-extension-free',
				'item_id' => self::PLUGIN_UPDATE_ID,       // ID of the product
				'author'  => 'Search & Filter', // author of this plugin
				'beta'    => false,
			)
		);
		
	}
	
	public function register_modules() {
		require_once self::$plugin_path . 'modules/search-form/search-form.php';
	}
}

if ( ! class_exists( 'Search_Filter_BB_Plugin_Updater' ) ) {
	// load our custom updater
	include( dirname( __FILE__ ) . '/includes/search-filter-bb-plugin-updater.php' );
}
