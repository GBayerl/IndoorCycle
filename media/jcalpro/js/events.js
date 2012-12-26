/**
 * @version		$Id: events.js 772 2012-04-17 19:21:09Z jeffchannell $
 * @package		JCalPro
 * @subpackage	com_jcalpro
@ant_copyright_header@
 */

(function() {
	window.addEvent('load', function() {
		if ('undefined' == typeof window.jclChildrenEvents) return;
		Array.each(window.jclChildrenEvents.list, function(id) {
			var row = $('jcl_component').getElement('.event-row-' + id);
			if (!row) return;
			Array.each(row.getElements('.event-row-confirm a'), function(el, idx) {
				var p = el.getParent(), o = el.onclick;
				el.set('onclick', null);
				el.addEvent('click', function(ev) {
					if (!confirm(Joomla.JText._('COM_JCALPRO_CONFIRM_DETACH'))) {
						new Event(ev).stop();
						return false;
					}
					return o(ev);
				});
			});
		});
		Array.each($$('a.toolbar'), function(el) {
			var icon = el.getElement('span');
			var o = el.onclick;
			if (icon.hasClass('icon-32-delete')) {
				el.set('onclick', null);
				el.addEvent('click', function(ev) {
					var toggle = $('event-toggle');
					toggle.set('checked', 'checked');
					Joomla.checkAll(toggle);
					return o(ev);
				});
			}
			if (!icon.hasClass('icon-32-publish') && !icon.hasClass('icon-32-unpublish') && !icon.hasClass('icon-32-trash')) return;
			el.set('onclick', null);
			el.addEvent('click', function(ev) {
				if (0 == document.adminForm.boxchecked.value) return o(ev);
				var children = false, checked = [];
				Array.each($('jcl_component').getElements('.adminlist tbody input[type=checkbox]:checked'), function(input) {
					var value = parseInt(input.value, 10);
					checked.push(value);
					if (children) return;
					if ('cid[]' != input.get('name')) return;
					if (!window.jclChildrenEvents.list.contains(value)) return;
					children = true;
				});
				if (children) {
					var warned = false, confirmed = false;
					Array.each(window.jclChildrenEvents.data, function(data) {
						if (checked.contains(data.id) || warned) return;
						warned = true;
						confirmed = confirm(Joomla.JText._('COM_JCALPRO_CONFIRM_DETACH_MULTI'));
					});
					if (warned && !confirmed) {
						new Event(ev).stop();
						return false;
					}
				}
				return o(ev);
			});
		});
	});
})();
