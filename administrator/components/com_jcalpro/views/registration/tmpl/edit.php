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

$customFields = $this->form->getFieldset('customfields');
// this is kinda backwards :)
$customFieldsFormTitle = '';
$fieldsets = $this->form->getFieldsets();
foreach ($fieldsets as $fieldset) {
	if ('customfields' != $fieldset->name) continue;
	$customFieldsFormTitle = $fieldset->label;
	break;
}
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task) {
		if (task == 'registration.cancel' || document.formvalidator.isValid(document.id('registration-form'))) {
			Joomla.submitform(task, document.getElementById('registration-form'));
		}
		else {
			alert(Joomla.JText._('JGLOBAL_VALIDATION_FORM_FAILED'));
		}
	}
</script>
<div id="jcl_component" class="<?php echo $this->viewClass; ?>">
	<form action="<?php echo JRoute::_('index.php?option=com_jcalpro&task=registration.save&id=' . (int) $this->item->id); ?>" method="post" id="registration-form" name="adminForm" class="form-validate">
		<div class="width-60 fltlft">
			<?php foreach ($this->form->getFieldsets() as $fieldset): ?>
				<?php
				if ('customfields' == $fieldset->name) continue;
				if ('hidden' == $fieldset->name) :
					?><div><?php
					foreach ($this->form->getFieldset($fieldset->name) as $name => $field) :
						echo $field->input;
					endforeach;
					?></div><?php
				else : ?>
			<fieldset class="adminform">
				<legend><?php echo JText::_($fieldset->label); ?></legend>
				<ul class="adminformlist">
				<?php foreach ($this->form->getFieldset($fieldset->name) as $name => $field): ?>
					<li class="jcl_form_label"><?php echo $field->label; ?></li>
					<li class="jcl_form_input"><?php echo $field->input; ?></li>
				<?php endforeach; ?>
				</ul>
			</fieldset>
				<?php endif; ?>
			<?php endforeach; ?>
		</div>
		<?php if (!empty($customFields)) : ?>
		<div class="width-40 fltlft">
			<fieldset class="adminform">
				<legend><?php echo JText::_($customFieldsFormTitle); ?></legend>
				<ul class="adminformlist">
				<?php foreach ($customFields as $name => $field): ?>
					<li class="jcl_form_label"><?php echo $field->label; ?></li>
					<li class="jcl_form_input"><?php echo $field->input; ?></li>
				<?php endforeach; ?>
				</ul>
			</fieldset>
		</div>
		<?php endif; ?>
		<div class="clr">
			<input type="hidden" id="registration-task" name="task" value="" />
			<?php echo JHtml::_('form.token'); ?>
		</div>
	</form>
</div>

<?php echo $this->loadTemplate('debug'); ?>