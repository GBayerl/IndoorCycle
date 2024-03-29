<?php
/**
 * @version		$Id: edit.php 807 2012-10-02 18:53:43Z jeffchannell $
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
JHtml::_('behavior.formvalidation');

JText::script('JGLOBAL_VALIDATION_FORM_FAILED');

$input = JFactory::getApplication()->input;

$locationFieldset = $this->form->getFieldset('location');
$hiddenFieldset   = $this->form->getFieldset('hidden');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task) {
		if (task == 'location.cancel') {
			Joomla.submitform(task, document.id('location-form'));
		}
		else if (document.formvalidator.isValid(document.id('location-form'))) {
			try {
				jcl_map_refresh('jform_address');
			}
			catch (err) {
				alert(err);
				return false;
			}
			// this is gross, but we need to allow a little time to let the map refresh
			setTimeout(function() {
				Joomla.submitform(task, document.id('location-form'));
			}, 1000);
		}
		else {
			alert(Joomla.JText._('JGLOBAL_VALIDATION_FORM_FAILED'));
		}
	}
</script>
<div id="jcl_component" class="<?php echo $this->viewClass; ?>">
	<form action="<?php echo JURI::base() . 'index.php?option=com_jcalpro&task=location.save&id=' . (int) $this->item->id; ?>" method="post" id="location-form" name="adminForm" class="form-validate">
<?php if ('component' == $input->get('tmpl', '', 'cmd')) : ?>
		<div class="right toolbar-list">
			<ul>
				<li id="toolbar-apply" class="button">
					<a class="toolbar" onclick="Joomla.submitbutton('location.apply');return false;" href="#">
						<span class="icon-32-apply"> </span>
						<?php echo JText::_('JTOOLBAR_APPLY'); ?>
					</a>
				</li>
				<li id="toolbar-save" class="button">
					<a class="toolbar" onclick="Joomla.submitbutton('location.save');return false;" href="#">
						<span class="icon-32-save"> </span>
						<?php echo JText::_('JTOOLBAR_SAVE'); ?>
					</a>
				</li>
				<li id="toolbar-cancel" class="button">
					<a class="toolbar" onclick="Joomla.submitbutton('location.cancel');return false;" href="#">
						<span class="icon-32-cancel"> </span>
						<?php echo JText::_('JTOOLBAR_CANCEL'); ?>
					</a>
				</li>
			</ul>
		</div>
		<div class="jcl_clear"><!--  --></div>
<?php endif; ?>
		<div class="width-40 fltlft">
			<fieldset class="adminform">
				<legend><?php echo JText::_('COM_JCALPRO_LOCATION'); ?></legend>
				<ul class="adminformlist">
				<?php foreach ($locationFieldset as $name => $field): ?>
					<li class="jcl_form_label"><?php echo $field->label; ?></li>
					<li class="jcl_form_input"><?php echo $field->input; ?></li>
				<?php endforeach; ?>
				</ul>
			</fieldset>
		</div>
		<div class="width-60 fltlft">
			<fieldset class="adminform">
				<legend><?php echo JText::_('COM_JCALPRO_MAP'); ?></legend>
				<div id="map_canvas_container">
					<div id="map_canvas"><?php echo JText::_('COM_JCALPRO_LOCATION_LOADING_MAP'); ?></div>
				</div>
			</fieldset>
		</div>
		<div>
			<?php foreach ($hiddenFieldset as $name => $field) echo $field->input; ?>
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="function" value="<?php echo $input->get('function', '', 'cmd'); ?>" />
			<?php if ('component' == $input->get('tmpl', '', 'cmd')) : ?>
			<input type="hidden" name="tmpl" value="component" />
			<input type="hidden" name="mlayout" value="modal" />
			<?php endif; ?>
			<?php echo JHtml::_('form.token'); ?>
		</div>
	</form>
</div>

<?php echo $this->loadTemplate('debug'); ?>