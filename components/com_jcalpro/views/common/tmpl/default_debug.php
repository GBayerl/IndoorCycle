<?php
/**
 * @version		$Id: default_debug.php 776 2012-04-26 20:23:13Z jeffchannell $
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

if (!property_exists($this, 'raw')) $this->raw = false;
if (!property_exists($this, 'tpl')) $this->tpl = false;
$isAdmin = JFactory::getApplication()->isAdmin();

if (JDEBUG && !$this->raw && !$this->tpl) :
	JFactory::getDocument()->addStyleSheet(JCalProHelperUrl::media() . '/css/debug.css');
	?><div id="jcl_debug"<?php if ($isAdmin) : ?> class="width-100 jcl_clear"><br /><hr />
	<fieldset class="adminform">
		<legend><?php echo JText::_('COM_JCALPRO_DEBUG_INFORMATION'); ?></legend<?php endif; ?>>
	<?php
	$debug = JCalPro::debugger();
	if (!empty($debug)) :
		echo JHtml::_('sliders.start', 'jcldebug');
		$i = 0;
		foreach ($debug as $key => $value) :
			echo JHtml::_('sliders.panel', $this->escape($key), 'debug-' . $i++);
			JCalPro::debug($value);
		endforeach;
		echo JHtml::_('sliders.end');
	endif;
	if ($isAdmin) : ?></fieldset><?php endif; ?>
	</div><?php
endif;
