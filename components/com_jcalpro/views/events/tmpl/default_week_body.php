<?php
/**
 * @version		$Id: default_week_body.php 822 2012-10-22 23:06:00Z jeffchannell $
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

$showEmpty = JCalPro::config('week_empty_days', 1);
$eventsThisWeek = false;
$i = 0;
foreach ($this->items as $day => $stack) :
	$i++;
	$hasEvents = !empty($stack['events']);
	
	if ($hasEvents || $showEmpty) :	
?>
	<h2 class="jcl_header"><a id="w<?php echo $i; ?>" name="w<?php echo $i; ?>"><!--  --></a><?php
		echo JCalProHelperFilter::escape($stack['user_datetime']->format(JText::_('COM_JCALPRO_DATE_FORMAT_FULL_DATE')));
	?></h2>
<?php
		// render events
		if ($hasEvents) :
			// set this for later :)
			$eventsThisWeek = true;
			// loop
			foreach ($stack['events'] as $event) :
?>
<div class="jcl_row jcl_row_<?php echo (0 == $i % 2 ? 'even' : 'odd'); ?>">
	<div class="jcl_event_body jcl_nooverflow" style="border-left-color: <?php echo @$event->color; ?>;">
		<h3>
			<?php
				$title = $event->title;
				if ($this->title_limit) $title = JCalProHelperFilter::truncate($title, $this->title_limit);
				$title = JCalProHelperFilter::escape($title);
				
				if ($this->tpl) :
					echo $title; ?> (<?php echo $event->user_month_datetime_display; ?>)<?php
				else :
					?><a href="<?php echo $event->href; ?>" class="eventtitle noajax"><?php echo $title; ?> (<?php echo $event->user_month_datetime_display; ?>)</a><?php
				endif;
			?>
		</h3>
		<?php if ($this->show_description) : ?>
		<div class="jcl_event_description"><?php
			$description = $event->description;
			if ($this->description_limit) $description = JCalProHelperFilter::truncate($description, $this->description_limit);
			echo JCalProHelperFilter::purify($description);
		?></div>
		<?php endif; ?>
	</div>
</div>
<?php
			endforeach;
		elseif ($showEmpty) :
			// no events on this day
					
?>
<div class="jcl_row">
	<div class="jcl_message"><?php echo JText::_('COM_JCALPRO_DAY_NO_EVENTS'); ?></div>
</div>
<?php
		endif;
	endif;
endforeach;
?>
<?php
// no events this week?
if (!$eventsThisWeek && !$showEmpty) :
?>
<div class="jcl_row">
	<div class="jcl_message"><?php echo JText::_('COM_JCALPRO_WEEK_NO_EVENTS'); ?></div>
</div>
<?php endif; ?>
<?php if (!$this->tpl) echo $this->loadTemplate('categories'); ?>