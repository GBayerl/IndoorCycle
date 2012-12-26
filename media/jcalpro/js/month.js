/**
 * @version		$Id: month.js 805 2012-09-20 01:26:01Z jeffchannell $
 * @package		JCalPro
 * @subpackage	com_jcalpro
@ant_copyright_header@
 */

function jclMonthCell(el) {
	var inclass = '';
	if (JCalPro.hasClass(el, 'todayclr')   || JCalPro.hasClass(el, 'todayemptyclr'))   inclass = "todayhover";
	if (JCalPro.hasClass(el, 'weekdayclr') || JCalPro.hasClass(el, 'weekdayemptyclr')) inclass = "weekdayhover";
	if (JCalPro.hasClass(el, 'sundayclr')  || JCalPro.hasClass(el, 'sundayemptyclr'))  inclass = "sundayhover";
	if (0 < inclass.length) JCalPro.toggleClass(el, inclass);
	var img = JCalPro.getElement(el, '.jcl_month_add img');
	if (img) {
		var src = JCalPro.getAttribute(img, 'src');
		if (src.match(/addsign\.gif$/)) {
			JCalPro.setAttribute(img, 'src', src.replace(/addsign\.gif$/, 'addsign_a.gif'));
		}
		else {
			JCalPro.setAttribute(img, 'src', src.replace(/addsign_a\.gif$/, 'addsign.gif'));
		}
	}
	return true;
}
