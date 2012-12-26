/**
 * @version		$Id: imagemanager.js 772 2012-04-17 19:21:09Z jeffchannell $
 * @package		JCalPro
 * @subpackage	com_jcalpro
@ant_copyright_header@
 */

(function(){
	window.addEvent('load', function() {
		if ('undefined' != typeof ImageManager) {
			// "fix" the ImageManager "setFolder" function
			ImageManager.setFolder = function(folder, asset, author) {
				for (var i = 0; i < this.folderlist.length; i++) {
					if(folder == this.folderlist.options[i].value) {
						this.folderlist.selectedIndex = i;
						break;
					}
				}
				this.frame.location.href = 'index.php?option=com_jcalpro&view=media&layout=list&tmpl=component&folder=' + folder + '&asset=' + asset + '&author=' + author;
			};
			// another "fix"
			ImageManager.getImageFolder = function() {
				var url    = this.frame.location.search.substring(1);
				var args   = this.parseQuery(url);
				var folder = args['folder'];
				return folder ? folder : '';
			};
		}
	});
})();
