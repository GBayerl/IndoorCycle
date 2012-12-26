/**
 * @version		$Id: formfield.js 772 2012-04-17 19:21:09Z jeffchannell $
 * @package		JCalPro
 * @subpackage	com_jcalpro
@ant_copyright_header@
 */


(function(){
	window.addEvent('load', function() {
		// we want this to run for each instance
		// it's doubtful there will be more than one ever,
		// but it's always something to consider
		var fields = $(document.body).getElements('.jcalformfields');
		if (!fields) {
			alert(Joomla.JText._('COM_JCALPRO_JCALFORMFIELD_ERROR'));
			return;
		}
		// loop over each of our fields (there should only be one, but hey! who knows?)
		for (var i=0; i<fields.length; i++) {
			var field = fields[i];
			// the input for this field
			var input = field.getElement('.jcalformfieldsinput');
			// sortable elements
			var sortables = field.getElements('.jcalformfieldssortable');
			// check our elements to ensure we have them!
			if (!sortables) {
				alert(Joomla.JText._('COM_JCALPRO_JCALFORMFIELD_NOSORTABLE'));
				return;
			}
			// add sortable to our lists
			new Sortables(sortables, {
				clone: false // using clone breaks the onComplete :(
			,	revert: {duration: 500, transition: 'elastic:out'}
			,	opacity: 0.7
			,	constrain: false
				// this event fires to ensure the actual JForm element gets populated
			,	onComplete: function(e) {
					// start our new value
					var val = [];
					// loop the found inputs & push to our new value
					Array.each(field.getElement('.jcalformfieldsassigned').getElements('input'), function(el, idx) {
						if (el.getParent().isDisplayed() && !val.contains(el.value)) val.push(el.value);
					});
					// reset the main input value
					input.value = val.join('|');
				}
			});
		}
	});
})();
