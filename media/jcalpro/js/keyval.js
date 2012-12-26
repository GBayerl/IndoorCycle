/**
 * @version		$Id: keyval.js 772 2012-04-17 19:21:09Z jeffchannell $
 * @package		JCalPro
 * @subpackage	com_jcalpro
@ant_copyright_header@
 */

(function(){
	var resetButtons, addF, subF, upF, downF, moveF;
	// we have to have a function to reset the buttons
	// this is because mootools doesn't have a live() event handler
	resetButtons = function(elem) {
		var addButtons  = elem.getElements('.jcalkeyval_add');
		var subButtons  = elem.getElements('.jcalkeyval_sub');
		var upButtons   = elem.getElements('.jcalkeyval_up');
		var downButtons = elem.getElements('.jcalkeyval_down');
		if (addButtons) {
			addButtons.each(function(el) {
				el.removeEvent('click');
				el.addEvent('click', addF);
			});
		}
		if (subButtons) {
			subButtons.each(function(el) {
				el.removeEvent('click');
				el.addEvent('click', subF);
			});
		}
		if (upButtons) {
			upButtons.each(function(el, idx) {
				el.removeProperty('disabled');
				el.removeEvent('click');
				el.addEvent('click', upF);
				// we have to use index 1 and not 0 as the hidden block is 0
				if (1 == idx) el.set('disabled', 'disabled');
			});
		}
		if (downButtons) {
			downButtons.each(function(el, idx) {
				el.removeProperty('disabled');
				el.removeEvent('click');
				el.addEvent('click', downF);
				if (downButtons.length - 1 == idx) el.set('disabled', 'disabled');
			});
		}
	};
	addF = function(ev) {
		var hasEmpty = false, p, t, b, inputBlocks, range, documentFragment;
		try {
			// get the main parent block
			p = $(ev.target.parentNode.parentNode.parentNode);
			// the template for adding a new block
			t = p.getElement('.jcalkeyval_default');
			// the main block
			b = p.getElement('.jcalkeyval_stage');
			// check our inputs to see if we have empties
			// note that both key AND value must be empty!
			inputBlocks = b.getElements('.jcalkeyval_inputs');
			if (inputBlocks) {
				inputBlocks.each(function(el) {
					if (hasEmpty) return;
					var i = el.getElements('input');
					if ('' === i[0].value && '' === i[1].value) hasEmpty = true;
				});
			}
			if (hasEmpty) {
				alert(Joomla.JText._('COM_JCALPRO_JCALKEYVAL_EMPTY'));
				return false;
			}
			// create a new range to append
			range = document.createRange();
			range.selectNode(t);
			documentFragment = range.createContextualFragment(t.innerHTML.toString());
			b.appendChild(documentFragment);
			resetButtons(p);
		} catch (err) {
			alert(err);
			return false;
		}
	};
	subF = function(ev) {
		var hasEmpty = false, p, t, b, inputBlocks, range, documentFragment;
		try {
			// get the main parent block
			p = $(ev.target.parentNode);
			if (p) {
				var c = p.getParent().getChildren();
				if (1 < c.length) {
					p.destroy();
				}
				else {
					alert(Joomla.JText._('COM_JCALPRO_JCALKEYVAL_EMPTY_REMOVE'));
				}
			}
		} catch (err) {
			alert(err);
			return false;
		}
	};
	moveF = function(ev, dir) {
		try {
			// parent block, target sibling
			var p = $(ev.target.parentNode), t;
			switch (dir) {
				case 'before':
					t = p.getPrevious('.jcalkeyval_block');
					break;
				case 'after':
					t = p.getNext('.jcalkeyval_block');
					break;
				default: return false;
			}
			if (t) {
				p.inject(t, dir);
				resetButtons($(ev.target.parentNode.parentNode.parentNode));
				return true;
			}
			return false;
		}
		catch (err) {
			return false;
		}
	};
	upF = function(ev) {
		return moveF(ev, 'before');
	};
	downF = function(ev) {
		return moveF(ev, 'after');
	};
	// everything starts here
	window.addEvent('load', function() {
		// we want this to run for each instance
		var fields = $(document.body).getElements('.jcalkeyval');
		if (!fields) {
			alert(Joomla.JText._('COM_JCALPRO_JCALKEYVAL_ERROR'));
			return;
		}
		// loop over each of our fields (there should only be one, but hey! who knows?)
		for (var i=0; i<fields.length; i++) {
			resetButtons(fields[i]);
		}
	});
})();