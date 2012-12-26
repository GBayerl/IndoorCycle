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

$formFieldset   = $this->form->getFieldset('form');
$fieldsFieldset = $this->form->getFieldset('form_fields');
$hiddenFieldset = $this->form->getFieldset('hidden');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task) {
		if (task == 'form.cancel' || document.formvalidator.isValid(document.id('form-form'))) {
			Joomla.submitform(task, document.getElementById('form-form'));
		}
		else {
			alert(Joomla.JText._('JGLOBAL_VALIDATION_FORM_FAILED'));
		}
	}
</script>
<div id="jcl_component" class="<?php echo $this->viewClass; ?>">
	<form action="<?php echo JRoute::_('index.php?option=com_jcalpro&task=form.save&id=' . (int) $this->item->id); ?>" method="post" id="form-form" name="adminForm" class="form-validate">
		<div class="width-40 fltlft">
			<fieldset class="adminform">
				<legend><?php echo JText::_('COM_JCALPRO_FORM'); ?></legend>
				<ul class="adminformlist">
				<?php foreach ($formFieldset as $name => $field): ?>
					<li class="jcl_form_label"><?php echo $field->label; ?></li>
					<li class="jcl_form_input"><?php echo $field->input; ?></li>
				<?php endforeach; ?>
				</ul>
			</fieldset>
		</div>
		<div class="width-60 fltlft">
			<fieldset class="adminform">
				<legend><?php echo JText::_('COM_JCALPRO_FORM_FIELDS'); ?></legend>
				<ul class="adminformlist">
				<?php foreach ($fieldsFieldset as $name => $field): ?>
					<li class="jcl_form_label"><?php echo $field->label; ?></li>
					<li class="jcl_form_input"><?php echo $field->input; ?></li>
				<?php endforeach; ?>
				</ul>
			</fieldset>
		</div>
		<div>
			<?php foreach ($hiddenFieldset as $name => $field) echo $field->input; ?>
			<input type="hidden" name="task" value="" />
			<?php echo JHtml::_('form.token'); ?>
		</div>
	</form>
</div>

<?php echo $this->loadTemplate('debug'); ?>