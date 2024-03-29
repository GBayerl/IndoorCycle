<?php
/**
 * @version		$Id: default.php 830 2012-10-25 20:52:20Z jeffchannell $
 * @package		JCalPro
 * @subpackage	mod_jcalpro_flex

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
<div class="jcalpro_flex<?php echo $moduleclass_sfx; ?>">
<?php
if ($panes) :
	echo JHtml::_($layout . '.start', 'flex-' . $layout, array('useCookie'=>1));
else :
	?><ul><?php
endif;

foreach ($params->get('panels') as $name => $panel) :
	if (empty($panel->panel_title)) continue;
	switch ($panel->panel_type) :
		case 'events':
		case 'calendar':
			if ($panes) :
				echo JHtml::_($layout . '.panel', $panel->panel_title, 'flex-' . $layout . '-' . JCalProHelperFilter::escape(JApplication::stringURLSafe($panel->panel_title)));
			else :
				?><li class="jcalpro_flex_pane">
				<h4><?php echo JCalProHelperFilter::escape($panel->panel_title); ?></h4><?php
			endif;
			echo modJCalProFlexHelper::panel($panel);
			if (!$panes) :
				?></li><?php
			endif;
	endswitch;
endforeach;

if ($panes) :
	echo JHtml::_($layout . '.end');
else :
	?></ul><?php
endif;
?>

	<?php if (defined('JDEBUG') && JDEBUG) : JCalProHelperTheme::addStyleSheet('module_debug'); ?>
	<div class="jcalpro_module_debug">
		<h3>$module</h3>
		<?php JCalPro::debug($module); ?>
		<h3>$params</h3>
		<?php JCalPro::debug($params); ?>
	</div>
	<?php endif; ?>
</div>
