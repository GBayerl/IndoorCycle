<?php
/**
 * @version		$Id: edit.php 811 2012-10-09 15:51:00Z jeffchannell $
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

JText::script('COM_JCALPRO_VALIDATION_FORM_FAILED');
JText::script('COM_JCALPRO_VALIDATION_JFORM_TITLE_FAILED');
JText::script('COM_JCALPRO_VALIDATION_JFORM_CANONICAL_ID_FAILED');
JText::script('COM_JCALPRO_INVALID_DATE');

// show recurrence warnings
$showRecurrence = true;
if ($this->item->id && $this->item->rec_id && !$this->item->detached_from_rec) {
	$showRecurrence = false;
	$warning = JText::_('COM_JCALPRO_EVENT_CHILD_DETACHED_WARNING');
	if (!$this->item->detached_from_rec) {
		$warning = JText::sprintf('COM_JCALPRO_EVENT_CHILD_NOT_DETACHED_WARNING', JCalProHelperUrl::task('event.edit', false, array('id'=>$this->item->rec_id)));
	}
	JFactory::getApplication()->enqueuemessage($warning, 'warning');
}

$detailsForm      = $this->form->getFieldset('event');
$adminForm        = $this->form->getFieldset('admin');
$hiddenForm       = $this->form->getFieldset('hidden');
$repeatForm       = $this->form->getFieldset('repeat');
$contactForm      = $this->form->getFieldset('contact');
$durationForm     = $this->form->getFieldset('duration');
$startDateForm    = $this->form->getFieldset('startdate');
$registrationForm = $this->form->getFieldset('registration');
$customfieldsForm = $this->form->getFieldset('customfields');
$nonextra = array('admin', 'event', 'hidden', 'repeat', 'contact', 'duration', 'startdate', 'registration', 'customfields');
// this is kinda backwards :)
$customfieldsFormTitle = '';
$fieldsets = $this->form->getFieldsets();
foreach ($fieldsets as $fieldset) {
	if ('customfields' != $fieldset->name) continue;
	$customfieldsFormTitle = $fieldset->label;
	break;
}
// permissions
$canonical = null;
$formcanonical = $this->form->getValue('canonical');
if (!empty($formcanonical)) $canonical = $formcanonical;
$canCreatePrivate = JCalPro::canDo('core.create.private', $canonical);
$canCreatePublic  = JCalPro::canDo('core.create', $canonical);
$canModerate      = JCalPro::canDo('core.moderate', $canonical);
$canEditState     = JCalPro::canDo('core.edit.state', $canonical);
?>
<script type="text/javascript">
	window.jclAcl = {
		moderate: <?php echo (int) $canModerate; ?>
	,	createPrivate: <?php echo (int) $canCreatePrivate; ?>
	,	createPublic: <?php echo (int) $canCreatePublic; ?>
	,	editState: <?php echo (int) $canEditState; ?>
	};
	Joomla.submitbutton = function(task) {
		if (task == 'event.cancel' || document.formvalidator.isValid(document.id('event-form'))) {
			try {
				<?php echo $this->form->getField('description')->save(); ?>
			}
			catch (err) {
				// tinyMCE not in use
			}
			Joomla.submitform(task, document.getElementById('event-form'));
		}
		else {
			var fields = ['jform_title', 'jform_canonical_id'], found = false;
			Array.each(fields, function(el, idx) {
				if (found) return;
				if ('' == $(el).value) {
					found = true;
					alert(Joomla.JText._('COM_JCALPRO_VALIDATION_' + el.toUpperCase() + '_FAILED'));
				}
			});
			if (!found) alert(Joomla.JText._('COM_JCALPRO_VALIDATION_FORM_FAILED'));
		}
	}
</script>
<div id="jcl_component" class="<?php echo $this->viewClass; ?>">
	<form action="<?php echo JRoute::_('index.php?option=com_jcalpro&task=event.save&id=' . (int) $this->item->id); ?>" method="post" id="event-form" name="adminForm" class="form-validate">
		<div class="width-60 fltlft">
			<fieldset class="adminform">
				<legend><?php echo JText::_('COM_JCALPRO_EVENT_DETAILS'); ?></legend>
				<ul class="adminformlist">
				<?php foreach ($detailsForm as $name => $field): ?>
					<li class="jcl_form_label"><?php echo $field->label; ?></li>
					<li class="jcl_form_input"<?php if (in_array($field->name, array('jform[description]', 'jform[cat]'))) echo ' style="clear:left"' ?>><?php echo $field->input; ?></li>
				<?php endforeach; ?>
				</ul>
			</fieldset>
		</div>
		<div class="width-40 fltrt">
			<fieldset class="adminform">
				<legend><?php
					echo JText::_('COM_JCALPRO_EVENT_DATE');
				?></legend>
				<ul class="adminformlist">
					<li class="jcl_form_label"><?php
						echo JText::_('COM_JCALPRO_START_TIME');
					?></li>
					<li class="jcalnofloat"><?php
						echo $startDateForm['jform_start_date_array']->input;
					?></li>
					<li class="jcl_form_label"><?php
						echo JText::_('COM_JCALPRO_TIMEZONE');
					?></li>
					<li class="jcalnofloat"><?php
						echo $startDateForm['jform_timezone']->input;
					?></li>
					<li class="jcl_form_label"><?php
						echo JText::_('COM_JCALPRO_DURATION');
					?></li>
					<li><?php
						printf($durationForm['jform_duration_type']->input
							, '</label>'
							. $durationForm['jform_end_days']->input
							. $durationForm['jform_end_days']->label
							. $durationForm['jform_end_hours']->input
							. $durationForm['jform_end_hours']->label
							. $durationForm['jform_end_minutes']->input
							. $durationForm['jform_end_minutes']->label
							. '<label class="jcl_block">'
						);
					?></li>
				</ul>
			</fieldset>
<?php if ($this->item->allow_registration && !empty($registrationForm)) : ?>
			<fieldset class="adminform" id="jcl_registration">
				<legend><?php echo JText::_('COM_JCALPRO_REGISTRATION'); ?></legend>
				<ul class="adminformlist">
					<li class="jcl_form_label"><?php echo $registrationForm['jform_registration']->label; ?></li>
					<li class="jcl_form_input"><?php echo $registrationForm['jform_registration']->input; ?></li>
				</ul>
				<div id="jcl_registration_off_options"> </div>
				<div id="jcl_registration_on_options">
					<ul class="adminformlist">
					<?php foreach ($registrationForm as $name => $field): if ('jform_registration' == $name) continue; ?>
						<li class="jcalnofloat jcl_form_label"><?php echo $field->label; ?></li>
						<li class="jcalnofloat jcl_form_input"><?php echo $field->input; ?></li>
					<?php endforeach; ?>
					</ul>
				</div>
			</fieldset>
<?php endif; ?>
			<fieldset class="adminform">
				<legend><?php echo JText::_('COM_JCALPRO_ADMIN_OPTIONS'); ?></legend>
				<ul class="adminformlist">
				<?php foreach ($adminForm as $name => $field): ?>
					<li class="jcl_form_label"><?php echo $field->label; ?></li>
					<li><?php echo $field->input; ?></li>
				<?php endforeach; ?>
				</ul>
			</fieldset>
<?php if (!empty($customfieldsFormTitle)) : ?>
			<fieldset class="adminform">
				<legend><?php
					echo JCalProHelperFilter::escape($customfieldsFormTitle);
				?></legend>
				<ul class="adminformlist">
					<?php foreach ($customfieldsForm as $name => $field) : ?>
					<li class="jcl_form_label"><?php echo $field->label; ?></li>
					<li><?php echo $field->input; ?></li>
					<?php endforeach; ?>
				</ul>
			</fieldset>
<?php endif; ?>
		</div>
		<div class="width-100 jcl_clear">
<?php if ($showRecurrence) : ?>
			<fieldset class="adminform">
				<legend><?php echo JText::_('COM_JCALPRO_REPEAT_METHOD'); ?></legend>
				<ul class="adminformlist">
					<li><?php
						echo $repeatForm['jform_recur_type']->input;
					?></li>
					<li>
						<div id="jcl_rec_none_options"> </div>
						<div id="jcl_rec_daily_options">
							<ul>
								<li><?php
									printf($repeatForm['jform_rec_daily_period']->label, 'X', '</label>' . $repeatForm['jform_rec_daily_period']->input . '<label>');
								?></li>
							</ul>
						</div>
						<div id="jcl_rec_weekly_options">
							<ul>
								<li><?php
									printf($repeatForm['jform_rec_weekly_period']->label, 'X', '</label>' . $repeatForm['jform_rec_weekly_period']->input . '<label>');
								?><br /><?php
									echo $repeatForm['jform_rec_weekly_on_sunday']->input;
									echo $repeatForm['jform_rec_weekly_on_sunday']->label;
									
									echo $repeatForm['jform_rec_weekly_on_monday']->input;
									echo $repeatForm['jform_rec_weekly_on_monday']->label;
									
									echo $repeatForm['jform_rec_weekly_on_tuesday']->input;
									echo $repeatForm['jform_rec_weekly_on_tuesday']->label;
									
									echo $repeatForm['jform_rec_weekly_on_wednesday']->input;
									echo $repeatForm['jform_rec_weekly_on_wednesday']->label;
									
									echo $repeatForm['jform_rec_weekly_on_thursday']->input;
									echo $repeatForm['jform_rec_weekly_on_thursday']->label;
									
									echo $repeatForm['jform_rec_weekly_on_friday']->input;
									echo $repeatForm['jform_rec_weekly_on_friday']->label;
									
									echo $repeatForm['jform_rec_weekly_on_saturday']->input;
									echo $repeatForm['jform_rec_weekly_on_saturday']->label;
								?></li>
							</ul>
						</div>
						<div id="jcl_rec_monthly_options">
							<ul>
								<li><?php
									printf($repeatForm['jform_rec_monthly_period']->label, 'X', '</label>' . $repeatForm['jform_rec_monthly_period']->input . '<label>');
								?><br /><?php
									printf(
										$repeatForm['jform_rec_monthly_type']->input
									, '</label>' . $repeatForm['jform_rec_monthly_day_number']->input . '<label class="jcl_block jcl_clear">'
									, '</label>' . $repeatForm['jform_rec_monthly_day_order']->input . ' ' . $repeatForm['jform_rec_monthly_day_type']->input . '<label>'
									);
								?></li>
							</ul>
						</div>
						<div id="jcl_rec_yearly_options">
							<ul>
								<li><?php
									printf($repeatForm['jform_rec_yearly_period']->label, 'X', 'X', '</label>' . $repeatForm['jform_rec_yearly_period']->input . '<label>', '</label>' . $repeatForm['jform_rec_yearly_on_month']->input . '<label>');
								?><br /><?php
									printf(
										$repeatForm['jform_rec_yearly_type']->input
									, '</label>' . $repeatForm['jform_rec_yearly_day_number']->input . '<label class="jcl_block jcl_clear">'
									, '</label>' . $repeatForm['jform_rec_yearly_day_order']->input . ' ' . $repeatForm['jform_rec_yearly_day_type']->input . '<label>'
									);
								?></li>
							</ul>
						</div>
					</li>
				</ul>
			</fieldset>
			<fieldset class="adminform jcalrepeatend">
				<legend><?php echo JText::_('COM_JCALPRO_REPEAT_END_DATE'); ?></legend>
				<ul class="adminformlist">
					<li class="jcalrepeatend"><?php
						printf($repeatForm['jform_recur_end_type']->input, '</label>' . $repeatForm['jform_recur_end_count']->input . '<label>', '</label>' . $repeatForm['jform_recur_end_until']->input . '<label>');
					?></li>
				</ul>
			</fieldset>
<?php endif; ?>
		</div>
		<div>
			<input type="hidden" id="event-id" name="id" value="<?php echo intval($this->item->id); ?>" />
			<input type="hidden" id="event-task" name="task" value="" />
			<input type="hidden" id="event-catid" name="catid" value="<?php echo JFactory::getApplication()->getUserState('com_jcalpro.events.jcal.catid'); ?>" />
			<?php echo JHtml::_('form.token'); ?>
		</div>
	</form>
</div>
<?php echo $this->loadTemplate('debug'); ?>