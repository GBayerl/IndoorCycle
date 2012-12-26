<?php
/**
 * @version		$Id: default_category.php 772 2012-04-17 19:21:09Z jeffchannell $
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
<div class="jcl_subtitlebar">
	<div class="jcl_left"><?php echo $this->linkdata['current']; ?></div>
	<div class="jcl_right"><?php echo JCalProHelperDate::getToday()->format(JText::_('COM_JCALPRO_DATE_FORMAT_FULL_DATE')); ?></div>
	<div class="jcl_clear"><!--  --></div>
</div>
<?php if ($this->show_description || 1 == $this->category->params->get('jcalpro_category_description')) : ?>
<div class="jcal_categories">
	<?php echo $this->category->description; ?>
</div>
<?php
endif;

echo $this->loadTemplate('category_' . (empty($this->items) ? 'empty' : 'events'));
