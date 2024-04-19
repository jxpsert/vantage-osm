<?php
/**
 * OpenStreetMap class.
 *
 * @package Geo
 * 
 * version 1.0.1
 */

/**
 * Map provider APP_OpenStreetMap_Provider class
 *
 * @since   1.0.0
 */
class APP_OpenStreetMap_Provider extends APP_Map_Provider
{

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct(
			'osm',
			array(
				'dropdown' => __('OpenStreetMap', APP_TD),
				'admin' => __('OpenStreetMap', APP_TD),
			)
		);
	}

	/**
	 * Init
	 */
	public function init()
	{
	}

	/**
	 * Check to see if there are required variables.
	 *
	 * @since  1.0.0
	 *
	 * @return string
	 */
	public function has_required_vars()
	{
		return true;
	}

	/**
	 * Load up the map scripts.
	 *
	 * @since  1.0.0
	 *
	 * @return void
	 */
	public function _enqueue_scripts()
	{

		$url = '//unpkg.com/leaflet@1.9.4/dist/leaflet.js';
		$css = '//unpkg.com/leaflet@1.9.4/dist/leaflet.css';

		$defaults = array(
			'leaflet_attribution' => true,
		);

		$options = wp_parse_args($this->options, $defaults);

		wp_enqueue_script('leaflet', $url, array(), null, false);
		wp_enqueue_style('leaflet', $css, array(), null);
		wp_enqueue_script('wux-openstreetmap', get_template_directory_uri() . '/includes/geo/map-providers/openstreetmap.js', array('leaflet', 'appthemes-maps'), '20180916', true);
	}



	/**
	 * Map provider variables.
	 *
	 * @since  1.0.0
	 *
	 * @return void
	 */
	public function _map_provider_vars()
	{
		$this->map_provider_vars = wp_parse_args(
			$this->options,
			array(
				'text_directions_error' => __('Could not get directions to the given address. Please make your search more specific.', APP_TD),
				'leaflet_attribution' => true,
				'tile_layer_url' => 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
				'attribution' => '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>',
				'max_zoom' => 19,
			)
		);
	}

	/**
	 * Generates the admin option fields.
	 *
	 * @since  1.0.0
	 *
	 * @return array
	 */
	public function form()
	{

		$general = array(
			array(
				'title' => __('General', APP_TD),
				'fields' => array(
					array(
						'title' => __('Leaflet attribution', APP_TD),
						'desc' => __('Whether or not to show the \'Leaflet\' text on the map', APP_TD),
						'type' => 'checkbox',
						'name' => 'leaflet_attribution',
						// 'tip' => __('Show the \'Leaflet\' text on the map', APP_TD),
					),
					array(
						'title' => __('Tile layer URL', APP_TD),
						'desc' => __('The tiles to use for the map.', APP_TD),
						'type' => 'text',
						'name' => 'tile_layer_url',
						'tip' => 'OpenStreetMap-Carto will be used if not specified: https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png'
					),
					array(
						'title' => __('Attribution text', APP_TD),
						'desc' => __('Attribution for the map data. Mandatory in most cases. HTML allowed.', APP_TD),
						'type' => 'text',
						'name' => 'attribution',
						'tip' => 'Standard OpenStreetMap attribution: <code>&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a></code>'
					),
					array(
						'title' => __('Max zoom level', APP_TD),
						'desc' => __('The maximum zoom level the provider accepts', APP_TD),
						'type' => 'number',
						'name' => 'max_zoom',
						'tip' => 'Standard OpenStreetMap zoom level: 19'
					)
				),
			)
		);

		return $general;
	}
}

appthemes_register_map_provider('APP_OpenStreetMap_Provider');
