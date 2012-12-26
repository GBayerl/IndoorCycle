/**
 * @version		$Id: admin.js 772 2012-04-17 19:21:09Z jeffchannell $
 * @package		JCalPro
 * @subpackage	mod_jcalpro_flex

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

window.jcal_flex_sortables = new Sortables([], {
	clone: false
,	handle: 'legend'
,	revert: {duration: 500, transition: 'elastic:out'}
,	opacity: 0.7
,	constrain: true
,	onComplete: function(e) {
		Array.each(e.getParent().getElements('.jcalflexpanel'), function(el, idx) {
			jcl_flex_panel_number(el, jcl_flex_panel_get_number(el), idx + 1);
		});
	}
});

function jcal_flex_panel_add() {
	var type   = document.id('jcalflexpanel_type_select').get('value');
	var base   = document.id('jcalflexpanel_' + type + '_0');
	var parent = base.getParent().getElement('.jcalflexpanels');
	var last   = parent.getChildren().getLast();
	// get the number from the last element
	var next;
	try {
		next = jcl_flex_panel_get_number(last) + 1;
	}
	catch (err) {
		next = 1;
	}
	// clone & inject the new panel
	var panel  = base.clone().inject(parent, 'bottom');
	// reset the element
	jcl_flex_panel_number(panel, '0', next);
	// scroll to the anchor
	jcl_flex_panel_scrollto(panel);
	// add to sortables
	window.jcal_flex_sortables.addItems(panel);
}

function jcal_flex_panel_del(what) {
	if (!confirm(Joomla.JText._('MOD_JCALPRO_FLEX_CONFIRM_PANEL_DELETE'))) return;
	// fade this panel out before destroying it
	var kill = function() {
		var panel = what.getParent().getParent().getParent();
		// scroll to the previous fieldset
		jcl_flex_panel_scrollto(panel.getPrevious());
		// renumber the ones after this one
		var step = panel;
		var num  = jcl_flex_panel_get_number(step);
		while (step = step.getNext('div')) {
			jcl_flex_panel_number(step, jcl_flex_panel_get_number(step), num++);
		}
		// remove from sortables
		window.jcal_flex_sortables.removeItems(panel);
		// kill the fieldset
		panel.destroy();
	};
	try {
		var fFx = new Fx.Morph(what.getParent().getParent().getParent(), {
			onComplete: kill
		,	duration: 500
		,	transition: Fx.Transitions.Sine.easeOut
		}).start({
			opacity:0
		,	height:0
		});
	}
	catch (err) {
		kill();
	}
}

function jcl_flex_panel_number(panel, from, to) {
	var id = panel.id.replace(/[0-9]+$/, to), reg = new RegExp('\\[' + from + '\\]');
	panel.removeAttribute('id');
	panel.set('id', id);
	panel.getElement('a[name="panel_' + from + '"]').set('name', 'panel_' + to);
	panel.getElement('legend span').set('text', to);
	Array.each(panel.getElements('input, select, textarea'), function(el) {
		el.set('name', el.get('name').replace(reg, '[' + to + ']'));
	});
}

function jcl_flex_panel_get_number(panel) {
	return parseInt(panel.getElement('legend span').get('text'), 10);
}

function jcl_flex_panel_scrollto(what) {
	try {
		var sFx = new Fx.Scroll(window).toElement(what, 'y');
	}
	catch (err) {}
}

