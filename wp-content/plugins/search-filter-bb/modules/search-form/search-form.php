<?php

//setup module
class SearchFilterFormBBModule extends FLBuilderModule {
	
	private $search_form_options = array();
	
	public function __construct()
	{
		parent::__construct(array(
			'name'            => __( 'Search & Filter Form', 'search-filter-bb' ),
			'description'     => __( 'Add a Search & Filter Form', 'search-filter-bb' ),
			'category'        => __( 'Search & Filter', 'search-filter-bb' ),
			'dir'             => plugin_dir_path(__FILE__),
			'url'             => plugin_dir_url(__FILE__),
			/*'editor_export'   => true, // Defaults to true and can be omitted.
			'enabled'         => true, // Defaults to true and can be omitted.
			'partial_refresh' => false, // Defaults to false and can be omitted.*/
		));
	}
}

//register module with the search form dropdown
$search_form_options = $this->get_search_form_options();
$search_form_options[0] = 'Choose a Search Form:';

FLBuilder::register_module( 'SearchFilterFormBBModule', array(
	'my-tab-1'      => array(
		'title'         => __( 'General', 'search-filter-bb' ),
		'sections'      => array(
			'general'  => array(
				'title'            => __( 'Choose a Search Form', 'search-filter-bb' ),
				'fields'        => array( // Section Fields
					'search_form_id'   => array(
						'type'          => 'select',
						//'label'         => __('Select Field', 'fl-builder'),
						'default'       => '0',
						'options'       => $search_form_options
					),
				),
			)
		)
	),
) );
