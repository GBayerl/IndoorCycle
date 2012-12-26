/**
 * @version		$Id: categories.js 811 2012-10-09 15:51:00Z jeffchannell $
 * @package		JCalPro
 * @subpackage	com_jcalpro
@ant_copyright_header@
 */

(function() {
	JCalPro.onLoad(function() {
		var url = 'index.php?option=com_jcalpro&task=event.catcounts', form = JCalPro.id('adminForm');
		if (!form) return;
		// here's the new th to go after the title
		var th1 = new Element('th', {
			text: Joomla.JText._('COM_JCALPRO_TOTAL_EVENTS')
		,	width: '5%'
		});
		var th2 = new Element('th', {
			text: Joomla.JText._('COM_JCALPRO_UPCOMING_EVENTS')
		,	width: '5%'
		});
		var old = true;
		try {
			th1.inject(form.getElement('.adminlist').getElement('th').getNext(), 'after');
			th2.inject(th1, 'after');
			form.getElement('.adminlist').getElement('tfoot').getElement('td').set('colspan', 17);
		}
		catch (err) {
			old = false;
			th1.inject(JCalPro.id('categoryList').getElement('th').getNext().getNext().getNext(), 'after');
			th2.inject(th1, 'after');
			JCalPro.id('categoryList').getElement('tfoot').getElement('td').set('colspan', 17);
		}
		JCalPro.each(form.getElements('input[type=checkbox]'), function(el, idx) {
			var elid = el.id;
			if (!elid.match(/^cb[0-9]+/)) return;
			url += '&catids[]=' + el.value;
			var td1 = new Element('td', {id: 'jcal_category_total_' + el.value, align: 'center'});
			var td2 = new Element('td', {id: 'jcal_category_upcoming_' + el.value, align: 'center'});
			td1.inject(old ? el.getParent().getNext() : el.getParent().getNext().getNext(), 'after');
			td2.inject(td1, 'after');
		});
		var r = new Request.JSON({
			url: url
		,	format: 'json'
		,	onSuccess: function(responseJSON, responseText) {
				if (!responseJSON || !responseJSON.categories || !responseJSON.categories.length) return;
				var i = 0, cid;
				for (; i<responseJSON.categories.length; i++) {
					cid = responseJSON.categories[i].id;
					$('jcal_category_total_' + cid).set('text', parseInt(responseJSON.categories[i].total_events, 10));
					$('jcal_category_upcoming_' + cid).set('text', parseInt(responseJSON.categories[i].upcoming_events, 10));
					$('jcal_category_total_' + cid).getParent().getElement('td').setStyle('background-color', responseJSON.categories[i].color);
				}
			}
		}).send();
	});
})();