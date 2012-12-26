<?php
/**
 * @version		$Id: default.php 834 2012-11-13 17:58:50Z jeffchannell $
 * @package		JCalPro
 * @subpackage	mod_jcalpro_events

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
<div class="jcalpro_events<?php echo $moduleclass_sfx; ?>">
	<ul class="jcalpro_events<?php echo $moduleclass_sfx; ?>">
	<?php foreach ($list as $item) :  ?>
		<li>
			<span class="jcalpro_events_link">
				<a href="<?php echo JCalProHelperUrl::event($item->id, true, $urlparams); ?>" title="<?php echo JCalProHelperFilter::escape($item->title); ?>"><?php
					if ($limit_title) :
						echo JCalProHelperFilter::escape(JCalProHelperFilter::truncate($item->title, $limit_title));
					else :
						echo JCalProHelperFilter::escape($item->title);
					endif;
				?></a>
			</span>
			<?php if ($display_date || $display_time) : ?>
			<span class="jcalpro_events_date"><?php
				
				if ($display_date) echo $item->mod_events_date;
				$timedisplay = '';
				switch ((int) $item->duration_type) :
					case JCalPro::JCL_EVENT_DURATION_ALL:
						echo ' (' . JText::_('COM_JCALPRO_ALL_DAY') . ')';
						break;
					default:
						switch ($display_time) :
							case 2: echo ' (' . $item->user_start_timedisplay . ')'; break;
							case 1: echo ' (' . $item->user_timedisplay . ')'; break;
							default: break;
						endswitch;
				endswitch;
				
			?></span>
			<?php endif; ?>
			<?php if ((int) $params->get('display_category', 1)) : ?>
			<span class="jcalpro_events_category">
				<a href="<?php
				echo JCalProHelperUrl::category($item->categories->canonical->id, true, $urlparams);
				?>"><?php echo JCalProHelperFilter::escape($item->categories->canonical->title); ?></a>
			</span>
			<?php endif; ?>
			<?php if ((int) $params->get('display_description', 1)) : ?>
			<span class="jcalpro_events_description"><?php
				$description = "{$item->description}";
				if ($filter_description) :
					$description = strip_tags($description);
				endif;
				if ($limit_description) :
					$description = JCalProHelperFilter::truncate($description, $limit_description);
				endif;
				echo $description;
			?></span>
			<?php endif; ?>
		</li>
	<?php endforeach; ?>
	</ul>
	<?php if (JCalPro::canAddEvents() && (int) $params->get('display_add', 1)) : ?>
	<span class="jcalpro_events_add">
		<a href="<?php echo JCalProHelperUrl::task('event.add', true, $urlparams); ?>" title="<?php echo JCalProHelperFilter::escape(JText::_('MOD_JCALPRO_EVENTS_DISPLAY_ADD_TEXT')); ?>"><?php
			echo JCalProHelperFilter::escape(JText::_('MOD_JCALPRO_EVENTS_DISPLAY_ADD_TEXT'));
		?></a>
	</span>
	<?php endif; ?>
	<?php if ((int) $params->get('display_events_link', 1)) : ?>
	<span class="jcalpro_events_link">
		<a href="<?php echo JCalProHelperUrl::events('', 'month', true, $urlparams); ?>" title="<?php echo JCalProHelperFilter::escape(JText::_('MOD_JCALPRO_EVENTS_DISPLAY_EVENTS_LINK_TEXT')); ?>"><?php
			echo JCalProHelperFilter::escape(JText::_('MOD_JCALPRO_EVENTS_DISPLAY_EVENTS_LINK_TEXT'));
		?></a>
	</span>
	<?php endif; ?>
	<?php if (defined('JDEBUG') && JDEBUG) : JCalProHelperTheme::addStyleSheet('module_debug'); ?>
	<div class="jcalpro_module_debug">
		<h3>$module</h3>
		<?php JCalPro::debug($module); ?>
		<h3>$params</h3>
		<?php JCalPro::debug($params); ?>
	</div>
	<?php endif; ?>
</div>