<?php
/**
 * OpenStreetMap class.
 
 *
 * @package Vantage
 */

class APP_OpenStreetMap
{

	/**
	 * The page we're on for options.
	 *
	 * @since 4.0.0
	 */
	private static $page;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		add_action('va_listings_map_canvas', array($this, 'map_canvas'));
		add_action('tabs_vantage_page_app-geo-settings', array($this, 'admin_fields'));
		add_filter('appthemes_map_icon', array($this, 'map_icon'));
	}

	/**
	 * Default map icon parameters.
	 *
	 * @param type $params
	 * @return type
	 */
	public function map_icon($params)
	{
		global $va_options;

		$params['app_icon_color'] = $va_options->marker_color;
		$params['app_icon_template'] = join(
			array(
				'<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" ',
				'width="{{ width }}" height="{{ height }}" x="0px" y="0px" viewBox="1638.4 310.6 52.3 84.7" enable-background="new 1638.4 310.6 52.3 84.7">',
				'<g>',
				'<path id="svg_2" fill="{{ color }}" d="M1664.6,395.2c-1.9-9.5-5.4-17.4-9.5-24.8c-3.1-5.4-6.6-10.5-9.9-15.7c-1.1-1.8-2-3.6-3.1-5.5',
				'c-2.1-3.7-3.8-7.9-3.7-13.4c0.1-5.4,1.7-9.7,3.9-13.2c3.7-5.8,9.9-10.5,18.1-11.8c6.8-1,13.1,0.7,17.6,3.3c3.7,2.2,6.5,5,8.7,8.4',
				'c2.3,3.5,3.8,7.7,3.9,13.2c0.1,2.8-0.4,5.4-1,7.5c-0.7,2.2-1.7,4-2.6,5.9c-1.8,3.8-4.1,7.2-6.4,10.7',
				'C1673.8,370.3,1667.4,380.8,1664.6,395.2z"/>',
				'<path id="svg_3" fill="#FFFFFF" d="m 1664.7893,317.45394 16.3029,6.56076 v 2.18691 h -2.1738 q 0,0.44442 -0.3481,0.76884 -0.3482,0.32437 -0.8236,0.32437 h -25.9149 q -0.4754,0 -0.8236,-0.32437 -0.3481,-0.32435 -0.3481,-0.76884 h -2.1737 v -2.18691 z m -11.9555,10.93458 h 4.3475 v 13.1215 h 2.1737 v -13.1215 h 4.3474 v 13.1215 h 2.1738 v -13.1215 h 4.3473 v 13.1215 h 2.1738 v -13.1215 h 4.3474 v 13.1215 h 1.002 q 0.4755,0 0.8236,0.32437 0.3481,0.32436 0.3481,0.76884 v 1.09343 h -28.2582 v -1.09343 q 0,-0.44441 0.3481,-0.76884 0.3482,-0.32437 0.8236,-0.32437 h 1.0019 z m 27.0866,16.40191 q 0.4755,0 0.8236,0.32437 0.3482,0.32436 0.3482,0.76877 v 2.187 h -32.6058 v -2.187 q 0,-0.44441 0.3482,-0.76877 0.3481,-0.32437 0.8236,-0.32437 z"/>',
				'</g>',
				'</svg>'
			)
		);
		return $params;
	}

	/**
	 * Generate the listings map canvas for Google maps.
	 *
	 * @since 4.0.0
	 * @return string
	 */
	public function map_canvas(): ?string
	{
		if (!class_exists('APP_Map_Provider_Registry') || !APP_Map_Provider_Registry::get_active_map_provider()) {
			return null;
		}
		ob_start();
		?>
		<div id="listings-map-wrap">
			<div id="listings-map" class="listing-map content-wrap"></div>
			<div id="map-loading">
				<h4><?php _e('Loading Map', APP_TD); ?></h4>
				<div class="spinner map_loader" id="listing_loader_maps">
					<div class="rect1"></div>
					<div class="rect2"></div>
					<div class="rect3"></div>
					<div class="rect4"></div>
					<div class="rect5"></div>
				</div>
			</div>
			<div id="map-no-results">
				<h4><?php _e('No Results Found', APP_TD); ?></h4>
			</div>
		</div>
		<?php
		echo ob_get_clean();
	}

	/**
	 * Generates the admin option fields.
	 *
	 * Remember to add each field to the whitelist otherwise it won't save.
	 * Located in includes/options.php
	 *
	 * @since  4.0.0
	 *
	 * @return array
	 */
	public function admin_fields($page)
	{
		self::$page = $page;

		// Insert the fields.
		$page->tab_sections['general']['map_providers']['fields'] = array_merge(
			$page->tab_sections['general']['map_providers']['fields'],
			array(
				array(
					'title' => __('Marker color', APP_TD),
					'type' => 'custom',
					'name' => 'marker_color',
					'render' => array($this, 'va_color_picker_callback'),
					'tip' => '',
				),
			)
		);

	}
}
