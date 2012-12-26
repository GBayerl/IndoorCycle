/**
 * @version		$Id: jcalpro.js 826 2012-10-24 18:31:29Z jeffchannell $
 * @package		JCalPro
 * @subpackage	com_jcalpro
@ant_copyright_header@
 */

/**
 * Global JCalPro object
 * 
 * used to bridge the gap between 2.5 and 3.0 (and maybe beyond, someday)
 * 
 */
window.JCalPro = window.JCalPro || {
	
	/**
	 * attach a function to the load event
	 * 
	 * @param function
	 */
	onLoad: function(handler) {
		return JCalPro._switch(function(){
			return window.addEvent('load', handler);
		}, function(){
			return jQuery(document).ready(handler);
		});
	}
	,
	/**
	 * get a DOM element by id
	 * 
	 * @param string
	 * 
	 * @return DOM element
	 */
	id: function(sel) {
		return JCalPro._switch(function(){
			return document.id(sel);
		}, function(){
			return jQuery('#' + sel);
		});
	}
	,
	/**
	 * get a collection of DOM elements
	 * 
	 * @param string
	 * 
	 * @return DOM elements
	 */
	els: function(sel) {
		return JCalPro._switch(function(){
			return $$(sel);
		}, function(){
			return jQuery(sel);
		});
	}
	,
	/**
	 * find the first element matching the selector inside the given element
	 * 
	 * @param element
	 * @param selector
	 * 
	 * @return mixed
	 */
	getElement: function(el, sel) {
		try {
			return JCalPro._switch(function(){
				return $(el).getElement(sel);
			}, function(){
				return jQuery(el).find(sel).first();
			});
		}
		catch (err) {
			return null;
		}
	}
	,
	/**
	 * find the elements matching the selector inside the given element
	 * 
	 * @param elements
	 * @param selector
	 * 
	 * @return mixed
	 */
	getElements: function(el, sel) {
		return JCalPro._switch(function(){
			return $(el).getElements(sel);
		}, function(){
			return jQuery(el).find(sel);
		});
	}
	,
	/**
	 * get an element's attribute
	 * 
	 * @param element
	 * @param attribute
	 * 
	 * @return mixed
	 */
	getAttribute: function(el, attr) {
		return JCalPro._switch(function(){
			return $(el).get(attr);
		}, function(){
			return jQuery(el).attr(attr);
		});
	}
	,
	/**
	 * set an element's attribute
	 * 
	 * @param element
	 * @param attribute
	 * @param value
	 * 
	 * @return mixed
	 */
	setAttribute: function(el, attr, value) {
		// TODO: support jQuery map in mootools?
		return JCalPro._switch(function(){
			return $(el).set(attr, value);
		}, function(){
			return jQuery(el).attr(attr, value);
		});
	}
	,
	/**
	 * stop the current event
	 * 
	 * @param event
	 * 
	 * @return void
	 */
	stopEvent: function(e) {
		try {
			new Event(e).stop();
			return;
		}
		catch (err) {}
		try {
			e.preventDefault();
			return;
		}
		catch (err) {}
		// wtf? shouldn't get here, but oh well :)
		// this is bad because it stops propagation instead of preventing default behavior
		// but oh well, we should never see this part anyways (but never say never!)
		e = e||window.event;
		try {
			e.stopPropagation();
		}
		catch (err) {
			e.cancelBubble = true;
		}
	}
	,
	/**
	 * make a request
	 * 
	 * @param object
	 * 
	 * @return ?
	 */
	request: function(opts) {
		var type = opts.requestType || 'html';
		if (opts.requestType) delete opts.requestType;
		return JCalPro._switch(function(){
			var r;
			switch (type) {
				case 'json': r = new Request.JSON(opts);
				case 'html':
				default:
					r = new Request.HTML(opts);
			}
			return r.send();
		}, function(){
			// fix url
			var url = opts.url;
			delete opts.url;
			// fix success function
			opts.success = function(data) {
				if (opts.update) {
					jQuery(opts.update).html(data);
				}
				opts.onSuccess();
			};
			return jQuery.ajax(url, opts);
		});
	}
	,
	/**
	 * simple iterator
	 * 
	 * @param array
	 * @param function
	 */
	each: function(collection, callback) {
		return JCalPro._switch(function(){
			return Array.each(collection, callback);
		}, function(){
			return jQuery.each(collection, (function(idx, el){
				callback(el, idx);
			}));
		});
	}
	,
	/**
	 * add an event to an element
	 * 
	 * @param string
	 * @param DOM element
	 * @param function
	 */
	addEvent: function(event, el, callback) {
		return JCalPro._switch(function(){
			return el.addEvent(event, callback);
		}, function(){
			return el[event](callback);
		});
	}
	,
	/**
	 * determines if an element has a class
	 * 
	 * @param mixed
	 * @param string
	 * 
	 * @return bool
	 */
	hasClass: function(el, className) {
		return JCalPro._fw(el, 'hasClass', className);
	}
	,
	/**
	 * determines if an element has a class
	 * 
	 * @param mixed
	 * @param string
	 * 
	 * @return bool
	 */
	toggleClass: function(el, className) {
		return JCalPro._fw(el, 'toggleClass', className);
	}
	,
	/**
	 * debug via the console
	 * 
	 * @param mixed
	 * 
	 * @return void
	 */
	debug: function(data) {
		try {
			console.log(data);
		}
		catch (e) {
			// we do nothing here
		}
	}
	,
	/**
	 * execute a function from the proper framework, provided both frameworks have the same function
	 * 
	 * @param mixed
	 * @param string
	 * @param mixed
	 * 
	 * @return mixed
	 */
	_fw: function(el, func, arg) {
		return JCalPro._switch(function(){
			return $(el)[func](arg);
		}, function(){
			return jQuery(el)[func](arg);
		});
	}
	,
	/**
	 * fire a callback depending on mootools or jquery
	 * 
	 * @param function
	 * @param function
	 */
	_switch: function(moo, jq) {
		if (document.id) {
			return moo();
		}
		else if (jQuery) {
			return jq();
		}
		else {
			throw 'No handler';
		}
	}
};
