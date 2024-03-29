<?php
/**
 * @version		$Id: default.php 805 2012-09-20 01:26:01Z jeffchannell $
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

JHtml::_('behavior.tooltip');
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
$this->cols = 7;
?>
<div id="jcl_component" class="<?php echo $this->viewClass; ?>">
	<form action="<?php echo JCalProHelperUrl::_(array('view'=>'fields')); ?>" method="post" name="adminForm" id="adminForm">
		<div class="row-fluid">
			<?php
				echo $this->loadTemplate('filters');
				if (empty($this->items)) :
					echo $this->loadTemplate('empty');
				else :
			?>
			<table class="adminlist table table-striped">
				<thead><?php echo $this->loadTemplate('head');?></thead>
				<tfoot><?php echo $this->loadTemplate('foot');?></tfoot>
				<tbody><?php echo $this->loadTemplate('body');?></tbody>
			</table>
			<?php endif; ?>
			<div>
				<input type="hidden" name="task" value="" />
				<input type="hidden" name="boxchecked" value="0" />
				<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
				<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
				<?php echo JHtml::_('form.token'); ?>
			</div>
		</div>
	</form>
</div>

<?php echo $this->loadTemplate('debug'); ?>