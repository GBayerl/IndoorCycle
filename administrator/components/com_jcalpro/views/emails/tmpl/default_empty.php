<?php
/**
 * @version		$Id: default_empty.php 805 2012-09-20 01:26:01Z jeffchannell $
 * @package		JCalPro
 * @subpackage	com_jcalpro

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

defined('JPATH_PLATFORM') or die;

?>
<div class="jcal-empty">
	<h3><?php echo JText::_('COM_JCALPRO_EMAILS_EMPTY'); ?></h3>
	<div class="jcal-empty-button">
		<div class="icon-wrapper">
			<div class="icon">
				<a href="<?php echo JCalProHelperUrl::task('email.add'); ?>">
					<?php echo JHTML::_('jcalpro.image', 'icon-48-emails.png'); ?><br />
					<span><?php echo JText::_('COM_JCALPRO_EMAIL_ADD_NEW'); ?></span></a>
			</div>
		</div>
	</div>
</div>
