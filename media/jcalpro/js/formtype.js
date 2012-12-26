/**
 * @version		$Id: formtype.js 772 2012-04-17 19:21:09Z jeffchannell $
 * @package		JCalPro
 * @subpackage	com_jcalpro
@ant_copyright_header@
 */

(function(){
	window.addEvent('load', function() {
		var switchers = $(document.body).getElements('.jcalformtype');
		if (switchers) {
			Array.each(switchers, function(switcher, idx) {
				switcher.addEvent('change', function(ev) {
					var hidden, shown;
					switch (this.getElement(':selected').value) {
						case '0':
							hidden = '.jcalformfieldformtyperegistration';
							shown  = '.jcalformfieldformtypeevent';
							break;
						case '1':
							hidden = '.jcalformfieldformtypeevent';
							shown  = '.jcalformfieldformtyperegistration';
							break;
						default:
							shown = '.jcalformfieldformtypeevent, .jcalformfieldformtyperegistration';
					}
					if (hidden) {
						Array.each($(document.body).getElements(hidden), function(el, i) {
							el.setStyle('display', 'none');
						});
					}
					if (shown) {
						Array.each($(document.body).getElements(shown), function(el, i) {
							el.setStyle('display', 'block');
						});
					}
				});
				switcher.fireEvent('change');
			});
		}
	});
})();
