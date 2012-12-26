/**
 * @version		$Id: event.js 805 2012-09-20 01:26:01Z jeffchannell $
 * @package		JCalPro
 * @subpackage	com_jcalpro
@ant_copyright_header@
 */

/**
 * hides or shows options for recurrence type
 * 
 * @param obj
 * @return
 */
function jclShowRecOptions(obj) {
	var opts = [
		'jcl_rec_none_options'
	,	'jcl_rec_daily_options'
	,	'jcl_rec_weekly_options'
	,	'jcl_rec_monthly_options'
	,	'jcl_rec_yearly_options'
	]
	, repeatend = JCalPro.getElements('jcl_component', '.jcalrepeatend')
	, i = 0, elem, disp;
	for (; i<opts.length; i++) {
		elem = $(opts[i]);
		disp = (i == obj.value ? 'block' : 'none');
		elem.setStyle('display', disp);
	}
	JCalPro.each(repeatend, function(el, idx) {
		el.setStyle('display', (0 == obj.value ? 'none' : ('table' == el.tagName.toLowerCase() ? 'table' : 'block')));
	});
}

/**
 * hides or shows options for registration
 * 
 * @param obj
 * @return
 */
function jclShowRegOptions(obj) {
	var opts = [
		'jcl_registration_off_options'
	,	'jcl_registration_on_options'
	]
	, i = 0, elem, disp;
	for (; i<opts.length; i++) {
		elem = $(opts[i]);
		disp = (i == obj.value ? 'block' : 'none');
		elem.setStyle('display', disp);
	}
}

/**
 * initializes fields with sub options
 * 
 * @param count
 * @param optname
 * @param togglename
 * @param callback
 * @return
 */
function jclInitializeOptions(count, optname, togglename, callback) {
	var i = 0, toggle = 'minus', elem;
	for (; i<count; i++) {
		elem = $('jform_' + optname + i);
		if (!elem) return;
		if (elem.checked) {
			callback(elem);
			if (0 > i) toggle = 'plus';
			// this only works on frontend
			try {
				$('jcl_component').getElement('.jcal_' + togglename + '_' + toggle).fireEvent('click');
			} catch (e) {}
			return;
		}
	}
}

/**
 * initializes the form toggles
 * 
 * @return
 */
function jclInitializeToggle(bType) {
	var buttons = $('jcl_component').getElements('.jcal_' + bType + '_button');
	if (buttons) {
		Array.each(buttons, function(el, idx) {
			el.addEvent('click', function(ev) {
				if (ev) JCalPro.stopEvent(ev);
				var opening, closing;
				if (el.hasClass('jcal_' + bType + '_plus')) {
					opening = 'close';
					closing = 'open'
				}
				else if (el.hasClass('jcal_' + bType + '_minus')) {
					opening = 'open';
					closing = 'close'
				}
				else {
					return;
				}
				$(bType + '_' + opening).setStyle('display', 'block');
				$(bType + '_' + closing).setStyle('display', 'none');
			});
		});
	}
}

/**
 * initializes the day select
 */
function jclInitializeDaySelect() {
	var day = $('jformstart_date_arrayday');
	if (day) {
		day.addEvent('change', function(ev) {
			var v = this.getElement(':selected').value;
			$('jform_rec_monthly_day_number').value = v;
			$('jform_rec_yearly_day_number').value = v;
		});
	}
}

/**
 * toggles recurrence
 * 
 * @param t
 */
function jclToggleRegEnd(t) {
	var d = $('jformregistration_end_date_arrayday');
	if (d) {
		var li = d.getParent();
		Array.each(li.getElements('select'), function(el, idx) {
			if (t) el.erase('disabled');
			else   el.set({disabled: 'disabled'});
		});
	}
}

/**
 * initializes registration end
 */
function jclInitializeRegEnd() {
	var fieldset = $('jform_registration_until_event');
	if (fieldset) {
		jclToggleRegEnd(0 == parseInt(fieldset.getElement(':checked').value, 10));
	}
}

/**
 * adds listeners to various form elements so that if they gain focus,
 * the corresponding radio options get selected
 * 
 * @return
 */
function jclInitializeRadioFocus() {
	// duration
	Array.each(['jform_end_days', 'jform_end_hours', 'jform_end_minutes'], function(el, idx) {
		el = $(el);
		if (!el) return;
		el.addEvent('focus', function(ev) {
			$('jform_duration_type0').checked = 'checked';
		});
	});
	// month/year repeat
	Array.each(['monthly', 'yearly'], function(el, idx) {
		var dayNum = $('jform_rec_' + el + '_day_number');
		if (!dayNum) return;
		dayNum.addEvent('focus', function(ev) {
			$('jform_rec_' + el + '_type0').checked = 'checked';
		});
		Array.each(['order', 'type'], function(sel, sidx) {
			$('jform_rec_' + el + '_day_' + sel).addEvent('focus', function (ev) {
				$('jform_rec_' + el + '_type1').checked = 'checked';
			});
		});
	});
	// end times
	try {
		$('jform_recur_end_count').addEvent('focus', function(el, idx) {
			$('jform_recur_end_type0').checked = 'checked';
		});
		$('jform_recur_end_until').addEvent('focus', function(el, sidx) {
			$('jform_recur_end_type1').checked = 'checked';
		});
		$('jform_recur_end_until_img').addEvent('click', function(el, sidx) {
			$('jform_recur_end_type1').checked = 'checked';
		});
	}
	catch (err) {
		// must be in a display view
	}
}

/**
 * adds listeners to the date parts of the start date field to update the recurrence fields
 * this is used primarily to enforce rules already in place during event save
 * and to prevent users from having to figure out certain elements of the recurrence
 * 
 * for example, when repeating weekly, the user has to ensure that the day of the week
 * that the start date falls on is selected - rather than have them calculate that themselves,
 * we should calculate it for them and check that box for them
 * 
 * @return
 */
function jclInitializeRecUpdateFromStartDate() {
	if ('undefined' == typeof window.jclDateTimeCheckUrl) return;
	var dateparts = ['jformstart_date_arrayday', 'jformstart_date_arraymonth', 'jformstart_date_arrayyear', 'jformstart_date_arrayhour', 'jformstart_date_arrayminute'];
	var days = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
	Array.each(dateparts, function(el, idx) {
		el = $(el);
		if (!el) return;
		el.set('idx', idx);
		el.addEvent('change', function(ev) {
			if (1 == el.get('idx')) {
				Array.each($('jform_rec_yearly_on_month').getElements('option'), function(oel, oidx) {
					if (oel.value == el.getElement(':selected').value) {
						oel.selected = 'selected';
					}
				});
			}
			var data = {
				day     : $(dateparts[0]).getElement(':selected').value
			,	month   : $(dateparts[1]).getElement(':selected').value
			,	year    : $(dateparts[2]).getElement(':selected').value
			,	hour    : $(dateparts[3]).getElement(':selected').value
			,	minute  : $(dateparts[4]).getElement(':selected').value
			,	timezone: $('jform_timezone').getElement(':selected').value
			}
			, req = new Request.JSON({
				url: window.jclDateTimeCheckUrl
			,	data: data
			,	format: 'json'
			,	onSuccess: function(responseJSON, responseText) {
					if (!responseJSON.valid) {
						alert(responseJSON.error ? responseJSON.error : Joomla.JText._('COM_JCALPRO_INVALID_DATE'));
						Array.each(dateparts, function(pel, pidx) {
							$(pel).addClass('invalid');
						});
						return;
					}
					Array.each(dateparts, function(pel, pidx) {
						$(pel).removeClass('invalid');
					});
					try {
						$('jform_rec_weekly_on_' + days[responseJSON.weekday]).checked = 'checked';
					}
					catch (err) {
						$('jform_rec_weekly_on_' + days[responseJSON.weekday]).set('checked', 'checked');
					}
				}
			,	onError: function(text, error) {
					alert(error);
				}
			,	onFailure: function(xhr) {
					alert('request failed');
				}
			}).send();
		});
	});
}

/**
 * Initializes a change event on privacy selection to toggle published status
 * 
 * @return
 */
function jclInitializePrivacy() {
	var priv = $('jform_private'), publish = $('jformpublished'), approve = $('jform_approved'), reg = $('jcl_registration');
	if (!priv || !publish) return;
	priv.addEvent('change', function(ev) {
		if (1 == priv.getElement(':selected').value) {
			if (0 == window.jclAcl.editState) publish.removeProperty('disabled');
			if (approve) approve.set('disabled', 'disabled');
			if (reg) reg.set('styles', {display: 'none'});
		}
		else {
			if (0 == window.jclAcl.editState) publish.set('disabled', 'disabled');
			if (approve) approve.removeProperty('disabled');
			if (reg) reg.set('styles', {display: 'block'});
		}
	});
	priv.fireEvent('change');
}

/**
 * initializes a map for this event
 * 
 * @param lat
 * @param lon
 */
function jclEventMapInit(lat, lng) {
	var map = document.getElementById('jcl_event_map');
	if (map) {
		window.jcl_event_latlng = new google.maps.LatLng(lat, lng);
		var mapOptions = {
			zoom: 8
		,	center: window.jcl_event_latlng
		,	mapTypeId: google.maps.MapTypeId.ROADMAP
		};
		window.jcl_event_map = new google.maps.Map(map, mapOptions);
		setTimeout(function() {
			jcl_marker = new google.maps.Marker({
				map: window.jcl_event_map
			,	position: window.jcl_event_latlng
			});
		}, 200);
	}
}

/**
 * handler for load event
 */
window.addEvent('load', function() {
	jclInitializePrivacy();
	// set recurrence buttons
	jclInitializeToggle('recurrence');
	jclInitializeToggle('registration');
	// set options
	jclInitializeOptions(5, 'recur_type', 'recurrence', jclShowRecOptions);
	// set day select to reset recurrence options
	jclInitializeDaySelect();
	// set state of registration - this may not be accessible so dump errors
	try {
		jclInitializeOptions(2, 'registration', 'registration', jclShowRegOptions);
		jclInitializeRegEnd();
	}
	catch (err) {}
	// help our user with recurrence parts
	jclInitializeRecUpdateFromStartDate();
	// assist in focusing the correct inputs
	jclInitializeRadioFocus();
});
