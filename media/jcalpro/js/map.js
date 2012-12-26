/**
 * @version		$Id: map.js 805 2012-09-20 01:26:01Z jeffchannell $
 * @package		JCalPro
 * @subpackage	com_jcalpro
@ant_copyright_header@
 */

var jcl_lat = 0.0, jcl_lng = 0.0, jcl_map, jcl_geocoder, jcl_marker;

/**
 * map initialization function
 */
var jcl_map_init = function() {
	var map = document.getElementById('map_canvas'), loc;
	if (map) {
		loc = jcl_get_latlng();
		jcl_geocoder = new google.maps.Geocoder();
		var latlng = new google.maps.LatLng(loc.lat, loc.lng);
		var mapOptions = {
			zoom: 8
		,	center: latlng
		,	mapTypeId: google.maps.MapTypeId.ROADMAP
		};
		jcl_map = new google.maps.Map(map, mapOptions);
		if (!(0 == loc.lat && 0 == loc.lng)) {
			jcl_marker = new google.maps.Marker({map: jcl_map, position: latlng});
		}
	}
};

/**
 * refresh the map from the given element
 */
var jcl_map_refresh = function(id) {
	var textarea = JCalPro.id(id), address, loc;
	if (textarea) {
		address = textarea.value.replace(/[\t\n\r]/, ' ').replace(/^\s+/, '').replace(/\s+$/, '');
		if ('' == address) return;
		jcl_geocoder.geocode({'address': address}, function(results, status) {
			switch (status) {
				case google.maps.GeocoderStatus.OK :
					loc = results[0].geometry.location;
					jcl_map.setCenter(loc);
					try {
						jcl_marker.setMap(null);
					}
					catch (err) {
						// nothing, just swallow errors
					}
					jcl_marker = new google.maps.Marker({map: jcl_map, position: loc});
					jcl_update_latlng(loc.lat(), loc.lng());
					jcl_update_city(results[0], address);
					break;
				default :
					alert(Joomla.JText._('COM_JCALPRO_GEOCODER_STATUS_' + status));
					break;
			}
		});
	}
};

/**
 * gets the latitude & longitude from the hidden inputs
 */
var jcl_get_latlng = function() {
	var latitude = JCalPro.id('jform_latitude'), longitude = JCalPro.id('jform_longitude'), ll = {
		lat: jcl_lat
	,	lng: jcl_lng
	};
	if (latitude && longitude) {
		ll.lat = latitude.value;
		ll.lng = longitude.value;
	}
	return ll;
};

/**
 * update the city field
 */
var jcl_update_city = function(result, address) {
	var city = false, guess = false, field = JCalPro.id('jform_city');
	if (!field || 'undefined' == typeof result.address_components) return;
	JCalPro.each(result.address_components, function(el) {
		if (city || 'undefined' == typeof el.types) return;
		for (var i=0; i<el.types.length; i++) {
			if ('locality' == el.types[i]) {
				city = el.long_name;
				break;
			}
		}
		/*
		if (city || 'undefined' == typeof el.long_name || el.long_name.match(/^[0-9]+$/)) return;
		guess = el.long_name;
		if (-1 != address.toLowerCase().indexOf(guess.toLowerCase())) {
			city = el.long_name;
		}
		*/
	});
	if (city) field.value = city;
	//else if (guess) field.value = guess;
};

/**
 * update the latitude and longitude fields
 */
var jcl_update_latlng = function(lat, lng) {
	var latitude = JCalPro.id('jform_latitude'), longitude = JCalPro.id('jform_longitude');
	if (!latitude || !longitude) return;
	latitude.value = lat;
	longitude.value = lng;
};

if ('undefined' != typeof google && 'undefined' != google.maps) {
	google.maps.event.addDomListener(window, 'load', jcl_map_init);
}
