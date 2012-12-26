/**
 * @version		$Id: catselect.js 772 2012-04-17 19:21:09Z jeffchannell $
 * @package		JCalPro
 * @subpackage	com_jcalpro
@ant_copyright_header@
 */

(function(){
	window.addEvent('load', function() {
		var d = window.parent.document, id, eid, task, catid, form, oldvalue;
		catid = d.getElementById('event-catid');
		if (catid && catid.value) {
			Array.each($('catid').getElements('option'), function(el, idx) {
				if (catid.value == el.value) {
					el.set('selected', 'selected');
				}
			});
		}
		$('jclCatidSelectButton').addEvent('click', function(ev) {
			var selopt = $('catid').getElement(':selected');
			if (selopt) {
				id = d.getElementById('jform_canonical_id');
				task = d.getElementById('event-task');
				eid = d.getElementById('event-id');
				if (id.value != selopt.value) {
					oldvalue = id.value;
					id.value = selopt.value;
					catid.value = selopt.value;
					task.value = 'event.' + (eid.value ? 'edit' : 'add');
					form = d.getElementById('event-form');
					try {
						form.submit();
					}
					catch (err) {
						task.value = 'event.save';
						id.value = oldvalue;
						catid.value = oldvalue;
						alert(Joomla.JText._('COM_JCALPRO_CATSELECT_COULD_NOT_CHANGE'));
						window.parent.SqueezeBox.close();
					}
				}
				else {
					window.parent.SqueezeBox.close();
				}
			}
			else {
				alert(Joomla.JText._('COM_JCALPRO_CATSELECT_SELECT_ONE'));
			}
		});
		Array.each($('catid').getElements('option'), function(el, idx) {
			el.addEvent('dblclick', function(ev) {
				$('jclCatidSelectButton').fireEvent('click');
			});
		});
	});
})();