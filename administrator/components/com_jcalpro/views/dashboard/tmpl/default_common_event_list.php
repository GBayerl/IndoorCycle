<?php
/**
 * @version		$Id: default_common_event_list.php 772 2012-04-17 19:21:09Z jeffchannell $
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

$i = 0;
?>
<table width="100%" cellpadding="2">
<?php foreach ($this->events as $item) : ?>
	<tr>
		<td>
			<?php echo ++$i; ?>
		</td>
		<td>
			<table style="table-layout:fixed;width:100%">
				<tr>
					<td>
						<div style="overflow-x:hidden">
							<a href="<?php echo JCalProHelperUrl::task('event.edit', false, array('id' => $item->id)); ?>"><?php
								echo JCalProHelperFilter::escape($item->title);
							?></a>
						</div>
					</td>
				</tr>
			</table>
		</td>
		<td style="text-align:center;width:125px">
			<?php
				echo JCalProHelperFilter::escape($item->minidisplay);
				if (isset($item->start_timedisplay)) :
					echo " " . JCalProHelperFilter::escape($item->start_timedisplay);
				endif;
			?>
		</td>
	</tr>
<?php endforeach; ?>
</table>