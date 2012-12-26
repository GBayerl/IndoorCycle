<?php
/**
 * @version		$Id: default_category_events.php 827 2012-10-24 18:58:44Z jeffchannell $
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
<table class="jcl_table">
	<thead>
		<tr class="jcl_header">
			<th width="90%" nowrap="nowrap">
				<h3 class="jcl_header"><?php
					echo JText::_('COM_JCALPRO_CATEGORY_EVENT_NAME');
				?></h3>
			</th>
			<th align="center" nowrap="nowrap">
				<h3 class="jcl_header"><?php
					echo JText::_('COM_JCALPRO_DATE');
				?></h3>
			</th>
		</tr>
	</thead>
	<tbody>
<?php foreach ($this->items as $i => $item) : ?>
		<tr class="jcl_row jcl_row_<?php echo (0 == ($i + 1) % 2 ? 'even' : 'odd'); ?>">
			<td width="90%">
				<table class="jcl_month_inner_row">
					<tr>
						<td>
							<h3>
								<?php
									$title = $item->title;
									if ($this->title_limit) $title = JCalProHelperFilter::truncate($title, $this->title_limit);
									$title = JCalProHelperFilter::escape($title);
									
									if ($this->tpl) :
										echo $title;
									else :
										?><a href="<?php echo $item->href; ?>" class="eventtitle"><?php echo $title; ?></a><?php
									endif;
								?>
							</h3>
							<?php if ($this->show_description) : ?>
							<div class="jcl_event_description"><?php
								$description = $item->description;
								if ($this->description_limit) $description = JCalProHelperFilter::truncate($description, $this->description_limit);
								echo JCalProHelperFilter::purify($description);
							?></div>
							<?php endif; ?>
						</td>
					</tr>
				</table>
			</td>
			<td align="center" nowrap="nowrap"><?php
				if (JCalPro::JCL_EVENT_DURATION_ALL == (int) $item->duration_type) :
					echo $item->user_datedisplay;
				elseif (property_exists($item, 'user_end_minidisplay')) :
					echo $item->user_datedisplay . ' ';
					if ($item->user_datedisplay != $item->user_end_datedisplay) :
						echo $item->user_start_timedisplay . ' - ' . $item->user_end_datedisplay . ' ' . $item->user_end_timedisplay;
					else :
						echo $item->user_timedisplay;
					endif;
				else:
					echo $item->user_datedisplay . ' ' . $item->user_timedisplay;
				endif;
				?>
				<br /><?php echo $item->repeat_display; ?>
			</td>
		</tr>
<?php endforeach; ?>
<?php if (1 < $this->pagination->get('pages.total')) : ?>
		<tr class="jcal_categories">
			<td align="left">
				<div class="pagination"><?php echo $this->pagination->getPagesLinks(); ?></div>
			</td>
			<td align="right" colspan="2">
				<div class="pagination">
					<p class="counter"><?php echo $this->pagination->getPagesCounter(); ?></p>
				</div>
			</td>
		</tr>
<?php endif; ?>
	</tbody>
</table>
