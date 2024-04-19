/* global $, jQuery, l10n, validateL10n, appthemes_map_vars, appthemes_map_icon */
(function ($) {
	$.extend(true, $.appthemes.appthemes_map.prototype, {
		options: {
			zoom: 17
		},

		_auto_zoom: function () {
			if (this.markers.length < 1) {
				return;
			}

			this.options.map.fitBounds(this.option.markers);
		},

		_create_map: function () {
			const map = L.map(this.element[0].id, {
				scrollWheelZoom: false
			});
			map.setView([this.options.center_lat, this.options.center_lng], this.options.zoom);	// set the initial center of the map

			if(this.options.tile_layer_url == '')
			{
				this.options.tile_layer_url = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png'; // OSM Carto
			}

			if(this.options.attribution == '')
			{
				this.options.attribution = '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>';
			}

			if(this.options.max_zoom == '')
			{
				this.options.max_zoom = 19;
			}

			L.tileLayer(this.options.tile_layer_url, {
				maxZoom: this.options.max_zoom,
				attribution: this.options.attribution
			}).addTo(map);

			if (!this.options.leaflet_attribution || this.options.leaflet_attribution === '') {
				map.attributionControl.setPrefix('');
			}

			return map;
		},

		_create_marker: function (marker_opts) {
			const marker = L.marker([marker_opts.lat, marker_opts.lng], {
				draggable: (marker_opts.draggable ? true : false)
			}).addTo(this.options.map);

			return marker;
		},

		_create_marker_info: function () {
			return L.popup();
		},

		_set_marker_info: function (info, marker, marker_opts) {
			info.setContent(marker_opts.popup_content);
			info.setLatLng(marker.getLatLng());
			marker.bindPopup(info);
		},

		_set_marker_anchor: function (marker, anchor) {
			google.maps.event.addListener(marker, "click", function (e) {
				location = anchor;
			});
		},

		_get_marker_position: function (marker) {
			return marker.getPosition();
		},

		_marker_drag_end: function (marker) {
			var $this = this;
		},

		_update_marker_position: function (updated_pos, marker, map) {
			marker.setLatLng(updated_pos);
			map.setView(updated_pos);
		},

		// _directions_btn_handler: function (start_address, end_address, directions_panel, print_directions_btn) {
		// 	var $this = this;
		// 	var directionsDisplay = new google.maps.DirectionsRenderer();
		// 	var directionsService = new google.maps.DirectionsService();
		// 	var start = jQuery('#' + start_address).val();
		// 	var end = end_address; // This is the address for the listing
		// 	var map = this.options.map;
		// 	var request = {
		// 		origin: start,
		// 		destination: end,
		// 		region: appthemes_map_vars.geo_region,
		// 		travelMode: google.maps.TravelMode.DRIVING,
		// 		unitSystem: (appthemes_map_vars.geo_unit == 'mi') ? google.maps.UnitSystem.IMPERIAL : google.maps.UnitSystem.METRIC
		// 	};

		// 	directionsService.route(request, function (result, status) {
		// 		jQuery('#' + directions_panel).show();
		// 		if (status == google.maps.DirectionsStatus.OK) {
		// 			directionsDisplay.setDirections(result);
		// 			this.markers[0].setVisible(false);
		// 			jQuery('#' + print_directions_btn).slideDown('fast');
		// 			directionsDisplay.setPanel(document.getElementById(directions_panel));
		// 			directionsDisplay.setMap(map);
		// 		} else {
		// 			jQuery('#' + print_directions_btn).hide();
		// 			jQuery('#' + directions_panel).html(appthemes_map_vars.text_directions_error).fadeOut(5000, function () {
		// 				$(this).html('');
		// 			});
		// 			// restore original map on failure
		// 			directionsDisplay.setMap(null);

		// 			if ($this.options.markers) {
		// 				$.each($this.options.markers, function (i, marker_opts) {
		// 					var marker = $this.add_marker(marker_opts);
		// 					$this.markers.push(marker);
		// 				});

		// 				$this.auto_zoom();
		// 			}
		// 		}
		// 	});
		// }
	});

})(jQuery);


