/**
 * @version		$Id: ajax.js 832 2012-10-30 18:41:43Z jeffchannell $
 * @package		JCalPro
 * @subpackage	mod_jcalpro_calendar

**********************************************
JCal Pro
Copyright (c) 2006-2012 Anything-Digital.com
**********************************************
JCalPro is a native Joomla! calendar component for Joomla!

JCal Pro was once a fork of the existing Extcalendar component for Joomla!
(com_extcal_0_9_2_RC4.zip from mamboguru.com).
Extcal (http://sourceforge.net/projects/extcal) was renamed
and adapted to become a Mambo/Joomla! component by
Matthew Friedman, and further modified by David McKinnis
(mamboguru.com) to repair some security holes.

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This header must not be removed. Additional contributions/changes
may be added to this header as long as no information is deleted.
**********************************************
Get the latest version of JCal Pro at:
http://anything-digital.com/
**********************************************

 */

function mod_jcalpro_calendar_ajax(cal) {
	var id = parseInt(JCalPro.getAttribute(cal, 'id').replace(/jcalpro_calendar_/, ''), 10)
	,   buttons = JCalPro.getElements(cal, '.jcalpro_calendar_nav_button')
	,   data = window.mod_jcalpro_calendar['mod' + id]
	,   loader = JCalPro.getElement(cal, '.jcalpro_calendar_loader')
	;
	if (!buttons || 'object' != typeof data) return;
	if (loader) loader.setStyle('display', 'none');
	try {
		new Tips(JCalPro.els('.jcalpro_calendar_tip_' + id));
	}
	catch (err) {
		
	}
	JCalPro.each(buttons, function(button, bidx) {
		var url = false;
		if (JCalPro.hasClass(button, 'jcalpro_calendar_nav_prev')) url = data.prev;
		else if (JCalPro.hasClass(button, 'jcalpro_calendar_nav_next')) url = data.next;
		else return;
		JCalPro.addEvent('click', button, function(ev) {
			if (loader) loader.setStyle('display', 'block');
			var req = new Request.HTML({
				url: url
			,	link: 'ignore'
			,	update: cal
			,	evalScripts: true
			,	filter: '.jcalpro_calendar>*'
			,	onSuccess: function() {
					mod_jcalpro_calendar_ajax(cal);
				}
			}).send();
		});
	});
}

(function() {
	JCalPro.onLoad(function() {
		if ('object' != typeof window.mod_jcalpro_calendar) return;
		var cals = JCalPro.els('.jcalpro_calendar');
		if (!cals) return;
		JCalPro.each(cals, function(cal, cidx) {
			mod_jcalpro_calendar_ajax(cal);
		});
	});
})();