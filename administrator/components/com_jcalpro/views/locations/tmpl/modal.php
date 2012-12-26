<?php
/**
 * @version		$Id: modal.php 799 2012-08-08 22:56:32Z jeffchannell $
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

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');

$function  = JFactory::getApplication()->input->get('function', 'jclSelectLocation', 'cmd');
$listOrder = $this->state->get('list.ordering');
$listDirn  = $this->state->get('list.direction');
?>
<div id="jcl_component">
	<form action="<?php echo JRoute::_('index.php?option=com_jcalpro&view=locations&layout=modal&tmpl=component&function='.$function);?>" method="post" name="adminForm" id="adminForm">
		<fieldset class="filter clearfix">
			<div class="left">
				<label for="filter_search"><?php
					echo JText::_('JSEARCH_FILTER_LABEL');
				?></label>
				<input type="text" name="filter_search" id="filter_search" value="<?php echo JCalProHelperFilter::escape($this->state->get('filter.search')); ?>" size="30" title="<?php echo JText::_('COM_JCALPRO_FILTER_SEARCH_DESC'); ?>" />
				<button type="submit"><?php
					echo JText::_('JSEARCH_FILTER_SUBMIT');
				?></button>
				<button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php
					echo JText::_('JSEARCH_FILTER_CLEAR');
				?></button>
			</div>
<?php if (JFactory::getUser()->authorise('core.create', 'com_jcalpro.locations')) : ?>
			<div class="right toolbar-list">
				<ul>
					<li id="toolbar-new" class="button">
						<a class="toolbar" href="<?php echo JUri::base() . 'index.php?option=com_jcalpro&task=location.add&tmpl=component&function=' . $function;?>">
							<span class="icon-32-new"> </span>
							<?php echo JText::_('JTOOLBAR_NEW'); ?>
						</a>
					</li>
				</ul>
			</div>
<?php endif; ?>
		</fieldset>
		<table class="adminlist">
			<thead>
				<tr>
					<th>
						<?php echo JHtml::_('grid.sort', 'COM_JCALPRO_TITLE', 'Location.title', $listDirn, $listOrder); ?>
					</th>
					<th>
						<?php echo JHtml::_('grid.sort', 'COM_JCALPRO_LOCATION_ADDRESS', 'Location.address', $listDirn, $listOrder); ?>
					</th>
					<th>
						<?php echo JHtml::_('grid.sort', 'COM_JCALPRO_LOCATION_LATITUDE', 'Location.latitude', $listDirn, $listOrder); ?>
					</th>
					<th>
						<?php echo JHtml::_('grid.sort', 'COM_JCALPRO_LOCATION_LONGITUDE', 'Location.longitude', $listDirn, $listOrder); ?>
					</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="4">
						<?php echo $this->pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
			<tbody>
				<?php foreach ($this->items as $i => $item) : ?>
				<tr class="row<?php echo $i % 2; ?>">
					<td>
						<a class="pointer" onclick="if (window.parent) window.parent.<?php echo JCalProHelperFilter::escape($function);?>('<?php echo $item->id; ?>', '<?php echo JCalProHelperFilter::escape(addslashes($item->title)); ?>');"><?php
							echo JCalProHelperFilter::escape($item->title);
						?></a>
					</td>
					<td>
						<?php echo nl2br(JCalProHelperFilter::escape($item->address)); ?>
					</td>
					<td>
						<?php echo JCalProHelperFilter::escape($item->latitude); ?>
					</td>
					<td>
						<?php echo JCalProHelperFilter::escape($item->longitude); ?>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		<div>
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
			<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
			<?php echo JHtml::_('form.token'); ?>
		</div>
	</form>
</div>