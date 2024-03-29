<?php
/**
 * @version		$Id: default_head.php 822 2012-10-22 23:06:00Z jeffchannell $
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

$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
$saveOrder = ($listOrder == 'Registration.id');
?>
<tr>
	<th width="1%" class="nowrap hidden-phone">
		<?php echo JText::_('COM_JCALPRO_ID'); ?>
	</th>
	<th width="1%" class="nowrap hidden-phone">
		<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>);" />
	</th>
	<th>
		<?php echo JHtml::_('grid.sort', 'COM_JCALPRO_ALTNAME', 'Registration.altname', $listDirn, $listOrder); ?>
	</th>
	<th width="10%">
		<?php echo JHtml::_('grid.sort', 'COM_JCALPRO_EVENT', 'Registration.event_id', $listDirn, $listOrder); ?>
	</th>
	<th width="10%" class="nowrap hidden-phone">
		<?php echo JHtml::_('grid.sort', 'COM_JCALPRO_START_DATE', 'Event.start_date', $listDirn, $listOrder); ?>
	</th>
	<th width="10%" class="nowrap hidden-phone">
		<?php echo JHtml::_('grid.sort', 'COM_JCALPRO_CREATED_BY', 'Registration.created_by', $listDirn, $listOrder); ?>
	</th>
	<th width="1%">
		<?php echo JHtml::_('grid.sort', 'COM_JCALPRO_CONFIRMED', 'Registration.published', $listDirn, $listOrder); ?>
	</th>
</tr>