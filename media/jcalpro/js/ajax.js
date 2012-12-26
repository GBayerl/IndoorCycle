/**
 * @version		$Id: ajax.js 772 2012-04-17 19:21:09Z jeffchannell $
 * @package		JCalPro
 * @subpackage	com_jcalpro
@ant_copyright_header@
 */

(function() {
	// store the last page hash
	var oldhash = window.location.hash;
	// store prefetched data
	var prefetchData = {};
	
	/**
	 * takes a href string & parses out any date variable found
	 * 
	 * @param  string  href to change
	 * @return string  changed href
	 */
	var changeHref = function(h) {
		var r = /^([^#]*?)([1-9][0-9]{3}\-[0-9]{2}\-[0-9]{2})(.*?)$/, n, d;
		if (h && h.match(r)) {
			n = h.replace(r, '$1$3').replace(/date\=/, '').replace(/\?\&/, '?').replace(/\&{2,}/, '&');
			d = h.replace(r, '$2');
			h = n.replace(/[\?\&]$/, '') + '#' + d;
		}
		return h;
	};
	
	/**
	 * changes any links found in the component page to use hashes instead of request variables
	 */
	var fixLinks = function () {
		var hasModal = ('undefined' != typeof SqueezeBox);
		Array.each($('jcl_component').getElements('a'), function(el, idx) {
			if (hasModal && el.hasClass('modal')) {
				SqueezeBox.assign(el, {parse: 'rel'});
			}
			if (el.hasClass('noajax')) return;
			el.set('href', changeHref(el.get('href')));
		});
	};
	
	/**
	 * interval function to monitor the changing of the hash
	 */
	var hashChange = function() {
		var hash = window.location.hash;
		if (oldhash != hash) {
			requestPage(hash);
			oldhash = hash;
		}
	};
	
	/**
	 * destroys the loading element
	 */
	var destroyLoader = function() {
		try {
			$('jcl_ajax_loader').destroy();
		}
		catch (err) {
		}
	};
	
	/**
	 * creates the loading element
	 */
	var createLoader = function() {
		var loader = new Element('div#jcl_ajax_loader'), dim = $('jcl_component').getDimensions();
		loader.setStyles({opacity: 0.3, width: dim.x + 'px', height: dim.y + 'px'});
		loader.inject($('jcl_component'));
	};
	
	/**
	 * adds prefetch data for neighboring pages
	 */
	var prefetchNeighbors = function() {
		Array.each($('jcl_component').getElements('.ajaxlayoutlink'), function(el) {
			var hash = el.get('href').replace(/([^#]*?#)(.*)$/, '$2');
			fetchData(hash, false);
		});
	};
	
	/**
	 * gets the page data & appends to the page
	 * 
	 * @param  date hash
	 * @param  completion callback
	 */
	var fetchData = function(hash, complete) {
		if ('undefined' != typeof prefetchData[hash]) {
			if ('function' == typeof complete) complete(prefetchData[hash]);
			return;
		}
		var req = new Request.HTML({
			url: window.location.href
		,	data: {
				format: 'raw'
			,	date: hash
			}
		,	link: 'ignore'
		,	onSuccess: function(responseText) {
				Object.each(responseText, function(item, key, obj) {
					if ('undefined' == typeof item || 'TextNode' == typeof item || 'function' != typeof item.clone) delete obj[key];
				});
				prefetchData[hash] = responseText;
				if ('function' == typeof complete) complete(responseText);
			}
		}).send();
	};
	
	/**
	 * requests a new page to replace the current one
	 * 
	 * @param  string  date string
	 */
	var requestPage = function(hash) {
		hash = hash.replace(/^\#/, '');
		createLoader();
		fetchData(hash, function(html) {
			$('jcl_layout_body').empty();
			Object.each(html, function(el) {
				try {
					var copy = el.clone(true, true);
					$('jcl_layout_body').adopt(copy);
				}
				catch (err) {
				}
			});
			Array.each(['prev', 'next', 'current'], function(u, uidx){
				var uel = $('jcl_component').getElement('.ajax'+u);
				var tel = $('jcl_layout_value_'+u+'_text');
				var vel = $('jcl_layout_value_'+u+'_href');
				if (uel) {
					if (tel) uel.set('text', tel.value);
					if (vel) uel.set('href', vel.value);
					if ('current' != u) {
						if ('' == uel.get('text')) {
							uel.setStyle('visibility', 'hidden');
							uel.setStyle('display', 'none');
						}
						else {
							uel.setStyle('visibility', 'visible');
							uel.setStyle('display', 'inline');
						}
					}
				}
			});
			// we may not have this element
			try {
				// the values element
				var values = $('jcl_layout_values');
				// try to replace the toolbar buttons
				$('jcl_component').getElement('.jcl_toolbar').set('html', values.getElement('.jcl_toolbar').get('html'));
				
				// destroy the whole thing
				values.destroy();
			}
			catch (err) {};
			// fix tooltips, if possible
			if ('undefined' != Tips) {
				Array.each($('jcl_component').getElements('.hasTip'), function(el, idx) {
					var title = el.get('title');
					if (title) {
						var parts = title.split('::', 2);
						el.store('tip:title', parts[0]);
						el.store('tip:text', parts[1]);
					}
				});
				var JTooltips = new Tips($('jcl_component').getElements('.hasTip'), {maxTitleChars: 50, fixed: false});
			}
			destroyLoader();
			fixLinks();
			prefetchNeighbors();
		});
	};
	
	// sets the interval for the hash change monitor
	setInterval(hashChange, 200);
	
	// initialization
	window.addEvent('load', function() {
		// existing hashes take precedence
		if (oldhash) {
			requestPage(oldhash);
		}
		// no hash
		else {
			fixLinks();
			prefetchNeighbors();
		}
	});
})();
