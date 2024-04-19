<?php

/**
 * Geocoder using Google Maps API v3
 */
class APP_Nominatim_Geocoder extends APP_Geocoder
{

	private $api_url = 'https://nominatim.openstreetmap.org/search?format=jsonv2&limit=1&addressdetails=1&q=';

	/**
	 * Sets up the gateway
	 */
	public function __construct()
	{
		parent::__construct(
			'nominatim',
			array(
				'dropdown' => __('Nominatim', APP_TD),
				'admin' => __('Nominatim', APP_TD)
			)
		);
	}

	public function has_required_vars()
	{

		return true;
	}

	public function process_geocode()
	{
		$this->set_bounds();
		$this->set_coords();
		$this->set_address_components();
		$this->set_address();
		$this->set_response_code();
		$this->calculate_radius();
	}

	public function geocode_address($address)
	{
		$args = array(
			'address' => urlencode($address)
		);

		return $this->geocode_api($args);
	}

	public function geocode_lat_lng($lat, $lng)
	{
		$args = array(
			'latlng' => (float) $lat . ',' . (float) $lng,
		);

		return $this->geocode_api($args);
	}

	public function geocode_api($args)
	{

		$defaults = array(
		);

		$options = wp_parse_args($this->options, $defaults);

		$params = array(
		);

		$args = wp_parse_args($args, $params);

		$api_url = $this->api_url . urlencode($args['address']);

		$api_url = esc_url_raw($api_url);

		$response = wp_remote_get($api_url);

		if (200 != wp_remote_retrieve_response_code($response)) {
			return false;
		}

		$this->geocode_results = json_decode(wp_remote_retrieve_body($response), true);

		if (!$this->geocode_results) {
			return false;
		}

		$this->process_geocode();
	}

	public function set_response_code()
	{
		$this->_set_response_code(200);
	}

	public function set_bounds()
	{
		if ($this->geocode_results[0]['boundingbox']) {
			$this->_set_bounds(
				$this->geocode_results[0]['boundingbox'][1], // ne_lat
				$this->geocode_results[0]['boundingbox'][2], // ne_lng
				$this->geocode_results[0]['boundingbox'][0], // sw_lat
				$this->geocode_results[0]['boundingbox'][3], // sw_lng
			);
		}
	}

	public function set_coords()
	{

		if ($this->geocode_results[0]['lat'] && $this->geocode_results[0]['lon']) {
			$point = $this->geocode_results[0];

			$this->_set_coords($point['lat'], $point['lon']);
		}
	}

	public function set_address()
	{
		if (isset($this->geocode_results[0]['display_name'])) {
			$address = $this->get_address_components();
			$street_number = $address['street_number'];
			$street = $address['street'];
			$city = $address['city'];
			$state = $address['state'];
			$postal_code = $address['postal_code'];
			$country = $address['country'];

			$formatted_address = "{$street} {$street_number}, {$city}, {$state} {$postal_code}, {$country}";

			$this->_set_address($formatted_address);
		}
	}

	/**
	 * Retrieves address components array from geocode result.
	 *
	 * @return array
	 */
	public function set_address_components()
	{
		$output = array();

		if (!$this->geocode_results[0]['address']) {
			return $output;
		}

		$data = (array) $this->geocode_results[0]['address'];

		$output = $this->parse_address_components($data);

		$this->_set_address_components($output);
	}

	/**
	 * Builds formatted address components array from the raw data.
	 *
	 * @param array $data Raw components array.
	 *
	 * @return array Formatted array.
	 */
	public function parse_address_components($data)
	{
		$output = array();

		foreach ($data as $key => $value) {

			switch ($key) {
				case 'house_number':
				case 'house_name':
					$output['street_number'] = $value;
					break;
				case 'road':
					$output['street'] = $value;
					break;
				case 'town':
				case 'city':
				case 'village':
					$output['city'] = $value;
					break;
				case 'state':
				case 'region':
				case 'county':
					$output['state'] = $value;
					break;
				case 'postcode':
					$output['postal_code'] = $value;
					break;
				case 'country':
					$output['country'] = $value;
					break;
			}

			// edgecase
			if (!isset($data['city']) && !isset($data['village']) && !isset($data['town'])) {
				$output['city'] = $data['municipality']; // Some places don't have city, village or town, and are only in a municipality
			}
		}

		return $output;
	}

	public function form()
	{
		$settings = array(
			// Empty array means no tab
			// array(
			// 	'title' => __('General', APP_TD),
			// 	'fields' => array(
			// 	)
			// )
		);

		return $settings;
	}
}

appthemes_register_geocoder('APP_Nominatim_Geocoder');
